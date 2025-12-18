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
        // Supprimer tous les stades existants
        Stadium::truncate();

        $stadiums = [
            [
                'name' => 'Complexe Sportif Moulay Abdellah',
                'city' => 'Rabat',
                'capacity' => 53000,
                'latitude' => '33.959957',
                'longitude' => '-6.886073',
                'is_active' => true,
            ],
            [
                'name' => 'Grand Stade de Tanger (Ibn Batouta)',
                'city' => 'Tanger',
                'capacity' => 65000,
                'latitude' => '35.741477',
                'longitude' => '-5.856974',
                'is_active' => true,
            ],
            [
                'name' => 'Stade Mohammed V',
                'city' => 'Casablanca',
                'capacity' => 45000,
                'latitude' => '33.582869',
                'longitude' => '-7.646877',
                'is_active' => true,
            ],
            [
                'name' => 'Grand Stade de Marrakech',
                'city' => 'Marrakech',
                'capacity' => 45240,
                'latitude' => '31.706240',
                'longitude' => '-7.980321',
                'is_active' => true,
            ],
            [
                'name' => 'Grand Stade d\'Agadir (Adrar)',
                'city' => 'Agadir',
                'capacity' => 45480,
                'latitude' => '30.435832',
                'longitude' => '-9.544778',
                'is_active' => true,
            ],
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

        $this->command->info('✅ 6 stades créés avec succès!');
    }
}
