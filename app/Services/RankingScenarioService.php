<?php

namespace App\Services;

use App\Models\PointLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Recalcule, EN LECTURE SEULE (dry-run), deux classements alternatifs selon
 * deux politiques de bonus « point de vente » (POS). Aucune écriture en base :
 * uniquement des SELECT + un calcul en mémoire. Le seul effet de bord est
 * l'écriture des fichiers d'export (CSV / HTML) sur le disque.
 *
 * RAPPEL DONNÉES (voir mémo) :
 *  - Pas de table check_ins : un check-in géolocalisé n'est PAS persisté.
 *  - Les seules traces POS sont dans point_logs :
 *      • venue_visit : créé À la soumission d'un pronostic géolocalisé en PDV
 *        (a match_id) → lié à un pronostic par construction = légitime.
 *      • bar_visit   : ancien bonus de check-in (match_id NULL) → aucun lien.
 *  - predictions n'a PAS de bar_id : on ne peut relier un pronostic qu'au
 *    couple (utilisateur, jour), pas au PDV précis.
 */
class RankingScenarioService
{
    public const SCENARIO_A = 'A';
    public const SCENARIO_B = 'B';

    /** Pronostics + connexion quotidienne = base « points légitimes ». */
    private const BASE_PREDICTION_SOURCES = [
        'prediction', 'prediction_participation', 'prediction_winner',
        'prediction_exact', 'accuracy',
    ];
    private const BASE_LOGIN_SOURCES = ['login', 'login_daily'];

    /** Bonus point de vente, exclus de la base et traités selon le scénario. */
    private const POS_SOURCES = ['venue_visit', 'bar_visit'];

    /** Valeur d'un bonus POS légitime. */
    private const POS_BONUS = 4;

    /**
     * Construit un classement complet pour le scénario demandé.
     *
     * @return array{scenario:string, label:string, rule:string, rows:array<int,array>, totals:array}
     */
    public function build(string $scenario, bool $includeStaff = false): array
    {
        $scenario = strtoupper($scenario);

        // 1) Base : pronostics + connexion quotidienne, agrégée par utilisateur.
        $base = $this->basePointsByUser();

        // 2) Bonus POS selon le scénario.
        if ($scenario === self::SCENARIO_A) {
            $pos = collect();          // tous les POS remis à zéro
            $posKept = 0;
            $posDropped = $this->countAllPosBonuses();
        } else {
            [$pos, $posKept, $posDropped] = $this->posPointsScenarioB();
        }

        // 3) Utilisateurs concernés.
        $userIds = $base->keys()->merge($pos->keys())->unique();
        $usersQuery = User::whereIn('id', $userIds);
        if (!$includeStaff) {
            $usersQuery->where('role', 'user');
        }
        $users = $usersQuery->get(['id', 'name', 'phone', 'role'])->keyBy('id');

        // 4) Lignes du classement.
        $rows = [];
        foreach ($users as $id => $u) {
            $pred  = (int) ($base[$id]['pred'] ?? 0);
            $login = (int) ($base[$id]['login'] ?? 0);
            $posPts = (int) ($pos[$id]['points'] ?? 0);
            $posCount = (int) ($pos[$id]['count'] ?? 0);
            $total = $pred + $login + $posPts;

            if ($total === 0 && $posCount === 0) {
                continue; // pas de score → hors classement
            }

            $rows[] = [
                'user_id'        => $id,
                'name'           => $u->name,
                'phone'          => $u->phone,
                'points_pronostics' => $pred,
                'points_connexion'  => $login,
                'points_pos'        => $posPts,
                'bonus_pos_retenus' => $posCount,
                'total'             => $total,
            ];
        }

        // 5) Tri (total desc, puis nom) + rang.
        usort($rows, function ($a, $b) {
            return $b['total'] <=> $a['total']
                ?: strcasecmp((string) $a['name'], (string) $b['name']);
        });
        foreach ($rows as $i => &$row) {
            $row['rang'] = $i + 1;
        }
        unset($row);

        return [
            'scenario' => $scenario,
            'label'    => $scenario === self::SCENARIO_A
                ? 'Scénario A — Reset total des bonus POS'
                : 'Scénario B — Recalcul conditionnel des bonus POS',
            'rule'     => $scenario === self::SCENARIO_A
                ? 'Tous les bonus POS (venue_visit + bar_visit) sont mis à zéro. Classement = pronostics + connexions quotidiennes uniquement.'
                : 'Le bonus de +4 points est conservé uniquement si un pronostic est effectué le même jour après le check-in, avec un plafonnement à un seul check-in par point de vente et par jour. Les check-ins sans pronostic associé sont retirés.',
            'rows'     => $rows,
            'totals'   => [
                'utilisateurs'   => count($rows),
                'bonus_pos_retenus'  => $posKept,
                'bonus_pos_ecartes'  => $posDropped,
                'points_pos_total'   => $pos->sum('points'),
            ],
        ];
    }

