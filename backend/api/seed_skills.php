<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$db = getDb();

// [name, category, factions, roles, sort_order]
$skills = [
    // Agility
    ['Catfall',      'Agility', '', 'Leader,Champion', 100],
    ['Clamber',      'Agility', '', 'Leader,Champion', 110],
    ['Dodge',        'Agility', '', 'Leader,Champion', 120],
    ['Mighty Leap',  'Agility', '', 'Leader,Champion', 130],
    ['Spring Up',    'Agility', '', 'Leader,Champion', 140],
    ['Sprint',       'Agility', '', 'Leader,Champion', 150],
    // Brawn
    ['Bull Charge',      'Brawn', '', 'Leader,Champion', 200],
    ['Bulging Biceps',   'Brawn', '', 'Leader,Champion', 210],
    ['Crushing Blow',    'Brawn', '', 'Leader,Champion', 220],
    ['Headbutt',         'Brawn', '', 'Leader,Champion', 230],
    ['Hurl',             'Brawn', '', 'Leader,Champion', 240],
    ['Iron Jaw',         'Brawn', '', 'Leader,Champion', 250],
    // Combat
    ['Combat Master',  'Combat', '', 'Leader,Champion', 300],
    ['Counter-attack', 'Combat', '', 'Leader,Champion', 310],
    ['Disarm',         'Combat', '', 'Leader,Champion', 320],
    ['Parry',          'Combat', '', 'Leader,Champion', 330],
    ['Rain of Blows',  'Combat', '', 'Leader,Champion', 340],
    ['Step Aside',     'Combat', '', 'Leader,Champion', 350],
    // Cunning
    ['Backstab',        'Cunning', '', 'Leader,Champion', 400],
    ['Escape Artist',   'Cunning', '', 'Leader,Champion', 410],
    ['Evade',           'Cunning', '', 'Leader,Champion', 420],
    ['Infiltrate',      'Cunning', '', 'Leader,Champion', 430],
    ['Lie Low',         'Cunning', '', 'Leader,Champion', 440],
    ['Overwatch',       'Cunning', '', 'Leader,Champion', 450],
    // Driving
    ['Jink',             'Driving', '', 'Leader,Champion', 500],
    ['Expert Driver',    'Driving', '', 'Leader,Champion', 510],
    ['Heavy Foot',       'Driving', '', 'Leader,Champion', 520],
    ['Slalom',           'Driving', '', 'Leader,Champion', 530],
    ['T-bone',           'Driving', '', 'Leader,Champion', 540],
    ['Running Repairs',  'Driving', '', 'Leader,Champion', 550],
    // Ferocity
    ['Berserker',        'Ferocity', '', 'Leader,Champion', 600],
    ['Fearsome',         'Ferocity', '', 'Leader,Champion', 610],
    ['Impetuous',        'Ferocity', '', 'Leader,Champion', 620],
    ['Nerves of Steel',  'Ferocity', '', 'Leader,Champion', 630],
    ['True Grit',        'Ferocity', '', 'Leader,Champion', 640],
    ['Unstoppable',      'Ferocity', '', 'Leader,Champion', 650],
    // Leadership
    ['Commanding Presence', 'Leadership', '', 'Leader,Champion', 700],
    ['Inspirational',       'Leadership', '', 'Leader,Champion', 710],
    ['Iron Will',           'Leadership', '', 'Leader,Champion', 720],
    ['Mentor',              'Leadership', '', 'Leader,Champion', 730],
    ['Overseer',            'Leadership', '', 'Leader,Champion', 740],
    ['Regroup',             'Leadership', '', 'Leader,Champion', 750],
    // Savant
    ['Ballistics Expert', 'Savant', '', 'Leader,Champion', 800],
    ['Connected',         'Savant', '', 'Leader,Champion', 810],
    ['Fixer',             'Savant', '', 'Leader,Champion', 820],
    ['Medicae',           'Savant', '', 'Leader,Champion', 830],
    ['Munitioneer',       'Savant', '', 'Leader,Champion', 840],
    ['Savvy Trader',      'Savant', '', 'Leader,Champion', 850],
    // Shooting
    ['Fast Shot',       'Shooting', '', 'Leader,Champion', 900],
    ['Gunfighter',      'Shooting', '', 'Leader,Champion', 910],
    ['Hip Shooting',    'Shooting', '', 'Leader,Champion', 920],
    ['Marksman',        'Shooting', '', 'Leader,Champion', 930],
    ['Precision Shot',  'Shooting', '', 'Leader,Champion', 940],
    ['Trick Shot',      'Shooting', '', 'Leader,Champion', 950],
    // GSC Wyrd Powers – Telepathy
    ['Hypnosis',        'Telepathy', 'Genestealer Cult', 'Leader,Champion', 1000],
    ['Unbreakable Will','Telepathy', 'Genestealer Cult', 'Leader,Champion', 1010],
    ['Zealot',          'Telepathy', 'Genestealer Cult', 'Leader,Champion', 1020],
    ['Mind Control',    'Telepathy', 'Genestealer Cult', 'Leader,Champion', 1030],
    // GSC Wyrd Powers – Telekinesis
    ['Assail',      'Telekinesis', 'Genestealer Cult', 'Leader,Champion', 1100],
    ['Force Blast', 'Telekinesis', 'Genestealer Cult', 'Leader,Champion', 1110],
];

$stmt = $db->prepare('
    INSERT INTO skill_library (name, category, factions, roles, sort_order)
    SELECT ?, ?, ?, ?, ?
    FROM DUAL
    WHERE NOT EXISTS (
        SELECT 1 FROM skill_library WHERE name = ? AND category = ?
    )
');

$inserted = 0;
foreach ($skills as [$name, $category, $factions, $roles, $sort_order]) {
    $stmt->execute([$name, $category, $factions, $roles, $sort_order, $name, $category]);
    $inserted += $stmt->rowCount();
}

jsonResponse(['inserted' => $inserted, 'total' => count($skills)]);
