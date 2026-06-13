<?php
// Upsert all 5 GSC fighter templates with correct stats from the rulebook.
// Safe to run multiple times. Delete from server after running.
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$db = getDb();

// [name, cost, m, ws, bs, s, t, w, i, a, ld, cl, wil, int_stat, sort_order]
// Stats with "+" stored as the number only (e.g. WS4+ → 4, M5 → 5)
$fighters = [
    ['Cult Adept (Leader)',       120, 5, 4, 4, 3, 3, 2, 3, 1,  3, 5, 5,  4, 10],
    ['Cult Alpha (Leader)',       145, 5, 3, 3, 3, 3, 2, 3, 2,  3, 5, 5,  4, 20],
    ['Hybrid Acolyte (Champion)',  85, 5, 3, 3, 3, 3, 1, 3, 1,  4, 4, 7,  5, 30],
    ['Aberrant (Ganger)',          95, 5, 3, 6, 5, 4, 2, 5, 2,  9, 4, 6, 10, 40],
    ['Neophyte Hybrid (Ganger)',   45, 4, 4, 4, 3, 3, 1, 4, 1,  7, 5, 6,  8, 50],
];

// Add special_rules column if missing
try { $db->exec("ALTER TABLE fighter_templates ADD COLUMN special_rules TEXT NOT NULL DEFAULT ''"); } catch (PDOException $e) {}

$specialRules = [
    'Cult Adept (Leader)'      => json_encode([['name'=>'Psyker','description'=>'The Cult Adept is a Psyker as described on page 75 of the Necromunda Rulebook. Genestealer Cults Wyrd Powers are treated as Primary skill sets for the purposes of skill selection. A Cult Adept is always a later generation hybrid.']]),
    'Cult Alpha (Leader)'      => json_encode([['name'=>'Extra Arm','description'=>'May use a third arm to better handle Unwieldy weapons or gain an extra attack. If armed with an Unwieldy ranged weapon, shooting becomes a Basic action. Otherwise gains +1 Attack and may carry a fourth weapon. The extra attack gains the Rending trait.']]),
    'Hybrid Acolyte (Champion)'=> json_encode([['name'=>'Extra Arm','description'=>'May use a third arm to better handle Unwieldy weapons or gain an extra attack. If armed with an Unwieldy ranged weapon, shooting becomes a Basic action. Otherwise gains +1 Attack and may carry a fourth weapon. The extra attack gains the Rending trait.']]),
    'Aberrant (Ganger)'        => json_encode([['name'=>'Unstoppable','description'=>'All Aberrants have the Unstoppable skill.']]),
    'Neophyte Hybrid (Ganger)' => json_encode([['name'=>'Extra Arm','description'=>'An early generation Neophyte Hybrid has a third arm. May use it to better handle Unwieldy weapons or gain an extra attack. The extra attack gains the Rending trait. Early generation costs +45 credits.']]),
];

$check  = $db->prepare("SELECT id FROM fighter_templates WHERE gang_type='Genestealer Cult' AND name=? LIMIT 1");
$update = $db->prepare("UPDATE fighter_templates SET cost=?,m=?,ws=?,bs=?,s=?,t=?,w=?,i=?,a=?,ld=?,cl=?,wil=?,int_stat=?,sort_order=?,special_rules=? WHERE gang_type='Genestealer Cult' AND name=?");
$insert = $db->prepare("INSERT INTO fighter_templates (gang_type,name,cost,m,ws,bs,s,t,w,i,a,ld,cl,wil,int_stat,sort_order,notes,special_rules) VALUES ('Genestealer Cult',?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'',?)");

$updated = 0; $inserted = 0;
foreach ($fighters as [$name, $cost, $m, $ws, $bs, $s, $t, $w, $i, $a, $ld, $cl, $wil, $int_stat, $sort_order]) {
    $rules = $specialRules[$name] ?? '[]';
    $check->execute([$name]);
    if ($check->fetch()) {
        $update->execute([$cost, $m, $ws, $bs, $s, $t, $w, $i, $a, $ld, $cl, $wil, $int_stat, $sort_order, $rules, $name]);
        $updated++;
    } else {
        $insert->execute([$name, $cost, $m, $ws, $bs, $s, $t, $w, $i, $a, $ld, $cl, $wil, $int_stat, $sort_order, $rules]);
        $inserted++;
    }
}

header('Content-Type: text/plain');
echo "Done!\n";
echo "Inserted $inserted, updated $updated fighter templates.\n";
echo "\nDelete this file from the server after running.\n";
