<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Normalisation des noms d'équipes pour la correspondance entre la base
 * locale (noms FIFA anglais des seeders) et football-data.org.
 * Partagé par matches:map-external-ids et matches:sync-knockout-teams.
 */
class TeamNameNormalizer
{
    /**
     * Différences de nommage connues entre les deux sources. Clés/valeurs
     * normalisées (minuscules, ascii, alphanumérique). Les deux côtés
     * passent par cette table avant comparaison.
     */
    private const ALIASES = [
        'united states'          => 'usa',
        'korea republic'         => 'south korea',
        'ir iran'                => 'iran',
        'bosnia and herzegovina' => 'bosnia herzegovina',
        'cabo verde'             => 'cape verde',
        'cape verde islands'     => 'cape verde',
        'congo dr'               => 'dr congo',
        'cote d ivoire'          => 'ivory coast',
        'czechia'                => 'czech republic',
        'turkiye'                => 'turkey',
    ];

    /**
     * Minuscules, accents supprimés, ponctuation remplacée par des espaces
     * ("Bosnia-Herzegovina" et "Bosnia & Herzegovina" donnent tous deux
     * "bosnia herzegovina"), puis canonisation via la table d'alias.
     */
    public static function normalize(string $name): string
    {
        $key = trim(preg_replace('/[^a-z0-9]+/', ' ', Str::lower(Str::ascii($name))));
        return self::ALIASES[$key] ?? $key;
    }

    /**
     * Tous les candidats normalisés pour une équipe du payload API
     * (name, shortName, tla).
     */
    public static function teamKeys(array $team): array
    {
        return collect([$team['name'] ?? null, $team['shortName'] ?? null, $team['tla'] ?? null])
            ->filter()
            ->map(fn ($n) => self::normalize($n))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Libellé d'équipe non encore déterminé : null, "À déterminer", ou
     * placeholder de bracket ("2A", "1C", "3A/B/C/D/F", "W49").
     */
    public static function isPlaceholder(?string $label): bool
    {
        if ($label === null || trim($label) === '') {
            return true;
        }
        if (Str::contains(Str::lower($label), 'déterminer')) {
            return true;
        }
        return (bool) preg_match('/[\d\/]/', $label);
    }
}
