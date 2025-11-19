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
        return $this->view('pages/client/home/landing', $this->pageData($request));
    }

    public function homeAfterLogin(Request $request)
    {
        return $this->view('pages/client/home/home', $this->pageData($request));
    }

    public function login(Request $request)
    {
        return $this->view('pages/auth/login', $this->pageData($request), 200, []);
    }

    public function register(Request $request)
    {
        return $this->view('pages/auth/register', $this->pageData($request), 200, []);
    }

    public function profile(Request $request)
    {
        return $this->view('pages/client/profile/profile', $this->pageData($request));
    }

    public function dailyRewards(Request $request)
    {
        return $this->view('pages/client/daily-rewards/daily-rewards', $this->pageData($request));
    }

    public function events(Request $request)
    {
        return $this->view('pages/client/events/events', $this->pageData($request));
    }

    public function rewards(Request $request)
    {
        return $this->view('pages/client/rewards/rewards', $this->pageData($request));
    }

    public function terms(Request $request)
    {
        return $this->view('pages/client/terms/terms-of-use', $this->pageData($request));
    }

    public function contact(Request $request)
    {
        return $this->view('pages/client/contact/contact', $this->pageData($request));
    }

    public function surveys(Request $request)
    {
        return $this->view('pages/client/surveys/surveys', $this->pageData($request));
    }

    public function quickPoll(Request $request)
    {
        return $this->view('pages/client/surveys/quick-poll', $this->pageData($request));
    }

    public function surveyGuide(Request $request)
    {
        return $this->view('pages/client/surveys/guide', $this->pageData($request));
    }

    public function surveyQuestions(Request $request)
    {
        return $this->view('pages/client/surveys/questions', $this->pageData($request));
    }

    private function pageData(Request $request): array
    {
        $config = Container::get('config');
        $appConfig = $config['app'] ?? [];

        $baseUrl = $this->detectBaseUrl($request, $appConfig);

        return [
            'appName' => (string) ($appConfig['name'] ?? 'PHP Application'),
            'baseUrl' => $baseUrl,
            'currentPath' => $request->uri(),
            'urls' => [
                'home' => $this->urlFor($baseUrl, '/home'),
                'login' => $this->urlFor($baseUrl, '/login'),
                'register' => $this->urlFor($baseUrl, '/register'),
            ],
        ];
    }

    private function detectBaseUrl(Request $request, array $appConfig): string
    {
        $configured = trim((string) ($appConfig['base_url'] ?? ''));
        if ($configured !== '') {
            return rtrim($configured, '/');
        }

        $https = $request->server('HTTPS');
        $scheme = (!empty($https) && $https !== 'off') ? 'https' : 'http';
        $host = $request->server('HTTP_HOST') ?? 'localhost';
        $scriptName = (string) $request->server('SCRIPT_NAME', '');
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
