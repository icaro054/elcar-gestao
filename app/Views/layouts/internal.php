<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Elcar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        elcar: {
                            blue: '#123B5D',
                            secondary: '#1F5F91',
                            yellow: '#F2C230',
                            bg: '#F4F7FA',
                            text: '#1C2933',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-elcar-bg text-elcar-text min-h-screen flex flex-col">

    <!-- Navbar Interna -->
    <header class="bg-elcar-blue text-white shadow-md">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="text-xl font-bold flex items-center">
                <a href="/app/dashboard" class="text-white hover:text-elcar-yellow transition tracking-wider">ELCAR <span class="text-sm font-normal text-gray-300 ml-2">Painel de Gestão</span></a>
            </div>
            
            <!-- Menu Principal (Secção 3.2) -->
            <nav class="hidden md:flex space-x-6 items-center">
                <a href="/app/dashboard" class="hover:text-elcar-yellow transition">Dashboard</a>
                <a href="/app/servicos" class="hover:text-elcar-yellow transition">Serviços</a>
                <a href="/app/historico" class="hover:text-elcar-yellow transition">Histórico por Placa</a>
                <a href="/app/clientes" class="hover:text-elcar-yellow transition">Clientes</a>
                <a href="/app/veiculos" class="hover:text-elcar-yellow transition">Veículos</a>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="/app/usuarios" class="hover:text-elcar-yellow transition text-sm border-l border-elcar-secondary pl-4">Usuários</a>
                <?php endif; ?>
            </nav>
            
            <!-- Utilizador e Logout -->
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-300">Olá, <?= htmlspecialchars($_SESSION['name'] ?? 'Usuário') ?></span>
                <a href="/logout" class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition">Sair</a>
            </div>
        </div>
    </header>

    <!-- Conteúdo da Página -->
    <main class="flex-grow container mx-auto px-4 py-8">
        <?php 
            if (isset($viewPath) && file_exists($viewPath)) {
                require $viewPath;
            }
        ?>
    </main>

    <!-- Rodapé Interno -->
    <footer class="bg-white border-t py-4 mt-auto">
        <div class="container mx-auto px-4 text-center text-sm text-gray-500">
            &copy; <?= date('Y') ?> Elcar Autopeças e Serviços - Sistema de Gestão Interna
        </div>
    </footer>

</body>
</html>