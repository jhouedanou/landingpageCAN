<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Journal d'actions admin (A4) : trace immuable de QUI fait QUOI, QUAND.
 *
 * Enregistre chaque action sensible de l'espace admin (réinitialisation de points,
 * suppression d'utilisateur/pronostic, application d'un scénario de classement,
 * retrait de bonus, etc.). Sert de preuve en cas de contestation et de contrôle
 * interne.
 *
 *  - admin_id   : l'administrateur (NULL si action CLI / système) ;
 *  - admin_name : nom figé au moment de l'action (survit à la suppression du compte) ;
 *  - action     : code court de l'action (ex. 'user.reset_points') ;
 *  - target_*   : entité visée (type + id), facultatif ;
 *  - description: résumé lisible ;
 *  - meta       : détails structurés (montants, anciens/nouveaux totaux…) ;
 *  - ip_address : IP de l'admin.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('admin_name')->nullable();

            $table->string('action', 64);
            $table->string('target_type', 64)->nullable();
            $table->unsignedBigInteger('target_id')->nullable();

            $table->text('description')->nullable();
            $table->json('meta')->nullable();

            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            $table->index('admin_id');
            $table->index('action');
            $table->index('created_at');
            $table->index(['target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_audit_logs');
    }
};
