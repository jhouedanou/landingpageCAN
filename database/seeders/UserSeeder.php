<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['phone' => '+221770000000'],
            [
                'name' => 'Admin SOBOA',
                'phone' => '+221770000000',
                'email' => 'admin@soboa.sn',
                'points_total' => 0,
                'is_admin' => true,
            ]
        );

        // Test users with points for leaderboard
        $testUsers = [
            ['name' => 'Moussa Diop', 'phone' => '+221771234501', 'points' => 147],
            ['name' => 'Fatou Sall', 'phone' => '+221771234502', 'points' => 132],
            ['name' => 'Amadou Ba', 'phone' => '+221771234503', 'points' => 125],
            ['name' => 'Aïssatou Ndiaye', 'phone' => '+221771234504', 'points' => 118],
            ['name' => 'Ibrahima Fall', 'phone' => '+221771234505', 'points' => 112],
            ['name' => 'Mariama Sy', 'phone' => '+221771234506', 'points' => 98],
            ['name' => 'Ousmane Gueye', 'phone' => '+221771234507', 'points' => 89],
            ['name' => 'Khady Mbaye', 'phone' => '+221771234508', 'points' => 76],
            ['name' => 'Cheikh Diallo', 'phone' => '+221771234509', 'points' => 65],
            ['name' => 'Bineta Faye', 'phone' => '+221771234510', 'points' => 54],
            ['name' => 'Pape Sow', 'phone' => '+221771234511', 'points' => 43],
            ['name' => 'Awa Thiam', 'phone' => '+221771234512', 'points' => 32],
        ];

        foreach ($testUsers as $userData) {
            User::updateOrCreate(
                ['phone' => $userData['phone']],
                [
                    'name' => $userData['name'],
                    'points_total' => $userData['points'],
                ]
            );
        }

        $this->command->info('✅ 13 test users created (1 admin + 12 players with rankings)');
    }
}
