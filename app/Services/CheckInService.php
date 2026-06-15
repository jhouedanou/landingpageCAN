<?php

namespace App\Services;

use App\Models\Bar;
use App\Models\CheckIn;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Persiste les preuves de présence (table check_ins). Centralise le calcul de la
 * distance au PDV et la capture de l'empreinte réseau (IP + user-agent), pour que
 * les différents flux (check-in explicite, pronostic sur place) écrivent une preuve
 * homogène.
 */
class CheckInService
{
    public function __construct(protected GeolocationService $geolocationService)
    {
    }

    /**
     * Enregistre une preuve de présence géolocalisée.
     *
     * @param User       $user
     * @param Bar|null   $bar          PDV détecté à proximité (NULL si aucun).
     * @param float      $latitude     Latitude du téléphone.
     * @param float      $longitude    Longitude du téléphone.
     * @param float|null $gpsAccuracy  Précision GPS annoncée (mètres).
     * @param string     $source       CheckIn::SOURCE_CHECKIN ou SOURCE_PREDICTION.
     * @param int|null   $predictionId Pronostic associé (flux « sur place »).
     * @param Request|null $request    Pour capturer IP + user-agent.
     */
    public function record(
        User $user,
        ?Bar $bar,
        float $latitude,
        float $longitude,
        ?float $gpsAccuracy = null,
        string $source = CheckIn::SOURCE_CHECKIN,
        ?int $predictionId = null,
        ?Request $request = null
    ): CheckIn {
        $distanceM = null;
        if ($bar && $bar->latitude !== null && $bar->longitude !== null) {
            $km = $this->geolocationService->calculateHaversineDistance(
                $latitude,
                $longitude,
                (float) $bar->latitude,
                (float) $bar->longitude
            );
            $distanceM = (int) round($km * 1000);
        }

        return CheckIn::create([
            'user_id' => $user->id,
            'bar_id' => $bar?->id,
            'prediction_id' => $predictionId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'gps_accuracy' => $gpsAccuracy,
            'distance_m' => $distanceM,
            'source' => $source,
            'ip_address' => $request?->ip(),
            'user_agent' => $request ? substr((string) $request->userAgent(), 0, 512) : null,
        ]);
    }
}
