<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Élargit teams.iso_code : varchar(2) -> varchar(10).
     * Certaines équipes WC utilisent des codes flagcdn composés
     * (ex: gb-sct pour l'Écosse, gb-wls pour le Pays de Galles).
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('iso_code', 10)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('iso_code', 2)->nullable()->change();
        });
    }
};
