<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\DailyReward;
use App\Models\PointTransaction;
use App\Models\User;

class DailyRewardController extends Controller
{
    public function status(Request $request)
    {
        $userId = (int) ($request->query('userId') ?? $request->input('userId'));

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

        $record = DailyReward::findByUserId($userId);

        $today = (new \DateTimeImmutable('today'))->format('Y-m-d');
        $lastClaimed = $record ? $record->getLastClaimedDate() : null;
        $todayClaimed = $lastClaimed === $today;

        return $this->json([
            'error' => false,
            'data' => [
                'userId' => $userId,
                'currentStreak' => $record ? $record->getCurrentStreak() : 0,
                'lastClaimedDate' => $lastClaimed,
                'today' => $today,
                'todayClaimed' => $todayClaimed,
                'totalPoints' => $record ? $record->getTotalPoints() : 0,
            ],
        ]);
    }

    public function claim(Request $request)
    {
        $userId = (int) ($request->query('userId') ?? $request->input('userId'));

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

        $today = (new \DateTimeImmutable('today'))->format('Y-m-d');

        $record = DailyReward::findByUserId($userId);
        $currentStreak = $record ? $record->getCurrentStreak() : 0;
        $lastClaimed = $record ? $record->getLastClaimedDate() : null;
        $totalPoints = $record ? $record->getTotalPoints() : 0;

        if ($lastClaimed === $today) {
            return $this->json([
                'error' => true,
                'message' => 'Bạn đã điểm danh hôm nay rồi.',
            ], 400);
        }

        if ($lastClaimed !== null) {
            $last = new \DateTimeImmutable($lastClaimed);
            $current = new \DateTimeImmutable($today);
            $diffDays = (int) $current->diff($last)->days;

            if ($diffDays > 1) {
                $currentStreak = 0;
            }
        }

        $currentStreak++;

        $rewardConfig = $this->rewardDaysConfig();
        $totalDays = count($rewardConfig) ?: 1;
        // Xác định ngày trong chu kỳ (1..N), xoay vòng sau khi hết danh sách
        $rewardIndex = ($currentStreak - 1) % $totalDays;

        $pointsEarned = $rewardConfig[$rewardIndex]['points'] ?? 0;
        $totalPoints += $pointsEarned;

        if ($record) {
            $record->updateStreak($currentStreak, $today, $totalPoints);
        } else {
            $record = DailyReward::create($userId, $currentStreak, $today, $totalPoints);
        }

        try {
            if ($pointsEarned > 0) {
                $dateKey = (int) (new \DateTimeImmutable($today))->format('Ymd');
                PointTransaction::addPoints(
                    $userId,
                    $pointsEarned,
                    'daily_reward',
                    $dateKey,
                    'Điểm danh ngày ' . $today
                );
            }
            try {
                $userPoint = \App\Models\UserPoint::getOrCreate($userId);
                $userPoint->addLuckyWheelSpins(1);
            } catch (\Throwable $e) {
                error_log('[DailyRewardController::claim] Failed to add lucky wheel spin: ' . $e->getMessage());
            }   
        } catch (\Throwable $e) {
            error_log('[DailyRewardController::claim] Failed to add points: ' . $e->getMessage());
        }

        return $this->json([
            'error' => false,
            'message' => 'Điểm danh thành công.',
            'data' => [
                'userId' => $userId,
                'currentStreak' => $record->getCurrentStreak(),
                'lastClaimedDate' => $record->getLastClaimedDate(),
                'today' => $today,
                'todayClaimed' => true,
                'pointsEarned' => $pointsEarned,
                'totalPoints' => $record->getTotalPoints(),
            ],
        ]);
    }

    private function rewardDaysConfig(): array
    {
        return [
            ['day' => 1, 'points' => 10, 'icon' => 'gift'],
            ['day' => 2, 'points' => 15, 'icon' => 'gift'],
            ['day' => 3, 'points' => 15, 'icon' => 'gift'],
            ['day' => 4, 'points' => 20, 'icon' => 'gift'],
            ['day' => 5, 'points' => 25, 'icon' => 'gift'],
            ['day' => 6, 'points' => 30, 'icon' => 'gift'],
            ['day' => 7, 'points' => 50, 'icon' => 'star'],
            ['day' => 8, 'points' => 20, 'icon' => 'gift'],
            ['day' => 9, 'points' => 25, 'icon' => 'gift'],
            ['day' => 10, 'points' => 30, 'icon' => 'gift'],
            ['day' => 11, 'points' => 30, 'icon' => 'gift'],
            ['day' => 12, 'points' => 35, 'icon' => 'gift'],
            ['day' => 13, 'points' => 40, 'icon' => 'gift'],
            ['day' => 14, 'points' => 75, 'icon' => 'star'],
            ['day' => 15, 'points' => 30, 'icon' => 'gift'],
            ['day' => 16, 'points' => 35, 'icon' => 'gift'],
            ['day' => 17, 'points' => 40, 'icon' => 'gift'],
            ['day' => 18, 'points' => 40, 'icon' => 'gift'],
            ['day' => 19, 'points' => 45, 'icon' => 'gift'],
            ['day' => 20, 'points' => 50, 'icon' => 'gift'],
            ['day' => 21, 'points' => 100, 'icon' => 'star'],
            ['day' => 22, 'points' => 40, 'icon' => 'gift'],
            ['day' => 23, 'points' => 45, 'icon' => 'gift'],
            ['day' => 24, 'points' => 50, 'icon' => 'gift'],
            ['day' => 25, 'points' => 50, 'icon' => 'gift'],
            ['day' => 26, 'points' => 55, 'icon' => 'gift'],
            ['day' => 27, 'points' => 60, 'icon' => 'gift'],
            ['day' => 28, 'points' => 125, 'icon' => 'star'],
            ['day' => 29, 'points' => 70, 'icon' => 'gift'],
            ['day' => 30, 'points' => 250, 'icon' => 'crown'],
        ];
    }
}
