<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Request;
use App\Core\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request): ?Response
    {
        // Kiểm tra user_id có trong session không
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

        // Nếu đã xác thực, tiếp tục xử lý request
        return null;
    }

    private function isApiRequest(Request $request): bool
    {
        $path = $request->uri();
        return strpos($path, '/api/') === 0;
    }
}
