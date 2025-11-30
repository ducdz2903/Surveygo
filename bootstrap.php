<?php

declare(strict_types=1);

/**
 * Simple bootstrap file that sets up configuration, autoloading, and database connection.
 */

// Define base path for convenience.
define('BASE_PATH', __DIR__);

// Register a very small PSR-4-like autoloader for the App namespace.
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load application configuration.
$config = require BASE_PATH . '/config/app.php';

// Make config globally accessible through a simple container.
App\Core\Container::set('config', $config);

// Register database connection singleton.
App\Core\Container::set('db', function () use ($config) {
    return App\Core\Database::make($config['db']);
});