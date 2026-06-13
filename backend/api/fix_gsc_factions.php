<?php
// Directly tag all Genestealer Cult weapons in the Universal list.
// Safe to run multiple times. Delete from server after running.
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$db = getDb();

// Ensure factions column exists
try {
    $db->exec("ALTER TABLE weapon_library ADD COLUMN factions TEXT NOT NULL DEFAULT ''");
} catch (PDOException $e) {}

// All weapon names available to Genestealer Cult (case-insensitive match)
$gscNames = [
    // Basic
    'autogun', 'lasgun', 'shotgun (solid ammo)', 'shotgun (solid & scatter ammo)',
    // Close combat
    'chainsword', 'fighting knife', 'heavy rock drill', 'heavy rock saw',
    'heavy rock saw (upgraded)', 'heavy rock cutter', 'power hammer',
    'power maul', 'power pick', 'power sword',
    'shock stave (staff of office)', 'shock whip', 'two-handed hammer',
    // Pistols
    'autopistol', 'laspistol', 'hand flamer', 'needle pistol',
    // Special
    'grenade launcher (frag grenades)', 'grenade launcher (frag & krak grenades)',
    'flamer', 'long las', 'web gun',
    // Heavy
    'mining laser', 'seismic cannon (short-wave)', 'seismic cannon', 'heavy stubber',
    // Grenades
    'blasting charges', 'demo charge', 'demolition charges',
    'frag grenade', 'frag grenades', 'incendiary charges',
    // Armour & equipment (GSC-exclusive, may already exist as Universal)
    'hazard suit', 'flak armour', 'mesh armour',
    'bio-booster', 'cult icon', 'filter plugs', 'photo-goggles', 'respirator',
    'psychic familiar',
];

$all = $db->query("SELECT id, name FROM weapon_library")->fetchAll(PDO::FETCH_ASSOC);

$tagStmt = $db->prepare("
    UPDATE weapon_library
    SET factions = CASE
        WHEN factions IS NULL OR factions = '' THEN 'Genestealer Cult'
        WHEN factions LIKE '%Genestealer Cult%' THEN factions
        ELSE CONCAT(factions, ',Genestealer Cult')
    END
    WHERE id = ?
");

$updated = 0;
$skipped = 0;
foreach ($all as $row) {
    if (in_array(strtolower(trim($row['name'])), $gscNames)) {
        $tagStmt->execute([$row['id']]);
        $updated++;
    } else {
        $skipped++;
    }
}

header('Content-Type: text/plain');
echo "Done!\n";
echo "Tagged $updated weapons for Genestealer Cult.\n";
echo "Skipped $skipped weapons (not in GSC list).\n";
echo "\nDELETE THIS FILE FROM THE SERVER after running it.\n";
