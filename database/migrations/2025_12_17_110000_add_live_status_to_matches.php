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
        // Modify the status column to include 'live'
        DB::statement("ALTER TABLE matches MODIFY COLUMN status ENUM('scheduled', 'live', 'finished') DEFAULT 'scheduled'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        // First, update any 'live' status to 'scheduled'
        DB::table('matches')->where('status', 'live')->update(['status' => 'scheduled']);

        // Then modify the enum
        DB::statement("ALTER TABLE matches MODIFY COLUMN status ENUM('scheduled', 'finished') DEFAULT 'scheduled'");
    }
};
