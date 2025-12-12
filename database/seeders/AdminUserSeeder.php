<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminPhone = config('auth_phones.admin_phone', '+2250748348221');

        // Vérifier si l'utilisateur admin existe déjà
        $admin = User::where('phone', $adminPhone)->first();

        if ($admin) {
            // Mettre à jour le rôle si nécessaire
            if ($admin->role !== 'admin') {
                $admin->update(['role' => 'admin']);
                $this->command->info("✅ Rôle admin mis à jour pour le numéro: {$adminPhone}");
            } else {
                $this->command->info("✅ L'utilisateur admin existe déjà: {$adminPhone}");
            }
        } else {
            // Créer l'utilisateur administrateur
            User::create([
                'name' => 'Administrateur',
                'phone' => $adminPhone,
                'password' => Hash::make(Str::random(32)),
                'role' => 'admin',
                'points_total' => 0,
            ]);

            $this->command->info("✅ Utilisateur admin créé avec succès: {$adminPhone}");
        }

        // Vérifier et mettre à jour les numéros de test CI si nécessaire
        $testPhonesCI = config('auth_phones.test_phones_ci', []);
        
        foreach ($testPhonesCI as $testPhone) {
            $testUser = User::where('phone', $testPhone)->first();
            
            if ($testUser) {
                $this->command->info("✅ Utilisateur test CI trouvé: {$testPhone}");
            } else {
                $this->command->info("⚠️  Utilisateur test CI non trouvé: {$testPhone} (sera créé lors de la première connexion)");
            }
        }
    }
}
