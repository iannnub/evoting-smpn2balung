<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function __construct()
    {
        $this->routes = require BASE_PATH . '/config/routes.php';
    }

    public function dispatch(string $method, string $path): void
    {
        $path = '/' . trim($path, '/');
        $method = strtoupper($method);
        if ($method === 'HEAD') {
            $method = 'GET';
        }

        // Security check: require authentication on admin routes (except login)
        if (str_starts_with($path, '/admin') && $path !== '/admin/login') {
            if (!Session::has('admin_id')) {
                flash('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
                redirect('/admin/login');
            }
        }

        if (str_starts_with($path, '/api/admin') && $path !== '/api/admin/auth/login') {
            if (!Session::has('admin_id')) {
                json(['success' => false, 'message' => 'Sesi admin tidak valid atau belum login.'], 401);
            }
        }

        // Security check: CSRF for POST requests on web forms (API requests use auth tokens/CORS)
        if ($method === 'POST') {
            $isApi = str_starts_with($path, '/api/');
            if (!$isApi) {
                $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
                if (!verify_csrf($token)) {
                    if (is_api_request()) {
                        json(['success' => false, 'message' => 'Token keamanan (CSRF) tidak valid atau telah kadaluarsa.'], 403);
                    }
                    flash('error', 'Token keamanan sesi tidak valid atau telah kadaluarsa. Silakan coba lagi.');
                    back();
                }
            }
        }

        foreach ($this->routes as $route) {
            [$routeMethod, $routePath, $handler] = $route;
            if ($routeMethod !== $method) {
                continue;
            }

            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }

                $this->executeHandler($handler, $params);
                return;
            }
        }

        // 404 handler
        http_response_code(404);
        if (is_api_request()) {
            json(['success' => false, 'message' => 'Halaman atau endpoint tidak ditemukan (404).'], 404);
        }

        $viewFile = BASE_PATH . '/app/Views/errors/404.php';
        if (is_file($viewFile)) {
            require $viewFile;
        } else {
            echo "<h1>404 - Halaman Tidak Ditemukan</h1>";
        }
        exit;
    }

    private function executeHandler(string $handler, array $params): void
    {
        [$controllerName, $action] = explode('@', $handler);
        $fullControllerClass = "App\\Controllers\\" . $controllerName;

        if (!class_exists($fullControllerClass)) {
            throw new \RuntimeException("Controller {$fullControllerClass} tidak ditemukan.");
        }

        $controller = new $fullControllerClass();
        if (!method_exists($controller, $action)) {
            throw new \RuntimeException("Action {$action} tidak ditemukan di {$fullControllerClass}.");
        }

        call_user_func_array([$controller, $action], array_values($params));
    }
}
