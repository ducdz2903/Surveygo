<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class User
{
    private int $id;
    private string $name;
    private string $email;
    private string $password;
    private ?string $phone;
    private ?string $gender;
    private ?string $avatar;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $attributes)
    {
        $this->id = (int)($attributes['id'] ?? 0);
        $this->name = $attributes['name'];
        $this->email = $attributes['email'];
        $this->password = $attributes['password'];
        $this->phone = $attributes['phone'] ?? null;
        $this->gender = $attributes['gender'] ?? null;
        $this->avatar = $attributes['avatar'] ?? null;
        $this->createdAt = $attributes['created_at'];
        $this->updatedAt = $attributes['updated_at'];
    }

    public static function create(string $name, string $email, string $hashedPassword): self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $statement = $db->prepare('INSERT INTO users (name, email, password, created_at, updated_at) VALUES (:name, :email, :password, :created_at, :updated_at)');
        $statement->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);

        $id = (int)$db->lastInsertId();

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'avatar' => $this->avatar,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}

