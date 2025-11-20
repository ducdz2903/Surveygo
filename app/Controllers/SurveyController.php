<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Container;
use App\Models\Survey;
use App\Models\Question;
use App\Models\User;
use App\Models\SurveySubmission;
use App\Models\UserResponse;
use PDO;

class SurveyController extends Controller
{
    /**
     * GET /api/surveys
     * Lấy danh sách khảo sát với phân trang và lọc
     * 
     * Query params:
     * - page: int (default: 1)
     * - limit: int (default: 10, max: 100)
     * - search: string (tìm kiếm trong tiêu đề và mô tả)
     * - trangThai: string (lọc theo trạng thái: hoạtĐộng, draft, published, etc.)
     * - danhMuc: int (lọc theo danh mục ID)
     * - quickPoll: bool (nếu true, chỉ lấy surveys có 1 câu hỏi)
     * 
     * Ví dụ: GET /api/surveys?page=1&limit=6&search=sức khỏe&trangThai=hoạtĐộng
     *        GET /api/surveys?page=1&limit=6&quickPoll=true
     */
    public function index(Request $request)
    {
        $page = (int) ($request->query('page') ?? 1);
        $limit = (int) ($request->query('limit') ?? 10);

        $filters = [];

        if ($search = $request->query('search')) {
            $filters['search'] = $search;
        }

        if ($trangThai = $request->query('trangThai')) {
            $filters['trangThai'] = $trangThai;
        }

        if ($danhMuc = $request->query('danhMuc')) {
            $filters['danhMuc'] = $danhMuc;
        }

        if ($request->query('quickPoll')) {
            $filters['quickPoll'] = true;
        }

        $result = Survey::paginate($page, $limit, $filters);

        return $this->json([
            'error' => false,
            'data' => array_map(fn($s) => $s->toArray(), $result['surveys']),
            'meta' => [
                'total' => $result['total'],
                'page' => $result['page'],
                'limit' => $result['limit'],
                'totalPages' => $result['totalPages'],
            ],
        ]);
    }

    /**
     * GET /api/surveys?id=:id
     * Lấy chi tiết khảo sát (kèm danh sách câu hỏi)
     */
    public function show(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');

        if (!$id || !is_numeric($id)) {
            return $this->json([
                'error' => true,
                'message' => 'ID khảo sát không hợp lệ.',
            ], 422);
        }

        $survey = Survey::find((int) $id);
        if (!$survey) {
            return $this->json([
                'error' => true,
                'message' => 'Khảo sát không tồn tại.',
            ], 404);
        }

        $questions = Question::findBySurvey($survey->getId());

        $surveyData = $survey->toArray();
        $surveyData['questions'] = array_map(fn($q) => $q->toArray(), $questions);

        return $this->json([
            'error' => false,
            'data' => $surveyData,
        ]);
    }

    /**
     * POST /api/surveys
     * Tạo khảo sát mới
     * Body: { tieuDe, moTa?, loaiKhaoSat?, thoiGianBatDau?, thoiGianKetThuc?, maNguoiTao, diemThuong?, danhMuc?, maSuKien?, soNguoiThamGia? }
     */
    public function create(Request $request)
    {
        $data = $request->input();

        // Validation
        $errors = $this->validateSurveyCreate($data);
        if (!empty($errors)) {
            return $this->json([
                'error' => true,
                'message' => 'Validation failed',
                'errors' => $errors,
            ], 422);
        }

        $survey = Survey::create($data);
        if (!$survey) {
            return $this->json([
                'error' => true,
                'message' => 'Không thể tạo khảo sát. Kiểm tra dữ liệu hoặc user có tồn tại.',
            ], 422);
        }

        return $this->json([
            'error' => false,
            'message' => 'Khảo sát được tạo thành công.',
            'data' => $survey->toArray(),
        ], 201);
    }

