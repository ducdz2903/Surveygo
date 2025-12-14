<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Controllers\HomeController;
use App\Controllers\AdminController;
use App\Controllers\DailyRewardController;
use App\Controllers\RewardController;
use App\Controllers\RewardRedemptionController;
use App\Controllers\UserPointController;
use App\Controllers\ActivityLogController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;
use App\Middlewares\GuestMiddleware;
use App\Helpers\ActivityLogHelper;

$router = new Router();

// Authentication routes - públicas
$router->post('/api/register', [App\Controllers\AuthController::class, 'register']);
$router->post('/api/login', [App\Controllers\AuthController::class, 'login']);
$router->post('/api/logout', function(Request $request) {
    session_destroy();
    return Response::json([
        'error' => false,
        'message' => 'Logout successful'
    ]);
});

// Rutas que requieren autenticación
$router->post('/api/auth/update-profile', [App\Controllers\AuthController::class, 'updateProfile'], [new AuthMiddleware()]);
$router->post('/api/auth/change-password', [App\Controllers\AuthController::class, 'changePassword'], [new AuthMiddleware()]);

// Landing pages - públicas
$router->get('/', [HomeController::class, 'home']);
$router->get('/home', [HomeController::class, 'homeAfterLogin']);
$router->get('/surveys', [HomeController::class, 'surveys']);
$router->get('/surveys/guide', [HomeController::class, 'surveyGuide']);
$router->get('/surveys/{id}/questions', [HomeController::class, 'surveyQuestions']);
$router->get('/quick-poll', [HomeController::class, 'quickPoll']);
$router->get('/daily-rewards', [HomeController::class, 'dailyRewards'], [new AuthMiddleware()]);
$router->get('/events', [HomeController::class, 'events'], [new AuthMiddleware()]);
$router->get('/rewards', [HomeController::class, 'rewards'], [new AuthMiddleware()]);
$router->get('/contact', [HomeController::class, 'contact']);
$router->get('/about', [HomeController::class, 'about']);
$router->get('/terms-of-use', [HomeController::class, 'terms']);
$router->get('/login', [HomeController::class, 'login']);
$router->get('/register', [HomeController::class, 'register']);

// Rutas de perfil - requieren autenticación
$router->get('/profile', [HomeController::class, 'profile'], [new AuthMiddleware()]);

// Admin routes - solo para admin
$router->get('/admin', [AdminController::class, 'dashboard'], [new RoleMiddleware(['admin'])]);
$router->get('/admin/dashboard', [AdminController::class, 'dashboard'], [new RoleMiddleware(['admin'])]);
$router->get('/admin/surveys', [AdminController::class, 'surveys'], [new RoleMiddleware(['admin'])]);
$router->get('/admin/surveys/view', [AdminController::class, 'surveyView'], [new RoleMiddleware(['admin'])]);
$router->get('/admin/users', [AdminController::class, 'users'], [new RoleMiddleware(['admin'])]);
$router->get('/admin/questions', [AdminController::class, 'questions'], [new RoleMiddleware(['admin'])]);
$router->get('/admin/reports', [AdminController::class, 'reports'], [new RoleMiddleware(['admin'])]);
$router->get('/admin/events', [AdminController::class, 'events'], [new RoleMiddleware(['admin'])]);
$router->get('/admin/settings', [AdminController::class, 'settings'], [new RoleMiddleware(['admin'])]);
$router->get('/admin/feedbacks', [AdminController::class, 'feedbacks'], [new RoleMiddleware(['admin'])]);
$router->get('/admin/contact-messages', [AdminController::class, 'contactMessages'], [new RoleMiddleware(['admin'])]);

// Health check route
$router->get('/api/health', fn() => Response::json([
    'error' => false,
    'message' => 'API is running',
    'time' => date(DATE_ATOM),
]));

// Survey API routes

