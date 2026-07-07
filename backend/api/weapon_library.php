<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$db     = getDb();
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    if ($id) {
        $stmt = $db->prepare('SELECT * FROM weapon_library WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) jsonError('Not found', 404);
        $row = decodeWeapon($row);
        jsonResponse($row);
    }
    if (isset($_GET['faction'])) {
        $stmt = $db->prepare('SELECT * FROM weapon_library WHERE FIND_IN_SET(?, factions) > 0 ORDER BY category, sort_order, name');
        $stmt->execute([$_GET['faction']]);
    } elseif (isset($_GET['gang_type'])) {
        $stmt = $db->prepare('SELECT * FROM weapon_library WHERE gang_type = ? ORDER BY sort_order, name');
        $stmt->execute([$_GET['gang_type']]);
    } else {
        $stmt = $db->query('SELECT * FROM weapon_library ORDER BY gang_type, sort_order, name');
    }
    $rows = array_map('decodeWeapon', $stmt->fetchAll());
    jsonResponse($rows);
}

if ($method === 'POST') {
    requireAuth();
    $body = getBody();
    $data = validateWeapon($body);
    $stmt = $db->prepare('
        INSERT INTO weapon_library
            (gang_type, category, name, cost, range_s, range_l, hit_s, hit_l, str, ap, dmg, ammo, traits, factions, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([
        $data['gang_type'], $data['category'], $data['name'], $data['cost'],
        $data['range_s'], $data['range_l'], $data['hit_s'], $data['hit_l'],
        $data['str'], $data['ap'], $data['dmg'], $data['ammo'],
        $data['traits'], $data['factions'], $data['sort_order'],
    ]);
    $newId = (int)$db->lastInsertId();
    $row = $db->query("SELECT * FROM weapon_library WHERE id = $newId")->fetch();
    jsonResponse(decodeWeapon($row), 201);
}

if ($method === 'PUT') {
    requireAuth();
    if (!$id) jsonError('id required');
    $body = getBody();
    $data = validateWeapon($body);
    $stmt = $db->prepare('
        UPDATE weapon_library SET
            gang_type=?, category=?, name=?, cost=?,
            range_s=?, range_l=?, hit_s=?, hit_l=?,
            str=?, ap=?, dmg=?, ammo=?, traits=?, factions=?, sort_order=?
        WHERE id=?
    ');
    $stmt->execute([
        $data['gang_type'], $data['category'], $data['name'], $data['cost'],
        $data['range_s'], $data['range_l'], $data['hit_s'], $data['hit_l'],
        $data['str'], $data['ap'], $data['dmg'], $data['ammo'],
        $data['traits'], $data['factions'], $data['sort_order'], $id,
    ]);
    $row = $db->query("SELECT * FROM weapon_library WHERE id = $id")->fetch();
    if (!$row) jsonError('Not found', 404);
    jsonResponse(decodeWeapon($row));
}

if ($method === 'DELETE') {
    requireAuth();
    if (!$id) jsonError('id required');
    $stmt = $db->prepare('DELETE FROM weapon_library WHERE id = ?');
    $stmt->execute([$id]);
    jsonResponse(['deleted' => true]);
}

jsonError('Method not allowed', 405);

function validateWeapon(array $body): array {
    $gangType = trim($body['gang_type'] ?? '');
    $name     = trim($body['name']      ?? '');
    if ($gangType === '') jsonError('gang_type is required');
    if ($name === '')     jsonError('name is required');
    return [
        'gang_type'  => $gangType,
        'category'   => trim($body['category']    ?? ''),
        'name'       => $name,
        'cost'       => (int)($body['cost']       ?? 0),
        'range_s'    => trim($body['range_s']     ?? '-'),
        'range_l'    => trim($body['range_l']     ?? '-'),
        'hit_s'      => trim($body['hit_s']       ?? '-'),
        'hit_l'      => trim($body['hit_l']       ?? '-'),
        'str'        => trim($body['str']         ?? '-'),
        'ap'         => trim($body['ap']          ?? '-'),
        'dmg'        => trim($body['dmg']         ?? '1'),
        'ammo'       => trim($body['ammo']        ?? '-'),
        'traits'     => trim($body['traits']      ?? ''),
        'factions'   => trim($body['factions']    ?? ''),
        'sort_order' => (int)($body['sort_order'] ?? 0),
    ];
}

function decodeWeapon(array $row): array {
    foreach (['id','cost','sort_order'] as $k) {
        if (isset($row[$k])) $row[$k] = (int)$row[$k];
    }
    return $row;
}
