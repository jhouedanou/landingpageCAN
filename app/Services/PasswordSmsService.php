<?php

namespace App\Services;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Envoi par SMS (Twilio) du code personnel d'un utilisateur.
 *
 * Chaque tentative est tracée dans sms_logs (context "password",
 * status sent/failed) : c'est ce qui permet à la commande
 * users:send-password-sms de retrouver et relancer les envois échoués.
 *
 * Utilisé par AuthController::resetPassword et par la commande de renvoi.
 */
class PasswordSmsService
{
    /**
     * @return array{success: bool, sid?: string|null, error?: string}
     */
    public function send(string $phone, string $code, string $userName, string $context = 'password'): array
    {
        $accountSid = config('services.twilio.account_sid');
        $authToken = config('services.twilio.auth_token');
        $fromNumber = config('services.twilio.from_number');

        $toNumber = '+' . ltrim($phone, '+');
        $message = "SOBOA FOOT TIME - Bonjour {$userName}! Votre nouveau code personnel est: {$code}. Conservez-le precieusement pour vos connexions futures.";

        if (!$accountSid || !$authToken || !$fromNumber) {
            Log::error('PasswordSmsService: configuration Twilio incomplète');
            $this->logAttempt($toNumber, $message, 'failed', null, 'Configuration Twilio incomplete', $context);
            return ['success' => false, 'error' => 'Configuration Twilio incomplete'];
        }

        try {
            $url = "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json";

            $response = Http::withBasicAuth($accountSid, $authToken)
                ->asForm()
                ->timeout(30)
                ->post($url, [
                    'To' => $toNumber,
                    'From' => $fromNumber,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                $sid = $response->json()['sid'] ?? null;
                Log::info('PasswordSmsService: SMS envoyé', ['to' => $toNumber, 'sid' => $sid]);
                $this->logAttempt($toNumber, $message, 'sent', $sid, null, $context);
                return ['success' => true, 'sid' => $sid];
            }

            $errorMessage = $response->json()['message'] ?? ('HTTP ' . $response->status());
            Log::error('PasswordSmsService: échec Twilio', ['to' => $toNumber, 'error' => $errorMessage]);
            $this->logAttempt($toNumber, $message, 'failed', null, $errorMessage, $context);
            return ['success' => false, 'error' => $errorMessage];
        } catch (\Throwable $e) {
            Log::error('PasswordSmsService: exception Twilio', ['to' => $toNumber, 'message' => $e->getMessage()]);
            $this->logAttempt($toNumber, $message, 'failed', null, $e->getMessage(), $context);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Échecs d'envoi de code personnel encore en attente : dernier log
     * "failed" par numéro, sans envoi réussi postérieur pour ce numéro.
     * Couvre aussi les anciens logs sans context via le texte du message.
     */
    public function pendingFailures()
    {
        $passwordScope = function ($q) {
            $q->where('context', 'like', 'password%')
              ->orWhere('message', 'like', '%code personnel%')
              ->orWhere('message', 'like', '%mot de passe%');
        };

        return SmsLog::where('status', 'failed')
            ->where($passwordScope)
            ->orderByDesc('id')
            ->get()
            ->unique('to_number')
            ->reject(function ($log) use ($passwordScope) {
                // Comparaison par id (monotone) plutôt que created_at :
                // un échec et sa relance dans la même seconde resteraient
                // sinon indistinguables.
                return SmsLog::where('to_number', $log->to_number)
                    ->where('status', 'sent')
                    ->where($passwordScope)
                    ->where('id', '>', $log->id)
                    ->exists();
            })
            ->values();
    }

    /**
     * Relance l'envoi du code personnel pour tous les échecs en attente.
     * Le code envoyé est celui actuellement actif (password_encrypted de
     * l'utilisateur) — jamais de régénération silencieuse.
     *
     * @return array{resent: string[], failed: string[], skipped: string[]}
     */
    public function resendFailures(bool $dryRun = false): array
    {
        $resent = [];
        $failed = [];
        $skipped = [];

        foreach ($this->pendingFailures() as $log) {
            $user = \App\Models\User::where('phone', $log->to_number)->first();

            if (!$user) {
                $skipped[] = "{$log->to_number} : aucun compte associé";
                continue;
            }
            if (!$user->plain_password) {
                $skipped[] = "{$log->to_number} ({$user->name}) : code non récupérable — utiliser « Code oublié »";
                continue;
            }

            if ($dryRun) {
                $resent[] = "{$log->to_number} ({$user->name}) [dry-run]";
                continue;
            }

            $result = $this->send($user->phone, $user->plain_password, $user->name, 'password_resend');
            if ($result['success']) {
                $resent[] = "{$log->to_number} ({$user->name})";
            } else {
                $failed[] = "{$log->to_number} ({$user->name}) : " . ($result['error'] ?? 'erreur inconnue');
            }
        }

        return compact('resent', 'failed', 'skipped');
    }

    private function logAttempt(string $to, string $message, string $status, ?string $sid, ?string $error, string $context): void
    {
        try {
            SmsLog::create([
                'to_number' => $to,
                // Ne jamais stocker le code en clair dans les logs SMS.
                'message' => preg_replace('/code personnel est: \S+/', 'code personnel est: ******', $message),
                'status' => $status,
                'twilio_sid' => $sid,
                'error' => $error,
                'context' => $context,
            ]);
        } catch (\Throwable $e) {
            Log::warning('PasswordSmsService: échec écriture sms_logs', ['message' => $e->getMessage()]);
        }
    }
}
