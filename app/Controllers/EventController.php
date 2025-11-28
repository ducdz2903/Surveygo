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

    // API to get user data for spinning wheel
    public function getUserData(Request $request)
    {
        $userId = $request->query('userId');
        if (!$userId) {
            return $this->json(['error' => true, 'message' => 'User ID is required'], 400);
        }

        $user = User::findById((int) $userId);
        if (!$user) {
            return $this->json(['error' => true, 'message' => 'User not found'], 404);
        }

        return $this->json([
            'error' => false,
            'data' => [
                'points' => $user->getPoints(),
                'spins' => $user->getSpins(),
            ],
        ]);
    }

    // API to perform spin
    public function spin(Request $request)
    {
        $userId = $request->input('userId');
        if (!$userId) {
            return $this->json(['error' => true, 'message' => 'User ID is required'], 400);
        }

        $user = User::findById((int) $userId);
        if (!$user) {
            return $this->json(['error' => true, 'message' => 'User not found'], 404);
        }

        if ($user->getSpins() <= 0) {
            return $this->json(['error' => true, 'message' => 'No spins remaining'], 400);
        }

        // Simulate spin result (random points between 10-100)
        $pointsWon = rand(10, 100);

        // Update user data
        $user->addPoints($pointsWon);
        $user->subtractSpins(1);

        return $this->json([
            'error' => false,
            'data' => [
                'pointsWon' => $pointsWon,
                'newPoints' => $user->getPoints(),
                'remainingSpins' => $user->getSpins(),
            ],
        ]);
    }

    // API for daily check-in
    public function dailyCheckIn(Request $request)
    {
        $userId = $request->input('userId');
        $streak = (int) $request->input('streak', 1);

        if (!$userId) {
            return $this->json(['error' => true, 'message' => 'User ID is required'], 400);
        }

        $user = User::findById((int) $userId);
        if (!$user) {
            return $this->json(['error' => true, 'message' => 'User not found'], 404);
        }

        // Define reward days (same as in daily-rewards.php)
        $rewardDays = [
            1 => ['points' => 10, 'icon' => 'gift'],
            2 => ['points' => 15, 'icon' => 'gift'],
            3 => ['points' => 15, 'icon' => 'gift'],
            4 => ['points' => 20, 'icon' => 'gift'],
            5 => ['points' => 25, 'icon' => 'gift'],
            6 => ['points' => 30, 'icon' => 'gift'],
            7 => ['points' => 50, 'icon' => 'star'],
            8 => ['points' => 20, 'icon' => 'gift'],
            9 => ['points' => 25, 'icon' => 'gift'],
            10 => ['points' => 30, 'icon' => 'gift'],
            11 => ['points' => 30, 'icon' => 'gift'],
            12 => ['points' => 35, 'icon' => 'gift'],
            13 => ['points' => 40, 'icon' => 'gift'],
            14 => ['points' => 75, 'icon' => 'star'],
            15 => ['points' => 30, 'icon' => 'gift'],
            16 => ['points' => 35, 'icon' => 'gift'],
            17 => ['points' => 40, 'icon' => 'gift'],
            18 => ['points' => 40, 'icon' => 'gift'],
            19 => ['points' => 45, 'icon' => 'gift'],
            20 => ['points' => 50, 'icon' => 'gift'],
            21 => ['points' => 100, 'icon' => 'star'],
            22 => ['points' => 40, 'icon' => 'gift'],
            23 => ['points' => 45, 'icon' => 'gift'],
            24 => ['points' => 50, 'icon' => 'gift'],
            25 => ['points' => 50, 'icon' => 'gift'],
            26 => ['points' => 55, 'icon' => 'gift'],
            27 => ['points' => 60, 'icon' => 'gift'],
            28 => ['points' => 125, 'icon' => 'star'],
            29 => ['points' => 70, 'icon' => 'gift'],
            30 => ['points' => 250, 'icon' => 'crown'],
        ];

        // Get reward for current streak
        $rewardIndex = $streak - 1;
        if ($rewardIndex >= count($rewardDays)) {
            $rewardIndex = count($rewardDays) - 1; // Use last reward if streak > 30
        }
        $day = array_keys($rewardDays)[$rewardIndex];
        $reward = $rewardDays[$day];

        // Add points to user
        $user->addPoints($reward['points']);

        // Add spin if 'star' or 'crown'
        $addedSpin = false;
        if ($reward['icon'] === 'star' || $reward['icon'] === 'crown') {
            $user->addSpins(1);
            $addedSpin = true;
        }

        return $this->json([
            'error' => false,
            'data' => [
                'pointsEarned' => $reward['points'],
                'newPoints' => $user->getPoints(),
                'newSpins' => $user->getSpins(),
                'addedSpin' => $addedSpin,
                'icon' => $reward['icon'],
            ],
        ]);
    }
}
