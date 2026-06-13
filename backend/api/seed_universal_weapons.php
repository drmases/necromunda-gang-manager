<?php
// Seed all universal Necromunda weapons into weapon_library with gang_type='Universal'.
// Run once, then delete from the server.
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$db = getDb();

// Add category column if not exists
try {
    $db->exec("ALTER TABLE weapon_library ADD COLUMN category VARCHAR(50) NOT NULL DEFAULT '' AFTER gang_type");
} catch (PDOException $e) {}

// [name, cost, category, sort_order, range_s, range_l, hit_s, hit_l, str, ap, dmg, ammo, traits]
$weapons = [

    // ── PISTOLS ──────────────────────────────────────────────────────────────
    ['Autopistol',                      10,  'Pistol', 1000, '4"',  '12"', '+1', '-',  '3',    '-',  '1', '4+', 'Rapid Fire (1), Sidearm'],
    ['Autopistol (warp round)',          0,  'Pistol', 1001, '4"',  '12"', '+1', '-',  '3',    '-',  '1', '4+', 'Limited, Rapid Fire (1), Sidearm'],
    ['Compact Autopistol (Gun Skull)',   0,  'Pistol', 1002, '4"',  '12"', '+1', '-',  '2',    '-',  '1', '4+', 'Rapid Fire (1), Scarce'],
    ['Reclaimed Autopistol',             5,  'Pistol', 1003, '4"',  '12"', '+1', '-',  '3',    '-',  '1', '5+', 'Rapid Fire (1), Sidearm'],
    ['Bolt Pistol',                     45,  'Pistol', 1010, '6"',  '12"', '+1', '+1', '4',    '-1', '2', '6+', 'Rapid Fire (1), Sidearm'],
    ['Bolt Pistol (gas shells)',          0,  'Pistol', 1011, '6"',  '12"', '+1', '+1', '-',    '-',  '2', '6+', 'Blast (3"), Gas, Limited, Single Shot, Sidearm'],
    ['Bolt Pistol (shatter shells)',      0,  'Pistol', 1012, '6"',  '12"', '+1', '+1', '3',    '-1', '2', '6+', 'Blast (3"), Limited, Sidearm'],
    ['Plasma Pistol',                   50,  'Pistol', 1020, '6"',  'T',   '+2', '-',  '5',    '-2', '2', '5+', 'Combi, Scarce, Sidearm'],
    ['Stub Gun',                         5,  'Pistol', 1030, '6"',  '9"',  '+2', '-',  '3',    '-',  '1', '4+', 'Plentiful, Sidearm'],
    ['Stub Gun (static round)',           0,  'Pistol', 1031, '6"',  '9"',  '+2', '-',  '3',    '-',  '1', '4+', 'Plentiful, Sidearm'],
    ['Stub Gun (warp round)',             0,  'Pistol', 1032, '6"',  '9"',  '+2', '-',  '3',    '-',  '1', '4+', 'Limited, Scarce, Sidearm'],
    ['Laspistol',                       10,  'Pistol', 1040, '8"',  '12"', '+1', '-',  '3',    '-',  '1', '2+', 'Plentiful, Sidearm'],
    ['Laspistol (hotshot las pack)',     20,  'Pistol', 1041, '8"',  '12"', '+1', '+1', '4',    '-1', '1', '-',  'Scarce, Sidearm'],
    ['Laspistol (focusing crystal)',     30,  'Pistol', 1042, '8"',  '12"', '+1', '+1', '4',    '-1', '1', '-',  'Sidearm'],
    ['Hand Flamer',                     75,  'Pistol', 1050, 'T',   '-',   '-',  '-',  '3',    '-',  '1', '5+', 'Blaze, Template'],
    ['Inferno Pistol',                 145,  'Pistol', 1051, '6"',  '12"', '+1', '+1', '8',    '-4', '3', '6+', 'Melta, Scarce, Sidearm'],
    ['Needle Pistol',                   30,  'Pistol', 1060, '4"',  '9"',  '-',  '+2', '*',    '-',  '-', '6+', 'Scarce, Sidearm, Silent, Toxin'],
    ['Needle Pistol (chem darts)',        0,  'Pistol', 1061, '4"',  '9"',  '-',  '+2', '*',    '-',  '-', '6+', 'Chem Delivery, Limited, Sidearm, Silent'],
    ['Grav Pistol',                     70,  'Pistol', 1070, '6"',  '12"', '+2', '+1', '*',    '-1', '-', '5+', 'Blast (3"), Concussion, Graviton Pulse'],
    ['Web Pistol',                      80,  'Pistol', 1080, 'T',   '-',   '-',  '-',  '*',    '-',  '-', '4+', 'Scarce, Sidearm, Web'],
    ['Volkite Pistol',                  30,  'Pistol', 1090, '6"',  '12"', '+2', '+2', '5',    '-',  '2', '4+', 'Blaze, Combi'],

    // ── BASIC WEAPONS ────────────────────────────────────────────────────────
    ['Autogun',                         15,  'Basic Weapon', 2000, '8"',  '24"', '+1', '-',  '3',  '-',  '1', '4+', 'Rapid Fire (1)'],
    ['Autogun (warp round)',              0,  'Basic Weapon', 2001, '8"',  '24"', '+1', '-',  '3',  '-',  '1', '4+', 'Limited, Rapid Fire (1)'],
    ['Reclaimed Autogun',               10,  'Basic Weapon', 2002, '8"',  '24"', '+1', '-',  '3',  '-',  '1', '5+', 'Rapid Fire (1)'],
    ['Shotgun (solid ammo)',            15,  'Basic Weapon', 2010, '8"',  '24"', '+1', '-',  '4',  '-',  '2', '4+', 'Knockback, Plentiful, Scattershot'],
    ['Shotgun (scatter ammo)',           0,  'Basic Weapon', 2011, '4"',  '8"',  '-',  '-',  '2',  '-',  '1', '4+', 'Plentiful, Scattershot, Sidearm'],
    ['Shotgun (salvo ammo)',             0,  'Basic Weapon', 2012, '8"',  '16"', '+1', '-',  '4',  '-',  '2', '5+', 'Rapid Fire (1), Scattershot'],
    ['Shotgun (firestorm ammo)',         0,  'Basic Weapon', 2013, '8"',  '16"', '+1', '-',  '4',  '-',  '2', '6+', 'Blaze, Scattershot'],
    ['Shotgun (blast shells)',           0,  'Basic Weapon', 2014, '4"',  '18"', '+1', '-',  '4',  '-1', '2', '6+', 'Blast (3"), Limited, Scattershot'],
    ['Lasgun',                          10,  'Basic Weapon', 2020, '18"', '24"', '+1', '-',  '3',  '-',  '1', '2+', 'Plentiful'],
    ['Lasgun (hotshot las pack)',        15,  'Basic Weapon', 2021, '18"', '24"', '+1', '-',  '4',  '-1', '1', '-',  'Scarce'],
    ['Lasgun (focusing crystal)',        25,  'Basic Weapon', 2022, '18"', '24"', '+1', '+1', '4',  '-1', '1', '-',  ''],
    ['Needle Rifle',                    25,  'Basic Weapon', 2030, '12"', '24"', '-',  '+2', '*',  '-',  '-', '6+', 'Scarce, Silent, Toxin'],
    ['Stub Rifle',                      15,  'Basic Weapon', 2040, '6"',  '24"', '+1', '-',  '3',  '-',  '1', '3+', 'Plentiful'],
    ['Sling Gun',                       20,  'Basic Weapon', 2050, '4"',  '16"', '-',  '+1', '4',  '-',  '2', '5+', 'Plentiful'],
    ["Kroop's Long Rifle",              30,  'Basic Weapon', 2060, '18"', '36"', '-',  '+1', '4',  '-1', '2', '4+', 'Esoteric, Scarce, Unwieldy'],

    // ── SPECIAL WEAPONS ───────────────────────────────────────────────────────
    ['Flamer',                         140,  'Special Weapon', 3000, 'T',   '-',   '-',  '-',  '4',  '-',  '1', '5+', 'Blaze, Template'],
    ['Grenade Launcher (frag grenades)', 55, 'Special Weapon', 3010, '6"',  '24"', '-',  '-',  '3',  '-',  '1', '6+', 'Blast (3"), Combi, Knockback'],
    ['Grenade Launcher (krak grenades)', 0,  'Special Weapon', 3011, '6"',  '24"', '-',  '-',  '6',  '-2', '2', '6+', 'Combi'],
    ['Grenade Launcher (stun rounds)',   0,  'Special Weapon', 3012, '6"',  '24"', '-',  '-',  '-',  '-',  '-', '6+', 'Blast (3"), Combi, Concussion'],
    ['Bolter combi-weapon',              0,  'Special Weapon', 3020, '6"',  '24"', '-',  '-',  '4',  '-1', '2', '6+', 'Blaze, Combi, Rapid Fire (1), Template'],
    ['Bolter combi-weapon (frag)',        0,  'Special Weapon', 3021, '6"',  '24"', '+1', '-',  '3',  '-',  '1', '6+', 'Blast (3"), Combi, Knockback'],
    ['Bolter combi-weapon (stun rounds)',0,  'Special Weapon', 3022, '6"',  '24"', '+1', '-',  '-',  '-',  '-', '6+', 'Blast (3"), Combi, Concussion'],
    ['Long Las',                        20,  'Special Weapon', 3030, '18"', '36"', '-',  '+1', '3',  '-1', '1', '4+', 'Scarce'],
    ['Meltagun',                       135,  'Special Weapon', 3040, '6"',  '12"', '+1', '+1', '8',  '-4', '3', '6+', 'Melta, Scarce'],
    ['Plasma Gun (low)',               100,  'Special Weapon', 3050, '12"', '24"', '+2', '+1', '6',  '-2', '2', '5+', 'Combi, Rapid Fire (1), Scarce'],
    ['Plasma Gun (maximal)',             0,  'Special Weapon', 3051, '12"', '24"', '+2', '+1', '8',  '-3', '3', '5+', 'Blast (5"), Combi, Rapid Fire (1), Scarce, Unstable'],
    ['Web Gun',                        125,  'Special Weapon', 3060, 'T',   '-',   '-',  '-',  '*',  '-',  '-', '6+', 'Scarce, Web'],
    ['Arc Rifle',                      120,  'Special Weapon', 3070, '9"',  '18"', '+1', '-',  '6',  '-2', '1', '4+', 'Esoteric, Rapid Fire (1), Scarce, Unstable'],
    ['Rad Gun',                        100,  'Special Weapon', 3080, '6"',  '12"', '+1', '+1', '3',  '-',  '1', '4+', 'Esoteric, Irradiated, Rapid Fire (1), Scarce'],
    ['Rad Gun (irradiated ammo)',        0,  'Special Weapon', 3081, '6"',  '12"', '+1', '+1', '3',  '-',  '1', '4+', 'Esoteric, Irradiated, Scarce'],
    ['Concussion Carbine',              30,  'Special Weapon', 3090, '6"',  '24"', '-',  '+1', '2',  '-',  '1', '4+', 'Concussion'],
    ['Needle Rifle (special)',          25,  'Special Weapon', 3100, '12"', '24"', '-',  '+2', '*',  '-',  '-', '6+', 'Scarce, Silent, Toxin'],
    ['Plasma Gun',                     100,  'Special Weapon', 3110, '12"', '24"', '+2', '+1', '6',  '-2', '2', '5+', 'Combi, Rapid Fire (1), Scarce'],

    // ── HEAVY WEAPONS ─────────────────────────────────────────────────────────
    ['Autocannon',                     100,  'Heavy Weapon', 4000, '24"', '48"', '+1', '+1', '8',  '-3', '3', '4+', 'Knockback, Rapid Fire (1), Scarce, Unwieldy'],
    ['Heavy Bolter',                   160,  'Heavy Weapon', 4010, '24"', '48"', '+1', '+1', '5',  '-2', '2', '6+', 'Rapid Fire (2), Unwieldy'],
    ['Heavy Stubber',                  130,  'Heavy Weapon', 4020, '20"', '40"', '+1', '-',  '4',  '-',  '1', '4+', 'Rapid Fire (3), Unwieldy'],
    ['Heavy Stubber (static round)',     0,  'Heavy Weapon', 4021, '20"', '40"', '+1', '-',  '4',  '-',  '1', '4+', 'Rapid Fire (3), Scarce, Unwieldy'],
    ['Heavy Stubber (warp round)',       0,  'Heavy Weapon', 4022, '20"', '40"', '+1', '-',  '4',  '-',  '1', '4+', 'Limited, Rapid Fire (3), Scarce, Unwieldy'],
    ['Lascannon',                      155,  'Heavy Weapon', 4030, '24"', '48"', '+1', '+1', '10', '-3', '4', '6+', 'Scarce, Unwieldy'],
    ['Mining Laser',                   125,  'Heavy Weapon', 4040, '12"', '24"', '+1', '+1', '9',  '-3', '3', '5+', 'Scarce, Unwieldy'],
    ['Missile Launcher (frag)',        165,  'Heavy Weapon', 4050, '24"', '48"', '-',  '-',  '4',  '-',  '1', '6+', 'Blast (5"), Combi, Scarce, Single Shot, Unwieldy'],
    ['Missile Launcher (krak)',          0,  'Heavy Weapon', 4051, '24"', '48"', '+1', '+1', '8',  '-2', '2', '6+', 'Combi, Scarce, Single Shot, Unwieldy'],
    ['Mole Launcher',                  145,  'Heavy Weapon', 4060, '18"', '24"', '-',  '+2', '5',  '-2', '3', '6+', 'Blast (3"), Cursed, Esoteric, Knockback, Unwieldy'],
    ['Multi-Melta',                    180,  'Heavy Weapon', 4070, '12"', '24"', '+1', '+1', '8',  '-4', '3', '6+', 'Blast (3"), Melta, Scarce, Unwieldy'],
    ['Plasma Cannon (low)',            130,  'Heavy Weapon', 4080, '18"', '36"', '+1', '-',  '6',  '-2', '2', '5+', 'Combi, Rapid Fire (1), Scarce, Unwieldy'],
    ['Plasma Cannon (maximal)',          0,  'Heavy Weapon', 4081, '18"', '36"', '+1', '-',  '8',  '-3', '3', '5+', 'Blast (5"), Combi, Rapid Fire (1), Scarce, Unstable, Unwieldy'],
    ['Seismic Cannon (short-wave)',    140,  'Heavy Weapon', 4090, '12"', '24"', '-',  '+1', '5',  '-1', '1', '5+', 'Knockback, Rapid Fire (2), Unwieldy'],
    ['Seismic Cannon (long-wave)',       0,  'Heavy Weapon', 4091, '12"', '24"', '-',  '+1', '6',  '-2', '2', '5+', 'Knockback, Unwieldy'],
    ['Harpoon Launcher',               110,  'Heavy Weapon', 4100, '18"', '24"', '+1', '-',  '6',  '-2', '3', '5+', 'Drag, Impale, Scarce, Unwieldy'],
    ['Grav Cannon',                    140,  'Heavy Weapon', 4110, '18"', '36"', '+1', '-',  '*',  '-2', '-', '5+', 'Blast (5"), Concussion, Graviton Pulse, Unwieldy'],
    ['Heavy Flamer',                   195,  'Heavy Weapon', 4120, 'T',   '-',   '-',  '-',  '5',  '-3', '1', '5+', 'Blaze, Template, Unwieldy'],
    ['Boltgun',                        100,  'Heavy Weapon', 4130, '8"',  '24"', '+1', '+1', '4',  '-1', '2', '6+', 'Rapid Fire (1)'],

    // ── CLOSE COMBAT WEAPONS ──────────────────────────────────────────────────
    ['Brawl',                            0,  'Close Combat Weapon', 5000, 'E', 'E', '-', '-', 'S',    '-',  '1', '-', 'Melee'],
    ['Fighting Knife',                  15,  'Close Combat Weapon', 5010, 'E', 'E', '-', '-', 'S+1',  '-1', '1', '-', 'Backstab, Melee'],
    ['Stiletto Knife',                  25,  'Close Combat Weapon', 5020, 'E', 'E', '-', '-', '*',    '-',  '-', '-', 'Backstab, Melee, Toxin'],
    ['Whisperbane Knife',               30,  'Close Combat Weapon', 5021, 'E', 'E', '-', '-', 'S+1',  '-1', '1', '-', 'Backstab, Melee, Silent'],
    ['Sword',                           20,  'Close Combat Weapon', 5030, 'E', 'E', '-', '-', 'S+1',  '-',  '1', '-', 'Melee, Parry'],
    ['Stiletto Sword',                  30,  'Close Combat Weapon', 5031, 'E', 'E', '-', '-', 'S+1',  '-2', '1', '-', 'Melee, Parry'],
    ['Chainsword',                      25,  'Close Combat Weapon', 5040, 'E', 'E', '-', '-', 'S+1',  '-1', '1', '-', 'Disarm, Melee, Parry'],
    ['Cleaver',                         30,  'Close Combat Weapon', 5050, 'E', 'E', '-', '-', 'S+1',  '-1', '2', '-', 'Disarm, Melee'],
    ['Maul (Club)',                     10,  'Close Combat Weapon', 5060, 'E', 'E', '-', '-', 'S+2',  '-',  '1', '-', 'Knockback, Melee'],
    ['Flail',                           20,  'Close Combat Weapon', 5070, 'E', 'E', '-', '-', 'S+2',  '-',  '1', '-', 'Entangle, Melee'],
    ['Whip',                            15,  'Close Combat Weapon', 5080, 'E', '3"','-', '-', 'S+1',  '-',  '1', '-', 'Entangle, Melee, Versatile'],
    ['Bill',                            20,  'Close Combat Weapon', 5090, 'E', 'E', '-', '-', 'S+2',  '-',  '2', '-', 'Melee, Versatile'],
    ['Digi Laser',                      25,  'Close Combat Weapon', 5100, 'E', 'E', '-', '-', '*',    '-1', '-', '-', 'Digi, Melee'],
    ['Servo Claw',                      35,  'Close Combat Weapon', 5110, 'E', 'E', '-', '-', 'S+2',  '-1', '2', '-', 'Melee'],
    ['Goredrinker Axe',                 40,  'Close Combat Weapon', 5120, 'E', 'E', '-', '-', 'S+2',  '-1', '2', '-', 'Esoteric, Melee, Reckless, Rending'],
    ['Heavy Club',                      15,  'Close Combat Weapon', 5130, 'E', 'E', '-', '-', 'S+2',  '-',  '2', '-', 'Cursed, Esoteric, Melee, Parry'],
    ['Hex Iron Blade',                  20,  'Close Combat Weapon', 5140, 'E', 'E', '-', '-', 'S+1',  '-',  '1', '-', 'Cursed, Melee'],
    ['Xenarch Death-Arc',               75,  'Close Combat Weapon', 5150, 'E', 'E', '-', '-', '*',    '-',  '-', '-', 'Esoteric, Melee, Rapid Fire (2), Shock, Versatile'],

    // ── POWER / SHOCK WEAPONS ─────────────────────────────────────────────────
    ["Death's Needle",                  50,  'Power/Shock Weapon', 6000, 'E', 'E', '+1', '-', 'S+2', '-1', '3', '-', 'Chem Delivery, Esoteric, Melee, Power, Toxin'],
    ['Lightning Claw',                   0,  'Power/Shock Weapon', 6010, 'E', 'E', '-',  '-', 'S+2', '-2', '2', '-', 'Melee, Parry, Power, Rending'],
    ['Power Claw',                      45,  'Power/Shock Weapon', 6020, 'E', 'E', '-',  '-', 'S+1', '-2', '3', '-', 'Disarm, Melee, Power, Unwieldy'],
    ['Power Fist',                      45,  'Power/Shock Weapon', 6030, 'E', 'E', '+1', '-', 'S+4', '-3', '3', '-', 'Melee, Power, Pulverise, Unwieldy'],
    ['Power Hammer',                    45,  'Power/Shock Weapon', 6040, 'E', 'E', '-',  '-', 'S+2', '-2', '3', '-', 'Melee, Power, Pulverise'],
    ['Power Maul',                      30,  'Power/Shock Weapon', 6050, 'E', 'E', '-',  '-', 'S+1', '-2', '2', '-', 'Melee, Parry, Power'],
    ['Power Pick',                      45,  'Power/Shock Weapon', 6060, 'E', 'E', '-',  '-', 'S+2', '-1', '3', '-', 'Melee, Parry, Power'],
    ['Power Sword',                     30,  'Power/Shock Weapon', 6070, 'E', 'E', '+1', '-', 'S+1', '-2', '1', '-', 'Melee, Parry, Power'],
    ['Shock Stave (Staff of Office)',   25,  'Power/Shock Weapon', 6080, 'E', '3"','-',  '-', 'S+1', '-',  '1', '-', 'Melee, Shock, Versatile'],
    ['Shock Whip',                      25,  'Power/Shock Weapon', 6081, 'E', '3"','-',  '-', 'S+1', '-',  '1', '-', 'Melee, Shock, Versatile'],
    ['Shock Baton',                      0,  'Power/Shock Weapon', 6090, 'E', 'E', '-',  '-', 'S+1', '-',  '2', '-', 'Melee, Shock'],
    ['Tenebrous Scourge',              110,  'Power/Shock Weapon', 6100, 'E', '2"','-',  '-', 'S+2', '-2', '3', '-', 'Esoteric, Melee, Power, Shock'],

    // ── TWO-HANDED WEAPONS ────────────────────────────────────────────────────
    ['Chain Glaive',                    20,  'Two-Handed Weapon', 7000, 'E', 'E', '-', '-', 'S+1', '-1', '1', '-', 'Melee, Unwieldy, Versatile'],
    ['Heavy Rock Cutter',              135,  'Two-Handed Weapon', 7010, 'E', 'E', '-', '-', 'S+4', '-4', '3', '-', 'Melee, Unwieldy'],
    ['Heavy Rock Drill',               170,  'Two-Handed Weapon', 7020, 'E', 'E', '-', '-', 'S+2', '-2', '3', '-', 'Melee, Pulverise, Unwieldy'],
    ['Heavy Rock Saw',                 120,  'Two-Handed Weapon', 7030, 'E', 'E', '-', '-', 'S+3', '-2', '2', '-', 'Melee, Rending, Unwieldy'],
    ['Heavy Rock Saw (upgraded)',      135,  'Two-Handed Weapon', 7031, 'E', 'E', '-', '-', 'S+3', '-2', '2', '-', 'Melee, Rending, Unwieldy'],
    ['Two-Handed Axe',                  25,  'Two-Handed Weapon', 7040, 'E', 'E', '-', '-', 'S+1', '-1', '2', '-', 'Melee, Unwieldy'],
    ['Two-Handed Hammer',               35,  'Two-Handed Weapon', 7050, 'E', 'E', '-', '-', 'S+2', '-1', '2', '-', 'Knockback, Melee, Unwieldy'],

    // ── GRENADES ──────────────────────────────────────────────────────────────
    ['Blasting Charges',                35,  'Grenade', 8000, 'Sx3', 'Sx3', '-', '-', 'S+1', '-1', '3', '4+', 'Blast (5"), Grenade, Knockback'],
    ['Demo Charge',                     65,  'Grenade', 8010, 'Sx3', 'Sx3', '-', '-', 'S+4', '-3', '3', '-',  'Blast (5"), Grenade, Single Shot'],
    ['Frag Grenade',                    30,  'Grenade', 8020, 'Sx3', 'Sx3', '-', '-', '3',   '-',  '1', '4+', 'Blast (3"), Grenade, Knockback'],
    ['Krak Grenade',                    45,  'Grenade', 8030, 'Sx3', 'Sx3', '-', '-', '6',   '-2', '2', '4+', 'Grenade'],
    ['Melta Bomb',                      60,  'Grenade', 8040, 'Sx3', 'Sx3', '-', '-', '8',   '-4', '3', '5+', 'Grenade, Melta, Single Shot'],
    ['Haywire Grenade',                 45,  'Grenade', 8050, 'Sx3', 'Sx3', '-', '-', '*',   '-',  '-', '4+', 'Grenade, Haywire'],
    ['Smoke Grenade',                   15,  'Grenade', 8060, 'Sx3', 'Sx3', '-', '-', '-',   '-',  '-', '4+', 'Grenade, Smoke'],
    ['Photon Flash Grenade',            15,  'Grenade', 8070, 'Sx3', 'Sx3', '-', '-', '-',   '-',  '-', '4+', 'Flash, Grenade'],
    ['Scare Gas Grenade',               45,  'Grenade', 8080, 'Sx3', 'Sx3', '-', '-', '-',   '-',  '-', '5+', 'Gas, Grenade, Scarce'],
    ['Incendiary Charges',              40,  'Grenade', 8090, 'Sx3', 'Sx3', '-', '-', '3',   '-',  '1', '4+', 'Blaze, Grenade'],
    ['Stun Grenade',                    15,  'Grenade', 8100, 'Sx3', 'Sx3', '-', '-', '-',   '-',  '-', '4+', 'Blast (3"), Concussion, Grenade'],

    // ── BOOBY TRAPS ───────────────────────────────────────────────────────────
    ['Flash Trap',                      20,  'Booby Trap', 9000, '-', '-', '-', '-', '-',  '-', '-', '-', 'Flash, Single Shot'],
    ['Rad Trap',                        10,  'Booby Trap', 9010, '-', '-', '-', '-', '*',  '-', '-', '-', 'Esoteric, Irradiated, Single Shot'],
    ['Melta Trap',                      30,  'Booby Trap', 9020, '-', '-', '-', '-', '8',  '-4','3', '-', 'Melta, Single Shot'],
];

$stmt = $db->prepare("
    INSERT INTO weapon_library (gang_type, category, name, cost, sort_order, range_s, range_l, hit_s, hit_l, str, ap, dmg, ammo, traits)
    SELECT 'Universal', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    WHERE NOT EXISTS (
        SELECT 1 FROM weapon_library WHERE gang_type = 'Universal' AND name = ?
    )
");

$inserted = 0;
$skipped  = 0;
foreach ($weapons as [$name, $cost, $category, $sort, $rs, $rl, $hs, $hl, $str, $ap, $dmg, $ammo, $traits]) {
    $stmt->execute([$category, $name, $cost, $sort, $rs, $rl, $hs, $hl, $str, $ap, $dmg, $ammo, $traits, $name]);
    if ($stmt->rowCount() > 0) $inserted++;
    else $skipped++;
}

header('Content-Type: text/plain');
echo "Done! Inserted: $inserted, Skipped (already existed): $skipped\n";
echo "DELETE THIS FILE FROM THE SERVER after running it.\n";
