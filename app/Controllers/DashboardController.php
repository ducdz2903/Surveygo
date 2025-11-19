<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class DashboardController extends Controller
{
    /**
     * GET /survey-dashboard
     * Hiển thị trang dashboard test API
     */
    public function surveyDashboard(Request $request)
    {
        return $this->view('pages/dashboard/survey-dashboard', [
            'pageTitle' => 'Khảo Sát API Dashboard',
            'description' => 'Test tất cả các API endpoints cho hệ thống khảo sát',
            'appName' => $this->detectAppName(),
            'baseUrl' => $this->detectBaseUrl($request),
        ]);
    }

    // Helpers to keep controller simple and avoid duplicating logic from HomeController
    private function detectAppName(): string
    {
        try {
            $config = \App\Core\Container::get('config');
            return (string)($config['app']['name'] ?? 'PHP Application');
        } catch (\Throwable $e) {
            return 'PHP Application';
        }
    }

    private function detectBaseUrl(Request $request): string
    {
        try {
            $config = \App\Core\Container::get('config');
            $appConfig = $config['app'] ?? [];
            $configured = trim((string)($appConfig['base_url'] ?? ''));
            if ($configured !== '') {
                return rtrim($configured, '/');
            }

            $https = $request->server('HTTPS');
            $scheme = (!empty($https) && $https !== 'off') ? 'https' : 'http';
            $host = $request->server('HTTP_HOST') ?? 'localhost';
            $scriptName = (string)$request->server('SCRIPT_NAME', '');
            $basePath = str_replace('\\', '/', $scriptName);
            $basePath = rtrim(dirname($basePath), '/');

            if ($basePath === '.' || $basePath === '/' || $basePath === '') {
                $basePath = '';
            }

            if (str_ends_with($basePath, '/public')) {
                $basePath = substr($basePath, 0, -strlen('/public'));
            }

            $basePath = rtrim($basePath, '/');

            return rtrim("{$scheme}://{$host}{$basePath}", '/');
        } catch (\Throwable $e) {
            return '';
        }
    }
}
