<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Container;
use PDO;

class SurveyQuestionMap
{
    private int $id;
    private int $idKhaoSat;
    private int $idCauHoi;
    private int $thuTu;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $attributes)
    {
        $this->id = (int) ($attributes['id'] ?? 0);
        $this->idKhaoSat = (int) ($attributes['idKhaoSat'] ?? $attributes['maKhaoSat'] ?? 0);
        $this->idCauHoi = (int) ($attributes['idCauHoi'] ?? $attributes['maCauHoi'] ?? 0);
        $this->thuTu = isset($attributes['thuTu']) ? (int)$attributes['thuTu'] : 0;
        $this->createdAt = $attributes['created_at'] ?? '';
        $this->updatedAt = $attributes['updated_at'] ?? '';
    }

    /**
     * Gắn câu hỏi vào khảo sát (thêm hoặc cập nhật thuTu)
     */
    public static function attach(int $surveyId, int $questionId): bool
    {
        /** @var PDO $db */
        $db = Container::get('db');

        try {
            $stmt = $db->prepare('INSERT INTO survey_question_map (idKhaoSat, idCauHoi, created_at, updated_at) VALUES (:survey, :question, NOW(), NOW()) ON DUPLICATE KEY UPDATE updated_at = NOW()');
            return $stmt->execute([
                ':survey' => $surveyId,
                ':question' => $questionId,
            ]);
        } catch (\Throwable $e) {
            // Debug: Log or return error
            file_put_contents('debug_log.txt', $e->getMessage() . PHP_EOL, FILE_APPEND);
            return false;
        }
    }

    /**
     * Gỡ mapping câu hỏi khỏi khảo sát
     */
    public static function detach(int $surveyId, int $questionId): bool
    {
        /** @var PDO $db */
        $db = Container::get('db');

        try {
            $stmt = $db->prepare('DELETE FROM survey_question_map WHERE idKhaoSat = :survey AND idCauHoi = :question');
            return $stmt->execute([
                ':survey' => $surveyId,
                ':question' => $questionId,
            ]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Gỡ tất cả mapping của một câu hỏi (khi gán sang khảo sát mới hoặc không gán)
     */
    public static function detachAllByQuestion(int $questionId): bool
    {
        /** @var PDO $db */
        $db = Container::get('db');

        try {
            $stmt = $db->prepare('DELETE FROM survey_question_map WHERE idCauHoi = :question');
            return $stmt->execute([':question' => $questionId]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Lấy danh sách Question objects của một khảo sát theo thứ tự thuTu
     */
    public static function findQuestionsBySurvey(int $surveyId): array
    {
        /** @var PDO $db */
        $db = Container::get('db');

        // Schema doesn't expose per-mapping order; return questions joined by map, ordered by question id
        $stmt = $db->prepare('SELECT q.* FROM survey_question_map sqm JOIN questions q ON q.id = sqm.idCauHoi WHERE sqm.idKhaoSat = :surveyId ORDER BY q.id ASC');
        $stmt->execute([':surveyId' => $surveyId]);
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => new Question($row), $rows);
    }

    /**
     * Đếm số câu hỏi của khảo sát theo mapping
     */
    public static function countBySurvey(int $surveyId): int
    {
        /** @var PDO $db */
        $db = Container::get('db');

        try {
            $stmt = $db->prepare('SELECT COUNT(*) as cnt FROM survey_question_map WHERE idKhaoSat = :surveyId');
            $stmt->execute([':surveyId' => $surveyId]);
            $row = $stmt->fetch();
            return (int)($row['cnt'] ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
