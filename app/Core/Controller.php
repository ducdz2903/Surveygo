<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function json(array $data, int $status = 200, array $headers = []): Response
    {
        return Response::json($data, $status, $headers);
    }

    protected function view(string $template, array $data = [], ?string $layout = null, int $status = 200, array $headers = []): Response
    {
        $view = new View();
        if ($layout) {
            $data['_layout'] = $layout;
        }
        return Response::html($view->render($template, $data), $status, $headers);
    }
}
