<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Cette migration ajoute une colonne pour stocker le code OTP
     * comme mot de passe permanent (hashé) pour les connexions futures.
     * Cela permet d'économiser les coûts Twilio en n'envoyant qu'un seul SMS
     * lors de la première inscription.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('otp_password')->nullable()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('otp_password');
        });
    }
};