    /**
     * Base par utilisateur : pronostics et connexions quotidiennes.
     *
     * @return Collection<int, array{pred:int, login:int}>
     */
    private function basePointsByUser(): Collection
    {
        $predList  = "'" . implode("','", self::BASE_PREDICTION_SOURCES) . "'";
        $loginList = "'" . implode("','", self::BASE_LOGIN_SOURCES) . "'";

        return PointLog::query()
            ->selectRaw('user_id')
            ->selectRaw("SUM(CASE WHEN source IN ($predList) THEN points ELSE 0 END) AS pred")
            ->selectRaw("SUM(CASE WHEN source IN ($loginList) THEN points ELSE 0 END) AS login")
            ->whereIn('source', array_merge(self::BASE_PREDICTION_SOURCES, self::BASE_LOGIN_SOURCES))
            ->groupBy('user_id')
            ->get()
            ->mapWithKeys(fn ($r) => [(int) $r->user_id => [
                'pred'  => (int) $r->pred,
                'login' => (int) $r->login,
            ]]);
    }

    /** Nombre total de bonus POS existants (pour le rapport scénario A). */
    private function countAllPosBonuses(): int
    {
        return PointLog::whereIn('source', self::POS_SOURCES)
            ->where('points', '>', 0)
            ->count();
    }

    /**
     * Scénario B : recalcule les bonus POS légitimes selon la règle officielle.
     * « Le bonus de +4 points est conservé uniquement si un pronostic est effectué
     *   le même jour après le check-in, avec un plafonnement à un seul check-in
     *   par point de vente et par jour. »
     *  - venue_visit : pronostic réellement fait en PDV → légitime.
     *  - bar_visit   : conservé seulement si un pronostic du même utilisateur a été
     *                  soumis le MÊME JOUR, À/APRÈS l'heure du check-in.
     *  - plafond : un seul +4 par (utilisateur, PDV, jour).
     *
     * @return array{0:Collection<int,array{points:int,count:int}>, 1:int, 2:int}
     */
    private function posPointsScenarioB(): array
    {
        $posRows = PointLog::query()
            ->whereIn('source', self::POS_SOURCES)
            ->where('points', '>', 0)
            ->get(['id', 'user_id', 'bar_id', 'source', 'points', 'created_at']);

        // Pronostics par utilisateur (created_at), pour tester « même jour, après le check-in ».
        $predsByUser = \App\Models\Prediction::query()
            ->whereIn('user_id', $posRows->pluck('user_id')->unique())
            ->get(['user_id', 'created_at'])
            ->groupBy('user_id')
            ->map(fn ($g) => $g->pluck('created_at')->filter()->map(fn ($d) => Carbon::parse($d))->values());

        $legit = [];      // [user|bar|jour] => points (plafond : 1 check-in par PDV et par jour)
        $kept = 0;
        $dropped = 0;

        foreach ($posRows as $row) {
            $isLegit = false;

            if ($row->source === 'venue_visit') {
                // Pronostic réellement fait en PDV → légitime.
                $isLegit = true;
            } else {
                // bar_visit : +4 conservé seulement si un pronostic a été fait le
                // même jour, à/après l'heure du check-in (created_at pronostic >= check-in).
                $checkin = Carbon::parse($row->created_at);
                foreach ($predsByUser->get($row->user_id, collect()) as $ts) {
                    if ($ts->greaterThanOrEqualTo($checkin) && $ts->isSameDay($checkin)) {
                        $isLegit = true;
                        break;
                    }
                }
            }

            if (!$isLegit) {
                $dropped++;
                continue;
            }

            // Plafond : un seul +4 par (utilisateur, PDV, jour).
            $day = Carbon::parse($row->created_at)->toDateString();
            $key = $row->user_id . '|' . ($row->bar_id ?? 'null') . '|' . $day;
            if (isset($legit[$key])) {
                $dropped++; // doublon (même user, même PDV, même jour) → plafonné
                continue;
            }
            $legit[$key] = ['user_id' => $row->user_id, 'points' => self::POS_BONUS];
            $kept++;
        }

        // Agrégation par utilisateur.
        $byUser = collect($legit)
            ->groupBy('user_id')
            ->mapWithKeys(fn ($g, $uid) => [(int) $uid => [
                'points' => $g->sum('points'),
                'count'  => $g->count(),
            ]]);

        return [$byUser, $kept, $dropped];
    }

