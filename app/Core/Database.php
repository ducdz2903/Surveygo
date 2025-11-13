<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    public static function make(array $config): PDO
    {
        $driver = $config['driver'] ?? 'mysql';

        try {
            switch ($driver) {
                case 'mysql':
                    $host = $config['host'] ?? '127.0.0.1';
                    $database = $config['database'] ?? '';
                    if ($database === '') {
                        throw new \InvalidArgumentException('MySQL database name is required.');
                    }
                    $charset = $config['charset'] ?? 'utf8mb4';
                    $port = $config['port'] ?? 3306;
                    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";
                    $options = $config['options'] ?? [];
                    $pdo = new PDO(
                        $dsn,
                        $config['username'] ?? null,
                        $config['password'] ?? null,
                        $options
                    );
                    break;
                    case 'mariadb':
                    $host = $config['host'] ?? '127.0.0.1';
                    $database = $config['database'] ?? '';
                    if ($database === '') {
                        throw new \InvalidArgumentException('MySQL database name is required.');
                    }
                    $charset = $config['charset'] ?? 'utf8mb4';
                    $port = $config['port'] ?? 3306;
                    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";
                    $options = $config['options'] ?? [];
                    $pdo = new PDO(
                        $dsn,
                        $config['username'] ?? null,
                        $config['password'] ?? null,
                        $options
                    );
                    break;
                default:
                    throw new \InvalidArgumentException("Unsupported database driver {$driver}. Only 'mysql' is supported.");
            }
        } catch (PDOException $exception) {
            throw new \RuntimeException('Database connection failed: ' . $exception->getMessage());
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;
    }
}
