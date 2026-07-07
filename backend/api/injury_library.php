<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$db     = getDb();
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    if ($id) {
        $stmt = $db->prepare('SELECT * FROM injury_library WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) jsonError('Not found', 404);
        jsonResponse(decodeInjury($row));
    }

    $stmt = $db->query('SELECT * FROM injury_library ORDER BY category, sort_order, name');
    jsonResponse(array_map('decodeInjury', $stmt->fetchAll()));
}

if ($method === 'POST') {
    requireAuth();
    $body = getBody();
    $data = validateInjury($body);
    $stmt = $db->prepare('
        INSERT INTO injury_library (name, category, description, sort_order)
        VALUES (?, ?, ?, ?)
    ');
    $stmt->execute([$data['name'], $data['category'], $data['description'], $data['sort_order']]);
    $newId = (int)$db->lastInsertId();
    $row = $db->query("SELECT * FROM injury_library WHERE id = $newId")->fetch();
    jsonResponse(decodeInjury($row), 201);
}

if ($method === 'PUT') {
    requireAuth();
    if (!$id) jsonError('id required');
    $body = getBody();
    $data = validateInjury($body);
    $stmt = $db->prepare('
        UPDATE injury_library SET name=?, category=?, description=?, sort_order=?
        WHERE id=?
    ');
    $stmt->execute([$data['name'], $data['category'], $data['description'], $data['sort_order'], $id]);
    $row = $db->query("SELECT * FROM injury_library WHERE id = $id")->fetch();
    if (!$row) jsonError('Not found', 404);
    jsonResponse(decodeInjury($row));
}

if ($method === 'DELETE') {
    requireAuth();
    if (!$id) jsonError('id required');
    $stmt = $db->prepare('DELETE FROM injury_library WHERE id = ?');
    $stmt->execute([$id]);
    jsonResponse(['deleted' => true]);
}

jsonError('Method not allowed', 405);

function validateInjury(array $body): array {
    $name = trim($body['name'] ?? '');
    if ($name === '') jsonError('name is required');
    return [
        'name'        => $name,
        'category'    => trim($body['category']    ?? ''),
        'description' => trim($body['description'] ?? ''),
        'sort_order'  => (int)($body['sort_order'] ?? 0),
    ];
}

function decodeInjury(array $row): array {
    foreach (['id', 'sort_order'] as $k) {
        if (isset($row[$k])) $row[$k] = (int)$row[$k];
    }
    return $row;
}
