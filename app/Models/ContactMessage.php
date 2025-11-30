<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class ContactMessage
{
    private int $id;
    private string $ma;
    private string $hoTen;
    private string $email;
    private ?string $soDienThoai;
    private string $chuDe;
    private string $tinNhan;
    private ?int $idNguoiDung;
    private ?string $phanHoi;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $row)
    {
        $this->id = (int) ($row['id'] ?? 0);
        $this->ma = $row['ma'] ?? '';
        $this->hoTen = $row['hoTen'] ?? '';
        $this->email = $row['email'] ?? '';
        $this->soDienThoai = $row['soDienThoai'] ?? null;
        $this->chuDe = $row['chuDe'] ?? '';
        $this->tinNhan = $row['tinNhan'] ?? '';
        $this->idNguoiDung = isset($row['idNguoiDung']) && $row['idNguoiDung'] !== null ? (int) $row['idNguoiDung'] : null;
        $this->phanHoi = $row['phanHoi'] ?? null;
        $this->createdAt = $row['created_at'] ?? '';
        $this->updatedAt = $row['updated_at'] ?? '';
    }

    public static function create(array $data): ?self
    {
        $db = Container::get('db');

        $ma = $data['ma'] ?? ('CM' . str_pad((string) rand(1, 9999), 3, '0', STR_PAD_LEFT));
        $hoTen = trim((string) ($data['hoTen'] ?? ''));
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $soDienThoai = $data['soDienThoai'] ?? null;
        $chuDe = trim((string) ($data['chuDe'] ?? ''));
        $tinNhan = trim((string) ($data['tinNhan'] ?? ''));
        $idNguoiDung = isset($data['idNguoiDung']) ? (int) $data['idNguoiDung'] : null;

        $sql = 'INSERT INTO contact_messages (ma, hoTen, email, soDienThoai, chuDe, tinNhan, idNguoiDung, created_at, updated_at)
                VALUES (:ma, :hoTen, :email, :soDienThoai, :chuDe, :tinNhan, :idNguoiDung, NOW(), NOW())';

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':ma', $ma);
        $stmt->bindParam(':hoTen', $hoTen);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':soDienThoai', $soDienThoai);
        $stmt->bindParam(':chuDe', $chuDe);
        $stmt->bindParam(':tinNhan', $tinNhan);
        $stmt->bindValue(':idNguoiDung', $idNguoiDung, $idNguoiDung === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $id = (int) $db->lastInsertId();
        return self::findById($id);
    }

    public static function paginate(int $page = 1, int $limit = 10, array $filter = []): array
    {
        $db = Container::get('db');

        $page = max(1, (int) $page);
        $limit = max(1, min(100, (int) $limit));
        $offset = ($page - 1) * $limit;

        $where = [];
        $params = [];

        if (!empty($filter['ma'])) {
            $where[] = 'ma = :ma';
            $params[':ma'] = $filter['ma'];
        }

        if (!empty($filter['search'])) {
            $where[] = '(hoTen LIKE :search OR email LIKE :search OR chuDe LIKE :search)';
            $params[':search'] = '%' . $filter['search'] . '%';
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countSql = 'SELECT COUNT(*) as total FROM contact_messages ' . $whereSql;
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = 'SELECT * FROM contact_messages ' . $whereSql . ' ORDER BY created_at DESC LIMIT :offset, :limit';
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $items = array_map(fn($r) => new self($r), $rows);
        $totalPages = (int) ceil($total / $limit);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages,
        ];
    }

    public static function findById(int $id): ?self
    {
        $db = Container::get('db');
        $sql = 'SELECT * FROM contact_messages WHERE id = :id LIMIT 1';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new self($row);
    }

    public function update(array $data): bool
    {
        $db = Container::get('db');
        $sql = 'UPDATE contact_messages SET ma = :ma, hoTen = :hoTen, email = :email, soDienThoai = :soDienThoai, chuDe = :chuDe, tinNhan = :tinNhan, idNguoiDung = :idNguoiDung, phanHoi = :phanHoi, updated_at = NOW() WHERE id = :id';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':ma', $data['ma'] ?? $this->ma);
        $stmt->bindValue(':hoTen', $data['hoTen'] ?? $this->hoTen);
        $stmt->bindValue(':email', $data['email'] ?? $this->email);
        $stmt->bindValue(':soDienThoai', $data['soDienThoai'] ?? $this->soDienThoai);
        $stmt->bindValue(':chuDe', $data['chuDe'] ?? $this->chuDe);
        $stmt->bindValue(':tinNhan', $data['tinNhan'] ?? $this->tinNhan);
        $stmt->bindValue(':phanHoi', $data['phanHoi'] ?? $this->phanHoi);
        $stmt->bindValue(':idNguoiDung', isset($data['idNguoiDung']) ? (int) $data['idNguoiDung'] : $this->idNguoiDung, PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $ok = $stmt->execute();
        if ($ok) {
            $fresh = self::findById($this->id);
            if ($fresh) {
                $this->ma = $fresh->ma;
                $this->hoTen = $fresh->hoTen;
                $this->email = $fresh->email;
                $this->soDienThoai = $fresh->soDienThoai;
                $this->chuDe = $fresh->chuDe;
                $this->tinNhan = $fresh->tinNhan;
                $this->idNguoiDung = $fresh->idNguoiDung;
                $this->phanHoi = $fresh->phanHoi;
                $this->updatedAt = $fresh->updatedAt;
            }
        }

        return (bool) $ok;
    }

    public function delete(): bool
    {
        $db = Container::get('db');
        $stmt = $db->prepare('DELETE FROM contact_messages WHERE id = :id');
        return (bool) $stmt->execute([':id' => $this->id]);
    }

    public static function updateById(int $id, array $data): ?self
    {
        $m = self::findById($id);
        if (!$m) {
            return null;
        }
        $ok = $m->update($data);
        return $ok ? self::findById($id) : null;
    }

    // getters
    public function getId(): int { return $this->id; }
    public function getMa(): string { return $this->ma; }
    public function getHoTen(): string { return $this->hoTen; }
    public function getEmail(): string { return $this->email; }
    public function getSoDienThoai(): ?string { return $this->soDienThoai; }
    public function getChuDe(): string { return $this->chuDe; }
    public function getTinNhan(): string { return $this->tinNhan; }
    public function getIdNguoiDung(): ?int { return $this->idNguoiDung; }
    public function getPhanHoi(): ?string { return $this->phanHoi; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ma' => $this->ma,
            'hoTen' => $this->hoTen,
            'email' => $this->email,
            'soDienThoai' => $this->soDienThoai,
            'chuDe' => $this->chuDe,
            'tinNhan' => $this->tinNhan,
            'idNguoiDung' => $this->idNguoiDung,
            'phanHoi' => $this->phanHoi,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

}