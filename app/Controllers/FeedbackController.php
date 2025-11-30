<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    // GET /api/feedbacks
    public function index(Request $request)
    {
        $page = (int) ($request->query('page') ?? 1);
        $limit = (int) ($request->query('limit') ?? 10);

        $filters = [];
        if ($search = $request->query('search')) {
            $filters['search'] = $search;
        }
        if ($ma = $request->query('ma')) {
            $filters['ma'] = $ma;
        }

        $result = Feedback::paginate($page, $limit, $filters);

        return $this->json([
            'error' => false,
            'data' => array_map(fn($f) => $f->toArray(), $result['feedbacks']),
            'meta' => [
                'total' => $result['total'],
                'page' => $result['page'],
                'limit' => $result['limit'],
                'totalPages' => $result['totalPages'],
            ],
        ]);
    }

    // GET /api/feedbacks/show?id=:id
    public function show(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');
        if (!$id || !is_numeric($id)) {
            return $this->json(['error' => true, 'message' => 'ID không hợp lệ'], 422);
        }
        $fb = Feedback::findById((int) $id);
        if (!$fb) {
            return $this->json(['error' => true, 'message' => 'Feedback không tồn tại'], 404);
        }
        return $this->json(['error' => false, 'data' => $fb->toArray()]);
    }

    // POST /api/feedbacks
    public function create(Request $request)
    {
        $data = $request->input();

        // basic validation
        $errors = [];
        if (empty($data['tenNguoiDung'])) {
            $errors['tenNguoiDung'] = 'Tên người dùng là bắt buộc.';
        }
        if (!isset($data['danhGia'])) {
            $errors['danhGia'] = 'Đánh giá là bắt buộc.';
        } else {
            $d = (int) $data['danhGia'];
            if ($d < 0 || $d > 5) {
                $errors['danhGia'] = 'Đánh giá phải trong khoảng 0-5.';
            }
        }

        if (!empty($errors)) {
            return $this->json(['error' => true, 'message' => 'Validation failed', 'errors' => $errors], 422);
        }

        $fb = Feedback::create($data);
        if (!$fb) {
            return $this->json(['error' => true, 'message' => 'Không thể tạo feedback.'], 500);
        }

        return $this->json(['error' => false, 'message' => 'Feedback được tạo.', 'data' => $fb->toArray()], 201);
    }

    // PUT /api/feedbacks?id=:id
    public function update(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');
        if (!$id || !is_numeric($id)) {
            return $this->json(['error' => true, 'message' => 'ID không hợp lệ'], 422);
        }
        $fb = Feedback::findById((int) $id);
        if (!$fb) {
            return $this->json(['error' => true, 'message' => 'Feedback không tồn tại'], 404);
        }

        $data = $request->input();

        // minimal validation
        if (isset($data['danhGia'])) {
            $d = (int) $data['danhGia'];
            if ($d < 0 || $d > 5) {
                return $this->json(['error' => true, 'message' => 'Đánh giá phải trong khoảng 0-5.'], 422);
            }
        }

        if (!$fb->update($data)) {
            return $this->json(['error' => true, 'message' => 'Không thể cập nhật feedback.'], 500);
        }

        $fb = Feedback::findById((int) $id);
        return $this->json(['error' => false, 'message' => 'Cập nhật thành công.', 'data' => $fb->toArray()]);
    }

    // DELETE /api/feedbacks?id=:id
    public function delete(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');
        if (!$id || !is_numeric($id)) {
            return $this->json(['error' => true, 'message' => 'ID không hợp lệ'], 422);
        }
        $fb = Feedback::findById((int) $id);
        if (!$fb) {
            return $this->json(['error' => true, 'message' => 'Feedback không tồn tại'], 404);
        }
        if (!$fb->delete()) {
            return $this->json(['error' => true, 'message' => 'Không thể xóa feedback.'], 500);
        }
        return $this->json(['error' => false, 'message' => 'Đã xóa feedback.']);
    }
}
