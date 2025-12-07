<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class Reward
{
    protected $table = 'rewards';
    protected $db;

    // Properties
    public $id;
    public $code;
    public $name;
    public $type; // 'cash', 'e_wallet', 'giftcard', 'physical'
    public $provider;
    public $point_cost;
    public $value;
    public $stock;
    public $image;
    public $description;
    public $created_at;
    public $updated_at;

    public function __construct()
    {
        /** @var PDO $db */
        $this->db = Container::get('db');
    }

    /**
     * Lấy tất cả phần thưởng
     */
    public function getAllActive($limit = null, $offset = 0)
    {
        $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy phần thưởng theo ID
     */
    public function getById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Lấy phần thưởng theo code
     */
    public function getByCode($code)
    {
        $query = "SELECT * FROM {$this->table} WHERE code = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$code]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Lấy phần thưởng theo type
     */
    public function getByType($type, $limit = null, $offset = 0)
    {
        $query = "SELECT * FROM {$this->table} WHERE type = ? ORDER BY created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute([$type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy phần thưởng theo provider
     */
    public function getByProvider($provider, $limit = null, $offset = 0)
    {
        $query = "SELECT * FROM {$this->table} WHERE provider = ? ORDER BY created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute([$provider]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo phần thưởng mới
     */
    public function create($data)
    {
        // Validate required fields
        if (empty($data['code']) || empty($data['name']) || empty($data['type']) || empty($data['point_cost'])) {
            return false;
        }

        $query = "INSERT INTO {$this->table} 
                  (code, name, type, provider, point_cost, value, stock, image, description)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $data['code'],
            $data['name'],
            $data['type'],
            $data['provider'] ?? null,
            $data['point_cost'],
            $data['value'] ?? null,
            $data['stock'] ?? null,
            $data['image'] ?? null,
            $data['description'] ?? null
        ];

        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Cập nhật phần thưởng
     */
    public function update($id, $data)
    {
        $allowedFields = ['code', 'name', 'type', 'provider', 'point_cost', 'value', 'stock', 'image', 'description'];
        
        $updates = [];
        $params = [];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "{$field} = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($updates)) {
            return false;
        }

        $params[] = $id;
        $query = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Xóa phần thưởng
     */
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Cập nhật stock (cho phần thưởng vật lý)
     */
    public function updateStock($id, $quantity)
    {
        $query = "UPDATE {$this->table} SET stock = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$quantity, $id]);
    }

    /**
     * Giảm stock (khi người dùng đổi quà)
     */
    public function decreaseStock($id, $amount = 1)
    {
        $query = "UPDATE {$this->table} SET stock = stock - ? WHERE id = ? AND stock IS NOT NULL";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$amount, $id]);
    }

    /**
     * Tăng stock (hoàn trả hoặc nhập thêm)
     */
    public function increaseStock($id, $amount = 1)
    {
        $query = "UPDATE {$this->table} SET stock = stock + ? WHERE id = ? AND stock IS NOT NULL";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$amount, $id]);
    }

    /**
     * Kiểm tra stock có đủ không
     */
    public function hasEnoughStock($id, $amount = 1)
    {
        $reward = $this->getById($id);
        
        if (!$reward) {
            return false;
        }

        // Nếu là phần thưởng điện tử (stock = null), luôn có đủ
        if ($reward['stock'] === null) {
            return true;
        }

        return $reward['stock'] >= $amount;
    }

    /**
     * Tìm kiếm phần thưởng
     */
    public function search($keyword, $limit = null, $offset = 0)
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE (code LIKE ? OR name LIKE ? OR description LIKE ?)
                  ORDER BY created_at DESC";

        $likeKeyword = "%{$keyword}%";
        $params = [$likeKeyword, $likeKeyword, $likeKeyword];

        if ($limit) {
            $query .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy phần thưởng có giá point_cost <= maxPoints (để hiển thị những quà có thể đổi)
     */
    public function getAffordable($maxPoints, $limit = null, $offset = 0)
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE point_cost <= ?
                  ORDER BY point_cost ASC";

        $params = [$maxPoints];

        if ($limit) {
            $query .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách phần thưởng theo điều kiện lọc
     */
    public function filter($filters = [])
    {
        $query = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if (isset($filters['type']) && $filters['type']) {
            $query .= " AND type = ?";
            $params[] = $filters['type'];
        }

        if (isset($filters['provider']) && $filters['provider']) {
            $query .= " AND provider = ?";
            $params[] = $filters['provider'];
        }

        if (isset($filters['min_points']) && is_numeric($filters['min_points'])) {
            $query .= " AND point_cost >= ?";
            $params[] = $filters['min_points'];
        }

        if (isset($filters['max_points']) && is_numeric($filters['max_points'])) {
            $query .= " AND point_cost <= ?";
            $params[] = $filters['max_points'];
        }

        $query .= " ORDER BY created_at DESC";

        if (isset($filters['limit']) && is_numeric($filters['limit'])) {
            $query .= " LIMIT {$filters['limit']}";

            if (isset($filters['offset']) && is_numeric($filters['offset'])) {
                $query .= " OFFSET {$filters['offset']}";
            }
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số phần thưởng
     */
    public function count()
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['total'] : 0;
    }

    /**
     * Đếm phần thưởng theo type
     */
    public function countByType($type)
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE type = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$type]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['total'] : 0;
    }

    /**
     * Lấy danh sách các loại quà (unique types)
     */
    public function getTypes()
    {
        $query = "SELECT DISTINCT type FROM {$this->table} ORDER BY type ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results ? array_column($results, 'type') : [];
    }

    /**
     * Lấy danh sách các nhà cung cấp (unique providers)
     */
    public function getProviders()
    {
        $query = "SELECT DISTINCT provider FROM {$this->table} WHERE provider IS NOT NULL ORDER BY provider ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results ? array_column($results, 'provider') : [];
    }

    /**
     * Lấy thống kê phần thưởng
     */
    public function getStats()
    {
        $query = "SELECT 
                    COUNT(*) as total_rewards,
                    COUNT(DISTINCT type) as total_types,
                    COUNT(DISTINCT provider) as total_providers
                  FROM {$this->table}";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Đếm tổng số phần thưởng
     */
    public function countActive()
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }
}