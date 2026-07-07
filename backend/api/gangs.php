<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$db     = getDb();

if ($method === 'GET') {
    $stmt = $db->query('SELECT * FROM gangs ORDER BY created_at DESC');
    jsonResponse($stmt->fetchAll());
}

if ($method === 'POST') {
    requireAuth();
    $body = getBody();
    $name   = trim($body['name']       ?? '');
    $type   = trim($body['type']       ?? '');
    $credits    = (int)($body['credits']    ?? 1000);
    $reputation = (int)($body['reputation'] ?? 0);

    if ($name === '' || $type === '') jsonError('name and type are required');

    $stmt = $db->prepare(
        'INSERT INTO gangs (name, type, credits, reputation) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$name, $type, $credits, $reputation]);
    $id = (int)$db->lastInsertId();

    $row = $db->query("SELECT * FROM gangs WHERE id = $id")->fetch();
    jsonResponse($row, 201);
}

jsonError('Method not allowed', 405);
