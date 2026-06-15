<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bar;
use App\Models\CheckIn;
use App\Models\PointLog;
use App\Services\CheckInService;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckInController extends Controller
{
    public function store(Request $request, PointsService $pointsService, CheckInService $checkInService)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        $userLat = $request->latitude;
        $userLng = $request->longitude;

        // Simple geofencing check
        // Find if user is within a certain radius (e.g., 200m) of any active bar
        $bars = Bar::where('is_active', true)->get();

        $foundBar = null;
        foreach ($bars as $bar) {
            $distance = $this->calculateDistance($userLat, $userLng, $bar->latitude, $bar->longitude);
            if ($distance <= 0.2) { // 200 meters in km
                $foundBar = $bar;
                break;
            }
        }

        if ($foundBar) {
            // PREUVE (A1) : persiste le check-in géolocalisé (coordonnées, précision
            // GPS, distance au PDV, IP, user-agent) — trace horodatée opposable en
            // cas de contestation et base des détections de fraude (vitesse, IP, GPS).
            $checkInService->record(
                $user,
                $foundBar,
                (float) $userLat,
                (float) $userLng,
                $request->filled('accuracy') ? (float) $request->accuracy : null,
                CheckIn::SOURCE_CHECKIN,
                null,
                $request
            );

            // RÈGLE 2026 : le check-in NE rapporte AUCUN point. Les +4 points venue
            // s'obtiennent uniquement via un pronostic fait sur place (voir
            // PointsService::awardPredictionVenuePoints).
            return response()->json([
                'success' => true,
                'message' => "Bienvenue à {$foundBar->name} ! Faites vos pronostics sur place pour gagner +4 points.",
                'points_awarded' => 0,
                'user_points_total' => $user->points_total,
                'bar_name' => $foundBar->name,
                'venue_id' => $foundBar->id
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Aucun PDV trouvé à proximité'
        ], 404);
    }

    /**
     * Vérifier si l'utilisateur a déjà fait son check-in aujourd'hui pour ce venue
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'venue_id' => 'required|exists:bars,id',
        ]);

        $user = Auth::user();
        
        // Vérifier si un check-in existe aujourd'hui pour ce venue
        $todayCheckIn = PointLog::where('user_id', $user->id)
            ->where('bar_id', $request->venue_id)
            ->where('source', 'venue_visit')
            ->whereDate('created_at', Carbon::today())
            ->first();

        return response()->json([
            'already_checked_in' => $todayCheckIn !== null,
            'venue_id' => $request->venue_id,
        ]);
    }

    /**
     * Haversine formula to calculate distance in km
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
