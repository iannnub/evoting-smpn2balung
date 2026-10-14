<?php
return [
    'name' => env('APP_NAME', 'Pemilihan OSIS SMPN 2 BALUNG'),
    'env' => env('APP_ENV', 'production'),
    'school' => env('SCHOOL_NAME', 'SMPN 2 BALUNG'),
    'region' => env('SCHOOL_REGION', 'Kabupaten Jember'),
    'timezone' => 'Asia/Jakarta',
    'session_lifetime' => (int) env('SESSION_LIFETIME', 7200),
    'upload_max_size' => (int) env('UPLOAD_MAX_SIZE', 2 * 1024 * 1024),
    'import_max_rows' => (int) env('IMPORT_MAX_ROWS', 1000),
];
