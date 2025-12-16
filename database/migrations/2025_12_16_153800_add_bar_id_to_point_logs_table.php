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
        Schema::table('point_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('bar_id')->nullable()->after('match_id');
            $table->foreign('bar_id')->references('id')->on('bars')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('point_logs', function (Blueprint $table) {
            $table->dropForeign(['bar_id']);
            $table->dropColumn('bar_id');
        });
    }
};
