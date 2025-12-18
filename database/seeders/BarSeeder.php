<?php

namespace Database\Seeders;

use App\Models\Bar;
use Illuminate\Database\Seeder;

class BarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Supprimer tous les points de vente existants
        Bar::truncate();

        $venues = [
            // Dakar - Plateau
            [
                'name' => 'Bar Le Central - Plateau',
                'address' => 'Avenue Georges Pompidou, Plateau, Dakar',
                'latitude' => 14.6937,
                'longitude' => -17.4441,
                'is_active' => true,
            ],
            [
                'name' => 'Brasserie du Port',
                'address' => 'Près du Port de Dakar, Plateau',
                'latitude' => 14.6928,
                'longitude' => -17.4467,
                'is_active' => true,
            ],

            // Dakar - Medina
            [
                'name' => 'Maquis Teranga - Medina',
                'address' => 'Medina, Dakar',
                'latitude' => 14.6812,
                'longitude' => -17.4534,
                'is_active' => true,
            ],

            // Dakar - HLM
            [
                'name' => 'Espace Foot HLM',
                'address' => 'HLM Grand Yoff, Dakar',
                'latitude' => 14.7456,
                'longitude' => -17.4534,
                'is_active' => true,
            ],
            [
                'name' => 'Le Stade - HLM',
                'address' => 'HLM 5, Dakar',
                'latitude' => 14.7234,
                'longitude' => -17.4623,
                'is_active' => true,
            ],

            // Dakar - Parcelles Assainies
            [
                'name' => 'Bar Le Lion - Parcelles',
                'address' => 'Parcelles Assainies Unité 15, Dakar',
                'latitude' => 14.7789,
                'longitude' => -17.4323,
                'is_active' => true,
            ],
            [
                'name' => 'Maquis CAN 2025',
                'address' => 'Parcelles Assainies Unité 25, Dakar',
                'latitude' => 14.7834,
                'longitude' => -17.4289,
                'is_active' => true,
            ],

            // Dakar - Ouakam
            [
                'name' => 'Beach Bar Ouakam',
                'address' => 'Plage de Ouakam, Dakar',
                'latitude' => 14.7234,
                'longitude' => -17.4912,
                'is_active' => true,
            ],

            // Dakar - Almadies
            [
                'name' => 'Le Phare des Mamelles',
                'address' => 'Les Almadies, Dakar',
                'latitude' => 14.7423,
                'longitude' => -17.5134,
                'is_active' => true,
            ],

            // Dakar - Point E
            [
                'name' => 'Brasserie Point E',
                'address' => 'Point E, Dakar',
                'latitude' => 14.7112,
                'longitude' => -17.4534,
                'is_active' => true,
            ],

            // Dakar - Fann
            [
                'name' => 'Maquis Fann Résidence',
                'address' => 'Fann Résidence, Dakar',
                'latitude' => 14.7045,
                'longitude' => -17.4634,
                'is_active' => true,
            ],

            // Pikine
            [
                'name' => 'Le Populaire - Pikine',
                'address' => 'Pikine Guédiawaye, Dakar',
                'latitude' => 14.7589,
                'longitude' => -17.3923,
                'is_active' => true,
            ],
            [
                'name' => 'Espace Teranga Pikine',
                'address' => 'Pikine Tally Bou Bess',
                'latitude' => 14.7512,
                'longitude' => -17.4012,
                'is_active' => true,
            ],

            // Rufisque
            [
                'name' => 'Bar du Marché - Rufisque',
                'address' => 'Centre-ville, Rufisque',
                'latitude' => 14.7134,
                'longitude' => -17.2712,
                'is_active' => true,
            ],

            // Thiaroye
            [
                'name' => 'Maquis Thiaroye',
                'address' => 'Thiaroye sur Mer, Dakar',
                'latitude' => 14.7623,
                'longitude' => -17.3234,
                'is_active' => true,
            ],

            // Mbour
            [
                'name' => 'Beach Bar Saly',
                'address' => 'Saly, Mbour',
                'latitude' => 14.4534,
                'longitude' => -16.9923,
                'is_active' => true,
            ],

            // Thiès
            [
                'name' => 'Maquis Escale - Thiès',
                'address' => 'Centre-ville, Thiès',
                'latitude' => 14.7889,
                'longitude' => -16.9262,
                'is_active' => true,
            ],

            // Saint-Louis
            [
                'name' => 'Bar de l\'Île - Saint-Louis',
                'address' => 'Île de Saint-Louis',
                'latitude' => 16.0178,
                'longitude' => -16.5089,
                'is_active' => true,
            ],

            // Ziguinchor
            [
                'name' => 'Maquis Casamance',
                'address' => 'Centre-ville, Ziguinchor',
                'latitude' => 12.5833,
                'longitude' => -16.2667,
                'is_active' => true,
            ],

            // Kaolack
            [
                'name' => 'Espace CAN Kaolack',
                'address' => 'Kaolack Centre',
                'latitude' => 14.1333,
                'longitude' => -16.0667,
                'is_active' => true,
            ],
        ];

        foreach ($venues as $venue) {
            Bar::create($venue);
        }

        $this->command->info('✅ ' . count($venues) . ' points de vente créés/mis à jour avec succès!');
        $this->command->info('📍 Zones couvertes: Dakar (Plateau, Medina, HLM, Parcelles, Ouakam, Almadies, Point E, Fann), Pikine, Rufisque, Thiaroye, Mbour, Thiès, Saint-Louis, Ziguinchor, Kaolack');
        $this->command->info('🇸🇳 Tous les points de vente sont au Sénégal');
        $this->command->info('📏 Rayon de geofencing: 200 mètres');
    }
}
