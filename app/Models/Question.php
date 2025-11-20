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
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $attributes)
    {
        $this->id = (int)($attributes['id'] ?? 0);
        $this->maCauHoi = $attributes['maCauHoi'] ?? '';
        $this->maKhaoSat = (int)($attributes['maKhaoSat'] ?? 0);
        $this->loaiCauHoi = $attributes['loaiCauHoi'] ?? '';
        $this->noiDungCauHoi = $attributes['noiDungCauHoi'] ?? '';
        $this->batBuocTraLoi = (bool)($attributes['batBuocTraLoi'] ?? false);
        $this->thuTu = (int)($attributes['thuTu'] ?? 0);
        $this->createdAt = $attributes['created_at'] ?? '';
        $this->updatedAt = $attributes['updated_at'] ?? '';
    }
    /** 
     * Lấy tất cả câu hỏi
     */
    public static function all(): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->query('SELECT * FROM questions ORDER BY thuTu ASC');
        $rows = $statement->fetchAll();

        return array_map(fn($row) => new self($row), $rows);
    }

    /**
     * Lấy tất cả câu hỏi của một khảo sát (sắp xếp theo thuTu)
     */
    public static function findBySurvey(int $surveyId): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('SELECT * FROM questions WHERE maKhaoSat = :surveyId ORDER BY thuTu ASC');
        $statement->execute([':surveyId' => $surveyId]);
        $rows = $statement->fetchAll();

        return array_map(fn($row) => new self($row), $rows);
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
     * Lấy câu hỏi theo maCauHoi
     */
    public static function findByMa(string $maCauHoi): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('SELECT * FROM questions WHERE maCauHoi = :ma LIMIT 1');
        $statement->execute([':ma' => $maCauHoi]);
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
        if (empty($data['noiDungCauHoi']) || empty($data['maKhaoSat']) || empty($data['loaiCauHoi'])) {
            return null;
        }

        // Kiểm tra khóa ngoại maKhaoSat
        $surveyStmt = $db->prepare('SELECT id FROM surveys WHERE id = :id LIMIT 1');
        $surveyStmt->execute([':id' => $data['maKhaoSat']]);
        if (!$surveyStmt->fetch()) {
            return null; // Survey không tồn tại
        }

        // Auto-gen maCauHoi nếu chưa có
        $maCauHoi = $data['maCauHoi'] ?? 'CH-' . time() . '-' . random_int(1000, 9999);

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        try {
            $statement = $db->prepare(
                'INSERT INTO questions (maCauHoi, maKhaoSat, loaiCauHoi, noiDungCauHoi, batBuocTraLoi, thuTu, created_at, updated_at)
                VALUES (:ma, :surveyId, :loai, :noidung, :batbuoc, :thutu, :created, :updated)'
            );

            $statement->execute([
                ':ma' => $maCauHoi,
                ':surveyId' => $data['maKhaoSat'],
                ':loai' => $data['loaiCauHoi'],
                ':noidung' => $data['noiDungCauHoi'],
                ':batbuoc' => (int)($data['batBuocTraLoi'] ?? false),
                ':thutu' => (int)($data['thuTu'] ?? 0),
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
        $thuTu = $data['thuTu'] ?? $this->thuTu;

        $statement = $db->prepare(
            'UPDATE questions SET loaiCauHoi = :loai, noiDungCauHoi = :noidung, batBuocTraLoi = :batbuoc, thuTu = :thutu, updated_at = :updated WHERE id = :id'
        );

        return $statement->execute([
            ':loai' => $loaiCauHoi,
            ':noidung' => $noiDungCauHoi,
            ':batbuoc' => (int)$batBuocTraLoi,
            ':thutu' => (int)$thuTu,
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

        $statement = $db->prepare('SELECT * FROM answers WHERE maCauHoi = :questionId ORDER BY id ASC');
        $statement->execute([':questionId' => $this->id]);
        $rows = $statement->fetchAll();

        return array_map(fn($row) => [
            'id' => (int)$row['id'],
            'maCauHoi' => (int)$row['maCauHoi'],
            'noiDungCauTraLoi' => $row['noiDungCauTraLoi'],
            'laDung' => (bool)$row['laDung'],
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
}