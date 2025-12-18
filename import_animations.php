<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Bar;
use App\Models\MatchGame;
use App\Models\Team;
use App\Models\Animation;

$data = [
    ["venue_name" => "CHEZ JEAN", "zone" => "THIAROYE", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.7517342, "longitude" => -17.381228],
    ["venue_name" => "BAR BONGRE", "zone" => "TIVAOUNE PEUL", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.7880784, "longitude" => -17.2884962],
    ["venue_name" => "BAR CHEZ HENRI", "zone" => "SEBIKOTANE", "date" => "12-27-25", "time" => "15 H", "match_name" => "SENEGAL VS RDC", "latitude" => 14.75083, "longitude" => -17.4558011],
    ["venue_name" => "BAR CHEZ PREIRA", "zone" => "KEUR MBAYE FALL", "date" => "12-28-25", "time" => "20 H", "match_name" => "COTE D'IVOIRE VS CAMEROUN", "latitude" => 14.7498845, "longitude" => -17.3440214],
    ["venue_name" => "BAR KAMIEUM", "zone" => "THAIROYE", "date" => "12-30-25", "time" => "19 H", "match_name" => "SENEGAL VS BENIN", "latitude" => 14.7642474, "longitude" => -17.3732367],
    ["venue_name" => "BAR ALLIANCE", "zone" => "KEUR MBAYE FALL", "date" => "01-03-26", "time" => "16 H", "match_name" => "HUITIEME DE FINALE", "latitude" => 14.7407892, "longitude" => -17.3234235],
    ["venue_name" => "BAR CHEZ TANTI", "zone" => "THAIROYE", "date" => "01-09-26", "time" => "16 H", "match_name" => "QUART DE FINALE", "latitude" => 14.7669105, "longitude" => -17.3801388],
    ["venue_name" => "BAR BLEUKEUSSS", "zone" => "DIAMEGEUNE", "date" => "01-14-26", "time" => "16 H", "match_name" => "DEMI FINALE", "latitude" => 14.7652458, "longitude" => -17.4457674],
    ["venue_name" => "CHEZ JEAN", "zone" => "THIAROYE", "date" => "01-17-26", "time" => "16 H", "match_name" => "TROISIEME PLACE", "latitude" => 14.7517342, "longitude" => -17.381228],
    ["venue_name" => "BAR CHEZ PREIRA", "zone" => "KEUR MBAYE FALL", "date" => "01-18-26", "time" => "16 H", "match_name" => "FINALE", "latitude" => 14.7498845, "longitude" => -17.3440214],
    ["venue_name" => "BAR FOUGON 2", "zone" => "MALIKA", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.7922816, "longitude" => -17.3289989],
    ["venue_name" => "BAR JOE BASS", "zone" => "KEUR MASSAR", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.7778322, "longitude" => -17.33062],
    ["venue_name" => "BAR CHEZ MILI", "zone" => "MALIKA", "date" => "12-27-25", "time" => "15 H", "match_name" => "SENEGAL VS RDC", "latitude" => 14.7508245, "longitude" => -17.4557677],
    ["venue_name" => "BAR TERANGA", "zone" => "KEUR MASSAR", "date" => "12-28-25", "time" => "20 H", "match_name" => "COTE D'IVOIRE VS CAMEROUN", "latitude" => 14.7508636, "longitude" => -17.3102724],
    ["venue_name" => "BAR BAKASAO", "zone" => "MALIKA", "date" => "12-30-25", "time" => "19 H", "match_name" => "SENEGAL VS BENIN", "latitude" => 14.7508286, "longitude" => -17.4557744],
    ["venue_name" => "BAR KAWARAFAN", "zone" => "KEUR MASSAR", "date" => "01-03-26", "time" => "16 H", "match_name" => "HUITIEME DE FINALE", "latitude" => 14.7644153, "longitude" => -17.3052866],
    ["venue_name" => "BAR CHEZ ALICE", "zone" => "KEUR MASSAR", "date" => "01-09-26", "time" => "16 H", "match_name" => "QUART DE FINALE", "latitude" => 14.7612882, "longitude" => -17.2841361],
    ["venue_name" => "BAR TITANIUM", "zone" => "KOUNOUNE", "date" => "01-14-26", "time" => "16 H", "match_name" => "DEMI FINALE", "latitude" => 14.7562447, "longitude" => -17.2612446],
    ["venue_name" => "BAR CONCENSUS", "zone" => "KEUR MASSAR", "date" => "01-17-26", "time" => "16 H", "match_name" => "TROISIEME PLACE", "latitude" => 14.7738608, "longitude" => -17.3208291],
    ["venue_name" => "BAR POPEGUINE", "zone" => "KEUR MASSAR", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.7722491, "longitude" => -17.3154377],
    ["venue_name" => "BAR YAKAR", "zone" => "KEURMASSAR", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.771011, "longitude" => -17.3150093],
    ["venue_name" => "BAR BAZILE", "zone" => "GUEDIAWAYE", "date" => "12-27-25", "time" => "15 H", "match_name" => "SENEGAL VS RDC", "latitude" => 14.7813471, "longitude" => -17.3755211],
    ["venue_name" => "BAR CHEZ PASCAL", "zone" => "GUEDIAWAYE", "date" => "12-30-25", "time" => "19 H", "match_name" => "SENEGAL VS BENIN", "latitude" => 14.785374, "longitude" => -17.378309],
    ["venue_name" => "BAR KAPOL", "zone" => "GUEDIAWAYE", "date" => "01-03-26", "time" => "16 H", "match_name" => "HUITIEME DE FINALE", "latitude" => 14.776948, "longitude" => -17.377118],
    ["venue_name" => "CHEZ MARCEL", "zone" => "GUEDIAWAYE", "date" => "01-09-26", "time" => "16 H", "match_name" => "QUART DE FINALE", "latitude" => 14.76825, "longitude" => -17.3895],
    ["venue_name" => "BAR ELTON", "zone" => "GUEDIAWAYE", "date" => "01-14-26", "time" => "16 H", "match_name" => "DEMI FINALE", "latitude" => 14.785426, "longitude" => -17.3783207],
    ["venue_name" => "BAR BOUELO", "zone" => "GUEDIAWAYE", "date" => "01-17-26", "time" => "16 H", "match_name" => "TROISIEME PLACE", "latitude" => 14.6761585, "longitude" => -17.4477634],
    ["venue_name" => "BAR OUTHEKOR", "zone" => "GRAND-YOFF", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.736913, "longitude" => -17.4467729],
    ["venue_name" => "CHEZ HENRIETTE", "zone" => "GRAND-YOFF", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.7382659, "longitude" => -17.4518328],
    ["venue_name" => "CASA BAR", "zone" => "GRAND-YOFF", "date" => "12-27-25", "time" => "15 H", "match_name" => "SENEGAL VS RDC", "latitude" => 14.7375747, "longitude" => -17.444779],
    ["venue_name" => "BAR KAMEME", "zone" => "GRAND-YOFF", "date" => "12-28-25", "time" => "20 H", "match_name" => "COTE D'IVOIRE VS CAMEROUN", "latitude" => 14.7343559, "longitude" => -17.4462383],
    ["venue_name" => "CHEZ MANOU", "zone" => "GRAND-YOFF", "date" => "12-30-25", "time" => "19 H", "match_name" => "SENEGAL VS BENIN", "latitude" => 14.7344494, "longitude" => -17.4539584],
    ["venue_name" => "BAR EDIOUNGOU", "zone" => "GRAND-YOFF", "date" => "01-09-26", "time" => "16 H", "match_name" => "QUART DE FINALE", "latitude" => 14.7375483, "longitude" => -17.4481482],
    ["venue_name" => "BAR AWARA", "zone" => "GRAND-YOFF", "date" => "01-14-26", "time" => "16 H", "match_name" => "DEMI FINALE", "latitude" => 14.7416678, "longitude" => -17.4444997],
    ["venue_name" => "BAR ROYAUME DU PORC", "zone" => "GRAND-YOFF", "date" => "01-17-26", "time" => "16 H", "match_name" => "TROISIEME PLACE", "latitude" => 14.7378194, "longitude" => -17.4435484],
    ["venue_name" => "BAR SANTHIABA", "zone" => "GRAND-YOFF", "date" => "01-18-26", "time" => "16 H", "match_name" => "FINALE", "latitude" => 14.7372804, "longitude" => -17.4447347],
    ["venue_name" => "BAR ETALON", "zone" => "GRAND-DAKAR", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.6917911, "longitude" => -17.4337784],
    ["venue_name" => "BAR CHEZ JEAN", "zone" => "GRAND-DAKAR", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.7382449, "longitude" => -17.4518402],
    ["venue_name" => "BAR BANDIAL", "zone" => "REUBEUSS", "date" => "12-27-25", "time" => "15 H", "match_name" => "SENEGAL VS RDC", "latitude" => 14.6704586, "longitude" => -17.4414847],
    ["venue_name" => "BAR BISTRO", "zone" => "SICAP LIBERTE 5", "date" => "12-28-25", "time" => "20 H", "match_name" => "COTE D'IVOIRE VS CAMEROUN", "latitude" => 14.7090235, "longitude" => -17.4582593],
    ["venue_name" => "BAR CHEZ CATHO", "zone" => "LIBERTE 5", "date" => "12-30-25", "time" => "19 H", "match_name" => "SENEGAL VS BENIN", "latitude" => 14.7215509, "longitude" => -17.4628454],
    ["venue_name" => "BAR CHEZ GUILLAINE", "zone" => "HLM", "date" => "01-03-26", "time" => "16 H", "match_name" => "HUITIEME DE FINALE", "latitude" => 14.70865, "longitude" => -17.446952],
    ["venue_name" => "BAR SAMARITIN", "zone" => "LIBERT 3", "date" => "01-14-26", "time" => "16 H", "match_name" => "DEMI FINALE", "latitude" => 14.80691, "longitude" => -17.33091],
    ["venue_name" => "BAR UMIRAN", "zone" => "PARCELLES ASSAINIES U 17", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.7567058, "longitude" => -17.4406723],
    ["venue_name" => "BAR LA GOREENNE", "zone" => "PATTE D'OIE", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.7476696, "longitude" => -17.4432123],
    ["venue_name" => "BAR DAKHARGUI", "zone" => "PARCELLES ASSAINIES U 17", "date" => "12-27-25", "time" => "15 H", "match_name" => "SENEGAL VS RDC", "latitude" => 14.757696, "longitude" => -17.439845],
    ["venue_name" => "BAR ETHIOUNG", "zone" => "PARCELLES ASSAINIES U 7", "date" => "12-28-25", "time" => "20 H", "match_name" => "COTE D'IVOIRE VS CAMEROUN", "latitude" => 14.72545, "longitude" => -17.442953],
    ["venue_name" => "BAR MONTAGNE", "zone" => "PARCELLES ASSAINIES U 26", "date" => "12-30-25", "time" => "19 H", "match_name" => "SENEGAL VS BENIN", "latitude" => 14.756638, "longitude" => -17.441177],
    ["venue_name" => "BAR KANDJIDIASSA", "zone" => "PARCELLES ASSAINIES U 19", "date" => "01-03-26", "time" => "16 H", "match_name" => "HUITIEME DE FINALE", "latitude" => 14.75513, "longitude" => -17.451919],
    ["venue_name" => "BAR KADETH", "zone" => "PARCELLES ASSAINIES U 12", "date" => "01-14-26", "time" => "16 H", "match_name" => "DEMI FINALE", "latitude" => 14.7573599, "longitude" => -17.4417203],
    ["venue_name" => "BAR CHEZ VINCENT", "zone" => "PARCELLES ASSAINIES U 24", "date" => "01-17-26", "time" => "16 H", "match_name" => "TROISIEME PLACE", "latitude" => 14.7536497, "longitude" => -17.4467705],
    ["venue_name" => "BAR SET SET", "zone" => "PARCELLES ASSAINIES U 21", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.7557735, "longitude" => -17.4448494],
    ["venue_name" => "BAR CASA ESTANCIA", "zone" => "PARCELLES ASSAINIES U 10", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.76136, "longitude" => -17.4337118],
    ["venue_name" => "BAR CHEZ FRANCOIS", "zone" => "CITE FADIA", "date" => "01-09-26", "time" => "15 H", "match_name" => "SENEGAL VS RDC", "latitude" => 14.7095886, "longitude" => -17.4523725],
    ["venue_name" => "BAR CHEZ VALERIE", "zone" => "ROND POINT CASE", "date" => "01-03-26", "time" => "16 H", "match_name" => "HUITIEME DE FINALE", "latitude" => 14.7577258, "longitude" => -17.4285123],
    ["venue_name" => "BAR MAISON BLANCHE", "zone" => "PARCELLES U 10", "date" => "01-14-26", "time" => "16 H", "match_name" => "DEMI FINALE", "latitude" => 14.7617111, "longitude" => -17.4365972],
    ["venue_name" => "BAR JOYCE", "zone" => "OUAKAM", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.6928039, "longitude" => -17.4603993],
    ["venue_name" => "BAR JEROME", "zone" => "OUAKAM", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.7269138, "longitude" => -17.4828138],
    ["venue_name" => "BAR LE BOURBEOIS", "zone" => "OUAKAM", "date" => "12-28-25", "time" => "20 H", "match_name" => "COTE D'IVOIRE VS CAMEROUN", "latitude" => 14.7274408, "longitude" => -17.4838794],
];

