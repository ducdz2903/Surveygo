<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Request;
use App\Core\Response;

/**
 * Middleware kiểm tra xác thực (Authentication)
 * 
 * Kiểm tra user đã đăng nhập hay chưa
 * - Nếu chưa đăng nhập + API request → Trả JSON 401
 * - Nếu chưa đăng nhập + page request → Redirect tới /login
 * 
 * @example
 * $router->get('/profile', [Controller::class, 'profile'], [AuthMiddleware::class]);
 */
class AuthMiddleware implements MiddlewareInterface
{
    /**
     * Xử lý request
     * 
     * @param Request $request
     * @return Response|null
     */
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

    /**
     * Kiểm tra có phải API request không
     * 
     * @param Request $request
     * @return bool true nếu là API request, false nếu là page request
     */
    private function isApiRequest(Request $request): bool
    {
        $path = $request->uri();
        return strpos($path, '/api/') === 0;
    }
}
