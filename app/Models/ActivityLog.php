<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class ActivityLog
{
    private PDO $db;
    private string $table = 'activity_logs';

    private int $id;
    private int $userId;
    private string $action;
    private ?string $entityType;
    private ?int $entityId;
    private ?string $description;
    private ?string $ipAddress;
    private ?string $userAgent;
    private ?array $oldValues;
    private ?array $newValues;
    private string $createdAt;

    public function __construct(array $attributes = [])
    {
        $this->db = Container::get('db');

        if (!empty($attributes)) {
            $this->id = (int) ($attributes['id'] ?? 0);
            $this->userId = (int) ($attributes['user_id'] ?? 0);
            $this->action = (string) ($attributes['action'] ?? '');
            $this->entityType = $attributes['entity_type'] ?? null;
            $this->entityId = isset($attributes['entity_id']) ? (int) $attributes['entity_id'] : null;
            $this->description = $attributes['description'] ?? null;
            $this->ipAddress = $attributes['ip_address'] ?? null;
            $this->userAgent = $attributes['user_agent'] ?? null;
            $this->oldValues = isset($attributes['old_values']) && is_string($attributes['old_values']) 
                ? json_decode($attributes['old_values'], true) 
                : ($attributes['old_values'] ?? null);
            $this->newValues = isset($attributes['new_values']) && is_string($attributes['new_values'])
                ? json_decode($attributes['new_values'], true)
                : ($attributes['new_values'] ?? null);
            $this->createdAt = $attributes['created_at'] ?? '';
        }
    }


    public function log(int $userId, string $action, array $options = []): bool
    {
        $entityType = $options['entity_type'] ?? null;
        $entityId = $options['entity_id'] ?? null;
        $description = $options['description'] ?? null;
        $oldValues = $options['old_values'] ?? null;
        $newValues = $options['new_values'] ?? null;
        $ipAddress = $options['ip_address'] ?? ($this->getClientIp());
        $userAgent = $options['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? null;

        $query = "
            INSERT INTO {$this->table} 
            (user_id, action, entity_type, entity_id, description, ip_address, user_agent, old_values, new_values, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ";

        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            $userId,
            $action,
            $entityType,
            $entityId,
            $description,
            $ipAddress,
            $userAgent,
            $oldValues ? json_encode($oldValues) : null,
            $newValues ? json_encode($newValues) : null,
        ]);
    }

    public function getByUserId(int $userId, int $limit = 50, int $offset = 0): array
    {
        $query = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getByEntity(string $entityType, int $entityId, int $limit = 50): array
    {
        $query = "SELECT al.*, u.name as user_name, u.email FROM {$this->table} al LEFT JOIN users u ON al.user_id = u.id WHERE al.entity_type = ? AND al.entity_id = ? ORDER BY al.created_at DESC LIMIT {$limit}";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$entityType, $entityId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getByAction(string $action, int $limit = 50, int $offset = 0): array
    {
        $query = "SELECT al.*, u.name as user_name, u.email FROM {$this->table} al LEFT JOIN users u ON al.user_id = u.id WHERE al.action = ? ORDER BY al.created_at DESC LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$action]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getAll(int $limit = 50, int $offset = 0): array
    {
        $query = "SELECT al.*, u.name as user_name, u.email FROM {$this->table} al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->db->prepare($query);
        $stmt->execute([]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function countAll(): int
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($result['total'] ?? 0);
    }

    public function countByUserId(int $userId): int
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($result['total'] ?? 0);
    }

    public function deleteOldLogs(int $days = 90): bool
    {
        $query = "DELETE FROM {$this->table} WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        $stmt = $this->db->prepare($query);

        return $stmt->execute([$days]);
    }

    private function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
    }

    // Getter methods
    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getEntityType(): ?string
    {
        return $this->entityType;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getOldValues(): ?array
    {
        return $this->oldValues;
    }

    public function getNewValues(): ?array
    {
        return $this->newValues;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    // Setter methods
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    public function setEntityType(?string $entityType): self
    {
        $this->entityType = $entityType;
        return $this;
    }

    public function setEntityId(?int $entityId): self
    {
        $this->entityId = $entityId;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function setOldValues(?array $oldValues): self
    {
        $this->oldValues = $oldValues;
        return $this;
    }

    public function setNewValues(?array $newValues): self
    {
        $this->newValues = $newValues;
        return $this;
    }
}

