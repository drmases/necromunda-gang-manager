<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';
$db = getDb();
// Add factions column
try {
    $db->exec("ALTER TABLE weapon_library ADD COLUMN factions TEXT NOT NULL DEFAULT '' AFTER traits");
} catch (PDOException $e) {}
// Tag existing GSC weapons
$db->exec("UPDATE weapon_library SET factions='Genestealer Cult' WHERE gang_type='Genestealer Cult' AND factions=''");
header('Content-Type: text/plain');
echo "Done. DELETE this file.\n";
