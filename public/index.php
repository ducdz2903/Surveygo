<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Controllers\HomeController;
use App\Controllers\AdminController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;

$router = new Router();

// Authentication routes.
$router->post('/api/register', [App\Controllers\AuthController::class, 'register']);
$router->post('/api/login', [App\Controllers\AuthController::class, 'login']);
// Profile update and password change
$router->post('/api/auth/update-profile', [App\Controllers\AuthController::class, 'updateProfile']);
$router->post('/api/auth/change-password', [App\Controllers\AuthController::class, 'changePassword']);

// Landing pages.
$router->get('/', [HomeController::class, 'home']);
$router->get('/home', [HomeController::class, 'homeAfterLogin']);
$router->get('/surveys', [HomeController::class, 'surveys']);
$router->get('/surveys/guide', [HomeController::class, 'surveyGuide']);
$router->get('/surveys/{id}/questions', [HomeController::class, 'surveyQuestions']);
$router->get('/quick-poll', [HomeController::class, 'quickPoll']);
$router->get('/daily-rewards', [HomeController::class, 'dailyRewards']);
$router->get('/events', [HomeController::class, 'events']);
$router->get('/rewards', [HomeController::class, 'rewards']);
$router->get('/contact', [HomeController::class, 'contact']);
$router->get('/terms-of-use', [HomeController::class, 'terms']);
$router->get('/login', [HomeController::class, 'login']);
$router->get('/register', [HomeController::class, 'register']);
$router->get('/profile', [HomeController::class, 'profile']);

// Admin routes
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
$router->get('/admin/surveys', [AdminController::class, 'surveys']);
$router->get('/admin/users', [AdminController::class, 'users']);
$router->get('/admin/questions', [AdminController::class, 'questions']);
$router->get('/admin/reports', [AdminController::class, 'reports']);
$router->get('/admin/events', [AdminController::class, 'events']);
$router->get('/admin/settings', [AdminController::class, 'settings']);

// Health check route.
$router->get('/api/health', fn() => Response::json([
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
$router->post('/api/surveys/{id}/submit', [App\Controllers\SurveyController::class, 'submit']); // Submit khảo sát
$router->get('/api/surveys/{id}/check-submission', [App\Controllers\SurveyController::class, 'checkSubmission']); // Kiểm tra đã submit chưa

// Question API routes
$router->get('/api/questions/{id}/answers', [App\Controllers\QuestionController::class, 'getAnswersForQuestion']); // Lấy danh sách đáp án của câu hỏi
$router->get('/api/questions', [App\Controllers\QuestionController::class, 'index']); // Lấy danh sách tất cả câu hỏi 
$router->get('/api/questions/by-survey', [App\Controllers\QuestionController::class, 'getBySurvey']); // Lấy câu hỏi theo khảo sát
$router->get('/api/questions/show', [App\Controllers\QuestionController::class, 'show']); // Lấy chi tiết một câu hỏi

// Events API
$router->get('/api/events', [App\Controllers\EventController::class, 'index']); // Lấy danh sách sự kiện (phân trang + tìm kiếm)

$router->post('/api/questions', [App\Controllers\QuestionController::class, 'create']); // Tạo câu hỏi mới
$router->put('/api/questions', [App\Controllers\QuestionController::class, 'update']); // Cập nhật câu hỏi
$router->delete('/api/questions', [App\Controllers\QuestionController::class, 'delete']); // Xóa câu hỏi

// Users API
$router->get('/api/users', [App\Controllers\UserController::class, 'index']); // Lấy danh sách users (phân trang + tìm kiếm)


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
