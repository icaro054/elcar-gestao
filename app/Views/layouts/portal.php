<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon-32x32.png">
    <title>Portal do Cliente - Elcar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'elcar-blue': '#123B5D',
                        'elcar-secondary': '#1F5F91',
                        'elcar-yellow': '#F2C230',
                        'elcar-gray': '#F4F7FA',
                        'elcar-text': '#1C2933'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-elcar-gray text-elcar-text min-h-screen">
    
    <!-- O conteúdo da view 'cliente/portal.php' vai ser injetado diretamente aqui, 
         sem nenhuma barra de navegação por cima! -->
    <main>
        <?php require $viewPath; ?>
    </main>

</body>
</html>