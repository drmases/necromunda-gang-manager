<?php
// Seed Genestealer Cult fighter templates. Safe to run multiple times.
// Delete from server after running.
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$db = getDb();

// [name, cost, m, ws, bs, s, t, w, i, a, ld, cl, wil, int_stat, sort_order]
$templates = [
    ['Cult Adept (Leader)',       120, 5, 4, 4, 3, 3, 2, 3, 1, 3, 5, 5, 4, 10],
    ['Cult Alpha (Leader)',       145, 5, 3, 3, 3, 3, 2, 3, 2, 3, 5, 5, 4, 20],
    ['Hybrid Acolyte (Champion)',  85, 5, 3, 3, 3, 3, 1, 3, 1, 4, 4, 7, 5, 30],
    ['Aberrant (Ganger)',          95, 5, 3, 6, 5, 4, 2, 5, 2, 9, 4, 6, 10, 40],
    ['Neophyte Hybrid (Ganger)',   45, 4, 4, 4, 3, 3, 1, 4, 1, 7, 5, 6, 8, 50],
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
