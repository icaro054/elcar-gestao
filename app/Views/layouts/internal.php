<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon-32x32.png">
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
        <div class="container mx-auto px-4 py-3 flex flex-wrap gap-y-3 items-center lg:grid lg:grid-cols-[1fr_auto_1fr] lg:gap-4">
            <div class="text-lg sm:text-xl font-bold flex items-center mr-auto lg:mr-0">
                <a href="/app/dashboard" class="text-white hover:text-elcar-yellow transition tracking-wider">ELCAR <span class="hidden text-sm font-normal text-gray-300 ml-2 sm:inline">Painel de Gestão</span></a>
            </div>
            
            <!-- Menu Principal (Secção 3.2) -->
            <nav class="hidden lg:flex items-center justify-self-center space-x-6">
                <a href="/app/dashboard" class="hover:text-elcar-yellow transition">Ínicio</a>
                <a href="/app/servicos" class="hover:text-elcar-yellow transition">Serviços</a>
                <a href="/app/historico" class="hover:text-elcar-yellow transition">Pesquise por Placa</a>
                <a href="/app/clientes" class="hover:text-elcar-yellow transition">Clientes</a>
                <a href="/app/veiculos" class="hover:text-elcar-yellow transition">Veículos</a>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="/app/usuarios" class="hover:text-elcar-yellow transition text-sm border-l border-elcar-secondary pl-4">Usuários</a>
                <?php endif; ?>
            </nav>

            <details class="lg:hidden relative">
                <summary class="list-none cursor-pointer rounded border border-elcar-secondary px-3 py-2 text-sm font-bold hover:bg-elcar-secondary">
                    Menu
                </summary>
                <nav class="absolute right-0 top-full z-20 mt-2 flex min-w-48 flex-col rounded-lg bg-white py-2 text-elcar-text shadow-lg">
                    <a href="/app/dashboard" class="px-4 py-2 hover:bg-gray-100">Início</a>
                    <a href="/app/servicos" class="px-4 py-2 hover:bg-gray-100">Serviços</a>
                    <a href="/app/historico" class="px-4 py-2 hover:bg-gray-100">Pesquise por Placa</a>
                    <a href="/app/clientes" class="px-4 py-2 hover:bg-gray-100">Clientes</a>
                    <a href="/app/veiculos" class="px-4 py-2 hover:bg-gray-100">Veículos</a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="/app/usuarios" class="px-4 py-2 hover:bg-gray-100">Usuários</a>
                    <?php endif; ?>
                </nav>
            </details>
            
            <!-- Utilizador e Logout -->
            <div class="flex items-center gap-2 sm:gap-4 lg:justify-self-end">
                <span class="hidden text-sm text-gray-300 sm:inline">Olá, <?= htmlspecialchars($_SESSION['name'] ?? 'Usuário') ?></span>
                <a href="/logout" class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition">Sair</a>
            </div>
        </div>
    </header>

    <!-- Conteúdo da Página -->
    <main class="flex-grow container mx-auto min-w-0 px-4 py-5 sm:py-8">
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