<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamIsoCodesSeeder extends Seeder
{
    /**
     * Ajoute les codes ISO aux équipes existantes
     */
    public function run(): void
    {
        $teams = [
            'SENEGAL' => 'sn',
            'BOTSWANA' => 'bw',
            'AFRIQUE DU SUD' => 'za',
            'EGYPTE' => 'eg',
            'RD CONGO' => 'cd',
            'COTE D\'IVOIRE' => 'ci',
            'CAMEROUN' => 'cm',
            'BENIN' => 'bj',
        ];

        $updated = 0;
        $notFound = [];

        foreach ($teams as $name => $isoCode) {
            $team = Team::where('name', 'LIKE', "%{$name}%")->first();

            if ($team) {
                $team->update(['iso_code' => $isoCode]);
                $updated++;
                $this->command->info("✓ {$name} → {$isoCode}");
            } else {
                $notFound[] = $name;
                $this->command->warn("⚠ {$name} non trouvée");
            }
        }

        $this->command->info('');
        $this->command->info("✅ {$updated} équipes mises à jour");

        if (!empty($notFound)) {
            $this->command->warn("⚠️  " . count($notFound) . " équipes non trouvées:");
            foreach ($notFound as $name) {
                $this->command->warn("   - {$name}");
            }
        }

        // Afficher toutes les équipes
        $this->command->info('');
        $this->command->info('📋 Liste des équipes:');
        $allTeams = Team::orderBy('name')->get();
        foreach ($allTeams as $team) {
            $iso = $team->iso_code ?: 'NO ISO';
            $this->command->info("   - {$team->name} ({$iso})");
        }
    }
}
