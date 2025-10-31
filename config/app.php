<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => 'PHP MVC Demo',
        'env' => 'local',
        'debug' => true,
        // Leave empty to auto-detect base URL (works under subfolders like /Surveyon)
        'base_url' => '',
    ],
    'db' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'mvc_app',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    'jwt' => [
        // Replace this secret in production.
        'secret' => 'change-this-secret-key',
        'algo' => 'HS256',
        'issuer' => 'php-mvc-jwt-demo',
        'audience' => 'php-mvc-jwt-demo-users',
        'ttl' => 3600, // 1 hour
        'refresh_ttl' => 1209600, // 14 days
    ],
];
