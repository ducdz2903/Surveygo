<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class PointTransaction
{
    private int $id;
    private int $userId;
    private string $source;
    private ?int $refId;
    private int $amount;
    private int $balanceAfter;
    private ?string $note;
    private string $createdAt;
    private string $updatedAt;

    private const ALLOWED_SOURCES = ['daily_reward', 'survey', 'manual_adjustment'];

    public function __construct(array $attributes)
    {
        $this->id = (int) ($attributes['id'] ?? 0);
        $this->userId = (int) ($attributes['user_id'] ?? 0);
        $this->source = (string) ($attributes['source'] ?? '');
        $this->refId = isset($attributes['ref_id']) ? (int) $attributes['ref_id'] : null;
        $this->amount = (int) ($attributes['amount'] ?? 0);
        $this->balanceAfter = (int) ($attributes['balance_after'] ?? 0);
        $this->note = $attributes['note'] ?? null;
        $this->createdAt = (string) ($attributes['created_at'] ?? '');
        $this->updatedAt = (string) ($attributes['updated_at'] ?? '');
    }

    public static function find(int $id): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('SELECT * FROM point_transactions WHERE id = :id LIMIT 1');
        $statement->execute([':id' => $id]);
        $row = $statement->fetch();

        return $row ? new self($row) : null;
    }

    public static function findBySourceAndRef(int $userId, string $source, ?int $refId): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare(
            'SELECT * FROM point_transactions 
             WHERE user_id = :user_id 
               AND source = :source
               AND (
                   (ref_id IS NULL AND :ref_id IS NULL)
                   OR ref_id = :ref_id
               )
             LIMIT 1'
        );

        $statement->execute([
            ':user_id' => $userId,
            ':source' => $source,
            ':ref_id' => $refId,
        ]);

        $row = $statement->fetch();

        return $row ? new self($row) : null;
    }

    public static function addPoints(int $userId, int $amount, string $source, ?int $refId = null, ?string $note = null): self
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Point amount must be greater than zero.');
        }

        self::assertValidSource($source);

        /** @var PDO $db */
        $db = Container::get('db');

        try {
            $db->beginTransaction();

            $userPoints = UserPoint::getOrCreate($userId, true);

            $currentBalance = $userPoints->getBalance();
            $currentTotalEarned = $userPoints->getTotalEarned();

            $balanceAfter = $currentBalance + $amount;
            $totalEarned = $currentTotalEarned + $amount;

            $insert = $db->prepare(
                'INSERT INTO point_transactions (user_id, source, ref_id, amount, balance_after, note, created_at, updated_at)
                 VALUES (:user_id, :source, :ref_id, :amount, :balance_after, :note, NOW(), NOW())'
            );

            $insert->execute([
                ':user_id' => $userId,
                ':source' => $source,
                ':ref_id' => $refId,
                ':amount' => $amount,
                ':balance_after' => $balanceAfter,
                ':note' => $note,
            ]);

            $update = $db->prepare(
                'UPDATE user_points
                 SET balance = :balance, total_earned = :total_earned, updated_at = NOW()
                 WHERE user_id = :user_id'
            );

            $update->execute([
                ':balance' => $balanceAfter,
                ':total_earned' => $totalEarned,
                ':user_id' => $userId,
            ]);

            $txId = (int) $db->lastInsertId();
            $db->commit();

            return self::find($txId) ?? new self([
                'user_id' => $userId,
                'source' => $source,
                'ref_id' => $refId,
                'amount' => $amount,
                'balance_after' => $balanceAfter,
                'note' => $note,
            ]);
        } catch (\PDOException $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            // Handle idempotent insert attempts based on unique (user_id, source, ref_id)
            if ($exception->getCode() === '23000') {
                $existing = self::findBySourceAndRef($userId, $source, $refId);
                if ($existing) {
                    return $existing;
                }
            }

            throw $exception;
        } catch (\Exception $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $exception;
        }
    }

    public static function listByUser(int $userId, int $limit = 50): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $limit = max(1, min($limit, 200));

        $statement = $db->prepare(
            'SELECT * FROM point_transactions WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit'
        );
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        $rows = $statement->fetchAll();

        return array_map(fn($row) => new self($row), $rows);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getRefId(): ?int
    {
        return $this->refId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getBalanceAfter(): int
    {
        return $this->balanceAfter;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'source' => $this->source,
            'ref_id' => $this->refId,
            'amount' => $this->amount,
            'balance_after' => $this->balanceAfter,
            'note' => $this->note,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    private static function assertValidSource(string $source): void
    {
        if (!in_array($source, self::ALLOWED_SOURCES, true)) {
            throw new \InvalidArgumentException('Invalid point source: ' . $source);
        }
    }
}
