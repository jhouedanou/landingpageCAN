<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bar;
use App\Models\MatchGame;
use App\Models\Animation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FixAnimationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder fixes venue coordinates/zones and creates proper Animation records
     * linking venues to matches based on the cleaned JSON data.
     */
    public function run()
    {
        $this->command->info('🚀 Starting FixAnimationsSeeder...');

        // Cleaned JSON data with valid OSM coordinates
        $data = [
            ["venue_name" => "CHEZ JEAN", "zone" => "THIAROYE", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.751734, "longitude" => -17.381228],
            ["venue_name" => "BAR BONGRE", "zone" => "TIVAOUNE PEUL", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.788078, "longitude" => -17.288496],
            ["venue_name" => "BAR CHEZ HENRI", "zone" => "SEBIKOTANE", "date" => "12-27-25", "time" => "15 H", "match_name" => "SENEGAL VS RDC", "latitude" => 14.740830, "longitude" => -17.155801],
            ["venue_name" => "BAR CHEZ PREIRA", "zone" => "KEUR MBAYE FALL", "date" => "12-28-25", "time" => "20 H", "match_name" => "COTE D'IVOIRE VS CAMEROUN", "latitude" => 14.749884, "longitude" => -17.344021],
            ["venue_name" => "BAR KAMIEUM", "zone" => "THAIROYE", "date" => "12-30-25", "time" => "19 H", "match_name" => "SENEGAL VS BENIN", "latitude" => 14.764247, "longitude" => -17.373236],
            ["venue_name" => "BAR ALLIANCE", "zone" => "KEUR MBAYE FALL", "date" => "01-03-26", "time" => "16 H", "match_name" => "HUITIEME DE FINALE", "latitude" => 14.740789, "longitude" => -17.323423],
            ["venue_name" => "BAR CHEZ TANTI", "zone" => "THAIROYE", "date" => "01-09-26", "time" => "16 H", "match_name" => "QUART DE FINALE", "latitude" => 14.766910, "longitude" => -17.380138],
            ["venue_name" => "BAR BLEUKEUSSS", "zone" => "DIAMEGEUNE", "date" => "01-14-26", "time" => "16 H", "match_name" => "DEMI FINALE", "latitude" => 14.765245, "longitude" => -17.405767],
            ["venue_name" => "CHEZ JEAN", "zone" => "THIAROYE", "date" => "01-17-26", "time" => "16 H", "match_name" => "TROISIEME PLACE", "latitude" => 14.751734, "longitude" => -17.381228],
            ["venue_name" => "BAR CHEZ PREIRA", "zone" => "KEUR MBAYE FALL", "date" => "01-18-26", "time" => "16 H", "match_name" => "FINALE", "latitude" => 14.749884, "longitude" => -17.344021],
            ["venue_name" => "BAR FOUGON 2", "zone" => "MALIKA", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.792281, "longitude" => -17.328998],
            ["venue_name" => "BAR JOE BASS", "zone" => "KEUR MASSAR", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.777832, "longitude" => -17.330620],
            ["venue_name" => "BAR CHEZ MILI", "zone" => "MALIKA", "date" => "12-27-25", "time" => "15 H", "match_name" => "SENEGAL VS RDC", "latitude" => 14.790824, "longitude" => -17.335767],
            ["venue_name" => "BAR TERANGA", "zone" => "KEUR MASSAR", "date" => "12-28-25", "time" => "20 H", "match_name" => "COTE D'IVOIRE VS CAMEROUN", "latitude" => 14.750863, "longitude" => -17.310272],
            ["venue_name" => "BAR BAKASAO", "zone" => "MALIKA", "date" => "12-30-25", "time" => "19 H", "match_name" => "SENEGAL VS BENIN", "latitude" => 14.790828, "longitude" => -17.335774],
            ["venue_name" => "BAR KAWARAFAN", "zone" => "KEUR MASSAR", "date" => "01-03-26", "time" => "16 H", "match_name" => "HUITIEME DE FINALE", "latitude" => 14.764415, "longitude" => -17.305286],
            ["venue_name" => "BAR CHEZ ALICE", "zone" => "KEUR MASSAR", "date" => "01-09-26", "time" => "16 H", "match_name" => "QUART DE FINALE", "latitude" => 14.761288, "longitude" => -17.284136],
            ["venue_name" => "BAR TITANIUM", "zone" => "KOUNOUNE", "date" => "01-14-26", "time" => "16 H", "match_name" => "DEMI FINALE", "latitude" => 14.756244, "longitude" => -17.261244],
            ["venue_name" => "BAR CONCENSUS", "zone" => "KEUR MASSAR", "date" => "01-17-26", "time" => "16 H", "match_name" => "TROISIEME PLACE", "latitude" => 14.773860, "longitude" => -17.320829],
            ["venue_name" => "BAR POPEGUINE", "zone" => "KEUR MASSAR", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.772249, "longitude" => -17.315437],
            ["venue_name" => "BAR YAKAR", "zone" => "KEURMASSAR", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.771011, "longitude" => -17.315009],
            ["venue_name" => "BAR BAZILE", "zone" => "GUEDIAWAYE", "date" => "12-27-25", "time" => "15 H", "match_name" => "SENEGAL VS RDC", "latitude" => 14.781347, "longitude" => -17.375521],
            ["venue_name" => "BAR CHEZ PASCAL", "zone" => "GUEDIAWAYE", "date" => "12-30-25", "time" => "19 H", "match_name" => "SENEGAL VS BENIN", "latitude" => 14.785374, "longitude" => -17.378309],
            ["venue_name" => "BAR KAPOL", "zone" => "GUEDIAWAYE", "date" => "01-03-26", "time" => "16 H", "match_name" => "HUITIEME DE FINALE", "latitude" => 14.776948, "longitude" => -17.377118],
            ["venue_name" => "CHEZ MARCEL", "zone" => "GUEDIAWAYE", "date" => "01-09-26", "time" => "16 H", "match_name" => "QUART DE FINALE", "latitude" => 14.768250, "longitude" => -17.389500],
            ["venue_name" => "BAR ELTON", "zone" => "GUEDIAWAYE", "date" => "01-14-26", "time" => "16 H", "match_name" => "DEMI FINALE", "latitude" => 14.785426, "longitude" => -17.378320],
            ["venue_name" => "BAR BOUELO", "zone" => "GUEDIAWAYE", "date" => "01-17-26", "time" => "16 H", "match_name" => "TROISIEME PLACE", "latitude" => 14.776158, "longitude" => -17.387763],
            ["venue_name" => "BAR OUTHEKOR", "zone" => "GRAND-YOFF", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.736913, "longitude" => -17.446772],
            ["venue_name" => "CHEZ HENRIETTE", "zone" => "GRAND-YOFF", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.738265, "longitude" => -17.451832],
            ["venue_name" => "CASA BAR", "zone" => "GRAND-YOFF", "date" => "12-27-25", "time" => "15 H", "match_name" => "SENEGAL VS RDC", "latitude" => 14.737574, "longitude" => -17.444779],
            ["venue_name" => "BAR KAMEME", "zone" => "GRAND-YOFF", "date" => "12-28-25", "time" => "20 H", "match_name" => "COTE D'IVOIRE VS CAMEROUN", "latitude" => 14.734355, "longitude" => -17.446238],
            ["venue_name" => "CHEZ MANOU", "zone" => "GRAND-YOFF", "date" => "12-30-25", "time" => "19 H", "match_name" => "SENEGAL VS BENIN", "latitude" => 14.734449, "longitude" => -17.453958],
            ["venue_name" => "BAR EDIOUNGOU", "zone" => "GRAND-YOFF", "date" => "01-09-26", "time" => "16 H", "match_name" => "QUART DE FINALE", "latitude" => 14.737548, "longitude" => -17.448148],
            ["venue_name" => "BAR AWARA", "zone" => "GRAND-YOFF", "date" => "01-14-26", "time" => "16 H", "match_name" => "DEMI FINALE", "latitude" => 14.741667, "longitude" => -17.444499],
            ["venue_name" => "BAR ROYAUME DU PORC", "zone" => "GRAND-YOFF", "date" => "01-17-26", "time" => "16 H", "match_name" => "TROISIEME PLACE", "latitude" => 14.737819, "longitude" => -17.443548],
            ["venue_name" => "BAR SANTHIABA", "zone" => "GRAND-YOFF", "date" => "01-18-26", "time" => "16 H", "match_name" => "FINALE", "latitude" => 14.737280, "longitude" => -17.444734],
            ["venue_name" => "BAR ETALON", "zone" => "GRAND-DAKAR", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.691791, "longitude" => -17.433778],
            ["venue_name" => "BAR CHEZ JEAN", "zone" => "GRAND-DAKAR", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.738244, "longitude" => -17.451840],
            ["venue_name" => "BAR BANDIAL", "zone" => "REUBEUSS", "date" => "12-27-25", "time" => "15 H", "match_name" => "SENEGAL VS RDC", "latitude" => 14.670458, "longitude" => -17.441484],
            ["venue_name" => "BAR BISTRO", "zone" => "SICAP LIBERTE 5", "date" => "12-28-25", "time" => "20 H", "match_name" => "COTE D'IVOIRE VS CAMEROUN", "latitude" => 14.709023, "longitude" => -17.458259],
            ["venue_name" => "BAR CHEZ CATHO", "zone" => "LIBERTE 5", "date" => "12-30-25", "time" => "19 H", "match_name" => "SENEGAL VS BENIN", "latitude" => 14.721550, "longitude" => -17.462845],
            ["venue_name" => "BAR CHEZ GUILLAINE", "zone" => "HLM", "date" => "01-03-26", "time" => "16 H", "match_name" => "HUITIEME DE FINALE", "latitude" => 14.708650, "longitude" => -17.446952],
            ["venue_name" => "BAR SAMARITIN", "zone" => "LIBERT 3", "date" => "01-14-26", "time" => "16 H", "match_name" => "DEMI FINALE", "latitude" => 14.725000, "longitude" => -17.455000],
            ["venue_name" => "BAR UMIRAN", "zone" => "PARCELLES ASSAINIES U 17", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.756705, "longitude" => -17.440672],
            ["venue_name" => "BAR LA GOREENNE", "zone" => "PATTE D'OIE", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.747669, "longitude" => -17.443212],
            ["venue_name" => "BAR DAKHARGUI", "zone" => "PARCELLES ASSAINIES U 17", "date" => "12-27-25", "time" => "15 H", "match_name" => "SENEGAL VS RDC", "latitude" => 14.757696, "longitude" => -17.439845],
            ["venue_name" => "BAR ETHIOUNG", "zone" => "PARCELLES ASSAINIES U 7", "date" => "12-28-25", "time" => "20 H", "match_name" => "COTE D'IVOIRE VS CAMEROUN", "latitude" => 14.755450, "longitude" => -17.442953],
            ["venue_name" => "BAR MONTAGNE", "zone" => "PARCELLES ASSAINIES U 26", "date" => "12-30-25", "time" => "19 H", "match_name" => "SENEGAL VS BENIN", "latitude" => 14.756638, "longitude" => -17.441177],
            ["venue_name" => "BAR KANDJIDIASSA", "zone" => "PARCELLES ASSAINIES U 19", "date" => "01-03-26", "time" => "16 H", "match_name" => "HUITIEME DE FINALE", "latitude" => 14.755130, "longitude" => -17.451919],
            ["venue_name" => "BAR KADETH", "zone" => "PARCELLES ASSAINIES U 12", "date" => "01-14-26", "time" => "16 H", "match_name" => "DEMI FINALE", "latitude" => 14.757359, "longitude" => -17.441720],
            ["venue_name" => "BAR CHEZ VINCENT", "zone" => "PARCELLES ASSAINIES U 24", "date" => "01-17-26", "time" => "16 H", "match_name" => "TROISIEME PLACE", "latitude" => 14.753649, "longitude" => -17.446770],
            ["venue_name" => "BAR SET SET", "zone" => "PARCELLES ASSAINIES U 21", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.755773, "longitude" => -17.444849],
            ["venue_name" => "BAR CASA ESTANCIA", "zone" => "PARCELLES ASSAINIES U 10", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.761360, "longitude" => -17.433711],
            ["venue_name" => "BAR CHEZ FRANCOIS", "zone" => "CITE FADIA", "date" => "01-09-26", "time" => "15 H", "match_name" => "SENEGAL VS RDC", "latitude" => 14.760588, "longitude" => -17.432372],
            ["venue_name" => "BAR CHEZ VALERIE", "zone" => "ROND POINT CASE", "date" => "01-03-26", "time" => "16 H", "match_name" => "HUITIEME DE FINALE", "latitude" => 14.757725, "longitude" => -17.428512],
            ["venue_name" => "BAR MAISON BLANCHE", "zone" => "PARCELLES U 10", "date" => "01-14-26", "time" => "16 H", "match_name" => "DEMI FINALE", "latitude" => 14.761711, "longitude" => -17.436597],
            ["venue_name" => "BAR JOYCE", "zone" => "OUAKAM", "date" => "12-23-25", "time" => "15 H", "match_name" => "SENEGAL VS BOTSWANA", "latitude" => 14.712803, "longitude" => -17.480399],
            ["venue_name" => "BAR JEROME", "zone" => "OUAKAM", "date" => "12-26-25", "time" => "15 H", "match_name" => "AFRIQUE DU SUD VS EGYPTE", "latitude" => 14.726913, "longitude" => -17.482813],
            ["venue_name" => "BAR LE BOURBEOIS", "zone" => "OUAKAM", "date" => "12-28-25", "time" => "20 H", "match_name" => "COTE D'IVOIRE VS CAMEROUN", "latitude" => 14.727440, "longitude" => -17.483879],
            ["venue_name" => "COUCOU LE JOIE", "zone" => "GRAND-YOFF", "date" => "01-03-26", "time" => "16 H", "match_name" => "HUITIEME DE FINALE", "latitude" => 14.737000, "longitude" => -17.447000],
            ["venue_name" => "BAR CHEZ LOPY", "zone" => "OUAKAM", "date" => "01-09-26", "time" => "16 H", "match_name" => "QUART DE FINALE", "latitude" => 14.720000, "longitude" => -17.480000],
            ["venue_name" => "BAR AWALE", "zone" => "OUAKAM", "date" => "01-17-26", "time" => "16 H", "match_name" => "TROISIEME PLACE", "latitude" => 14.725000, "longitude" => -17.481000],
        ];

        $venuesUpdated = 0;
        $venuesCreated = 0;
        $animationsCreated = 0;
        $matchesNotFound = [];

        DB::beginTransaction();
        try {
            foreach ($data as $index => $item) {
                // 1. CREATE OR UPDATE VENUE - Ensure venue exists with correct data
                $venueName = trim($item['venue_name']);

                // Use updateOrCreate to automatically create missing venues
                $venue = Bar::updateOrCreate(
                    ['name' => $venueName], // Find by name
                    [
                        'address' => $item['zone'], // Use zone as default address for new venues
                        'zone' => $item['zone'],
                        'latitude' => $item['latitude'],
                        'longitude' => $item['longitude'],
                        'is_active' => true,
                    ]
                );

                if ($venue->wasRecentlyCreated) {
                    $venuesCreated++;
                    $this->command->info("✨ Created new venue: {$venueName}");
                } else {
                    $venuesUpdated++;
                }

                // 2. PARSE DATE & TIME
                // Date format: "MM-DD-YY" (12-23-25 = December 23, 2025)
                // Time format: "HH H" (15 H = 15:00:00)
                try {
                    $date = Carbon::createFromFormat('m-d-y', $item['date']);
                    $hour = (int) explode(' ', $item['time'])[0];
                    $datetime = $date->setTime($hour, 0, 0);
                } catch (\Exception $e) {
                    $this->command->error("❌ Date parsing error for item {$index}: {$item['date']} {$item['time']}");
                    continue;
                }

                // 3. FIND MATCH
                $matchName = $item['match_name'];
                $match = null;

                // Check if it's a knockout phase match (generic names)
                $phaseMap = [
                    'HUITIEME DE FINALE' => 'round_of_16',
                    'QUART DE FINALE' => 'quarter_final',
                    'DEMI FINALE' => 'semi_final',
                    'TROISIEME PLACE' => 'third_place',
                    'FINALE' => 'final',
                ];

                if (array_key_exists($matchName, $phaseMap)) {
                    // Find by phase (take the first match of that phase)
                    $phase = $phaseMap[$matchName];
                    $match = MatchGame::where('phase', $phase)->first();

                    if (!$match) {
                        $matchesNotFound[] = $matchName;
                        $this->command->warn("⚠️  Match not found for phase: {$matchName}");
                        continue;
                    }
                } else {
                    // Regular match - parse team names (e.g., "SENEGAL VS BOTSWANA")
                    $teams = explode(' VS ', strtoupper($matchName));

                    if (count($teams) === 2) {
                        $teamA = trim($teams[0]);
                        $teamB = trim($teams[1]);

                        // Normalize team names for better matching
                        $teamANormalized = $this->normalizeTeamName($teamA);
                        $teamBNormalized = $this->normalizeTeamName($teamB);

                        // Find match by team names (case-insensitive with normalization)
                        $match = MatchGame::where(function($query) use ($teamANormalized, $teamBNormalized) {
                            $query->whereRaw('UPPER(REPLACE(REPLACE(TRIM(team_a), "é", "e"), "ô", "o")) LIKE ?', ["%{$teamANormalized}%"])
                                  ->whereRaw('UPPER(REPLACE(REPLACE(TRIM(team_b), "é", "e"), "ô", "o")) LIKE ?', ["%{$teamBNormalized}%"]);
                        })
                        ->orWhere(function($query) use ($teamANormalized, $teamBNormalized) {
                            $query->whereRaw('UPPER(REPLACE(REPLACE(TRIM(team_a), "é", "e"), "ô", "o")) LIKE ?', ["%{$teamBNormalized}%"])
                                  ->whereRaw('UPPER(REPLACE(REPLACE(TRIM(team_b), "é", "e"), "ô", "o")) LIKE ?', ["%{$teamANormalized}%"]);
                        })
                        ->first();
                    }

                    if (!$match) {
                        $matchesNotFound[] = $matchName;
                        $this->command->warn("⚠️  Match not found: {$matchName}");
                        continue;
                    }
                }

                // 4. CREATE OR UPDATE ANIMATION (Pivot Record)
                if ($venue && $match) {
                    Animation::updateOrCreate(
                        [
                            'bar_id' => $venue->id,
                            'match_id' => $match->id,
                        ],
                        [
                            'animation_date' => $datetime->format('Y-m-d'),
                            'animation_time' => $datetime->format('H:i:s'),
                            'is_active' => true,
                        ]
                    );
                    $animationsCreated++;
                }
            }

            DB::commit();

            // Summary
            $this->command->info("\n✅ FixAnimationsSeeder completed successfully!");
            $this->command->info("✨ Venues created: {$venuesCreated}");
            $this->command->info("📍 Venues updated: {$venuesUpdated}");
            $this->command->info("🔗 Animations created/updated: {$animationsCreated}");
            $this->command->info("📊 Total venues processed: " . ($venuesCreated + $venuesUpdated));

            if (count($matchesNotFound) > 0) {
                $this->command->warn("\n⚠️  Matches not found (" . count(array_unique($matchesNotFound)) . "):");
                foreach (array_unique($matchesNotFound) as $match) {
                    $this->command->warn("   - {$match}");
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("\n❌ Seeder failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Normalize team name for better matching
     * Handles common variations like RDC vs RD Congo, removes accents, etc.
     */
    private function normalizeTeamName($name)
    {
        $name = strtoupper(trim($name));

        // Common team name variations
        $variations = [
            'RDC' => 'RD CONGO',
            'REPUBLIQUE DEMOCRATIQUE DU CONGO' => 'RD CONGO',
            'SENEGAL' => 'SENEGAL',
            'AFRIQUE DU SUD' => 'AFRIQUE DU SUD',
            'COTE D\'IVOIRE' => 'COTE',
            "COTE D'IVOIRE" => 'COTE',
            'EGYPTE' => 'EGYPTE',
            'BENIN' => 'BENIN',
        ];

        foreach ($variations as $from => $to) {
            if (str_contains($name, $from)) {
                $name = str_replace($from, $to, $name);
            }
        }

        // Remove accents
        $name = str_replace(['É', 'È', 'Ê', 'Ë'], 'E', $name);
        $name = str_replace(['À', 'Â', 'Ä'], 'A', $name);
        $name = str_replace(['Ô', 'Ö'], 'O', $name);

        return $name;
    }
}
