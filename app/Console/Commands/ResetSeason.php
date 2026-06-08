<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetSeason extends Command
{
    /**
     * Remise à zéro pour repartir d'une nouvelle compétition (Coupe du Monde 2026).
     *
     * - Supprime les résultats précédents (CAN) : pronostics, points, classements.
     * - Supprime les comptes joueurs (les utilisateurs recréent leur compte).
     * - Conserve les comptes admin et les PDV (réseau SOBOA).
     * - Vide puis reseed équipes / stades / matchs WC + match test.
     *
     * Usage Forge (une seule fois) :
     *   php artisan season:reset --force
     */
    protected $signature = 'season:reset {--force : ne pas demander de confirmation}';

    protected $description = 'Reset complet pour une nouvelle compétition : efface résultats + comptes joueurs, conserve admin + PDV, reseed matchs WC';

    public function handle(): int
    {
        $players = User::where('is_admin', false)
            ->where(function ($q) {
                $q->whereNull('role')->orWhere('role', '!=', 'admin');
            })
            ->count();
        $admins = User::count() - $players;

        $this->warn('⚠️  RESET COMPLET — irréversible.');
        $this->line("   Comptes joueurs supprimés : {$players}");
        $this->line("   Comptes admin conservés   : {$admins}");
        $this->line('   Effacés : pronostics, points, logs, classements (résultats CAN)');
        $this->line('   Conservés : PDV (bars)');
        $this->line('   Reseed : équipes, stades, matchs WC 2026 + match test');
        $this->newLine();

        if (!$this->option('force') && !$this->confirm('Confirmer le reset ?', false)) {
            $this->warn('❌ Annulé.');
            return self::SUCCESS;
        }

        DB::transaction(function () {
            Schema::disableForeignKeyConstraints();

            // Détacher les références équipe dans les réglages avant de vider teams
            if (Schema::hasTable('site_settings')) {
                DB::table('site_settings')->update([
                    'favorite_team_id'          => null,
                    'tournament_winner_team_id' => null,
                    'tournament_ended'          => false,
                ]);
            }

            // 1) Résultats & historique (CAN)
            foreach ([
                'prediction_likes',
                'prediction_comments',
                'predictions',
                'point_logs',
                'weekly_rankings',
                'match_notifications',
                'animations',
            ] as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->delete();
                }
            }

            // 2) Comptes joueurs (garder admin)
            User::where('is_admin', false)
                ->where(function ($q) {
                    $q->whereNull('role')->orWhere('role', '!=', 'admin');
                })
                ->delete();

            // Remettre les points des admin restants à zéro
            User::query()->update(['points_total' => 0]);

            // 3) Données sportives (garder PDV / bars)
            foreach (['matches', 'teams', 'stadiums'] as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->delete();
                }
            }

            Schema::enableForeignKeyConstraints();
        });

        // 4) Reseed compétition WC (hors transaction : les seeders gèrent leurs propres écritures)
        $this->info('🌱 Reseed des données Coupe du Monde 2026...');
        foreach ([
            \Database\Seeders\TeamSeeder::class,
            \Database\Seeders\StadiumSeeder::class,
            \Database\Seeders\MatchSeeder::class,
            \Database\Seeders\TestMatchSeeder::class,
        ] as $seeder) {
            Artisan::call('db:seed', ['--class' => $seeder, '--force' => true]);
            $this->line('   ✓ ' . class_basename($seeder));
        }

        $this->newLine();
        $this->info('✅ Reset terminé. Compétition repart à zéro (WC 2026).');
        $this->line('   Vide les caches si besoin : php artisan optimize:clear');

        return self::SUCCESS;
    }
}