    /**
     * Détecte les profils suspects de farming de check-in (source bar_visit :
     * passage en PDV SANS pronostic). Signaux : plusieurs PDV le même jour
     * (improbable physiquement), beaucoup de PDV différents, volume de check-ins.
     *
     * @return array<int, array{user_id:int, name:string, phone:string, checkins:int, distinct_bars:int, max_bars_day:int, reasons:array<int,string>}>
     */
    public function detectFraudPatterns(int $limit = 40): array
    {
        $rows = PointLog::query()
            ->where('source', 'bar_visit')
            ->get(['user_id', 'bar_id', 'created_at']);

        if ($rows->isEmpty()) {
            return [];
        }

        $byUser = [];
        foreach ($rows as $r) {
            $u = (int) $r->user_id;
            $day = Carbon::parse($r->created_at)->toDateString();
            $byUser[$u]['checkins'] = ($byUser[$u]['checkins'] ?? 0) + 1;
            $byUser[$u]['bars'][$r->bar_id] = true;
            $byUser[$u]['perDay'][$day][$r->bar_id] = true;
        }

        $out = [];
        foreach ($byUser as $uid => $d) {
            $checkins = $d['checkins'];
            $distinctBars = count($d['bars']);
            $maxBarsDay = 0;
            foreach ($d['perDay'] as $bars) {
                $maxBarsDay = max($maxBarsDay, count($bars));
            }

            $reasons = [];
            if ($maxBarsDay >= 3) {
                $reasons[] = "{$maxBarsDay} PDV différents le même jour";
            }
            if ($distinctBars >= 4) {
                $reasons[] = "{$distinctBars} PDV différents au total";
            }
            if ($checkins >= 8) {
                $reasons[] = "{$checkins} check-ins";
            }
            if (empty($reasons)) {
                continue;
            }

            $out[] = [
                'user_id'       => $uid,
                'checkins'      => $checkins,
                'distinct_bars' => $distinctBars,
                'max_bars_day'  => $maxBarsDay,
                'reasons'       => $reasons,
            ];
        }

        usort($out, fn ($a, $b) => [$b['max_bars_day'], $b['checkins']] <=> [$a['max_bars_day'], $a['checkins']]);
        $out = array_slice($out, 0, $limit);

        $users = User::whereIn('id', array_column($out, 'user_id'))
            ->get(['id', 'name', 'phone'])->keyBy('id');
        foreach ($out as &$o) {
            $o['name'] = $users[$o['user_id']]->name ?? '—';
            $o['phone'] = $users[$o['user_id']]->phone ?? '';
        }
        unset($o);

        return $out;
    }

