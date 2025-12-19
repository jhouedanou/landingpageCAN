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
        Schema::table('bars', function (Blueprint $table) {
            // Type de PDV (4 catégories)
            $table->enum('type_pdv', ['dakar', 'regions', 'chr', 'fanzone'])
                ->default('dakar')
                ->after('is_active')
                ->comment('Type de point de vente: dakar, regions, chr (Cafés-Hôtel-Restaurants), fanzone');

            // Index pour améliorer les performances de filtrage
            $table->index('type_pdv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bars', function (Blueprint $table) {
            $table->dropIndex(['type_pdv']);
            $table->dropColumn('type_pdv');
        });
    }
};
