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

if ($action === 'weapon') {
    if ($method === 'POST') {
        $body = getBody();
        $stmt = $db->prepare('INSERT INTO fighter_weapons (fighter_id, name, cost, notes) VALUES (?,?,?,?)');
        $stmt->execute([$id, trim($body['name'] ?? ''), (int)($body['cost'] ?? 0), trim($body['notes'] ?? '')]);
        $row = $db->prepare('SELECT * FROM fighter_weapons WHERE id = ?');
        $row->execute([(int)$db->lastInsertId()]);
        $w = $row->fetch();
        $w['id'] = (int)$w['id']; $w['cost'] = (int)$w['cost'];
        jsonResponse($w, 201);
    }
    if ($method === 'DELETE') {
        $weaponId = (int)($_GET['weapon_id'] ?? 0);
        $db->prepare('DELETE FROM fighter_weapons WHERE id = ? AND fighter_id = ?')->execute([$weaponId, $id]);
        jsonResponse(['deleted' => true]);
    }
}

if ($action === 'armour') {
    if ($method === 'POST') {
        $body = getBody();
        $stmt = $db->prepare('INSERT INTO fighter_armour (fighter_id, name, cost, notes) VALUES (?,?,?,?)');
        $stmt->execute([$id, trim($body['name'] ?? ''), (int)($body['cost'] ?? 0), trim($body['notes'] ?? '')]);
        $row = $db->prepare('SELECT * FROM fighter_armour WHERE id = ?');
        $row->execute([(int)$db->lastInsertId()]);
        $ar = $row->fetch();
        $ar['id'] = (int)$ar['id']; $ar['cost'] = (int)$ar['cost'];
        jsonResponse($ar, 201);
    }
    if ($method === 'DELETE') {
        $armourId = (int)($_GET['armour_id'] ?? 0);
        $db->prepare('DELETE FROM fighter_armour WHERE id = ? AND fighter_id = ?')->execute([$armourId, $id]);
        jsonResponse(['deleted' => true]);
    }
}

if ($action === 'wargear') {
    if ($method === 'POST') {
        $body = getBody();
        $stmt = $db->prepare('INSERT INTO fighter_wargear (fighter_id, name, cost, notes) VALUES (?,?,?,?)');
        $stmt->execute([$id, trim($body['name'] ?? ''), (int)($body['cost'] ?? 0), trim($body['notes'] ?? '')]);
        $row = $db->prepare('SELECT * FROM fighter_wargear WHERE id = ?');
        $row->execute([(int)$db->lastInsertId()]);
        $wg = $row->fetch();
        $wg['id'] = (int)$wg['id']; $wg['cost'] = (int)$wg['cost'];
        jsonResponse($wg, 201);
    }
    if ($method === 'DELETE') {
        $wargearId = (int)($_GET['wargear_id'] ?? 0);
        $db->prepare('DELETE FROM fighter_wargear WHERE id = ? AND fighter_id = ?')->execute([$wargearId, $id]);
        jsonResponse(['deleted' => true]);
    }
}

if ($action === 'special_rule') {
    if ($method === 'POST') {
        $body = getBody();
        $stmt = $db->prepare('INSERT INTO fighter_special_rules (fighter_id, rule_name, description) VALUES (?,?,?)');
        $stmt->execute([$id, trim($body['rule_name'] ?? ''), trim($body['description'] ?? '')]);
        $row = $db->prepare('SELECT * FROM fighter_special_rules WHERE id = ?');
        $row->execute([(int)$db->lastInsertId()]);
        $sr = $row->fetch();
        $sr['id'] = (int)$sr['id'];
        jsonResponse($sr, 201);
    }
    if ($method === 'DELETE') {
        $ruleId = (int)($_GET['rule_id'] ?? 0);
        $db->prepare('DELETE FROM fighter_special_rules WHERE id = ? AND fighter_id = ?')->execute([$ruleId, $id]);
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

    $wpns = $db->prepare('SELECT * FROM fighter_weapons WHERE fighter_id = ?');
    $wpns->execute([$id]);
    $ws = $wpns->fetchAll();
    foreach ($ws as &$w) { $w['id'] = (int)$w['id']; $w['cost'] = (int)$w['cost']; }
    $fighter['weapons'] = $ws;

    $ars = $db->prepare('SELECT * FROM fighter_armour WHERE fighter_id = ?');
    $ars->execute([$id]);
    $arList = $ars->fetchAll();
    foreach ($arList as &$ar) { $ar['id'] = (int)$ar['id']; $ar['cost'] = (int)$ar['cost']; }
    $fighter['armour'] = $arList;

    $wgs = $db->prepare('SELECT * FROM fighter_wargear WHERE fighter_id = ?');
    $wgs->execute([$id]);
    $wgList = $wgs->fetchAll();
    foreach ($wgList as &$wg) { $wg['id'] = (int)$wg['id']; $wg['cost'] = (int)$wg['cost']; }
    $fighter['wargear'] = $wgList;

    $srs = $db->prepare('SELECT * FROM fighter_special_rules WHERE fighter_id = ?');
    $srs->execute([$id]);
    $srList = $srs->fetchAll();
    foreach ($srList as &$sr) { $sr['id'] = (int)$sr['id']; }
    $fighter['special_rules'] = $srList;

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

    $wpns2 = $db->prepare('SELECT * FROM fighter_weapons WHERE fighter_id = ?');
    $wpns2->execute([$id]);
    $ws2 = $wpns2->fetchAll();
    foreach ($ws2 as &$w2) { $w2['id'] = (int)$w2['id']; $w2['cost'] = (int)$w2['cost']; }
    $fighter['weapons'] = $ws2;

    $ars2 = $db->prepare('SELECT * FROM fighter_armour WHERE fighter_id = ?');
    $ars2->execute([$id]);
    $arList2 = $ars2->fetchAll();
    foreach ($arList2 as &$ar2) { $ar2['id'] = (int)$ar2['id']; $ar2['cost'] = (int)$ar2['cost']; }
    $fighter['armour'] = $arList2;

    $wgs2 = $db->prepare('SELECT * FROM fighter_wargear WHERE fighter_id = ?');
    $wgs2->execute([$id]);
    $wgList2 = $wgs2->fetchAll();
    foreach ($wgList2 as &$wg2) { $wg2['id'] = (int)$wg2['id']; $wg2['cost'] = (int)$wg2['cost']; }
    $fighter['wargear'] = $wgList2;

    $srs2 = $db->prepare('SELECT * FROM fighter_special_rules WHERE fighter_id = ?');
    $srs2->execute([$id]);
    $srList2 = $srs2->fetchAll();
    foreach ($srList2 as &$sr2) { $sr2['id'] = (int)$sr2['id']; }
    $fighter['special_rules'] = $srList2;

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
