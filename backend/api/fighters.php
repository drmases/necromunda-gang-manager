<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$db     = getDb();
$gangId = (int)($_GET['gang_id'] ?? 0);
if ($gangId <= 0) jsonError('Invalid gang_id');

if ($method === 'GET') {
    $stmt = $db->prepare('SELECT * FROM fighters WHERE gang_id = ? ORDER BY type, name');
    $stmt->execute([$gangId]);
    jsonResponse($stmt->fetchAll());
}

if ($method === 'POST') {
    $body = getBody();
    $name = trim($body['name'] ?? '');
    $type = trim($body['type'] ?? '');
    if ($name === '' || $type === '') jsonError('name and type are required');

    $stmt = $db->prepare(
        'INSERT INTO fighters
            (gang_id, name, type, cost, experience, kills, advancement_count,
             in_recovery, dead, m, ws, bs, s, t, w, i, a, ld, cl, wil, int_stat)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
    );
    $stmt->execute([
        $gangId,
        $name,
        $type,
        (int)($body['cost']              ?? 0),
        (int)($body['experience']        ?? 0),
        (int)($body['kills']             ?? 0),
        (int)($body['advancement_count'] ?? 0),
        (int)($body['in_recovery']       ?? 0),
        (int)($body['dead']              ?? 0),
        (int)($body['m']                 ?? 5),
        (int)($body['ws']                ?? 4),
        (int)($body['bs']                ?? 4),
        (int)($body['s']                 ?? 3),
        (int)($body['t']                 ?? 3),
        (int)($body['w']                 ?? 1),
        (int)($body['i']                 ?? 4),
        (int)($body['a']                 ?? 1),
        (int)($body['ld']                ?? 6),
        (int)($body['cl']                ?? 7),
        (int)($body['wil']               ?? 7),
        (int)($body['int']               ?? 7),
    ]);
    $newId = (int)$db->lastInsertId();
    $row = $db->prepare('SELECT * FROM fighters WHERE id = ?');
    $row->execute([$newId]);
    $fighter = $row->fetch();
    $fighter = normalizeFighter($fighter);
    jsonResponse($fighter, 201);
}

jsonError('Method not allowed', 405);

function normalizeFighter(array $f): array {
    $f['in_recovery'] = (bool)$f['in_recovery'];
    $f['dead']        = (bool)$f['dead'];
    $f['int']         = $f['int_stat'];
    unset($f['int_stat']);
    return $f;
}
