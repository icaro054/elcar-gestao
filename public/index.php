<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';

loadEnv(dirname(__DIR__) . '/.env');
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Fortaleza');

// Detect HTTPS both directly and when SSL is terminated by the hosting proxy.
$isHttps = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
    || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';

// Configuração segura de sessão (Seção 10.1)
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Carrega o roteador principal
require_once dirname(__DIR__) . '/routes/web.php';