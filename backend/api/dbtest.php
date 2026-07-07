<?php
header('Content-Type: text/plain');
require_once __DIR__ . '/db.php';

echo "DB_HOST=" . DB_HOST . "\n";
echo "DB_NAME=" . DB_NAME . "\n";
echo "DB_USER=" . DB_USER . "\n";
echo "DB_PASS length=" . strlen(DB_PASS) . "\n";

try {
    $db = getDb();
    echo "DB connection OK\n";
    $stmt = $db->query('SELECT 1 AS ok');
    echo "Query OK: " . json_encode($stmt->fetchAll()) . "\n";
} catch (Throwable $e) {
    echo "ERROR: " . get_class($e) . ": " . $e->getMessage() . "\n";
}
