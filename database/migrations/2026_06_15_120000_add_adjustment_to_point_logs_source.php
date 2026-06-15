<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute la source 'adjustment' à point_logs.
 * Sert à tracer les corrections manuelles, notamment le retrait des points
 * de check-in indus (commande points:revoke-checkin-bonus) : on garde les
 * lignes 'bar_visit' d'origine pour l'historique et on ajoute une ligne
 * négative 'adjustment' afin que points_total reste égal à la somme des logs.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE point_logs MODIFY COLUMN source ENUM('login', 'prediction', 'accuracy', 'venue_visit', 'bar_visit', 'login_daily', 'prediction_participation', 'prediction_winner', 'prediction_exact', 'adjustment') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE point_logs MODIFY COLUMN source ENUM('login', 'prediction', 'accuracy', 'venue_visit', 'bar_visit', 'login_daily', 'prediction_participation', 'prediction_winner', 'prediction_exact') NOT NULL");
    }
};
