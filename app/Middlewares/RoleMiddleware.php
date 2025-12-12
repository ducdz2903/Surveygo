<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Request;
use App\Core\Response;

class RoleMiddleware implements MiddlewareInterface
{

    private array $allowedRoles;
    public function __construct(array $allowedRoles)
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function handle(Request $request): ?Response
    {
        // Bước 1: Kiểm tra user đã xác thực chưa
        if (!isset($_SESSION['user_id'])) {
            // Nếu là API request (bắt đầu với /api/)
            if ($this->isApiRequest($request)) {
                return Response::json([
                    'error' => true,
                    'message' => 'Chưa xác thực. Vui lòng đăng nhập.',
                ], 401);
            }
            
            // Nếu là page request, redirect tới trang login
            return Response::redirect('/login');
        }

        // Bước 2: Kiểm tra user có đủ quyền không
        $userRole = $_SESSION['user_role'] ?? null;

        // Nếu user không có role hoặc role không có trong danh sách cho phép
        if (!$userRole || !in_array($userRole, $this->allowedRoles, true)) {
            // Nếu là API request
            if ($this->isApiRequest($request)) {
                return Response::json([
                    'error' => true,
                    'message' => 'Không có quyền truy cập tài nguyên này.',
                ], 403);
            }
            
            // Nếu là page request, redirect tới home
            return Response::redirect('/');
        }

        // Nếu đã xác thực và có đủ quyền, tiếp tục xử lý request
        return null;
    }

    private function isApiRequest(Request $request): bool
    {
        $path = $request->uri();
        return strpos($path, '/api/') === 0;
    }
}
