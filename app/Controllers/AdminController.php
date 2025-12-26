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
            'title' => 'Chi tiết khảo sát',
            'headerTitle' => 'Chi tiết khảo sát',
            'headerIcon' => 'fas fa-eye',
        ])));
    }

    public function questionResponses(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $data['surveyId'] = (int) ($request->query('surveyId') ?? 0);
        $data['questionId'] = (int) ($request->query('questionId') ?? 0);

        $content = $view->render('pages/admin/surveys/question-responses', $data);

        return \App\Core\Response::html($view->render('layouts/admin', array_merge($data, [
            'content' => $content,
            'title' => 'Câu trả lời của câu hỏi',
            'headerTitle' => 'Câu trả lời của câu hỏi',
            'headerIcon' => 'fas fa-comments',
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

        // Fetch top surveys by responses with average ratings
        $data['topSurveys'] = \App\Models\Survey::getTopSurveysByResponses(5);

        // Fetch top active users by survey count
        $data['topUsers'] = \App\Models\User::getTopActiveUsers(5);

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

    public function eventView(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $data['eventId'] = (int) ($request->query('id') ?? 0);

        $content = $view->render('pages/admin/events/view', $data);

        return \App\Core\Response::html($view->render('layouts/admin', array_merge($data, [
            'content' => $content,
            'title' => 'Chi tiết sự kiện',
            'headerTitle' => 'Chi tiết sự kiện',
            'headerIcon' => 'fas fa-calendar-alt',
        ])));
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

    /**
     * API endpoint to get top surveys by response count
     * GET /api/admin/top-surveys
     */
    public function getTopSurveys(Request $request)
    {
        try {
            $limit = (int) ($request->query('limit') ?? 5);
            $limit = max(1, min(20, $limit)); // Between 1 and 20

            $topSurveys = \App\Models\Survey::getTopSurveysByResponses($limit);

            return \App\Core\Response::json([
                'success' => true,
                'data' => $topSurveys,
            ]);
        } catch (\Throwable $e) {
            return \App\Core\Response::json([
                'success' => false,
                'message' => 'Failed to load top surveys: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API endpoint to get user statistics
     * GET /api/admin/user-stats
     */
    public function getUserStats(Request $request)
    {
        try {
            $stats = \App\Models\User::getUserStatistics();

            return \App\Core\Response::json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Throwable $e) {
            return \App\Core\Response::json([
                'success' => false,
                'message' => 'Failed to load user statistics: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API endpoint to get survey statistics
     * GET /api/admin/survey-stats
     */
    public function getSurveyStats(Request $request)
    {
        try {
            $stats = \App\Models\Survey::getSurveyStatistics();

            return \App\Core\Response::json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Throwable $e) {
            return \App\Core\Response::json([
                'success' => false,
                'message' => 'Failed to load survey statistics: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API endpoint to get response statistics (survey submissions, feedback, contact messages)
     * GET /api/admin/response-stats
     */
    public function getResponseStats(Request $request)
    {
        try {
            $stats = \App\Models\SurveySubmission::getResponseStatistics();

            return \App\Core\Response::json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Throwable $e) {
            return \App\Core\Response::json([
                'success' => false,
                'message' => 'Failed to load response statistics: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API endpoint to get event statistics
     * GET /api/admin/event-stats
     */
    public function getEventStats(Request $request)
    {
        try {
            $stats = \App\Models\Event::getEventStatistics();

            return \App\Core\Response::json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Throwable $e) {
            return \App\Core\Response::json([
                'success' => false,
                'message' => 'Failed to load event statistics: ' . $e->getMessage(),
            ], 500);
        }
    }
}
