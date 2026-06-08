<?php

namespace Database\Seeders;

use App\Models\Stadium;
use Illuminate\Database\Seeder;

class StadiumSeeder extends Seeder
{
    /**
     * Stades de la Coupe du Monde 2026 (Football Fest 2026).
     * 16 stades répartis entre les États-Unis, le Canada et le Mexique.
     * Idempotent : updateOrCreate par nom.
     */
    public function run(): void
    {
        $stadiums = [
            ['name' => 'BC Place', 'city' => 'Vancouver', 'capacity' => 54000, 'latitude' => '49.276667', 'longitude' => '-123.111944', 'is_active' => true],
            ['name' => 'Lumen Field', 'city' => 'Seattle', 'capacity' => 69000, 'latitude' => '47.595278', 'longitude' => '-122.331667', 'is_active' => true],
            ['name' => 'Levi\'s Stadium', 'city' => 'San Francisco Bay Area (Santa Clara)', 'capacity' => 71000, 'latitude' => '37.403000', 'longitude' => '-121.970000', 'is_active' => true],
            ['name' => 'SoFi Stadium', 'city' => 'Los Angeles (Inglewood)', 'capacity' => 70000, 'latitude' => '33.953000', 'longitude' => '-118.339000', 'is_active' => true],
            ['name' => 'Estadio Akron', 'city' => 'Guadalajara (Zapopan)', 'capacity' => 48000, 'latitude' => '20.681667', 'longitude' => '-103.462778', 'is_active' => true],
            ['name' => 'Estadio Azteca', 'city' => 'Mexico City', 'capacity' => 83000, 'latitude' => '19.303056', 'longitude' => '-99.150556', 'is_active' => true],
            ['name' => 'Estadio BBVA', 'city' => 'Monterrey (Guadalupe)', 'capacity' => 53500, 'latitude' => '25.669167', 'longitude' => '-100.244444', 'is_active' => true],
            ['name' => 'NRG Stadium', 'city' => 'Houston', 'capacity' => 72000, 'latitude' => '29.684722', 'longitude' => '-95.410833', 'is_active' => true],
            ['name' => 'AT&T Stadium', 'city' => 'Dallas (Arlington)', 'capacity' => 94000, 'latitude' => '32.747778', 'longitude' => '-97.092778', 'is_active' => true],
            ['name' => 'Arrowhead Stadium', 'city' => 'Kansas City', 'capacity' => 73000, 'latitude' => '39.048889', 'longitude' => '-94.483889', 'is_active' => true],
            ['name' => 'Mercedes-Benz Stadium', 'city' => 'Atlanta', 'capacity' => 75000, 'latitude' => '33.755556', 'longitude' => '-84.400000', 'is_active' => true],
            ['name' => 'Hard Rock Stadium', 'city' => 'Miami (Miami Gardens)', 'capacity' => 65000, 'latitude' => '25.958056', 'longitude' => '-80.238889', 'is_active' => true],
            ['name' => 'BMO Field', 'city' => 'Toronto', 'capacity' => 45000, 'latitude' => '43.633333', 'longitude' => '-79.418611', 'is_active' => true],
            ['name' => 'Gillette Stadium', 'city' => 'Boston (Foxborough)', 'capacity' => 65000, 'latitude' => '42.091000', 'longitude' => '-71.264000', 'is_active' => true],
            ['name' => 'Lincoln Financial Field', 'city' => 'Philadelphia', 'capacity' => 69000, 'latitude' => '39.900833', 'longitude' => '-75.167500', 'is_active' => true],
            ['name' => 'MetLife Stadium', 'city' => 'New York/New Jersey (East Rutherford)', 'capacity' => 82500, 'latitude' => '40.813528', 'longitude' => '-74.074361', 'is_active' => true],
        ];

        $created = 0;
        $updated = 0;

        foreach ($stadiums as $stadium) {
            $stadiumModel = Stadium::updateOrCreate(
                ['name' => $stadium['name']], // Unique key
                $stadium // All data to update/create
            );

            if ($stadiumModel->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("✅ Stadiums: {$created} created, {$updated} updated (Total: " . count($stadiums) . ")");
    }
}