    /**
     * Calcule le plan d'APPLICATION d'un scénario : par utilisateur, le nombre
     * de points de bonus POS à retirer de points_total.
     *  - Scénario A : retire TOUS les bonus POS.
     *  - Scénario B : retire seulement les bonus POS écartés (non légitimes).
     * Lecture seule (aucune écriture) — sert de base à la commande d'application.
     *
     * @return array{scenario:string, rows:array<int,array>, totals:array}
     */
    public function buildApplyPlan(string $scenario): array
    {
        $scenario = strtoupper($scenario);

        // Total des bonus POS par utilisateur (venue_visit + bar_visit).
        $allPos = PointLog::whereIn('source', self::POS_SOURCES)
            ->where('points', '>', 0)
            ->selectRaw('user_id, SUM(points) AS pts')
            ->groupBy('user_id')->get()
            ->mapWithKeys(fn ($r) => [(int) $r->user_id => (int) $r->pts]);

        // Bonus POS conservés selon le scénario.
        $kept = $scenario === self::SCENARIO_A
            ? collect()
            : $this->posPointsScenarioB()[0];

        $users = User::whereIn('id', $allPos->keys())
            ->get(['id', 'name', 'phone', 'points_total'])->keyBy('id');

        $rows = [];
        $totalRemoved = 0;
        foreach ($allPos as $uid => $posPts) {
            $keptPts = (int) ($kept[$uid]['points'] ?? 0);
            $removed = $posPts - $keptPts;
            if ($removed <= 0) {
                continue;
            }
            $u = $users->get($uid);
            $current = (int) ($u->points_total ?? 0);
            $rows[] = [
                'user_id'      => $uid,
                'name'         => $u->name ?? '—',
                'phone'        => $u->phone ?? '',
                'pos_current'  => $posPts,
                'pos_kept'     => $keptPts,
                'pos_removed'  => $removed,
                'total_before' => $current,
                'total_after'  => max(0, $current - $removed),
            ];
            $totalRemoved += $removed;
        }
        usort($rows, fn ($a, $b) => $b['pos_removed'] <=> $a['pos_removed']);

        return [
            'scenario' => $scenario,
            'rows'     => $rows,
            'totals'   => ['users' => count($rows), 'points_removed' => $totalRemoved],
        ];
    }

    /**
     * Exporte un résultat build() en CSV (séparateur ';' + BOM UTF-8 pour Excel FR).
     */
    public function toCsv(array $result): string
    {
        $sep = ';';
        $lines = [];
        $lines[] = implode($sep, [
            'rang', 'user_id', 'nom', 'telephone',
            'points_pronostic', 'points_connexion', 'points_visite_pdv', 'bonus_visite_pdv_retenus', 'total',
        ]);
        foreach ($result['rows'] as $r) {
            $lines[] = implode($sep, [
                $r['rang'],
                $r['user_id'],
                $this->csvCell($r['name'], $sep),
                $this->csvCell($r['phone'], $sep),
                $r['points_pronostics'],
                $r['points_connexion'],
                $r['points_pos'],
                $r['bonus_pos_retenus'],
                $r['total'],
            ]);
        }

        return "\xEF\xBB\xBF" . implode("\r\n", $lines) . "\r\n";
    }

    /**
     * Bloc HTML décrivant pas à pas l'algorithme de calcul du scénario,
     * pour que le PDF généré soit auto-explicatif.
     */
    private function algorithmSection(string $scenario): string
    {
        if (strtoupper($scenario) === self::SCENARIO_A) {
            $steps = [
                'Base par utilisateur = somme des points de <strong>pronostics</strong> (participation, bon vainqueur, score exact) <strong>+ connexions quotidiennes</strong>, lue dans le journal point_logs.',
                'Tous les <strong>bonus point de vente</strong> (sources <code>venue_visit</code> et <code>bar_visit</code>) sont <strong>ignorés / remis à zéro</strong>, sans condition.',
                '<strong>Total = base.</strong> Aucun bonus POS n\'entre dans le calcul.',
                'Classement trié par total décroissant (égalité départagée par le nom).',
            ];
        } else {
            $steps = [
                'Base par utilisateur = points de <strong>pronostics + connexions quotidiennes</strong> (identique au scénario A).',
                'Chaque <strong>bonus POS</strong> du journal (point_logs) est filtré :'
                    . '<ul>'
                    . '<li><code>venue_visit</code> (pronostic réellement fait en PDV) : <strong>retenu</strong>.</li>'
                    . '<li><code>bar_visit</code> (check-in) : <strong>retenu seulement si</strong> un pronostic du même utilisateur a été effectué le <strong>même jour, à/après</strong> l\'heure du check-in. Sinon retiré.</li>'
                    . '</ul>',
                'Plafond : <strong>un seul check-in par point de vente et par jour</strong> (un seul +4 par couple utilisateur / PDV / jour).',
                '<strong>Total = base + bonus POS retenus.</strong> Classement trié par total décroissant (égalité départagée par le nom).',
            ];
        }

        $lis = implode('', array_map(fn ($s) => "<li>{$s}</li>", $steps));

        return '<div class="algo"><h2>Algorithme de calcul</h2><ol>' . $lis . '</ol>'
            . '<p style="margin:8px 0 0 -12px;color:#777;">Calcul en lecture seule (dry-run) : aucune donnée n\'est modifiée en base. '
            . 'Sources de points retenues pour la base : pronostics (participation, bon vainqueur, score exact) et connexions quotidiennes.</p></div>';
    }

