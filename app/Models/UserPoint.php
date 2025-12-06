<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class UserPoint
{
    private int $id;
    private int $userId;
    private int $balance;
    private int $totalEarned;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $attributes)
    {
        $this->id = (int) ($attributes['id'] ?? 0);
        $this->userId = (int) ($attributes['user_id'] ?? 0);
        $this->balance = (int) ($attributes['balance'] ?? 0);
        $this->totalEarned = (int) ($attributes['total_earned'] ?? 0);
        $this->createdAt = (string) ($attributes['created_at'] ?? '');
        $this->updatedAt = (string) ($attributes['updated_at'] ?? '');
    }

    public static function findByUserId(int $userId, bool $forUpdate = false): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $sql = 'SELECT * FROM user_points WHERE user_id = :user_id';
        if ($forUpdate) {
            $sql .= ' FOR UPDATE';
        }

        $statement = $db->prepare($sql);
        $statement->execute([':user_id' => $userId]);
        $row = $statement->fetch();

        return $row ? new self($row) : null;
    }

    public static function create(int $userId, bool $forUpdate = false): self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare(
            'INSERT INTO user_points (user_id, balance, total_earned, created_at, updated_at)
             VALUES (:user_id, 0, 0, NOW(), NOW())'
        );
        $statement->execute([':user_id' => $userId]);

        return self::findByUserId($userId, $forUpdate) ?? new self(['user_id' => $userId]);
    }

    public static function getOrCreate(int $userId, bool $forUpdate = false): self
    {
        $record = self::findByUserId($userId, $forUpdate);
        if ($record) {
            return $record;
        }

        return self::create($userId, $forUpdate);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function getTotalEarned(): int
    {
        return $this->totalEarned;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'balance' => $this->balance,
            'total_earned' => $this->totalEarned,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
