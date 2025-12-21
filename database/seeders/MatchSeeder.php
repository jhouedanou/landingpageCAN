<?php

namespace Database\Seeders;

use App\Models\MatchGame;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Production-safe: updateOrCreate instead of truncate
        // CRITICAL: Preserves matches and their predictions (cascade delete!)
        // Uses composite key: home_team_id + away_team_id + match_date for uniqueness

        $matches = [
            // Matchs de phase de poules (selon CSV animations avec stades)
            ['home' => 'Sénégal', 'away' => 'Botswana', 'date' => '2025-12-23 15:00:00', 'phase' => 'group_stage', 'grp' => 'D', 'stadium' => 'Grand Stade de Tanger'],
            ['home' => 'Afrique du Sud', 'away' => 'Égypte', 'date' => '2025-12-26 15:00:00', 'phase' => 'group_stage', 'grp' => 'B', 'stadium' => 'Grand Stade d\'Agadir'],
            ['home' => 'Sénégal', 'away' => 'RD Congo', 'date' => '2025-12-27 15:00:00', 'phase' => 'group_stage', 'grp' => 'D', 'stadium' => 'Grand Stade de Tanger'],
            ['home' => 'Côte d\'Ivoire', 'away' => 'Cameroun', 'date' => '2025-12-28 20:00:00', 'phase' => 'group_stage', 'grp' => 'F', 'stadium' => 'Grand Stade de Marrakech'],
            ['home' => 'Sénégal', 'away' => 'Bénin', 'date' => '2025-12-30 19:00:00', 'phase' => 'group_stage', 'grp' => 'D', 'stadium' => 'Grand Stade de Tanger'],

            // Matches de phase finale (équipes à déterminer avec stades)
            ['home' => null, 'away' => null, 'date' => '2026-01-03 16:00:00', 'phase' => 'round_of_16', 'grp' => null, 'stadium' => 'Grand Stade de Tanger'],
            ['home' => null, 'away' => null, 'date' => '2026-01-09 16:00:00', 'phase' => 'quarter_final', 'grp' => null, 'stadium' => 'Grand Stade de Tanger'],
            ['home' => null, 'away' => null, 'date' => '2026-01-14 16:00:00', 'phase' => 'semi_final', 'grp' => null, 'stadium' => 'Complexe Sportif Prince Moulay Abdellah'],
            ['home' => null, 'away' => null, 'date' => '2026-01-17 16:00:00', 'phase' => 'third_place', 'grp' => null, 'stadium' => 'Complexe Sportif Mohammed-V'],
            ['home' => null, 'away' => null, 'date' => '2026-01-18 16:00:00', 'phase' => 'final', 'grp' => null, 'stadium' => 'Complexe Sportif Prince Moulay Abdellah'],
        ];

        $created = 0;
        $updated = 0;

        foreach ($matches as $matchData) {
            $homeTeamId = null;
            $awayTeamId = null;
            $teamA = 'À déterminer';
            $teamB = 'À déterminer';

            // Trouver les équipes si spécifiées (null pour les matchs de phase finale à déterminer)
            if ($matchData['home']) {
                $homeTeam = Team::where('name', $matchData['home'])->first();
                if ($homeTeam) {
                    $homeTeamId = $homeTeam->id;
                    $teamA = $homeTeam->name;
                } else {
                    $this->command->warn("⚠️ Équipe domicile non trouvée: {$matchData['home']}");
                    continue;
                }
            }

            if ($matchData['away']) {
                $awayTeam = Team::where('name', $matchData['away'])->first();
                if ($awayTeam) {
                    $awayTeamId = $awayTeam->id;
                    $teamB = $awayTeam->name;
                } else {
                    $this->command->warn("⚠️ Équipe extérieur non trouvée: {$matchData['away']}");
                    continue;
                }
            }

            $matchDate = Carbon::parse($matchData['date']);

            // Composite unique key to prevent duplicates
            // Pour les matchs à déterminer (équipes null): utiliser date + phase
            // For known matches: use home_team_id + away_team_id + match_date
            $uniqueKey = [];
            if ($homeTeamId && $awayTeamId) {
                $uniqueKey = [
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'match_date' => $matchDate,
                ];
            } else {
                // Matchs de phase finale à déterminer: uniques par date + phase
                $uniqueKey = [
                    'match_date' => $matchDate,
                    'phase' => $matchData['phase'] ?? 'group_stage',
                ];
            }

            $matchModel = MatchGame::updateOrCreate(
                $uniqueKey, // Composite unique key
                [
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'team_a' => $teamA,
                    'team_b' => $teamB,
                    'match_date' => $matchDate,
                    'phase' => $matchData['phase'] ?? 'group_stage',
                    'group_name' => $matchData['grp'],
                    'stadium' => $matchData['stadium'],
                    'status' => 'scheduled',
                    // Preserve existing scores if match already exists
                ]
            );

            if ($matchModel->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("✅ Matches: {$created} created, {$updated} updated (Total: " . ($created + $updated) . ")");
    }
}
