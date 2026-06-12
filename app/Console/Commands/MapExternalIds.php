<?php

namespace App\Console\Commands;

use App\Models\MatchGame;
use App\Services\FootballDataService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Bulk-map local matches to football-data.org match ids (external_id).
 *
 * Strategy: 1 API call fetches every match of the configured competition,
 * then each local match without external_id is matched by normalized team
 * pair + kickoff proximity (±30 h, covers timezone drift between the local
 * match_date and the API utcDate).
 *
 * Idempotent and safe to re-run: already-mapped matches are skipped, and
 * TBD knockout matches ("à déterminer") are picked up automatically on a
 * later run once their teams are known. Ambiguous or unmatched matches are
 * reported and left for manual entry in the admin UI.
 */
class MapExternalIds extends Command
{
    protected $signature = 'matches:map-external-ids
                            {--dry-run : Show the mapping without saving anything}';

    protected $description = 'Auto-fill external_id on local matches from football-data.org (by teams + date)';

    /**
     * Known naming differences between the local DB (FIFA-style names used
     * by the seeders) and football-data.org. Keys/values are normalized
     * (lowercase, ascii, alphanumeric only). Both sides are canonicalized
     * through this map before comparison.
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

    public function __construct(private readonly FootballDataService $api)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (!$this->api->enabled()) {
            $this->error('football-data.org integration disabled (FOOTBALL_DATA_ENABLED or FOOTBALL_DATA_API_KEY missing).');
            return self::FAILURE;
        }

        $locals = MatchGame::whereNull('external_id')
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('match_date')
            ->get();

        if ($locals->isEmpty()) {
            $this->info('All matches already have an external_id. Nothing to do — no API call made.');
            return self::SUCCESS;
        }

        $payload = $this->api->getCompetitionMatches();
        if ($payload === null || empty($payload['matches'])) {
            $this->error('API call failed or returned no matches.');
            return self::FAILURE;
        }

        $apiMatches = collect($payload['matches'])->map(function ($m) {
            return [
                'id'      => (string) $m['id'],
                'utc'     => Carbon::parse($m['utcDate']),
                'home'    => $this->teamKeys($m['homeTeam'] ?? []),
                'away'    => $this->teamKeys($m['awayTeam'] ?? []),
                'label'   => ($m['homeTeam']['name'] ?? '?') . ' vs ' . ($m['awayTeam']['name'] ?? '?'),
            ];
        });

        $dryRun = (bool) $this->option('dry-run');
        $usedIds = MatchGame::whereNotNull('external_id')->pluck('external_id')
            ->map(fn ($id) => (string) $id)->flip();

        $mapped = 0;
        $skippedTbd = 0;
        $unmatched = [];

        foreach ($locals as $match) {
            $home = $this->localTeamName($match->homeTeam?->name, $match->team_a);
            $away = $this->localTeamName($match->awayTeam?->name, $match->team_b);

            // Knockout placeholders: teams unknown yet, re-run later.
            if ($home === null || $away === null) {
                $skippedTbd++;
                continue;
            }

            $kickoff = $match->match_date->copy()->utc();

            $best = $apiMatches
                ->reject(fn ($api) => $usedIds->has($api['id']))
                ->filter(fn ($api) => $this->pairMatches($home, $away, $api))
                ->map(fn ($api) => $api + ['diff' => abs($kickoff->diffInMinutes($api['utc'], false))])
                ->filter(fn ($api) => $api['diff'] <= 30 * 60)
                ->sortBy('diff')
                ->first();

            $label = "#{$match->id} {$match->team_a} vs {$match->team_b} ({$match->match_date->format('d/m H:i')})";

            if ($best === null) {
                $unmatched[] = $label;
                continue;
            }

            $this->line(sprintf(
                '%s %s  ->  %s (external_id %s)',
                $dryRun ? '[dry-run]' : '[mapped] ',
                $label,
                $best['label'],
                $best['id']
            ));

            if (!$dryRun) {
                $match->external_id = $best['id'];
                $match->save();
            }
            $usedIds->put($best['id'], true);
            $mapped++;
        }

        $this->newLine();
        $this->info(sprintf(
            '%s: %d mapped, %d TBD skipped (re-run once teams are known), %d unmatched.',
            $dryRun ? 'Dry run' : 'Done',
            $mapped,
            $skippedTbd,
            count($unmatched)
        ));

        foreach ($unmatched as $label) {
            $this->warn("  unmatched: {$label} — set external_id manually in the admin UI");
        }

        return self::SUCCESS;
    }

    /**
     * All normalized name candidates for an API team (name, shortName, tla).
     */
    private function teamKeys(array $team): array
    {
        return collect([$team['name'] ?? null, $team['shortName'] ?? null, $team['tla'] ?? null])
            ->filter()
            ->map(fn ($n) => $this->normalize($n))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * English DB name when the relation is set, otherwise translate the
     * team_a/team_b label back from French. Returns null for TBD labels,
     * including knockout bracket placeholders like "2A", "1C" or "W49".
     */
    private function localTeamName(?string $relationName, ?string $label): ?string
    {
        if ($relationName) {
            return $this->normalize($relationName);
        }
        if ($label === null || Str::contains(Str::lower($label), 'déterminer')) {
            return null;
        }

        // Reverse lookup French label -> canonical English name.
        $english = array_search($label, config('teams_fr', []), true);
        if ($english !== false) {
            return $this->normalize($english);
        }

        // Unknown label containing a digit or slash = bracket placeholder
        // ("2A", "1C", "3A/B/C/D/F", "W49") -> teams not determined yet.
        if (preg_match('/[\d\/]/', $label)) {
            return null;
        }

        return $this->normalize($label);
    }

    private function pairMatches(string $home, string $away, array $api): bool
    {
        $homeHit = in_array($home, $api['home'], true);
        $awayHit = in_array($away, $api['away'], true);
        if ($homeHit && $awayHit) {
            return true;
        }

        // Home/away occasionally inverted between sources.
        return in_array($home, $api['away'], true) && in_array($away, $api['home'], true);
    }

    /**
     * Lowercase, strip accents, turn punctuation runs into single spaces
     * ("Bosnia-Herzegovina" and "Bosnia & Herzegovina" both become
     * "bosnia herzegovina"), then canonicalize through the alias table.
     */
    private function normalize(string $name): string
    {
        $key = trim(preg_replace('/[^a-z0-9]+/', ' ', Str::lower(Str::ascii($name))));
        return self::ALIASES[$key] ?? $key;
    }
}
