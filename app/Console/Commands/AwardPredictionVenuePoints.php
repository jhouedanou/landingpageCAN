<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\PointLog;
use App\Services\PointsService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AwardPredictionVenuePoints extends Command
{
    protected $signature = 'points:award-prediction-venue {phone} {match_id} {bar_id} {--date=today}';
    protected $description = 'Award 4 points for predictions made in venues for a specific user, match and bar';

    public function handle()
    {
        $phone = $this->argument('phone');
        $matchId = $this->argument('match_id');
        $barId = $this->argument('bar_id');
        $dateString = $this->option('date');
        
        // Find user by phone
        $user = User::where('phone', $phone)->first();
        
        if (!$user) {
            $this->error("User with phone {$phone} not found.");
            return 1;
        }
        
        $this->info("Found user: {$user->name} ({$user->phone})");
        
        // Parse date
        $date = $dateString === 'today' ? Carbon::today() : Carbon::parse($dateString);
        
        // Check if user already has venue_visit points for this date
        $alreadyAwarded = PointLog::where('user_id', $user->id)
            ->where('source', 'venue_visit')
            ->whereDate('created_at', $date)
            ->first();
        
        if ($alreadyAwarded) {
            $this->info("User already has {$alreadyAwarded->points} points awarded on {$date->format('Y-m-d')}.");
            return 0;
        }
        
        // Award points (with match and venue verification)
        $pointsService = app(PointsService::class);
        $pointsAwarded = $pointsService->awardPredictionVenuePoints($user, $matchId, $barId);
        
        if ($pointsAwarded > 0) {
            $this->info("✅ Successfully awarded {$pointsAwarded} points to user {$user->name}.");
            $this->info("Total points: {$user->points_total}");
            return 0;
        } else {
            $this->warn("⚠️  No points awarded. Possible reasons:");
            $this->line("  - User already received venue points today");
            $this->line("  - Match is not being shown at this venue");
            return 0;
        }
    }
}
