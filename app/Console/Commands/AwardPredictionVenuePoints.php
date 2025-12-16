<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\PointLog;
use App\Services\PointsService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AwardPredictionVenuePoints extends Command
{
    protected $signature = 'points:award-prediction-venue {phone} {--date=today}';
    protected $description = 'Award 4 points for predictions made in venues for a specific user and date';

    public function handle()
    {
        $phone = $this->argument('phone');
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
        
        // Award points
        $pointsService = app(PointsService::class);
        $pointsAwarded = $pointsService->awardPredictionVenuePoints($user);
        
        if ($pointsAwarded > 0) {
            $this->info("✅ Successfully awarded {$pointsAwarded} points to user {$user->name}.");
            $this->info("Total points: {$user->points_total}");
            return 0;
        } else {
            $this->info("User already received venue points today.");
            return 0;
        }
    }
}
