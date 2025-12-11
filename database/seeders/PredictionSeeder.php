<?php

namespace Database\Seeders;

use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\Database\Seeder;

class PredictionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('is_admin', false)->get();
        $matches = MatchGame::all();

        if ($users->isEmpty() || $matches->isEmpty()) {
            $this->command->warn('⚠️ Please run UserSeeder and MatchSeeder first');
            return;
        }

        $predictionsCreated = 0;

        foreach ($users as $user) {
            // Each user makes predictions for 3-5 random matches
            $userMatches = $matches->random(min(rand(3, 5), $matches->count()));

            foreach ($userMatches as $match) {
                // Generate random but realistic scores
                $scoreA = rand(0, 4);
                $scoreB = rand(0, 4);

                Prediction::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'match_id' => $match->id,
                    ],
                    [
                        'score_a' => $scoreA,
                        'score_b' => $scoreB,
                    ]
                );

                $predictionsCreated++;
            }
        }

        $this->command->info("✅ {$predictionsCreated} test predictions created");
    }
}
