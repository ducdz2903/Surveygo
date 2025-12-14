<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Container;
use App\Core\Controller;
use App\Core\Request;
use App\Models\ActivityLog;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $base = rtrim((string) ($data['baseUrl'] ?? ''), '/');
        $data['headExtra'] = '<link rel="stylesheet" href="' . $base . '/public/assets/css/client/pages/landing-page.css">';
        $content = $view->render("pages/client/home/landing", $data);
        return \App\Core\Response::html($view->render('layouts/main', array_merge($data, ['content' => $content])));
    }

    public function homeAfterLogin(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $base = rtrim((string) ($data['baseUrl'] ?? ''), '/');
        $data['headExtra'] = '<link rel="stylesheet" href="' . $base . '/public/assets/css/client/pages/home.css">';
        
        // Get recent activities for current user
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $activityLog = new ActivityLog();
            $data['recentActivities'] = $activityLog->getByUserId($userId, 5, 0);
        } else {
            $data['recentActivities'] = [];
        }
        
        $content = $view->render("pages/client/home/home", $data);
        return \App\Core\Response::html($view->render('layouts/main', array_merge($data, ['content' => $content])));
    }

    public function login(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $base = rtrim((string) ($data['baseUrl'] ?? ''), '/');
        $data['headExtra'] = '<link rel="stylesheet" href="' . $base . '/public/assets/css/auth/login.css">';
        $viewScriptPath = BASE_PATH . '/app/Views/pages/auth/script.js';
        $scriptContent = (string) @file_get_contents($viewScriptPath);
        $data['scriptsExtra'] = "<script>\n" . $scriptContent . "\n</script>";
        $content = $view->render("pages/auth/login", $data);
        return \App\Core\Response::html($view->render('layouts/main', array_merge($data, ['content' => $content])));
    }

    public function register(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $base = rtrim((string) ($data['baseUrl'] ?? ''), '/');
        $data['headExtra'] = '<link rel="stylesheet" href="' . $base . '/public/assets/css/auth/register.css">';
        $viewScriptPath = BASE_PATH . '/app/Views/pages/auth/script.js';
        $scriptContent = (string) @file_get_contents($viewScriptPath);
        $data['scriptsExtra'] = "<script>\n" . $scriptContent . "\n</script>";
        $content = $view->render("pages/auth/register", $data);
        return \App\Core\Response::html($view->render('layouts/main', array_merge($data, ['content' => $content])));
    }

    public function profile(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $base = rtrim((string) ($data['baseUrl'] ?? ''), '/');
        $data['headExtra'] = '<link rel="stylesheet" href="' . $base . '/public/assets/css/client/pages/profile.css">';
        $content = $view->render("pages/client/profile/profile", $data);
        return \App\Core\Response::html($view->render('layouts/main', array_merge($data, ['content' => $content])));
    }

    public function dailyRewards(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $base = rtrim((string) ($data['baseUrl'] ?? ''), '/');
        $data['headExtra'] = '<link rel="stylesheet" href="' . $base . '/public/assets/css/client/pages/daily-rewards.css">';
        $content = $view->render("pages/client/daily-rewards/daily-rewards", $data);
        return \App\Core\Response::html($view->render('layouts/main', array_merge($data, ['content' => $content])));
    }

    public function events(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $base = rtrim((string) ($data['baseUrl'] ?? ''), '/');
        $data['headExtra'] = '<link rel="stylesheet" href="' . $base . '/public/assets/css/client/pages/events.css">';
        $content = $view->render('pages/client/events/events', $data);
        return \App\Core\Response::html($view->render('layouts/main', array_merge($data, ['content' => $content])));
    }

    public function rewards(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $base = rtrim((string) ($data['baseUrl'] ?? ''), '/');
        $data['headExtra'] = '<link rel="stylesheet" href="' . $base . '/public/assets/css/client/pages/rewards.css">';
        $content = $view->render('pages/client/rewards/rewards', $data);
        return \App\Core\Response::html($view->render('layouts/main', array_merge($data, ['content' => $content])));
    }

    public function terms(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $base = rtrim((string) ($data['baseUrl'] ?? ''), '/');
        $data['headExtra'] = '<link rel="stylesheet" href="' . $base . '/public/assets/css/client/pages/term-of-use.css">';
        $content = $view->render('pages/client/terms/terms-of-use', $data);
        return \App\Core\Response::html($view->render('layouts/main', array_merge($data, ['content' => $content])));
    }

    public function about(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $base = rtrim((string) ($data['baseUrl'] ?? ''), '/');
        $data['headExtra'] = '<link rel="stylesheet" href="' . $base . '/public/assets/css/client/pages/term-of-use.css">';
        $content = $view->render('pages/client/about/about', $data);
        return \App\Core\Response::html($view->render('layouts/main', array_merge($data, ['content' => $content])));
    }


    public function contact(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $base = rtrim((string) ($data['baseUrl'] ?? ''), '/');
        $data['headExtra'] = '<link rel="stylesheet" href="' . $base . '/public/assets/css/client/pages/contact.css">';
        $content = $view->render('pages/client/contact/contact', $data);
        return \App\Core\Response::html($view->render('layouts/main', array_merge($data, ['content' => $content])));
    }

    public function surveys(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $base = rtrim((string) ($data['baseUrl'] ?? ''), '/');
        $data['headExtra'] = '<link rel="stylesheet" href="' . $base . '/public/assets/css/client/pages/home.css">';
        $content = $view->render('pages/client/surveys/surveys', $data);
        return \App\Core\Response::html($view->render('layouts/main', array_merge($data, ['content' => $content])));
    }

    public function quickPoll(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $base = rtrim((string) ($data['baseUrl'] ?? ''), '/');
        $data['headExtra'] = '<link rel="stylesheet" href="' . $base . '/public/assets/css/client/pages/home.css">';
        $content = $view->render('pages/client/surveys/quick-poll', $data);
        return \App\Core\Response::html($view->render('layouts/main', array_merge($data, ['content' => $content])));
    }

    public function surveyGuide(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $base = rtrim((string) ($data['baseUrl'] ?? ''), '/');
        $data['headExtra'] = '<link rel="stylesheet" href="' . $base . '/public/assets/css/client/pages/survey-guide.css">';
        $content = $view->render('pages/client/surveys/guide', $data);
        return \App\Core\Response::html($view->render('layouts/main', array_merge($data, ['content' => $content])));
    }

    public function surveyQuestions(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $base = rtrim((string) ($data['baseUrl'] ?? ''), '/');
        $data['headExtra'] = '<link rel="stylesheet" href="' . $base . '/public/assets/css/client/pages/survey-questions.css">';
        $content = $view->render('pages/client/surveys/questions', $data);
        return \App\Core\Response::html($view->render('layouts/main', array_merge($data, ['content' => $content])));
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
