<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Équipes de la Coupe du Monde 2026 (Football Fest 2026).
     * 48 équipes réparties en 12 groupes (A à L).
     * Idempotent : updateOrCreate par nom.
     */
    public function run(): void
    {
        $teams = [
            // Group A
            ['name' => 'Mexico', 'iso_code' => 'mx', 'group' => 'A'],
            ['name' => 'South Africa', 'iso_code' => 'za', 'group' => 'A'],
            ['name' => 'South Korea', 'iso_code' => 'kr', 'group' => 'A'],
            ['name' => 'Czech Republic', 'iso_code' => 'cz', 'group' => 'A'],
            // Group B
            ['name' => 'Canada', 'iso_code' => 'ca', 'group' => 'B'],
            ['name' => 'Bosnia & Herzegovina', 'iso_code' => 'ba', 'group' => 'B'],
            ['name' => 'Qatar', 'iso_code' => 'qa', 'group' => 'B'],
            ['name' => 'Switzerland', 'iso_code' => 'ch', 'group' => 'B'],
            // Group C
            ['name' => 'Brazil', 'iso_code' => 'br', 'group' => 'C'],
            ['name' => 'Morocco', 'iso_code' => 'ma', 'group' => 'C'],
            ['name' => 'Haiti', 'iso_code' => 'ht', 'group' => 'C'],
            ['name' => 'Scotland', 'iso_code' => 'gb-sct', 'group' => 'C'],
            // Group D
            ['name' => 'USA', 'iso_code' => 'us', 'group' => 'D'],
            ['name' => 'Paraguay', 'iso_code' => 'py', 'group' => 'D'],
            ['name' => 'Australia', 'iso_code' => 'au', 'group' => 'D'],
            ['name' => 'Turkey', 'iso_code' => 'tr', 'group' => 'D'],
            // Group E
            ['name' => 'Germany', 'iso_code' => 'de', 'group' => 'E'],
            ['name' => 'Curaçao', 'iso_code' => 'cw', 'group' => 'E'],
            ['name' => 'Ivory Coast', 'iso_code' => 'ci', 'group' => 'E'],
            ['name' => 'Ecuador', 'iso_code' => 'ec', 'group' => 'E'],
            // Group F
            ['name' => 'Netherlands', 'iso_code' => 'nl', 'group' => 'F'],
            ['name' => 'Japan', 'iso_code' => 'jp', 'group' => 'F'],
            ['name' => 'Sweden', 'iso_code' => 'se', 'group' => 'F'],
            ['name' => 'Tunisia', 'iso_code' => 'tn', 'group' => 'F'],
            // Group G
            ['name' => 'Belgium', 'iso_code' => 'be', 'group' => 'G'],
            ['name' => 'Egypt', 'iso_code' => 'eg', 'group' => 'G'],
            ['name' => 'Iran', 'iso_code' => 'ir', 'group' => 'G'],
            ['name' => 'New Zealand', 'iso_code' => 'nz', 'group' => 'G'],
            // Group H
            ['name' => 'Spain', 'iso_code' => 'es', 'group' => 'H'],
            ['name' => 'Cape Verde', 'iso_code' => 'cv', 'group' => 'H'],
            ['name' => 'Saudi Arabia', 'iso_code' => 'sa', 'group' => 'H'],
            ['name' => 'Uruguay', 'iso_code' => 'uy', 'group' => 'H'],
            // Group I
            ['name' => 'France', 'iso_code' => 'fr', 'group' => 'I'],
            ['name' => 'Senegal', 'iso_code' => 'sn', 'group' => 'I'],
            ['name' => 'Iraq', 'iso_code' => 'iq', 'group' => 'I'],
            ['name' => 'Norway', 'iso_code' => 'no', 'group' => 'I'],
            // Group J
            ['name' => 'Argentina', 'iso_code' => 'ar', 'group' => 'J'],
            ['name' => 'Algeria', 'iso_code' => 'dz', 'group' => 'J'],
            ['name' => 'Austria', 'iso_code' => 'at', 'group' => 'J'],
            ['name' => 'Jordan', 'iso_code' => 'jo', 'group' => 'J'],
            // Group K
            ['name' => 'Portugal', 'iso_code' => 'pt', 'group' => 'K'],
            ['name' => 'DR Congo', 'iso_code' => 'cd', 'group' => 'K'],
            ['name' => 'Uzbekistan', 'iso_code' => 'uz', 'group' => 'K'],
            ['name' => 'Colombia', 'iso_code' => 'co', 'group' => 'K'],
            // Group L
            ['name' => 'England', 'iso_code' => 'gb-eng', 'group' => 'L'],
            ['name' => 'Croatia', 'iso_code' => 'hr', 'group' => 'L'],
            ['name' => 'Ghana', 'iso_code' => 'gh', 'group' => 'L'],
            ['name' => 'Panama', 'iso_code' => 'pa', 'group' => 'L'],
        ];

        $created = 0;
        $updated = 0;

        foreach ($teams as $team) {
            $teamModel = Team::updateOrCreate(
                ['name' => $team['name']], // Unique key
                $team // All data to update/create
            );

            if ($teamModel->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("✅ Teams: {$created} created, {$updated} updated (Total: " . count($teams) . ")");
    }
}
