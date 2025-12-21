<?php

namespace Database\Seeders;

use App\Models\Stadium;
use Illuminate\Database\Seeder;

class StadiumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver les contraintes de clés étrangères
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Supprimer tous les stades existants
        Stadium::truncate();

        // Réactiver les contraintes de clés étrangères
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $stadiums = [
            // Rabat - 4 stades
            [
                'name' => 'Complexe Sportif Prince Moulay Abdellah',
                'city' => 'Rabat',
                'capacity' => 53000,
                'latitude' => '33.959957',
                'longitude' => '-6.886073',
                'is_active' => true,
            ],
            [
                'name' => 'Stade Annexe Olympique',
                'city' => 'Rabat',
                'capacity' => 5000,
                'latitude' => '33.961000',
                'longitude' => '-6.885000',
                'is_active' => true,
            ],
            [
                'name' => 'Complexe Sportif Prince Héritier Moulay El Hassan',
                'city' => 'Rabat',
                'capacity' => 5000,
                'latitude' => '33.958000',
                'longitude' => '-6.887000',
                'is_active' => true,
            ],
            [
                'name' => 'Stade Al Barid',
                'city' => 'Rabat',
                'capacity' => 3000,
                'latitude' => '33.960000',
                'longitude' => '-6.884000',
                'is_active' => true,
            ],

            // Casablanca
            [
                'name' => 'Complexe Sportif Mohammed-V',
                'city' => 'Casablanca',
                'capacity' => 45000,
                'latitude' => '33.582869',
                'longitude' => '-7.646877',
                'is_active' => true,
            ],

            // Tanger
            [
                'name' => 'Grand Stade de Tanger - Ibn Batouta',
                'city' => 'Tanger',
                'capacity' => 65000,
                'latitude' => '35.741477',
                'longitude' => '-5.856974',
                'is_active' => true,
            ],

            // Marrakech
            [
                'name' => 'Grand Stade de Marrakech',
                'city' => 'Marrakech',
                'capacity' => 45240,
                'latitude' => '31.706240',
                'longitude' => '-7.980321',
                'is_active' => true,
            ],

            // Agadir
            [
                'name' => 'Grand Stade d\'Agadir - Stade Adrar',
                'city' => 'Agadir',
                'capacity' => 45480,
                'latitude' => '30.435832',
                'longitude' => '-9.544778',
                'is_active' => true,
            ],

            // Fès
            [
                'name' => 'Complexe Sportif de Fès',
                'city' => 'Fès',
                'capacity' => 45000,
                'latitude' => '34.004419',
                'longitude' => '-4.957500',
                'is_active' => true,
            ],
        ];

        foreach ($stadiums as $stadium) {
            Stadium::create($stadium);
        }

        $this->command->info('✅ 9 stades créés avec succès!');
    }
}
