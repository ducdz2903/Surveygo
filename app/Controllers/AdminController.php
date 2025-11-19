<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Container;
use App\Core\Controller;
use App\Core\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        return $this->view('pages/admin/dashboard/dashboard', $this->pageData($request));
    }

    public function surveys(Request $request)
    {
        return $this->view('pages/admin/surveys/index', $this->pageData($request));
    }

    public function users(Request $request)
    {
        return $this->view('pages/admin/users/index', $this->pageData($request));
    }

    public function questions(Request $request)
    {
        return $this->view('pages/admin/questions/index', $this->pageData($request));
    }

    public function reports(Request $request)
    {
        return $this->view('pages/admin/reports/index', $this->pageData($request));
    }

    public function events(Request $request)
    {
        return $this->view('pages/admin/events/index', $this->pageData($request));
    }

    public function settings(Request $request)
    {
        return $this->view('pages/admin/settings/index', $this->pageData($request));
    }

    private function pageData(Request $request): array
    {
        $config = Container::get('config');
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
