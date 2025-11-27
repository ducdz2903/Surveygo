<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class Feedback
{
    private int $id;
    private string $ma;
    private int $idKhaoSat;
    private ?int $idNguoiDung;
    private ?string $tenNguoiDung;
    private int $danhGia;
    private ?string $binhLuan;
    private DateTime|string $createdAt;
    private DateTime|string $updatedAt;

    public function __construct(array $row)
    {
        $this->id = (int) ($row['id'] ?? 0);
        $this->ma = $row['ma'] ?? '';
        $this->idKhaoSat = (int) ($row['idKhaoSat'] ?? 0);
        $this->idNguoiDung = (int) ($row['idNguoiDung'] ?? 0);
        $this->tenNguoiDung = (string) ($row['tenNguoiDung'] ?? '');
        $this->danhGia = (int) ($row['danhGia'] ?? 0);
        $this->binhLuan = $row['binhLuan'] ?? null;
        $this->createdAt = $row['created_at'] ?? '';
        $this->updatedAt = $row['updated_at'] ?? '';
    }

    // tạo mới phản hồi
    public static function create(array $data): ?self
    {
        $db = Container::get('db');

        $ma = $data['ma'] ?? ('FB' . str_pad((string) rand(1, 9999), 3, '0', STR_PAD_LEFT));
        $idKhaoSat = (int) ($data['idKhaoSat '] ?? 0);
        $idNguoiDung = (int) ($data['idNguoiDung '] ?? 0);
        $tenNguoiDung = (string) ($data['tenNguoiDung'] ?? '');
        $danhGia = (int) ($data['danhGia'] ?? 0);
        $binhLuan = $data['binhLuan'] ?? null;
        $sql = 'INSERT INTO feedbacks (ma, idKhaoSat , idNguoiDung , tenNguoiDung, danhGia, binhLuan, created_at, updated_at)
                VALUES (:ma, :idKhaoSat , :idNguoiDung , :tenNguoiDung, :danhGia, :binhLuan, NOW(), NOW())';

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':ma', $ma);
        $stmt->bindParam(':idKhaoSat ', $idKhaoSat);
        $stmt->bindParam(':idNguoiDung ', $idNguoiDung);
        $stmt->bindParam(':tenNguoiDung', $tenNguoiDung);
        $stmt->bindParam(':danhGia', $danhGia);
        $stmt->bindParam(':binhLuan', $binhLuan);
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
            $where[] = '(ma LIKE :search OR tenNguoiDung LIKE :search)';
            $params[':search'] = '%' . $filter['search'] . '%';
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countSql = 'SELECT COUNT(*) as total FROM feedbacks ' . $whereSql;
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();
        $sql = 'SELECT * FROM feedbacks ' . $whereSql . ' ORDER BY created_at DESC LIMIT :offset, :limit';
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $feedbacks = array_map(fn($r) => new self($r), $rows);
        $totalPages = (int) ceil($total / $limit);

        return [
            'feedbacks' => $feedbacks,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages,
        ];
    }

    // lấy thông tin chi tiết phản hồi theo id 
    public static function findById(int $id): ?self
    {
        $db = Container::get('db');

        $sql = 'SELECT * FROM feedbacks WHERE id = :id LIMIT 1';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return new self($row);
    }

    // cập nhật (instance)
    public function update(array $data): bool
    {
        $db = Container::get('db');

        $sql = 'UPDATE feedbacks SET ma = :ma, idKhaoSat = :idKhaoSat, idNguoiDung = :idNguoiDung, tenNguoiDung = :tenNguoiDung, danhGia = :danhGia, binhLuan = :binhLuan, updated_at = NOW() WHERE id = :id';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':ma', $data['ma'] ?? $this->ma);
        $stmt->bindValue(':idKhaoSat', isset($data['idKhaoSat']) ? (int) $data['idKhaoSat'] : $this->idKhaoSat, PDO::PARAM_INT);
        $stmt->bindValue(':idNguoiDung', isset($data['idNguoiDung']) ? (int) $data['idNguoiDung'] : $this->idNguoiDung, PDO::PARAM_INT);
        $stmt->bindValue(':tenNguoiDung', $data['tenNguoiDung'] ?? $this->tenNguoiDung);
        $stmt->bindValue(':danhGia', isset($data['danhGia']) ? (int) $data['danhGia'] : $this->danhGia, PDO::PARAM_INT);
        $stmt->bindValue(':binhLuan', $data['binhLuan'] ?? $this->binhLuan);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $ok = $stmt->execute();
        if ($ok) {
            $fresh = self::findById($this->id);
            if ($fresh) {
                $this->ma = $fresh->ma;
                $this->idKhaoSat = $fresh->idKhaoSat;
                $this->idNguoiDung = $fresh->idNguoiDung;
                $this->tenNguoiDung = $fresh->tenNguoiDung;
                $this->danhGia = $fresh->danhGia;
                $this->binhLuan = $fresh->binhLuan;
                $this->updatedAt = $fresh->updatedAt;
            }
        }

        return (bool) $ok;
    }

    // xóa
    public function delete(): bool
    {
        $db = Container::get('db');
        $stmt = $db->prepare('DELETE FROM feedbacks WHERE id = :id');
        return (bool) $stmt->execute([':id' => $this->id]);
    }

    // static wrapper update by id
    public static function updateById(int $id, array $data): ?self
    {
        $fb = self::findById($id);
        if (!$fb) {
            return null;
        }
        $ok = $fb->update($data);
        return $ok ? self::findById($id) : null;
    }

    // getters
    public function getId(): int
    {
        return $this->id;
    }
    public function getMa(): string
    {
        return $this->ma;
    }
    public function getIdKhaoSat(): int
    {
        return $this->idKhaoSat;
    }
    public function getIdNguoiDung(): int
    {
        return $this->idNguoiDung;
    }
    public function getTenNguoiDung(): string
    {
        return $this->tenNguoiDung;
    }
    public function getDanhGia(): int
    {
        return $this->danhGia;
    }
    public function getBinhLuan(): ?string
    {
        return $this->binhLuan;
    }
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ma' => $this->ma,
            'idKhaoSat' => $this->idKhaoSat,
            'idNguoiDung' => $this->idNguoiDung,
            'tenNguoiDung' => $this->tenNguoiDung,
            'danhGia' => $this->danhGia,
            'binhLuan' => $this->binhLuan,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

}