$router->post('/api/surveys/quick-poll', [App\Controllers\SurveyController::class, 'createQuickPoll']); // Tạo quick poll
$router->get('/api/surveys', [App\Controllers\SurveyController::class, 'index']); // Lấy ra danh sách khảo sát
$router->get('/api/surveys/show', [App\Controllers\SurveyController::class, 'show']); // Lấy chi tiết một khảo sát
$router->post('/api/surveys', [App\Controllers\SurveyController::class, 'create'], [new RoleMiddleware(['admin', 'moderator'])]);
$router->put('/api/surveys', [App\Controllers\SurveyController::class, 'update'], [new RoleMiddleware(['admin', 'moderator'])]);
$router->delete('/api/surveys', [App\Controllers\SurveyController::class, 'delete'], [new RoleMiddleware(['admin'])]);
$router->post('/api/surveys/publish', [App\Controllers\SurveyController::class, 'publish'], [new RoleMiddleware(['admin', 'moderator'])]);
$router->post('/api/surveys/approve', [App\Controllers\SurveyController::class, 'approve'], [new RoleMiddleware(['admin', 'moderator'])]);
$router->post('/api/surveys/attach-question', [App\Controllers\SurveyController::class, 'attachQuestion'], [new RoleMiddleware(['admin', 'moderator'])]);
$router->post('/api/surveys/detach-question', [App\Controllers\SurveyController::class, 'detachQuestion'], [new RoleMiddleware(['admin', 'moderator'])]);
$router->post('/api/surveys/{id}/submit', [App\Controllers\SurveyController::class, 'submit'], [new AuthMiddleware()]); // Submit khảo sát
$router->get('/api/surveys/{id}/check-submission', [App\Controllers\SurveyController::class, 'checkSubmission'], [new AuthMiddleware()]); // Kiểm tra đã submit chưa

// Question API routes
$router->get('/api/questions/{id}/answers', [App\Controllers\QuestionController::class, 'getAnswersForQuestion']);
$router->get('/api/questions', [App\Controllers\QuestionController::class, 'index']);
$router->get('/api/questions/by-survey', [App\Controllers\QuestionController::class, 'getBySurvey']);
$router->get('/api/questions/show', [App\Controllers\QuestionController::class, 'show']);
$router->post('/api/questions', [App\Controllers\QuestionController::class, 'create'], [new RoleMiddleware(['admin', 'moderator'])]);
$router->put('/api/questions', [App\Controllers\QuestionController::class, 'update'], [new RoleMiddleware(['admin', 'moderator'])]);
$router->delete('/api/questions', [App\Controllers\QuestionController::class, 'delete'], [new RoleMiddleware(['admin'])]);
$router->delete('/api/questions/{id}', [App\Controllers\QuestionController::class, 'delete']); // Xóa câu hỏi

// Contact messages API
$router->put('/api/contact-messages', [App\Controllers\ContactController::class, 'update'], [new RoleMiddleware(['admin'])]);
$router->delete('/api/contact-messages', [App\Controllers\ContactController::class, 'delete'], [new RoleMiddleware(['admin'])]);
// Events API
$router->get('/api/events', [App\Controllers\EventController::class, 'index']);
$router->post('/api/events', [App\Controllers\EventController::class, 'create'], [new RoleMiddleware(['admin', 'moderator'])]);
$router->post('/api/events/{id}/join', [App\Controllers\EventController::class, 'join'], [new AuthMiddleware()]);
$router->post('/api/events/lucky-wheel/spin', [App\Controllers\EventController::class, 'spinLuckyWheel']);
$router->get('/api/users/points', [App\Controllers\UserController::class, 'getPoints']);


// Feedbacks API
$router->get('/api/feedbacks', [App\Controllers\FeedbackController::class, 'index'], [new AuthMiddleware()]);
$router->get('/api/feedbacks/show', [App\Controllers\FeedbackController::class, 'show'], [new AuthMiddleware()]);
$router->post('/api/feedbacks', [App\Controllers\FeedbackController::class, 'create'], [new AuthMiddleware()]);
$router->post('/api/feedbacks/submit', [App\Controllers\FeedbackController::class, 'submit'], [new AuthMiddleware()]);
$router->put('/api/feedbacks', [App\Controllers\FeedbackController::class, 'update'], [new RoleMiddleware(['admin'])]);
$router->delete('/api/feedbacks', [App\Controllers\FeedbackController::class, 'delete'], [new RoleMiddleware(['admin'])]);

// Contact messages API - solo para admin
$router->get('/api/contact-messages', [App\Controllers\ContactController::class, 'index'], [new RoleMiddleware(['admin'])]);
$router->get('/api/contact-messages/show', [App\Controllers\ContactController::class, 'show'], [new RoleMiddleware(['admin'])]);
$router->post('/api/contact-messages', [App\Controllers\ContactController::class, 'create']);

// Daily rewards API
$router->get('/api/daily-rewards/status', [DailyRewardController::class, 'status'], [new AuthMiddleware()]);
$router->post('/api/daily-rewards/claim', [DailyRewardController::class, 'claim'], [new AuthMiddleware()]);

// Users API - solo para admin
$router->get('/api/users', [App\Controllers\UserController::class, 'index'], [new RoleMiddleware(['admin'])]);