    /**
     * PUT /api/surveys?id=:id (hoặc body.id)
     * Cập nhật khảo sát
     */
    public function update(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');

        if (!$id || !is_numeric($id)) {
            return $this->json([
                'error' => true,
                'message' => 'ID khảo sát không hợp lệ.',
            ], 422);
        }

        $survey = Survey::find((int) $id);
        if (!$survey) {
            return $this->json([
                'error' => true,
                'message' => 'Khảo sát không tồn tại.',
            ], 404);
        }

        $data = $request->input();
        $errors = $this->validateSurveyUpdate($data);
        if (!empty($errors)) {
            return $this->json([
                'error' => true,
                'message' => 'Validation failed',
                'errors' => $errors,
            ], 422);
        }

        if (!$survey->update($data)) {
            return $this->json([
                'error' => true,
                'message' => 'Không thể cập nhật khảo sát.',
            ], 500);
        }

        // Reload
        $survey = Survey::find((int) $id);

        return $this->json([
            'error' => false,
            'message' => 'Khảo sát được cập nhật thành công.',
            'data' => $survey->toArray(),
        ]);
    }

    /**
     * DELETE /api/surveys?id=:id
     * Xóa khảo sát
     */
    public function delete(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');

        if (!$id || !is_numeric($id)) {
            return $this->json([
                'error' => true,
                'message' => 'ID khảo sát không hợp lệ.',
            ], 422);
        }

        $survey = Survey::find((int) $id);
        if (!$survey) {
            return $this->json([
                'error' => true,
                'message' => 'Khảo sát không tồn tại.',
            ], 404);
        }

        if (!$survey->delete()) {
            return $this->json([
                'error' => true,
                'message' => 'Không thể xóa khảo sát.',
            ], 500);
        }

        return $this->json([
            'error' => false,
            'message' => 'Khảo sát được xóa thành công.',
        ]);
    }

    /**
     * POST /api/surveys?id=:id/publish
     * Công bố khảo sát (chuyển từ draft → published)
     */
    public function publish(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');

        if (!$id || !is_numeric($id)) {
            return $this->json([
                'error' => true,
                'message' => 'ID khảo sát không hợp lệ.',
            ], 422);
        }

        $survey = Survey::find((int) $id);
        if (!$survey) {
            return $this->json([
                'error' => true,
                'message' => 'Khảo sát không tồn tại.',
            ], 404);
        }

        // Validate: khảo sát phải ở trạng thái draft hoặc closed
        if ($survey->getTrangThai() !== 'draft' && $survey->getTrangThai() !== 'closed') {
            return $this->json([
                'error' => true,
                'message' => 'Chỉ có thể công bố khảo sát ở trạng thái draft hoặc closed.',
            ], 422);
        }


        if (!$survey->updateStatus('published')) {
            return $this->json([
                'error' => true,
                'message' => 'Không thể công bố khảo sát.',
            ], 500);
        }

        // Reload
        $survey = Survey::find((int) $id);

        return $this->json([
            'error' => false,
            'message' => 'Khảo sát được công bố thành công.',
            'data' => $survey->toArray(),
        ]);
    }

    /**
     * POST /api/surveys?id=:id/approve
     * Phê duyệt khảo sát (trangThaiKiemDuyet: pending → approved)
     * Chỉ admin/mod mới được gọi
     */
    public function approve(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');

        if (!$id || !is_numeric($id)) {
            return $this->json([
                'error' => true,
                'message' => 'ID khảo sát không hợp lệ.',
            ], 422);
        }

        $survey = Survey::find((int) $id);
        if (!$survey) {
            return $this->json([
                'error' => true,
                'message' => 'Khảo sát không tồn tại.',
            ], 404);
        }

        $status = $request->input('status') ?? 'approved'; // approved hoặc rejected
        if (!in_array($status, ['approved', 'rejected'])) {
            return $this->json([
                'error' => true,
                'message' => 'Status không hợp lệ. Chỉ "approved" hoặc "rejected".',
            ], 422);
        }

        if (!$survey->updateVerificationStatus($status)) {
            return $this->json([
                'error' => true,
                'message' => 'Không thể phê duyệt khảo sát.',
            ], 500);
        }

        // Reload
        $survey = Survey::find((int) $id);

        return $this->json([
            'error' => false,
            'message' => 'Khảo sát được phê duyệt thành công.',
            'data' => $survey->toArray(),
        ]);
    }

