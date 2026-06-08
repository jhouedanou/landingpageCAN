<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ajoute la phase "round_of_32" (32es de finale) à l'enum des phases.
     * Nécessaire pour la Coupe du Monde 2026 (Football Fest 2026) qui inclut
     * un tour de 32es de finale absent de l'ancienne CAN.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE matches MODIFY COLUMN phase ENUM(
            'group_stage',
            'round_of_32',
            'round_of_16',
            'quarter_final',
            'semi_final',
            'third_place',
            'final'
        ) NOT NULL DEFAULT 'group_stage'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Repli des éventuels matchs en round_of_32 vers round_of_16 avant retrait de la valeur
        DB::statement("UPDATE matches SET phase = 'round_of_16' WHERE phase = 'round_of_32'");

        DB::statement("ALTER TABLE matches MODIFY COLUMN phase ENUM(
            'group_stage',
            'round_of_16',
            'quarter_final',
            'semi_final',
            'third_place',
            'final'
        ) NOT NULL DEFAULT 'group_stage'");
    }
};
