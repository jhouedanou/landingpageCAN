<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\MatchGame;
use Illuminate\Console\Command;

class DebugSenegalMatches extends Command
{
    protected $signature = 'debug:senegal-matches';
    protected $description = 'Debug Senegal matches and team information';

    public function handle()
    {
        $this->info("=== Senegal Team Search ===");
        
        // Search by iso_code
        $senegalByIso = Team::where('iso_code', 'sn')->first();
        if ($senegalByIso) {
            $this->info("✅ Found Senegal by iso_code 'sn': ID={$senegalByIso->id}, Name={$senegalByIso->name}");
        } else {
            $this->warn("❌ Senegal not found by iso_code 'sn'");
        }
        
        // Search by name
        $senegalByName = Team::where('name', 'Sénégal')->first();
        if ($senegalByName) {
            $this->info("✅ Found Senegal by name 'Sénégal': ID={$senegalByName->id}, ISO={$senegalByName->iso_code}");
        } else {
            $this->warn("❌ Senegal not found by name 'Sénégal'");
        }
        
        // List all teams
        $this->info("\n=== All Teams ===");
        $allTeams = Team::orderBy('name')->get();
        foreach ($allTeams as $team) {
            $this->info("ID={$team->id}, Name={$team->name}, ISO={$team->iso_code}, Group={$team->group}");
        }
        
        // Check Senegal matches
        $this->info("\n=== Senegal Matches ===");
        if ($senegalByIso) {
            $matches = MatchGame::where(function($query) use ($senegalByIso) {
                $query->where('home_team_id', $senegalByIso->id)
                    ->orWhere('away_team_id', $senegalByIso->id);
            })->orderBy('match_date')->get();
            
            if ($matches->isEmpty()) {
                $this->warn("❌ No matches found for Senegal");
            } else {
                $this->info("✅ Found " . $matches->count() . " matches for Senegal:");
                foreach ($matches as $match) {
                    $this->info("  - {$match->team_a} vs {$match->team_b} | Date: {$match->match_date} | Status: {$match->status}");
                }
            }
            
            // Check scheduled matches only
            $this->info("\n=== Senegal Scheduled Matches ===");
            $scheduledMatches = MatchGame::where('status', 'scheduled')
                ->where(function($query) use ($senegalByIso) {
                    $query->where('home_team_id', $senegalByIso->id)
                        ->orWhere('away_team_id', $senegalByIso->id);
                })->orderBy('match_date')->get();
            
            if ($scheduledMatches->isEmpty()) {
                $this->warn("❌ No scheduled matches found for Senegal");
            } else {
                $this->info("✅ Found " . $scheduledMatches->count() . " scheduled matches:");
                foreach ($scheduledMatches as $match) {
                    $this->info("  - {$match->team_a} vs {$match->team_b} | {$match->match_date}");
                }
            }
        }
        
        return 0;
    }
}
