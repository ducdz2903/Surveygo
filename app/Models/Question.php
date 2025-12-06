<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class Question
{
    private int $id;
    private string $maCauHoi;
    private int $maKhaoSat;
    private string $loaiCauHoi;
    private string $noiDungCauHoi;
    private bool $batBuocTraLoi;
    private int $thuTu;
    private bool $isQuickPoll;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $attributes)
    {
        $this->id = (int)($attributes['id'] ?? 0);
        $this->maCauHoi = $attributes['maCauHoi'] ?? '';
        // legacy support: maKhaoSat may be provided by older code, but questions table no longer stores survey id
        $this->maKhaoSat = isset($attributes['maKhaoSat']) ? (int)$attributes['maKhaoSat'] : 0;
        $this->loaiCauHoi = $attributes['loaiCauHoi'] ?? '';
        $this->noiDungCauHoi = $attributes['noiDungCauHoi'] ?? '';
        $this->batBuocTraLoi = (bool)($attributes['batBuocTraLoi'] ?? false);
        $this->thuTu = isset($attributes['thuTu']) ? (int)$attributes['thuTu'] : 0;
        // quick_poll in DB (snake_case) or isQuickPoll in code may appear from different layers
        $this->isQuickPoll = (bool)($attributes['quick_poll'] ?? $attributes['isQuickPoll'] ?? false);
        $this->createdAt = $attributes['created_at'] ?? '';
        $this->updatedAt = $attributes['updated_at'] ?? '';
    }

    public static function paginate(int $page = 1, int $perPage = 10, array $filters = []): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $offset = max(0, ($page - 1)) * $perPage;

        $params = [];

        // If filtering by survey, use the map table join (survey_question_map uses idKhaoSat/idCauHoi)
        if (!empty($filters['maKhaoSat'])) {
            $surveyId = (int)$filters['maKhaoSat'];

            // total count via map
            $countSql = "SELECT COUNT(*) as cnt FROM survey_question_map sqm WHERE sqm.idKhaoSat = :surveyId";
            $countStmt = $db->prepare($countSql);
            $countStmt->execute([':surveyId' => $surveyId]);
            $total = (int)$countStmt->fetchColumn();

            $sql = "SELECT q.* FROM survey_question_map sqm JOIN questions q ON q.id = sqm.idCauHoi
                    WHERE sqm.idKhaoSat = :surveyId
                    ORDER BY q.id ASC
                    LIMIT :limit OFFSET :offset";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':surveyId', $surveyId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();
        } else {
            $wheres = [];
            if (!empty($filters['search'])) {
                $wheres[] = 'noiDungCauHoi LIKE :search';
                $params[':search'] = '%' . $filters['search'] . '%';
            }

            if (!empty($filters['loaiCauHoi'])) {
                $wheres[] = 'loaiCauHoi = :loai';
                $params[':loai'] = $filters['loaiCauHoi'];
            }

            $whereSql = '';
            if (!empty($wheres)) {
                $whereSql = 'WHERE ' . implode(' AND ', $wheres);
            }

            // total count
            $countSql = "SELECT COUNT(*) as cnt FROM questions $whereSql";
            $countStmt = $db->prepare($countSql);
            $countStmt->execute($params);
            $total = (int)$countStmt->fetchColumn();

            // fetch paginated rows
            $sql = "SELECT * FROM questions $whereSql ORDER BY id ASC LIMIT :limit OFFSET :offset";
            $stmt = $db->prepare($sql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v);
            }
            $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();
        }

        $data = array_map(fn($row) => new self($row), $rows);

        $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;

        return [
            'data' => $data,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => $totalPages,
            ],
        ];
    }

    /**
     * Lấy câu hỏi theo ID
     */
    public static function find(int $id): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('SELECT * FROM questions WHERE id = :id LIMIT 1');
        $statement->execute([':id' => $id]);
        $row = $statement->fetch();

        return $row ? new self($row) : null;
    } 
    /**
     * Tạo câu hỏi mới
     * - Auto-gen maCauHoi nếu không cung cấp
     * - Validate maKhaoSat phải tồn tại
     * - Auto-update soLuongCauHoi của survey
     */
    public static function create(array $data): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        // Check required 
        if (empty($data['noiDungCauHoi']) || empty($data['loaiCauHoi'])) {
            return null;
        }

        // Kiểm tra khóa ngoại maKhaoSat nếu cung cấp (chỉ để đảm bảo survey tồn tại khi frontend gửi mapping)
        if (!empty($data['maKhaoSat'])) {
            $surveyStmt = $db->prepare('SELECT id FROM surveys WHERE id = :id LIMIT 1');
            $surveyStmt->execute([':id' => $data['maKhaoSat']]);
            if (!$surveyStmt->fetch()) {
                return null; // Survey không tồn tại
            }
        }

        // Auto-gen maCauHoi nếu chưa có
        $maCauHoi = $data['maCauHoi'] ?? 'CH-' . time() . '-' . random_int(1000, 9999);

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        try {
            // questions table in schema stores quick_poll and does not contain survey id or thuTu
            $statement = $db->prepare(
                'INSERT INTO questions (maCauHoi, loaiCauHoi, noiDungCauHoi, batBuocTraLoi, quick_poll, created_at, updated_at)
                VALUES (:ma, :loai, :noidung, :batbuoc, :quickpoll, :created, :updated)'
            );

            $statement->execute([
                ':ma' => $maCauHoi,
                ':loai' => $data['loaiCauHoi'],
                ':noidung' => $data['noiDungCauHoi'],
                ':batbuoc' => (int)($data['batBuocTraLoi'] ?? false),
                ':quickpoll' => (int)($data['isQuickPoll'] ?? $data['quick_poll'] ?? false),
                ':created' => $now,
                ':updated' => $now,
            ]);

            $id = (int)$db->lastInsertId();
            return self::find($id);
        } catch (\PDOException $e) {
            return null; // Duplicate maCauHoi hoặc lỗi DB
        }
    }

    /**
     * Cập nhật câu hỏi
     */
    public function update(array $data): bool
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $loaiCauHoi = $data['loaiCauHoi'] ?? $this->loaiCauHoi;
        $noiDungCauHoi = $data['noiDungCauHoi'] ?? $this->noiDungCauHoi;
        $batBuocTraLoi = $data['batBuocTraLoi'] ?? $this->batBuocTraLoi;
        $isQuickPoll = isset($data['isQuickPoll']) ? (bool)$data['isQuickPoll'] : (isset($data['quick_poll']) ? (bool)$data['quick_poll'] : $this->isQuickPoll);
        $thuTu = $data['thuTu'] ?? $this->thuTu;

        $statement = $db->prepare(
            'UPDATE questions SET loaiCauHoi = :loai, noiDungCauHoi = :noidung, batBuocTraLoi = :batbuoc, quick_poll = :quickpoll, updated_at = :updated WHERE id = :id'
        );

        return $statement->execute([
            ':loai' => $loaiCauHoi,
            ':noidung' => $noiDungCauHoi,
            ':batbuoc' => (int)$batBuocTraLoi,
            ':quickpoll' => $isQuickPoll ? 1 : 0,
            ':updated' => $now,
            ':id' => $this->id,
        ]);
    }

    /**
     * Xóa câu hỏi
     * - Auto-update soLuongCauHoi của survey
     */
    public function delete(): bool
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('DELETE FROM questions WHERE id = :id');
        $result = $statement->execute([':id' => $this->id]);
        return $result;
    }

    /**
     * Lấy danh sách đáp án cho câu hỏi này
     */
    public function getAnswers(): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        // answers table uses idCauHoi as FK to questions.id
        $statement = $db->prepare('SELECT * FROM answers WHERE idCauHoi = :questionId ORDER BY id ASC');
        $statement->execute([':questionId' => $this->id]);
        $rows = $statement->fetchAll();

        return array_map(fn($row) => [
            'id' => (int)$row['id'],
            'idCauHoi' => (int)$row['idCauHoi'],
            'noiDungCauTraLoi' => $row['noiDungCauTraLoi'],
            'creator_id' => isset($row['creator_id']) ? (int)$row['creator_id'] : 0,
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ], $rows);
    }

    /**
     * Chuyển đổi thành array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'maCauHoi' => $this->maCauHoi,
            'maKhaoSat' => $this->maKhaoSat,
            'loaiCauHoi' => $this->loaiCauHoi,
            'noiDungCauHoi' => $this->noiDungCauHoi,
            'batBuocTraLoi' => $this->batBuocTraLoi,
            'thuTu' => $this->thuTu,
            'isQuickPoll' => $this->isQuickPoll,
            'quick_poll' => $this->isQuickPoll,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getMaCauHoi(): string { return $this->maCauHoi; }
    public function getMaKhaoSat(): int { return $this->maKhaoSat; }
    public function getLoaiCauHoi(): string { return $this->loaiCauHoi; }
    public function getNoiDungCauHoi(): string { return $this->noiDungCauHoi; }
    public function isBatBuocTraLoi(): bool { return $this->batBuocTraLoi; }
    public function getThuTu(): int { return $this->thuTu; }
    public function isQuickPoll(): bool { return $this->isQuickPoll; }
}
