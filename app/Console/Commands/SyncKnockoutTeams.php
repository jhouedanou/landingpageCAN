<?php

namespace App\Console\Commands;

use App\Models\MatchGame;
use App\Models\Team;
use App\Services\FootballDataService;
use App\Services\TeamNameNormalizer;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Pull knockout-stage teams from football-data.org into local TBD matches.
 *
 * Two passes, 1 API call total:
 *  1. Assign external_id to knockout placeholders ("2A vs 2B") by matching
 *     stage + kickoff time — name matching is impossible before teams are
 *     known, but stage+datetime identifies the slot unambiguously.
 *  2. For every knockout match with an external_id, fill each side that is
 *     still a placeholder locally once the API has the real team.
 *
 * Fill-only-empty: a side the admin already set (home/away_team_id present
 * or a real team label) is never overwritten, so manual placement always
 * wins. Idempotent — safe to re-run any time (button or scheduler).
 */
class SyncKnockoutTeams extends Command
{
    protected $signature = 'matches:sync-knockout-teams
                            {--dry-run : Show what would change without saving}';

    protected $description = 'Fill knockout match teams (and external_id) from football-data.org';

    private const STAGE_MAP = [
        'round_of_32'   => 'LAST_32',
        'round_of_16'   => 'LAST_16',
        'quarter_final' => 'QUARTER_FINALS',
        'semi_final'    => 'SEMI_FINALS',
        'third_place'   => 'THIRD_PLACE',
        'final'         => 'FINAL',
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

        $locals = MatchGame::where('phase', '!=', 'group_stage')->get();

        $needsWork = $locals->filter(
            fn ($m) => $m->external_id === null
                || $this->sideIsOpen($m->home_team_id, $m->team_a)
                || $this->sideIsOpen($m->away_team_id, $m->team_b)
        );

        if ($needsWork->isEmpty()) {
            $this->info('Every knockout match is fully mapped and has its teams. Nothing to do — no API call made.');
            return self::SUCCESS;
        }

        $payload = $this->api->getCompetitionMatches();
        if ($payload === null || empty($payload['matches'])) {
            $this->error('API call failed or returned no matches.');
            return self::FAILURE;
        }

        $apiKnockout = collect($payload['matches'])
            ->filter(fn ($m) => ($m['stage'] ?? '') !== 'GROUP_STAGE')
            ->map(fn ($m) => [
                'id'    => (string) $m['id'],
                'stage' => $m['stage'],
                'utc'   => Carbon::parse($m['utcDate']),
                'home'  => $m['homeTeam'] ?? [],
                'away'  => $m['awayTeam'] ?? [],
            ]);

        $byId = $apiKnockout->keyBy('id');
        $usedIds = MatchGame::whereNotNull('external_id')->pluck('external_id')
            ->map(fn ($id) => (string) $id)->flip();

        // Local teams indexed by normalized canonical name.
        $teamsByKey = Team::all()->keyBy(fn ($t) => TeamNameNormalizer::normalize($t->name));

        $dryRun = (bool) $this->option('dry-run');
        $idsAssigned = 0;
        $sidesFilled = 0;
        $pending = 0;
        $problems = [];

        foreach ($locals as $match) {
            $changed = false;

            // Pass 1 — external_id via stage + kickoff (±90 min).
            if ($match->external_id === null) {
                $apiStage = self::STAGE_MAP[$match->phase] ?? null;
                if ($apiStage === null) {
                    $problems[] = "#{$match->id}: phase inconnue \"{$match->phase}\"";
                    continue;
                }

                $kickoff = $match->match_date->copy()->utc();
                $candidates = $apiKnockout
                    ->reject(fn ($api) => $usedIds->has($api['id']))
                    ->filter(fn ($api) => $api['stage'] === $apiStage
                        && abs($kickoff->diffInMinutes($api['utc'], false)) <= 90)
                    ->values();

                if ($candidates->count() !== 1) {
                    $problems[] = sprintf(
                        '#%d %s vs %s (%s): %d candidat(s) API pour %s — réglage manuel requis',
                        $match->id, $match->team_a, $match->team_b,
                        $match->match_date->format('d/m H:i'),
                        $candidates->count(), $apiStage
                    );
                    continue;
                }

                $match->external_id = $candidates->first()['id'];
                $usedIds->put($match->external_id, true);
                $idsAssigned++;
                $changed = true;
                $this->line(sprintf(
                    '%s #%d %s vs %s (%s) -> external_id %s',
                    $dryRun ? '[dry-run]' : '[id]     ',
                    $match->id, $match->team_a, $match->team_b,
                    $match->phase, $match->external_id
                ));
            }

            // Pass 2 — fill TBD sides once the API knows the teams.
            $api = $byId->get((string) $match->external_id);
            if ($api) {
                foreach ([['home_team_id', 'team_a', 'home'], ['away_team_id', 'team_b', 'away']] as [$idCol, $labelCol, $side]) {
                    if (!$this->sideIsOpen($match->{$idCol}, $match->{$labelCol})) {
                        continue;
                    }
                    $apiName = $api[$side]['name'] ?? null;
                    if ($apiName === null) {
                        $pending++;
                        continue; // l'API ne connaît pas encore l'équipe
                    }

                    $team = collect(TeamNameNormalizer::teamKeys($api[$side]))
                        ->map(fn ($key) => $teamsByKey->get($key))
                        ->filter()
                        ->first();

                    $newLabel = $team ? $team->name : $apiName;
                    $this->line(sprintf(
                        '%s #%d %s: %s -> %s%s',
                        $dryRun ? '[dry-run]' : '[team]   ',
                        $match->id, $labelCol, $match->{$labelCol} ?? '∅', $newLabel,
                        $team ? '' : ' (équipe absente de la table teams, id non lié)'
                    ));

                    $match->{$labelCol} = $newLabel;
                    $match->{$idCol} = $team?->id;
                    $sidesFilled++;
                    $changed = true;
                }
            }

            if ($changed && !$dryRun) {
                $match->save();
            }
        }

        $this->newLine();
        $this->info(sprintf(
            '%s: %d external_id assigné(s), %d équipe(s) remplie(s), %d côté(s) en attente côté API.',
            $dryRun ? 'Dry run' : 'Terminé',
            $idsAssigned,
            $sidesFilled,
            $pending
        ));

        foreach ($problems as $p) {
            $this->warn('  ' . $p);
        }

        return self::SUCCESS;
    }

    /**
     * A side is fillable only when no team id is set AND the label is still
     * a placeholder. Anything the admin placed manually is left untouched.
     */
    private function sideIsOpen(?int $teamId, ?string $label): bool
    {
        return $teamId === null && TeamNameNormalizer::isPlaceholder($label);
    }
}
