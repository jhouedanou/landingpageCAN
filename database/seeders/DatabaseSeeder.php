<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeders de données essentielles (toujours exécutés)
        $this->call([
            TeamSeeder::class,           // ✅ Équipes CAN
            StadiumSeeder::class,        // ✅ Stades
            MatchSeeder::class,          // ✅ Matchs
            BarSeeder::class,            // ✅ Points de vente
            AdminUserSeeder::class,      // ✅ Admin
        ]);

        // Seeders de test (uniquement en développement)
        if (app()->environment('local', 'development')) {
            $this->call([
                UserSeeder::class,       // 🧪 Utilisateurs de test
                PredictionSeeder::class, // 🧪 Prédictions de test
            ]);
            $this->command->info('🧪 Données de test ajoutées (environnement local)');
        }
    }
}
