<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Request;
use App\Core\Response;


class GuestMiddleware implements MiddlewareInterface
{
    public function handle(Request $request): ?Response
    {
        if (isset($_SESSION['user_id'])) {
            // Nếu là API request
            if ($this->isApiRequest($request)) {
                return Response::json([
                    'error' => true,
                    'message' => 'Bạn đã đăng nhập rồi.',
                ], 403);
            }

            // Nếu là page request, redirect về home
            return Response::redirect('/');
        }

        return null;
    }
}
