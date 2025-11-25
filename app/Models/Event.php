<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class Event
{
    private int $id;
    private string $maSuKien;
    private string $tenSuKien;
    private ?string $thoiGianBatDau;
    private ?string $thoiGianKetThuc;
    private string $trangThai;
    private int $soNguoiThamGia;
    private int $soKhaoSat;
    private ?string $diaDiem;
    private int $maNguoiTao;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $row)
    {
        $this->id = (int) ($row['id'] ?? 0);
        $this->maSuKien = $row['maSuKien'] ?? '';
        $this->tenSuKien = $row['tenSuKien'] ?? '';
        $this->thoiGianBatDau = $row['thoiGianBatDau'] ?? null;
        $this->thoiGianKetThuc = $row['thoiGianKetThuc'] ?? null;
        $this->trangThai = $row['trangThai'] ?? 'upcoming';
        $this->soNguoiThamGia = isset($row['soNguoiThamGia']) ? (int) $row['soNguoiThamGia'] : 0;
        $this->soKhaoSat = isset($row['soKhaoSat']) ? (int) $row['soKhaoSat'] : 0;
        $this->diaDiem = $row['diaDiem'] ?? null;
        $this->maNguoiTao = isset($row['maNguoiTao']) ? (int) $row['maNguoiTao'] : 0;
        $this->createdAt = $row['created_at'] ?? '';
        $this->updatedAt = $row['updated_at'] ?? '';
    }

    /**
     * Tạo sự kiện mới
     */
    public static function create(array $data): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $ma = $data['maSuKien'] ?? ('SK' . str_pad((string) rand(1, 9999), 3, '0', STR_PAD_LEFT));
        $ten = $data['tenSuKien'] ?? '';
        $start = $data['thoiGianBatDau'] ?? null;
        $end = $data['thoiGianKetThuc'] ?? null;
        $trangThai = $data['trangThai'] ?? 'upcoming';
        $soNguoi = (int) ($data['soNguoiThamGia'] ?? 0);
        $soKhaoSat = (int) ($data['soKhaoSat'] ?? 0);
        $diaDiem = $data['diaDiem'] ?? null;
        $maNguoiTao = (int) ($data['maNguoiTao'] ?? 0);

        $sql = 'INSERT INTO events (maSuKien, tenSuKien, thoiGianBatDau, thoiGianKetThuc, trangThai, soNguoiThamGia, soKhaoSat, diaDiem, maNguoiTao, created_at, updated_at)
                VALUES (:ma, :ten, :start, :end, :trangThai, :songuoi, :sokhaosat, :diadiem, :maNguoiTao, NOW(), NOW())';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':ma', $ma);
        $stmt->bindValue(':ten', $ten);
        $stmt->bindValue(':start', $start);
        $stmt->bindValue(':end', $end);
        $stmt->bindValue(':trangThai', $trangThai);
        $stmt->bindValue(':songuoi', $soNguoi, PDO::PARAM_INT);
        $stmt->bindValue(':sokhaosat', $soKhaoSat, PDO::PARAM_INT);
        $stmt->bindValue(':diadiem', $diaDiem);
        $stmt->bindValue(':maNguoiTao', $maNguoiTao, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            return null;
        }

        $id = (int) $db->lastInsertId();
        return self::find($id);
    }

    public static function find(int $id): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $stmt = $db->prepare('SELECT * FROM events WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? new self($row) : null;
    }

    public static function paginate(int $page = 1, int $limit = 10, array $filters = []): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $page = max(1, (int) $page);
        $limit = max(1, min(100, (int) $limit));
        $offset = ($page - 1) * $limit;

        $where = [];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = '(tenSuKien LIKE :search OR diaDiem LIKE :search)';
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['trangThai'])) {
            $where[] = 'trangThai = :trangThai';
            $params[':trangThai'] = $filters['trangThai'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countSql = "SELECT COUNT(*) as total FROM events {$whereSql}";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()['total'];

        $sql = "SELECT * FROM events {$whereSql} ORDER BY created_at DESC LIMIT :offset, :limit";
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $events = array_map(fn($r) => new self($r), $rows);
        $totalPages = (int) ceil($total / $limit);

        return [
            'events' => $events,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages,
        ];
    }

    public function update(array $data): bool
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $sql = 'UPDATE events SET tenSuKien = :ten, thoiGianBatDau = :start, thoiGianKetThuc = :end, trangThai = :trangThai, soNguoiThamGia = :songuoi, soKhaoSat = :sokhaosat, diaDiem = :diadiem, updated_at = NOW() WHERE id = :id';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':ten', $data['tenSuKien'] ?? $this->tenSuKien);
        $stmt->bindValue(':start', $data['thoiGianBatDau'] ?? $this->thoiGianBatDau);
        $stmt->bindValue(':end', $data['thoiGianKetThuc'] ?? $this->thoiGianKetThuc);
        $stmt->bindValue(':trangThai', $data['trangThai'] ?? $this->trangThai);
        $stmt->bindValue(':songuoi', isset($data['soNguoiThamGia']) ? (int) $data['soNguoiThamGia'] : $this->soNguoiThamGia, PDO::PARAM_INT);
        $stmt->bindValue(':sokhaosat', isset($data['soKhaoSat']) ? (int) $data['soKhaoSat'] : $this->soKhaoSat, PDO::PARAM_INT);
        $stmt->bindValue(':diadiem', $data['diaDiem'] ?? $this->diaDiem);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $ok = $stmt->execute();
        if ($ok) {
            // refresh object
            $fresh = self::find($this->id);
            if ($fresh) {
                $this->tenSuKien = $fresh->tenSuKien;
                $this->thoiGianBatDau = $fresh->thoiGianBatDau;
                $this->thoiGianKetThuc = $fresh->thoiGianKetThuc;
                $this->trangThai = $fresh->trangThai;
                $this->soNguoiThamGia = $fresh->soNguoiThamGia;
                $this->soKhaoSat = $fresh->soKhaoSat;
                $this->diaDiem = $fresh->diaDiem;
                $this->updatedAt = $fresh->updatedAt;
            }
        }
        return $ok;
    }

    public function delete(): bool
    {
        /** @var PDO $db */
        $db = Container::get('db');
        $stmt = $db->prepare('DELETE FROM events WHERE id = :id');
        return $stmt->execute([':id' => $this->id]);
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }
    public function getMaSuKien(): string
    {
        return $this->maSuKien;
    }
    public function getTenSuKien(): string
    {
        return $this->tenSuKien;
    }
    public function getThoiGianBatDau(): ?string
    {
        return $this->thoiGianBatDau;
    }
    public function getThoiGianKetThuc(): ?string
    {
        return $this->thoiGianKetThuc;
    }
    public function getTrangThai(): string
    {
        return $this->trangThai;
    }
    public function getSoNguoiThamGia(): int
    {
        return $this->soNguoiThamGia;
    }
    public function getSoKhaoSat(): int
    {
        return $this->soKhaoSat;
    }
    public function getDiaDiem(): ?string
    {
        return $this->diaDiem;
    }
    public function getMaNguoiTao(): int
    {
        return $this->maNguoiTao;
    }
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}