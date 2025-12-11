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

        $events = array_map(fn(Event $e) => $this->transformEvent($e), $result['events']);

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

    public function show(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');

        if (!$id || !is_numeric($id)) {
            return $this->json([
                'error' => true,
                'message' => 'Mã sự kiện không hợp lệ.',
            ], 422);
        }

        $event = Event::find((int) $id);
        if (!$event) {
            return $this->json([
                'error' => true,
                'message' => 'Không tìm thấy sự kiện.',
            ], 404);
        }

        return $this->json([
            'error' => false,
            'data' => $this->transformEvent($event),
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

        if (empty($data)) {
            $raw = @file_get_contents('php://input');
            $json = $raw ? json_decode($raw, true) : null;
            if (is_array($json)) {
                $data = $json;
            } else {
                $data = [];
            }
        }

        $data['tenSuKien'] = trim((string) ($data['tenSuKien'] ?? $data['ten_su_kien'] ?? $data['title'] ?? ''));
        $data['diaDiem'] = $data['diaDiem'] ?? $data['dia_diem'] ?? $data['location'] ?? null;
        $data['thoiGianBatDau'] = $data['thoiGianBatDau'] ?? $data['startDate'] ?? $data['start_date'] ?? null;
        $data['thoiGianKetThuc'] = $data['thoiGianKetThuc'] ?? $data['endDate'] ?? $data['end_date'] ?? null;
        $data['trangThai'] = $data['trangThai'] ?? $data['trang_thai'] ?? $data['status'] ?? 'upcoming';
        $data['soNguoiThamGia'] = isset($data['soNguoiThamGia'])
            ? (int) $data['soNguoiThamGia']
            : (isset($data['participants']) ? (int) $data['participants'] : 0);
        $data['soKhaoSat'] = isset($data['soKhaoSat'])
            ? (int) $data['soKhaoSat']
            : (isset($data['surveys']) ? (int) $data['surveys'] : 0);

        $rawCreator = $data['maNguoiTao'] ?? $data['creatorId'] ?? $data['creator_id'] ?? null;
        if ($rawCreator === null || $rawCreator === '') {
            $data['maNguoiTao'] = 1;
        } else {
            $data['maNguoiTao'] = (int) $rawCreator;
        }

        $errors = $this->validateEventCreate($data);
        if (!empty($errors)) {
            return $this->json([
                'error' => true,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $errors
            ], 422);
        }

        if (empty($data['tenSuKien'])) {
            return $this->json([
                'error' => true,
                'message' => 'Tên sự kiện là bắt buộc.',
            ], 500);
        }

        $event = Event::create($data);
        if (!$event) {
            return $this->json([
                'error' => true,
                'message' => 'Không thể tạo sự kiện.',
            ], 422);
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
            'message' => 'Sự kiện đã được tạo thành công.',
            'data' => $this->transformEvent($event),
        ], 201);
    }

    public function update(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');

        if (!$id || !is_numeric($id)) {
            return $this->json([
                'error' => true,
                'message' => 'Invalid event ID.',
            ], 422);
        }

        $event = Event::find((int) $id);
        if (!$event) {
            return $this->json([
                'error' => true,
                'message' => 'Event not found.',
            ], 404);
        }

        $data = $request->input();

        $errors = $this->validateEventUpdate($data);
        if (!empty($errors)) {
            return $this->json([
                'error' => true,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $errors,
            ], 422);
        }

        if (!$event->update($data)) {
            return $this->json([
                'error' => true,
                'message' => 'Không thể cập nhật sự kiện.',
            ], 500);
        }

        $event = Event::find((int) $id);

        return $this->json([
            'error' => false,
            'message' => 'Cập nhật sự kiện thành công.',
            'data' => $event ? $this->transformEvent($event) : null,
        ]);
    }

    public function delete(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');

        if (!$id || !is_numeric($id)) {
            return $this->json([
                'error' => true,
                'message' => 'ID sự kiện không hợp lệ.',
            ], 422);
        }

        $event = Event::find((int) $id);
        if (!$event) {
            return $this->json([
                'error' => true,
                'message' => 'Không tìm thấy sự kiện.',
            ], 404);
        }

        if (!$event->delete()) {
            return $this->json([
                'error' => true,
                'message' => 'Không thể xóa sự kiện.',
            ], 500);
        }

        return $this->json([
            'error' => false,
            'message' => 'Sự kiện đã được xóa thành công.',
        ]);
    }

    private function transformEvent(Event $e): array
    {
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
    }

    private function validateEventCreate(array $data): array
    {
        $errors = [];

        if (empty($data['tenSuKien'])) {
            $errors['tenSuKien'] = 'Tiêu đề sự kiện là bắt buộc.';
        }

        if (isset($data['trangThai']) && $data['trangThai'] !== '') {
            $allowed = ['upcoming', 'ongoing', 'completed'];
            if (!in_array($data['trangThai'], $allowed, true)) {
                $errors['trangThai'] = 'Trạng thái sự kiện không hợp lệ.';
            }
        }

        if (empty($data['maNguoiTao']) || !is_numeric($data['maNguoiTao']) || (int) $data['maNguoiTao'] <= 0) {
            $errors['maNguoiTao'] = 'ID người tạo không hợp lệ.';
        } else {
            $user = User::findById((int) $data['maNguoiTao']);
            if (!$user) {
                $errors['maNguoiTao'] = 'Không tìm thấy người tạo.';
            }
        }

        return $errors;
    }

    private function validateEventUpdate(array $data): array
    {
        $errors = [];

        if (array_key_exists('tenSuKien', $data) && trim((string) $data['tenSuKien']) === '') {
            $errors['tenSuKien'] = 'Tiêu đề sự kiện không được để trống.';
        }

        if (array_key_exists('trangThai', $data) && $data['trangThai'] !== '') {
            $allowed = ['upcoming', 'ongoing', 'completed'];
            if (!in_array($data['trangThai'], $allowed, true)) {
                $errors['trangThai'] = 'Trạng thái sự kiện không hợp lệ.';
            }
        }

        if (array_key_exists('maNguoiTao', $data)) {
            if (empty($data['maNguoiTao']) || !is_numeric($data['maNguoiTao']) || (int) $data['maNguoiTao'] <= 0) {
                $errors['maNguoiTao'] = 'ID người tạo không hợp lệ.';
            } else {
                $user = User::findById((int) $data['maNguoiTao']);
                if (!$user) {
                    $errors['maNguoiTao'] = 'Không tìm thấy người tạo.';
                }
            }
        }

        return $errors;
    }
}

