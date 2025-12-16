<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\MatchGame;
use App\Models\Bar;
use App\Models\Prediction;
use App\Models\PointLog;
use Illuminate\Console\Command;

class CleanDatabase extends Command
{
    protected $signature = 'db:clean {--force : Skip confirmation} {--skip-backup : Skip backup creation}';
    protected $description = 'Clean database: remove users (except admin), matches, PDV, and related data. Keep teams.';

    public function handle()
    {
        // Check if --force flag is provided
        if (!$this->option('force')) {
            $this->warn('⚠️  ATTENTION: Cette opération va supprimer:');
            $this->warn('  - Tous les utilisateurs SAUF l\'admin');
            $this->warn('  - Tous les matchs');
            $this->warn('  - Tous les points de vente (PDV)');
            $this->warn('  - Tous les pronostics');
            $this->warn('  - Tous les logs de points');
            $this->info('');
            $this->info('✅ Sera CONSERVÉ:');
            $this->info('  - Les équipes (Teams)');
            $this->info('  - L\'utilisateur admin');
            $this->info('');

            if (!$this->confirm('Êtes-vous sûr de vouloir continuer?')) {
                $this->info('Opération annulée.');
                return 0;
            }
        }

        // Create backup before cleaning (unless --skip-backup is used)
        if (!$this->option('skip-backup')) {
            $this->info('');
            $this->info('📦 Création d\'un backup de sécurité avant nettoyage...');
            $backupResult = $this->call('db:backup');

            if ($backupResult !== 0) {
                $this->error('❌ Impossible de créer le backup. Nettoyage annulé.');
                return 1;
            }
        } else {
            $this->warn('⚠️  Backup ignoré (--skip-backup utilisé)');
        }

        $this->info('');

        try {
            // Disable foreign key constraints for cleanup
            \DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // 1. Delete all predictions (they reference matches and users)
            $predictionCount = Prediction::count();
            Prediction::query()->delete();
            $this->info("✅ Suppression de $predictionCount pronostics");

            // 2. Delete all point logs (they reference users and matches)
            $pointLogCount = PointLog::count();
            PointLog::query()->delete();
            $this->info("✅ Suppression de $pointLogCount logs de points");

            // 3. Delete all matches
            $matchCount = MatchGame::count();
            MatchGame::query()->delete();
            $this->info("✅ Suppression de $matchCount matchs");

            // 4. Delete all bars/PDV
            $barCount = Bar::count();
            Bar::query()->delete();
            $this->info("✅ Suppression de $barCount points de vente (PDV)");

            // 5. Delete all users except admin
            $adminUser = User::where('is_admin', true)->first();
            $adminId = $adminUser?->id;

            if ($adminId) {
                $usersDeleted = User::where('id', '!=', $adminId)->delete();
                $this->info("✅ Suppression de $usersDeleted utilisateurs (admin conservé: {$adminUser->name})");
            } else {
                $usersDeleted = User::query()->delete();
                $this->warn("⚠️  Aucun admin trouvé. Tous les utilisateurs ont été supprimés.");
            }

            // Re-enable foreign key constraints
            \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        } catch (\Exception $e) {
            // Re-enable foreign key constraints even on error
            try {
                \DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $ignored) {
                // Ignore if this fails
            }

            $this->error('❌ Erreur lors du nettoyage: ' . $e->getMessage());
            return 1;
        }

        // Reset auto-increment for better organization (after transaction completely done)
        try {
            $this->resetAutoIncrement();
        } catch (\Exception $e) {
            $this->warn('⚠️  Erreur lors du reset des auto-increment: ' . $e->getMessage());
        }

        $this->info('');
        $this->info('✅ Nettoyage de la base de données terminé avec succès!');
        $this->info('');

        $this->info('État final:');
        $this->info('  - Équipes: ' . \App\Models\Team::count() . ' équipes');
        $this->info('  - Utilisateurs: ' . User::count() . ' utilisateur(s)');
        $this->info('  - Matchs: ' . MatchGame::count() . ' match(s)');
        $this->info('  - PDV: ' . Bar::count() . ' point(s) de vente');
        $this->info('  - Pronostics: ' . Prediction::count() . ' pronostic(s)');
        $this->info('  - Logs de points: ' . PointLog::count() . ' log(s)');

        return 0;
    }

    private function resetAutoIncrement()
    {
        try {
            // Reset auto-increment for tables
            \DB::statement('ALTER TABLE predictions AUTO_INCREMENT = 1');
            \DB::statement('ALTER TABLE point_logs AUTO_INCREMENT = 1');
            \DB::statement('ALTER TABLE matches AUTO_INCREMENT = 1');
            \DB::statement('ALTER TABLE bars AUTO_INCREMENT = 1');
            
            // Reset users auto-increment but keep admin's ID
            $adminId = User::where('is_admin', true)->value('id') ?? 1;
            $nextId = $adminId + 1;
            \DB::statement("ALTER TABLE users AUTO_INCREMENT = $nextId");
        } catch (\Exception $e) {
            // Silently fail if statement doesn't work (different DB engines)
        }
    }
}
