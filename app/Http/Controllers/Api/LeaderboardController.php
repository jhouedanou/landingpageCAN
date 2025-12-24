<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    protected LeaderboardService $leaderboardService;

    public function __construct(LeaderboardService $leaderboardService)
    {
        $this->leaderboardService = $leaderboardService;
    }

    /**
     * Récupère le leaderboard complet
     * - TOP 5 national (noms abrégés)
     * - Position de l'utilisateur connecté
     * - Filtrable par période (global, week_1, week_2, week_3, week_4, semifinal)
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'global');
        $currentUser = Auth::user();
        
        $leaderboardData = $this->leaderboardService->getLeaderboardData(
            $currentUser?->id, 
            $period
        );

        return response()->json([
            'top5' => $leaderboardData['top5'],
            'user_position' => $leaderboardData['user_position'],
            'user_in_top5' => $leaderboardData['user_in_top5'],
            'period' => $period,
            'period_label' => $leaderboardData['period_label'],
            'available_periods' => array_keys($leaderboardData['available_periods']),
        ]);
    }

    /**
     * Récupère les gagnants d'une période (TOP 5)
     */
    public function winners(Request $request)
    {
        $period = $request->get('period', 'week_1');
        $winners = $this->leaderboardService->getWeeklyWinners($period);

        return response()->json([
            'period' => $period,
            'winners' => $winners,
        ]);
    }
}
