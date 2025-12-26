<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\User;
use App\Core\Container;

class UserController extends Controller
{

    // api phân trang 
    public function index(Request $request)
    {
        $page = (int) ($request->query('page') ?? 1);
        $limit = (int) ($request->query('limit') ?? 10);

        $filters = [];
        if ($search = $request->query('search')) {
            $filters['search'] = $search;
        }
        if ($role = $request->query('role')) {
            $filters['role'] = $role;
        }

        $result = User::paginate($page, $limit, $filters);

        // lấy cả thông tin số lượng khảo sát và phản hồi
        $db = Container::get('db');
        $users = array_map(function ($u) use ($db) {
            $uid = $u->getId();

            $stmt1 = $db->prepare('SELECT COUNT(*) as c FROM surveys WHERE maNguoiTao = :uid');
            $stmt1->execute([':uid' => $uid]);
            $surveys = (int) $stmt1->fetch()['c'];

            $stmt2 = $db->prepare('SELECT COUNT(*) as c FROM user_responses WHERE maNguoiDung = :uid');
            $stmt2->execute([':uid' => $uid]);
            $responses = (int) $stmt2->fetch()['c'];

            $arr = $u->toArray();
            return [
                'id' => $arr['id'],
                'code' => $arr['code'] ?? '',
                'name' => $arr['name'],
                'email' => $arr['email'],
                'phone' => $arr['phone'] ?? '',
                'gender' => $arr['gender'] ?? 'other',
                'role' => $arr['role'],
                'status' => 'active',
                'surveys' => $surveys,
                'responses' => $responses,
                'joinedAt' => $arr['created_at'],
                'avatar' => '',
            ];
        }, $result['users']);

        return $this->json([
            'error' => false,
            'data' => $users,
            'meta' => [
                'total' => $result['total'],
                'page' => $result['page'],
                'limit' => $result['limit'],
                'totalPages' => $result['totalPages'],
            ],
        ]);
    }

