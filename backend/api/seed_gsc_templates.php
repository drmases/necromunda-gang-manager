<?php
// Seed Genestealer Cult fighter templates. Safe to run multiple times.
// Delete from server after running.
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$db = getDb();

// [name, cost, m, ws, bs, s, t, w, i, a, ld, cl, wil, int_stat, sort_order]
$templates = [
    ['Cult Adept (Leader)',       110, 5, 4, 3, 3, 3, 2, 4, 1, 7, 6, 8, 7, 10],
    ['Cult Alpha (Leader)',       115, 5, 3, 4, 3, 3, 2, 4, 2, 7, 6, 8, 7, 20],
    ['Hybrid Acolyte (Champion)',  85, 5, 3, 4, 3, 3, 1, 4, 2, 8, 7, 8, 8, 30],
    ['Aberrant (Ganger)',          75, 4, 3, 6, 5, 4, 2, 4, 2, 8, 6, 6, 9, 40],
    ['Neophyte Hybrid (Ganger)',   35, 5, 4, 4, 3, 3, 1, 4, 1, 8, 7, 8, 8, 50],
];

$check  = $db->prepare("SELECT id FROM fighter_templates WHERE gang_type = 'Genestealer Cult' AND name = ? LIMIT 1");
$insert = $db->prepare("
    INSERT INTO fighter_templates
        (gang_type, name, cost, m, ws, bs, s, t, w, i, a, ld, cl, wil, int_stat, sort_order, notes)
    VALUES
        ('Genestealer Cult', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '')
");

$inserted = 0;
foreach ($templates as [$name, $cost, $m, $ws, $bs, $s, $t, $w, $i, $a, $ld, $cl, $wil, $int_stat, $sort_order]) {
    $check->execute([$name]);
    if (!$check->fetch()) {
        $insert->execute([$name, $cost, $m, $ws, $bs, $s, $t, $w, $i, $a, $ld, $cl, $wil, $int_stat, $sort_order]);
        $inserted++;
    }
}

header('Content-Type: text/plain');
echo "Done!\n";
echo "Inserted $inserted templates (skipped " . (count($templates) - $inserted) . " duplicates).\n";
echo "\nDelete this file from the server after running.\n";
