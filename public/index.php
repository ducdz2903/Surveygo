<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Controllers\HomeController;
use App\Controllers\AdminController;
use App\Controllers\DailyRewardController;
use App\Controllers\RewardController;
use App\Controllers\RewardRedemptionController;
use App\Controllers\UserPointController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;

$router = new Router();

// Authentication routes.
$router->post('/api/register', [App\Controllers\AuthController::class, 'register']);
$router->post('/api/login', [App\Controllers\AuthController::class, 'login']);
// Profile update and password change endpoints
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
$router->get('/admin/surveys/view', [AdminController::class, 'surveyView']);
$router->get('/admin/users', [AdminController::class, 'users']);
$router->get('/admin/questions', [AdminController::class, 'questions']);
$router->get('/admin/reports', [AdminController::class, 'reports']);
$router->get('/admin/events', [AdminController::class, 'events']);
$router->get('/admin/settings', [AdminController::class, 'settings']);
$router->get('/admin/feedbacks', [AdminController::class, 'feedbacks']);
$router->get('/admin/contact-messages', [AdminController::class, 'contactMessages']);

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
$router->post('/api/surveys/attach-question', [App\Controllers\SurveyController::class, 'attachQuestion']);
$router->post('/api/surveys/detach-question', [App\Controllers\SurveyController::class, 'detachQuestion']);
$router->post('/api/surveys/{id}/submit', [App\Controllers\SurveyController::class, 'submit']); // Submit khảo sát
$router->get('/api/surveys/{id}/check-submission', [App\Controllers\SurveyController::class, 'checkSubmission']); // Kiểm tra đã submit chưa

// Question API routes
$router->get('/api/questions/{id}/answers', [App\Controllers\QuestionController::class, 'getAnswersForQuestion']); // Lấy danh sách đáp án của câu hỏi
$router->get('/api/questions', [App\Controllers\QuestionController::class, 'index']); // Lấy danh sách tất cả câu hỏi 
$router->get('/api/questions/by-survey', [App\Controllers\QuestionController::class, 'getBySurvey']); // Lấy câu hỏi theo khảo sát
$router->get('/api/questions/show', [App\Controllers\QuestionController::class, 'show']); // Lấy chi tiết một câu hỏi

// Events API
$router->get('/api/events', [App\Controllers\EventController::class, 'index']); // Lấy danh sách sự kiện (phân trang + tìm kiếm)

// Feedbacks API
$router->get('/api/feedbacks', [App\Controllers\FeedbackController::class, 'index']);
$router->get('/api/feedbacks/show', [App\Controllers\FeedbackController::class, 'show']);
$router->post('/api/feedbacks', [App\Controllers\FeedbackController::class, 'create']);
$router->post('/api/feedbacks/submit', [App\Controllers\FeedbackController::class, 'submit']);
$router->put('/api/feedbacks', [App\Controllers\FeedbackController::class, 'update']);
$router->delete('/api/feedbacks', [App\Controllers\FeedbackController::class, 'delete']);

// Contact messages API
$router->get('/api/contact-messages', [App\Controllers\ContactController::class, 'index']);
$router->get('/api/contact-messages/show', [App\Controllers\ContactController::class, 'show']);
$router->post('/api/contact-messages', [App\Controllers\ContactController::class, 'create']);
$router->put('/api/contact-messages', [App\Controllers\ContactController::class, 'update']);
$router->delete('/api/contact-messages', [App\Controllers\ContactController::class, 'delete']);

$router->post('/api/questions', [App\Controllers\QuestionController::class, 'create']); // Tạo câu hỏi mới
$router->put('/api/questions', [App\Controllers\QuestionController::class, 'update']); // Cập nhật câu hỏi
$router->delete('/api/questions', [App\Controllers\QuestionController::class, 'delete']); // Xóa câu hỏi

// Daily rewards API
$router->get('/api/daily-rewards/status', [DailyRewardController::class, 'status']);
$router->post('/api/daily-rewards/claim', [DailyRewardController::class, 'claim']);

// Users API
$router->get('/api/users', [App\Controllers\UserController::class, 'index']); // Lấy danh sách users (phân trang + tìm kiếm)

// Rewards API
$router->get('/api/rewards', [RewardController::class, 'apiList']); // Lấy danh sách quà (JSON)
$router->get('/api/rewards/giftcard/details', [RewardController::class, 'getGiftCardDetails']); // Chi tiết gift card
$router->get('/api/rewards/filter', [RewardController::class, 'filter']); // Lọc quà
$router->get('/api/rewards/search', [RewardController::class, 'search']); // Tìm kiếm quà
$router->get('/api/rewards/{id}', [RewardController::class, 'detail']); // Chi tiết quà
$router->post('/api/rewards/redeem', [RewardController::class, 'redeem']); // Đổi quà

// Admin Rewards API
$router->get('/admin/rewards', [RewardController::class, 'adminIndex']); // Danh sách quà (admin)
$router->get('/admin/rewards/create', [RewardController::class, 'adminCreate']); // Form tạo
$router->post('/admin/rewards/create', [RewardController::class, 'adminCreate']); // Lưu quà
$router->get('/admin/rewards/{id}/edit', [RewardController::class, 'adminEdit']); // Form sửa
$router->post('/admin/rewards/{id}/edit', [RewardController::class, 'adminEdit']); // Lưu sửa
$router->post('/api/admin/rewards', [RewardController::class, 'apiCreateReward']); // Tạo quà (API JSON)
$router->put('/api/admin/rewards/{id}', [RewardController::class, 'apiUpdateReward']); // Cập nhật quà (API JSON)
$router->post('/api/admin/rewards/{id}/delete', [RewardController::class, 'adminDelete']); // Xóa quà
$router->post('/api/admin/rewards/{id}/toggle', [RewardController::class, 'adminToggle']); // Kích hoạt/vô hiệu
$router->post('/api/admin/rewards/{id}/stock', [RewardController::class, 'adminUpdateStock']); // Cập nhật stock
$router->get('/api/admin/rewards', [RewardController::class, 'listRewards']); // Lấy danh sách quà (API)

// Admin Redemptions
$router->get('/admin/redemptions', [RewardRedemptionController::class, 'adminIndex']); // Danh sách redemptions (admin)
$router->post('/api/admin/rewards/redemptions/{id}/status', [RewardController::class, 'adminUpdateRedemptionStatus']); // Cập nhật trạng thái
$router->get('/admin/rewards/stats', [RewardController::class, 'adminStats']); // Thống kê

// User Points API
$router->get('/api/user-points/balance', [UserPointController::class, 'getBalance']); // Lấy số điểm hiện tại
$router->get('/api/user-points/check', [UserPointController::class, 'hasEnoughPoints']); // Kiểm tra đủ điểm

// Reward Redemptions API (Client)
$router->get('/api/redemptions/my', [RewardRedemptionController::class, 'myRedemptions']); // Danh sách redemption của user
$router->get('/api/redemptions/detail', [RewardRedemptionController::class, 'detail']); // Chi tiết redemption
$router->post('/api/redemptions/create', [RewardRedemptionController::class, 'create']); // Tạo redemption

// Reward Redemptions API (Admin)
$router->get('/api/admin/redemptions', [RewardRedemptionController::class, 'apiList']); // Danh sách redemption
$router->post('/api/admin/redemptions/update-status', [RewardRedemptionController::class, 'updateStatus']); // Cập nhật trạng thái
$router->post('/api/admin/redemptions/delete', [RewardRedemptionController::class, 'delete']); // Xóa redemption
$router->get('/api/admin/redemptions/stats', [RewardRedemptionController::class, 'stats']); // Thống kê

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
