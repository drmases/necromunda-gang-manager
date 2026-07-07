<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$db     = getDb();
$id     = (int)($_GET['id'] ?? 0);
if ($id <= 0) jsonError('Invalid gang id');

if ($method === 'GET') {
    $gang = $db->prepare('SELECT * FROM gangs WHERE id = ?');
    $gang->execute([$id]);
    $row = $gang->fetch();
    if (!$row) jsonError('Gang not found', 404);

    $fighters = $db->prepare('SELECT * FROM fighters WHERE gang_id = ? ORDER BY type, name');
    $fighters->execute([$id]);
    $fList = $fighters->fetchAll();

    $gearCost = $db->prepare(
        'SELECT
            COALESCE((SELECT SUM(cost) FROM fighter_weapons WHERE fighter_id = ?), 0) +
            COALESCE((SELECT SUM(cost) FROM fighter_armour  WHERE fighter_id = ?), 0) +
            COALESCE((SELECT SUM(cost) FROM fighter_wargear WHERE fighter_id = ?), 0) +
            COALESCE((SELECT SUM(cost) FROM equipment       WHERE fighter_id = ?), 0)'
    );
    foreach ($fList as &$fRow) {
        $gearCost->execute([$fRow['id'], $fRow['id'], $fRow['id'], $fRow['id']]);
        $fRow['cost'] = (int)$fRow['cost'] + (int)$gearCost->fetchColumn();
    }
    unset($fRow);
    $row['fighters'] = $fList;

    jsonResponse($row);
}

if ($method === 'PUT') {
    requireAuth();
    $body  = getBody();
    $check = $db->prepare('SELECT id FROM gangs WHERE id = ?');
    $check->execute([$id]);
    if (!$check->fetch()) jsonError('Gang not found', 404);

    $allowed = ['name', 'type', 'credits', 'reputation'];
    $sets = []; $params = [];
    foreach ($allowed as $field) {
        if (array_key_exists($field, $body)) {
            $sets[]   = "$field = ?";
            $params[] = $body[$field];
        }
    }
    if (empty($sets)) jsonError('No fields to update');
    $params[] = $id;

    $db->prepare('UPDATE gangs SET ' . implode(', ', $sets) . ' WHERE id = ?')->execute($params);
    $row = $db->prepare('SELECT * FROM gangs WHERE id = ?');
    $row->execute([$id]);
    jsonResponse($row->fetch());
}

if ($method === 'DELETE') {
    requireAuth();
    $db->prepare('DELETE FROM gangs WHERE id = ?')->execute([$id]);
    jsonResponse(['deleted' => true]);
}

jsonError('Method not allowed', 405);
