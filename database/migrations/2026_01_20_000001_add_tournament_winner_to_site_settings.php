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
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('tournament_ended')->default(false)->after('geofencing_radius');
            $table->foreignId('tournament_winner_team_id')->nullable()->after('tournament_ended')
                  ->constrained('teams')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropForeign(['tournament_winner_team_id']);
            $table->dropColumn(['tournament_ended', 'tournament_winner_team_id']);
        });
    }
};
