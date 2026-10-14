<?php
/**
 * Loader .env sederhana (tanpa Composer).
 */

function load_env(string $path): void
{
    // 1. Coba baca file utama (misal .env)
    if (is_file($path)) {
        parse_env_lines($path);
        return;
    }

    $dir = dirname($path);

    // 2. Fallback: env.php (sangat ramah hosting yang menyembunyikan dotfiles)
    if (is_file($dir . '/env.php')) {
        $data = require $dir . '/env.php';
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $_ENV[$k] = (string) $v;
                putenv("$k=" . (string) $v);
            }
            return;
        }
    }

    // 3. Fallback: env.txt
    if (is_file($dir . '/env.txt')) {
        parse_env_lines($dir . '/env.txt');
    }
}

function parse_env_lines(string $filePath): void
{
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $len = strlen($value);
        if ($len >= 2 && (($value[0] === '"' && $value[$len - 1] === '"') || ($value[0] === "'" && $value[$len - 1] === "'"))) {
            $value = substr($value, 1, -1);
        }
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

function env(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === false || $value === null) {
        return $default;
    }
    return match (strtolower((string) $value)) {
        'true' => true,
        'false' => false,
        'null' => null,
        default => $value,
    };
}
