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

// Survey API routes
$router->get('/api/surveys', [App\Controllers\SurveyController::class, 'index']); // Lấy ra danh sách khảo sát
$router->get('/api/surveys/show', [App\Controllers\SurveyController::class, 'show']); // Lấy chi tiết một khảo sát
$router->post('/api/surveys', [App\Controllers\SurveyController::class, 'create']);
$router->put('/api/surveys', [App\Controllers\SurveyController::class, 'update']);
$router->delete('/api/surveys', [App\Controllers\SurveyController::class, 'delete']);
$router->post('/api/surveys/publish', [App\Controllers\SurveyController::class, 'publish']);
$router->post('/api/surveys/approve', [App\Controllers\SurveyController::class, 'approve']);

// Question API routes
$router->get('/api/questions', [App\Controllers\QuestionController::class, 'index']); // Lấy danh sách tất cả câu hỏi 
$router->get('/api/questions/by-survey', [App\Controllers\QuestionController::class, 'getBySurvey']); // Lấy câu hỏi theo khảo sát
$router->get('/api/questions/show', [App\Controllers\QuestionController::class, 'show']); // Lấy chi tiết một câu hỏi

$router->post('/api/questions', [App\Controllers\QuestionController::class, 'create']); // Tạo câu hỏi mới
$router->put('/api/questions', [App\Controllers\QuestionController::class, 'update']); // Cập nhật câu hỏi
$router->delete('/api/questions', [App\Controllers\QuestionController::class, 'delete']); // Xóa câu hỏi


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
