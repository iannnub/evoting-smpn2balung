<?php
return [
    'host' => env('DB_HOST', 'localhost'),
    'port' => (int) env('DB_PORT', 3306),
    'name' => env('DB_NAME', 'osis_voting'),
    'user' => env('DB_USER', 'root'),
    'pass' => (string) env('DB_PASS', ''),
];
