<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    private ActivityLog $activityLogModel;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLog();
    }

    // lấy tất cả activity logs (admin)
    public function index(Request $request)
    {
        $page = (int) ($request->query('page') ?? 1);
        $limit = (int) ($request->query('limit') ?? 50);
        $offset = ($page - 1) * $limit;

        $logs = $this->activityLogModel->getAll($limit, $offset);
        $total = $this->activityLogModel->countAll();

        return Response::json([
            'success' => true,
            'data' => $logs,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit),
            ],
        ]);
    }

    // lấy activity logs của user hiện tại
    public function getMyLogs(Request $request)
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            return Response::json([
                'success' => false,
                'message' => 'Chưa xác thực',
            ], 401);
        }

        $page = (int) ($request->query('page') ?? 1);
        $limit = (int) ($request->query('limit') ?? 50);
        $offset = ($page - 1) * $limit;

        $logs = $this->activityLogModel->getByUserId($userId, $limit, $offset);
        $total = $this->activityLogModel->countByUserId($userId);

        return Response::json([
            'success' => true,
            'user_id' => $userId,
            'data' => $logs,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit),
            ],
        ]);
    }

    // lấy activity logs của user
    public function getUserLogs(Request $request)
    {
        $userId = (int) $request->getAttribute('id');
        $page = (int) ($request->query('page') ?? 1);
        $limit = (int) ($request->query('limit') ?? 50);
        $offset = ($page - 1) * $limit;

        $logs = $this->activityLogModel->getByUserId($userId, $limit, $offset);
        $total = $this->activityLogModel->countByUserId($userId);

        return Response::json([
            'success' => true,
            'user_id' => $userId,
            'data' => $logs,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit),
            ],
        ]);
    }

    // lấy activity logs của một entity
    public function getEntityLogs(Request $request)
    {
        $entityType = (string) $request->getAttribute('type');
        $entityId = (int) $request->getAttribute('id');
        $limit = (int) ($request->query('limit') ?? 50);

        $logs = $this->activityLogModel->getByEntity($entityType, $entityId, $limit);

        return Response::json([
            'success' => true,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'data' => $logs,
            'count' => count($logs),
        ]);
    }

    // lấy theo action
    public function getActionLogs(Request $request)
    {
        $action = (string) $request->getAttribute('action');
        $page = (int) ($request->query('page') ?? 1);
        $limit = (int) ($request->query('limit') ?? 50);
        $offset = ($page - 1) * $limit;

        $logs = $this->activityLogModel->getByAction($action, $limit, $offset);

        return Response::json([
            'success' => true,
            'action' => $action,
            'data' => $logs,
        ]);
    }

    public function cleanup(Request $request)
    {
        $days = (int) ($request->query('days') ?? 90);

        if ($days < 7) {
            return Response::json([
                'success' => false,
                'message' => 'Không thể xóa logs trong vòng 7 ngày gần đây',
            ], 400);
        }

        $result = $this->activityLogModel->deleteOldLogs($days);

        return Response::json([
            'success' => $result,
            'message' => $result ? "Activity logs cũ hơn {$days} ngày đã được xóa" : 'Xóa thất bại',
        ]);
    }

    public function viewPage(Request $request)
    {
        $view = new \App\Core\View();
        $data = $this->pageData($request);
        $page = (int) ($request->query('page') ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $logs = $this->activityLogModel->getAll($limit, $offset);
        $total = $this->activityLogModel->countAll();

        $data['logs'] = $logs;
        $data['pagination'] = [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => ceil($total / $limit),
        ];

        $content = $view->render('pages/admin/activity-logs/index', $data);
        return Response::html($view->render('layouts/admin', array_merge($data, ['content' => $content])));
    }
}
