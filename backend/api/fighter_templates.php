<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$db     = getDb();
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

// GET /fighter_templates.php?gang_type=Genestealer+Cult
// GET /fighter_templates.php  (all)
// GET /fighter_templates.php?id=1
if ($method === 'GET') {
    if ($id) {
        $stmt = $db->prepare('SELECT * FROM fighter_templates WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) jsonError('Not found', 404);
        $row = decodeTemplate($row);
        jsonResponse($row);
    }
    if (isset($_GET['gang_type'])) {
        $stmt = $db->prepare('SELECT * FROM fighter_templates WHERE gang_type = ? ORDER BY sort_order, name');
        $stmt->execute([$_GET['gang_type']]);
    } else {
        $stmt = $db->query('SELECT * FROM fighter_templates ORDER BY gang_type, sort_order, name');
    }
    $rows = array_map('decodeTemplate', $stmt->fetchAll());
    jsonResponse($rows);
}

if ($method === 'POST') {
    requireAuth();
    $body = getBody();
    $data = validateTemplate($body);
    $stmt = $db->prepare('
        INSERT INTO fighter_templates
            (gang_type, name, cost, m, ws, bs, s, t, w, i, a, ld, cl, wil, int_stat, sort_order, notes, special_rules)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([
        $data['gang_type'], $data['name'], $data['cost'],
        $data['m'], $data['ws'], $data['bs'], $data['s'], $data['t'],
        $data['w'], $data['i'], $data['a'],
        $data['ld'], $data['cl'], $data['wil'], $data['int_stat'],
        $data['sort_order'], $data['notes'], $data['special_rules'],
    ]);
    $newId = (int)$db->lastInsertId();
    $row = $db->query("SELECT * FROM fighter_templates WHERE id = $newId")->fetch();
    jsonResponse(decodeTemplate($row), 201);
}

if ($method === 'PUT') {
    requireAuth();
    if (!$id) jsonError('id required');
    $body = getBody();
    $data = validateTemplate($body);
    $stmt = $db->prepare('
        UPDATE fighter_templates SET
            gang_type=?, name=?, cost=?,
            m=?, ws=?, bs=?, s=?, t=?, w=?, i=?, a=?,
            ld=?, cl=?, wil=?, int_stat=?, sort_order=?, notes=?, special_rules=?
        WHERE id=?
    ');
    $stmt->execute([
        $data['gang_type'], $data['name'], $data['cost'],
        $data['m'], $data['ws'], $data['bs'], $data['s'], $data['t'],
        $data['w'], $data['i'], $data['a'],
        $data['ld'], $data['cl'], $data['wil'], $data['int_stat'],
        $data['sort_order'], $data['notes'], $data['special_rules'], $id,
    ]);
    $row = $db->query("SELECT * FROM fighter_templates WHERE id = $id")->fetch();
    if (!$row) jsonError('Not found', 404);
    jsonResponse(decodeTemplate($row));
}

if ($method === 'DELETE') {
    requireAuth();
    if (!$id) jsonError('id required');
    $stmt = $db->prepare('DELETE FROM fighter_templates WHERE id = ?');
    $stmt->execute([$id]);
    jsonResponse(['deleted' => true]);
}

jsonError('Method not allowed', 405);

function validateTemplate(array $body): array {
    $gangType = trim($body['gang_type'] ?? '');
    $name     = trim($body['name']      ?? '');
    if ($gangType === '') jsonError('gang_type is required');
    if ($name === '')     jsonError('name is required');
    return [
        'gang_type'  => $gangType,
        'name'       => $name,
        'cost'       => (int)($body['cost']       ?? 0),
        'm'          => (int)($body['m']           ?? 5),
        'ws'         => (int)($body['ws']          ?? 4),
        'bs'         => (int)($body['bs']          ?? 4),
        's'          => (int)($body['s']           ?? 3),
        't'          => (int)($body['t']           ?? 3),
        'w'          => (int)($body['w']           ?? 1),
        'i'          => (int)($body['i']           ?? 4),
        'a'          => (int)($body['a']            ?? 1),
        'ld'         => (int)($body['ld']          ?? 6),
        'cl'         => (int)($body['cl']          ?? 7),
        'wil'        => (int)($body['wil']         ?? 7),
        'int_stat'   => (int)($body['int_stat']    ?? 7),
        'sort_order'    => (int)($body['sort_order']  ?? 0),
        'notes'         => trim($body['notes']        ?? ''),
        'special_rules' => trim($body['special_rules'] ?? ''),
    ];
}

function decodeTemplate(array $row): array {
    foreach (['id','cost','m','ws','bs','s','t','w','i','a','ld','cl','wil','int_stat','sort_order'] as $k) {
        if (isset($row[$k])) $row[$k] = (int)$row[$k];
    }
    if (!isset($row['special_rules'])) $row['special_rules'] = '';
    return $row;
}
