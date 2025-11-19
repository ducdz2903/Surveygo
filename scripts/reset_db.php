<?php

declare(strict_types=1);

// Simple, standalone DB reset script.
// Does NOT load app bootstrap or autoloaders.

// 1) Load DB config from server-side config/app.php (not deployed by CI).
$appConfigFile = __DIR__ . '/../config/app.php';
if (!file_exists($appConfigFile)) {
    http_response_code(500);
    echo 'Config file not found';
    exit;
}

$config = require $appConfigFile;

if (!is_array($config) || !isset($config['db']) || !is_array($config['db'])) {
    http_response_code(500);
    echo 'Invalid DB config';
    exit;
}

$db = $config['db'];

// Support both MySQL and MariaDB.
// In PDO, MariaDB cũng dùng driver "mysql".
$driver = strtolower((string)($db['driver'] ?? 'mysql'));
$host = $db['host'] ?? '127.0.0.1';
$port = (int)($db['port'] ?? 3306);
$dbName = $db['database'] ?? '';
$user = $db['username'] ?? '';
$pass = $db['password'] ?? '';
$charset = $db['charset'] ?? 'utf8mb4';

// 2) Load reset token from a separate secret file on the server.
//    This file is NOT deployed by CI because config/** is excluded.
//    On the server, create: config/reset_db_token.php
//    with content: <?php return 'YOUR_FTP_PASSWORD_HERE';
$tokenFile = __DIR__ . '/../config/reset_db_token.php';
if (!file_exists($tokenFile)) {
    http_response_code(500);
    echo 'Reset token config not found';
    exit;
}

$expectedToken = require $tokenFile;
if (!is_string($expectedToken) || $expectedToken === '') {
    http_response_code(500);
    echo 'Invalid reset token config';
    exit;
}

// 3) Validate incoming token (from POST or GET).
$token = $_POST['token'] ?? $_GET['token'] ?? '';

if (!is_string($token) || $token !== $expectedToken) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}
// 4) Connect to MySQL/MariaDB at server level (no database in DSN).
// Cho dù config để 'mysql' hay 'mariadb' thì PDO vẫn dùng driver 'mysql'.
$pdoDriver = 'mysql';
$dsn = sprintf('%s:host=%s;port=%d;charset=%s', $pdoDriver, $host, $port, $charset);
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo 'DB connect error: ' . $e->getMessage();
    exit;
}

if ($dbName === '') {
    http_response_code(500);
    echo 'Database name is empty';
    exit;
}

// 5) Drop + recreate database, then import init.sql.
try {
    $pdo->exec('DROP DATABASE IF EXISTS `' . str_replace('`', '``', $dbName) . '`');
    $pdo->exec(
        'CREATE DATABASE `' . str_replace('`', '``', $dbName) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
    );
    $pdo->exec('USE `' . str_replace('`', '``', $dbName) . '`');
} catch (PDOException $e) {
    http_response_code(500);
    echo 'DB recreate error: ' . $e->getMessage();
    exit;
}

$sqlFile = __DIR__ . '/../sql/mysql/init.sql';
if (!file_exists($sqlFile)) {
    http_response_code(500);
    echo 'init.sql not found';
    exit;
}

$sql = file_get_contents($sqlFile);
if ($sql === false) {
    http_response_code(500);
    echo 'Failed to read init.sql';
    exit;
}

$statements = array_filter(array_map('trim', explode(';', $sql)));

try {
    foreach ($statements as $stmt) {
        if ($stmt !== '') {
            $pdo->exec($stmt);
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo 'SQL import error: ' . $e->getMessage();
    exit;
}

// 6) Self-check: try simple query on a core table (users).
header('Content-Type: application/json');

try {
    $stmt = $pdo->query('SELECT COUNT(*) AS c FROM users');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = isset($row['c']) ? (int)$row['c'] : 0;

    http_response_code(200);
    echo json_encode([
        'status' => 'ok',
        'rows_in_users' => $count,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
}