echo "Début de l'importation des animations...\n\n";

$matchesCache = [];
$barsCache = [];
$createdAnimations = 0;
$errors = [];

foreach ($data as $index => $item) {
    try {
        echo "Traitement " . ($index + 1) . "/" . count($data) . ": {$item['venue_name']} - {$item['match_name']}\n";

        // Parse date (MM-DD-YY format)
        [$month, $day, $year] = explode('-', $item['date']);
        $fullYear = '20' . $year;
        $dateStr = "{$fullYear}-{$month}-{$day}";

        // Parse time
        $time = str_replace(' H', ':00', $item['time']);
        $matchDate = $dateStr . ' ' . $time;

        // Find or create bar
        $barKey = strtoupper($item['venue_name']) . '_' . strtoupper($item['zone']);
        if (!isset($barsCache[$barKey])) {
            $bar = Bar::firstOrCreate(
                ['name' => $item['venue_name']],
                [
                    'zone' => $item['zone'],
                    'address' => $item['zone'],
                    'latitude' => $item['latitude'],
                    'longitude' => $item['longitude'],
                    'is_active' => true,
                ]
            );
            $barsCache[$barKey] = $bar;
            echo "  ✓ Bar créé/trouvé: {$bar->name}\n";
        } else {
            $bar = $barsCache[$barKey];
        }

        // Determine match type and phase
        $matchName = strtoupper($item['match_name']);
        $isKnockout = in_array($matchName, ['HUITIEME DE FINALE', 'QUART DE FINALE', 'DEMI FINALE', 'TROISIEME PLACE', 'FINALE']);

        if ($isKnockout) {
            // Knockout match
            $phaseMap = [
                'HUITIEME DE FINALE' => 'round_of_16',
                'QUART DE FINALE' => 'quarter_final',
                'DEMI FINALE' => 'semi_final',
                'TROISIEME PLACE' => 'third_place',
                'FINALE' => 'final',
            ];
            $phase = $phaseMap[$matchName];

            // Use match date + phase as cache key
            $matchKey = $dateStr . '_' . $phase;

            if (!isset($matchesCache[$matchKey])) {
                // Check database first to avoid duplicates
                $match = MatchGame::where('phase', $phase)
                    ->whereDate('match_date', $dateStr)
                    ->first();

                if (!$match) {
                    // Create knockout match with "À déterminer" teams
                    $match = MatchGame::create([
                        'team_a' => 'À déterminer',
                        'team_b' => 'À déterminer',
                        'match_date' => $matchDate,
                        'status' => 'scheduled',
                        'phase' => $phase,
                        'group_name' => null,
                    ]);
                    echo "  ✓ Match knockout créé: {$matchName}\n";
                } else {
                    echo "  ⚡ Match knockout existant trouvé: {$matchName}\n";
                }
                $matchesCache[$matchKey] = $match;
            } else {
                $match = $matchesCache[$matchKey];
            }
        } else {
            // Group stage match with actual teams
            $teams = explode(' VS ', $matchName);
            if (count($teams) !== 2) {
                throw new \Exception("Format de match invalide: {$matchName}");
            }

            $teamA = trim($teams[0]);
            $teamB = trim($teams[1]);

            // Use teams + date as cache key for group matches
            $matchKey = $teamA . '_' . $teamB . '_' . $dateStr;

            if (!isset($matchesCache[$matchKey])) {
                // Check database first to avoid duplicates
                $match = MatchGame::where('team_a', $teamA)
                    ->where('team_b', $teamB)
                    ->whereDate('match_date', $dateStr)
                    ->first();

                if (!$match) {
                    // Find teams in database
                    $homeTeam = Team::where('name', 'LIKE', '%' . $teamA . '%')->first();
                    $awayTeam = Team::where('name', 'LIKE', '%' . $teamB . '%')->first();

                    $match = MatchGame::create([
                        'team_a' => $teamA,
                        'team_b' => $teamB,
                        'home_team_id' => $homeTeam ? $homeTeam->id : null,
                        'away_team_id' => $awayTeam ? $awayTeam->id : null,
                        'match_date' => $matchDate,
                        'status' => 'scheduled',
                        'phase' => 'group_stage',
                        'group_name' => null, // Will be set later if needed
                    ]);
                    echo "  ✓ Match de groupe créé: {$teamA} vs {$teamB}\n";
                } else {
                    echo "  ⚡ Match de groupe existant trouvé: {$teamA} vs {$teamB}\n";
                }
                $matchesCache[$matchKey] = $match;
            } else {
                $match = $matchesCache[$matchKey];
            }
        }

        // Create animation (venue-match link)
        $animation = Animation::create([
            'bar_id' => $bar->id,
            'match_id' => $match->id,
            'animation_date' => $dateStr,
            'animation_time' => $time,
            'is_active' => true,
        ]);

        $createdAnimations++;
        echo "  ✓ Animation créée\n\n";

    } catch (\Exception $e) {
        $errors[] = "Ligne " . ($index + 1) . ": " . $e->getMessage();
        echo "  ✗ ERREUR: " . $e->getMessage() . "\n\n";
    }
}

echo "\n========================================\n";
echo "RÉSUMÉ DE L'IMPORTATION\n";
echo "========================================\n";
echo "Total d'animations créées: {$createdAnimations}/" . count($data) . "\n";
echo "Bars uniques: " . count($barsCache) . "\n";
echo "Matchs uniques: " . count($matchesCache) . "\n";

if (count($errors) > 0) {
    echo "\nERREURS:\n";
    foreach ($errors as $error) {
        echo "  - {$error}\n";
    }
}

echo "\n✅ Importation terminée!\n";
