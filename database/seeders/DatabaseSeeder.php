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
        $this->call([
            TeamSeeder::class,
            StadiumSeeder::class,
            MatchSeeder::class,
            UserSeeder::class,
            BarSeeder::class,
            PredictionSeeder::class,
            AdminUserSeeder::class, // Assure que l'admin existe
        ]);
    }
}
