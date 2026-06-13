<?php
// Fix GSC fighter template stats with correct values from the rulebook.
// Safe to run multiple times. Delete from server after running.
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$db = getDb();

// [name, cost, m, ws, bs, s, t, w, i, a, ld, cl, wil, int_stat]
// Stats with "+" are stored as the number only (e.g. WS4+ → 4)
$updates = [
    ['Cult Adept (Leader)',  120, 5, 4, 4, 3, 3, 2, 3, 1, 3, 5, 5, 4],
    ['Cult Alpha (Leader)',  145, 5, 3, 3, 3, 3, 2, 3, 2, 3, 5, 5, 4],
];

$stmt = $db->prepare("
    UPDATE fighter_templates
    SET cost=?, m=?, ws=?, bs=?, s=?, t=?, w=?, i=?, a=?, ld=?, cl=?, wil=?, int_stat=?
    WHERE gang_type='Genestealer Cult' AND name=?
");

$updated = 0;
foreach ($updates as [$name, $cost, $m, $ws, $bs, $s, $t, $w, $i, $a, $ld, $cl, $wil, $int_stat]) {
    $stmt->execute([$cost, $m, $ws, $bs, $s, $t, $w, $i, $a, $ld, $cl, $wil, $int_stat, $name]);
    $updated += $stmt->rowCount();
}

header('Content-Type: text/plain');
echo "Done!\n";
echo "Updated $updated fighter templates.\n";
echo "\nDelete this file from the server after running.\n";
