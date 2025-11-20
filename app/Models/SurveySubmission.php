<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class SurveySubmission
{
    private int $id;
    private int $maKhaoSat;
    private int $maNguoiDung;
    private string $trangThai;
    private ?int $diemDat;
    private ?string $ghiChu;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $attributes)
    {
        $this->id = (int) ($attributes['id'] ?? 0);
        $this->maKhaoSat = (int) ($attributes['maKhaoSat'] ?? 0);
        $this->maNguoiDung = (int) ($attributes['maNguoiDung'] ?? 0);
        $this->trangThai = $attributes['trangThai'] ?? 'submitted';
        $this->diemDat = isset($attributes['diemDat']) ? (int) $attributes['diemDat'] : null;
        $this->ghiChu = $attributes['ghiChu'] ?? null;
        $this->createdAt = $attributes['created_at'] ?? '';
        $this->updatedAt = $attributes['updated_at'] ?? '';
    }

    /**
     * Lấy tất cả submission
     */
    public static function all(): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->query('SELECT * FROM survey_submissions ORDER BY created_at DESC');
        $rows = $statement->fetchAll();

        return array_map(fn($row) => new self($row), $rows);
    }

    /**
     * Lấy submission theo ID
     */
    public static function find(int $id): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('SELECT * FROM survey_submissions WHERE id = :id LIMIT 1');
        $statement->execute([':id' => $id]);
        $row = $statement->fetch();

        return $row ? new self($row) : null;
    }

    /**
     * Lấy submission của user cho survey
     */
    public static function findBySurveyAndUser(int $surveyId, int $userId): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare(
            'SELECT * FROM survey_submissions 
             WHERE maKhaoSat = :surveyId AND maNguoiDung = :userId LIMIT 1'
        );
        $statement->execute([
            ':surveyId' => $surveyId,
            ':userId' => $userId,
        ]);
        $row = $statement->fetch();

        return $row ? new self($row) : null;
    }

    /**
     * Lấy tất cả submission của user
     */
    public static function findByUser(int $userId): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('SELECT * FROM survey_submissions WHERE maNguoiDung = :userId ORDER BY created_at DESC');
        $statement->execute([':userId' => $userId]);
        $rows = $statement->fetchAll();

        return array_map(fn($row) => new self($row), $rows);
    }

    /**
     * Lấy tất cả submission cho survey
     */
    public static function findBySurvey(int $surveyId): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('SELECT * FROM survey_submissions WHERE maKhaoSat = :surveyId ORDER BY created_at DESC');
        $statement->execute([':surveyId' => $surveyId]);
        $rows = $statement->fetchAll();

        return array_map(fn($row) => new self($row), $rows);
    }

    /**
     * Tạo submission mới
     */
    public static function create(array $data): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $maKhaoSat = (int) ($data['maKhaoSat'] ?? 0);
        $maNguoiDung = (int) ($data['maNguoiDung'] ?? 0);
        $trangThai = $data['trangThai'] ?? 'submitted';
        $diemDat = isset($data['diemDat']) ? (int) $data['diemDat'] : null;
        $ghiChu = $data['ghiChu'] ?? null;

        if (!$maKhaoSat || !$maNguoiDung) {
            return null;
        }

        try {
            $statement = $db->prepare(
                'INSERT INTO survey_submissions 
                 (maKhaoSat, maNguoiDung, trangThai, diemDat, ghiChu, created_at, updated_at) 
                 VALUES (:maKhaoSat, :maNguoiDung, :trangThai, :diemDat, :ghiChu, NOW(), NOW())'
            );

            $statement->execute([
                ':maKhaoSat' => $maKhaoSat,
                ':maNguoiDung' => $maNguoiDung,
                ':trangThai' => $trangThai,
                ':diemDat' => $diemDat,
                ':ghiChu' => $ghiChu,
            ]);

            $id = (int) $db->lastInsertId();
            return self::find($id);
        } catch (\PDOException $e) {
            // Check for UNIQUE constraint violation (duplicate submission)
            if (strpos($e->getMessage(), 'Duplicate') !== false || $e->getCode() === '23000') {
                throw new \Exception('User has already submitted this survey');
            }
            throw new \Exception('Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('Failed to create survey submission: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật submission
     */
    public static function update(int $id, array $data): bool
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $updateFields = [];
        $params = [':id' => $id];

        if (isset($data['trangThai'])) {
            $updateFields[] = 'trangThai = :trangThai';
            $params[':trangThai'] = $data['trangThai'];
        }

        if (isset($data['diemDat'])) {
            $updateFields[] = 'diemDat = :diemDat';
            $params[':diemDat'] = $data['diemDat'];
        }

        if (isset($data['ghiChu'])) {
            $updateFields[] = 'ghiChu = :ghiChu';
            $params[':ghiChu'] = $data['ghiChu'];
        }

        if (empty($updateFields)) {
            return false;
        }

        $updateFields[] = 'updated_at = NOW()';
        $query = 'UPDATE survey_submissions SET ' . implode(', ', $updateFields) . ' WHERE id = :id';

        try {
            $statement = $db->prepare($query);
            return $statement->execute($params);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Xóa submission
     */
    public static function delete(int $id): bool
    {
        /** @var PDO $db */
        $db = Container::get('db');

        try {
            $statement = $db->prepare('DELETE FROM survey_submissions WHERE id = :id');
            return $statement->execute([':id' => $id]);
        } catch (\Exception $e) {
            return false;
        }
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }
    public function getMaKhaoSat(): int
    {
        return $this->maKhaoSat;
    }
    public function getMaNguoiDung(): int
    {
        return $this->maNguoiDung;
    }
    public function getTrangThai(): string
    {
        return $this->trangThai;
    }
    public function getDiemDat(): ?int
    {
        return $this->diemDat;
    }
    public function getGhiChu(): ?string
    {
        return $this->ghiChu;
    }
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     * Chuyển đổi model thành array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'maKhaoSat' => $this->maKhaoSat,
            'maNguoiDung' => $this->maNguoiDung,
            'trangThai' => $this->trangThai,
            'diemDat' => $this->diemDat,
            'ghiChu' => $this->ghiChu,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
