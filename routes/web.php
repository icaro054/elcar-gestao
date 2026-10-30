<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/app/Services/PlateService.php';

// Carrega variáveis de ambiente e define o fuso horário
loadEnv(dirname(__DIR__) . '/.env');
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Fortaleza');

// Configuração segura de sessão (Secção 10.1)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Obtém a URI atual limpa para o roteamento
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = ''; 
$route = str_replace($basePath, '', $requestUri);
$route = rtrim($route, '/');
if ($route === '') {
    $route = '/';
}

/**
 * Função auxiliar para renderizar views
 * @param string $viewName Nome do ficheiro em app/Views
 * @param array $data Dados a enviar para a view
 * @param string $layout Nome do ficheiro de layout em app/Views/layouts (padrão: 'main')
 */
function renderView(string $viewName, array $data = [], string $layout = 'main'): void {
    extract($data);
    $viewPath = dirname(__DIR__) . "/app/Views/{$viewName}.php";
    require dirname(__DIR__) . "/app/Views/layouts/{$layout}.php";
}

// Roteador principal
switch ($route) {
    case '/':
        renderView('home');
        break;

    case '/login':
        // Se o utilizador já estiver autenticado, redireciona para a área correta (Secção 6.2)
        if (isset($_SESSION['user_id'])) {
            $redirect = ($_SESSION['role'] === 'customer') ? '/cliente' : '/app/dashboard';
            header("Location: $redirect");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $erroGenerico = "E-mail ou palavra-passe inválidos.";

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
                    
                    if ($user['client_id']) {
                        $_SESSION['client_id'] = $user['client_id'];
                    }

                    $updateStmt = $pdo->prepare("UPDATE users SET last_login_at = NOW() WHERE id = :id");
                    $updateStmt->execute(['id' => $user['id']]);

                    if ($user['role'] === 'customer') {
                        header("Location: /cliente");
                    } else {
                        header("Location: /app/dashboard");
                    }
                    exit;

                } else {
                    renderView('login', ['erro' => $erroGenerico], 'auth');
                }
            } catch (Exception $e) {
                error_log("Erro no login: " . $e->getMessage());
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

    case '/app/dashboard':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') {
            header("Location: /login");
            exit;
        }
        
        try {
            $pdo = getDatabaseConnection();

            $metrics = [
                'total_pendentes' => $pdo->query("SELECT COUNT(*) FROM service_orders WHERE status = 'pending'")->fetchColumn() ?? 0,
                'total_em_execucao' => $pdo->query("SELECT COUNT(*) FROM service_orders WHERE status = 'in_progress'")->fetchColumn() ?? 0,
                'total_clientes' => $pdo->query("SELECT COUNT(*) FROM clients WHERE is_active = 1")->fetchColumn() ?? 0,
                'total_veiculos' => $pdo->query("SELECT COUNT(*) FROM vehicles WHERE is_active = 1")->fetchColumn() ?? 0,
            ];

            $stmtOs = $pdo->query("
                SELECT so.id, so.status, so.total_amount, v.plate_display, v.model, c.name as client_name
                FROM service_orders so
                JOIN vehicles v ON so.vehicle_id = v.id
                JOIN clients c ON so.client_id = c.id
                ORDER BY so.created_at DESC
                LIMIT 5
            ");
            $ultimas_os = $stmtOs->fetchAll();

            renderView('dashboard', [
                'metrics' => $metrics,
                'ultimas_os' => $ultimas_os
            ], 'internal');

        } catch (Exception $e) {
            error_log("Erro no dashboard: " . $e->getMessage());
            renderView('dashboard', [
                'metrics' => ['total_pendentes' => 0, 'total_em_execucao' => 0, 'total_clientes' => 0, 'total_veiculos' => 0],
                'ultimas_os' => []
            ], 'internal');
        }
        break;

    // ==========================================
    // MÓDULO DE CLIENTES
    // ==========================================
    case '/app/clientes':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') {
            header("Location: /login");
            exit;
        }
        
        $pdo = getDatabaseConnection();
        $stmt = $pdo->query("SELECT id, name, phone, email, is_active FROM clients ORDER BY name ASC");
        $clientes = $stmt->fetchAll();
        renderView('clientes/index', ['clientes' => $clientes], 'internal');
        break;

    case '/app/clientes/novo':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') {
            header("Location: /login");
            exit;
        }
        
        renderView('clientes/form', [], 'internal');
        break;

    case '/app/clientes/salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') {
            header("Location: /app/clientes");
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $address = trim($_POST['address'] ?? '');
        
        $document_number = preg_replace('/[^0-9]/', '', $_POST['document_number'] ?? '');
        if (empty($document_number)) {
            $document_number = null;
        }

        if (empty($name) || empty($phone)) {
            die("Erro: O Nome e o Telefone são campos obrigatórios.");
        }

        try {
            $pdo = getDatabaseConnection();
            $pdo->beginTransaction();
            
            $sql = "INSERT INTO clients (name, document_number, phone, email, address, is_active) 
                    VALUES (:name, :document_number, :phone, :email, :address, 1)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name' => $name,
                'document_number' => $document_number,
                'phone' => $phone,
                'email' => $email ?: null,
                'address' => $address ?: null
            ]);
            
            $newClientId = $pdo->lastInsertId();

            $auditSql = "INSERT INTO audit_logs (user_id, entity_type, entity_id, action) VALUES (:user_id, 'client', :entity_id, 'create')";
            $auditStmt = $pdo->prepare($auditSql);
            $auditStmt->execute(['user_id' => $_SESSION['user_id'], 'entity_id' => $newClientId]);

            $pdo->commit();
            header("Location: /app/clientes");
            exit;

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            if ($e->getCode() == 23000) {
                die("Erro: Este CPF/CNPJ já encontra-se registado noutro cliente.");
            }
            error_log("Erro ao salvar cliente: " . $e->getMessage());
            die("Erro interno ao tentar guardar os dados do cliente.");
        }
        break;

    // ==========================================
    // MÓDULO DE VEÍCULOS
    // ==========================================
    case '/app/veiculos':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') {
            header("Location: /login");
            exit;
        }
        
        $pdo = getDatabaseConnection();
        $sql = "SELECT v.*, c.name as client_name 
                FROM vehicles v 
                JOIN clients c ON v.client_id = c.id 
                ORDER BY v.id DESC";
        $stmt = $pdo->query($sql);
        $veiculos = $stmt->fetchAll();
        
        renderView('veiculos/index', ['veiculos' => $veiculos], 'internal');
        break;

    case '/app/veiculos/novo':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') {
            header("Location: /login");
            exit;
        }
        
        $pdo = getDatabaseConnection();
        $stmt = $pdo->query("SELECT id, name, document_number FROM clients WHERE is_active = 1 ORDER BY name ASC");
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        
        renderView('veiculos/form', ['clientes' => $clientes], 'internal');
        break;

    case '/app/veiculos/salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') {
            header("Location: /app/veiculos");
            exit;
        }

        $client_id = (int) $_POST['client_id'];
        $plate = $_POST['plate'] ?? '';
        $brand = trim($_POST['brand'] ?? '');
        $model = trim($_POST['model'] ?? '');
        $color = trim($_POST['color'] ?? '');
        $model_year = !empty($_POST['model_year']) ? (int) $_POST['model_year'] : null;
        $chassis = trim($_POST['chassis'] ?? '');
        
        if (!\App\Services\PlateService::validate($plate)) {
            die("Erro: O formato da placa é inválido. Utilize o formato Nacional Antigo (ABC-1234) ou Mercosul (ABC1D23).");
        }
        
        $plate_normalized = \App\Services\PlateService::normalize($plate);
        $plate_display = \App\Services\PlateService::formatDisplay($plate_normalized);

        try {
            $pdo = getDatabaseConnection();
            $pdo->beginTransaction();
            
            $sql = "INSERT INTO vehicles (client_id, plate_normalized, plate_display, brand, model, model_year, color, chassis, is_active) 
                    VALUES (:client_id, :plate_normalized, :plate_display, :brand, :model, :model_year, :color, :chassis, 1)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'client_id' => $client_id,
                'plate_normalized' => $plate_normalized,
                'plate_display' => $plate_display,
                'brand' => $brand ?: null,
                'model' => $model,
                'model_year' => $model_year,
                'color' => $color ?: null,
                'chassis' => $chassis ?: null
            ]);
            
            $newVehicleId = $pdo->lastInsertId();

            $auditSql = "INSERT INTO audit_logs (user_id, entity_type, entity_id, action) VALUES (:user_id, 'vehicle', :entity_id, 'create')";
            $auditStmt = $pdo->prepare($auditSql);
            $auditStmt->execute(['user_id' => $_SESSION['user_id'], 'entity_id' => $newVehicleId]);

            $pdo->commit();
            header("Location: /app/veiculos");
            exit;

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            if ($e->getCode() == 23000) {
                die("Erro: A placa informada já está registada noutro veículo.");
            }
            error_log("Erro ao salvar veículo: " . $e->getMessage());
            die("Erro interno ao tentar guardar os dados do veículo.");
        }
        break;

    // ==========================================
    // MÓDULO DE SERVIÇOS (OS) E HISTÓRICO
    // ==========================================
    case '/app/servicos':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') {
            header("Location: /login");
            exit;
        }
        
        $pdo = getDatabaseConnection();
        $sql = "SELECT so.id, so.status, so.total_amount, v.plate_display, v.model, c.name as client_name 
                FROM service_orders so 
                JOIN vehicles v ON so.vehicle_id = v.id 
                JOIN clients c ON v.client_id = c.id 
                ORDER BY so.created_at DESC";
        $stmt = $pdo->query($sql);
        $servicos = $stmt->fetchAll();
        
        renderView('servicos/index', ['servicos' => $servicos], 'internal');
        break;

    case '/app/servicos/novo':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') {
            header("Location: /login");
            exit;
        }
        
        $pdo = getDatabaseConnection();
        $sql = "SELECT v.id, v.plate_display, v.model, c.name as client_name 
                FROM vehicles v 
                JOIN clients c ON v.client_id = c.id 
                WHERE v.is_active = 1 
                ORDER BY c.name ASC";
        $stmt = $pdo->query($sql);
        $veiculos = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        
        renderView('servicos/form', ['veiculos' => $veiculos], 'internal');
        break;

    case '/app/servicos/salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') {
            header("Location: /app/servicos");
            exit;
        }

        $vehicle_id = (int) $_POST['vehicle_id'];
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'pending';
        $total_amount = !empty($_POST['total_amount']) ? (float) $_POST['total_amount'] : 0.00;

        if (empty($vehicle_id) || empty($description)) {
            die("Erro: O Veículo e a Descrição são obrigatórios.");
        }

        try {
            $pdo = getDatabaseConnection();
            
            $stmtVeh = $pdo->prepare("SELECT client_id FROM vehicles WHERE id = :id");
            $stmtVeh->execute(['id' => $vehicle_id]);
            $veiculo = $stmtVeh->fetch();
            
            if (!$veiculo) {
                die("Erro: Veículo não encontrado na base de dados.");
            }
            
            $client_id = $veiculo['client_id'];

            $pdo->beginTransaction();
            
            $sql = "INSERT INTO service_orders (vehicle_id, client_id, description, status, total_amount, created_by) 
                    VALUES (:vehicle_id, :client_id, :description, :status, :total_amount, :created_by)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'vehicle_id' => $vehicle_id,
                'client_id' => $client_id,
                'description' => $description,
                'status' => $status,
                'total_amount' => $total_amount,
                'created_by' => $_SESSION['user_id']
            ]);
            
            $newOsId = $pdo->lastInsertId();

            $auditSql = "INSERT INTO audit_logs (user_id, entity_type, entity_id, action) VALUES (:user_id, 'service_order', :entity_id, 'create')";
            $auditStmt = $pdo->prepare($auditSql);
            $auditStmt->execute(['user_id' => $_SESSION['user_id'], 'entity_id' => $newOsId]);

            $pdo->commit();
            header("Location: /app/servicos");
            exit;

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Erro ao salvar OS: " . $e->getMessage());
            die("Erro real do Banco de Dados: " . $e->getMessage());
        }
        break;

    case '/app/servicos/imprimir':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') {
            header("Location: /login");
            exit;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            die("ID da Ordem de Serviço inválido.");
        }

        $pdo = getDatabaseConnection();
        $sql = "SELECT so.*, v.plate_display, v.brand, v.model, v.color, v.model_year, v.chassis, 
                       c.name as client_name, c.document_number, c.phone, c.email, c.address,
                       u.name as user_name
                FROM service_orders so 
                JOIN vehicles v ON so.vehicle_id = v.id 
                JOIN clients c ON so.client_id = c.id 
                LEFT JOIN users u ON so.created_by = u.id
                WHERE so.id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $os = $stmt->fetch();

        if (!$os) {
            die("Ordem de Serviço não encontrada.");
        }

        renderView('servicos/print', ['os' => $os], 'portal');
        break;

    case '/app/historico':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'customer') {
            header("Location: /login");
            exit;
        }

        $placaInput = trim($_GET['placa'] ?? '');
        $pdo = getDatabaseConnection();
        $servicos = [];
        $veiculo = null;

        if (!empty($placaInput)) {
            $normalized = \App\Services\PlateService::normalize($placaInput);
            
            $stmtV = $pdo->prepare("
                SELECT v.*, c.name as client_name, c.phone 
                FROM vehicles v 
                JOIN clients c ON v.client_id = c.id 
                WHERE v.plate_normalized = :plate OR v.plate_display = :display
            ");
            $stmtV->execute(['plate' => $normalized, 'display' => $placaInput]);
            $veiculo = $stmtV->fetch();

            if ($veiculo) {
                $stmtS = $pdo->prepare("
                    SELECT so.*, u.name as user_name 
                    FROM service_orders so 
                    LEFT JOIN users u ON so.created_by = u.id 
                    WHERE so.vehicle_id = :vehicle_id 
                    ORDER BY so.created_at DESC
                ");
                $stmtS->execute(['vehicle_id' => $veiculo['id']]);
                $servicos = $stmtS->fetchAll();
            }
        }

        renderView('servicos/historico', [
            'placa' => $placaInput,
            'veiculo' => $veiculo,
            'servicos' => $servicos
        ], 'internal');
        break;

    // ==========================================
    // PORTAL DO CLIENTE E TESTES
    // ==========================================
    case '/cliente':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
            header("Location: /login");
            exit;
        }

        if (!isset($_SESSION['client_id'])) {
            die("Erro: Conta de utilizador não está vinculada a nenhum cliente físico na base de dados.");
        }

        $pdo = getDatabaseConnection();
        $client_id = $_SESSION['client_id'];

        $stmtVeiculos = $pdo->prepare("SELECT plate_display, brand, model FROM vehicles WHERE client_id = :client_id AND is_active = 1");
        $stmtVeiculos->execute(['client_id' => $client_id]);
        $veiculos = $stmtVeiculos->fetchAll();

        $stmtOS = $pdo->prepare("
            SELECT so.id, so.description, so.status, so.total_amount, so.created_at, v.plate_display, v.model
            FROM service_orders so
            JOIN vehicles v ON so.vehicle_id = v.id
            WHERE so.client_id = :client_id
            ORDER BY so.created_at DESC
        ");
        $stmtOS->execute(['client_id' => $client_id]);
        $servicos = $stmtOS->fetchAll();

        renderView('cliente/portal', [
            'nome_cliente' => $_SESSION['name'],
            'veiculos' => $veiculos,
            'servicos' => $servicos
        ], 'portal');
        break;
        
    case '/gerar-acesso-cliente':
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->query("SELECT id, name, email FROM clients ORDER BY id DESC LIMIT 1");
            $cliente = $stmt->fetch();
            
            if (!$cliente) die("Cadastre um cliente primeiro no painel Admin.");
            
            $email = $cliente['email'] ?: 'cliente@elcar.com';
            $hash = password_hash('cliente123', PASSWORD_DEFAULT);
            
            $pdo->exec("DELETE FROM users WHERE email = '$email'");
            
            $sql = "INSERT INTO users (name, email, password_hash, role, client_id, is_active) 
                    VALUES (?, ?, ?, 'customer', ?, 1)";
            $pdo->prepare($sql)->execute([$cliente['name'], $email, $hash, $cliente['id']]);
            
            echo "<h1 style='color: green;'>Conta de Cliente gerada!</h1>";
            echo "<p>Agora clique em Sair, e faça login com:</p>";
            echo "<ul><li><b>E-mail:</b> $email</li><li><b>Senha:</b> cliente123</li></ul>";
            echo "<a href='/logout'>=> Fazer Logout do Admin</a>";
        } catch(Exception $e) { echo "Erro: " . $e->getMessage(); }
        break;
        
    case '/teste-banco':
        try {
            $pdo = getDatabaseConnection();
            echo "<h1 style='color: green;'>Sucesso! Ligado à base de dados Elcar.</h1>";
            $stmt = $pdo->query("SELECT count(*) as total FROM users");
            $resultado = $stmt->fetch();
            echo "<p>Total de utilizadores registados: " . $resultado['total'] . "</p>";
        } catch (Exception $e) {
            echo "<h1 style='color: red;'>Erro de Ligação:</h1><p>" . $e->getMessage() . "</p>";
        }
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1><p>A rota solicitada não existe neste sistema.</p>";
        break;
}