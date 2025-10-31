<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Controllers\HomeController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;

$router = new Router();

// Authentication routes.
$router->post('/api/register', [App\Controllers\AuthController::class, 'register']);
$router->post('/api/login', [App\Controllers\AuthController::class, 'login']);

// Landing pages.
$router->get('/', [HomeController::class, 'home']);
$router->get('/features', [HomeController::class, 'features']);
$router->get('/login', [HomeController::class, 'login']);
$router->get('/register', [HomeController::class, 'register']);

// Health check route.
$router->get('/api/health', fn () => Response::json([
    'error' => false,
    'message' => 'API is running',
    'time' => date(DATE_ATOM),
]));

// API documentation routes removed.


$request = Request::capture();

try {
    $response = $router->dispatch($request);
} catch (\Throwable $exception) {
    $response = Response::json([
        'error' => true,
        'message' => $exception->getMessage(),
        'trace' => $request->isDebug() ? $exception->getTraceAsString() : null,
    ], 500);
}

$response->send();
