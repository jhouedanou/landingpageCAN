<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fiche points — {{ $user->name }}</title>
    <style>
        :root { --soboa-orange: #f7951e; --soboa-blue: #003a70; }
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1f2937; margin: 0; background: #f3f4f6;
        }
        .sheet { max-width: 900px; margin: 24px auto; background: #fff; padding: 32px 40px; box-shadow: 0 10px 30px rgba(0,0,0,.08); }
        .toolbar { max-width: 900px; margin: 16px auto 0; display: flex; gap: 12px; justify-content: space-between; padding: 0 8px; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; border-radius: 8px; font-weight: 700; text-decoration: none; cursor: pointer; border: none; font-size: 14px; }
        .btn-print { background: var(--soboa-orange); color: #111; }
        .btn-back { background: #e5e7eb; color: #374151; }

        .letterhead { display: flex; align-items: center; gap: 16px; border-bottom: 3px solid var(--soboa-orange); padding-bottom: 16px; margin-bottom: 8px; }
        .letterhead img { height: 56px; width: auto; }
        .letterhead .org { font-size: 20px; font-weight: 800; color: var(--soboa-blue); line-height: 1.1; }
        .letterhead .sub { font-size: 12px; color: #6b7280; }
        .doc-title { text-align: right; margin-left: auto; }
        .doc-title h1 { font-size: 16px; margin: 0; color: var(--soboa-blue); text-transform: uppercase; letter-spacing: .5px; }
        .doc-title .meta { font-size: 11px; color: #6b7280; }

        .user-block { display: flex; justify-content: space-between; align-items: flex-end; margin: 20px 0; }
        .user-block .name { font-size: 22px; font-weight: 800; }
        .user-block .phone { color: #6b7280; font-size: 14px; }
        .total-badge { text-align: right; }
        .total-badge .val { font-size: 36px; font-weight: 900; color: var(--soboa-orange); line-height: 1; }
        .total-badge .lbl { font-size: 11px; text-transform: uppercase; color: #6b7280; letter-spacing: 1px; }

        .cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin: 16px 0 24px; }
        .card { background: #f9fafb; border: 1px solid #eef0f3; border-radius: 10px; padding: 12px; text-align: center; }
        .card .v { font-size: 20px; font-weight: 800; color: var(--soboa-blue); }
        .card .l { font-size: 11px; color: #6b7280; }

        h2 { font-size: 14px; text-transform: uppercase; letter-spacing: .5px; color: var(--soboa-blue); border-left: 4px solid var(--soboa-orange); padding-left: 8px; margin: 24px 0 10px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 8px 10px; border-bottom: 1px solid #eef0f3; text-align: left; }
        th { background: #f3f4f6; font-size: 11px; text-transform: uppercase; color: #6b7280; }
        td.num, th.num { text-align: center; }
        td.sub { text-align: right; font-weight: 800; color: var(--soboa-blue); }
        .pos { color: #15803d; font-weight: 700; }
        .muted { color: #9ca3af; }
        tfoot td { font-weight: 800; background: #fafafa; }
        .grand { margin-top: 20px; display: flex; justify-content: flex-end; align-items: baseline; gap: 12px; border-top: 2px solid var(--soboa-blue); padding-top: 12px; }
        .grand .lbl { font-size: 13px; color: #6b7280; text-transform: uppercase; }
        .grand .val { font-size: 28px; font-weight: 900; color: var(--soboa-orange); }
        .reconcile { font-size: 11px; color: #9ca3af; text-align: right; margin-top: 4px; }
        .signature { margin-top: 48px; display: flex; justify-content: space-between; font-size: 12px; color: #6b7280; }
        .signature .line { border-top: 1px solid #9ca3af; width: 220px; padding-top: 4px; text-align: center; }
        .footer-note { margin-top: 32px; font-size: 10px; color: #9ca3af; text-align: center; border-top: 1px solid #eef0f3; padding-top: 8px; }

        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { box-shadow: none; margin: 0; max-width: none; padding: 0; }
            @page { margin: 14mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="{{ route('admin.claim-verification') }}" class="btn btn-back">← Nouvelle recherche</a>
        <button type="button" class="btn btn-print" onclick="window.print()">🖨️ Imprimer / Enregistrer en PDF</button>
    </div>

    <div class="sheet">
        <div class="letterhead">
            <img src="{{ asset('images/soboa.png') }}" alt="SOBOA">
            <div>
                <div class="org">SOBOA</div>
                <div class="sub">{{ config('app.name') }} — Vérification de réclamation</div>
            </div>
            <div class="doc-title">
                <h1>Répartition des points</h1>
                <div class="meta">Éditée le {{ now()->locale('fr')->isoFormat('D MMMM YYYY [à] HH:mm') }}</div>
                @if($generatedBy)
                <div class="meta">Par : {{ $generatedBy->name }}</div>
                @endif
            </div>
        </div>

        <div class="user-block">
            <div>
                <div class="name">{{ $user->name }}</div>
                <div class="phone">{{ $user->phone }}</div>
            </div>
            <div class="total-badge">
                <div class="val">{{ $summary['points_total'] }}</div>
                <div class="lbl">Points au total</div>
            </div>
        </div>

        <div class="cards">
            <div class="card"><div class="v">{{ $summary['predictions_count'] }}</div><div class="l">Pronostics</div></div>
            <div class="card"><div class="v">{{ $summary['winner_count'] }}</div><div class="l">Bons vainqueurs</div></div>
            <div class="card"><div class="v">{{ $summary['exact_count'] }}</div><div class="l">Scores exacts</div></div>
            <div class="card"><div class="v">{{ $summary['other_points'] }}</div><div class="l">Points hors match</div></div>
        </div>

        <h2>Détail par match</h2>
        @if($matchRows->isEmpty())
            <p class="muted">Aucun pronostic enregistré pour cet utilisateur.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Match</th>
                    <th class="num">Résultat</th>
                    <th class="num">Pronostic</th>
                    <th class="num">Particip.</th>
                    <th class="num">Vainqueur</th>
                    <th class="num">Score exact</th>
                    <th class="num">PDV</th>
                    <th class="num">Sous-total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($matchRows as $row)
                    @php $m = $row['match']; $p = $row['prediction']; @endphp
                    <tr>
                        <td class="muted">{{ $m && $m->match_date ? \Carbon\Carbon::parse($m->match_date)->format('d/m/Y') : '—' }}</td>
                        <td>{{ ($m->homeTeam->name ?? $m->team_a ?? '?') }} vs {{ ($m->awayTeam->name ?? $m->team_b ?? '?') }}</td>
                        <td class="num">
                            @if($m && $m->status === 'finished' && $m->score_a !== null)
                                {{ $m->score_a }} - {{ $m->score_b }}
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                        <td class="num">
                            @if($p)
                                {{ $p->score_a ?? '-' }} - {{ $p->score_b ?? '-' }}
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                        <td class="num">{!! $row['participation'] ? '<span class="pos">+'.$row['participation'].'</span>' : '<span class="muted">0</span>' !!}</td>
                        <td class="num">{!! $row['winner'] ? '<span class="pos">+'.$row['winner'].'</span>' : '<span class="muted">0</span>' !!}</td>
                        <td class="num">{!! $row['exact'] ? '<span class="pos">+'.$row['exact'].'</span>' : '<span class="muted">0</span>' !!}</td>
                        <td class="num">{!! $row['venue'] ? '<span class="pos">+'.$row['venue'].'</span>' : '<span class="muted">0</span>' !!}</td>
                        <td class="sub">{{ $row['subtotal'] }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">Total points liés aux matchs</td>
                    <td class="sub">{{ $summary['match_points'] }}</td>
                </tr>
            </tfoot>
        </table>
        @endif

        @if($otherRows->isNotEmpty())
        <h2>Points hors match</h2>
        <table>
            <thead>
                <tr><th>Source</th><th class="num">Occurrences</th><th class="num">Points</th></tr>
            </thead>
            <tbody>
                @foreach($otherRows as $o)
                <tr>
                    <td>{{ $o['label'] }}</td>
                    <td class="num">{{ $o['count'] }}</td>
                    <td class="num"><span class="pos">+{{ $o['points'] }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="grand">
            <span class="lbl">Total général</span>
            <span class="val">{{ $summary['points_total'] }} pts</span>
        </div>
        @if($summary['logs_total'] !== $summary['points_total'])
        <div class="reconcile">
            Somme des journaux de points : {{ $summary['logs_total'] }} pts
            (écart de {{ $summary['points_total'] - $summary['logs_total'] }} avec le total affiché).
        </div>
        @endif

        <div class="signature">
            <div class="line">Signature de l'agent</div>
            <div class="line">Signature de l'utilisateur</div>
        </div>

        <div class="footer-note">
            Document généré automatiquement par {{ config('app.name') }} — à des fins de vérification de réclamation.
        </div>
    </div>
</body>
</html>
