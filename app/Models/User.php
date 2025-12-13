<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class User
{
    private int $id;
    private string $code;
    private string $name;
    private ?string $avatar;
    private string $email;
    private ?string $phone;
    private string $password;
    private ?string $gender;
    private string $role;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $attributes)
    {
        $this->id = (int) ($attributes['id'] ?? 0);
        $this->code = (string) ($attributes['code'] ?? '');
        $this->name = $attributes['name'];
        $this->avatar = $attributes['avatar'] ?? null;
        $this->email = $attributes['email'];
        $this->phone = $attributes['phone'] ?? null;
        $this->password = $attributes['password'];
        $this->gender = $attributes['gender'];
        $this->role = $attributes['role'];
        $this->createdAt = $attributes['created_at'];
        $this->updatedAt = $attributes['updated_at'];
    }

    public static function create(array $attributes): self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $statement = $db->prepare('INSERT INTO users (name, avatar, email, phone, password, gender, role, code, created_at, updated_at) VALUES (:name, :avatar, :email, :phone, :password, :gender, :role, :code, :created_at, :updated_at)');
        $statement->execute([
            ':name' => $attributes['name'],
            ':avatar' => $attributes['avatar'] ?? '',
            ':email' => $attributes['email'],
            ':phone' => $attributes['phone'] ?? null,
            ':password' => $attributes['password'],
            ':gender' => $attributes['gender'] ?? 'other',
            ':role' => $attributes['role'] ?? 'user',
            ':code' => '',
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);

        $id = (int) $db->lastInsertId();

        if ($id > 0) {
            $code = 'US' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
            $upd = $db->prepare('UPDATE users SET code = :code WHERE id = :id');
            $upd->execute([':code' => $code, ':id' => $id]);
        }

        return self::findById($id);
    }

    public static function findByEmail(string $email): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute([':email' => $email]);
        $row = $statement->fetch();

        if (!$row) {
            return null;
        }

        return new self($row);
    }

    public static function findById(int $id): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $statement->execute([':id' => $id]);
        $row = $statement->fetch();

        if (!$row) {
            return null;
        }

        return new self($row);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'avatar' => $this->avatar,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'role' => $this->role,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }


    // hàm phân trang người dùng
    public static function paginate(int $page = 1, int $limit = 10, array $filters = []): array
    {
        /** @var \PDO $db */
        $db = Container::get('db');

        $page = max(1, (int) $page);
        $limit = max(1, min(100, (int) $limit));
        $offset = ($page - 1) * $limit;

        $where = [];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = '(name LIKE :search OR email LIKE :search OR phone LIKE :search)';
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['role'])) {
            $where[] = 'role = :role';
            $params[':role'] = $filters['role'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countSql = "SELECT COUNT(*) as total FROM users {$whereSql}";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()['total'];

        $sql = "SELECT * FROM users {$whereSql} ORDER BY created_at DESC LIMIT :offset, :limit";
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $users = array_map(fn($r) => new self($r), $rows);
        $totalPages = (int) ceil($total / $limit);

        return [
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages,
        ];
    }

    public function updatePassword(string $newHashedPassword): void
    {
        /** @var \PDO $db */
        $db = Container::get('db');
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $stmt = $db->prepare('UPDATE users SET password = :password, updated_at = :updated_at WHERE id = :id');
        $stmt->execute([':password' => $newHashedPassword, ':updated_at' => $now, ':id' => $this->id]);
    }

    /**
     * Get user statistics including total, monthly growth, and new users
     * 
     * @return array Statistics data
     */
    public static function getUserStatistics(): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        // Get total users
        $totalStmt = $db->query('SELECT COUNT(*) as total FROM users');
        $totalUsers = (int) $totalStmt->fetch()['total'];

        // Get current month date range
        $currentMonthStart = date('Y-m-01 00:00:00');
        $currentMonthEnd = date('Y-m-t 23:59:59');

        // Get previous month date range
        $previousMonthStart = date('Y-m-01 00:00:00', strtotime('first day of last month'));
        $previousMonthEnd = date('Y-m-t 23:59:59', strtotime('last day of last month'));

        // Count new users in current month
        $currentMonthStmt = $db->prepare(
            'SELECT COUNT(*) as count FROM users WHERE created_at >= :start AND created_at <= :end'
        );
        $currentMonthStmt->execute([
            ':start' => $currentMonthStart,
            ':end' => $currentMonthEnd,
        ]);
        $newUsersThisMonth = (int) $currentMonthStmt->fetch()['count'];

        // Count new users in previous month
        $previousMonthStmt = $db->prepare(
            'SELECT COUNT(*) as count FROM users WHERE created_at >= :start AND created_at <= :end'
        );
        $previousMonthStmt->execute([
            ':start' => $previousMonthStart,
            ':end' => $previousMonthEnd,
        ]);
        $newUsersPreviousMonth = (int) $previousMonthStmt->fetch()['count'];

        // Calculate monthly growth percentage
        $growthPercentage = 0.0;
        if ($newUsersPreviousMonth > 0) {
            $growthPercentage = (($newUsersThisMonth - $newUsersPreviousMonth) / $newUsersPreviousMonth) * 100;
        } elseif ($newUsersThisMonth > 0) {
            $growthPercentage = 100.0; // If no users last month but have users this month
        }

        return [
            'total_users' => $totalUsers,
            'new_users_this_month' => $newUsersThisMonth,
            'new_users_previous_month' => $newUsersPreviousMonth,
            'growth_percentage' => round($growthPercentage, 1),
            'is_growth_positive' => $growthPercentage >= 0,
        ];
    }

    /**
 * Get top active users by completed surveys count
 * 
 * @param int $limit Number of users to return
 * @return array Array of users with completed_surveys_count and created_surveys_count
 */
public static function getTopActiveUsers(int $limit = 5): array
{
    /** @var PDO $db */
    $db = Container::get('db');

    $sql = "
        SELECT 
            u.id,
            u.code,
            u.name,
            u.avatar,
            u.email,
            u.created_at,
            COUNT(DISTINCT ss.id) as completed_surveys_count,
            COUNT(DISTINCT s.id) as created_surveys_count
        FROM users u
        LEFT JOIN survey_submissions ss ON u.id = ss.maNguoiDung
        LEFT JOIN surveys s ON u.id = s.maNguoiTao
        GROUP BY u.id
        HAVING completed_surveys_count > 0
        ORDER BY completed_surveys_count DESC, created_surveys_count DESC
        LIMIT :limit
    ";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(function($row) {
        return [
            'id' => (int) $row['id'],
            'code' => $row['code'],
            'name' => $row['name'],
            'avatar' => $row['avatar'],
            'email' => $row['email'],
            'created_at' => $row['created_at'],
            'completed_surveys_count' => (int) $row['completed_surveys_count'],
            'created_surveys_count' => (int) $row['created_surveys_count'],
        ];
    }, $results);
}
}
