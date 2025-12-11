<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add firebase_uid and role to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['user', 'admin'])->default('user')->after('points_total');
            }
        });
        
        // Update point_logs source enum to include all new reasons
        DB::statement("ALTER TABLE point_logs MODIFY COLUMN source ENUM('login', 'prediction', 'accuracy', 'venue_visit', 'bar_visit', 'login_daily', 'prediction_participation', 'prediction_winner', 'prediction_exact') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
