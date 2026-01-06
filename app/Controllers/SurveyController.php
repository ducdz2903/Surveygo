<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Survey;
use App\Models\Question;
use App\Models\SurveyQuestionMap;
use App\Models\User;
use App\Models\SurveySubmission;
use App\Models\UserResponse;
use App\Models\PointTransaction;
use App\Models\Answer;
use App\Helpers\ActivityLogHelper;

use PDO;

class SurveyController extends Controller
{

    // api phân trang
    public function index(Request $request)
    {
        $page = (int) ($request->query('page') ?? 1);
        $limit = (int) ($request->query('limit') ?? 10);
        $userId = $request->query('user_id') ? (int) $request->query('user_id') : null;

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

        if ($loaiKhaoSat = $request->query('loaiKhaoSat')) {
            $filters['loaiKhaoSat'] = $loaiKhaoSat;
        }

        if ($maSuKien = $request->query('maSuKien')) {
            $filters['maSuKien'] = (int) $maSuKien;
        }

        // Bộ lọc standalone: khảo sát không có sự kiện (maSuKien là null)
        $standaloneParam = $request->query('standalone');
        if ($standaloneParam !== null && $standaloneParam !== '') {
            if (filter_var($standaloneParam, FILTER_VALIDATE_BOOLEAN)) {
                $filters['standalone'] = true;
            }
        }

        // Chế độ xem client: hiển thị cả khảo sát approved+event VÀ published
        $clientViewParam = $request->query('clientView');
        if ($clientViewParam !== null && $clientViewParam !== '') {
            if (filter_var($clientViewParam, FILTER_VALIDATE_BOOLEAN)) {
                $filters['clientView'] = true;
            }
        }

        if ($sortBy = $request->query('sortBy')) {
            $filters['sortBy'] = $sortBy;
        }

        // Use loaiKhaoSat filter for quick polls instead of isQuickPoll
        $qpParam = $request->query('isQuickPoll');
        if ($qpParam !== null && $qpParam !== '') {
            // Ánh xạ isQuickPoll=true thành loaiKhaoSat='quick_poll'
            if (filter_var($qpParam, FILTER_VALIDATE_BOOLEAN)) {
                $filters['loaiKhaoSat'] = 'QuickPoll';
            }
        }

        // Thêm bộ lọc isCompleted nếu có user_id
        $isCompletedParam = $request->query('isCompleted');
        if ($isCompletedParam !== null && $isCompletedParam !== '' && $userId) {
            $filters['isCompleted'] = filter_var($isCompletedParam, FILTER_VALIDATE_BOOLEAN);
            $filters['user_id'] = $userId;
        }

        $result = Survey::paginate($page, $limit, $filters);
        // Nếu có user_id, kiểm tra từng survey xem user đã submit chưa
        $surveyData = array_map(fn($s) => $s->toArray(), $result['surveys']);

        // Lấy số lượng câu hỏi và số lượng phản hồi cho mỗi khảo sát
        try {
            $db = \App\Core\Container::get('db');
            foreach ($surveyData as &$survey) {
                // Lấy số lượng câu hỏi
                $qStmt = $db->prepare(
                    'SELECT COUNT(*) as count FROM survey_question_map WHERE idKhaoSat = :survey_id'
                );
                $qStmt->execute([':survey_id' => $survey['id']]);
                $qRow = $qStmt->fetch(PDO::FETCH_ASSOC);
                $survey['questionCount'] = (int) ($qRow['count'] ?? 0);

                // Lấy số lượng phản hồi (số lần nộp duy nhất)
                $rStmt = $db->prepare(
                    'SELECT COUNT(*) as count FROM survey_submissions WHERE maKhaoSat = :survey_id'
                );
                $rStmt->execute([':survey_id' => $survey['id']]);
                $rRow = $rStmt->fetch(PDO::FETCH_ASSOC);
                $survey['responseCount'] = (int) ($rRow['count'] ?? 0);

                // Kiểm tra nếu user đã hoàn thành (chỉ nếu có userId)
                if ($userId) {
                    $cStmt = $db->prepare(
                        'SELECT COUNT(*) as count FROM survey_submissions 
                         WHERE maKhaoSat = :survey_id AND maNguoiDung = :user_id'
                    );
                    $cStmt->execute([
                        ':survey_id' => $survey['id'],
                        ':user_id' => $userId
                    ]);
                    $cRow = $cStmt->fetch(PDO::FETCH_ASSOC);
                    $survey['isCompleted'] = ($cRow && $cRow['count'] > 0);
                } else {
                    $survey['isCompleted'] = false;
                }
            }
        } catch (\Throwable $e) {
            error_log('[SurveyController::index] Error fetching counts: ' . $e->getMessage());
            foreach ($surveyData as &$survey) {
                $survey['questionCount'] = 0;
                $survey['responseCount'] = 0;
                $survey['isCompleted'] = false;
            }
        }


        return $this->json([
            'error' => false,
            'data' => $surveyData,
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

        // Tải câu hỏi qua bảng mapping để đảm bảo thứ tự survey_question_map
        $questions = SurveyQuestionMap::findQuestionsBySurvey($survey->getId());
        $questionCount = SurveyQuestionMap::countBySurvey($survey->getId());
        $surveyData = $survey->toArray();
        $surveyData['questionCount'] = $questionCount;
        $surveyData['questions'] = array_map(fn($q) => $q->toArray(), $questions);

        return $this->json([
            'error' => false,
            'data' => $surveyData,
        ]);
    }

    /**
     * POST /api/surveys
     * Tạo khảo sát mới
     * Body:(maKhaoSat, tieuDe, moTa, loaiKhaoSat, thoiLuongDuTinh, maNguoiTao, trangThai, diemThuong, danhMuc, soLuongCauHoi, maSuKien, created_at, updated_at)
     */
    public function create(Request $request)
    {
        $data = $request->input();

        // Nếu phân tích request thất bại (rỗng), thử dùng raw JSON body làm phương án dự phòng
        if (empty($data)) {
            $raw = @file_get_contents('php://input');
            $json = $raw ? json_decode($raw, true) : null;
            if (is_array($json)) {
                $data = $json;
            } else {
                $data = [];
            }
        }

        // Chuẩn hóa các key đầu vào và cung cấp giá trị mặc định hợp lý để chấp nhận payload từ frontend
        $data['tieuDe'] = trim($data['tieuDe'] ?? $data['tieu_de'] ?? $data['title'] ?? $request->input('tieuDe') ?? '');
        $data['moTa'] = $data['moTa'] ?? $data['mo_ta'] ?? $data['description'] ?? $request->input('moTa') ?? null;
        $data['loaiKhaoSat'] = $data['loaiKhaoSat'] ?? $data['loai_khao_sat'] ?? $data['type'] ?? $request->input('loaiKhaoSat') ?? null;
        $data['thoiLuongDuTinh'] = isset($data['thoiLuongDuTinh']) ? (int) $data['thoiLuongDuTinh'] : (isset($data['thoi_luong']) ? (int) $data['thoi_luong'] : (int) ($request->input('thoiLuongDuTinh') ?? 0));

        $data['maNguoiTao'] = (int) (isset($data['maNguoiTao']) ? $data['maNguoiTao'] : ($request->input('maNguoiTao') ?? ($request->input('ma_nguoi_tao') ?? 1)));
        $data['trangThai'] = $data['trangThai'] ?? $data['trang_thai'] ?? $request->input('trangThai') ?? 'draft';
        $data['diemThuong'] = isset($data['diemThuong']) ? (int) $data['diemThuong'] : (int) ($data['points'] ?? $request->input('diemThuong') ?? 0);
        $data['danhMuc'] = $data['danhMuc'] ?? $data['danh_muc'] ?? $request->input('danhMuc') ?? null;
        $data['created_at'] = $data['created_at'] ?? (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        // Nếu frontend gửi maSuKien (event id), validate event tồn tại
        if (!empty($data['maSuKien'])) {
            try {
                $db = \App\Core\Container::get('db');
                $stmt = $db->prepare('SELECT id FROM events WHERE id = :id LIMIT 1');
                $stmt->execute([':id' => (int) $data['maSuKien']]);
                if (!$stmt->fetch()) {
                    return $this->json([
                        'error' => true,
                        'message' => 'Sự kiện được chọn không tồn tại.'
                    ], 422);
                }
            } catch (\Throwable $e) {
                // Nếu có lỗi DB, trả về lỗi hợp lý
                return $this->json([
                    'error' => true,
                    'message' => 'Lỗi khi kiểm tra sự kiện: ' . $e->getMessage(),
                ], 500);
            }
        }

        // Cung cấp giá trị mặc định an toàn để tránh lỗi validation khi frontend bỏ qua các trường
        if (empty($data['tieuDe'])) {
            $data['tieuDe'] = 'Khảo sát ' . date('YmdHis');
        }
        $data['maNguoiTao'] = isset($data['maNguoiTao']) && is_numeric($data['maNguoiTao']) ? (int) $data['maNguoiTao'] : 1;

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

        // Ghi log hoạt động
        try {
            ActivityLogHelper::logSurveyCreated(
                (int) $data['maNguoiTao'],
                $survey->getId(),
                $survey->getTieuDe()
            );
        } catch (\Throwable $e) {
            error_log('[SurveyController::create] Failed to log activity: ' . $e->getMessage());
        }

        return $this->json([
            'error' => false,
            'message' => 'Khảo sát được tạo thành công.',
            'data' => $survey->toArray(),
        ], 201);
    }

    /**
     * POST /api/surveys/quick-poll
     * Create a Quick Poll (Survey + Question + Answers) in one go
     */
    public function createQuickPoll(Request $request)
    {
        try {
            $data = $request->input();

            // 1. Kiểm tra dữ liệu cơ bản
            if (empty($data['title'])) {
                return $this->json(['error' => true, 'message' => 'Tiêu đề là bắt buộc.'], 422);
            }
            if (empty($data['questionType'])) {
                return $this->json(['error' => true, 'message' => 'Loại câu hỏi là bắt buộc.'], 422);
            }

            // 2. Chuẩn bị dữ liệu
            $userId = (int) ($data['maNguoiTao'] ?? 1);

            // Kiểm tra nếu user tồn tại, nếu không tìm user hợp lệ để tránh lỗi FK
            $user = User::findById($userId);
            if (!$user) {
                // Dự phòng: thử tìm user đầu tiên (thường là admin)
                $db = \App\Core\Container::get('db');
                $stmt = $db->query("SELECT id FROM users LIMIT 1");
                $fallbackUser = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($fallbackUser) {
                    $userId = (int) $fallbackUser['id'];
                } else {
                    return $this->json(['error' => true, 'message' => 'Không tìm thấy người dùng nào để gán quyền tạo.'], 500);
                }
            }

            $surveyData = [
                'tieuDe' => $data['title'],
                'moTa' => $data['description'] ?? '',
                'loaiKhaoSat' => 'quick_poll',

                'diemThuong' => (int) ($data['points'] ?? 0),
                'maNguoiTao' => $userId,
                'trangThai' => 'published',
                'thoiLuongDuTinh' => 1
            ];

            // 3. Wrapper thực thi

            // A. Tạo khảo sát
            $survey = Survey::create($surveyData);
            if (!$survey) {
                return $this->json(['error' => true, 'message' => 'Không thể tạo khảo sát (DB Error).'], 500);
            }

            // B. Tạo câu hỏi
            $questionData = [
                'noiDungCauHoi' => $data['title'],
                'loaiCauHoi' => $data['questionType'],

                'batBuocTraLoi' => 1,
                'maKhaoSat' => $survey->getId()
            ];

            // Ánh xạ loại câu hỏi từ frontend sang backend
            $typeMap = [
                'single' => 'single_choice',
                'multiple' => 'multiple_choice',
                'text' => 'text',
                'rating' => 'rating',
                'yesno' => 'yes_no'
            ];
            $questionData['loaiCauHoi'] = $typeMap[$data['questionType']] ?? $data['questionType'];

            $question = Question::create($questionData);
            if (!$question) {
                $survey->delete();
                return $this->json(['error' => true, 'message' => 'Không thể tạo câu hỏi.'], 500);
            }

            // C. Liên kết khảo sát & câu hỏi
            $this->attachQuestionToSurvey($survey->getId(), $question->getId());

            // D. Tạo các đáp án
            if (!empty($data['options']) && is_array($data['options'])) {
                foreach ($data['options'] as $optionText) {
                    if (trim($optionText) === '')
                        continue;
                    Answer::create([
                        'idCauHoi' => $question->getId(),
                        'noiDungCauTraLoi' => trim($optionText),
                        'creator_id' => $userId
                    ]);
                }
            } elseif ($data['questionType'] === 'yesno') {
                Answer::create(['idCauHoi' => $question->getId(), 'noiDungCauTraLoi' => 'Có', 'creator_id' => $userId]);
                Answer::create(['idCauHoi' => $question->getId(), 'noiDungCauTraLoi' => 'Không', 'creator_id' => $userId]);
            }

            return $this->json([
                'error' => false,
                'message' => 'Quick Poll created successfully',
                'data' => [
                    'survey' => $survey->toArray(),
                    'question' => $question->toArray()
                ]
            ], 201);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => true,
                'message' => 'Exception: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
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

        // Tải lại
        $survey = Survey::find((int) $id);

        return $this->json([
            'error' => false,
            'message' => 'Khảo sát được cập nhật thành công.',
            'data' => $survey->toArray(),
        ]);
    }

    // xóa
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

    // công bố
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

        // Tải lại
        $survey = Survey::find((int) $id);

        return $this->json([
            'error' => false,
            'message' => 'Khảo sát được công bố thành công.',
            'data' => $survey->toArray(),
        ]);
    }

    // phê duyệt
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

        // Tải lại
        $survey = Survey::find((int) $id);

        return $this->json([
            'error' => false,
            'message' => 'Khảo sát được phê duyệt thành công.',
            'data' => $survey->toArray(),
        ]);
    }

    // thêm mới
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
                'message' => 'Không thể tạo câu hỏi. Kiểm tra dữ liệu hoặc survey không tồn tại.',
            ], 422);
        }

        // lưu mapping nhiều-nhiều (nếu được truyền maKhaoSat)
        if (!empty($data['maKhaoSat'])) {
            $this->attachQuestionToSurvey((int) $data['maKhaoSat'], $question->getId());
        }

        return $this->json([
            'error' => false,
            'message' => 'C?u h?i ?? ???c th?m th?nh c?ng.',
            'data' => $question->toArray(),
        ], 201);
    }

    /**
     * Gắn câu hỏi vào khảo sát (chỉ tạo mapping, không tạo câu hỏi mới)
     */
    public function attachQuestion(Request $request)
    {
        $surveyId = $request->input('maKhaoSat');
        $questionId = $request->input('maCauHoi');

        if (!$surveyId || !is_numeric($surveyId) || !$questionId || !is_numeric($questionId)) {
            return $this->json([
                'error' => true,
                'message' => 'maKhaoSat và maCauHoi là bắt buộc và phải là số.',
            ], 422);
        }

        $survey = Survey::find((int) $surveyId);
        $question = Question::find((int) $questionId);

        if (!$survey || !$question) {
            return $this->json([
                'error' => true,
                'message' => 'Không tồn tại khảo sát hoặc câu hỏi.',
            ], 404);
        }

        if (!SurveyQuestionMap::attach((int) $surveyId, (int) $questionId)) {
            return $this->json([
                'error' => true,
                'message' => 'Không thể gắn câu hỏi vào khảo sát (có thể đã tồn tại).',
            ], 422);
        }

        return $this->json([
            'error' => false,
            'message' => 'Đã gắn câu hỏi vào khảo sát.',
        ], 201);
    }

    /**
     * Gỡ câu hỏi khỏi khảo sát (chỉ xóa mapping, không xóa câu hỏi)
     */
    public function detachQuestion(Request $request)
    {
        $surveyId = $request->input('maKhaoSat');
        $questionId = $request->input('maCauHoi');

        if (!$surveyId || !is_numeric($surveyId) || !$questionId || !is_numeric($questionId)) {
            return $this->json([
                'error' => true,
                'message' => 'maKhaoSat và maCauHoi là bắt buộc và phải là số.',
            ], 422);
        }

        if (!SurveyQuestionMap::detach((int) $surveyId, (int) $questionId)) {
            return $this->json([
                'error' => true,
                'message' => 'Không thể gỡ câu hỏi khỏi khảo sát (có thể không tồn tại).',
            ], 422);
        }
        return $this->json([
            'error' => false,
            'message' => 'Đã gỡ câu hỏi khỏi khảo sát.',
        ], 200);
    }

    private function attachQuestionToSurvey(int $surveyId, int $questionId): bool
    {
        try {
            /** @var PDO $db */
            $db = \App\Core\Container::get('db');
            $stmt = $db->prepare('INSERT INTO survey_question_map (idKhaoSat, idCauHoi) VALUES (:survey, :question) ON DUPLICATE KEY UPDATE idKhaoSat = VALUES(idKhaoSat)');
            return $stmt->execute([
                ':survey' => $surveyId,
                ':question' => $questionId,
            ]);
        } catch (\Throwable $e) {
            return false;
        }
    }

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

        // Tải lại
        $question = Question::find((int) $id);

        return $this->json([
            'error' => false,
            'message' => 'Câu hỏi được cập nhật thành công.',
            'data' => $question->toArray(),
        ]);
    }

    // xóa
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

        // Validate trangThai if provided (only value validation; authorization is handled client-side per request)
        if (isset($data['trangThai'])) {
            $allowed = ['draft', 'pending', 'published', 'rejected', 'approved'];
            if (!in_array($data['trangThai'], $allowed, true)) {
                $errors['trangThai'] = 'trangThai không hợp lệ.';
            }
        }

        return $errors;
    }

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

    private function isValidDateTime(string $dateTime): bool
    {
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        return $date && $date->format('Y-m-d H:i:s') === $dateTime;
    }

    // nộp khảo sát
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
            $questions = SurveyQuestionMap::findQuestionsBySurvey($surveyId);
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

            try {
                $points = (int) $survey->getDiemThuong();
                if ($points > 0) {
                    PointTransaction::addPoints(
                        $userId,
                        $points,
                        'survey',
                        $submission->getId(),
                        'Hoàn thành khảo sát ' . $survey->getMaKhaoSat()
                    );
                }
            } catch (\Throwable $e) {
                error_log('[SurveyController::submit] Failed to add points: ' . $e->getMessage());
            }

            // Log activity
            try {
                ActivityLogHelper::logSurveySubmitted($userId, $surveyId);
            } catch (\Throwable $e) {
                error_log('[SurveyController::submit] Failed to log activity: ' . $e->getMessage());
            }

            return $this->json([
                'error' => false,
                'message' => 'Survey submitted successfully.',
                'data' => [
                    'submissionId' => $submission->getId(),
                ],
            ], 201);

        } catch (\Exception $e) {
            // Check if it's a duplicate entry error
            if (
                strpos($e->getMessage(), 'Duplicate entry') !== false ||
                strpos($e->getMessage(), '1062') !== false
            ) {
                return $this->json([
                    'error' => true,
                    'message' => 'Bạn đã làm khảo sát này rồi. Mỗi người chỉ được làm một lần.',
                ], 409);
            }

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

    /**
     * GET /api/surveys/hourly-stats
     * Lấy thống kê số lượng khảo sát hoàn thành theo khoảng thời gian 3 tiếng trong 24 giờ qua
     */
    public function getHourlyStats(Request $request)
    {
        $userId = (int) $request->query('user_id');

        if (!$userId) {
            return $this->json([
                'error' => true,
                'message' => 'User ID is required'
            ], 401);
        }

        try {
            // Lấy tất cả survey submissions trong 24 giờ qua
            $db = \App\Core\Container::get('db');

            $statement = $db->prepare(
                'SELECT created_at 
                 FROM survey_submissions 
                 WHERE maNguoiDung = :user_id 
                   AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                 ORDER BY created_at ASC'
            );

            $statement->execute([':user_id' => $userId]);
            $submissions = $statement->fetchAll();

            // Khởi tạo mảng 8 khoảng thời gian (mỗi khoảng 3 tiếng)
            $hourlyData = array_fill(0, 8, 0);

            // Đếm số lượng submissions theo khoảng thời gian
            foreach ($submissions as $submission) {
                $timestamp = strtotime($submission['created_at']);
                $hour = (int) date('H', $timestamp);

                // Xác định khoảng thời gian (0-3h = index 0, 3-6h = index 1, ...)
                $intervalIndex = (int) floor($hour / 3);
                $hourlyData[$intervalIndex]++;
            }

            return $this->json([
                'success' => true,
                'data' => $hourlyData
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * GET /api/surveys/question-responses
     * Lấy danh sách câu trả lời cho một câu hỏi kèm thông tin user
     */
    public function getQuestionResponses(Request $request)
    {
        $questionId = (int) ($request->query('questionId') ?? 0);
        $surveyId = (int) ($request->query('surveyId') ?? 0);

        if (!$questionId) {
            return $this->json([
                'error' => true,
                'message' => 'questionId là bắt buộc.',
            ], 422);
        }

        // Lấy thông tin câu hỏi
        $question = Question::find($questionId);
        if (!$question) {
            return $this->json([
                'error' => true,
                'message' => 'Câu hỏi không tồn tại.',
            ], 404);
        }

        // Lấy các options của câu hỏi (nếu là choice question)
        $answers = Answer::findByQuestion($questionId);

        // Lấy danh sách responses kèm thông tin user
        $responses = UserResponse::findByQuestionWithUser($questionId);

        // Lấy thông tin survey nếu có surveyId
        $surveyData = null;
        if ($surveyId) {
            $survey = Survey::find($surveyId);
            if ($survey) {
                $surveyData = [
                    'id' => $survey->getId(),
                    'tieuDe' => $survey->getTieuDe(),
                    'maKhaoSat' => $survey->getMaKhaoSat(),
                ];
            }
        }

        return $this->json([
            'error' => false,
            'data' => [
                'question' => $question->toArray(),
                'answers' => array_map(fn($a) => $a->toArray(), $answers),
                'responses' => $responses,
                'survey' => $surveyData,
                'totalResponses' => count($responses),
            ],
        ]);
    }
}
