<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\ActivityLog;

class ActivityLogHelper
{
    private static ?ActivityLog $instance = null;


    private static function getInstance(): ActivityLog
    {
        if (self::$instance === null) {
            self::$instance = new ActivityLog();
        }
        return self::$instance;
    }

    public static function logSurveySubmitted(int $userId, int $surveyId): void
    {
        self::getInstance()->log($userId, 'survey_submitted', [
            'entity_type' => 'survey',
            'entity_id' => $surveyId,
            'description' => 'Hoàn thành khảo sát',
        ]);
    }

    public static function logParticipatedEvent(int $userId, int $eventId): void
    {
        self::getInstance()->log($userId, 'participated_event', [
            'entity_type' => 'event',
            'entity_id' => $eventId,
            'description' => 'Tham gia sự kiện',
        ]);
    }


    public static function logRewardRedeemed($userId, $redemptionId, $rewardId): void
    {
        self::getInstance()->log((int)$userId, 'reward_redeemed', [
            'entity_type' => 'reward_redemption',
            'entity_id' => (int)$redemptionId,
            'description' => 'Đổi thưởng thành công',
        ]);
    }


    public static function logSurveyCreated(int $userId, int $surveyId, string $title): void
    {
        self::getInstance()->log($userId, 'survey_created', [
            'entity_type' => 'survey',
            'entity_id' => $surveyId,
            'description' => 'Tạo khảo sát: ' . $title,
        ]);
    }

    public static function logEventCreated(int $userId, int $eventId, string $title): void
    {
        self::getInstance()->log($userId, 'event_created', [
            'entity_type' => 'event',
            'entity_id' => $eventId,
            'description' => 'Tạo sự kiện: ' . $title,
        ]);
    }

    public static function logQuestionCreated(int $userId, int $questionId, string $questionText): void
    {
        self::getInstance()->log($userId, 'question_created', [
            'entity_type' => 'question',
            'entity_id' => $questionId,
            'description' => 'Tạo câu hỏi: ' . $questionText,
        ]);
    }

    /**
     * Dịch action thành tiếng Việt
     */
    public static function translateAction(string $action): string
    {
        $translations = [
            'survey_submitted' => 'Hoàn thành khảo sát',
            'survey_created' => 'Tạo khảo sát',
            'event_created' => 'Tạo sự kiện',
            'question_created' => 'Tạo câu hỏi',
            'participated_event' => 'Tham gia sự kiện',
            'reward_redeemed' => 'Đổi thưởng',
            'profile_updated' => 'Cập nhật hồ sơ',
            'login' => 'Đăng nhập',
            'logout' => 'Đăng xuất',
            'contact_message' => 'Gửi liên hệ',
            'feedback_submitted' => 'Gửi phản hồi',
            'daily_reward_claimed' => 'Nhận thưởng hàng ngày',
            'user_created' => 'Tạo người dùng',
            'user_updated' => 'Cập nhật người dùng',
            'user_deleted' => 'Xóa người dùng',
            'reward_created' => 'Tạo phần thưởng',
            'reward_updated' => 'Cập nhật phần thưởng',
            'reward_deleted' => 'Xóa phần thưởng',
        ];

        return $translations[$action] ?? ucfirst(str_replace('_', ' ', $action));
    }

}

