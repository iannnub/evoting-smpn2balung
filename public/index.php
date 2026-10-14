<?php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Load env
require_once BASE_PATH . '/helpers/env.php';
load_env(BASE_PATH . '/.env');

// Load helper functions
require_once BASE_PATH . '/helpers/functions.php';
require_once BASE_PATH . '/helpers/spreadsheet.php';
require_once BASE_PATH . '/helpers/upload.php';

// Define BASE_URL (supports subfolder like /osis or root domain /)
define('BASE_URL', detect_base_url());

// Timezone
date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));

// Error handler setup
$isDev = config('app.env') === 'development';
if ($isDev) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    $logDir = BASE_PATH . '/storage/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0775, true);
    }
    ini_set('error_log', $logDir . '/error.log');
    error_reporting(E_ALL);
}

// Global exception handler
set_exception_handler(function (\Throwable $e) use ($isDev) {
    error_log('Unhandled exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);

    if (is_api_request()) {
        json([
            'success' => false,
            'message' => $isDev ? $e->getMessage() : 'Terjadi kesalahan pada sistem. Silakan hubungi administrator.',
        ], 500);
    }

    if ($isDev) {
        echo "<div style='font-family:sans-serif;padding:24px;background:#fee2e2;color:#991b1b;border-radius:8px;margin:24px;'>";
        echo "<h2 style='margin-top:0;'>Fatal Error (Development Mode)</h2>";
        echo "<p><strong>Pesan:</strong> " . e($e->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . e($e->getFile()) . " (Baris: " . $e->getLine() . ")</p>";
        echo "<pre style='background:#fff;padding:12px;border-radius:4px;overflow:auto;'>" . e($e->getTraceAsString()) . "</pre>";
        echo "</div>";
    } else {
        $view500 = BASE_PATH . '/app/Views/errors/500.php';
        if (is_file($view500)) {
            require $view500;
        } else {
            echo "<h1>500 - Terjadi Kesalahan Server</h1><p>Silakan coba beberapa saat lagi.</p>";
        }
    }
    exit;
});

// Autoloader for App\ namespace
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

// Handle CORS for API requests
\App\Middleware\CORS::handle();

// Start session
\App\Core\Session::start();

// Dispatch route
$router = new \App\Core\Router();
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', request_path());