    /**
     * Get user profile statistics
     * Returns: completed surveys count, current points, and redemptions count
     */
    public function getUserProfileStats(Request $request)
    {
        try {
            // Get user ID from request or session
            $userId = $request->query('user_id');
            if (!$userId) {
                // Try to get from session if authenticated
                if (isset($_SESSION['user_id'])) {
                    $userId = (int) $_SESSION['user_id'];
                } else {
                    return $this->json([
                        'error' => true,
                        'message' => 'User ID is required'
                    ], 400);
                }
            }

            $db = Container::get('db');

            // Get completed surveys count from survey_submissions table
            $surveysStmt = $db->prepare(
                'SELECT COUNT(*) as count FROM survey_submissions WHERE maNguoiDung = :user_id'
            );
            $surveysStmt->execute([':user_id' => $userId]);
            $completedSurveys = (int) $surveysStmt->fetch()['count'];

            // Get current points balance from user_points table
            $pointsStmt = $db->prepare(
                'SELECT balance FROM user_points WHERE user_id = :user_id'
            );
            $pointsStmt->execute([':user_id' => $userId]);
            $pointsRow = $pointsStmt->fetch();
            $currentPoints = $pointsRow ? (int) $pointsRow['balance'] : 0;

            // Get redemptions count from reward_redemptions table
            $redemptionsStmt = $db->prepare(
                'SELECT COUNT(*) as count FROM reward_redemptions WHERE user_id = :user_id'
            );
            $redemptionsStmt->execute([':user_id' => $userId]);
            $redemptionsCount = (int) $redemptionsStmt->fetch()['count'];

            return $this->json([
                'success' => true,
                'data' => [
                    'completed_surveys' => $completedSurveys,
                    'current_points' => $currentPoints,
                    'redemptions_count' => $redemptionsCount
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => true,
                'message' => 'Failed to fetch user statistics: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getPoints(Request $request)
    {
        $userId = (int) ($request->query('userId') ?? $request->input('userId'));

        if ($userId <= 0) {
            return $this->json([
                'error' => true,
                'message' => 'User ID is required.',
            ], 422);
        }

        $userPoints = \App\Models\UserPoint::findByUserId($userId);
        $balance = $userPoints ? $userPoints->getBalance() : 0;
        $luckyWheelSpins = $userPoints ? $userPoints->getLuckyWheelSpins() : 0;

        return $this->json([
            'error' => false,
            'data' => [
                'userId' => $userId,
                'balance' => $balance,
                'lucky_wheel_spins' => $luckyWheelSpins,
            ]
        ]);
    }

    public function show(Request $request)
    {
        $userId = (int) ($request->query('id') ?? 0);
        
        if ($userId <= 0) {
            return $this->json([
                'error' => true,
                'message' => 'ID người dùng không hợp lệ'
            ], 400);
        }
        
        $user = User::findById($userId);
        
        if (!$user) {
            return $this->json([
                'error' => true,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }
        
        $userPoint = \App\Models\UserPoint::findByUserId($userId);
        
        $userData = $user->toArray();
        $userData['points'] = [
            'balance' => $userPoint ? $userPoint->getBalance() : 0,
            'total_earned' => $userPoint ? $userPoint->getTotalEarned() : 0,
            'lucky_wheel_spins' => $userPoint ? $userPoint->getLuckyWheelSpins() : 0
        ];

        return $this->json([
            'error' => false,
            'data' => $userData
        ]);
    }

    public function create(Request $request)
    {
        $name = trim($request->input('name') ?? '');
        $email = trim($request->input('email') ?? '');
        $password = $request->input('password') ?? '';
        $phone = trim($request->input('phone') ?? '');
        $gender = $request->input('gender') ?? 'other';
        $role = $request->input('role') ?? 'user';

        // Validation
        if (empty($name) || empty($email) || empty($password)) {
            return $this->json([
                'error' => true,
                'message' => 'Tên, Email và Mật khẩu là bắt buộc'
            ], 400);
        }

        // Không cho phép tạo tài khoản với quyền Admin
        if ($role === 'admin') {
            return $this->json([
                'error' => true,
                'message' => 'Không được phép tạo tài khoản với quyền Quản trị viên'
            ], 403);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json([
                'error' => true,
                'message' => 'Định dạng email không hợp lệ'
            ], 400);
        }

        if (strlen($password) < 6) {
             return $this->json([
                'error' => true,
                'message' => 'Mật khẩu phải có ít nhất 6 ký tự'
            ], 400);
        }

        if (User::findByEmail($email)) {
            return $this->json([
                'error' => true,
                'message' => 'Email này đã được sử dụng'
            ], 400);
        }

        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'phone' => $phone,
                'gender' => $gender,
                'role' => $role,
                'avatar' => '' 
            ]);

            if ($user) {
                return $this->json([
                    'error' => false,
                    'message' => 'Tạo người dùng thành công',
                    'data' => $user->toArray()
                ]);
            } else {
                return $this->json([
                    'error' => true,
                    'message' => 'Lỗi khi tạo người dùng'
                ], 500);
            }

        } catch (\Exception $e) {
            return $this->json([
                'error' => true,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $userId = (int) ($request->input('id') ?? 0);
        
        if ($userId <= 0) {
            return $this->json([
                'error' => true,
                'message' => 'ID người dùng không hợp lệ'
            ], 400);
        }
        
        $user = User::findById($userId);
        if (!$user) {
            return $this->json([
                'error' => true,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }
        
        // Validation input
        $name = trim($request->input('name') ?? '');
        $email = trim($request->input('email') ?? '');
        $phone = trim($request->input('phone') ?? '');
        $gender = $request->input('gender') ?? 'other';
        $role = $request->input('role') ?? 'user';
        
        if (empty($name)) {
            return $this->json([
                'error' => true,
                'message' => 'Tên không được để trống'
            ], 400);
        }
        
        // Không cho phép admin tự thay đổi role của chính mình
        $currentUserId = (int) ($_SESSION['user_id'] ?? 0);
        if ($userId === $currentUserId && $role !== $user->getRole()) {
            return $this->json([
                'error' => true,
                'message' => 'Bạn không thể thay đổi vai trò của chính mình'
            ], 403);
        }

        // Nếu nâng quyền lên Admin, yêu cầu xác nhận mật khẩu của người đang thao tác
        if ($role === 'admin' && $user->getRole() !== 'admin') {
            $adminPassword = $request->input('admin_password');
            if (empty($adminPassword)) {
                return $this->json([
                    'error' => true,
                    'message' => 'Vui lòng nhập mật khẩu của bạn để xác nhận cấp quyền Admin'
                ], 400);
            }

            $currentUser = User::findById($currentUserId);
            if (!$currentUser || !$currentUser->verifyPassword($adminPassword)) {
                return $this->json([
                    'error' => true,
                    'message' => 'Mật khẩu xác nhận không chính xác'
                ], 403);
            }
        }
        
        // Cập nhật thông tin
        $user->setName($name);
        // Email không được phép thay đổi
        $user->setPhone($phone);
        $user->setRole($role);
        
        // Chỉ update gender nếu nó là giá trị hợp lệ
        if (in_array($gender, ['male', 'female', 'other'])) {
             $user->setGender($gender);
        }
        
        $result = $user->update();
        
        // Cập nhật điểm số và lượt quay (nếu có gửi lên)
        $points = $request->input('points');
        $spins = $request->input('spins');

        if ($points !== null || $spins !== null) {
            $userPoint = \App\Models\UserPoint::getOrCreate($userId);
            
            $currentBalance = $userPoint->getBalance();
            $currentSpins = $userPoint->getLuckyWheelSpins();
            
            $newBalance = $points !== null ? (int)$points : $currentBalance;
            $newSpins = $spins !== null ? (int)$spins : $currentSpins;
            
            // Chỉ xử lý nếu có sự thay đổi
            if ($newBalance !== $currentBalance || $newSpins !== $currentSpins) {
                // Yêu cầu xác nhận mật khẩu admin khi thay đổi điểm số/lượt quay
                $adminPassword = $request->input('admin_password');
                if (empty($adminPassword)) {
                    return $this->json([
                        'error' => true,
                        'message' => 'Vui lòng nhập mật khẩu của bạn để xác nhận thay đổi điểm số/lượt quay'
                    ], 400);
                }

                $currentUser = User::findById($currentUserId);
                if (!$currentUser || !$currentUser->verifyPassword($adminPassword)) {
                    return $this->json([
                        'error' => true,
                        'message' => 'Mật khẩu xác nhận không chính xác'
                    ], 403);
                }
                
                // Thực hiện cập nhật
                if ($newBalance !== $currentBalance) {
                     // Nếu điểm mới > tổng tích lũy hiện tại -> cập nhật luôn tổng tích lũy
                    if ($newBalance > $userPoint->getTotalEarned()) {
                        $userPoint->setTotalEarned($newBalance);
                    }
                    $userPoint->setBalance($newBalance);
                }
                
                if ($newSpins !== $currentSpins) {
                    $userPoint->setLuckyWheelSpins($newSpins);
                }
                
                $userPoint->update();
            }
        }
        
        if ($result) {
            return $this->json([
                'error' => false,
                'message' => 'Cập nhật thông tin thành công',
                'data' => $user->toArray()
            ]);
        } else {
            return $this->json([
                'error' => true,
                'message' => 'Không thể cập nhật thông tin'
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        $userId = (int) ($request->input('id') ?? $request->query('id') ?? 0);
        
        if ($userId <= 0) {
            return $this->json([
                'error' => true,
                'message' => 'ID người dùng không hợp lệ'
            ], 400);
        }
        
        $currentUserId = (int) ($_SESSION['user_id'] ?? 0);
        if ($userId === $currentUserId) {
            return $this->json([
                'error' => true,
                'message' => 'Bạn không thể xóa chính mình'
            ], 403);
        }
        
        $user = User::findById($userId);
        if (!$user) {
            return $this->json([
                'error' => true,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }
        
        if ($user->getRole() === 'admin') {
            return $this->json([
                'error' => true,
                'message' => 'Không thể xóa tài khoản Admin. Chỉ có thể xóa User hoặc Moderator.'
            ], 403);
        }
        
        $result = User::deleteById($userId);
        
        if ($result) {
            return $this->json([
                'error' => false,
                'message' => 'Xóa người dùng thành công'
            ]);
        } else {
            return $this->json([
                'error' => true,
                'message' => 'Không thể xóa người dùng'
            ], 500);
        }
    }
}
