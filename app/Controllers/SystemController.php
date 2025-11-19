<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Container;
use App\Core\SqlInitializer;
use PDO;

/**
 * SystemController
 *
 * POST /system/reset
 * Body JSON: { "database": "mvc_app" } or header X-Database-Name
 *
 * WARNING: This endpoint is destructive. It drops all tables in the configured
 * database and re-runs sql/mysql/init.sql. No auth is applied (per request).
 */
class SystemController extends Controller
{
    public function reset(Request $request): Response
    {
        $config = Container::get('config');
        $expectedDb = (string)($config['db']['database'] ?? '');

        $provided = $request->input('database') ?? $request->header('X-Database-Name');

        if (!is_string($provided) || $provided === '') {
            return $this->json([
                'error' => true,
                'message' => 'Missing database name in request.',
            ], 400);
        }

        if ($provided !== $expectedDb) {
            return $this->json([
                'error' => true,
                'message' => 'Database name does not match configured value.',
            ], 400);
        }

        $db = Container::get('db');
        if (!$db instanceof PDO) {
            return $this->json([
                'error' => true,
                'message' => 'Database connection not available.',
            ], 500);
        }

        try {
            // DROP DDL operations implicitly commit in MySQL. Avoid wrapping DROP TABLE in a transaction.
            $db->exec('SET FOREIGN_KEY_CHECKS=0');

            $stmt = $db->prepare('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = :db');
            $stmt->execute([':db' => $expectedDb]);
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                if (!is_string($table) || $table === '') {
                    continue;
                }
                // Use backticks to avoid identifier issues
                $db->exec(sprintf('DROP TABLE IF EXISTS `%s`', str_replace('`', '``', $table)));
            }

            $db->exec('SET FOREIGN_KEY_CHECKS=1');

            // Re-run initializer to recreate schema and seeds.
            SqlInitializer::run($db);

            return $this->json([
                'ok' => true,
                'message' => 'Database reset and initialized.',
            ], 200);
        } catch (\Throwable $e) {
            // Ensure foreign key checks are re-enabled if an error occurs.
            try {
                $db->exec('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Throwable $_) {
                // ignore
            }

            return $this->json([
                'error' => true,
                'message' => 'Failed to reset database: ' . $e->getMessage(),
            ], 500);
        }
    }
}
