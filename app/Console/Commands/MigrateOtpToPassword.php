<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MigrateOtpToPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:migrate-otp-to-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrer les codes OTP existants vers le champ password pour les anciens utilisateurs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Début de la migration des codes OTP vers passwords...');

        // Récupérer tous les utilisateurs ayant un otp_password mais pas de password (ou password générique)
        $users = User::whereNotNull('otp_password')
            ->where(function ($query) {
                $query->whereNull('password')
                      ->orWhere('password', ''); // Certains peuvent avoir une string vide
            })
            ->get();

        if ($users->isEmpty()) {
            $this->info('Aucun utilisateur à migrer.');
            return 0;
        }

        $this->info("Nombre d'utilisateurs à migrer: {$users->count()}");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $migrated = 0;

        foreach ($users as $user) {
            // Copier le otp_password vers password
            // Le hash est déjà fait, on le copie tel quel
            $user->password = $user->otp_password;
            $user->save();

            $migrated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Migration terminée avec succès !");
        $this->info("📊 {$migrated} utilisateur(s) migré(s)");
        $this->newLine();
        $this->comment("Les anciens utilisateurs peuvent maintenant se connecter avec leur code OTP à 6 chiffres comme mot de passe.");

        return 0;
    }
}
