<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table pour stocker les classements hebdomadaires de la CAN
     * - Semaine 1: 21-27 janvier 2025
     * - Semaine 2: 28 janvier - 3 février 2025
     * - Semaine 3: 4-10 février 2025
     * - Semaine 4: 11-17 février 2025 (jusqu'aux demi-finales)
     * - Demi-finale: Classement global recalculé après les demi-finales
     */
    public function up(): void
    {
        Schema::create('weekly_rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('period'); // 'week_1', 'week_2', 'week_3', 'week_4', 'semifinal'
            $table->integer('points')->default(0);
            $table->integer('rank')->nullable();
            $table->boolean('is_winner')->default(false); // Parmi les 5 gagnants de la semaine
            $table->timestamps();

            $table->unique(['user_id', 'period']);
            $table->index(['period', 'points']);
            $table->index(['period', 'rank']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_rankings');
    }
};
