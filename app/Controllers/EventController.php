<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Event;
use App\Models\User;

class EventController extends Controller
{

    // api phân trang
    public function index(Request $request)
    {
        $page = (int) ($request->query('page') ?? 1);
        $limit = (int) ($request->query('limit') ?? 10);

        $filters = [];
        if ($search = $request->query('search')) {
            $filters['search'] = $search;
        }
        if ($trangThai = $request->query('trangThai') ?? $request->query('status')) {
            $filters['trangThai'] = $trangThai;
        }

        $result = Event::paginate($page, $limit, $filters);

        $events = array_map(function ($e) {
            $creatorName = null;
            if ($e->getMaNguoiTao()) {
                $user = User::findById($e->getMaNguoiTao());
                $creatorName = $user ? $user->getName() : null;
            }

            return [
                'id' => $e->getId(),
                'code' => $e->getMaSuKien(),
                'title' => $e->getTenSuKien(),
                'location' => $e->getDiaDiem(),
                'startDate' => $e->getThoiGianBatDau(),
                'endDate' => $e->getThoiGianKetThuc(),
                'status' => $e->getTrangThai(),
                'participants' => $e->getSoNguoiThamGia(),
                'surveys' => $e->getSoKhaoSat(),
                'creatorId' => $e->getMaNguoiTao(),
                'creator' => $creatorName,
                'created_at' => $e->getCreatedAt() ?? null,
                'updated_at' => $e->getUpdatedAt() ?? null,
            ];
        }, $result['events']);

        return $this->json([
            'error' => false,
            'data' => $events,
            'meta' => [
                'total' => $result['total'],
                'page' => $result['page'],
                'limit' => $result['limit'],
                'totalPages' => $result['totalPages'],
            ],
        ]);
    }
    public function spinLuckyWheel(Request $request)
    {
        $userId = (int) ($request->input('userId') ?? 0);

        if ($userId <= 0) {
            return $this->json([
                'error' => true,
                'message' => 'User ID is required.',
            ], 422);
        }

        $user = User::findById($userId);
        if (!$user) {
            return $this->json([
                'error' => true,
                'message' => 'User not found.',
            ], 404);
        }

        // Logic quay thưởng ngẫu nhiên
        // Tỷ lệ: 10 điểm (40%), 20 điểm (30%), 50 điểm (20%), 100 điểm (5%), 200 điểm (3%), 500 điểm (2%)
        $prizes = [
            10 => 40,
            20 => 30,
            50 => 20,
            100 => 5,
            200 => 3,
            500 => 2
        ];

        $rand = mt_rand(1, 100);
        $points = 10; // Default
        $cumulative = 0;

        foreach ($prizes as $prize => $percent) {
            $cumulative += $percent;
            if ($rand <= $cumulative) {
                $points = $prize;
                break;
            }
        }

        try {
            $tx = \App\Models\PointTransaction::addPoints(
                $userId,
                $points,
                'lucky_wheel',
                null,
                'Quay thưởng Lucky Wheel (' . date('Y-m-d H:i:s') . ')'
            );

            return $this->json([
                'error' => false,
                'message' => "Chúc mừng! Bạn nhận được {$points} điểm.",
                'data' => [
                    'points_added' => $points,
                    'new_balance' => $tx->getBalanceAfter(),
                ]
            ]);

        } catch (\Throwable $e) {
            return $this->json([
                'error' => true,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
            ], 500);
        }
    }
}
