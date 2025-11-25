<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class Feedback
{
    private int $id;
    private string $ma ;
    private int $idKhaoSat ;
    private int $idNguoiDung ;
    private int $danhGia;
    private ?string $binhLuan;
    private DateTime|string $createdAt;
    private DateTime|string $updatedAt;

    public function __construct(array $row)
    {
        $this->id = (int) ($row['id'] ?? 0);
        $this->idKhaoSat  = (int) ($row['idKhaoSat '] ?? 0);
        $this->idNguoiDung  = (int) ($row['idNguoiDung '] ?? 0);
        $this->danhGia = (int) ($row['danhGia'] ?? 0);
        $this->binhLuan = $row['binhLuan'] ?? null;
        $this->createdAt = $row['created_at'] ?? '';
        $this->updatedAt = $row['updated_at'] ?? '';
    }

    // tạo mới phản hồi
    public static function create(array $data): ?self {
        $db = Container::get('db');

        $ma = $data['ma'] ?? ('FB' . str_pad((string) rand(1, 9999), 3, '0', STR_PAD_LEFT));
        $idKhaoSat  = (int) ($data['idKhaoSat '] ?? 0);
        $idNguoiDung  = (int) ($data['idNguoiDung '] ?? 0);
        $danhGia = (int) ($data['danhGia'] ?? 0);
        $binhLuan = $data['binhLuan'] ?? null;
        $sql = 'INSERT INTO feedbacks (ma, idKhaoSat , idNguoiDung , danhGia, binhLuan, created_at, updated_at)
                VALUES (:ma, :idKhaoSat , :idNguoiDung , :danhGia, :binhLuan, NOW(), NOW())';
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':ma', $ma);
        $stmt->bindParam(':idKhaoSat ', $idKhaoSat );
        $stmt->bindParam(':idNguoiDung ', $idNguoiDung );
        $stmt->bindParam(':danhGia', $danhGia);
        $stmt->bindParam(':binhLuan', $binhLuan);
        $stmt->execute();

        if($stmt->rowCount() === 0) {
            return null;
        }

        $id = (int) $db->lastInsertId();
        return self::findById($id);
    }

    public static function paginate (int $page = 1 , int $limit = 10 , array $filter = []) : array {
        $db = Container :: get('db');

        $page = max(1, (int) $page);
        $limit = max(1, min(100, (int) $limit));
        $offset = ($page - 1) * $limit;

        $where = [];
        $params = [];

        

    }

    // lấy thông tin chi tiết phản hồi theo id 
    public static function findById (int $id) : ?self {
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

}