<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$db     = getDb();
$id     = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($id <= 0) jsonError('Invalid fighter id');

// ── Sub-resource routes ──────────────────────────────────────────────────────

if ($action === 'skill') {
    if ($method === 'POST') {
        $body = getBody();
        $stmt = $db->prepare('INSERT INTO fighter_skills (fighter_id, skill_name, skill_category) VALUES (?,?,?)');
        $stmt->execute([$id, trim($body['skill_name'] ?? ''), trim($body['skill_category'] ?? '')]);
        $row = $db->prepare('SELECT * FROM fighter_skills WHERE id = ?');
        $row->execute([(int)$db->lastInsertId()]);
        jsonResponse($row->fetch(), 201);
    }
    if ($method === 'DELETE') {
        $skillId = (int)($_GET['skill_id'] ?? 0);
        $db->prepare('DELETE FROM fighter_skills WHERE id = ? AND fighter_id = ?')->execute([$skillId, $id]);
        jsonResponse(['deleted' => true]);
    }
}

if ($action === 'injury') {
    if ($method === 'POST') {
        $body = getBody();
        $stmt = $db->prepare('INSERT INTO fighter_injuries (fighter_id, injury_name, permanent) VALUES (?,?,?)');
        $stmt->execute([$id, trim($body['injury_name'] ?? ''), (int)($body['permanent'] ?? 0)]);
        $row = $db->prepare('SELECT * FROM fighter_injuries WHERE id = ?');
        $row->execute([(int)$db->lastInsertId()]);
        $inj = $row->fetch();
        $inj['permanent'] = (bool)$inj['permanent'];
        jsonResponse($inj, 201);
    }
    if ($method === 'DELETE') {
        $injId = (int)($_GET['injury_id'] ?? 0);
        $db->prepare('DELETE FROM fighter_injuries WHERE id = ? AND fighter_id = ?')->execute([$injId, $id]);
        jsonResponse(['deleted' => true]);
    }
}

if ($action === 'equipment') {
    if ($method === 'POST') {
        $body   = getBody();
        $traits = json_encode($body['traits'] ?? []);
        $stmt   = $db->prepare(
            'INSERT INTO equipment (fighter_id, name, type, cost, traits) VALUES (?,?,?,?,?)'
        );
        $stmt->execute([$id, trim($body['name'] ?? ''), $body['type'] ?? 'equipment', (int)($body['cost'] ?? 0), $traits]);
        $row = $db->prepare('SELECT * FROM equipment WHERE id = ?');
        $row->execute([(int)$db->lastInsertId()]);
        $eq = $row->fetch();
        $eq['traits'] = json_decode($eq['traits'] ?? '[]', true);
        jsonResponse($eq, 201);
    }
    if ($method === 'DELETE') {
        $equipId = (int)($_GET['equip_id'] ?? 0);
        $db->prepare('DELETE FROM equipment WHERE id = ? AND fighter_id = ?')->execute([$equipId, $id]);
        jsonResponse(['deleted' => true]);
    }
}

// ── Fighter CRUD ─────────────────────────────────────────────────────────────

if ($method === 'GET') {
    $stmt = $db->prepare('SELECT * FROM fighters WHERE id = ?');
    $stmt->execute([$id]);
    $fighter = $stmt->fetch();
    if (!$fighter) jsonError('Fighter not found', 404);

    $fighter = normalizeFighter($fighter);

    $skills = $db->prepare('SELECT * FROM fighter_skills WHERE fighter_id = ?');
    $skills->execute([$id]);
    $fighter['skills'] = $skills->fetchAll();

    $injuries = $db->prepare('SELECT * FROM fighter_injuries WHERE fighter_id = ?');
    $injuries->execute([$id]);
    $inj = $injuries->fetchAll();
    foreach ($inj as &$i) { $i['permanent'] = (bool)$i['permanent']; }
    $fighter['injuries'] = $inj;

    $equip = $db->prepare('SELECT * FROM equipment WHERE fighter_id = ?');
    $equip->execute([$id]);
    $eqs = $equip->fetchAll();
    foreach ($eqs as &$e) { $e['traits'] = json_decode($e['traits'] ?? '[]', true); }
    $fighter['equipment'] = $eqs;

    jsonResponse($fighter);
}

if ($method === 'PUT') {
    $body  = getBody();
    $check = $db->prepare('SELECT id FROM fighters WHERE id = ?');
    $check->execute([$id]);
    if (!$check->fetch()) jsonError('Fighter not found', 404);

    $allowed = [
        'name','type','cost','experience','kills','advancement_count',
        'in_recovery','dead','m','ws','bs','s','t','w','i','a','ld','cl','wil',
    ];
    $sets = []; $params = [];
    foreach ($allowed as $field) {
        if (array_key_exists($field, $body)) {
            $sets[]   = "$field = ?";
            $params[] = $body[$field];
        }
    }
    if (array_key_exists('int', $body)) {
        $sets[]   = 'int_stat = ?';
        $params[] = (int)$body['int'];
    }
    if (empty($sets)) jsonError('No fields to update');
    $params[] = $id;

    $db->prepare('UPDATE fighters SET ' . implode(', ', $sets) . ' WHERE id = ?')->execute($params);

    $stmt = $db->prepare('SELECT * FROM fighters WHERE id = ?');
    $stmt->execute([$id]);
    $fighter = normalizeFighter($stmt->fetch());

    $skills = $db->prepare('SELECT * FROM fighter_skills WHERE fighter_id = ?');
    $skills->execute([$id]);
    $fighter['skills'] = $skills->fetchAll();

    $injuries = $db->prepare('SELECT * FROM fighter_injuries WHERE fighter_id = ?');
    $injuries->execute([$id]);
    $inj = $injuries->fetchAll();
    foreach ($inj as &$i) { $i['permanent'] = (bool)$i['permanent']; }
    $fighter['injuries'] = $inj;

    $equip = $db->prepare('SELECT * FROM equipment WHERE fighter_id = ?');
    $equip->execute([$id]);
    $eqs = $equip->fetchAll();
    foreach ($eqs as &$e) { $e['traits'] = json_decode($e['traits'] ?? '[]', true); }
    $fighter['equipment'] = $eqs;

    jsonResponse($fighter);
}

if ($method === 'DELETE') {
    $db->prepare('DELETE FROM fighters WHERE id = ?')->execute([$id]);
    jsonResponse(['deleted' => true]);
}

jsonError('Method not allowed', 405);

function normalizeFighter(array $f): array {
    $f['in_recovery'] = (bool)$f['in_recovery'];
    $f['dead']        = (bool)$f['dead'];
    $f['int']         = $f['int_stat'];
    unset($f['int_stat']);
    return $f;
}
