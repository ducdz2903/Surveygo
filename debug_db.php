<?php
declare(strict_types=1);

require_once dirname(__FILE__) . '/bootstrap.php';

use App\Core\Container;
use PDO;

$db = Container::get('db');

// Check survey_submissions table structure
echo "=== survey_submissions Table Structure ===\n";
$result = $db->query("DESCRIBE survey_submissions");
$columns = $result->fetchAll(PDO::FETCH_ASSOC);

foreach ($columns as $col) {
    echo $col['Field'] . " (" . $col['Type'] . ") - " . ($col['Null'] === 'NO' ? 'NOT NULL' : 'NULLABLE') . "\n";
}

echo "\n=== Existing survey_submissions Records ===\n";
$result = $db->query("SELECT * FROM survey_submissions");
$records = $result->fetchAll(PDO::FETCH_ASSOC);
echo "Total records: " . count($records) . "\n";

foreach ($records as $rec) {
    echo "ID: " . $rec['id'] . ", Survey: " . $rec['maKhaoSat'] . ", User: " . $rec['maNguoiDung'] . ", Status: " . $rec['trangThai'] . "\n";
}

echo "\n=== Checking users table ===\n";
$result = $db->query("SELECT id, name, email FROM users LIMIT 3");
$users = $result->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $u) {
    echo "User ID: " . $u['id'] . ", Name: " . $u['name'] . ", Email: " . $u['email'] . "\n";
}

echo "\n=== Checking surveys table ===\n";
$result = $db->query("SELECT id, tieuDe FROM surveys LIMIT 3");
$surveys = $result->fetchAll(PDO::FETCH_ASSOC);
foreach ($surveys as $s) {
    echo "Survey ID: " . $s['id'] . ", Title: " . $s['tieuDe'] . "\n";
}
?>