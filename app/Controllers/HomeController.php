<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Container;
use App\Core\Controller;
use App\Core\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        return $this->view('pages/landing/index', $this->pageData($request));
    }

    public function features(Request $request)
    {
        return $this->view('pages/features/index', $this->pageData($request));
    }

    public function login(Request $request)
    {
        return $this->view('pages/login/index', $this->pageData($request));
    }

    public function register(Request $request)
    {
        return $this->view('pages/register/index', $this->pageData($request));
    }

    private function pageData(Request $request): array
    {
        $config = Container::get('config');
        $appConfig = $config['app'] ?? [];

        $baseUrl = $this->detectBaseUrl($request, $appConfig);

        return [
            'appName' => (string)($appConfig['name'] ?? 'PHP Application'),
            'baseUrl' => $baseUrl,
            'currentPath' => $request->uri(),
            'urls' => [
                'home' => $this->urlFor($baseUrl, '/'),
                'features' => $this->urlFor($baseUrl, '/features'),
                'login' => $this->urlFor($baseUrl, '/login'),
                'register' => $this->urlFor($baseUrl, '/register'),
            ],
        ];
    }

    private function detectBaseUrl(Request $request, array $appConfig): string
    {
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
    }

    private function urlFor(string $baseUrl, string $path): string
    {
        $normalizedBase = rtrim($baseUrl, '/');
        $normalizedPath = '/' . ltrim($path, '/');

        return $normalizedBase === '' ? $normalizedPath : "{$normalizedBase}{$normalizedPath}";
    }
}
