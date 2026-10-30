<?php

declare(strict_types=1);

/**
 * Carrega variáveis do ficheiro .env na raiz do projeto
 */
function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        [$name, $value] = array_pad(explode('=', $line, 2), 2, '');
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");

        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv("{$name}={$value}");
        }
    }
}

/**
 * Retorna a instância singleton da ligação PDO
 */
function getDatabaseConnection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        loadEnv(dirname(__DIR__) . '/.env');

        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $db   = $_ENV['DB_DATABASE'] ?? 'elcar_gestao';
        $user = $_ENV['DB_USERNAME'] ?? 'root';
        $pass = $_ENV['DB_PASSWORD'] ?? '';

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Regista o erro técnico em storage/logs sem expor credenciais no ecrã (Seção 6.11)
            $logFile = dirname(__DIR__) . '/storage/logs/error.log';
            $message = sprintf("[%s] Database Error: %s\n", date('Y-m-d H:i:s'), $e->getMessage());
            error_log($message, 3, $logFile);

            http_response_code(500);
            exit('Erro interno ao conectar ao servidor de dados. Tente novamente mais tarde.');
        }
    }

    return $pdo;
}