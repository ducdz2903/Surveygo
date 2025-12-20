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
    private int $soLuotRutThamMoiLan;
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
        // Ưu tiên trường đếm động (nếu có alias từ SQL), fallback về cột soKhaoSat
        if (isset($row['survey_count'])) {
            $this->soKhaoSat = (int) $row['survey_count'];
        } elseif (isset($row['soKhaoSat'])) {
            $this->soKhaoSat = (int) $row['soKhaoSat'];
        } else {
            $this->soKhaoSat = 0;
        }
        $this->soLuotRutThamMoiLan = isset($row['soLuotRutThamMoiLan']) ? (int) $row['soLuotRutThamMoiLan'] : 0;
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
        // soKhaoSat hiện được tính động từ bảng surveys, không cần lưu tay
        $soKhaoSat = 0;
        $soLuotRutThamMoiLan = (int) ($data['soLuotRutThamMoiLan'] ?? 0);
        $diaDiem = $data['diaDiem'] ?? null;
        $maNguoiTao = (int) ($data['maNguoiTao'] ?? 0);

        $sql = 'INSERT INTO events (maSuKien, tenSuKien, thoiGianBatDau, thoiGianKetThuc, trangThai, soNguoiThamGia, soKhaoSat, soLuotRutThamMoiLan, diaDiem, maNguoiTao, created_at, updated_at)
                VALUES (:ma, :ten, :start, :end, :trangThai, :songuoi, :sokhaosat, :soLuotRutThamMoiLan, :diadiem, :maNguoiTao, NOW(), NOW())';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':ma', $ma);
        $stmt->bindValue(':ten', $ten);
        $stmt->bindValue(':start', $start);
        $stmt->bindValue(':end', $end);
        $stmt->bindValue(':trangThai', $trangThai);
        $stmt->bindValue(':songuoi', $soNguoi, PDO::PARAM_INT);
        $stmt->bindValue(':sokhaosat', $soKhaoSat, PDO::PARAM_INT);
        $stmt->bindValue(':soLuotRutThamMoiLan', $soLuotRutThamMoiLan, PDO::PARAM_INT);
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

        $sql = 'SELECT e.*, 
                       (SELECT COUNT(*) FROM surveys s WHERE s.maSuKien = e.id) AS survey_count
                FROM events e
                WHERE e.id = :id
                LIMIT 1';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
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

        // Đếm số khảo sát gắn với mỗi sự kiện dựa trên surveys.maSuKien
        $sql = "SELECT e.*, 
                       (SELECT COUNT(*) FROM surveys s WHERE s.maSuKien = e.id) AS survey_count
                FROM events e
                {$whereSql}
                ORDER BY e.created_at DESC
                LIMIT :offset, :limit";
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

        // soKhaoSat được tính động từ bảng surveys, không cập nhật trực tiếp nữa
        $sql = 'UPDATE events 
                SET tenSuKien = :ten,
                    thoiGianBatDau = :start,
                    thoiGianKetThuc = :end,
                    trangThai = :trangThai,
                    soNguoiThamGia = :songuoi,
                    soLuotRutThamMoiLan = :soLuotRutThamMoiLan,
                    diaDiem = :diadiem,
                    updated_at = NOW()
                WHERE id = :id';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':ten', $data['tenSuKien'] ?? $this->tenSuKien);
        $stmt->bindValue(':start', $data['thoiGianBatDau'] ?? $this->thoiGianBatDau);
        $stmt->bindValue(':end', $data['thoiGianKetThuc'] ?? $this->thoiGianKetThuc);
        $stmt->bindValue(':trangThai', $data['trangThai'] ?? $this->trangThai);
        $stmt->bindValue(':songuoi', isset($data['soNguoiThamGia']) ? (int) $data['soNguoiThamGia'] : $this->soNguoiThamGia, PDO::PARAM_INT);
        $stmt->bindValue(
            ':soLuotRutThamMoiLan',
            isset($data['soLuotRutThamMoiLan']) ? (int) $data['soLuotRutThamMoiLan'] : $this->soLuotRutThamMoiLan,
            PDO::PARAM_INT
        );
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
                $this->soLuotRutThamMoiLan = $fresh->soLuotRutThamMoiLan;
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
    public function getSoLuotRutThamMoiLan(): int
    {
        return $this->soLuotRutThamMoiLan;
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
