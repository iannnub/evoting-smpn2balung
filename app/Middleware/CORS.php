<?php

namespace App\Middleware;

class CORS
{
    public static function handle(): void
    {
        $allowedOrigins = [
            'http://localhost:3000',
            'http://127.0.0.1:3000',
            'http://localhost:3001',
            'https://osisvoting.vercel.app',
            'https://evoting-smpn2balung.vercel.app',
            'https://evoting-smpn2balung.infinityfreeapp.com',
        ];

        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        if (in_array($origin, $allowedOrigins, true) || str_ends_with($origin, '.vercel.app')) {
            header("Access-Control-Allow-Origin: {$origin}");
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
            header("Access-Control-Allow-Credentials: true");
        } else {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
        }

        // Handle preflight OPTIONS
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