    private function csvCell(?string $value, string $sep): string
    {
        $value = (string) $value;
        if (str_contains($value, $sep) || str_contains($value, '"') || str_contains($value, "\n")) {
            return '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }

    /**
     * Exporte un résultat build() en HTML imprimable (Ctrl+P → PDF).
     */
    public function toHtml(array $result): string
    {
        $t = $result['totals'];
        $date = \Carbon\Carbon::now()->format('d/m/Y H:i');
        $rowsHtml = '';
        foreach ($result['rows'] as $r) {
            $rowsHtml .= '<tr>'
                . '<td class="r">' . $r['rang'] . '</td>'
                . '<td class="r">' . $r['user_id'] . '</td>'
                . '<td>' . e($r['name']) . '</td>'
                . '<td>' . e($r['phone']) . '</td>'
                . '<td class="r">' . $r['points_pronostics'] . '</td>'
                . '<td class="r">' . $r['points_connexion'] . '</td>'
                . '<td class="r">' . $r['points_pos'] . '</td>'
                . '<td class="r b">' . $r['total'] . '</td>'
                . '</tr>';
        }

        $label = e($result['label']);
        $rule = e($result['rule']);
        $algo = $this->algorithmSection($result['scenario']);

        return <<<HTML
<!doctype html>
<html lang="fr"><head><meta charset="utf-8">
<title>{$label}</title>
<style>
  * { box-sizing: border-box; }
  body { font-family: 'Segoe UI', Arial, sans-serif; color: #1a1a1a; margin: 32px; }
  h1 { font-size: 20px; margin: 0 0 4px; }
  .sub { color: #555; font-size: 12px; margin-bottom: 16px; }
  .rule { background: #fff7ed; border-left: 4px solid #f1862d; padding: 10px 14px; font-size: 13px; margin-bottom: 14px; }
  .algo { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 16px 12px 28px; font-size: 12.5px; margin-bottom: 18px; }
  .algo h2 { font-size: 13px; margin: 0 0 8px -12px; color: #1d4ed8; }
  .algo li { margin-bottom: 6px; line-height: 1.45; }
  .algo ul { margin: 4px 0 4px 0; }
  .totals { display: flex; gap: 24px; flex-wrap: wrap; margin-bottom: 18px; font-size: 13px; }
  .totals div { background: #f3f4f6; border-radius: 8px; padding: 8px 14px; }
  .totals b { display: block; font-size: 18px; color: #1d4ed8; }
  table { width: 100%; border-collapse: collapse; font-size: 12px; }
  th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
  th { background: #1d4ed8; color: #fff; }
  td.r { text-align: right; }
  td.b { font-weight: 700; }
  tr:nth-child(even) td { background: #fafafa; }
  .foot { margin-top: 16px; font-size: 11px; color: #777; }
  @media print { body { margin: 12px; } th { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
</style></head><body>
<h1>{$label}</h1>
<div class="sub">SOBOA FOOT TIME — Classement simulé (DRY-RUN, lecture seule) · généré le {$date}</div>
<div class="rule"><strong>Règle appliquée :</strong> {$rule}</div>
{$algo}
<div class="totals">
  <div>Utilisateurs classés<b>{$t['utilisateurs']}</b></div>
  <div>Bonus visite PDV retenus<b>{$t['bonus_pos_retenus']}</b></div>
  <div>Bonus visite PDV écartés<b>{$t['bonus_pos_ecartes']}</b></div>
  <div>Points visite PDV distribués<b>{$t['points_pos_total']}</b></div>
</div>
<table>
  <thead><tr>
    <th>Rang</th><th>ID</th><th>Nom</th><th>Téléphone</th>
    <th>Points pronostic</th><th>Points connexion</th><th>Points visite PDV</th><th>TOTAL</th>
  </tr></thead>
  <tbody>{$rowsHtml}</tbody>
</table>
<div class="foot">Document de travail pour décision finale. Aucune donnée n'a été modifiée en base lors de la génération.</div>
</body></html>
HTML;
    }
}
