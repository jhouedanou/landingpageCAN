<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Traçabilité des ajustements (A3) : qui a appliqué un retrait/scénario, et pourquoi.
 *
 * Ajoute à point_logs :
 *  - admin_id : l'administrateur ayant déclenché l'ajustement (NULL = action CLI /
 *    automatique, ex. commandes artisan ranking:apply-scenario, points:revoke-checkin-bonus) ;
 *  - note     : motif lisible de l'ajustement (« Scénario A appliqué », « Retrait
 *    bonus check-in indu », etc.).
 *
 * Concerne surtout les lignes source='adjustment' mais reste générique.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('point_logs', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('user_id')
                ->constrained('users')->nullOnDelete();
            $table->string('note', 255)->nullable()->after('source');
        });
    }

    public function down(): void
    {
        Schema::table('point_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_id');
            $table->dropColumn('note');
        });
    }
};
