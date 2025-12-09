<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Container;
use App\Models\Reward;
use App\Models\UserPoint;
use App\Models\User;

class RewardController extends Controller
{
    private $reward;

    public function __construct()
    {
        $this->reward = new Reward();
    }

    /**
     * Hiển thị danh sách phần thưởng cho client (HTML view)
     */
    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $rewards = $this->reward->getAllActive($limit, $offset);
        $total = $this->reward->count();
        $totalPages = ceil($total / $limit);

        $data = [
            'rewards' => $rewards,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'total' => $total
            ]
        ];

        return $this->view('pages/client/rewards/rewards', $data, 'main');
    }

    /**
     * API endpoint - Lấy danh sách phần thưởng (JSON)
     */
    public function apiList()
    {
        $type = $_GET['type'] ?? null;
        $page = $_GET['page'] ?? 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        try {
            if ($type) {
                // Lọc theo type (thêm limit và offset)
                $rewards = $this->reward->getByType($type, $limit, $offset);
            } else {
                // Lấy tất cả quà đang hoạt động
                $rewards = $this->reward->getAllActive($limit, $offset);
            }

            return Response::json($rewards);
        } catch (\Exception $e) {
            return Response::json([
                'error' => true,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * API endpoint - Lấy danh sách phần thưởng (JSON)
     */
    public function listRewards()
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';

        try {
            $db = Container::get('db');
            $where = [];
            $params = [];

            // Filter by search (name hoặc code)
            if (!empty($search)) {
                $where[] = "(name LIKE ? OR code LIKE ?)";
                $params[] = "%{$search}%";
                $params[] = "%{$search}%";
            }

            // Filter by type
            if (!empty($type)) {
                $where[] = "type = ?";
                $params[] = $type;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Get total
            $countQuery = "SELECT COUNT(*) as total FROM rewards {$whereClause}";
            $countStmt = $db->prepare($countQuery);
            $countStmt->execute($params);
            $countResult = $countStmt->fetch(\PDO::FETCH_ASSOC);
            $total = (int)$countResult['total'];

            // Get data - LIMIT and OFFSET không thể dùng placeholder, phải concatenate
            $dataQuery = "SELECT * FROM rewards {$whereClause} ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}";
            $dataStmt = $db->prepare($dataQuery);
            $dataStmt->execute($params);
            $rewards = $dataStmt->fetchAll(\PDO::FETCH_ASSOC);

            return Response::json([
                'data' => $rewards,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'pages' => (int)ceil($total / $limit)
                ]
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'error' => true,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * API: Tạo phần thưởng mới
     */
    public function apiCreateReward(Request $request)
    {
        $this->checkAjax();
        $data = $request->input();

        // Validation
        $errors = [];
        if (empty($data['code'])) $errors[] = 'Vui lòng nhập mã phần thưởng';
        if (empty($data['name'])) $errors[] = 'Vui lòng nhập tên phần thưởng';
        if (empty($data['type'])) $errors[] = 'Vui lòng chọn loại phần thưởng';
        if (!in_array($data['type'], ['cash', 'e_wallet', 'giftcard', 'physical'])) {
            $errors[] = 'Loại phần thưởng không hợp lệ';
        }
        if (empty($data['point_cost']) || $data['point_cost'] <= 0) {
            $errors[] = 'Chi phí điểm phải lớn hơn 0';
        }
        if ($data['type'] !== 'physical' && (empty($data['value']) || $data['value'] <= 0)) {
            $errors[] = 'Giá trị phải lớn hơn 0';
        }
        if ($data['type'] === 'physical' && (empty($data['stock']) || $data['stock'] <= 0)) {
            $errors[] = 'Stock phải lớn hơn 0';
        }

        if (!empty($errors)) {
            return Response::json(['success' => false, 'errors' => $errors], 422);
        }

        $rewardData = [
            'code' => trim($data['code']),
            'name' => trim($data['name']),
            'type' => $data['type'],
            'provider' => !empty($data['provider']) ? trim($data['provider']) : null,
            'point_cost' => (int)$data['point_cost'],
            'value' => ($data['type'] === 'physical') ? null : (int)$data['value'],
            'stock' => ($data['type'] === 'physical') ? (int)$data['stock'] : null,
            'description' => !empty($data['description']) ? trim($data['description']) : null
        ];

        try {
            $id = $this->reward->create($rewardData);
            return Response::json(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return Response::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Cập nhật phần thưởng
     */
    public function apiUpdateReward(Request $request)
    {
        $this->checkAjax();
        $id = $request->getAttribute('id');
        if (!$id) {
            return Response::json(['success' => false, 'message' => 'ID phần thưởng không hợp lệ'], 400);
        }

        $data = $request->input();

        $reward = $this->reward->getById($id);
        if (!$reward) {
            return Response::json(['success' => false, 'message' => 'Phần thưởng không tồn tại'], 404);
        }

        // Validation
        $errors = [];
        if (empty($data['code'])) $errors[] = 'Vui lòng nhập mã phần thưởng';
        if (empty($data['name'])) $errors[] = 'Vui lòng nhập tên phần thưởng';
        if (empty($data['type'])) $errors[] = 'Vui lòng chọn loại phần thưởng';
        if (!in_array($data['type'], ['cash', 'e_wallet', 'giftcard', 'physical'])) {
            $errors[] = 'Loại phần thưởng không hợp lệ';
        }
        if (empty($data['point_cost']) || $data['point_cost'] <= 0) {
            $errors[] = 'Chi phí điểm phải lớn hơn 0';
        }

        if (!empty($errors)) {
            return Response::json(['success' => false, 'errors' => $errors], 422);
        }

        $rewardData = [
            'code' => trim($data['code']),
            'name' => trim($data['name']),
            'type' => $data['type'],
            'provider' => !empty($data['provider']) ? trim($data['provider']) : null,
            'point_cost' => (int)$data['point_cost'],
            'value' => ($data['type'] === 'physical') ? null : (int)$data['value'],
            'stock' => ($data['type'] === 'physical') ? (int)$data['stock'] : null,
            'description' => !empty($data['description']) ? trim($data['description']) : null
        ];

        try {
            $this->reward->update($id, $rewardData);
            return Response::json(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return Response::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Hiển thị chi tiết phần thưởng
     */
    public function detail($id)
    {
        $reward = $this->reward->getById($id);

        if (!$reward) {
            return $this->notFound();
        }

        $data = [
            'reward' => $reward
        ];

        return $this->view('pages/client/rewards/detail', $data, 'main');
    }

    /**
     * Lọc phần thưởng (AJAX)
     */
    public function filter()
    {
        $this->checkAjax();

        $type = $_GET['type'] ?? null;
        $provider = $_GET['provider'] ?? null;
        $minPoints = $_GET['min_points'] ?? null;
        $maxPoints = $_GET['max_points'] ?? null;
        $page = $_GET['page'] ?? 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $filters = [
            'is_active' => true,
            'type' => $type,
            'provider' => $provider,
            'min_points' => $minPoints,
            'max_points' => $maxPoints,
            'limit' => $limit,
            'offset' => $offset
        ];

        $rewards = $this->reward->filter($filters);

        return Response::json($rewards);
    }

    /**
     * Tìm kiếm phần thưởng (AJAX)
     */
    public function search()
    {
        $this->checkAjax();

        $keyword = $_GET['q'] ?? '';
        if (strlen($keyword) < 2) {
            return Response::json([]);
        }

        $rewards = $this->reward->search($keyword, 10);
        return Response::json($rewards);
    }

    /**
     * Đổi phần thưởng
     */
    public function redeem()
    {
        $this->checkAjax();
        $this->requireLogin();

        $rewardId = $_POST['reward_id'] ?? null;
        if (!$rewardId) {
            return Response::json(['success' => false, 'message' => 'Vui lòng chọn quà thưởng'], 400);
        }

        $currentUser = $this->getCurrentUser();
        $reward = $this->reward->getById($rewardId);

        if (!$reward) {
            return Response::json(['success' => false, 'message' => 'Phần thưởng không tồn tại'], 404);
        }

        // Kiểm tra stock
        if (!$this->reward->hasEnoughStock($rewardId)) {
            return Response::json(['success' => false, 'message' => 'Phần thưởng đã hết hàng'], 400);
        }

        // TODO: Kiểm tra điểm người dùng từ database
        // Thực hiện đổi quà
        try {
            // TODO: Trừ điểm

            // Giảm stock nếu là quà vật lý
            if ($reward['stock'] !== null) {
                $this->reward->decreaseStock($rewardId);
            }

            // Tạo record redemption
            $this->createRedemption($currentUser['id'], $rewardId);

            return Response::json([
                'success' => true,
                'message' => 'Đổi quà thành công! Vui lòng chờ xử lý.',
                'remaining_points' => 0 // TODO: Cập nhật từ database
            ]);
        } catch (\Exception $e) {
            return Response::json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Admin: Hiển thị danh sách phần thưởng
     */
    public function adminIndex(Request $request)
    {
        $admin = new AdminController();
        return $admin->rewards($request);
    }

    /**
     * Admin: Tạo phần thưởng mới
     */
    public function adminCreate()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->adminStoreReward();
        }

        $types = ['cash' => 'Rút tiền', 'e_wallet' => 'Ví điện tử', 'giftcard' => 'Gift Card', 'physical' => 'Quà vật lý'];
        $providers = $this->reward->getProviders();

        $data = [
            'types' => $types,
            'providers' => $providers
        ];

        return $this->view('pages/admin/rewards/create', $data, 'admin');
    }

    /**
     * Admin: Lưu phần thưởng mới
     */
    private function adminStoreReward()
    {
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? '';
        $provider = trim($_POST['provider'] ?? '');
        $pointCost = (int)($_POST['point_cost'] ?? 0);
        $value = (int)($_POST['value'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        // Validate
        $errors = [];
        if (empty($code)) $errors[] = 'Vui lòng nhập mã phần thưởng';
        if (empty($name)) $errors[] = 'Vui lòng nhập tên phần thưởng';
        if (!in_array($type, ['cash', 'e_wallet', 'giftcard', 'physical'])) {
            $errors[] = 'Loại phần thưởng không hợp lệ';
        }
        if ($pointCost <= 0) $errors[] = 'Chi phí điểm phải lớn hơn 0';
        if ($type !== 'physical' && $value <= 0) $errors[] = 'Giá trị phải lớn hơn 0';
        if ($type === 'physical' && $stock <= 0) $errors[] = 'Stock phải lớn hơn 0';

        if (!empty($errors)) {
            return $this->respondWithError($errors);
        }

        // Xử lý upload ảnh
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $this->uploadImage($_FILES['image']);
            if (!$image) {
                return $this->respondWithError(['Lỗi upload ảnh']);
            }
        }

        $data = [
            'code' => $code,
            'name' => $name,
            'type' => $type,
            'provider' => $provider ?: null,
            'point_cost' => $pointCost,
            'value' => ($type === 'physical') ? null : $value,
            'stock' => ($type === 'physical') ? $stock : null,
            'image' => $image,
            'description' => $description
        ];

        if ($this->reward->create($data)) {
            return $this->redirectWithMessage('/admin/rewards', 'Tạo phần thưởng thành công');
        } else {
            return $this->respondWithError(['Lỗi tạo phần thưởng']);
        }
    }

    /**
     * Admin: Hiển thị form chỉnh sửa
     */
    public function adminEdit($id)
    {
        $this->requireAdmin();

        $reward = $this->reward->getById($id);
        if (!$reward) {
            return $this->notFound();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->adminUpdateReward($id);
        }

        $types = ['cash' => 'Rút tiền', 'e_wallet' => 'Ví điện tử', 'giftcard' => 'Gift Card', 'physical' => 'Quà vật lý'];
        $providers = $this->reward->getProviders();

        $data = [
            'reward' => $reward,
            'types' => $types,
            'providers' => $providers
        ];

        return $this->view('pages/admin/rewards/edit', $data, 'admin');
    }

    /**
     * Admin: Cập nhật phần thưởng
     */
    private function adminUpdateReward($id)
    {
        $reward = $this->reward->getById($id);
        if (!$reward) {
            return $this->notFound();
        }

        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? '';
        $provider = trim($_POST['provider'] ?? '');
        $pointCost = (int)($_POST['point_cost'] ?? 0);
        $value = (int)($_POST['value'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        // Validate
        $errors = [];
        if (empty($code)) $errors[] = 'Vui lòng nhập mã phần thưởng';
        if (empty($name)) $errors[] = 'Vui lòng nhập tên phần thưởng';
        if ($pointCost <= 0) $errors[] = 'Chi phí điểm phải lớn hơn 0';

        if (!empty($errors)) {
            return $this->respondWithError($errors);
        }

        // Xử lý upload ảnh
        $image = $reward['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $newImage = $this->uploadImage($_FILES['image']);
            if (!$newImage) {
                return $this->respondWithError(['Lỗi upload ảnh']);
            }
            $image = $newImage;
        }

        $updateData = [
            'code' => $code,
            'name' => $name,
            'type' => $type,
            'provider' => $provider ?: null,
            'point_cost' => $pointCost,
            'value' => ($type === 'physical') ? null : $value,
            'stock' => ($type === 'physical') ? $stock : null,
            'image' => $image,
            'description' => $description
        ];

        if ($this->reward->update($id, $updateData)) {
            return $this->redirectWithMessage("/admin/rewards/{$id}/edit", 'Cập nhật phần thưởng thành công');
        } else {
            return $this->respondWithError(['Lỗi cập nhật phần thưởng']);
        }
    }

    /**
     * Admin: Xóa phần thưởng
     */
    public function adminDelete(Request $request)
    {
        $this->requireAdmin();
        $this->checkAjax();

        $id = $request->getAttribute('id');
        if (!$id) {
            return Response::json(['success' => false, 'message' => 'ID phần thưởng không hợp lệ'], 400);
        }

        $reward = $this->reward->getById($id);
        if (!$reward) {
            return Response::json(['success' => false, 'message' => 'Phần thưởng không tồn tại'], 404);
        }

        if ($this->reward->delete($id)) {
            return Response::json(['success' => true, 'message' => 'Xóa phần thưởng thành công']);
        } else {
            return Response::json(['success' => false, 'message' => 'Lỗi xóa phần thưởng'], 500);
        }
    }

    /**
     * Admin: Kích hoạt/Vô hiệu hóa phần thưởng
     */
    public function adminToggle($id)
    {
        $this->requireAdmin();
        $this->checkAjax();

        $reward = $this->reward->getById($id);
        if (!$reward) {
            return Response::json(['success' => false, 'message' => 'Phần thưởng không tồn tại'], 404);
        }

        $action = $_POST['action'] ?? '';
        $result = false;

        if ($action === 'activate') {
            $result = $this->reward->activate($id);
        } elseif ($action === 'deactivate') {
            $result = $this->reward->deactivate($id);
        }

        if ($result) {
            return Response::json([
                'success' => true,
                'message' => $action === 'activate' ? 'Kích hoạt thành công' : 'Vô hiệu hóa thành công',
                'is_active' => $action === 'activate'
            ]);
        } else {
            return Response::json(['success' => false, 'message' => 'Lỗi cập nhật'], 500);
        }
    }
    public function adminUpdateStock($id)
    {
        $this->requireAdmin();
        $this->checkAjax();

        $reward = $this->reward->getById($id);
        if (!$reward || $reward['stock'] === null) {
            return Response::json(['success' => false, 'message' => 'Phần thưởng không hợp lệ'], 404);
        }

        $quantity = (int)($_POST['quantity'] ?? 0);
        if ($quantity < 0) {
            return Response::json(['success' => false, 'message' => 'Số lượng không hợp lệ'], 400);
        }

        if ($this->reward->updateStock($id, $quantity)) {
            return Response::json([
                'success' => true,
                'message' => 'Cập nhật stock thành công',
                'new_stock' => $quantity
            ]);
        } else {
            return Response::json(['success' => false, 'message' => 'Lỗi cập nhật'], 500);
        }
    }

    /**
     * Admin: Danh sách redemption
     */
    public function adminRedemptions()
    {
        $this->requireAdmin();

        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? null;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // TODO: Tạo model RewardRedemption để lấy danh sách
        $redemptions = [];
        $total = 0;

        $data = [
            'redemptions' => $redemptions,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => ceil($total / $limit),
                'total' => $total
            ],
            'statuses' => ['pending' => 'Chờ xử lý', 'processing' => 'Đang xử lý', 'completed' => 'Hoàn thành', 'rejected' => 'Từ chối']
        ];

        return $this->view('pages/admin/rewards/redemptions', $data, 'admin');
    }

    /**
     * Admin: Cập nhật trạng thái redemption
     */
    public function adminUpdateRedemptionStatus($id)
    {
        $this->requireAdmin();
        $this->checkAjax();

        $status = $_POST['status'] ?? '';
        $note = trim($_POST['note'] ?? '');

        if (!in_array($status, ['pending', 'processing', 'completed', 'rejected'])) {
            return Response::json(['success' => false, 'message' => 'Trạng thái không hợp lệ'], 400);
        }

        // TODO: Cập nhật status redemption
        return Response::json(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
    }

    /**
     * Admin: Thống kê
     */
    public function adminStats()
    {
        $this->requireAdmin();

        $stats = $this->reward->getStats();

        $data = [
            'stats' => $stats
        ];

        return $this->view('pages/admin/rewards/stats', $data, 'admin');
    }

    /**
     * Tạo record redemption (đổi quà)
     */
    private function createRedemption($userId, $rewardId)
    {
        // TODO: Tạo model RewardRedemption
        $query = "INSERT INTO reward_redemptions (user_id, reward_id, status) VALUES (?, ?, 'pending')";
        $db = \App\Core\Database::getInstance();
        return $db->execute($query, [$userId, $rewardId]);
    }

    /**
     * Upload ảnh
     */
    private function uploadImage($file)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        if ($file['size'] > $maxSize) {
            return false;
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/assets/images/rewards/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = uniqid('reward_') . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return 'assets/images/rewards/' . $filename;
        }

        return false;
    }

    /**
     * Check AJAX request
     */
    private function checkAjax()
    {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            header('HTTP/1.1 400 Bad Request');
            exit('Invalid request');
        }
    }

    /**
     * Require login
     */
    private function requireLogin()
    {
        if (!$this->getCurrentUser()) {
            return Response::json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }
    }

    /**
     * Require admin
     */
    private function requireAdmin()
    {
        $user = $this->getCurrentUser();
        if (!$user || $user['role'] !== 'admin') {
            return Response::json(['success' => false, 'message' => 'Bạn không có quyền'], 403);
        }
    }

    /**
     * Get current user
     */
    private function getCurrentUser()
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Respond with error
     */
    private function respondWithError($errors)
    {
        if ($this->isAjax()) {
            return Response::json(['success' => false, 'errors' => $errors], 400);
        }
    }

    /**
     * Check if AJAX
     */
    private function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Redirect with message
     */
    private function redirectWithMessage($path, $message)
    {
        $_SESSION['success'] = $message;
        header("Location: {$path}");
        exit;
    }

    /**
     * API endpoint - Lấy chi tiết gift card
     */
    public function getGiftCardDetails()
    {
        if (!$this->isAjax()) {
            return Response::json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $rewardId = $_GET['reward_id'] ?? null;

        if (!$rewardId) {
            return Response::json(['success' => false, 'message' => 'Missing reward_id'], 400);
        }

        $reward = $this->reward->getById($rewardId);

        if (!$reward) {
            return Response::json(['success' => false, 'message' => 'Reward not found'], 404);
        }

        if ($reward['type'] !== 'giftcard') {
            return Response::json(['success' => false, 'message' => 'This is not a gift card'], 400);
        }

        return Response::json([
            'success' => true,
            'data' => [
                'id' => $reward['id'],
                'name' => $reward['name'],
                'provider' => $reward['provider'],
                'value' => $reward['value'],
                'description' => $reward['description'],
                'code' => $reward['giftcard_code'] ?? null,
                'serial' => $reward['giftcard_serial'] ?? null,
                'expiry_date' => $reward['giftcard_expiry_date'] ?? null
            ]
        ]);
    }

    /**
     * Not found
     */
    private function notFound()
    {
        header('HTTP/1.1 404 Not Found');
        return $this->view('pages/404', [], 'main');
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
