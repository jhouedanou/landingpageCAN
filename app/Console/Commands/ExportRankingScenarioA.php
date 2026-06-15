<?php

namespace App\Console\Commands;

use App\Services\RankingScenarioService;

/**
 * Scénario A — Reset total des bonus POS.
 * Met à zéro TOUS les points POS (venue_visit + bar_visit) et recalcule le
 * classement sur les seuls points de pronostics + connexions quotidiennes.
 * DRY-RUN : aucune écriture en base.
 */
class ExportRankingScenarioA extends AbstractExportRankingCommand
{
    protected $signature = 'ranking:scenario-a
        {--out= : Dossier de sortie (défaut: storage/app/rankings)}
        {--top=10 : Nombre de lignes affichées dans l\'aperçu console}
        {--include-staff : Inclure les comptes admin/soboa dans le classement}';

    protected $description = 'Scénario A (dry-run) : classement avec TOUS les bonus POS remis à zéro. Exporte CSV + HTML.';

    protected function scenario(): string
    {
        return RankingScenarioService::SCENARIO_A;
    }
}
