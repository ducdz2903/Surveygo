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
}
