<?php
// Run once to seed Genestealer Cult equipment list into weapon_library.
// Visit this URL once after uploading, then delete it from the server.
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$db = getDb();

// Add category column if it doesn't exist
try {
    $db->exec("ALTER TABLE weapon_library ADD COLUMN category VARCHAR(50) NOT NULL DEFAULT '' AFTER gang_type");
} catch (PDOException $e) {
    // Column already exists — ignore
}

$weapons = [
    // [name, cost, category, sort_order]
    ['Autogun',                                    15,  'Basic Weapon',          100],
    ['Lasgun',                                     30,  'Basic Weapon',          110],
    ['Shotgun (solid & scatter ammo)',              30,  'Basic Weapon',          120],

    ['Chainsword',                                 25,  'Close Combat Weapon',   200],
    ['Fighting knife',                             15,  'Close Combat Weapon',   210],
    ['Heavy rock drill',                           90,  'Close Combat Weapon',   220],
    ['Heavy rock saw',                            120,  'Close Combat Weapon',   230],
    ['Heavy rock saw (upgraded)',                 135,  'Close Combat Weapon',   240],
    ['Heavy rock cutter',                          45,  'Close Combat Weapon',   250],
    ['Power hammer',                               30,  'Close Combat Weapon',   260],
    ['Power maul',                                 40,  'Close Combat Weapon',   270],
    ['Power pick',                                 45,  'Close Combat Weapon',   280],
    ['Power sword',                                75,  'Close Combat Weapon',   290],
    ['Shock stave (Staff of Office)',              25,  'Close Combat Weapon',   300],
    ['Shock whip',                                 25,  'Close Combat Weapon',   310],
    ['Two-handed hammer',                          35,  'Close Combat Weapon',   320],

    ['Autopistol',                                 10,  'Pistol',                400],
    ['Laspistol',                                  10,  'Pistol',                410],
    ['Hand flamer',                                50,  'Pistol',                420],
    ['Needle pistol',                              40,  'Pistol',                430],

    ['Grenade launcher (frag & krak grenades)',    55,  'Special Weapon',        500],
    ['Flamer',                                    140,  'Special Weapon',        510],
    ['Long las',                                   20,  'Special Weapon',        520],
    ['Web gun',                                   125,  'Special Weapon',        530],

    ['Mining laser',                              125,  'Heavy Weapon',          600],
    ['Seismic cannon',                            140,  'Heavy Weapon',          610],
    ['Heavy stubber',                             145,  'Heavy Weapon',          620],

    ['Blasting charges',                           35,  'Grenade',               700],
    ['Demolition charges',                         65,  'Grenade',               710],
    ['Frag grenades',                              30,  'Grenade',               720],
    ['Incendiary charges',                         40,  'Grenade',               730],

    ['Hazard suit',                                10,  'Armour',                800],
    ['Flak armour',                                10,  'Armour',                810],
    ['Mesh armour',                                15,  'Armour',                820],

    ['Bio-booster',                                35,  'Personal Equipment',    900],
    ['Cult Icon',                                  40,  'Personal Equipment',    910],
    ['Filter plugs',                               10,  'Personal Equipment',    920],
    ['Photo-goggles',                              35,  'Personal Equipment',    930],
    ['Respirator',                                 15,  'Personal Equipment',    940],

    ['Psychic Familiar',                           25,  'Exotic Beast',         1000],
];

$stmt = $db->prepare("
    INSERT INTO weapon_library (gang_type, category, name, cost, sort_order)
    SELECT 'Genestealer Cult', ?, ?, ?, ?
    WHERE NOT EXISTS (
        SELECT 1 FROM weapon_library WHERE gang_type = 'Genestealer Cult' AND name = ?
    )
");

$inserted = 0;
$skipped  = 0;
foreach ($weapons as [$name, $cost, $category, $sort]) {
    $stmt->execute([$category, $name, $cost, $sort, $name]);
    if ($stmt->rowCount() > 0) $inserted++;
    else $skipped++;
}

header('Content-Type: text/plain');
echo "Done! Inserted: $inserted, Skipped (already existed): $skipped\n";
echo "DELETE THIS FILE FROM THE SERVER after running it.\n";
