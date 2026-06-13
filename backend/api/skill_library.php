<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$db     = getDb();
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    if ($id) {
        $stmt = $db->prepare('SELECT * FROM skill_library WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) jsonError('Not found', 404);
        jsonResponse(decodeSkill($row));
    }

    $where  = [];
    $params = [];

    if (isset($_GET['faction'])) {
        $where[]  = "(factions = '' OR FIND_IN_SET(?, factions) > 0)";
        $params[] = $_GET['faction'];
    }
    if (isset($_GET['role'])) {
        $where[]  = "(roles = '' OR FIND_IN_SET(?, roles) > 0)";
        $params[] = $_GET['role'];
    }

    $sql = 'SELECT * FROM skill_library';
    if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
    $sql .= ' ORDER BY category, sort_order, name';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    jsonResponse(array_map('decodeSkill', $stmt->fetchAll()));
}

if ($method === 'POST') {
    $body = getBody();
    $data = validateSkill($body);
    $stmt = $db->prepare('
        INSERT INTO skill_library (name, category, factions, roles, sort_order)
        VALUES (?, ?, ?, ?, ?)
    ');
    $stmt->execute([$data['name'], $data['category'], $data['factions'], $data['roles'], $data['sort_order']]);
    $newId = (int)$db->lastInsertId();
    $row = $db->query("SELECT * FROM skill_library WHERE id = $newId")->fetch();
    jsonResponse(decodeSkill($row), 201);
}

if ($method === 'PUT') {
    if (!$id) jsonError('id required');
    $body = getBody();
    $data = validateSkill($body);
    $stmt = $db->prepare('
        UPDATE skill_library SET name=?, category=?, factions=?, roles=?, sort_order=?
        WHERE id=?
    ');
    $stmt->execute([$data['name'], $data['category'], $data['factions'], $data['roles'], $data['sort_order'], $id]);
    $row = $db->query("SELECT * FROM skill_library WHERE id = $id")->fetch();
    if (!$row) jsonError('Not found', 404);
    jsonResponse(decodeSkill($row));
}

if ($method === 'DELETE') {
    if (!$id) jsonError('id required');
    $stmt = $db->prepare('DELETE FROM skill_library WHERE id = ?');
    $stmt->execute([$id]);
    jsonResponse(['deleted' => true]);
}

jsonError('Method not allowed', 405);

function validateSkill(array $body): array {
    $name = trim($body['name'] ?? '');
    if ($name === '') jsonError('name is required');
    return [
        'name'       => $name,
        'category'   => trim($body['category']   ?? ''),
        'factions'   => trim($body['factions']   ?? ''),
        'roles'      => trim($body['roles']      ?? ''),
        'sort_order' => (int)($body['sort_order'] ?? 0),
    ];
}

function decodeSkill(array $row): array {
    foreach (['id', 'sort_order'] as $k) {
        if (isset($row[$k])) $row[$k] = (int)$row[$k];
    }
    return $row;
}
