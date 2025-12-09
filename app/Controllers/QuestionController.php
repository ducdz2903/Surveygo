<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Container;
use App\Models\Question;
use App\Models\Answer;
use App\Models\SurveyQuestionMap;
use PDO;

class QuestionController extends Controller
{
    // api lấy all
    public function index(Request $request)
    {
        // Support pagination and filters
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

        // Include answers for preview
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

        // Link to Survey if maKhaoSat is provided
        if (!empty($data['maKhaoSat']) && is_numeric($data['maKhaoSat'])) {
            SurveyQuestionMap::attach((int)$data['maKhaoSat'], $question->getId());
        }

        // Handle Answers (if any)
        if (!empty($data['answers']) && is_array($data['answers'])) {
            foreach ($data['answers'] as $ans) {
                if (!empty($ans['noiDungCauTraLoi'])) {
                    Answer::create([
                        'idCauHoi' => $question->getId(), // Use new ID
                        'noiDungCauTraLoi' => $ans['noiDungCauTraLoi'],
                        'creator_id' => 1 // simplified, should be current user
                    ]);
                }
            }
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

        // Update Survey Link (if maKhaoSat key exists in payload)
        if (array_key_exists('maKhaoSat', $data)) {
            // Treat null, empty string, or -1 as "unlink"
            $surveyId = $data['maKhaoSat'];
            
            // Always detach all current links for this question (assuming 1-to-many from Question side for this UI)
            SurveyQuestionMap::detachAllByQuestion($question->getId());

            // If a valid survey ID is selected, attach it
            if ($surveyId && is_numeric($surveyId) && (int)$surveyId > 0) {
                 SurveyQuestionMap::attach((int)$surveyId, $question->getId());
            }
        }

        // Handle Answers Update (Replace Strategy)
        // Only if 'answers' key exists in payload (even if empty, meaning "remove all")
        if (isset($data['answers']) && is_array($data['answers'])) {
            // Delete old answers
            Answer::deleteByQuestion($question->getId());
            
            // Add new answers
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

        // Reload
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
