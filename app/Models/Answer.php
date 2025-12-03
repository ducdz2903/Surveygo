<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class Answer
{
    private int $id;
    private int $idCauHoi;
    private string $noiDungCauTraLoi;
    private string $createdAt;
    private string $updatedAt;
    private int $creatorId;

    public function __construct(array $attributes)
    {
        $this->id = (int) ($attributes['id'] ?? 0);
        // DB uses idCauHoi; accept legacy maCauHoi as fallback
        $this->idCauHoi = isset($attributes['idCauHoi']) ? (int)$attributes['idCauHoi'] : (int)($attributes['maCauHoi'] ?? 0);
        $this->noiDungCauTraLoi = $attributes['noiDungCauTraLoi'] ?? '';
        $this->createdAt = $attributes['created_at'] ?? '';
        $this->updatedAt = $attributes['updated_at'] ?? '';
        $this->creatorId = (int) ($attributes['creator_id'] ?? 0);
    }

    /**
     * Lấy tất cả câu trả lời
     */
    public static function all(): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->query('SELECT * FROM answers ORDER BY id ASC');
        $rows = $statement->fetchAll();

        return array_map(fn($row) => new self($row), $rows);
    }

    /**
     * Lấy tất cả câu trả lời của một câu hỏi
     */
    public static function findByQuestion(int $questionId): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('SELECT * FROM answers WHERE idCauHoi = :questionId ORDER BY id ASC');
        $statement->execute([':questionId' => $questionId]);
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

        $statement = $db->prepare('SELECT * FROM answers WHERE id = :id LIMIT 1');
        $statement->execute([':id' => $id]);
        $row = $statement->fetch();

        return $row ? new self($row) : null;
    }

    /**
     * Tạo câu trả lời mới
     */
    public static function create(array $data): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $idCauHoi = (int) ($data['idCauHoi'] ?? $data['maCauHoi'] ?? 0);
        $noiDungCauTraLoi = (string) ($data['noiDungCauTraLoi'] ?? '');
        $creatorId = (int) ($data['creator_id'] ?? 0);

        if (!$idCauHoi || !$noiDungCauTraLoi) {
            return null;
        }

        try {
            $statement = $db->prepare(
                'INSERT INTO answers (idCauHoi, noiDungCauTraLoi, creator_id, created_at, updated_at) 
                 VALUES (:idCauHoi, :noiDungCauTraLoi, :creator_id, NOW(), NOW())'
            );

            $statement->execute([
                ':idCauHoi' => $idCauHoi,
                ':noiDungCauTraLoi' => $noiDungCauTraLoi,
                ':creator_id' => $creatorId,
            ]);

            $id = (int) $db->lastInsertId();
            return self::find($id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Cập nhật câu trả lời
     */
    public static function update(int $id, array $data): bool
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $updateFields = [];
        $params = [':id' => $id];

        if (isset($data['noiDungCauTraLoi'])) {
            $updateFields[] = 'noiDungCauTraLoi = :noiDungCauTraLoi';
            $params[':noiDungCauTraLoi'] = $data['noiDungCauTraLoi'];
        }

        // no 'laDung' column in DB schema; only allow updating content or creator if needed
        if (isset($data['noiDungCauTraLoi'])) {
            // handled above
        }

        if (empty($updateFields)) {
            return false;
        }

        $updateFields[] = 'updated_at = NOW()';
        $query = 'UPDATE answers SET ' . implode(', ', $updateFields) . ' WHERE id = :id';

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
            $statement = $db->prepare('DELETE FROM answers WHERE id = :id');
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
    public function getIdCauHoi(): int
    {
        return $this->idCauHoi;
    }
    public function getNoiDungCauTraLoi(): string
    {
        return $this->noiDungCauTraLoi;
    }
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
    public function getCreatorId(): int
    {
        return $this->creatorId;
    }

    /**
     * Chuyển đổi model thành array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'idCauHoi' => $this->idCauHoi,
            'noiDungCauTraLoi' => $this->noiDungCauTraLoi,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'creator_id' => $this->creatorId,
        ];
    }
}
