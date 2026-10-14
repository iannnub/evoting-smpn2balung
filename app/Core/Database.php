<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $config = config('database');
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $config['host'],
                $config['port'],
                $config['name']
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$instance = new PDO($dsn, $config['user'], $config['pass'], $options);
            } catch (PDOException $e) {
                error_log('Database connection error: ' . $e->getMessage());
                $envStatus = file_exists(BASE_PATH . '/.env') ? '.env ADA' : '.env TIDAK DITEMUKAN di ' . BASE_PATH . '/.env';
                throw new RuntimeException(
                    'Gagal terhubung ke database MySQL: ' . $e->getMessage() . 
                    ' [Status: ' . $envStatus . ', Host: ' . ($config['host'] ?? '-') . ', DB: ' . ($config['name'] ?? '-') . ', User: ' . ($config['user'] ?? '-') . ']'
                );
            }
        }

        return self::$instance;
    }
}
