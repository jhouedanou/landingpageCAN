<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Stocke le mot de passe généré automatiquement sous forme chiffrée
     * réversible (via APP_KEY) afin de pouvoir l'afficher une seule fois
     * dans l'espace personnel de l'utilisateur. Le mot de passe d'authentification
     * reste hashé (bcrypt) dans la colonne `password`.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('password_encrypted')->nullable()->after('otp_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password_encrypted');
        });
    }
};
