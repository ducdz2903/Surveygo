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
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $content = $view->render('pages/admin/dashboard/dashboard', $data);
        return \App\Core\Response::html($view->render('layouts/admin', array_merge($data, ['content' => $content])));
    }

    public function surveys(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $content = $view->render('pages/admin/surveys/surveys', $data);
        return \App\Core\Response::html($view->render('layouts/admin', array_merge($data, ['content' => $content])));
    }

    public function surveyView(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $data['surveyId'] = (int) ($request->query('id') ?? 0);

        $content = $view->render('pages/admin/surveys/view', $data);

        return \App\Core\Response::html($view->render('layouts/admin', array_merge($data, [
            'content' => $content,
            'title' => 'Chi tiet khao sat',
            'headerTitle' => 'Chi tiet khao sat',
            'headerIcon' => 'fas fa-eye',
        ])));
    }

    public function users(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $content = $view->render('pages/admin/users/users', $data);
        return \App\Core\Response::html($view->render('layouts/admin', array_merge($data, ['content' => $content])));
    }

    public function questions(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $data['surveys'] = \App\Models\Survey::all();
        $content = $view->render('pages/admin/questions/questions', $data);
        return \App\Core\Response::html($view->render('layouts/admin', array_merge($data, ['content' => $content])));
    }

    public function reports(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $content = $view->render('pages/admin/reports/reports', $data);
        return \App\Core\Response::html($view->render('layouts/admin', array_merge($data, ['content' => $content])));
    }

    public function events(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $content = $view->render('pages/admin/events/events', $data);
        return \App\Core\Response::html($view->render('layouts/admin', array_merge($data, ['content' => $content])));
    }

    public function settings(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $content = $view->render('pages/admin/settings/settings', $data);
        return \App\Core\Response::html($view->render('layouts/admin', array_merge($data, ['content' => $content])));
    }

    public function feedbacks(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $content = $view->render('pages/admin/feedbacks/feedbacks', $data);
        return \App\Core\Response::html($view->render('layouts/admin', array_merge($data, ['content' => $content])));
    }

    public function contactMessages(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $content = $view->render('pages/admin/contact-messages/contact-messages', $data);
        return \App\Core\Response::html($view->render('layouts/admin', array_merge($data, ['content' => $content])));
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

    public function rewards(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $content = $view->render('pages/admin/rewards/rewards', $data);
        return \App\Core\Response::html($view->render('layouts/admin', array_merge($data, ['content' => $content])));
    }

    public function redemptions(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $content = $view->render('pages/admin/redemptions/reward_redemptions', $data);
        return \App\Core\Response::html($view->render('layouts/admin', array_merge($data, ['content' => $content])));
    }
}
