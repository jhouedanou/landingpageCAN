<?php

namespace Database\Seeders;

use App\Models\MatchGame;
use App\Models\Bar;
use App\Models\Prediction;
use Illuminate\Database\Seeder;

/**
 * ProductionDataSeeder
 *
 * Seeder pour initialiser les données de production :
 * - Matches de la Grande Fête du Foot Africain
 * - Points de vente partenaires
 * - Pronostics (optionnel)
 *
 * Utilisation : php artisan db:seed --class=ProductionDataSeeder
 */
class ProductionDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Les données sont seedées par les seeders individuels appelés par DatabaseSeeder
        // Ce seeder sert de conteneur pour les seeders de production

        $this->command->info('🎮 Initialisation des données de production...');

        // Appeler les seeders de données
        $this->call([
            TeamSeeder::class,
            StadiumSeeder::class,
            MatchSeeder::class,
            BarSeeder::class,
        ]);

        $this->command->info('✅ Données de production initialisées avec succès!');
        $this->command->line('');
        $this->command->info('📊 Résumé:');
        $this->command->line('  • Équipes: ' . \App\Models\Team::count());
        $this->command->line('  • Stades: ' . \App\Models\Stadium::count());
        $this->command->line('  • Matches: ' . MatchGame::count());
        $this->command->line('  • Points de vente: ' . Bar::count());
        $this->command->line('');
    }
}
