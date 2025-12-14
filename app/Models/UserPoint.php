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
    private int $luckyWheelSpins;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $attributes)
    {
        $this->id = (int) ($attributes['id'] ?? 0);
        $this->userId = (int) ($attributes['user_id'] ?? 0);
        $this->balance = (int) ($attributes['balance'] ?? 0);
        $this->totalEarned = (int) ($attributes['total_earned'] ?? 0);
        $this->luckyWheelSpins = (int) ($attributes['lucky_wheel_spins'] ?? 0);
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

    public function addLuckyWheelSpins(int $amount): void
    {
        $db = Container::get('db');
        $stmt = $db->prepare(
            'UPDATE user_points 
             SET lucky_wheel_spins = lucky_wheel_spins + :amount,
                 updated_at = NOW()
             WHERE user_id = :user_id'
        );
        $stmt->execute([
            ':amount' => $amount,
            ':user_id' => $this->userId
        ]);
        $this->luckyWheelSpins += $amount;
    }

    public function useLuckyWheelSpin(): bool
    {
        if ($this->luckyWheelSpins <= 0) {
            return false;
        }
    
        $db = Container::get('db');
        $stmt = $db->prepare(
            'UPDATE user_points 
             SET lucky_wheel_spins = lucky_wheel_spins - 1,
                 updated_at = NOW()
             WHERE user_id = :user_id 
               AND lucky_wheel_spins > 0'
        );
        $stmt->execute([':user_id' => $this->userId]);
    
        if ($stmt->rowCount() > 0) {
            $this->luckyWheelSpins--;
            return true;
        }
    
    return false;
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

    public function getLuckyWheelSpins(): int
    {
        return $this->luckyWheelSpins;
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
