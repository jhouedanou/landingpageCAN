<?php

namespace App\Console\Commands;

use App\Models\PointLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Retire les points de bonus obtenus par simple check-in (source 'bar_visit').
 *
 * RÈGLE MÉTIER 2026 : le +4 points en point de vente n'est légitime que s'il
 * est lié à un pronostic enregistré sur place (source 'venue_visit'). Le plafond
 * partagé (utilisateur, PDV, jour) garantit qu'un même PDV n'a jamais donné à la
 * fois un 'bar_visit' ET un 'venue_visit' le même jour : toute ligne 'bar_visit'
 * correspond donc à un bonus de check-in SANS pronostic = à retirer.
 *
 * Sécurité :
 *  - DRY-RUN par défaut : n'écrit rien, affiche seulement le rapport.
 *  - --apply : exécute le retrait dans une transaction.
 *  - Les lignes 'bar_visit' d'origine sont CONSERVÉES (audit). Le retrait se fait
 *    via une ligne négative 'adjustment', pour que points_total reste cohérent
 *    avec la somme des point_logs (commande points:audit).
 */
class RevokeCheckinBonus extends Command
{
    protected $signature = 'points:revoke-checkin-bonus {--apply : Exécute réellement le retrait (sinon simulation)}';

    protected $description = 'Retire les points de bonus obtenus par check-in sans pronostic (source bar_visit)';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');

        $this->info('🔎 Recherche des bonus de check-in sans pronostic (source = bar_visit)...');
        $this->newLine();

        // Agrégat par utilisateur : nombre de check-ins indus + points à retirer.
        $rows = PointLog::query()
            ->where('source', 'bar_visit')
            ->selectRaw('user_id, COUNT(*) AS nb, COALESCE(SUM(points),0) AS pts')
            ->groupBy('user_id')
            ->having('pts', '>', 0)
            ->get();

        if ($rows->isEmpty()) {
            $this->info('✅ Aucun point de check-in indu trouvé. Rien à faire.');
            return self::SUCCESS;
        }

        $users = User::whereIn('id', $rows->pluck('user_id'))->get()->keyBy('id');

        $table = [];
        $totalPts = 0;
        $totalCheckins = 0;
        foreach ($rows as $r) {
            $u = $users->get($r->user_id);
            $table[] = [
                $r->user_id,
                $u?->name ?? '—',
                $u?->phone ?? ($u?->email ?? '—'),
                $r->nb,
                $r->pts,
                $u?->points_total ?? '?',
                ($u ? max(0, $u->points_total - $r->pts) : '?'),
            ];
            $totalPts += (int) $r->pts;
            $totalCheckins += (int) $r->nb;
        }

        $this->table(
            ['User ID', 'Nom', 'Contact', 'Check-ins', 'Points retirés', 'Total avant', 'Total après'],
            $table
        );

        $this->newLine();
        $this->warn("👥 Utilisateurs concernés : {$rows->count()}");
        $this->warn("📍 Check-ins indus      : {$totalCheckins}");
        $this->warn("➖ Points à retirer      : {$totalPts}");
        $this->newLine();

        if (!$apply) {
            $this->info('💡 SIMULATION (dry-run) : aucune donnée modifiée.');
            $this->info('   Pour appliquer réellement : php artisan points:revoke-checkin-bonus --apply');
            return self::SUCCESS;
        }

        if (!$this->confirm("Confirmer le retrait de {$totalPts} points pour {$rows->count()} utilisateurs ?", false)) {
            $this->info('Annulé. Aucune donnée modifiée.');
            return self::SUCCESS;
        }

        $applied = 0;
        DB::transaction(function () use ($rows, $users, &$applied) {
            foreach ($rows as $r) {
                $u = $users->get($r->user_id);
                if (!$u) {
                    continue;
                }
                $pts = (int) $r->pts;

                // Ligne négative d'ajustement = trace auditable du retrait.
                PointLog::create([
                    'user_id' => $u->id,
                    'source'  => 'adjustment',
                    'points'  => -$pts,
                ]);

                // points_total décrémenté exactement du même montant (reste cohérent
                // avec la somme des logs). Plancher à 0 par sécurité.
                $newTotal = max(0, $u->points_total - $pts);
                $u->update(['points_total' => $newTotal]);
                $applied++;
            }
        });

        $this->newLine();
        $this->info("✅ Retrait appliqué pour {$applied} utilisateurs ({$totalPts} points).");
        $this->info('   Les lignes bar_visit d\'origine sont conservées pour l\'historique.');
        $this->info('   Vérifier la cohérence : php artisan points:audit');

        return self::SUCCESS;
    }
}
