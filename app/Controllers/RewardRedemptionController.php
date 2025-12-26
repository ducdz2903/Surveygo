<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Container;
use App\Models\RewardRedemption;
use App\Models\Reward;
use App\Models\UserPoint;
use App\Helpers\ActivityLogHelper;

class RewardRedemptionController extends Controller
{
    private $redemptionModel;
    private $rewardModel;

    public function __construct()
    {
        $this->redemptionModel = new RewardRedemption();
        $this->rewardModel = new Reward();
    }

    /**
     * Hiển thị trang quản lý redemptions (admin)
     */
    public function adminIndex(Request $request)
    {
        $admin = new AdminController();
        return $admin->redemptions($request);
    }

    /**
     * Lấy danh sách redemption của user (client)
     */
    public function myRedemptions(Request $request)
    {
        $userId = $this->getCurrentUser($request);

        if (!$userId) {
            return Response::json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Lấy page từ query hoặc input
        $page = (int) ($request->input('page') ?? $request->query('page') ?? 1);
        $limit = (int) ($request->input('limit') ?? $request->query('limit') ?? 10);
        $offset = ($page - 1) * $limit;

        $redemptions = $this->redemptionModel->getByUserId($userId, $limit, $offset);
        $total = $this->redemptionModel->count(['user_id' => $userId]);

        return Response::json([
            'success' => true,
            'data' => $redemptions,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    /**
     * Lấy chi tiết redemption
     */
    public function detail(Request $request)
    {
        $userId = $this->getCurrentUser($request);
        $id = (int) $request->input('id');

        if (!$userId || !$id) {
            return Response::json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $redemption = $this->redemptionModel->getById($id);

        if (!$redemption) {
            return Response::json(['success' => false, 'message' => 'Redemption not found'], 404);
        }

        // Kiểm quyền: user chỉ xem được redemption của mình
        if ($redemption['user_id'] != $userId) {
            return Response::json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return Response::json(['success' => true, 'data' => $redemption]);
    }

    /**
     * Tạo redemption mới (đổi quà)
     */
    public function create(Request $request)
    {
        $userId = $this->getCurrentUser($request);

        if (!$userId) {
            return Response::json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $rewardId = (int) ($request->input('reward_id') ?? 0);
        $receiverInfo = $request->input('receiver_info');
        $bankName = $request->input('bank_name');
        $accountNumber = $request->input('account_number');

        if (!$rewardId) {
            return Response::json(['success' => false, 'message' => 'Reward ID is required'], 400);
        }

        // Lấy thông tin reward
        $reward = $this->rewardModel->getById($rewardId);
        if (!$reward) {
            return Response::json(['success' => false, 'message' => 'Reward not found'], 404);
        }

        // Kiểm tra stock (chỉ check nếu stock không null, vì null = unlimited)
        if ($reward['stock'] !== null && $reward['stock'] <= 0) {
            return Response::json(['success' => false, 'message' => 'Phần thưởng đã hết'], 400);
        }

        // Kiểm tra user có đủ points không
        $userPoint = UserPoint::findByUserId($userId);
        if (!$userPoint || $userPoint->getBalance() < $reward['point_cost']) {
            return Response::json(['success' => false, 'message' => 'Bạn không đủ điểm để đổi phần thưởng này'], 400);
        }

        // Bắt đầu transaction
        try {
            $db = Container::get('db');
            $db->beginTransaction();

            // Tạo redemption record
            $redemptionId = $this->redemptionModel->create(
                $userId,
                $rewardId,
                'pending',
                $receiverInfo,
                null,
                $bankName,
                $accountNumber
            );

            if (!$redemptionId) {
                $db->rollBack();
                return Response::json(['success' => false, 'message' => 'Failed to create redemption'], 500);
            }

            // Trừ points từ user
            $newBalance = $userPoint->getBalance() - $reward['point_cost'];
            $db->prepare("UPDATE user_points SET balance = ?, updated_at = NOW() WHERE user_id = ?")
                ->execute([$newBalance, $userId]);

            // Giảm stock của reward
            $reward['stock'] -= 1;
            $db->prepare("UPDATE rewards SET stock = ?, updated_at = NOW() WHERE id = ?")
                ->execute([$reward['stock'], $rewardId]);

            $db->commit();

            // Log activity
            try {
                error_log('[RewardRedemptionController::create] Attempting to log redemption - userId: ' . $userId . ', redemptionId: ' . $redemptionId . ', rewardId: ' . $rewardId);
                ActivityLogHelper::logRewardRedeemed((int) $userId, (int) $redemptionId, (int) $rewardId);
                error_log('[RewardRedemptionController::create] Activity logged successfully');
            } catch (\Throwable $e) {
                error_log('[RewardRedemptionController::create] Failed to log activity: ' . $e->getMessage());
                error_log('[RewardRedemptionController::create] Stack trace: ' . $e->getTraceAsString());
            }

            return Response::json([
                'success' => true,
                'message' => 'Redemption created successfully',
                'data' => [
                    'redemption_id' => $redemptionId,
                    'user_balance' => $newBalance
                ]
            ]);
        } catch (\Exception $e) {
            $db->rollBack();
            return Response::json(['success' => false, 'message' => 'Transaction error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Lưu tên chủ tài khoản (tự động gọi khi verify thành công)
     */
    public function saveAccountName(Request $request)
    {
        $id = (int) ($request->input('id') ?? 0);
        $accountName = $request->input('account_name') ?? '';

        if (!$id) {
            return Response::json(['success' => false, 'message' => 'Invalid request - ID required'], 400);
        }

        $redemption = $this->redemptionModel->getById($id);
        if (!$redemption) {
            return Response::json(['success' => false, 'message' => 'Redemption not found'], 404);
        }

        $result = $this->redemptionModel->updateAccountName($id, $accountName);

        if ($result) {
            return Response::json(['success' => true, 'message' => 'Account name saved successfully']);
        }

        return Response::json(['success' => false, 'message' => 'Failed to save account name'], 500);
    }

    /**
     * Lưu trạng thái chuyển khoản (tự động gọi khi transfer thành công)
     */
    public function saveTransferStatus(Request $request)
    {
        $id = (int) ($request->input('id') ?? 0);
        $transferStatus = $request->input('transfer_status') ?? 'pending';

        if (!$id) {
            return Response::json(['success' => false, 'message' => 'Invalid request - ID required'], 400);
        }

        $validStatuses = ['pending', 'completed', 'failed'];
        if (!in_array($transferStatus, $validStatuses)) {
            return Response::json(['success' => false, 'message' => 'Invalid transfer status'], 400);
        }

        $redemption = $this->redemptionModel->getById($id);
        if (!$redemption) {
            return Response::json(['success' => false, 'message' => 'Redemption not found'], 404);
        }

        $result = $this->redemptionModel->updateTransferStatus($id, $transferStatus);

        if ($result) {
            return Response::json(['success' => true, 'message' => 'Transfer status saved successfully']);
        }

        return Response::json(['success' => false, 'message' => 'Failed to save transfer status'], 500);
    }

    /**
     * Cập nhật status redemption (admin) - chỉ cập nhật status, không cập nhật account_name nữa
     */
    public function updateStatus(Request $request)
    {
        $id = (int) ($request->input('id') ?? 0);
        $status = $request->input('status');
        $note = $request->input('note');

        if (!$id || !$status) {
            return Response::json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $validStatuses = ['pending', 'processing', 'completed', 'rejected'];
        if (!in_array($status, $validStatuses)) {
            return Response::json(['success' => false, 'message' => 'Invalid status'], 400);
        }

        $redemption = $this->redemptionModel->getById($id);
        if (!$redemption) {
            return Response::json(['success' => false, 'message' => 'Redemption not found'], 404);
        }

        $result = $this->redemptionModel->updateStatus($id, $status, $note);

        if ($result) {
            // Nếu reject, hoàn lại points
            if ($status === 'rejected') {
                $reward = $this->rewardModel->getById($redemption['reward_id']);
                if ($reward) {
                    $userPoint = UserPoint::findByUserId($redemption['user_id']);
                    if ($userPoint) {
                        // Hoàn lại points trực tiếp vào DB
                        $pointCost = (int) ($reward['point_cost'] ?? $reward['points'] ?? 0);
                        if ($pointCost > 0) {
                            $db = Container::get('db');
                            $db->prepare("UPDATE user_points SET balance = balance + ?, updated_at = NOW() WHERE user_id = ?")
                                ->execute([$pointCost, $redemption['user_id']]);
                        }
                    }

                    // Hoàn lại stock
                    $db = Container::get('db');
                    $db->prepare("UPDATE rewards SET stock = stock + 1, updated_at = NOW() WHERE id = ?")
                        ->execute([$redemption['reward_id']]);
                }
            }

            return Response::json(['success' => true, 'message' => 'Status updated successfully']);
        }

        return Response::json(['success' => false, 'message' => 'Failed to update status'], 500);
    }

    /**
     * Xóa redemption (admin)
     */
    public function delete()
    {
        $request = new Request();
        $id = (int) ($request->post('id') ?? 0);

        if (!$id) {
            return Response::json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $redemption = $this->redemptionModel->getById($id);
        if (!$redemption) {
            return Response::json(['success' => false, 'message' => 'Redemption not found'], 404);
        }

        // Chỉ có thể xóa pending redemptions
        if ($redemption['status'] !== 'pending') {
            return Response::json(['success' => false, 'message' => 'Can only delete pending redemptions'], 400);
        }

        try {
            $db = Container::get('db');
            $db->beginTransaction();

            // Xóa redemption
            $this->redemptionModel->delete($id);

            // Hoàn lại points và stock
            $reward = $this->rewardModel->getById($redemption['reward_id']);
            if ($reward) {
                $userPoint = UserPoint::findByUserId($redemption['user_id']);
                if ($userPoint) {
                    $userPoint['balance'] += $reward['point_cost'];
                    $db->prepare("UPDATE user_points SET balance = ?, updated_at = NOW() WHERE user_id = ?")
                        ->execute([$userPoint['balance'], $redemption['user_id']]);
                }

                $db->prepare("UPDATE rewards SET stock = stock + 1, updated_at = NOW() WHERE id = ?")
                    ->execute([$redemption['reward_id']]);
            }

            $db->commit();

            return Response::json(['success' => true, 'message' => 'Redemption deleted successfully']);
        } catch (\Exception $e) {
            $db->rollBack();
            return Response::json(['success' => false, 'message' => 'Transaction error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Danh sách redemption (admin)
     */
    public function apiList(Request $request)
    {
        $page = (int) ($request->query('page') ?? 1);
        $status = $request->query('status');
        $type = $request->query('type');
        $search = $request->query('search');
        $userId = $request->query('user_id');
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $filters = [];
        if ($status)
            $filters['status'] = $status;
        if ($type)
            $filters['type'] = $type;
        if ($search)
            $filters['search'] = $search;
        if ($userId)
            $filters['user_id'] = $userId;

        $redemptions = $this->redemptionModel->getAll($limit, $offset, $filters);
        $total = $this->redemptionModel->count($filters);

        return Response::json([
            'success' => true,
            'data' => $redemptions,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    /**
     * Thống kê redemption (admin)
     */
    public function stats()
    {
        $request = new Request();
        $userId = $request->get('user_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $filters = [];
        if ($userId)
            $filters['user_id'] = $userId;
        if ($dateFrom)
            $filters['date_from'] = $dateFrom;
        if ($dateTo)
            $filters['date_to'] = $dateTo;

        $stats = $this->redemptionModel->getStats($filters);

        return Response::json(['success' => true, 'data' => $stats]);
    }

    /**
     * Lấy user hiện tại
     */
    private function getCurrentUser(Request $request)
    {
        // Cách 1: Từ SESSION (nếu có)
        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }

        // Cách 2: Từ request input/body (POST/JSON)
        $userId = $request->input('user_id');
        if ($userId) {
            return (int) $userId;
        }

        // Cách 3: Từ query parameter (GET)
        $userId = $request->query('user_id');
        if ($userId) {
            return (int) $userId;
        }

        return null;
    }

    /**
     * Kiểm tra quyền admin
     */
    private function requireAdmin()
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            die('Bạn không có quyền truy cập');
        }
    }

    private function pageData(Request $request): array
    {
        $config = \App\Core\Container::get('config');
        $appName = (string) ($config['app']['name'] ?? 'Surveygo Admin');

        $baseUrl = '';
        $scheme = $request->server('REQUEST_SCHEME') ?: ($request->server('HTTPS') === 'on' ? 'https' : 'http');
        $host = $request->server('HTTP_HOST');
        if ($host) {
            $baseUrl = $scheme . '://' . $host;
        }

        $currentPath = $request->server('REQUEST_URI') ?: '/';

        return [
            'appName' => $appName,
            'baseUrl' => $baseUrl,
            'currentPath' => $currentPath,
            'urls' => [
                'home' => $baseUrl . '/',
                'admin' => $baseUrl . '/admin',
                'dashboard' => $baseUrl . '/admin/dashboard',
                'login' => $baseUrl . '/login',
            ],
        ];
    }
}
