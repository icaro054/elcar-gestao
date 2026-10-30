<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso - Elcar Autopeças</title>
    
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
<body class="bg-elcar-bg text-elcar-text flex flex-col min-h-screen">
    
    <main class="flex-grow flex items-center justify-center p-4">
        <?php 
            if (isset($viewPath) && file_exists($viewPath)) {
                require $viewPath;
            }
        ?>
    </main>

</body>
</html>