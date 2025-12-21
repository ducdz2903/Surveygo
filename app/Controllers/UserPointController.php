<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\UserPoint;

class UserPointController extends Controller
{
    /**
     * Lấy điểm của người dùng hiện tại
     */
    public function getBalance()
    {
        $this->checkAjax();
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return Response::json([
                'error' => true,
                'message' => 'Unauthorized',
                'debug' => [
                    'has_session' => isset($_SESSION['user']),
                    'session_keys' => array_keys($_SESSION ?? [])
                ]
            ], 401);
        }

        try {
            $balance = UserPoint::findByUserId($user['id']);
            
            if (!$balance) {
                // Tạo record mới nếu chưa có
                $balance = UserPoint::create($user['id']);
            }

            return Response::json([
                'success' => true,
                'data' => $balance->toArray()
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'error' => true,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Kiểm tra xem người dùng có đủ điểm không
     */
    public function hasEnoughPoints()
    {
        $this->checkAjax();
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return Response::json([
                'error' => true,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $requiredPoints = $_GET['points'] ?? null;
            
            if (!$requiredPoints || $requiredPoints <= 0) {
                return Response::json([
                    'error' => true,
                    'message' => 'Số điểm không hợp lệ'
                ], 400);
            }

            $balance = UserPoint::getOrCreate($user['id']);
            
            return Response::json([
                'success' => true,
                'has_enough' => $balance->getBalance() >= $requiredPoints,
                'current_balance' => $balance->getBalance(),
                'required_points' => $requiredPoints
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'error' => true,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Lấy thống kê điểm theo khoảng thời gian 3 tiếng trong 24 giờ qua
     */
    public function getHourlyStats()
    {
        $this->checkAjax();
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return Response::json([
                'error' => true,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            // Lấy tất cả transactions trong 24 giờ qua
            $db = \App\Core\Container::get('db');
            
            $statement = $db->prepare(
                'SELECT amount, created_at 
                 FROM point_transactions 
                 WHERE user_id = :user_id 
                   AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                 ORDER BY created_at ASC'
            );
            
            $statement->execute([':user_id' => $user['id']]);
            $transactions = $statement->fetchAll();
            
            // Khởi tạo mảng 8 khoảng thời gian (mỗi khoảng 3 tiếng)
            $hourlyData = array_fill(0, 8, 0);
            
            // Nhóm dữ liệu theo khoảng thời gian
            foreach ($transactions as $tx) {
                $timestamp = strtotime($tx['created_at']);
                $hour = (int) date('H', $timestamp);
                
                // Xác định khoảng thời gian (0-3h = index 0, 3-6h = index 1, ...)
                $intervalIndex = (int) floor($hour / 3);
                $hourlyData[$intervalIndex] += (int) $tx['amount'];
            }
            
            return Response::json([
                'success' => true,
                'data' => $hourlyData
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'error' => true,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Helper methods
     */
    private function checkAjax()
    {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            throw new \Exception('Invalid request');
        }
    }

    private function getCurrentUser()
    {
        // Thử lấy từ session trước
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        }
        
        // Thử lấy từ request body (POST/GET)
        $userId = $_GET['user_id'] ?? $_POST['user_id'] ?? null;
        if ($userId) {
            return ['id' => (int)$userId];
        }
        
        // Nếu không có session, thử lấy từ request headers (JWT hoặc token)
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (strpos($authHeader, 'Bearer ') === 0) {
            $token = substr($authHeader, 7);
            // TODO: Decode JWT token để lấy user_id
        }
        
        // Cuối cùng, return null
        return null;
    }
}

