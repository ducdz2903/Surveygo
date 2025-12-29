<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class UserInvite
{
    private int $id;
    private int $userId;
    private string $inviteCode;
    private ?string $inviteToken;
    private string $inviteLink;
    private int $invitedCount;
    private int $totalRewards;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $attributes)
    {
        $this->id = (int) ($attributes['id'] ?? 0);
        $this->userId = (int) $attributes['user_id'];
        $this->inviteCode = (string) $attributes['invite_code'];
        $this->inviteToken = $attributes['invite_token'] ?? null;
        $this->inviteLink = (string) $attributes['invite_link'];
        $this->invitedCount = (int) ($attributes['invited_count'] ?? 0);
        $this->totalRewards = (int) ($attributes['total_rewards'] ?? 0);
        $this->createdAt = $attributes['created_at'];
        $this->updatedAt = $attributes['updated_at'];
    }

    /**
     * Tạo mã mời duy nhất 6 chữ số và chữ cái
     */
    private static function generateInviteCode(): string
    {
        /** @var PDO $db */
        $db = Container::get('db');
        
        $maxAttempts = 10;
        for ($i = 0; $i < $maxAttempts; $i++) {
            // Tạo mã 6 ký tự alphanum (chữ hoa và số)
            $code = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6);
            
            // Kiểm tra nếu mã đã tồn tại
            $stmt = $db->prepare('SELECT COUNT(*) as count FROM user_invites WHERE invite_code = :code');
            $stmt->execute([':code' => $code]);
            $result = $stmt->fetch();
            
            if ($result['count'] == 0) {
                return $code;
            }
        }
        
        // Dự phòng: sử dụng mã dựa trên timestamp
        return strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 6));
    }

    /**
     * Tạo token mời duy nhất ngẫu nhiên
     */
    private static function generateInviteToken(): string
    {
        /** @var PDO $db */
        $db = Container::get('db');
        
        $maxAttempts = 10;
        for ($i = 0; $i < $maxAttempts; $i++) {
            // Tạo token ngẫu nhiên 32 ký tự
            $token = bin2hex(random_bytes(16));
            
            // Kiểm tra nếu token đã tồn tại
            $stmt = $db->prepare('SELECT COUNT(*) as count FROM user_invites WHERE invite_token = :token');
            $stmt->execute([':token' => $token]);
            $result = $stmt->fetch();
            
            if ($result['count'] == 0) {
                return $token;
            }
        }
        
        // Dự phòng (không chắc xảy ra)
        return bin2hex(random_bytes(16));
    }

    /**
     * Tạo link mời dựa trên token
     */
    private static function generateInviteLink(string $token): string
    {
        // Lấy URL gốc từ cấu hình server
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        return "{$protocol}://{$host}/register?token={$token}";
    }

    /**
     * Tạo bản ghi mới mới cho user
     */
    public static function create(int $userId): self
    {
        /** @var PDO $db */
        $db = Container::get('db');
        
        $code = self::generateInviteCode();
        $token = self::generateInviteToken();
        $link = self::generateInviteLink($token);
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        
        $stmt = $db->prepare(
            'INSERT INTO user_invites (user_id, invite_code, invite_token, invite_link, invited_count, total_rewards, created_at, updated_at) 
             VALUES (:user_id, :invite_code, :invite_token, :invite_link, 0, 0, :created_at, :updated_at)'
        );
        
        $stmt->execute([
            ':user_id' => $userId,
            ':invite_code' => $code,
            ':invite_token' => $token,
            ':invite_link' => $link,
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);
        
        $id = (int) $db->lastInsertId();
        
        return self::findById($id);
    }

    /**
     * Tìm bản ghi mới theo ID
     */
    public static function findById(int $id): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');
        
        $stmt = $db->prepare('SELECT * FROM user_invites WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        return new self($row);
    }

    /**
     * Tìm bản ghi mời theo user ID
     */
    public static function findByUserId(int $userId): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');
        
        $stmt = $db->prepare('SELECT * FROM user_invites WHERE user_id = :user_id LIMIT 1');
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        return new self($row);
    }

    /**
     * Tìm bản ghi mời theo mã mời
     */
    public static function findByInviteCode(string $code): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');
        
        $stmt = $db->prepare('SELECT * FROM user_invites WHERE invite_code = :code LIMIT 1');
        $stmt->execute([':code' => strtoupper($code)]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        return new self($row);
    }

    /**
     * Tìm bản ghi mời theo invite token
     */
    public static function findByToken(string $token): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');
        
        $stmt = $db->prepare('SELECT * FROM user_invites WHERE invite_token = :token LIMIT 1');
        $stmt->execute([':token' => $token]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        return new self($row);
    }

    /**
     * Lấy hoặc tảo bản ghi mời cho user
     */
    public static function getOrCreate(int $userId): self
    {
        $invite = self::findByUserId($userId);
        
        if (!$invite) {
            $invite = self::create($userId);
        }
        
        return $invite;
    }

    /**
     * Tăng số lượng đã mời cho user này
     */
    public function incrementInviteCount(): void
    {
        /** @var PDO $db */
        $db = Container::get('db');
        
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        
        $stmt = $db->prepare(
            'UPDATE user_invites 
             SET invited_count = invited_count + 1, updated_at = :updated_at 
             WHERE id = :id'
        );
        
        $stmt->execute([
            ':updated_at' => $now,
            ':id' => $this->id,
        ]);
        
        $this->invitedCount++;
    }

    /**
     * Thêm vào tổng thưởng kiếm được từ giới thiệu
     */
    public function addRewards(int $amount): void
    {
        /** @var PDO $db */
        $db = Container::get('db');
        
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        
        $stmt = $db->prepare(
            'UPDATE user_invites 
             SET total_rewards = total_rewards + :amount, updated_at = :updated_at 
             WHERE id = :id'
        );
        
        $stmt->execute([
            ':amount' => $amount,
            ':updated_at' => $now,
            ':id' => $this->id,
        ]);
        
        $this->totalRewards += $amount;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getInviteCode(): string
    {
        return $this->inviteCode;
    }

    public function getInviteLink(): string
    {
        return $this->inviteLink;
    }

    public function getInvitedCount(): int
    {
        return $this->invitedCount;
    }

    public function getTotalRewards(): int
    {
        return $this->totalRewards;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function getInviteToken(): ?string
    {
        return $this->inviteToken;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'invite_code' => $this->inviteCode,
            'invite_token' => $this->inviteToken,
            'invite_link' => $this->inviteLink,
            'invited_count' => $this->invitedCount,
            'total_rewards' => $this->totalRewards,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
