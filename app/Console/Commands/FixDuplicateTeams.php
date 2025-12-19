<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\MatchGame;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDuplicateTeams extends Command
{
    protected $signature = 'teams:fix-duplicates';
    protected $description = 'Supprime les doublons d\'équipes en gardant la plus utilisée';

    public function handle()
    {
        $this->newLine();
        $this->info('🔍 Recherche des doublons d\'équipes...');
        $this->newLine();

        // Trouver tous les doublons
        $duplicates = Team::select('name', DB::raw('COUNT(*) as count'))
            ->groupBy('name')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ Aucun doublon trouvé!');
            return 0;
        }

        $this->warn('📊 Doublons détectés:');
        foreach ($duplicates as $dup) {
            $this->line("  - {$dup->name} ({$dup->count} fois)");
        }
        $this->newLine();

        $totalTeams = Team::count();
        $uniqueTeams = Team::distinct('name')->count('name');
        $this->line("Total équipes: {$totalTeams}");
        $this->line("Équipes uniques: {$uniqueTeams}");
        $this->line("Doublons à supprimer: " . ($totalTeams - $uniqueTeams));
        $this->newLine();

        $this->warn('⚠️  Cette opération va :');
        $this->line('   1. Garder l\'équipe LA PLUS UTILISÉE dans les matchs');
        $this->line('   2. Mettre à jour les matchs pour pointer vers l\'équipe conservée');
        $this->line('   3. Supprimer les doublons inutilisés');
        $this->newLine();

        if (!$this->confirm('Voulez-vous continuer?', true)) {
            $this->warn('❌ Opération annulée.');
            return 1;
        }

        $this->newLine();
        $this->info('🔧 Suppression des doublons...');
        $this->newLine();

        DB::beginTransaction();

        try {
            $fixed = 0;
            
            // Pour chaque nom d'équipe en doublon
            foreach ($duplicates as $duplicate) {
                $teamName = $duplicate->name;
                
                // Récupérer toutes les occurrences de cette équipe
                $teams = Team::where('name', $teamName)->orderBy('id', 'asc')->get();
                
                // Pour chaque équipe, compter combien de matchs l'utilisent
                $teamsWithUsage = $teams->map(function($team) {
                    $homeMatches = MatchGame::where('home_team_id', $team->id)->count();
                    $awayMatches = MatchGame::where('away_team_id', $team->id)->count();
                    $team->usage_count = $homeMatches + $awayMatches;
                    return $team;
                });
                
                // Garder celui qui est le PLUS utilisé (ou le premier si égalité)
                $keepTeam = $teamsWithUsage->sortByDesc('usage_count')->first();
                $duplicateIds = $teamsWithUsage->filter(fn($t) => $t->id !== $keepTeam->id)->pluck('id')->toArray();
                
                if (empty($duplicateIds)) {
                    continue;
                }
                
                $this->warn("  Traitement: {$teamName}");
                $this->line("    - ID conservé: <fg=green>{$keepTeam->id}</> (<fg=cyan>{$keepTeam->usage_count} matchs</>)");
                $this->line("    - IDs à supprimer: " . implode(', ', $duplicateIds));
                
                // Afficher l'usage de chaque doublon
                foreach ($teamsWithUsage->filter(fn($t) => $t->id !== $keepTeam->id) as $dup) {
                    $this->line("      <fg=gray>• ID {$dup->id}: {$dup->usage_count} matchs</>");
                }
                
                // Mettre à jour les matchs qui utilisent les doublons
                $updatedHome = MatchGame::whereIn('home_team_id', $duplicateIds)
                    ->update(['home_team_id' => $keepTeam->id]);
                
                $updatedAway = MatchGame::whereIn('away_team_id', $duplicateIds)
                    ->update(['away_team_id' => $keepTeam->id]);
                
                $this->line("    - Matchs mis à jour: {$updatedHome} (home) + {$updatedAway} (away)");
                
                // Supprimer les doublons
                $deleted = Team::whereIn('id', $duplicateIds)->delete();
                $this->line("    - Doublons supprimés: <fg=red>{$deleted}</>");
                $this->newLine();
                
                $fixed += $deleted;
            }
            
            DB::commit();
            
            $this->newLine();
            $this->info('✅ Nettoyage terminé!');
            $this->newLine();
            $this->info('📊 Résumé:');
            $this->line("   Doublons supprimés: <fg=red>{$fixed}</>");
            $this->line("   Équipes restantes: <fg=green>" . Team::count() . "</>");
            $this->info('   Toutes les équipes sont maintenant uniques ✨');
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Erreur: ' . $e->getMessage());
            return 1;
        }
    }
}
