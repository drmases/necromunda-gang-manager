<?php
// Seed injury library. Safe to run multiple times. Delete from server after running.
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$db = getDb();

// Ensure injury_library table exists
$db->exec("
    CREATE TABLE IF NOT EXISTS injury_library (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        name        VARCHAR(100) NOT NULL,
        category    VARCHAR(50)  NOT NULL DEFAULT '',
        description VARCHAR(255) NOT NULL DEFAULT '',
        sort_order  INT          NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// [name, category, description, sort_order]
$injuries = [
    // D66 Lasting Injury table
    ['Lesson Learned',      'Lasting Injury', 'The fighter goes into Convalescence but gains D3 Experience.', 10],
    ['Impressive Scars',    'Lasting Injury', 'The fighter goes into Convalescence. Gains +1 Cool characteristic permanently.', 11],
    ['Horrid Scars',        'Lasting Injury', 'The fighter goes into Convalescence. Gains the Fearsome skill.', 12],
    ['Bitter Enmity',       'Lasting Injury', 'The fighter goes into Convalescence. Gains a bitter grudge against the gang that inflicted the injury.', 13],
    ['Out Cold',            'Lasting Injury', 'The fighter misses the rest of the battle but avoids any long lasting injuries.', 14],
    ['Convalescence',       'Lasting Injury', 'The fighter recovers in time to perform post-battle actions but misses the next battle.', 15],
    ['Old Battle Wound',    'Lasting Injury', 'At the end of each battle this fighter participates in, roll a D6 on a 1 the fighter goes Out of Action.', 16],
    ['Partially Deafened',  'Lasting Injury', 'The fighter suffers no penalty if they are Partially Deafened, however suffering this injury again reduces their Leadership by 1.', 17],
    ['Humiliated',          'Lasting Injury', 'The fighter goes into Convalescence and their Leadership and Cool characteristics are each decreased by 1.', 18],
    ['Eye Injury',          'Lasting Injury', 'The fighter goes into Recovery and their Ballistic Skill characteristic is decreased by 1.', 19],
    ['Hand Injury',         'Lasting Injury', 'The fighter goes into Recovery and their Weapon Skill characteristic is decreased by 1.', 20],
    ['Hobbled',             'Lasting Injury', 'The fighter goes into Recovery and their Movement characteristic is decreased by 1.', 21],
    ['Spinal Injury',       'Lasting Injury', 'The fighter goes into Recovery and their Strength and Toughness characteristics are each decreased by 1.', 22],
    ['Enfeebled',           'Lasting Injury', 'The fighter goes into Recovery and their Toughness characteristic is decreased by 1.', 23],
    ['Head Injury',         'Lasting Injury', 'The fighter goes into Recovery and their Intelligence and Willpower characteristics are each decreased by 1.', 24],
    ['Multiple Injuries',   'Lasting Injury', 'The fighter has suffered many grievous wounds. Roll two more times on this table re-rolling any Memorable Death or Critical Injury results.', 25],
    ['Captured',            'Lasting Injury', 'The fighter might be Captured (see page 142).', 26],
    ['Critical Injury',     'Lasting Injury', 'The fighter is in a critical condition – if their injuries are not successfully treated by a visit to the Doc they will die.', 27],
    ['Memorable Death',     'Memorable Death', 'The fighter is killed instantly – not even the most talented Doc can save them. If the injury was caused by an Attack action, the attacker gains 1 additional XP.', 28],
];

$check  = $db->prepare('SELECT id FROM injury_library WHERE name = ? LIMIT 1');
$insert = $db->prepare('INSERT INTO injury_library (name, category, description, sort_order) VALUES (?, ?, ?, ?)');

$inserted = 0;
foreach ($injuries as [$name, $category, $description, $sort_order]) {
    $check->execute([$name]);
    if (!$check->fetch()) {
        $insert->execute([$name, $category, $description, $sort_order]);
        $inserted++;
    }
}

header('Content-Type: text/plain');
echo "Done!\n";
echo "Inserted $inserted injuries (skipped " . (count($injuries) - $inserted) . " duplicates).\n";
echo "\nDelete this file from the server after running.\n";
