<?php
/**
 * Helper global aplikasi.
 */

use App\Core\Session;

function config(string $key, mixed $default = null): mixed
{
    static $config = null;
    if ($config === null) {
        $config = [
            'app' => require BASE_PATH . '/config/app.php',
            'database' => require BASE_PATH . '/config/database.php',
        ];
    }
    $value = $config;
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

/** Escape output HTML (pencegahan XSS). */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Deteksi base URL agar aplikasi jalan di subfolder (localhost/osis) maupun root domain. */
function detect_base_url(): string
{
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $dir = preg_replace('#/public$#', '', $dir);
    return rtrim((string) $dir, '/');
}

function request_path(): string
{
    $path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
    foreach ([BASE_URL . '/public', BASE_URL] as $prefix) {
        if ($prefix !== '' && str_starts_with($path, $prefix)) {
            $path = substr($path, strlen($prefix));
            break;
        }
    }
    $path = '/' . trim($path, '/');
    return $path === '/index.php' ? '/' : $path;
}

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    $file = BASE_PATH . '/public/assets/' . ltrim($path, '/');
    $version = is_file($file) ? filemtime($file) : 0;
    return url('assets/' . ltrim($path, '/')) . '?v=' . $version;
}

function candidate_photo(?string $filename, ?int $orderNumber = null): string
{
    if ($filename && is_file(BASE_PATH . '/public/uploads/candidates/' . $filename)) {
        return url('uploads/candidates/' . rawurlencode($filename));
    }
    if ($orderNumber === 1 && is_file(BASE_PATH . '/public/assets/img/candidates/kandidat-1.svg')) {
        return asset('img/candidates/kandidat-1.svg');
    }
    if ($orderNumber === 2 && is_file(BASE_PATH . '/public/assets/img/candidates/kandidat-2.svg')) {
        return asset('img/candidates/kandidat-2.svg');
    }
    return asset('img/default-avatar.svg');
}

function candidate_photo_url(?string $filename, ?int $orderNumber = null): string
{
    return candidate_photo($filename, $orderNumber);
}

function is_api_request(): bool
{
    return str_starts_with(request_path(), '/api/')
        || (($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json')
        || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');
}

function current_path_is(string $prefix): bool
{
    $path = request_path();
    return $path === $prefix || str_starts_with($path, rtrim($prefix, '/') . '/');
}

/* ---------- CSRF ---------- */

function csrf_token(): string
{
    if (!Session::has('_csrf')) {
        Session::set('_csrf', bin2hex(random_bytes(32)));
    }
    return Session::get('_csrf');
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token): bool
{
    return is_string($token) && Session::has('_csrf') && hash_equals(Session::get('_csrf'), $token);
}

/* ---------- Response ---------- */

function redirect(string $path): never
{
    header('Location: ' . (preg_match('#^https?://#', $path) ? $path : url($path)));
    exit;
}

function back(string $fallback = '/'): never
{
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if ($ref !== '' && parse_url($ref, PHP_URL_HOST) === $host) {
        header('Location: ' . $ref);
        exit;
    }
    redirect($fallback);
}

function json(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function request_json(): array
{
    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);
    return is_array($data) ? $data : $_POST;
}

/* ---------- Flash & old input ---------- */

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    $items = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $items;
}

function with_input(array $input): void
{
    unset($input['csrf_token'], $input['password'], $input['password_confirmation'], $input['current_password']);
    $_SESSION['_old_next'] = $input;
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

/* ---------- Domain helpers ---------- */

/**
 * Normalisasi kelas: "7 A", "7a", "VII-A", "7.A" -> "7A".
 * Mengembalikan null jika tidak valid (harus tingkat 7-9 + huruf A-Z).
 */
function normalize_class(?string $raw): ?string
{
    $s = strtoupper(preg_replace('/[^0-9A-Za-z]/', '', (string) $raw));
    if ($s === 'GURU' || $s === 'DEWANGURU' || $s === 'GURUSTAF') {
        return 'GURU';
    }
    if (preg_match('/^(VIII|VII|IX)([A-Z])$/', $s, $m)) {
        $s = ['VII' => '7', 'VIII' => '8', 'IX' => '9'][$m[1]] . $m[2];
    }
    return preg_match('/^[7-9][A-Z]$/', $s) ? $s : null;
}

/** Tingkat (grade) otomatis dari kelas: 7 -> 1, 8 -> 2, 9 -> 3, GURU -> 4. */
function grade_from_class(string $class): int
{
    if ($class === 'GURU') {
        return 4;
    }
    return ((int) $class[0]) - 6;
}

function grade_label(int $grade): string
{
    if ($grade === 4) {
        return 'Dewan Guru';
    }
    return 'Kelas ' . ($grade + 6);
}

function client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

/**
 * Rate limit berbasis file (tahan terhadap hapus cookie).
 * Mengembalikan true jika request masih diizinkan.
 */
function rate_limit(string $key, int $max, int $windowSeconds): bool
{
    $dir = BASE_PATH . '/storage/cache';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $file = $dir . '/rl_' . md5($key) . '.json';
    $fp = fopen($file, 'c+');
    if ($fp === false) {
        return true;
    }
    flock($fp, LOCK_EX);
    $now = time();
    $hits = json_decode(stream_get_contents($fp) ?: '[]', true) ?: [];
    $hits = array_values(array_filter($hits, fn ($t) => $now - (int) $t < $windowSeconds));
    $allowed = count($hits) < $max;
    if ($allowed) {
        $hits[] = $now;
    }
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($hits));
    flock($fp, LOCK_UN);
    fclose($fp);
    return $allowed;
}

function format_date_id(?string $date, bool $withTime = false): string
{
    if (!$date) {
        return '-';
    }
    $ts = strtotime($date);
    if ($ts === false) {
        return '-';
    }
    $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $out = date('j', $ts) . ' ' . $months[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    return $withTime ? $out . ', ' . date('H.i', $ts) : $out;
}

function format_number(int|float $n, int $decimals = 0): string
{
    return number_format($n, $decimals, ',', '.');
}

function percent(int|float $part, int|float $total, int $decimals = 1): float
{
    return $total > 0 ? round($part * 100 / $total, $decimals) : 0.0;
}

function str_limit(?string $text, int $limit): string
{
    $text = trim(preg_replace('/\s+/', ' ', (string) $text));
    return mb_strlen($text) > $limit ? rtrim(mb_substr($text, 0, $limit)) . '…' : $text;
}

/** Pecah misi per baris menjadi list (buang bullet manual seperti "-", "1."). */
function mission_items(?string $text): array
{
    $items = [];
    foreach (preg_split('/\R/', (string) $text) as $line) {
        $line = trim(preg_replace('/^\s*(?:[-*•]|\d+[.)])\s*/u', '', $line));
        if ($line !== '') {
            $items[] = $line;
        }
    }
    return $items;
}

function clean_name(string $name): string
{
    return trim(preg_replace('/\s+/u', ' ', $name));
}

function is_strong_password(string $password): bool
{
    return strlen($password) >= 8 && preg_match('/[A-Za-z]/', $password) && preg_match('/\d/', $password);
}
