<?php

namespace App\Console\Commands;

use App\Models\PointLog;
use App\Models\User;
use App\Services\RankingScenarioService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Applique en base un scénario de classement (A ou B) : retire les points de
 * bonus POS conformément au scénario choisi.
 *  - Scénario A : retire TOUS les bonus POS (venue_visit + bar_visit).
 *  - Scénario B : retire seulement les bonus POS non légitimes (écartés).
 *
 * Sécurité :
 *  - DRY-RUN par défaut ; --apply pour exécuter.
 *  - Confirmation interactive avant écriture.
 *  - Les lignes POS d'origine sont CONSERVÉES (audit) ; le retrait est tracé par
 *    une ligne négative 'adjustment' par utilisateur (points_total reste cohérent
 *    avec la somme des logs).
 *  - À exécuter UNE seule fois (décision finale). Si des ajustements existent
 *    déjà, la commande exige --force pour éviter un double retrait.
 */
class ApplyRankingScenario extends Command
{
    protected $signature = 'ranking:apply-scenario
        {scenario : Scénario à appliquer (a ou b)}
        {--apply : Exécute réellement le retrait (sinon simulation)}
        {--force : Passe outre l\'avertissement si des ajustements existent déjà}';

    protected $description = 'Applique un scénario de classement (A=reset total POS, B=conditionnel) en retirant les bonus POS concernés.';

    public function handle(RankingScenarioService $service): int
    {
        $arg = strtolower((string) $this->argument('scenario'));
        if (!in_array($arg, ['a', 'b'], true)) {
            $this->error('Scénario invalide. Utilise "a" ou "b".');
            return self::INVALID;
        }
        $key = $arg === 'b' ? RankingScenarioService::SCENARIO_B : RankingScenarioService::SCENARIO_A;
        $apply = (bool) $this->option('apply');

        $this->warn($apply
            ? '⚠️  MODE APPLICATION : écriture en base.'
            : 'ℹ️  SIMULATION (dry-run) : aucune écriture en base.');
        $this->info("Scénario {$key} — calcul du plan de retrait des bonus POS...");
        $this->newLine();

        $plan = $service->buildApplyPlan($key);
        $rows = $plan['rows'];

        if (empty($rows)) {
            $this->info('✅ Aucun bonus POS à retirer pour ce scénario. Rien à faire.');
            return self::SUCCESS;
        }

        // Aperçu (30 premiers).
        $preview = array_map(
            fn ($r) => [$r['user_id'], $r['name'], $r['pos_current'], $r['pos_kept'], $r['pos_removed'], $r['total_before'], $r['total_after']],
            array_slice($rows, 0, 30)
        );
        $this->table(
            ['ID', 'Nom', 'POS actuel', 'POS gardé', 'POS retiré', 'Total avant', 'Total après'],
            $preview
        );
        if (count($rows) > 30) {
            $this->line('… ' . (count($rows) - 30) . ' utilisateurs supplémentaires.');
        }

        $t = $plan['totals'];
        $this->newLine();
        $this->warn("👥 Utilisateurs impactés : {$t['users']}");
        $this->warn("➖ Points POS à retirer   : {$t['points_removed']}");
        $this->newLine();

        if (!$apply) {
            $this->info('Pour appliquer réellement : php artisan ranking:apply-scenario ' . $arg . ' --apply');
            return self::SUCCESS;
        }

        // Garde-fou anti double-application.
        $existingAdjustments = PointLog::where('source', 'adjustment')->count();
        if ($existingAdjustments > 0 && !$this->option('force')) {
            $this->error("⛔ {$existingAdjustments} ligne(s) 'adjustment' existent déjà (revoke ou application précédente).");
            $this->error('   Ré-appliquer cumulerait les retraits. Relance avec --force si tu es certain.');
            return self::FAILURE;
        }

        if (!$this->confirm("Confirmer le retrait de {$t['points_removed']} points pour {$t['users']} utilisateurs (scénario {$key}) ?", false)) {
            $this->info('Annulé. Aucune donnée modifiée.');
            return self::SUCCESS;
        }

        $applied = 0;
        DB::transaction(function () use ($rows, &$applied) {
            foreach ($rows as $r) {
                PointLog::create([
                    'user_id' => $r['user_id'],
                    'source'  => 'adjustment',
                    'points'  => -$r['pos_removed'],
                ]);
                User::where('id', $r['user_id'])->update(['points_total' => $r['total_after']]);
                $applied++;
            }
        });

        $this->newLine();
        $this->info("✅ Scénario {$key} appliqué : {$applied} utilisateurs, {$t['points_removed']} points retirés.");
        $this->info('   Lignes POS d\'origine conservées. Vérifier : php artisan points:audit');

        return self::SUCCESS;
    }
}
