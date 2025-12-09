<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Request;
use App\Core\Response;

interface MiddlewareInterface
{
    public function handle(Request $request): ?Response;
}
