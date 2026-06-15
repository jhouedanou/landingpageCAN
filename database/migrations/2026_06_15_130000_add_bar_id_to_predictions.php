<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute predictions.bar_id (facultatif) : le PDV depuis lequel le pronostic a
 * été soumis, quand la présence sur place a été vérifiée (géoloc serveur).
 * NULL = pronostic hors PDV ou présence non vérifiée (cas par défaut).
 *
 * Permet de relier précisément un pronostic à un point de vente, donc de juger
 * la pertinence d'un bonus POS (scénario B) au niveau (utilisateur, PDV, jour)
 * et non plus seulement (utilisateur, jour).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->foreignId('bar_id')
                ->nullable()
                ->after('match_id')
                ->constrained('bars')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bar_id');
        });
    }
};
