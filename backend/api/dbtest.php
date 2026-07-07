<?php
header('Content-Type: text/plain');
require_once __DIR__ . '/db.php';

$socket = ini_get('pdo_mysql.default_socket') ?: ini_get('mysqli.default_socket');
echo "default_socket=" . var_export($socket, true) . "\n";

echo "--- TCP host=localhost ---\n";
try {
    $pdo = new PDO('mysql:host=localhost;dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]);
    echo "SUCCESS\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "--- unix_socket (from ini) ---\n";
if ($socket) {
    try {
        $pdo = new PDO('mysql:unix_socket=' . $socket . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]);
        echo "SUCCESS\n";
    } catch (Throwable $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
} else {
    echo "no default socket configured\n";
}
