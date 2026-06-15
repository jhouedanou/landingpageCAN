<?php

namespace App\Console\Commands;

use App\Services\RankingScenarioService;

/**
 * Scénario B — Recalcul conditionnel des bonus POS.
 * Conserve un bonus POS de +4 uniquement si un pronostic a été soumis le même
 * jour, à ou après le check-in (created_at >= bonus). Plafond anti-farming :
 * 1 bonus max par (utilisateur, PDV, jour). DRY-RUN : aucune écriture en base.
 */
class ExportRankingScenarioB extends AbstractExportRankingCommand
{
    protected $signature = 'ranking:scenario-b
        {--out= : Dossier de sortie (défaut: storage/app/rankings)}
        {--top=10 : Nombre de lignes affichées dans l\'aperçu console}
        {--include-staff : Inclure les comptes admin/soboa dans le classement}';

    protected $description = 'Scénario B (dry-run) : classement avec bonus POS conservé seulement si un pronostic a suivi le check-in (cap 1/PDV/jour). Exporte CSV + HTML.';

    protected function scenario(): string
    {
        return RankingScenarioService::SCENARIO_B;
    }
}
