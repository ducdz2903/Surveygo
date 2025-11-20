<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class UserResponse
{
    private int $id;
    private int $maCauHoi;
    private int $maNguoiDung;
    private int $maKhaoSat;
    private ?string $noiDungTraLoi;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $attributes)
    {
        $this->id = (int) ($attributes['id'] ?? 0);
        $this->maCauHoi = (int) ($attributes['maCauHoi'] ?? 0);
        $this->maNguoiDung = (int) ($attributes['maNguoiDung'] ?? 0);
        $this->maKhaoSat = (int) ($attributes['maKhaoSat'] ?? 0);
        $this->noiDungTraLoi = $attributes['noiDungTraLoi'] ?? null;
        $this->createdAt = $attributes['created_at'] ?? '';
        $this->updatedAt = $attributes['updated_at'] ?? '';
    }

    /**
     * Lấy tất cả câu trả lời
     */
    public static function all(): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->query('SELECT * FROM user_responses ORDER BY created_at DESC');
        $rows = $statement->fetchAll();

        return array_map(fn($row) => new self($row), $rows);
    }

    /**
     * Lấy câu trả lời theo ID
     */
    public static function find(int $id): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('SELECT * FROM user_responses WHERE id = :id LIMIT 1');
        $statement->execute([':id' => $id]);
        $row = $statement->fetch();

        return $row ? new self($row) : null;
    }

    /**
     * Lấy tất cả câu trả lời của user cho một khảo sát
     */
    public static function findBySurveyAndUser(int $surveyId, int $userId): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare(
            'SELECT * FROM user_responses 
             WHERE maKhaoSat = :surveyId AND maNguoiDung = :userId 
             ORDER BY maCauHoi ASC'
        );
        $statement->execute([
            ':surveyId' => $surveyId,
            ':userId' => $userId,
        ]);
        $rows = $statement->fetchAll();

        return array_map(fn($row) => new self($row), $rows);
    }

    /**
     * Lấy tất cả câu trả lời cho một câu hỏi
     */
    public static function findByQuestion(int $questionId): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('SELECT * FROM user_responses WHERE maCauHoi = :questionId ORDER BY created_at DESC');
        $statement->execute([':questionId' => $questionId]);
        $rows = $statement->fetchAll();

        return array_map(fn($row) => new self($row), $rows);
    }

    /**
     * Tạo câu trả lời mới
     * 
     * Hỗ trợ 2 loại:
     * 1. Text: { maCauHoi, maNguoiDung, maKhaoSat, noiDungTraLoi }
     * 2. Choice: { maCauHoi, maNguoiDung, maKhaoSat, maAnswerId }
     */
    public static function create(array $data): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $maCauHoi = (int) ($data['maCauHoi'] ?? 0);
        $maNguoiDung = (int) ($data['maNguoiDung'] ?? 0);
        $maKhaoSat = (int) ($data['maKhaoSat'] ?? 0);
        $noiDungTraLoi = $data['noiDungTraLoi'] ?? null;

        if (!$maCauHoi || !$maNguoiDung || !$maKhaoSat) {
            return null;
        }

        try {
            $statement = $db->prepare(
                'INSERT INTO user_responses 
                 (maCauHoi, maNguoiDung, maKhaoSat, noiDungTraLoi, created_at, updated_at) 
                 VALUES (:maCauHoi, :maNguoiDung, :maKhaoSat, :noiDungTraLoi, NOW(), NOW())'
            );

            $statement->execute([
                ':maCauHoi' => $maCauHoi,
                ':maNguoiDung' => $maNguoiDung,
                ':maKhaoSat' => $maKhaoSat,
                ':noiDungTraLoi' => $noiDungTraLoi,
            ]);

            $id = (int) $db->lastInsertId();
            return self::find($id);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create user response: ' . $e->getMessage());
        }
    }

    public function update(int $id, array $data): bool
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $updateFields = [];
        $params = [':id' => $id];

        if (isset($data['noiDungTraLoi'])) {
            $updateFields[] = 'noiDungTraLoi = :noiDungTraLoi';
            $params[':noiDungTraLoi'] = $data['noiDungTraLoi'];
        }

        if (empty($updateFields)) {
            return false;
        }

        $updateFields[] = 'updated_at = NOW()';
        $query = 'UPDATE user_responses SET ' . implode(', ', $updateFields) . ' WHERE id = :id';

        try {
            $statement = $db->prepare($query);
            return $statement->execute($params);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Xóa câu trả lời
     */
    public static function delete(int $id): bool
    {
        /** @var PDO $db */
        $db = Container::get('db');

        try {
            $statement = $db->prepare('DELETE FROM user_responses WHERE id = :id');
            return $statement->execute([':id' => $id]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Xóa tất cả câu trả lời của user cho một khảo sát
     */
    public static function deleteBySurveyAndUser(int $surveyId, int $userId): bool
    {
        /** @var PDO $db */
        $db = Container::get('db');

        try {
            $statement = $db->prepare(
                'DELETE FROM user_responses WHERE maKhaoSat = :surveyId AND maNguoiDung = :userId'
            );
            return $statement->execute([
                ':surveyId' => $surveyId,
                ':userId' => $userId,
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }
    public function getMaCauHoi(): int
    {
        return $this->maCauHoi;
    }
    public function getMaNguoiDung(): int
    {
        return $this->maNguoiDung;
    }
    public function getMaKhaoSat(): int
    {
        return $this->maKhaoSat;
    }
    public function getNoiDungTraLoi(): ?string
    {
        return $this->noiDungTraLoi;
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
            'maCauHoi' => $this->maCauHoi,
            'maNguoiDung' => $this->maNguoiDung,
            'maKhaoSat' => $this->maKhaoSat,
            'noiDungTraLoi' => $this->noiDungTraLoi,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
