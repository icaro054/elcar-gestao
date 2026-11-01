<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon-32x32.png">
    <title>Elcar Autopeças e Serviços</title>
    
    <!-- Tailwind CSS CDN com a Identidade Visual (Seção 9.3) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        elcar: {
                            blue: '#123B5D',      // Azul principal
                            secondary: '#1F5F91', // Azul secundário
                            yellow: '#F2C230',    // Amarelo de destaque
                            bg: '#F4F7FA',        // Fundo cinza
                            text: '#1C2933',      // Texto grafite
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-elcar-bg text-elcar-text flex flex-col min-h-screen">
    
    <!-- Cabeçalho (Seção 6.1) -->
    <header class="bg-elcar-blue text-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex flex-wrap gap-y-3 items-center">
            <div class="text-2xl font-bold flex items-center mr-auto">
                <a href="/" class="text-white hover:text-elcar-yellow transition">ELCAR</a>
            </div>
            
            <!-- Menu Principal -->
            <nav class="hidden md:flex space-x-6">
                <a href="/" class="hover:text-elcar-yellow transition">Início</a>
                <a href="#servicos" class="hover:text-elcar-yellow transition">Serviços</a>
                <a href="#sobre" class="hover:text-elcar-yellow transition">Sobre</a>
                <a href="#contato" class="hover:text-elcar-yellow transition">Contato</a>
            </nav>

            <details class="md:hidden relative">
                <summary class="list-none cursor-pointer rounded border border-elcar-secondary px-3 py-2 text-sm font-bold hover:bg-elcar-secondary">
                    Menu
                </summary>
                <nav class="absolute right-0 top-full z-20 mt-2 flex min-w-40 flex-col rounded-lg bg-white py-2 text-elcar-text shadow-lg">
                    <a href="/" class="px-4 py-2 hover:bg-gray-100">Início</a>
                    <a href="#servicos" class="px-4 py-2 hover:bg-gray-100">Serviços</a>
                    <a href="#sobre" class="px-4 py-2 hover:bg-gray-100">Sobre</a>
                    <a href="#contato" class="px-4 py-2 hover:bg-gray-100">Contato</a>
                </nav>
            </details>
            
            <!-- Botão de Acesso -->
            <div class="ml-2">
                <a href="/login" class="bg-elcar-yellow text-elcar-blue font-bold px-3 sm:px-5 py-2 rounded hover:bg-yellow-400 transition text-sm sm:text-base whitespace-nowrap">
                    Acessar Sistema
                </a>
            </div>
        </div>
    </header>

    <!-- Conteúdo Dinâmico -->
    <main class="flex-grow">
        <?php 
            // Injeta a view específica (ex: home.php ou login.php)
            if (isset($viewPath) && file_exists($viewPath)) {
                require $viewPath;
            }
        ?>
    </main>

    <!-- Rodapé (Seção 6.1) -->
    <footer class="bg-elcar-blue text-white py-6 mt-10">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; <?= date('Y') ?> Elcar Autopeças e Serviços. Todos os direitos reservados.</p>
            <p class="text-sm mt-2 text-gray-300">
                <a href="/login" class="hover:text-elcar-yellow transition">Acesso restrito</a>
            </p>
        </div>
    </footer>

</body>
</html>