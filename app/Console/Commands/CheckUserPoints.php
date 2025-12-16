<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\PointLog;
use App\Models\Prediction;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckUserPoints extends Command
{
    protected $signature = 'points:check-user {phone}';
    protected $description = 'Check points and predictions for a user';

    public function handle()
    {
        $phone = $this->argument('phone');
        
        // Find user by phone
        $user = User::where('phone', $phone)->first();
        
        if (!$user) {
            $this->error("User with phone {$phone} not found.");
            return 1;
        }
        
        $this->info("=== User Information ===");
        $this->info("Name: {$user->name}");
        $this->info("Phone: {$user->phone}");
        $this->info("Total Points: {$user->points_total}");
        
        $this->info("\n=== Recent Predictions ===");
        $predictions = Prediction::with('match')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        if ($predictions->isEmpty()) {
            $this->info("No predictions found.");
        } else {
            foreach ($predictions as $pred) {
                $this->info("Match: {$pred->match->team_a} vs {$pred->match->team_b} ({$pred->match->match_date})");
                $this->info("  Prediction: {$pred->score_a} - {$pred->score_b}");
                $this->info("  Created: {$pred->created_at->format('Y-m-d H:i:s')}");
            }
        }
        
        $this->info("\n=== Points Breakdown ===");
        $today = Carbon::today();
        
        $pointLogs = PointLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $sources = [];
        foreach ($pointLogs as $log) {
            $source = $log->source;
            if (!isset($sources[$source])) {
                $sources[$source] = ['count' => 0, 'total' => 0];
            }
            $sources[$source]['count']++;
            $sources[$source]['total'] += $log->points;
        }
        
        foreach ($sources as $source => $data) {
            $this->info("{$source}: {$data['count']} entries = {$data['total']} points");
        }
        
        $this->info("\n=== Today's Points (Venue Visit) ===");
        $venuePoints = PointLog::where('user_id', $user->id)
            ->where('source', 'venue_visit')
            ->whereDate('created_at', $today)
            ->first();
        
        if ($venuePoints) {
            $this->info("✅ User already has venue_visit points today: {$venuePoints->points} points");
            $this->info("Awarded at: {$venuePoints->created_at->format('Y-m-d H:i:s')}");
        } else {
            $this->warn("❌ User does NOT have venue_visit points for today!");
        }
        
        return 0;
    }
}
