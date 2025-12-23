<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cette migration s'assure que la table admin_otp_logs existe
     */
    public function up(): void
    {
        // Créer la table seulement si elle n'existe pas
        if (!Schema::hasTable('admin_otp_logs')) {
            Schema::create('admin_otp_logs', function (Blueprint $table) {
                $table->id();
                $table->string('phone');
                $table->string('code');
                $table->enum('status', ['sent', 'verified', 'expired', 'failed'])->default('sent');
                $table->string('whatsapp_number');
                $table->integer('verification_attempts')->default(0);
                $table->timestamp('otp_sent_at');
                $table->timestamp('otp_verified_at')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamps();
                $table->index('phone');
                $table->index('status');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne pas supprimer pour préserver les logs
    }
};
