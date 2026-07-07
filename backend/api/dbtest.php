<?php
header('Content-Type: text/plain');

$hosts = ['globbin.se.mysql', 'localhost', '127.0.0.1'];
foreach ($hosts as $host) {
    echo "--- trying host: $host ---\n";
    try {
        $dsn = 'mysql:host=' . $host . ';dbname=globbin_se;charset=utf8mb4';
        $pdo = new PDO($dsn, 'globbin_se', '131313', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $stmt = $pdo->query('SELECT 1 AS ok');
        echo "SUCCESS: " . json_encode($stmt->fetchAll()) . "\n";
    } catch (Throwable $e) {
        echo "ERROR: " . get_class($e) . ": " . $e->getMessage() . "\n";
    }
}
