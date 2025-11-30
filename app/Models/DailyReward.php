<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class DailyReward
{
    private int $id;
    private int $userId;
    private int $currentStreak;
    private ?string $lastClaimedDate;
    private int $totalPoints;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $attributes)
    {
        $this->id = (int) ($attributes['id'] ?? 0);
        $this->userId = (int) $attributes['user_id'];
        $this->currentStreak = (int) ($attributes['current_streak'] ?? 0);
        $this->lastClaimedDate = $attributes['last_claimed_date'] ?? null;
        $this->totalPoints = (int) ($attributes['total_points'] ?? 0);
        $this->createdAt = (string) ($attributes['created_at'] ?? '');
        $this->updatedAt = (string) ($attributes['updated_at'] ?? '');
    }

    public static function findByUserId(int $userId): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('SELECT * FROM daily_rewards WHERE user_id = :user_id LIMIT 1');
        $statement->execute([':user_id' => $userId]);
        $row = $statement->fetch();

        if (!$row) {
            return null;
        }

        return new self($row);
    }

    public static function create(int $userId, int $currentStreak, string $lastClaimedDate, int $totalPoints): self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('INSERT INTO daily_rewards (user_id, current_streak, last_claimed_date, total_points, created_at, updated_at) VALUES (:user_id, :current_streak, :last_claimed_date, :total_points, NOW(), NOW())');
        $statement->execute([
            ':user_id' => $userId,
            ':current_streak' => $currentStreak,
            ':last_claimed_date' => $lastClaimedDate,
            ':total_points' => $totalPoints,
        ]);

        $id = (int) $db->lastInsertId();

        $statement = $db->prepare('SELECT * FROM daily_rewards WHERE id = :id LIMIT 1');
        $statement->execute([':id' => $id]);
        $row = $statement->fetch();

        return new self($row ?: []);
    }

    public function updateStreak(int $currentStreak, string $lastClaimedDate, int $totalPoints): void
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('UPDATE daily_rewards SET current_streak = :current_streak, last_claimed_date = :last_claimed_date, total_points = :total_points, updated_at = NOW() WHERE id = :id');
        $statement->execute([
            ':current_streak' => $currentStreak,
            ':last_claimed_date' => $lastClaimedDate,
            ':total_points' => $totalPoints,
            ':id' => $this->id,
        ]);

        $this->currentStreak = $currentStreak;
        $this->lastClaimedDate = $lastClaimedDate;
        $this->totalPoints = $totalPoints;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCurrentStreak(): int
    {
        return $this->currentStreak;
    }

    public function getLastClaimedDate(): ?string
    {
        return $this->lastClaimedDate;
    }

    public function getTotalPoints(): int
    {
        return $this->totalPoints;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'current_streak' => $this->currentStreak,
            'last_claimed_date' => $this->lastClaimedDate,
            'total_points' => $this->totalPoints,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}

