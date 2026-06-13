<?php
// Update stats for existing Genestealer Cult weapons in weapon_library.
// Run once, then delete from server.
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$db = getDb();

// [name, range_s, range_l, hit_s, hit_l, str, ap, dmg, ammo, traits]
$stats = [
    // Basic Weapons
    ['Autogun',                             '8"',  '24"', '+1', '-',  '3',    '-',  '1', '4+', 'Rapid Fire (1)'],
    ['Lasgun',                              '18"', '24"', '+1', '-',  '3',    '-',  '1', '2+', 'Plentiful'],
    ['Shotgun (solid & scatter ammo)',      '8"',  '24"', '+1', '-',  '4',    '-',  '2', '4+', 'Knockback, Plentiful, Scattershot'],

    // Close Combat Weapons
    ['Chainsword',                          'E',   'E',   '-',  '-',  'S+1',  '-1', '1', '-',  'Disarm, Melee, Parry'],
    ['Fighting knife',                      'E',   'E',   '-',  '-',  'S+1',  '-1', '1', '-',  'Backstab, Melee'],
    ['Heavy rock drill',                    'E',   'E',   '-',  '-',  'S+2',  '-2', '3', '-',  'Melee, Pulverise, Unwieldy'],
    ['Heavy rock saw',                      'E',   'E',   '-',  '-',  'S+3',  '-2', '2', '-',  'Melee, Rending, Unwieldy'],
    ['Heavy rock saw (upgraded)',           'E',   'E',   '-',  '-',  'S+3',  '-2', '2', '-',  'Melee, Rending, Unwieldy'],
    ['Heavy rock cutter',                   'E',   'E',   '-',  '-',  'S+4',  '-4', '3', '-',  'Melee, Unwieldy'],
    ['Power hammer',                        'E',   'E',   '-',  '-',  'S+2',  '-2', '3', '-',  'Melee, Power, Pulverise'],
    ['Power maul',                          'E',   'E',   '-',  '-',  'S+1',  '-2', '2', '-',  'Melee, Parry, Power'],
    ['Power pick',                          'E',   'E',   '-',  '-',  'S+2',  '-1', '3', '-',  'Melee, Parry, Power'],
    ['Power sword',                         'E',   'E',   '+1', '-',  'S+1',  '-2', '1', '-',  'Melee, Parry, Power'],
    ['Shock stave (Staff of Office)',       'E',   '3"',  '-',  '-',  'S+1',  '-',  '1', '-',  'Melee, Shock, Versatile'],
    ['Shock whip',                          'E',   '3"',  '-',  '-',  'S+1',  '-',  '1', '-',  'Melee, Shock, Versatile'],
    ['Two-handed hammer',                   'E',   'E',   '-',  '-',  'S+2',  '-1', '2', '-',  'Knockback, Melee, Unwieldy'],

    // Pistols
    ['Autopistol',                          '4"',  '12"', '+1', '-',  '3',    '-',  '1', '4+', 'Rapid Fire (1), Sidearm'],
    ['Laspistol',                           '8"',  '12"', '+1', '-',  '3',    '-',  '1', '2+', 'Plentiful, Sidearm'],
    ['Hand flamer',                         'T',   '-',   '-',  '-',  '3',    '-',  '1', '5+', 'Blaze, Template'],
    ['Needle pistol',                       '4"',  '9"',  '-',  '+2', '*',    '-',  '-', '6+', 'Scarce, Sidearm, Silent, Toxin'],

    // Special Weapons
    ['Grenade launcher (frag & krak grenades)', '6"', '24"', '-', '-', '3', '-', '1', '6+', 'Blast (3"), Combi, Knockback'],
    ['Flamer',                              'T',   '-',   '-',  '-',  '4',    '-',  '1', '5+', 'Blaze, Template'],
    ['Long las',                            '18"', '36"', '-',  '+1', '3',    '-1', '1', '4+', 'Scarce'],
    ['Web gun',                             'T',   '-',   '-',  '-',  '*',    '-',  '-', '6+', 'Scarce, Web'],

    // Heavy Weapons
    ['Mining laser',                        '12"', '24"', '+1', '+1', '9',    '-3', '3', '5+', 'Scarce, Unwieldy'],
    ['Seismic cannon',                      '12"', '24"', '-',  '+1', '5',    '-1', '1', '5+', 'Knockback, Rapid Fire (2), Unwieldy'],
    ['Heavy stubber',                       '20"', '40"', '+1', '-',  '4',    '-',  '1', '4+', 'Rapid Fire (3), Unwieldy'],

    // Grenades
    ['Blasting charges',                    'Sx3', 'Sx3', '-',  '-',  'S+1',  '-1', '3', '4+', 'Blast (5"), Grenade, Knockback'],
    ['Demolition charges',                  'Sx3', 'Sx3', '-',  '-',  'S+4',  '-3', '3', '-',  'Blast (5"), Grenade, Single Shot'],
    ['Frag grenades',                       'Sx3', 'Sx3', '-',  '-',  '3',    '-',  '1', '4+', 'Blast (3"), Grenade, Knockback'],
    ['Incendiary charges',                  'Sx3', 'Sx3', '-',  '-',  '3',    '-',  '1', '4+', 'Blaze, Grenade'],

    // Armour — no weapon stats, but fill range etc. with '-'
    ['Hazard suit',   '-', '-', '-', '-', '-', '-', '-', '-', 'Saves 6+, special vs Blaze/Toxin/Gas'],
    ['Flak armour',   '-', '-', '-', '-', '-', '-', '-', '-', 'Saves 6+'],
    ['Mesh armour',   '-', '-', '-', '-', '-', '-', '-', '-', 'Saves 5+'],

    // Personal Equipment — no weapon stats
    ['Bio-booster',       '-', '-', '-', '-', '-', '-', '-', '-', 'Recover 1 Flesh Wound once per battle'],
    ['Cult Icon',         '-', '-', '-', '-', '-', '-', '-', '-', 'Nearby fighters gain bonus to Bottle tests'],
    ['Filter plugs',      '-', '-', '-', '-', '-', '-', '-', '-', 'Immunity to Gas'],
    ['Photo-goggles',     '-', '-', '-', '-', '-', '-', '-', '-', 'Ignore darkness, reduce flash penalties'],
    ['Respirator',        '-', '-', '-', '-', '-', '-', '-', '-', '5+ save vs Gas'],

    // Exotic Beast
    ['Psychic Familiar',  '-', '-', '-', '-', '-', '-', '-', '-', 'Provides bonus to psychic actions'],
];

$stmt = $db->prepare("
    UPDATE weapon_library
    SET range_s=?, range_l=?, hit_s=?, hit_l=?, str=?, ap=?, dmg=?, ammo=?, traits=?
    WHERE gang_type='Genestealer Cult' AND name=?
");

$updated = 0;
$missed  = 0;
foreach ($stats as [$name, $rs, $rl, $hs, $hl, $str, $ap, $dmg, $ammo, $traits]) {
    $stmt->execute([$rs, $rl, $hs, $hl, $str, $ap, $dmg, $ammo, $traits, $name]);
    if ($stmt->rowCount() > 0) $updated++;
    else $missed++;
}

header('Content-Type: text/plain');
echo "Done! Updated: $updated, Not found: $missed\n";
echo "DELETE THIS FILE FROM THE SERVER after running it.\n";