// Rewards API
$router->get('/api/rewards', [RewardController::class, 'apiList']);
$router->get('/api/rewards/giftcard/details', [RewardController::class, 'getGiftCardDetails']);
$router->get('/api/rewards/filter', [RewardController::class, 'filter']);
$router->get('/api/rewards/search', [RewardController::class, 'search']);
$router->get('/api/rewards/{id}', [RewardController::class, 'detail']);
$router->post('/api/rewards/redeem', [RewardController::class, 'redeem'], [new AuthMiddleware()]);

// Admin Rewards API - solo para admin
$router->get('/admin/rewards', [RewardController::class, 'adminIndex'], [new RoleMiddleware(['admin'])]);
$router->get('/admin/rewards/create', [RewardController::class, 'adminCreate'], [new RoleMiddleware(['admin'])]);
$router->post('/admin/rewards/create', [RewardController::class, 'adminCreate'], [new RoleMiddleware(['admin'])]);
$router->get('/admin/rewards/{id}/edit', [RewardController::class, 'adminEdit'], [new RoleMiddleware(['admin'])]);
$router->post('/admin/rewards/{id}/edit', [RewardController::class, 'adminEdit'], [new RoleMiddleware(['admin'])]);
$router->post('/api/admin/rewards', [RewardController::class, 'apiCreateReward'], [new RoleMiddleware(['admin'])]);
$router->put('/api/admin/rewards/{id}', [RewardController::class, 'apiUpdateReward'], [new RoleMiddleware(['admin'])]);
$router->post('/api/admin/rewards/{id}/delete', [RewardController::class, 'adminDelete'], [new RoleMiddleware(['admin'])]);
$router->post('/api/admin/rewards/{id}/toggle', [RewardController::class, 'adminToggle'], [new RoleMiddleware(['admin'])]);
$router->post('/api/admin/rewards/{id}/stock', [RewardController::class, 'adminUpdateStock'], [new RoleMiddleware(['admin'])]);
$router->get('/api/admin/rewards', [RewardController::class, 'listRewards'], [new RoleMiddleware(['admin'])]);

// Admin Redemptions - solo para admin
$router->get('/admin/redemptions', [RewardRedemptionController::class, 'adminIndex'], [new RoleMiddleware(['admin'])]);
$router->post('/api/admin/rewards/redemptions/{id}/status', [RewardController::class, 'adminUpdateRedemptionStatus'], [new RoleMiddleware(['admin'])]);
$router->get('/admin/rewards/stats', [RewardController::class, 'adminStats'], [new RoleMiddleware(['admin'])]);

// User Points API
$router->get('/api/user-points/balance', [UserPointController::class, 'getBalance'], [new AuthMiddleware()]);
$router->get('/api/user-points/check', [UserPointController::class, 'hasEnoughPoints'], [new AuthMiddleware()]);

// Reward Redemptions API (Client)
$router->get('/api/redemptions/my', [RewardRedemptionController::class, 'myRedemptions'], [new AuthMiddleware()]);
$router->get('/api/redemptions/detail', [RewardRedemptionController::class, 'detail'], [new AuthMiddleware()]);
$router->post('/api/redemptions/create', [RewardRedemptionController::class, 'create'], [new AuthMiddleware()]);

// Reward Redemptions API (Admin)
$router->get('/api/admin/redemptions', [RewardRedemptionController::class, 'apiList'], [new RoleMiddleware(['admin'])]);
$router->post('/api/admin/redemptions/update-status', [RewardRedemptionController::class, 'updateStatus'], [new RoleMiddleware(['admin'])]);
$router->post('/api/admin/redemptions/delete', [RewardRedemptionController::class, 'delete'], [new RoleMiddleware(['admin'])]);
$router->get('/api/admin/redemptions/stats', [RewardRedemptionController::class, 'stats'], [new RoleMiddleware(['admin'])]);

// Activity Log Routes
$router->get('/api/activity-logs/my', [ActivityLogController::class, 'getMyLogs'], [new AuthMiddleware()]);
$router->get('/api/admin/activity-logs', [ActivityLogController::class, 'index'], [new RoleMiddleware(['admin'])]);
$router->get('/api/admin/activity-logs/user/:id', [ActivityLogController::class, 'getUserLogs'], [new RoleMiddleware(['admin'])]);
$router->get('/api/admin/activity-logs/entity/:type/:id', [ActivityLogController::class, 'getEntityLogs'], [new RoleMiddleware(['admin'])]);
$router->get('/api/admin/activity-logs/action/:action', [ActivityLogController::class, 'getActionLogs'], [new RoleMiddleware(['admin'])]);
$router->delete('/api/admin/activity-logs/cleanup', [ActivityLogController::class, 'cleanup'], [new RoleMiddleware(['admin'])]);
$router->get('/admin/activity-logs/view', [ActivityLogController::class, 'viewPage'], [new RoleMiddleware(['admin'])]);

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