    /**
     * POST /api/questions
     * Thêm câu hỏi vào khảo sát
     * Body: { maKhaoSat, loaiCauHoi, noiDungCauHoi, batBuocTraLoi?, thuTu?, maCauHoi? }
     */
    public function addQuestion(Request $request)
    {
        $data = $request->input();

        // Validation
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
                'message' => 'Không thể tạo câu hỏi. Kiểm tra dữ liệu hoặc survey có tồn tại.',
            ], 422);
        }

        return $this->json([
            'error' => false,
            'message' => 'Câu hỏi được thêm thành công.',
            'data' => $question->toArray(),
        ], 201);
    }

    /**
     * PUT /api/questions?id=:id
     * Cập nhật câu hỏi
     */
    public function updateQuestion(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');

        if (!$id || !is_numeric($id)) {
            return $this->json([
                'error' => true,
                'message' => 'ID câu hỏi không hợp lệ.',
            ], 422);
        }

        $question = Question::find((int) $id);
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
        $question = Question::find((int) $id);

        return $this->json([
            'error' => false,
            'message' => 'Câu hỏi được cập nhật thành công.',
            'data' => $question->toArray(),
        ]);
    }

    /**
     * DELETE /api/questions?id=:id
     * Xóa câu hỏi
     */
    public function deleteQuestion(Request $request)
    {
        $id = $request->query('id') ?? $request->input('id');

        if (!$id || !is_numeric($id)) {
            return $this->json([
                'error' => true,
                'message' => 'ID câu hỏi không hợp lệ.',
            ], 422);
        }

        $question = Question::find((int) $id);
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

    private function validateSurveyCreate(array $data): array
    {
        $errors = [];

        if (empty($data['tieuDe'])) {
            $errors['tieuDe'] = 'Tiêu đề khảo sát là bắt buộc.';
        }

        if (empty($data['maNguoiTao']) || !is_numeric($data['maNguoiTao'])) {
            $errors['maNguoiTao'] = 'Mã người tạo là bắt buộc và phải là số.';
        }

        if (!empty($data['thoiGianBatDau']) && !$this->isValidDateTime($data['thoiGianBatDau'])) {
            $errors['thoiGianBatDau'] = 'Thời gian bắt đầu không hợp lệ (định dạng: YYYY-MM-DD HH:MM:SS).';
        }

        if (!empty($data['thoiGianKetThuc']) && !$this->isValidDateTime($data['thoiGianKetThuc'])) {
            $errors['thoiGianKetThuc'] = 'Thời gian kết thúc không hợp lệ (định dạng: YYYY-MM-DD HH:MM:SS).';
        }

        return $errors;
    }

    private function validateSurveyUpdate(array $data): array
    {
        $errors = [];

        if (isset($data['tieuDe']) && empty($data['tieuDe'])) {
            $errors['tieuDe'] = 'Tiêu đề không được để trống.';
        }

        if (!empty($data['thoiGianBatDau']) && !$this->isValidDateTime($data['thoiGianBatDau'])) {
            $errors['thoiGianBatDau'] = 'Thời gian bắt đầu không hợp lệ.';
        }

        if (!empty($data['thoiGianKetThuc']) && !$this->isValidDateTime($data['thoiGianKetThuc'])) {
            $errors['thoiGianKetThuc'] = 'Thời gian kết thúc không hợp lệ.';
        }

        return $errors;
    }

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

    private function isValidDateTime(string $dateTime): bool
    {
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        return $date && $date->format('Y-m-d H:i:s') === $dateTime;
    }

    /**
     * POST /api/surveys/{id}/submit
     * Nộp bài khảo sát - tạo survey submission + user responses
     * 
     * Request body:
     * {
     *   "userId": 1,
     *   "answers": {
     *     "1": "Buổi sáng",
     *     "2": "5",
     *     "3": "[\"7\", \"8\"]"
     *   }
     * }
     */
    public function submit(Request $request)
    {
        // Get survey ID from route parameter
        $surveyId = (int) $request->getAttribute('id');
        $userId = (int) $request->input('userId');
        $answers = $request->input('answers') ?? [];

        // Validate inputs
        if (!$surveyId || !$userId || !is_array($answers)) {
            return $this->json([
                'error' => true,
                'message' => 'Invalid survey ID, user ID, or answers format.',
            ], 422);
        }

        // Check if survey exists
        $survey = Survey::find($surveyId);
        if (!$survey) {
            return $this->json([
                'error' => true,
                'message' => 'Survey not found.',
            ], 404);
        }

        // Check if user exists
        $user = User::findById($userId);
        if (!$user) {
            return $this->json([
                'error' => true,
                'message' => 'User not found.',
            ], 404);
        }

        // Check if user already submitted this survey
        $existingSubmission = SurveySubmission::findBySurveyAndUser($surveyId, $userId);
        if ($existingSubmission) {
            return $this->json([
                'error' => true,
                'message' => 'User already submitted this survey.',
            ], 409);
        }

        try {
            // Create survey submission record
            $submission = SurveySubmission::create([
                'maKhaoSat' => $surveyId,
                'maNguoiDung' => $userId,
                'trangThai' => 'submitted',
                'diemDat' => 0,
                'ghiChu' => 'Submitted at ' . date('Y-m-d H:i:s'),
            ]);

            if (!$submission) {
                return $this->json([
                    'error' => true,
                    'message' => 'Failed to create submission record.',
                ], 500);
            }

            // Get all questions for this survey to validate
            $questions = Question::findBySurvey($surveyId);
            $questionIds = array_map(fn($q) => $q->getId(), $questions);

            // Create user response for each answered question
            foreach ($answers as $questionId => $answer) {
                $questionId = (int) $questionId;

                // Validate question belongs to this survey
                if (!in_array($questionId, $questionIds)) {
                    continue; // Skip invalid question IDs
                }

                // Create user response
                UserResponse::create([
                    'maCauHoi' => $questionId,
                    'maNguoiDung' => $userId,
                    'maKhaoSat' => $surveyId,
                    'noiDungTraLoi' => $answer, // Already formatted as JSON string from frontend
                ]);
            }

            return $this->json([
                'error' => false,
                'message' => 'Survey submitted successfully.',
                'data' => [
                    'submissionId' => $submission->getId(),
                ],
            ], 201);

        } catch (\Exception $e) {
            return $this->json([
                'error' => true,
                'message' => 'Error submitting survey: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/surveys/{id}/check-submission
     * Kiểm tra xem user đã submit khảo sát này chưa
     */
    public function checkSubmission(Request $request)
    {
        $surveyId = (int) $request->getAttribute('id');
        $userId = (int) $request->query('userId');

        if (!$surveyId || !$userId) {
            return $this->json([
                'error' => true,
                'message' => 'Survey ID and User ID are required.',
            ], 422);
        }

        // Check if survey exists
        $survey = Survey::find($surveyId);
        if (!$survey) {
            return $this->json([
                'error' => true,
                'message' => 'Survey not found.',
            ], 404);
        }

        // Check if user already submitted this survey
        $submission = SurveySubmission::findBySurveyAndUser($surveyId, $userId);

        return $this->json([
            'error' => false,
            'data' => [
                'hasSubmitted' => (bool) $submission,
                'submission' => $submission ? $submission->toArray() : null,
            ],
        ]);
    }
}