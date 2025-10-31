<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

class SqlInitializer
{
    public static function run(PDO $db): void
    {
        // MySQL-only initializer
        $path = BASE_PATH . '/sql/mysql/init.sql';

        if (!is_file($path)) {
            return; // no SQL to run
        }

        $sql = file_get_contents($path);
        if ($sql === false || trim($sql) === '') {
            return;
        }

        // Simple split on semicolons at end of statements.
        $statements = array_filter(array_map('trim', preg_split('/;\s*\n/m', $sql) ?: []));

        foreach ($statements as $stmt) {
            if ($stmt === '') { continue; }
            $db->exec($stmt);
        }
    }
}
