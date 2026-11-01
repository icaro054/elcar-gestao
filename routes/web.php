<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/app/Services/PlateService.php';

// Carrega variáveis de ambiente e define o fuso horário
loadEnv(dirname(__DIR__) . '/.env');
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Fortaleza');

// Configuração segura de sessão
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
        || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = rtrim(str_replace('', '', $requestUri), '/') ?: '/';

function renderView(string $viewName, array $data = [], string $layout = 'main'): void {
    extract($data);
    $viewPath = dirname(__DIR__) . "/app/Views/{$viewName}.php"; 
    require dirname(__DIR__) . "/app/Views/layouts/{$layout}.php";
}

switch ($route) {
    case '/':
        renderView('home');
        break;

    case '/login':
        if (isset($_SESSION['user_id'])) {
            header("Location: " . ($_SESSION['role'] === 'customer' ? '/cliente' : '/app/dashboard'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            
            if (!$email || !$password) {
                renderView('login', ['erro' => 'Preencha todos os campos.'], 'auth');
                break;
            }

            try {
                $pdo = getDatabaseConnection();
                $stmt = $pdo->prepare("SELECT id, name, password_hash, role, is_active, client_id FROM users WHERE email = :email LIMIT 1");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    if (!$user['is_active']) {
                        renderView('login', ['erro' => 'A sua conta encontra-se inativa.'], 'auth');
                        break;
                    }
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name']    = $user['name'];
                    $_SESSION['role']    = $user['role'];
                    if ($user['client_id']) $_SESSION['client_id'] = $user['client_id'];

                    $pdo->prepare("UPDATE users SET last_login_at = NOW() WHERE id = :id")->execute(['id' => $user['id']]);

                    header("Location: " . ($user['role'] === 'customer' ? '/cliente' : '/app/dashboard'));
                    exit;
                } else {
                    renderView('login', ['erro' => 'E-mail ou palavra-passe inválidos.'], 'auth');
                }
            } catch (Exception $e) {
                renderView('login', ['erro' => 'Erro interno ao processar a autenticação.'], 'auth');
            }
        } else {
            renderView('login', [], 'auth');
        }
        break;

    case '/logout':
        session_unset();
        session_destroy();
        header("Location: /login");
        exit;
    // ==========================================
    // PRIMEIRO ACESSO / RECUPERAR SENHA
    // ==========================================
    case '/recuperar-senha':
        if (isset($_SESSION['user_id'])) { header("Location: /"); exit; }
        renderView('recuperar', [], 'auth');
        break;

    case '/redefinir-senha':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $document = preg_replace('/[^0-9]/', '', $_POST['document_number'] ?? '');
            $nova_senha = $_POST['nova_senha'] ?? '';
            $confirmar_senha = $_POST['confirmar_senha'] ?? '';

            if ($nova_senha !== $confirmar_senha) {
                renderView('recuperar', ['erro' => 'As senhas digitadas não coincidem.'], 'auth');
                break;
            }

            $pdo = getDatabaseConnection();
            
            // 1. Procura o cliente pelo E-mail e CPF/CNPJ exatos
            $stmt = $pdo->prepare("SELECT id, name FROM clients WHERE email = ? AND document_number = ? AND is_active = 1");
            $stmt->execute([$email, $document]);
            $cliente = $stmt->fetch();

            if ($cliente) {
                $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                
                // 2. Verifica se o cliente já tem conta de utilizador
                $stmtUser = $pdo->prepare("SELECT id FROM users WHERE client_id = ?");
                $stmtUser->execute([$cliente['id']]);
                $userId = $stmtUser->fetchColumn();
                
                if ($userId) {
                    // Já tem conta: Apenas atualiza a senha
                    $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$hash, $userId]);
                } else {
                    // Primeiro Acesso: Cria a conta de utilizador para este cliente
                    $pdo->prepare("INSERT INTO users (name, email, password_hash, role, client_id, is_active) VALUES (?, ?, ?, 'customer', ?, 1)")
                        ->execute([$cliente['name'], $email, $hash, $cliente['id']]);
                }
                
                renderView('login', ['sucesso' => 'Senha registada com sucesso! Já pode fazer login.'], 'auth');
            } else {
                renderView('recuperar', ['erro' => 'E-mail ou CPF/CNPJ não encontrados no sistema da oficina.'], 'auth');
            }
        }
        break;

    case '/app/dashboard':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') { header("Location: /login"); exit; }
        try {
            $pdo = getDatabaseConnection();
            $metrics = [
                'total_pendentes' => $pdo->query("SELECT COUNT(*) FROM service_orders WHERE status = 'pending'")->fetchColumn() ?? 0,
                'total_em_execucao' => $pdo->query("SELECT COUNT(*) FROM service_orders WHERE status = 'in_progress'")->fetchColumn() ?? 0,
                'total_clientes' => $pdo->query("SELECT COUNT(*) FROM clients WHERE is_active = 1")->fetchColumn() ?? 0,
                'total_veiculos' => $pdo->query("SELECT COUNT(*) FROM vehicles WHERE is_active = 1")->fetchColumn() ?? 0,
            ];
            $ultimas_os = $pdo->query("SELECT so.id, so.status, so.total_amount, v.plate_display, v.model, c.name as client_name FROM service_orders so JOIN vehicles v ON so.vehicle_id = v.id JOIN clients c ON so.client_id = c.id ORDER BY so.created_at DESC LIMIT 5")->fetchAll();
            renderView('dashboard', ['metrics' => $metrics, 'ultimas_os' => $ultimas_os], 'internal');
        } catch (Exception $e) {
            renderView('dashboard', ['metrics' => ['total_pendentes'=>0,'total_em_execucao'=>0,'total_clientes'=>0,'total_veiculos'=>0], 'ultimas_os' => []], 'internal');
        }
        break;
    // ==========================================
    // MÓDULO DE LISTA DE REPOSIÇÃO (COMPRAS)
    // ==========================================
    case '/app/estoque/compras':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') { header("Location: /login"); exit; }
        $pdo = getDatabaseConnection();
        $itens_comprar = $pdo->query("SELECT * FROM shopping_list ORDER BY created_at DESC")->fetchAll();
        renderView('estoque/compras', ['itens' => $itens_comprar], 'internal');
        break;

    case '/app/estoque/compras/adicionar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $code = $_POST['code'] ?? null;
            $description = mb_strtoupper(trim($_POST['description'] ?? ''));
            $quantity = (float)$_POST['quantity'];
            
            $pdo = getDatabaseConnection();
            $pdo->prepare("INSERT INTO shopping_list (code, description, quantity) VALUES (?, ?, ?)")
                ->execute([$code, $description, $quantity]);
            
            header("Location: /app/estoque/compras");
            exit;
        }
        break;
    case '/app/estoque/compras/importar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $pdo = getDatabaseConnection();
            $pdo->beginTransaction();
            try {
                // 1. Puxa as peças de OS concluídas e soma as quantidades das peças iguais
                $sqlInsert = "INSERT INTO shopping_list (code, description, quantity) 
                              SELECT soi.code, soi.description, SUM(soi.quantity) 
                              FROM service_order_items soi 
                              JOIN service_orders so ON soi.service_order_id = so.id 
                              WHERE so.status = 'completed' AND soi.added_to_shopping_list = 0 
                              GROUP BY soi.code, soi.description";
                $pdo->exec($sqlInsert);
                
                // 2. Marca essas peças na OS como 'já adicionadas à lista de compras'
                $sqlUpdate = "UPDATE service_order_items soi 
                              JOIN service_orders so ON soi.service_order_id = so.id 
                              SET soi.added_to_shopping_list = 1 
                              WHERE so.status = 'completed' AND soi.added_to_shopping_list = 0";
                $pdo->exec($sqlUpdate);
                
                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
            }
            header("Location: /app/estoque/compras");
            exit;
        }
        break;

    case '/app/estoque/compras/remover':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $id = (int)$_POST['id'];
            $pdo = getDatabaseConnection();
            $pdo->prepare("DELETE FROM shopping_list WHERE id = ?")->execute([$id]);
            header("Location: /app/estoque/compras");
            exit;
        }
        break;

    // ==========================================
    // MÓDULO DE CLIENTES
    // ==========================================
    case '/app/clientes':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') { header("Location: /login"); exit; }
        $pdo = getDatabaseConnection();
        $clientes = $pdo->query("SELECT * FROM clients WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
        renderView('clientes/index', ['clientes' => $clientes], 'internal');
        break;

    case '/app/clientes/novo':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') { header("Location: /login"); exit; }
        renderView('clientes/form', [], 'internal');
        break;

    case '/app/clientes/salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) { header("Location: /app/clientes"); exit; }
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $address = trim($_POST['address'] ?? '');
        $document_number = preg_replace('/[^0-9]/', '', $_POST['document_number'] ?? '');
        $document_number = empty($document_number) ? null : $document_number;

        $pdo = getDatabaseConnection();
        $sql = "INSERT INTO clients (name, document_number, phone, email, address, is_active) VALUES (?, ?, ?, ?, ?, 1)";
        $pdo->prepare($sql)->execute([$name, $document_number, $phone, $email ?: null, $address ?: null]);
        header("Location: /app/clientes");
        exit;

    case '/app/clientes/editar':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') { header("Location: /login"); exit; }
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = :id AND is_active = 1");
        $stmt->execute(['id' => $id]);
        $cliente = $stmt->fetch();
        if (!$cliente) die("Cliente não encontrado.");
        renderView('clientes/edit', ['cliente' => $cliente], 'internal');
        break;

    case '/app/clientes/atualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $id = (int)$_POST['id'];
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $address = trim($_POST['address'] ?? '');
            $document_number = preg_replace('/[^0-9]/', '', $_POST['document_number'] ?? '') ?: null;

            $pdo = getDatabaseConnection();
            $sql = "UPDATE clients SET name=?, document_number=?, phone=?, email=?, address=? WHERE id=?";
            $pdo->prepare($sql)->execute([$name, $document_number, $phone, $email ?: null, $address ?: null, $id]);
            header("Location: /app/clientes");
            exit;
        }
        break;

    case '/app/clientes/excluir':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') { header("Location: /login"); exit; }
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $pdo = getDatabaseConnection();
        $pdo->prepare("UPDATE clients SET is_active = 0 WHERE id = :id")->execute(['id' => $id]);
        header("Location: /app/clientes");
        exit;

    // ==========================================
    // MÓDULO DE VEÍCULOS
    // ==========================================
    case '/app/veiculos':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') { header("Location: /login"); exit; }
        $pdo = getDatabaseConnection();
        $veiculos = $pdo->query("SELECT v.*, c.name as client_name FROM vehicles v JOIN clients c ON v.client_id = c.id WHERE v.is_active = 1 ORDER BY v.id DESC")->fetchAll();
        renderView('veiculos/index', ['veiculos' => $veiculos], 'internal');
        break;

    case '/app/veiculos/novo':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') { header("Location: /login"); exit; }
        $pdo = getDatabaseConnection();
        $clientes = $pdo->query("SELECT id, name FROM clients WHERE is_active = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        renderView('veiculos/form', ['clientes' => $clientes], 'internal');
        break;

    case '/app/veiculos/salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) { header("Location: /app/veiculos"); exit; }
        $client_id = (int) $_POST['client_id'];
        $plate = trim($_POST['plate'] ?? '');
        $brand = trim($_POST['brand'] ?? '');
        $model = trim($_POST['model'] ?? '');
        $color = trim($_POST['color'] ?? '');
        $model_year = !empty($_POST['model_year']) ? (int) $_POST['model_year'] : null;
        $chassis = trim($_POST['chassis'] ?? '');
        
        $plate_normalized = \App\Services\PlateService::normalize($plate);
        $plate_display = \App\Services\PlateService::formatDisplay($plate_normalized);

        $pdo = getDatabaseConnection();
        $sql = "INSERT INTO vehicles (client_id, plate_normalized, plate_display, brand, model, model_year, color, chassis, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $pdo->prepare($sql)->execute([$client_id, $plate_normalized, $plate_display, $brand, $model, $model_year, $color, $chassis]);
        header("Location: /app/veiculos");
        exit;

    case '/app/veiculos/editar':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') { header("Location: /login"); exit; }
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $pdo = getDatabaseConnection();
        $veiculo = $pdo->prepare("SELECT * FROM vehicles WHERE id = :id AND is_active = 1");
        $veiculo->execute(['id' => $id]);
        $veiculo = $veiculo->fetch();
        if (!$veiculo) die("Veículo não encontrado.");
        $clientes = $pdo->query("SELECT id, name FROM clients WHERE is_active = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        renderView('veiculos/edit', ['veiculo' => $veiculo, 'clientes' => $clientes], 'internal');
        break;

    case '/app/veiculos/atualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $id = (int)$_POST['id'];
            $client_id = (int)$_POST['client_id'];
            $brand = trim($_POST['brand'] ?? '');
            $model = trim($_POST['model'] ?? '');
            $color = trim($_POST['color'] ?? '');
            $model_year = !empty($_POST['model_year']) ? (int)$_POST['model_year'] : null;
            $chassis = trim($_POST['chassis'] ?? '');
            
            $pdo = getDatabaseConnection();
            $sql = "UPDATE vehicles SET client_id=?, brand=?, model=?, model_year=?, color=?, chassis=? WHERE id=?";
            $pdo->prepare($sql)->execute([$client_id, $brand, $model, $model_year, $color, $chassis, $id]);
            header("Location: /app/veiculos");
            exit;
        }
        break;

    case '/app/veiculos/excluir':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') { header("Location: /login"); exit; }
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $pdo = getDatabaseConnection();
        $pdo->prepare("UPDATE vehicles SET is_active = 0 WHERE id = :id")->execute(['id' => $id]);
        header("Location: /app/veiculos");
        exit;

    // ==========================================
    // MÓDULO DE SERVIÇOS (OS)
    // ==========================================
    case '/app/servicos':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') { header("Location: /login"); exit; }
        $pdo = getDatabaseConnection();
        $servicos = $pdo->query("SELECT so.id, so.status, so.total_amount, so.created_at, v.plate_display, v.model, c.name as client_name FROM service_orders so JOIN vehicles v ON so.vehicle_id = v.id JOIN clients c ON v.client_id = c.id ORDER BY so.created_at DESC")->fetchAll();
        renderView('servicos/index', ['servicos' => $servicos], 'internal');
        break;

    case '/app/servicos/novo':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') { header("Location: /login"); exit; }
        $pdo = getDatabaseConnection();
        $veiculos = $pdo->query("SELECT v.id, v.plate_display, v.model, c.name as client_name FROM vehicles v JOIN clients c ON v.client_id = c.id WHERE v.is_active = 1 ORDER BY c.name ASC")->fetchAll(PDO::FETCH_ASSOC);
        renderView('servicos/form', ['veiculos' => $veiculos], 'internal');
        break;

    case '/app/servicos/salvar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $pdo = getDatabaseConnection();
            $vehicle_id = (int) $_POST['vehicle_id'];
            $description = trim($_POST['description'] ?? '');
            
            $client_id = $pdo->query("SELECT client_id FROM vehicles WHERE id = $vehicle_id")->fetchColumn();
            
            $sql = "INSERT INTO service_orders (vehicle_id, client_id, description, status, total_amount, created_by) VALUES (?, ?, ?, 'pending', 0, ?)";
            $pdo->prepare($sql)->execute([$vehicle_id, $client_id, $description, $_SESSION['user_id']]);
            
            // Pega o ID da nova OS e redireciona direto para a gestão!
            $new_id = $pdo->lastInsertId();
            header("Location: /app/servicos/gerenciar?id=" . $new_id);
            exit;
        }
        break;

    case '/app/servicos/gerenciar':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') { header("Location: /login"); exit; }
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $pdo = getDatabaseConnection();
        
        $stmt = $pdo->prepare("SELECT so.*, v.plate_display, c.name as client_name FROM service_orders so JOIN vehicles v ON so.vehicle_id = v.id JOIN clients c ON so.client_id = c.id WHERE so.id = :id");
        $stmt->execute(['id' => $id]);
        $os = $stmt->fetch();
        
        $stmtItens = $pdo->prepare("SELECT * FROM service_order_items WHERE service_order_id = :id ORDER BY created_at ASC");
        $stmtItens->execute(['id' => $id]);
        $itens = $stmtItens->fetchAll();
        
        renderView('servicos/gerenciar', ['os' => $os, 'itens' => $itens], 'internal');
        break;

    case '/app/servicos/atualizar-status':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $id = (int)$_POST['id'];
            $status = $_POST['status'];
            $labor_cost = (float)$_POST['labor_cost']; // Novo campo
            $pdo = getDatabaseConnection();
            
            // Atualiza status e mão de obra
            if ($status === 'completed') {
                $pdo->prepare("UPDATE service_orders SET status = ?, labor_cost = ?, completed_at = NOW() WHERE id = ?")->execute([$status, $labor_cost, $id]);
            } else {
                $pdo->prepare("UPDATE service_orders SET status = ?, labor_cost = ?, completed_at = NULL WHERE id = ?")->execute([$status, $labor_cost, $id]);
            }
            
            // Recalcula o total (Mão de Obra + Soma das Peças)
            $pdo->prepare("UPDATE service_orders SET total_amount = labor_cost + COALESCE((SELECT SUM(quantity * unit_price) FROM service_order_items WHERE service_order_id = ?), 0) WHERE id = ?")->execute([$id, $id]);
            
            header("Location: /app/servicos/gerenciar?id=" . $id);
            exit;
        }
        break;

    case '/app/servicos/adicionar-item':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $os_id = (int)$_POST['service_order_id'];
            $pdo = getDatabaseConnection();
            $pdo->beginTransaction();
            try {
                $pdo->prepare("INSERT INTO service_order_items (service_order_id, code, description, quantity, unit_price, mechanic_name) VALUES (?, ?, ?, ?, ?, ?)")
                    ->execute([$os_id, $_POST['code'] ?? null, mb_strtoupper(trim($_POST['description'])), (float)$_POST['quantity'], (float)$_POST['unit_price'], mb_strtoupper(trim($_POST['mechanic_name'] ?? ''))]);
                
                // Recalcula total
                $pdo->prepare("UPDATE service_orders SET total_amount = labor_cost + COALESCE((SELECT SUM(quantity * unit_price) FROM service_order_items WHERE service_order_id = ?), 0) WHERE id = ?")->execute([$os_id, $os_id]);
                $pdo->commit();
            } catch (Exception $e) { $pdo->rollBack(); }
            header("Location: /app/servicos/gerenciar?id=" . $os_id);
            exit;
        }
        break;

    case '/app/servicos/remover-item':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $item_id = (int)$_POST['item_id'];
            $os_id = (int)$_POST['os_id'];
            $pdo = getDatabaseConnection();
            
            $pdo->prepare("DELETE FROM service_order_items WHERE id = ?")->execute([$item_id]);
            $pdo->prepare("UPDATE service_orders SET total_amount = labor_cost + COALESCE((SELECT SUM(quantity * unit_price) FROM service_order_items WHERE service_order_id = ?), 0) WHERE id = ?")->execute([$os_id, $os_id]);
            
            header("Location: /app/servicos/gerenciar?id=" . $os_id);
            exit;
        }
        break;

    case '/app/servicos/imprimir':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') { header("Location: /login"); exit; }
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $pdo = getDatabaseConnection();
        
        $stmt = $pdo->prepare("SELECT so.*, v.plate_display, v.brand, v.model, v.color, v.model_year, v.chassis, c.name as client_name, c.document_number, c.phone, c.email, c.address FROM service_orders so JOIN vehicles v ON so.vehicle_id = v.id JOIN clients c ON so.client_id = c.id WHERE so.id = :id");
        $stmt->execute(['id' => $id]);
        $os = $stmt->fetch();
        
        $stmtItens = $pdo->prepare("SELECT * FROM service_order_items WHERE service_order_id = :id ORDER BY created_at ASC");
        $stmtItens->execute(['id' => $id]);
        $itens = $stmtItens->fetchAll();
        
        renderView('servicos/print', ['os' => $os, 'itens' => $itens], 'portal');
        break;

    // ==========================================
    // OUTROS MÓDULOS (Histórico e Cliente)
    // ==========================================
    case '/app/historico':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') { header("Location: /login"); exit; }
        $placaInput = trim($_GET['placa'] ?? '');
        $pdo = getDatabaseConnection();
        $servicos = [];
        $veiculo = null;
        
        if (!empty($placaInput)) {
            $normalized = \App\Services\PlateService::normalize($placaInput);
            $stmtV = $pdo->prepare("SELECT v.*, c.name as client_name, c.phone FROM vehicles v JOIN clients c ON v.client_id = c.id WHERE v.plate_normalized = :plate OR v.plate_display = :display");
            $stmtV->execute(['plate' => $normalized, 'display' => $placaInput]);
            $veiculo = $stmtV->fetch();
            
            if ($veiculo) {
                $stmtS = $pdo->prepare("SELECT * FROM service_orders WHERE vehicle_id = :vehicle_id ORDER BY created_at DESC");
                $stmtS->execute(['vehicle_id' => $veiculo['id']]);
                $servicos = $stmtS->fetchAll();
            }
        }
        renderView('servicos/historico', ['placa' => $placaInput, 'veiculo' => $veiculo, 'servicos' => $servicos], 'internal');
        break;

    case '/cliente':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') { header("Location: /login"); exit; }
        $pdo = getDatabaseConnection();
        $client_id = $_SESSION['client_id'];
        
        // 1. Busca os veículos do cliente para criar os botões de filtro
        $veiculos = $pdo->prepare("SELECT id, plate_display, brand, model FROM vehicles WHERE client_id = :client_id AND is_active = 1");
        $veiculos->execute(['client_id' => $client_id]);
        $veiculos = $veiculos->fetchAll();
        
        // 2. Lógica do Filtro
        $filtro_veiculo = filter_input(INPUT_GET, 'veiculo_id', FILTER_VALIDATE_INT);
        $queryServicos = "SELECT so.*, v.plate_display, v.model FROM service_orders so JOIN vehicles v ON so.vehicle_id = v.id WHERE so.client_id = :client_id";
        $params = ['client_id' => $client_id];
        
        if ($filtro_veiculo) {
            $queryServicos .= " AND so.vehicle_id = :veiculo_id";
            $params['veiculo_id'] = $filtro_veiculo;
        }
        $queryServicos .= " ORDER BY so.created_at DESC";
        
        $servicos = $pdo->prepare($queryServicos);
        $servicos->execute($params);
        $servicos = $servicos->fetchAll();
        
        renderView('cliente/portal', [
            'nome_cliente' => $_SESSION['name'], 
            'veiculos' => $veiculos, 
            'servicos' => $servicos,
            'filtro_veiculo' => $filtro_veiculo // Passa para a view saber qual botão pintar de azul
        ], 'portal');
        break;

    case '/cliente/os':
        // Rota Segura: O cliente vê a impressão, mas APENAS se a OS for dele!
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') { header("Location: /login"); exit; }
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $client_id = $_SESSION['client_id'];
        $pdo = getDatabaseConnection();
        
        $stmt = $pdo->prepare("SELECT so.*, v.plate_display, v.brand, v.model, v.color, v.model_year, v.chassis, c.name as client_name, c.document_number, c.phone, c.email, c.address FROM service_orders so JOIN vehicles v ON so.vehicle_id = v.id JOIN clients c ON so.client_id = c.id WHERE so.id = :id AND so.client_id = :client_id");
        $stmt->execute(['id' => $id, 'client_id' => $client_id]);
        $os = $stmt->fetch();
        
        if (!$os) { die("<h1 style='text-align:center; margin-top:50px; font-family:sans-serif;'>Acesso Negado ou OS Inexistente.</h1>"); }
        
        $stmtItens = $pdo->prepare("SELECT * FROM service_order_items WHERE service_order_id = :id ORDER BY created_at ASC");
        $stmtItens->execute(['id' => $id]);
        $itens = $stmtItens->fetchAll();
        
        // Reutilizamos a vista de impressão que já é perfeita!
        renderView('servicos/print', ['os' => $os, 'itens' => $itens], 'portal');
        break;
    case '/cliente/senha':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') { header("Location: /login"); exit; }
        renderView('cliente/senha', ['nome_cliente' => $_SESSION['name']], 'portal');
        break;

    case '/cliente/atualizar-senha':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer') {
            $senha_atual = $_POST['senha_atual'] ?? '';
            $nova_senha = $_POST['nova_senha'] ?? '';
            $confirmar_senha = $_POST['confirmar_senha'] ?? '';

            if ($nova_senha !== $confirmar_senha) {
                echo "<script>alert('As senhas novas não coincidem!'); window.history.back();</script>";
                exit;
            }

            $pdo = getDatabaseConnection();
            $hash_atual = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
            $hash_atual->execute([$_SESSION['user_id']]);
            $user_hash = $hash_atual->fetchColumn();

            if (password_verify($senha_atual, $user_hash)) {
                $novo_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$novo_hash, $_SESSION['user_id']]);
                echo "<script>alert('Senha alterada com sucesso!'); window.location.href='/cliente';</script>";
            } else {
                echo "<script>alert('A senha atual está incorreta!'); window.history.back();</script>";
            }
            exit;
        }
        break;

    case '/gerar-acesso-cliente':
        try {
            $pdo = getDatabaseConnection();
            $cliente = $pdo->query("SELECT id, name, email FROM clients ORDER BY id DESC LIMIT 1")->fetch();
            $email = $cliente['email'] ?: 'cliente@elcar.com';
            $hash = password_hash('cliente123', PASSWORD_DEFAULT);
            $pdo->exec("DELETE FROM users WHERE email = '$email'");
            $pdo->prepare("INSERT INTO users (name, email, password_hash, role, client_id, is_active) VALUES (?, ?, ?, 'customer', ?, 1)")->execute([$cliente['name'], $email, $hash, $cliente['id']]);
            echo "Conta de Cliente gerada! Email: $email | Senha: cliente123";
        } catch(Exception $e) { 
            echo "Erro: " . $e->getMessage(); 
        }
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1>";
        break;
}