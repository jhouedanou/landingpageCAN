<?php

/**
 * Script pour supprimer les doublons d'équipes
 * 
 * Usage: php fix-duplicate-teams.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Team;
use App\Models\MatchGame;
use Illuminate\Support\Facades\DB;

echo "🔍 Recherche des doublons d'équipes...\n\n";

// Trouver tous les doublons
$duplicates = Team::select('name', DB::raw('COUNT(*) as count'))
    ->groupBy('name')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->isEmpty()) {
    echo "✅ Aucun doublon trouvé!\n";
    exit(0);
}

echo "📊 Doublons détectés:\n";
foreach ($duplicates as $dup) {
    echo "  - {$dup->name} ({$dup->count} fois)\n";
}
echo "\n";

$totalTeams = Team::count();
$uniqueTeams = Team::distinct('name')->count('name');
echo "Total équipes: {$totalTeams}\n";
echo "Équipes uniques: {$uniqueTeams}\n";
echo "Doublons à supprimer: " . ($totalTeams - $uniqueTeams) . "\n\n";

echo "⚠️  Cette opération va :\n";
echo "   1. Garder la première occurrence de chaque équipe\n";
echo "   2. Mettre à jour les matchs pour pointer vers l'équipe conservée\n";
echo "   3. Supprimer les doublons\n\n";

echo "Voulez-vous continuer? (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim(strtolower($line)) !== 'yes') {
    echo "❌ Opération annulée.\n";
    exit(0);
}

echo "\n🔧 Suppression des doublons...\n\n";

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
        
        echo "  Traitement: {$teamName}\n";
        echo "    - ID conservé: {$keepTeam->id} ({$keepTeam->usage_count} matchs)\n";
        echo "    - IDs à supprimer: " . implode(', ', $duplicateIds) . "\n";
        
        // Afficher l'usage de chaque doublon
        foreach ($teamsWithUsage->filter(fn($t) => $t->id !== $keepTeam->id) as $dup) {
            echo "      • ID {$dup->id}: {$dup->usage_count} matchs\n";
        }
        
        // Mettre à jour les matchs qui utilisent les doublons
        $updatedHome = MatchGame::whereIn('home_team_id', $duplicateIds)
            ->update(['home_team_id' => $keepTeam->id]);
        
        $updatedAway = MatchGame::whereIn('away_team_id', $duplicateIds)
            ->update(['away_team_id' => $keepTeam->id]);
        
        echo "    - Matchs mis à jour: {$updatedHome} (home) + {$updatedAway} (away)\n";
        
        // Supprimer les doublons
        $deleted = Team::whereIn('id', $duplicateIds)->delete();
        echo "    - Doublons supprimés: {$deleted}\n\n";
        
        $fixed += $deleted;
    }
    
    DB::commit();
    
    echo "✅ Nettoyage terminé!\n\n";
    echo "📊 Résumé:\n";
    echo "   Doublons supprimés: {$fixed}\n";
    echo "   Équipes restantes: " . Team::count() . "\n";
    echo "   Toutes les équipes sont maintenant uniques ✨\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
