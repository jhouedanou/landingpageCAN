<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Empreinte réseau sur les pronostics (B3) : IP + user-agent du client au moment
 * de la soumission. Permet de repérer un même appareil pilotant plusieurs comptes
 * (multi-comptes) en recoupant les pronostics par IP / user-agent.
 *
 * NULL pour les pronostics historiques (avant cette migration) : détection
 * forward-only, sans rétro-remplissage.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->after('penalty_winner');
            $table->string('user_agent', 512)->nullable()->after('ip_address');
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->dropIndex(['ip_address']);
            $table->dropColumn(['ip_address', 'user_agent']);
        });
    }
};
