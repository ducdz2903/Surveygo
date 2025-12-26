<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class RewardRedemption
{
    private $db;
    private $table = 'reward_redemptions';

    // Properties để lưu trữ dữ liệu redemption
    private $id;
    private $userId;
    private $rewardId;
    private $status;
    private $note;
    private $receiverInfo;
    private $bankName;
    private $accountNumber;
    private $accountName;
    private $createdAt;
    private $updatedAt;

    public function __construct()
    {
        $this->db = Container::get('db');
    }

    /**
     * Lấy tất cả redemption của một user
     */
    public function getByUserId($userId, $limit = null, $offset = 0)
    {
        $query = "SELECT rr.*, r.type, r.point_cost, r.value, r.provider, r.name 
                  FROM {$this->table} rr
                  LEFT JOIN rewards r ON rr.reward_id = r.id
                  WHERE rr.user_id = ? 
                  ORDER BY rr.created_at DESC";

        if ($limit) {
            $query .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy redemption theo ID
     */
    public function getById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả redemption với các filter
     */
    public function getAll($limit = null, $offset = 0, $filters = [])
    {
        $query = "SELECT 
                    rr.id, 
                    rr.user_id, 
                    rr.reward_id, 
                    rr.status, 
                    rr.note, 
                    rr.receiver_info, 
                    rr.bank_name, 
                    rr.account_number, 
                    rr.account_name,
                    rr.transfer_status,
                    rr.created_at, 
                    rr.updated_at,
                    u.name as user_name,
                    u.email as user_email,
                    r.name as reward_name,
                    r.type,
                    r.point_cost,
                    CASE 
                        WHEN r.type = 'cash' AND (r.value = 0 OR r.value IS NULL) THEN r.point_cost
                        ELSE r.value
                    END as value
                  FROM {$this->table} rr
                  LEFT JOIN users u ON rr.user_id = u.id
                  LEFT JOIN rewards r ON rr.reward_id = r.id
                  WHERE 1=1";

        if (isset($filters['search'])) {
            $query .= " AND (u.name LIKE ? OR u.email LIKE ?)";
        }

        if (isset($filters['status'])) {
            $query .= " AND rr.status = ?";
        }

        if (isset($filters['type'])) {
            $query .= " AND r.type = ?";
        }

        if (isset($filters['user_id'])) {
            $query .= " AND rr.user_id = ?";
        }

        if (isset($filters['reward_id'])) {
            $query .= " AND rr.reward_id = ?";
        }

        $query .= " ORDER BY rr.created_at DESC";

        if ($limit) {
            $query .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $params = [];
        if (isset($filters['search'])) {
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        if (isset($filters['status']))
            $params[] = $filters['status'];
        if (isset($filters['type']))
            $params[] = $filters['type'];
        if (isset($filters['user_id']))
            $params[] = $filters['user_id'];
        if (isset($filters['reward_id']))
            $params[] = $filters['reward_id'];

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo redemption mới
     */
    public function create($userId, $rewardId, $status = 'pending', $receiverInfo = null, $note = null, $bankName = null, $accountNumber = null)
    {
        $query = "INSERT INTO {$this->table} (user_id, reward_id, status, receiver_info, note, bank_name, account_number, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $stmt = $this->db->prepare($query);
        $result = $stmt->execute([$userId, $rewardId, $status, $receiverInfo, $note, $bankName, $accountNumber]);

        if ($result) {
            return $this->db->lastInsertId();
        }

        return false;
    }
    /** 
     * Cập nhật accountName
     */
    public function updateAccountName($id, $accountName)
    {
        $query = "UPDATE {$this->table} SET account_name = ?, updated_at = NOW() WHERE id = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$accountName, $id]);
    }
    /**
     * Cập nhật transfer_status
     */
    public function updateTransferStatus($id, $transferStatus)
    {
        $query = "UPDATE {$this->table} SET transfer_status = ?, updated_at = NOW() WHERE id = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$transferStatus, $id]);
    }
    /**
     * Cập nhật status của redemption
     */
    public function updateStatus($id, $status, $note = null)
    {
        $query = "UPDATE {$this->table} SET status = ?, note = ?, updated_at = NOW() WHERE id = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$status, $note, $id]);
    }

    /**
     * Cập nhật receiver_info
     */
    public function updateReceiverInfo($id, $receiverInfo)
    {
        $query = "UPDATE {$this->table} SET receiver_info = ?, updated_at = NOW() WHERE id = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$receiverInfo, $id]);
    }

    /**
     * Xóa redemption
     */
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Đếm redemption theo điều kiện
     */
    public function count($filters = [])
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table} rr
                  LEFT JOIN users u ON rr.user_id = u.id
                  LEFT JOIN rewards r ON rr.reward_id = r.id
                  WHERE 1=1";

        if (isset($filters['search'])) {
            $query .= " AND (u.name LIKE ? OR u.email LIKE ?)";
        }

        if (isset($filters['status'])) {
            $query .= " AND rr.status = ?";
        }

        if (isset($filters['type'])) {
            $query .= " AND r.type = ?";
        }

        if (isset($filters['user_id'])) {
            $query .= " AND rr.user_id = ?";
        }

        if (isset($filters['reward_id'])) {
            $query .= " AND rr.reward_id = ?";
        }

        $params = [];
        if (isset($filters['search'])) {
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        if (isset($filters['status']))
            $params[] = $filters['status'];
        if (isset($filters['type']))
            $params[] = $filters['type'];
        if (isset($filters['user_id']))
            $params[] = $filters['user_id'];
        if (isset($filters['reward_id']))
            $params[] = $filters['reward_id'];

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] ?? 0;
    }

    /**
     * Lấy redemption theo status
     */
    public function getByStatus($status, $limit = null, $offset = 0)
    {
        $query = "SELECT * FROM {$this->table} WHERE status = ? ORDER BY created_at DESC";

        if ($limit) {
            $query .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy redemption gần đây nhất của user
     */
    public function getLatestByUserId($userId)
    {
        $query = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Kiểm tra xem user đã đổi reward này chưa
     */
    public function hasRedeemed($userId, $rewardId, $status = 'completed')
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                  WHERE user_id = ? AND reward_id = ? AND status = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, $rewardId, $status]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] > 0;
    }

    /**
     * Lấy thống kê redemption
     */
    public function getStats($filters = [])
    {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
                  FROM {$this->table} WHERE 1=1";

        $params = [];
        if (isset($filters['user_id'])) {
            $query .= " AND user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (isset($filters['date_from'])) {
            $query .= " AND DATE(created_at) >= ?";
            $params[] = $filters['date_from'];
        }

        if (isset($filters['date_to'])) {
            $query .= " AND DATE(created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ====== Getters ======
    public function getId()
    {
        return $this->id;
    }
    public function getUserId()
    {
        return $this->userId;
    }
    public function getRewardId()
    {
        return $this->rewardId;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getNote()
    {
        return $this->note;
    }
    public function getReceiverInfo()
    {
        return $this->receiverInfo;
    }
    public function getBankName()
    {
        return $this->bankName;
    }
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }
    public function getAccountName()
    {
        return $this->accountName;
    }
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    // ====== Setters ======
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }
    public function setRewardId($rewardId)
    {
        $this->rewardId = $rewardId;
        return $this;
    }
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    public function setNote($note)
    {
        $this->note = $note;
        return $this;
    }
    public function setReceiverInfo($receiverInfo)
    {
        $this->receiverInfo = $receiverInfo;
        return $this;
    }
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;
        return $this;
    }
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }
    public function setAccountName($accountName)
    {
        $this->accountName = $accountName;
        return $this;
    }
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Tạo object từ array database
     */
    public function fromArray($data)
    {
        if (isset($data['id']))
            $this->id = $data['id'];
        if (isset($data['user_id']))
            $this->userId = $data['user_id'];
        if (isset($data['reward_id']))
            $this->rewardId = $data['reward_id'];
        if (isset($data['status']))
            $this->status = $data['status'];
        if (isset($data['note']))
            $this->note = $data['note'];
        if (isset($data['receiver_info']))
            $this->receiverInfo = $data['receiver_info'];
        if (isset($data['bank_name']))
            $this->bankName = $data['bank_name'];
        if (isset($data['account_number']))
            $this->accountNumber = $data['account_number'];
        if (isset($data['created_at']))
            $this->createdAt = $data['created_at'];
        if (isset($data['updated_at']))
            $this->updatedAt = $data['updated_at'];
        return $this;
    }

    /**
     * Chuyển object thành array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'reward_id' => $this->rewardId,
            'status' => $this->status,
            'note' => $this->note,
            'receiver_info' => $this->receiverInfo,
            'bank_name' => $this->bankName,
            'account_number' => $this->accountNumber,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
