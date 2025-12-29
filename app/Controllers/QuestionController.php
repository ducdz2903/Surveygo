<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Container;
use App\Models\Question;
use App\Models\Answer;
use App\Models\SurveyQuestionMap;
use App\Helpers\ActivityLogHelper;
use PDO;

class QuestionController extends Controller
{
    // api lấy all
    public function index(Request $request)
    {
        // Hỗ trợ phân trang và bộ lọc
        $page = (int)($request->query('page') ?? 1);
        $perPage = (int)($request->query('per_page') ?? 10);
        $search = $request->query('search') ?? null;
        $loai = $request->query('loaiCauHoi') ?? $request->query('type') ?? null;
        $survey = $request->query('maKhaoSat') ?? null;

        $filters = [];
        if ($search) $filters['search'] = $search;
        if ($loai) $filters['loaiCauHoi'] = $loai;
        if ($survey) $filters['maKhaoSat'] = $survey;

        $result = Question::paginate(max(1, $page), max(1, $perPage), $filters);

        return $this->json([
            'error' => false,
            'data' => array_map(fn($q) => $q->toArray(), $result['data']),
            'meta' => $result['meta'],
        ]);
    }


    // Lấy tất cả câu hỏi của một khảo sát
    public function getBySurvey(Request $request)
    {
        $surveyId = $request->query('maKhaoSat') ?? $request->input('maKhaoSat') ?? $request->query('surveyId') ?? $request->input('surveyId');

        if (!$surveyId || !is_numeric($surveyId)) {
            return $this->json([
                'error' => true,
                'message' => 'Mã khảo sát không hợp lệ.',
            ], 422);
        }

        $questions = SurveyQuestionMap::findQuestionsBySurvey($surveyId);

        return $this->json([
            'error' => false,
            'data' => array_map(fn($q) => $q->toArray(), $questions),
        ]);
    }

    // api detail
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

        // Bao gồm các đáp án để xem trước
        $data = $question->toArray();
        $data['answers'] = $question->getAnswers();

        return $this->json([
            'error' => false,
            'data' => $data,
        ]);
    }

    // api lấy danh sách đáp án
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

    // tạo mới 
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

        // Liên kết với khảo sát nếu có maKhaoSat
        if (!empty($data['maKhaoSat']) && is_numeric($data['maKhaoSat'])) {
            SurveyQuestionMap::attach((int)$data['maKhaoSat'], $question->getId());
        }

        // Xử lý các đáp án (nếu có)
        if (!empty($data['answers']) && is_array($data['answers'])) {
            foreach ($data['answers'] as $ans) {
                if (!empty($ans['noiDungCauTraLoi'])) {
                    Answer::create([
                        'idCauHoi' => $question->getId(), // Sử dụng ID mới
                        'noiDungCauTraLoi' => $ans['noiDungCauTraLoi'],
                        'creator_id' => 1 // đơn giản hóa, nên là người dùng hiện tại
                    ]);
                }
            }
        }

        // Ghi log hoạt động
        try {
            // Lấy userId từ session nếu có
            $userId = $_SESSION['user_id'] ?? 0;
            if ($userId) {
                ActivityLogHelper::logQuestionCreated(
                    $userId,
                    $question->getId(),
                    $question->getNoiDungCauHoi()
                );
            }
        } catch (\Throwable $e) {
            error_log('[QuestionController::create] Failed to log activity: ' . $e->getMessage());
        }

        return $this->json([
            'error' => false,
            'message' => 'Câu hỏi được tạo thành công.',
            'data' => $question->toArray(),
        ], 201);
    }

    // cập nhật
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

        // Cập nhật liên kết khảo sát (nếu có key maKhaoSat trong payload)
        if (array_key_exists('maKhaoSat', $data)) {
            // Coi null, chuỗi rỗng, hoặc -1 là "hủy liên kết"
            $surveyId = $data['maKhaoSat'];
            
            // Luôn hủy tất cả liên kết hiện tại cho câu hỏi này (giả định quan hệ 1-nhiều từ phía câu hỏi cho UI này)
            SurveyQuestionMap::detachAllByQuestion($question->getId());

            // Nếu có ID khảo sát hợp lệ được chọn, gắn nó vào
            if ($surveyId && is_numeric($surveyId) && (int)$surveyId > 0) {
                 SurveyQuestionMap::attach((int)$surveyId, $question->getId());
            }
        }

        // Xử lý cập nhật đáp án (chiến lược thay thế)
        // Chỉ nếu có key 'answers' trong payload (ngay cả khi rỗng, nghĩa là "xóa tất cả")
        if (isset($data['answers']) && is_array($data['answers'])) {
            // Xóa các đáp án cũ
            Answer::deleteByQuestion($question->getId());
            
            // Thêm các đáp án mới
            foreach ($data['answers'] as $ans) {
                if (!empty($ans['noiDungCauTraLoi'])) {
                    Answer::create([
                        'idCauHoi' => $question->getId(),
                        'noiDungCauTraLoi' => $ans['noiDungCauTraLoi'],
                        'creator_id' => 1
                    ]);
                }
            }
        }

        // Tải lại
        $question = Question::find((int)$id);

        return $this->json([
            'error' => false,
            'message' => 'Cập nhật câu hỏi thành công.',
            'data' => $question->toArray(),
        ]);
    }

    // xoá
    public function delete(Request $request)
    {
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

        if (isset($data['maKhaoSat']) && $data['maKhaoSat'] !== '' && !is_numeric($data['maKhaoSat'])) {
            $errors['maKhaoSat'] = 'M? kh?o s?t ph?i l? s? khi cung c?p.';
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
