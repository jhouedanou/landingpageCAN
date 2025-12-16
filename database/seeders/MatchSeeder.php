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
        $matches = [
            ['home' => 'Maroc', 'away' => 'Comores', 'date' => '2025-12-21 20:00:00', 'grp' => 'A', 'stadium' => 'Stade Mohammed V, Casablanca'],
            ['home' => 'Mali', 'away' => 'Zambie', 'date' => '2025-12-22 15:00:00', 'grp' => 'A', 'stadium' => 'Stade de Marrakech'],
            ['home' => 'Afrique du Sud', 'away' => 'Angola', 'date' => '2025-12-22 18:00:00', 'grp' => 'B', 'stadium' => 'Stade de Tanger'],
            ['home' => 'Sénégal', 'away' => 'Botswana', 'date' => '2025-12-23 16:00:00', 'grp' => 'D', 'stadium' => 'Stade de Rabat'],
            ['home' => 'Nigeria', 'away' => 'Tanzanie', 'date' => '2025-12-23 18:30:00', 'grp' => 'C', 'stadium' => 'Stade de Fès'],
            ['home' => 'Côte d\'Ivoire', 'away' => 'Mozambique', 'date' => '2025-12-24 18:30:00', 'grp' => 'F', 'stadium' => 'Stade Mohammed V, Casablanca'],
            ['home' => 'Cameroun', 'away' => 'Gabon', 'date' => '2025-12-24 21:00:00', 'grp' => 'F', 'stadium' => 'Stade de Marrakech'],
            ['home' => 'Maroc', 'away' => 'Mali', 'date' => '2025-12-26 21:00:00', 'grp' => 'A', 'stadium' => 'Stade Mohammed V, Casablanca'],
            ['home' => 'Nigeria', 'away' => 'Tunisie', 'date' => '2025-12-27 21:00:00', 'grp' => 'C', 'stadium' => 'Stade de Tanger'],
            ['home' => 'Cameroun', 'away' => 'Côte d\'Ivoire', 'date' => '2025-12-28 21:00:00', 'grp' => 'F', 'stadium' => 'Stade Mohammed V, Casablanca'],
            ['home' => 'Maroc', 'away' => 'Zambie', 'date' => '2025-12-29 20:00:00', 'grp' => 'A', 'stadium' => 'Stade de Rabat'],
            ['home' => 'Sénégal', 'away' => 'Bénin', 'date' => '2025-12-30 20:00:00', 'grp' => 'D', 'stadium' => 'Stade de Fès'],
            ['home' => 'Côte d\'Ivoire', 'away' => 'Gabon', 'date' => '2025-12-31 20:00:00', 'grp' => 'F', 'stadium' => 'Stade de Marrakech'],
        ];

        foreach ($matches as $matchData) {
            $homeTeam = Team::where('name', $matchData['home'])->first();
            $awayTeam = Team::where('name', $matchData['away'])->first();

            if (!$homeTeam || !$awayTeam) {
                $this->command->warn("⚠️ Team not found: {$matchData['home']} vs {$matchData['away']}");
                continue;
            }

            MatchGame::updateOrCreate(
                [
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                    'match_date' => Carbon::parse($matchData['date']),
                ],
                [
                    'team_a' => $homeTeam->name,
                    'team_b' => $awayTeam->name,
                    'group_name' => $matchData['grp'],
                    'stadium' => $matchData['stadium'],
                    'status' => 'scheduled',
                    'score_a' => null,
                    'score_b' => null,
                ]
            );
        }

        $this->command->info('✅ 13 Grande Fête du Foot Africain group stage matches seeded successfully!');
    }
}
