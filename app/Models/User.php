<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'is_admin',
        'role',
        'points_total',
        'last_login_at',
        'last_daily_reward_at',
        'password',
        'otp_password', // Code OTP permanent (hashé) pour les connexions futures
        'password_encrypted', // Mot de passe généré, chiffré réversible pour affichage espace perso
        'otp_code',
        'otp_expires_at',
        'phone_verified',
        'firebase_uid',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'last_login_at' => 'datetime',
        'last_daily_reward_at' => 'date',
        'otp_expires_at' => 'datetime',
        'phone_verified' => 'boolean',
    ];

    protected $hidden = [
        'password',
        'otp_password',
        'password_encrypted',
        'remember_token',
    ];

    /**
     * Stocke le mot de passe en clair sous forme chiffrée réversible.
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->password_encrypted = Crypt::encryptString($plainPassword);
    }

    /**
     * Retourne le mot de passe généré en clair (déchiffré), ou null.
     */
    public function getPlainPasswordAttribute(): ?string
    {
        if (empty($this->password_encrypted)) {
            return null;
        }

        try {
            return Crypt::decryptString($this->password_encrypted);
        } catch (\Throwable $e) {
            Log::warning('Impossible de déchiffrer le mot de passe', ['user_id' => $this->id]);
            return null;
        }
    }

    /**
     * Relation avec les prédictions de l'utilisateur
     */
    public function predictions()
    {
        return $this->hasMany(Prediction::class);
    }

    /**
     * Relation avec les logs de points de l'utilisateur
     */
    public function pointLogs()
    {
        return $this->hasMany(PointLog::class);
    }

    /**
     * Route notifications for the WhatsApp channel.
     */
    public function routeNotificationForWhatsapp(): string
    {
        // Utiliser le WhatsAppService pour formater le numéro
        return app(\App\Services\WhatsAppService::class)->formatWhatsAppNumber($this->phone);
    }
}
