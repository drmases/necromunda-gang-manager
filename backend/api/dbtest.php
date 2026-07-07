<?php
header('Content-Type: text/plain');
require_once __DIR__ . '/db.php';
try {
    $db = getDb();
    echo "DB connection OK\n";
    $stmt = $db->query('SELECT 1');
    echo "Query OK: " . json_encode($stmt->fetchAll()) . "\n";
} catch (Throwable $e) {
    echo "ERROR: " . get_class($e) . ": " . $e->getMessage() . "\n";
}
