<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    // GET /api/contact-messages
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

        $result = ContactMessage::paginate($page, $limit, $filters);

        return $this->json([
            'error' => false,
            'data' => array_map(fn($m) => $m->toArray(), $result['items']),
            'meta' => [
                'total' => $result['total'],
                'page' => $result['page'],
                'limit' => $result['limit'],
                'totalPages' => $result['totalPages'],
            ],
        ]);
    }

    // GET /api/contact-messages/show?id=:id
    public function show(Request $request)
    {
        $id = $request->getAttribute('id') ?? $request->query('id') ?? $request->input('id');
        if (!$id || !is_numeric($id)) {
            return $this->json(['error' => true, 'message' => 'ID không hợp lệ'], 422);
        }
        $m = ContactMessage::findById((int) $id);
        if (!$m) {
            return $this->json(['error' => true, 'message' => 'Message không tồn tại'], 404);
        }
        return $this->json(['error' => false, 'data' => $m->toArray()]);
    }

    // POST /api/contact-messages
    public function create(Request $request)
    {
        $data = $request->input();

        $errors = [];
        if (empty($data['hoTen'])) {
            $errors['hoTen'] = 'Họ tên là bắt buộc.';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ.';
        }
        if (empty($data['chuDe'])) {
            $errors['chuDe'] = 'Chủ đề là bắt buộc.';
        }
        if (empty($data['tinNhan'])) {
            $errors['tinNhan'] = 'Nội dung tin nhắn là bắt buộc.';
        }

        if (!empty($errors)) {
            return $this->json(['error' => true, 'message' => 'Validation failed', 'errors' => $errors], 422);
        }

        $m = ContactMessage::create($data);
        if (!$m) {
            return $this->json(['error' => true, 'message' => 'Không thể tạo contact message.'], 500);
        }

        return $this->json(['error' => false, 'message' => 'Message created.', 'data' => $m->toArray()], 201);
    }

    // PUT /api/contact-messages?id=:id
    public function update(Request $request)
    {
        $id = $request->getAttribute('id') ?? $request->query('id') ?? $request->input('id');
        if (!$id || !is_numeric($id)) {
            return $this->json(['error' => true, 'message' => 'ID không hợp lệ'], 422);
        }
        $m = ContactMessage::findById((int) $id);
        if (!$m) {
            return $this->json(['error' => true, 'message' => 'Message không tồn tại'], 404);
        }

        $data = $request->input();

        // only allow limited updates here (phanHoi or other fields)
        if (!$m->update($data)) {
            return $this->json(['error' => true, 'message' => 'Không thể cập nhật message.'], 500);
        }

        $m = ContactMessage::findById((int) $id);
        return $this->json(['error' => false, 'message' => 'Cập nhật thành công.', 'data' => $m->toArray()]);
    }

    // DELETE /api/contact-messages?id=:id
    public function delete(Request $request)
    {
        $id = $request->getAttribute('id') ?? $request->query('id') ?? $request->input('id');
        if (!$id || !is_numeric($id)) {
            return $this->json(['error' => true, 'message' => 'ID không hợp lệ'], 422);
        }
        $m = ContactMessage::findById((int) $id);
        if (!$m) {
            return $this->json(['error' => true, 'message' => 'Message không tồn tại'], 404);
        }
        if (!$m->delete()) {
            return $this->json(['error' => true, 'message' => 'Không thể xóa message.'], 500);
        }
        return $this->json(['error' => false, 'message' => 'Đã xóa message.']);
    }
}
