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
        // Add team foreign keys to matches table
        Schema::table('matches', function (Blueprint $table) {
            $table->foreignId('home_team_id')->nullable()->after('id')->constrained('teams')->nullOnDelete();
            $table->foreignId('away_team_id')->nullable()->after('home_team_id')->constrained('teams')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['home_team_id']);
            $table->dropForeign(['away_team_id']);
            $table->dropColumn(['home_team_id', 'away_team_id']);
        });
    }
};
