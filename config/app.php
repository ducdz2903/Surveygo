<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => 'Surveygo',
        'env' => 'localhost',
        'debug' => true,
        'base_url' => '',
        "check_payment_url" => "http://localhost:8002",
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
    'purchase_url' => 'http://localhost:8000',
];
