<?php

namespace App\Console\Commands;

use App\Services\RankingScenarioService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Base commune aux deux commandes de classement (scénarios A et B).
 *
 * STRICTEMENT LECTURE SEULE côté base de données (dry-run) : on ne fait que
 * lire point_logs / predictions / users via RankingScenarioService. Le seul
 * effet de bord est l'écriture des fichiers d'export CSV + HTML sur disque,
 * destinés à la décision finale (le HTML s'imprime en PDF via Ctrl+P).
 */
abstract class AbstractExportRankingCommand extends Command
{
    abstract protected function scenario(): string;

    public function handle(RankingScenarioService $service): int
    {
        $includeStaff = (bool) $this->option('include-staff');
        $top = (int) ($this->option('top') ?: 10);

        $this->warn('⚠️  DRY-RUN : lecture seule, aucune écriture en base de données.');
        $this->newLine();

        $result = $service->build($this->scenario(), $includeStaff);

        $this->info($result['label']);
        $this->line('Règle : ' . $result['rule']);
        $this->newLine();

        // Aperçu console (top N).
        $preview = array_map(
            fn ($r) => [$r['rang'], $r['user_id'], $r['name'], $r['points_pronostics'], $r['points_connexion'], $r['points_pos'], $r['total']],
            array_slice($result['rows'], 0, $top)
        );
        $this->table(
            ['Rang', 'ID', 'Nom', 'Points pronostic', 'Points connexion', 'Points visite PDV', 'TOTAL'],
            $preview
        );

        $t = $result['totals'];
        $this->newLine();
        $this->line("👥 Utilisateurs classés : {$t['utilisateurs']}");
        $this->line("✅ Bonus POS retenus    : {$t['bonus_pos_retenus']}");
        $this->line("➖ Bonus POS écartés     : {$t['bonus_pos_ecartes']}");
        $this->line("🎯 Points POS distribués : {$t['points_pos_total']}");
        $this->newLine();

        // Écriture des fichiers.
        $dir = $this->option('out') ?: storage_path('app/rankings');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $stamp = Carbon::now()->format('Ymd-His');
        $base = rtrim($dir, '/\\') . "/classement-scenario-{$result['scenario']}-{$stamp}";

        $csvPath = $base . '.csv';
        $htmlPath = $base . '.html';
        file_put_contents($csvPath, $service->toCsv($result));
        file_put_contents($htmlPath, $service->toHtml($result));

        $this->info('📄 CSV  : ' . $csvPath);
        $this->info('📄 HTML : ' . $htmlPath . '  (ouvrir puis Ctrl+P → Enregistrer en PDF)');

        return self::SUCCESS;
    }
}
