<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            // Pour les phases à élimination: prévoir égalité + tirs au but
            $table->boolean('predict_draw')->default(false)->after('score_b')
                ->comment('True si l\'utilisateur prédit une égalité (temps réglementaire)');
            
            $table->enum('penalty_winner', ['home', 'away'])->nullable()->after('predict_draw')
                ->comment('Vainqueur aux tirs au but si égalité prédite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->dropColumn(['predict_draw', 'penalty_winner']);
        });
    }
};
