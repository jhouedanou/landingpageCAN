<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table check_ins : PREUVE horodatée et géolocalisée de chaque présence.
 *
 * Jusqu'ici un check-in géolocalisé n'était PAS persisté (cf. mémo) : seules les
 * lignes point_logs (venue_visit / bar_visit) en gardaient une trace indirecte,
 * sans coordonnées ni précision GPS. En cas de contestation, impossible de
 * prouver où et quand l'utilisateur était présent.
 *
 * Cette table conserve, pour chaque détection de proximité PDV (check-in explicite
 * ou pronostic fait sur place), les coordonnées exactes du téléphone, la précision
 * GPS annoncée, la distance au PDV, le pronostic associé éventuel et l'empreinte
 * réseau (IP + user-agent). Elle alimente :
 *  - la preuve en cas de réclamation (A1) ;
 *  - la détection de fraude : vitesse impossible (B1), coordonnées identiques entre
 *    comptes (B4), multi-comptes même IP/appareil (B3).
 *
 * Les lignes sont conservées même si le PDV ou le pronostic est supprimé
 * (nullOnDelete) : la preuve de présence survit aux suppressions.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('bar_id')->nullable()->constrained('bars')->nullOnDelete();
            $table->foreignId('prediction_id')->nullable()->constrained('predictions')->nullOnDelete();

            // Coordonnées du téléphone au moment du check-in (mêmes précisions que bars).
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            // Précision GPS annoncée par le navigateur (mètres) — un rayon énorme
            // trahit souvent une position falsifiée / IP-based.
            $table->decimal('gps_accuracy', 8, 2)->nullable();

            // Distance calculée (serveur, Haversine) entre le téléphone et le PDV (mètres).
            $table->unsignedInteger('distance_m')->nullable();

            // Origine de la preuve : 'checkin' = /api/check-in ; 'prediction' = pronostic
            // soumis sur place (présence revalidée côté serveur).
            $table->enum('source', ['checkin', 'prediction'])->default('checkin');

            // Empreinte réseau pour le recoupement multi-comptes (B3).
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('bar_id');
            $table->index('ip_address');
            // Recherche des coordonnées identiques entre comptes (B4).
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};
