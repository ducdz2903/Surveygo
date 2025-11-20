<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Container;
use App\Models\Question;
use PDO;

class QuestionController extends Controller
{
    /**
     * Lấy tất cả câu hỏi
     * GET /api/questions
     */
    public function index(Request $request)
    {
        $questions = Question::all();

        return $this->json([
            'error' => false,
            'data' => array_map(fn($q) => $q->toArray(), $questions),
        ]);
    }

    /**
     * Lấy tất cả câu hỏi của một khảo sát
     * GET /api/questions?maKhaoSat=:id
     */
    public function getBySurvey(Request $request)
    {
        $surveyId = $request->query('maKhaoSat') ?? $request->input('maKhaoSat') ?? $request->query('surveyId') ?? $request->input('surveyId');

        if (!$surveyId || !is_numeric($surveyId)) {
            return $this->json([
                'error' => true,
                'message' => 'Mã khảo sát không hợp lệ.',
            ], 422);
        }

        $questions = Question::findBySurvey((int)$surveyId);

        return $this->json([
            'error' => false,
            'data' => array_map(fn($q) => $q->toArray(), $questions),
        ]);
    }

    /**
     * Lấy chi tiết 1 câu hỏi
     * GET /api/questions?id=:id
     */
    public function show(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');

        if (!$id || !is_numeric($id)) {
            return $this->json([
                'error' => true,
                'message' => 'ID câu hỏi không hợp lệ.',
            ], 422);
        }

        $question = Question::find((int)$id);
        if (!$question) {
            return $this->json([
                'error' => true,
                'message' => 'Câu hỏi không tồn tại.',
            ], 404);
        }

        return $this->json([
            'error' => false,
            'data' => $question->toArray(),
        ]);
    }

    /**
     * Lấy danh sách đáp án của một câu hỏi
     * GET /api/questions/{id}/answers
     */
    public function getAnswersForQuestion(Request $request)
    {
        // Lấy {id} từ URL params
        $id = $request->getAttribute('id') ?? $request->query('id') ?? $request->input('id');

        if (!$id || !is_numeric($id)) {
            return $this->json([
                'error' => true,
                'message' => 'ID câu hỏi không hợp lệ.',
            ], 422);
        }

        $question = Question::find((int)$id);
        if (!$question) {
            return $this->json([
                'error' => true,
                'message' => 'Câu hỏi không tồn tại.',
            ], 404);
        }

        $answers = $question->getAnswers();

        return $this->json([
            'error' => false,
            'data' => $answers,
        ]);
    }

    /**
     * Tạo câu hỏi mới
     * POST /api/questions
     * Body: { maKhaoSat, loaiCauHoi, noiDungCauHoi, batBuocTraLoi?, thuTu?, maCauHoi? }
     */
    public function create(Request $request)
    {
        $data = $request->input();

        $errors = $this->validateQuestionCreate($data);
        if (!empty($errors)) {
            return $this->json([
                'error' => true,
                'message' => 'Validation failed',
                'errors' => $errors,
            ], 422);
        }

        $question = Question::create($data);
        if (!$question) {
            return $this->json([
                'error' => true,
                'message' => 'Tạo câu hỏi thất bại. Vui lòng kiểm tra dữ liệu hoặc khảo sát không tồn tại.',
            ], 422);
        }

        return $this->json([
            'error' => false,
            'message' => 'Câu hỏi được tạo thành công.',
            'data' => $question->toArray(),
        ], 201);
    }

    /**
     * Cập nhật câu hỏi
     * PUT /api/questions?id=:id
     */
    public function update(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');

        if (!$id || !is_numeric($id)) {
            return $this->json([
                'error' => true,
                'message' => 'ID câu hỏi không hợp lệ.',
            ], 422);
        }

        $question = Question::find((int)$id);
        if (!$question) {
            return $this->json([
                'error' => true,
                'message' => 'Câu hỏi không tồn tại.',
            ], 404);
        }

        $data = $request->input();
        $errors = $this->validateQuestionUpdate($data);
        if (!empty($errors)) {
            return $this->json([
                'error' => true,
                'message' => 'Validation failed',
                'errors' => $errors,
            ], 422);
        }

        if (!$question->update($data)) {
            return $this->json([
                'error' => true,
                'message' => 'Không thể cập nhật câu hỏi.',
            ], 500);
        }

        // Reload
        $question = Question::find((int)$id);

        return $this->json([
            'error' => false,
            'message' => 'Cập nhật câu hỏi thành công.',
            'data' => $question->toArray(),
        ]);
    }

    /**
     * Xoá câu hỏi
     * DELETE /api/questions?id=:id
     */
    public function delete(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');

        if (!$id || !is_numeric($id)) {
            return $this->json([
                'error' => true,
                'message' => 'ID câu hỏi không hợp lệ.',
            ], 422);
        }

        $question = Question::find((int)$id);
        if (!$question) {
            return $this->json([
                'error' => true,
                'message' => 'Câu hỏi không tồn tại.',
            ], 404);
        }

        if (!$question->delete()) {
            return $this->json([
                'error' => true,
                'message' => 'Không thể xóa câu hỏi.',
            ], 500);
        }

        return $this->json([
            'error' => false,
            'message' => 'Câu hỏi được xóa thành công.',
        ]);
    }

    // ========== VALIDATION HELPERS ==========

    private function validateQuestionCreate(array $data): array
    {
        $errors = [];

        if (empty($data['maKhaoSat']) || !is_numeric($data['maKhaoSat'])) {
            $errors['maKhaoSat'] = 'Mã khảo sát là bắt buộc và phải là số.';
        }

        if (empty($data['loaiCauHoi'])) {
            $errors['loaiCauHoi'] = 'Loại câu hỏi là bắt buộc (single_choice, multiple_choice, text, etc.).';
        }

        if (empty($data['noiDungCauHoi'])) {
            $errors['noiDungCauHoi'] = 'Nội dung câu hỏi là bắt buộc.';
        }

        return $errors;
    }

    private function validateQuestionUpdate(array $data): array
    {
        $errors = [];

        if (isset($data['loaiCauHoi']) && empty($data['loaiCauHoi'])) {
            $errors['loaiCauHoi'] = 'Loại câu hỏi không được để trống.';
        }

        if (isset($data['noiDungCauHoi']) && empty($data['noiDungCauHoi'])) {
            $errors['noiDungCauHoi'] = 'Nội dung câu hỏi không được để trống.';
        }

        return $errors;
    }
}
