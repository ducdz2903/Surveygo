<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Event;
use App\Models\User;
use App\Helpers\ActivityLogHelper;

class EventController extends Controller
{

    // api phân trang
    public function index(Request $request)
    {
        $page = (int) ($request->query('page') ?? 1);
        $limit = (int) ($request->query('limit') ?? 10);

        $filters = [];
        if ($search = $request->query('search')) {
            $filters['search'] = $search;
        }
        if ($trangThai = $request->query('trangThai') ?? $request->query('status')) {
            $filters['trangThai'] = $trangThai;
        }

        $result = Event::paginate($page, $limit, $filters);

        $events = array_map(function ($e) {
            $creatorName = null;
            if ($e->getMaNguoiTao()) {
                $user = User::findById($e->getMaNguoiTao());
                $creatorName = $user ? $user->getName() : null;
            }

            return [
                'id' => $e->getId(),
                'code' => $e->getMaSuKien(),
                'title' => $e->getTenSuKien(),
                'location' => $e->getDiaDiem(),
                'startDate' => $e->getThoiGianBatDau(),
                'endDate' => $e->getThoiGianKetThuc(),
                'status' => $e->getTrangThai(),
                'participants' => $e->getSoNguoiThamGia(),
                'surveys' => $e->getSoKhaoSat(),
                'creatorId' => $e->getMaNguoiTao(),
                'creator' => $creatorName,
                'created_at' => $e->getCreatedAt() ?? null,
                'updated_at' => $e->getUpdatedAt() ?? null,
            ];
        }, $result['events']);

        return $this->json([
            'error' => false,
            'data' => $events,
            'meta' => [
                'total' => $result['total'],
                'page' => $result['page'],
                'limit' => $result['limit'],
                'totalPages' => $result['totalPages'],
            ],
        ]);
    }

    /**
     * POST /api/events/{id}/join
     * User tham gia sự kiện
     */
    public function join(Request $request)
    {
        $eventId = (int) $request->getAttribute('id');
        $userId = $_SESSION['user_id'] ?? null;

        if (!$eventId) {
            return $this->json([
                'error' => true,
                'message' => 'Event ID không hợp lệ.',
            ], 422);
        }

        if (!$userId) {
            return $this->json([
                'error' => true,
                'message' => 'Vui lòng đăng nhập để tham gia sự kiện.',
            ], 401);
        }

        $event = Event::find($eventId);
        if (!$event) {
            return $this->json([
                'error' => true,
                'message' => 'Sự kiện không tồn tại.',
            ], 404);
        }

        // Log activity
        try {
            ActivityLogHelper::logParticipatedEvent($userId, $eventId);
        } catch (\Throwable $e) {
            error_log('[EventController::join] Failed to log activity: ' . $e->getMessage());
        }

        return $this->json([
            'error' => false,
            'message' => 'Tham gia sự kiện thành công.',
            'data' => [
                'eventId' => $eventId,
                'eventName' => $event->getTenSuKien(),
            ],
        ], 201);
    }

    /**
     * POST /api/events
     * Admin tạo sự kiện mới
     */
    public function create(Request $request)
    {
        $data = $request->input();

        if (empty($data['tenSuKien'])) {
            return $this->json([
                'error' => true,
                'message' => 'Tên sự kiện là bắt buộc.',
            ], 422);
        }

        $event = Event::create($data);
        if (!$event) {
            return $this->json([
                'error' => true,
                'message' => 'Không thể tạo sự kiện.',
            ], 500);
        }

        // Log activity
        try {
            $userId = $_SESSION['user_id'] ?? ($data['maNguoiTao'] ?? 0);
            if ($userId) {
                ActivityLogHelper::logEventCreated($userId, $event->getId(), $event->getTenSuKien());
            }
        } catch (\Throwable $e) {
            error_log('[EventController::create] Failed to log activity: ' . $e->getMessage());
        }

        return $this->json([
            'error' => false,
            'message' => 'Sự kiện được tạo thành công.',
            'data' => $event->toArray(),
        ], 201);
    }
}
