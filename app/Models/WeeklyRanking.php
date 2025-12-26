<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyRanking extends Model
{
    protected $fillable = [
        'user_id',
        'period',
        'points',
        'rank',
        'is_winner',
    ];

    protected $casts = [
        'points' => 'integer',
        'rank' => 'integer',
        'is_winner' => 'boolean',
    ];

    /**
     * Périodes disponibles pour la CAN 2025
     * NOTE: Dates mises à jour pour la phase de test (décembre 2025)
     * 
     * Classements hebdomadaires: Top 5 gagnants par semaine (20 gagnants au total)
     * Classement spécial demi-finale: Classement global, le #1 gagne un billet finale
     */
    public const PERIODS = [
        'week_1' => [
            'label' => 'Semaine 1',
            'start' => '2025-12-21',
            'end' => '2025-12-27',
        ],
        'week_2' => [
            'label' => 'Semaine 2',
            'start' => '2025-12-28',
            'end' => '2026-01-03',
        ],
        'week_3' => [
            'label' => 'Semaine 3',
            'start' => '2026-01-04',
            'end' => '2026-01-10',
        ],
        'week_4' => [
            'label' => 'Semaine 4',
            'start' => '2026-01-11',
            'end' => '2026-01-17',
        ],
        'semifinal' => [
            'label' => 'Spécial Demi-Finale',
            'start' => '2025-12-21', // Début du jeu
            'end' => '2026-01-17', // Fin du jeu
        ],
    ];

    /**
     * L'utilisateur associé à ce classement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Récupère le label d'une période
     */
    public static function getPeriodLabel(string $period): string
    {
        return self::PERIODS[$period]['label'] ?? $period;
    }

    /**
     * Récupère la période actuelle basée sur la date
     */
    public static function getCurrentPeriod(): string
    {
        $now = now()->format('Y-m-d');

        foreach (self::PERIODS as $key => $period) {
            if ($key === 'semifinal') continue; // Ignorer le classement global
            
            if ($now >= $period['start'] && $now <= $period['end']) {
                return $key;
            }
        }

        // Par défaut, retourner semaine 1 si avant la CAN ou semifinal si après
        if ($now < '2025-01-21') {
            return 'week_1';
        }
        
        return 'semifinal';
    }

    /**
     * Récupère les périodes disponibles (passées ou en cours)
     */
    public static function getAvailablePeriods(): array
    {
        $now = now()->format('Y-m-d');
        $available = [];

        foreach (self::PERIODS as $key => $period) {
            // Afficher une période si on est dedans ou si elle est passée
            if ($now >= $period['start'] || $key === self::getCurrentPeriod()) {
                $available[$key] = $period;
            }
        }

        return $available;
    }
}
