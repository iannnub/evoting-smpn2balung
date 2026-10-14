<?php

namespace App\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $lifetime = (int) config('app.session_lifetime', 7200);
            ini_set('session.gc_maxlifetime', (string) $lifetime);
            
            // Set session cookie parameters
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            session_start();
        }

        // Handle auto-expire based on last activity
        if (self::has('last_activity')) {
            $lifetime = (int) config('app.session_lifetime', 7200);
            if (time() - self::get('last_activity') > $lifetime) {
                self::destroy();
                session_start();
            }
        }
        self::set('last_activity', time());

        // Manage flash old input
        if (isset($_SESSION['_old_next'])) {
            $_SESSION['_old'] = $_SESSION['_old_next'];
            unset($_SESSION['_old_next']);
        } else {
            unset($_SESSION['_old']);
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
            session_destroy();
        }
    }
}
