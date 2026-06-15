<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

/**
 * Journal d'actions admin (A4). Une ligne = une action sensible exécutée dans
 * l'espace admin (ou en CLI). Sert de preuve et de contrôle interne.
 *
 * Usage typique :
 *   AdminAuditLog::record('user.reset_points', "Points remis à zéro", $user, [
 *       'previous_points' => 42, 'logs_deleted' => 10,
 *   ]);
 */
class AdminAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'admin_name',
        'action',
        'target_type',
        'target_id',
        'description',
        'meta',
        'ip_address',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Enregistre une action admin. Résout automatiquement l'administrateur courant
     * via la session ; si aucune session (commande artisan / cron), trace une action
     * système (admin_id NULL, nom « CLI / système »).
     *
     * @param string $action      Code court, ex. 'user.reset_points'.
     * @param string $description Résumé lisible de l'action.
     * @param Model|null $target  Entité concernée (User, Prediction…), facultatif.
     * @param array $meta         Détails structurés (montants, totaux avant/après…).
     */
    public static function record(string $action, string $description, ?Model $target = null, array $meta = []): self
    {
        $adminId = null;
        $adminName = 'CLI / système';
        $ip = null;

        // session() et request() ne sont pas disponibles hors contexte HTTP (CLI).
        if (app()->bound('session') && session('user_id')) {
            $adminId = session('user_id');
            $admin = User::find($adminId);
            $adminName = $admin?->name ?? ('Admin #' . $adminId);
        }

        if (app()->runningInConsole() === false) {
            try {
                $ip = Request::ip();
            } catch (\Throwable $e) {
                $ip = null;
            }
        }

        return self::create([
            'admin_id' => $adminId,
            'admin_name' => $adminName,
            'action' => $action,
            'target_type' => $target ? class_basename($target) : null,
            'target_id' => $target?->getKey(),
            'description' => $description,
            'meta' => $meta ?: null,
            'ip_address' => $ip,
        ]);
    }
}
