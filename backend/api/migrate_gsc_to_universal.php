<?php
// Merge Genestealer Cult weapons into the Universal list.
// For each GSC weapon:
//   - If a matching Universal weapon exists → add 'Genestealer Cult' to its factions, delete the GSC duplicate
//   - If GSC-exclusive → move it to gang_type='Universal' with factions='Genestealer Cult'
// Run once, then delete from server.
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

$db = getDb();

// Map of GSC weapon name → Universal weapon name (where they differ)
$nameMap = [
    'Shotgun (solid & scatter ammo)'           => 'Shotgun (solid ammo)',
    'Grenade launcher (frag & krak grenades)'  => 'Grenade launcher (frag)',
    'Autogun'                                  => 'Autogun',
    'Lasgun'                                   => 'Lasgun',
    'Chainsword'                               => 'Chainsword',
    'Fighting knife'                           => 'Fighting Knife',
    'Heavy rock drill'                         => 'Heavy Rock Drill',
    'Heavy rock saw'                           => 'Heavy Rock Saw',
    'Heavy rock saw (upgraded)'                => 'Heavy Rock Saw (upgraded)',
    'Heavy rock cutter'                        => 'Heavy Rock Cutter',
    'Power hammer'                             => 'Power Hammer',
    'Power maul'                               => 'Power Maul',
    'Power pick'                               => 'Power Pick',
    'Power sword'                              => 'Power Sword',
    'Shock stave (Staff of Office)'            => 'Shock Stave (Staff of Office)',
    'Shock whip'                               => 'Shock Whip',
    'Two-handed hammer'                        => 'Two-Handed Hammer',
    'Autopistol'                               => 'Autopistol',
    'Laspistol'                                => 'Laspistol',
    'Hand flamer'                              => 'Hand Flamer',
    'Needle pistol'                            => 'Needle Pistol',
    'Flamer'                                   => 'Flamer',
    'Long las'                                 => 'Long Las',
    'Web gun'                                  => 'Web Gun',
    'Mining laser'                             => 'Mining Laser',
    'Seismic cannon'                           => 'Seismic Cannon (short-wave)',
    'Heavy stubber'                            => 'Heavy Stubber',
    'Blasting charges'                         => 'Blasting Charges',
    'Demolition charges'                       => 'Demo Charge',
    'Frag grenades'                            => 'Frag Grenade',
    'Incendiary charges'                       => 'Incendiary Charges',
];

// GSC-exclusive items: will be kept as Universal + factions='Genestealer Cult'
// (anything not in $nameMap above is treated as exclusive)

$tagged   = 0;
$moved    = 0;
$deleted  = 0;
$skipped  = 0;

// Fetch all GSC weapons
$gscWeapons = $db->query("SELECT * FROM weapon_library WHERE gang_type = 'Genestealer Cult'")->fetchAll(PDO::FETCH_ASSOC);

$addFaction = $db->prepare("
    UPDATE weapon_library
    SET factions = CASE
        WHEN factions = '' THEN 'Genestealer Cult'
        WHEN FIND_IN_SET('Genestealer Cult', factions) > 0 THEN factions
        ELSE CONCAT(factions, ',Genestealer Cult')
    END
    WHERE id = ?
");

$deleteGsc = $db->prepare("DELETE FROM weapon_library WHERE id = ?");

$moveToUniversal = $db->prepare("
    UPDATE weapon_library
    SET gang_type = 'Universal',
        factions  = 'Genestealer Cult'
    WHERE id = ?
");

foreach ($gscWeapons as $gsc) {
    $gscName     = $gsc['name'];
    $universalName = $nameMap[$gscName] ?? null;

    if ($universalName !== null) {
        // Find matching Universal weapon (case-insensitive)
        $find = $db->prepare("SELECT id FROM weapon_library WHERE gang_type = 'Universal' AND LOWER(name) = LOWER(?) LIMIT 1");
        $find->execute([$universalName]);
        $universal = $find->fetch(PDO::FETCH_ASSOC);

        if ($universal) {
            // Tag Universal entry and delete GSC duplicate
            $addFaction->execute([$universal['id']]);
            $deleteGsc->execute([$gsc['id']]);
            $tagged++;
            $deleted++;
        } else {
            // Universal entry not found — move GSC item to Universal
            $moveToUniversal->execute([$gsc['id']]);
            $moved++;
        }
    } else {
        // GSC-exclusive item — move to Universal tagged for GSC
        $moveToUniversal->execute([$gsc['id']]);
        $moved++;
    }
}

header('Content-Type: text/plain');
echo "Done!\n";
echo "Universal weapons tagged for GSC: $tagged\n";
echo "GSC-exclusive items moved to Universal: $moved\n";
echo "Duplicate GSC entries deleted: $deleted\n";
echo "\nDELETE THIS FILE FROM THE SERVER after running it.\n";
