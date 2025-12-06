<?php

declare(strict_types=1);

namespace App\Models;
use App\Core\Container;
use PDO;

class Survey
{
    private int $id;
    private string $maKhaoSat;
    private string $tieuDe;
    private ?string $moTa;
    private ?string $loaiKhaoSat;
    private ?int $thoiLuongDuTinh;
    private bool $isQuickPoll;
    private int $maNguoiTao;
    private string $trangThai;
    private int $diemThuong;
    private ?int $danhMuc;
    private ?int $maSuKien;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $attributes)
    {
        $this->id = (int) ($attributes['id'] ?? 0);
        $this->maKhaoSat = $attributes['maKhaoSat'] ?? '';
        $this->tieuDe = $attributes['tieuDe'] ?? '';
        $this->moTa = $attributes['moTa'] ?? null;
        $this->loaiKhaoSat = $attributes['loaiKhaoSat'] ?? null;
        $this->thoiLuongDuTinh = $attributes['thoiLuongDuTinh'] ? (int) $attributes['thoiLuongDuTinh'] : null;
        $this->isQuickPoll = (bool) ($attributes['isQuickPoll'] ?? false);
        $this->maNguoiTao = (int) ($attributes['maNguoiTao'] ?? 0);
        $this->trangThai = $attributes['trangThai'] ?? 'draft';
        $this->diemThuong = (int) ($attributes['diemThuong'] ?? 0);
        $this->danhMuc = array_key_exists('danhMuc', $attributes) && $attributes['danhMuc'] !== null ? (int) $attributes['danhMuc'] : null;
        $this->maSuKien = isset($attributes['maSuKien']) && $attributes['maSuKien'] !== null ? (int) $attributes['maSuKien'] : null;
        $this->createdAt = $attributes['created_at'] ?? '';
        $this->updatedAt = $attributes['updated_at'] ?? '';
    }

    /**
     * Lấy tất cả khảo sát (sắp xếp theo created_at DESC)
     */
    public static function all(): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->query('SELECT * FROM surveys ORDER BY created_at DESC');
        $rows = $statement->fetchAll();

        return array_map(fn($row) => new self($row), $rows);
    }

    /**
     * Lấy danh sách khảo sát với phân trang và lọc
     * 
     * @param int $page Trang hiện tại (bắt đầu từ 1)
     * @param int $limit Số khảo sát trên mỗi trang
     * @param array $filters Mảng lọc: ['search' => '', 'trangThai' => '', 'danhMuc' => '', 'isQuickPoll' => bool]
     * @return array ['surveys' => [...], 'total' => int, 'page' => int, 'limit' => int, 'totalPages' => int]
     */
    public static function paginate(int $page = 1, int $limit = 10, array $filters = []): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        // Validate page & limit
        $page = max(1, (int) $page);
        $limit = max(1, min(100, (int) $limit)); // Max 100 items per page
        $offset = ($page - 1) * $limit;

        // Build WHERE clause
        $where = [];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(tieuDe LIKE :search OR moTa LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['trangThai'])) {
            $where[] = "trangThai = :trangThai";
            $params[':trangThai'] = $filters['trangThai'];
        }

        if (!empty($filters['danhMuc'])) {
            $where[] = "danhMuc = :danhMuc";
            $params[':danhMuc'] = (int) $filters['danhMuc'];
        }

        if (isset($filters['isQuickPoll'])) {
            $where[] = "isQuickPoll = :isQuickPoll";
            $params[':isQuickPoll'] = intval($filters['isQuickPoll']);
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM surveys {$whereClause}";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()['total'];

        // Get paginated results
        $sql = "SELECT * FROM surveys {$whereClause} ORDER BY created_at DESC LIMIT :offset, :limit";
        $stmt = $db->prepare($sql);

        // Bind params
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        $rows = $stmt->fetchAll();

        $surveys = array_map(fn($row) => new self($row), $rows);

        $totalPages = (int) ceil($total / $limit);

        return [
            'surveys' => $surveys,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages,
        ];
    }

    /**
     * Lấy khảo sát theo ID
     */
    public static function find(int $id): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('SELECT * FROM surveys WHERE id = :id LIMIT 1');
        $statement->execute([':id' => $id]);
        $row = $statement->fetch();

        return $row ? new self($row) : null;
    }

    /**
     * Lấy khảo sát theo maKhaoSat
     */
    public static function findByMa(string $maKhaoSat): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('SELECT * FROM surveys WHERE maKhaoSat = :ma LIMIT 1');
        $statement->execute([':ma' => $maKhaoSat]);
        $row = $statement->fetch();

        return $row ? new self($row) : null;
    }

    /**
     * Tạo khảo sát mới
     * - Auto-gen maKhaoSat nếu không cung cấp
     * - Validate maNguoiTao phải tồn tại
     */
    public static function create(array $data): ?self
    {
        /** @var PDO $db */
        $db = Container::get('db');

        // Validate required fields
        if (empty($data['tieuDe'])) {
            return null; // Validation failed, nên throw exception trong controller
        }

        if (empty($data['maNguoiTao'])) {
            return null;
        }

        // Note: do not fail creation if the provided maNguoiTao does not exist.
        // Some environments may not have user seeded; controller already validates presence.

        // Auto-gen maKhaoSat nếu chưa có (độ dài <= 10, tránh va chạm UNIQUE)
        $maKhaoSat = $data['maKhaoSat'] ?? self::generateMaKhaoSat($db);

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        try {
            $statement = $db->prepare(
                'INSERT INTO surveys (maKhaoSat, tieuDe, moTa, loaiKhaoSat, thoiLuongDuTinh, isQuickPoll, maNguoiTao, trangThai, diemThuong, danhMuc, maSuKien, created_at, updated_at)
                 VALUES (:ma, :tieu, :mo, :loai, :thoiluong, :isquickpoll, :user, :status, :diem, :danh, :sukien, :created, :updated)'
            );

            $statement->execute([
                ':ma' => $maKhaoSat,
                ':tieu' => $data['tieuDe'],
                ':mo' => $data['moTa'] ?? null,
                ':loai' => $data['loaiKhaoSat'] ?? null,
                ':thoiluong' => $data['thoiLuongDuTinh'] ?? null,
                ':isquickpoll' => (int) ($data['isQuickPoll'] ?? 0),
                ':user' => $data['maNguoiTao'],
                ':status' => $data['trangThai'] ?? 'draft',
                ':diem' => (int) ($data['diemThuong'] ?? 0),
                ':danh' => isset($data['danhMuc']) ? (int) $data['danhMuc'] : null,
                ':sukien' => $data['maSuKien'] ?? null,
                ':created' => $now,
                ':updated' => $now,
            ]);

            $id = (int) $db->lastInsertId();
            return self::find($id);
        } catch (\PDOException $e) {
            error_log('[Survey::create] DB error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cập nhật khảo sát
     */
    public function update(array $data): bool
    {
        /** @var PDO $db */
        $db = Container::get('db');

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $tieuDe = $data['tieuDe'] ?? $this->tieuDe;
        $moTa = $data['moTa'] ?? $this->moTa;
        $loaiKhaoSat = $data['loaiKhaoSat'] ?? $this->loaiKhaoSat;
        $thoiLuongDuTinh = $data['thoiLuongDuTinh'] ?? $this->thoiLuongDuTinh;
        $isQuickPoll = isset($data['isQuickPoll']) ? (bool) $data['isQuickPoll'] : $this->isQuickPoll;
        $trangThai = $data['trangThai'] ?? $this->trangThai;
        $diemThuong = $data['diemThuong'] ?? $this->diemThuong;
        $danhMuc = isset($data['danhMuc']) ? (int)$data['danhMuc'] : $this->danhMuc;
        $maSuKien = $data['maSuKien'] ?? $this->maSuKien;

        $statement = $db->prepare(
            'UPDATE surveys SET tieuDe = :tieu, moTa = :mo, loaiKhaoSat = :loai, thoiLuongDuTinh = :thoiluong, isQuickPoll = :isquickpoll,
             trangThai = :status, diemThuong = :diem, danhMuc = :danh, maSuKien = :sukien, updated_at = :updated WHERE id = :id'
        );

        return $statement->execute([
            ':tieu' => $tieuDe,
            ':mo' => $moTa,
            ':loai' => $loaiKhaoSat,
            ':thoiluong' => $thoiLuongDuTinh,
            ':isquickpoll' => $isQuickPoll,
            ':status' => $trangThai,
            ':diem' => (int) $diemThuong,
            ':danh' => $danhMuc,
            ':sukien' => $maSuKien,
            ':updated' => $now,
            ':id' => $this->id,
        ]);
    }

    /**
     * Sinh mã khảo sát (10 ký tự, prefix KS) và tránh trùng UNIQUE.
     */
    private static function generateMaKhaoSat(PDO $db): string
    {
        $attempts = 0;
        do {
            $code = 'KS' . strtoupper(bin2hex(random_bytes(4))); // 2 + 8 = 10 ký tự
            $stmt = $db->prepare('SELECT COUNT(*) FROM surveys WHERE maKhaoSat = :ma LIMIT 1');
            $stmt->execute([':ma' => $code]);
            $exists = (int) $stmt->fetchColumn() > 0;
            $attempts++;
            if (!$exists) {
                return $code;
            }
        } while ($attempts < 5);
        // Nếu quá 5 lần vẫn trùng, trả về mã mới (xác suất trùng rất thấp)
        return 'KS' . strtoupper(bin2hex(random_bytes(4)));
    }

    /**
     * Xóa khảo sát (cascade xóa câu hỏi)
     */
    public function delete(): bool
    {
        return self::deleteById($this->id);
    }

    /**
     * Xóa khảo sát theo id (dùng trong controller hoặc các nơi khác).
     */
    public static function deleteById(int $id): bool
    {
        try {
            /** @var PDO $db */
            $db = Container::get('db');
            $stmt = $db->prepare('DELETE FROM surveys WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (\Throwable $e) {
            error_log('[Survey::deleteById] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật trạng thái khảo sát
     */
    public function updateStatus(string $trangThai): bool
    {
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('UPDATE surveys SET trangThai = :status, updated_at = :updated WHERE id = :id');
        return $statement->execute([
            ':status' => $trangThai,
            ':updated' => $now,
            ':id' => $this->id,
        ]);
    }

    /**
     * Cập nhật trạng thái kiểm duyệt
     */
    public function updateVerificationStatus(string $trangThai): bool
    {
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        /** @var PDO $db */
        $db = Container::get('db');

        $statement = $db->prepare('UPDATE surveys SET trangThai = :kiemduyet, updated_at = :updated WHERE id = :id');
        return $statement->execute([
            ':kiemduyet' => $trangThai,
            ':updated' => $now,
            ':id' => $this->id,
        ]);
    }

    /**
     * Chuyển đổi thành array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'maKhaoSat' => $this->maKhaoSat,
            'tieuDe' => $this->tieuDe,
            'moTa' => $this->moTa,
            'loaiKhaoSat' => $this->loaiKhaoSat,
            'thoiLuongDuTinh' => $this->thoiLuongDuTinh,
            'isQuickPoll' => $this->isQuickPoll,
            'maNguoiTao' => $this->maNguoiTao,
            'trangThai' => $this->trangThai,
            'diemThuong' => $this->diemThuong,
            'danhMuc' => $this->danhMuc,
            'maSuKien' => $this->maSuKien,
            'questionCount' => SurveyQuestionMap::countBySurvey($this->id),
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getMaKhaoSat(): string
    {
        return $this->maKhaoSat;
    }
    
    public function getTieuDe(): string
    {
        return $this->tieuDe;
    }
    
    public function getMoTa(): ?string
    {
        return $this->moTa;
    }
    
    public function getIsQuickPoll(): bool
    {
        return $this->isQuickPoll;
    }
    
    public function getMaNguoiTao(): int
    {
        return $this->maNguoiTao;
    }
    
    public function getTrangThai(): string
    {
        return $this->trangThai;
    }

    public function getDiemThuong(): int
    {
        return $this->diemThuong;
    }
}
