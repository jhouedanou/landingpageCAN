<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private string $baseUrl;
    private string $token;
    private string $senderId;

    public function __construct()
    {
        $this->baseUrl = config('services.sms_pro_africa.base_url');
        $this->token = config('services.sms_pro_africa.token');
        $this->senderId = config('services.sms_pro_africa.sender_id');
    }

    /**
     * Envoie un SMS via SMS Pro Africa
     *
     * @param string $recipient Numéro de téléphone du destinataire (format international sans +)
     * @param string $message Contenu du message
     * @return array ['success' => bool, 'data' => array|null, 'error' => string|null]
     */
    public function sendSms(string $recipient, string $message): array
    {
        Log::info('=== DEBUT SmsService::sendSms (SMS Pro Africa) ===');

        // Vérification de la configuration
        if (!$this->baseUrl || !$this->token || !$this->senderId) {
            Log::error('Configuration SMS Pro Africa incomplete !', [
                'base_url_set' => !empty($this->baseUrl),
                'token_set' => !empty($this->token),
                'sender_id_set' => !empty($this->senderId),
            ]);
            return [
                'success' => false,
                'data' => null,
                'error' => 'Configuration SMS Pro Africa incomplete',
            ];
        }

        // Formater le numéro (retirer le + si présent)
        $formattedRecipient = $this->formatPhoneNumber($recipient);

        Log::info('Envoi SMS via SMS Pro Africa', [
            'recipient_original' => $recipient,
            'recipient_formatted' => $formattedRecipient,
            'sender_id' => $this->senderId,
            'message_length' => strlen($message),
        ]);

        $url = rtrim($this->baseUrl, '/') . '/sms/send';

        $payload = [
            'recipient' => $formattedRecipient,
            'sender_id' => $this->senderId,
            'type' => 'plain',
            'message' => $message,
        ];

        try {
            Log::info('Envoi requete HTTP vers SMS Pro Africa...', [
                'url' => $url,
                'payload' => $payload,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->post($url, $payload);

            $responseData = $response->json();

            Log::info('Reponse SMS Pro Africa recue', [
                'status_code' => $response->status(),
                'body' => $responseData,
            ]);

            // Vérifier si la requête a réussi
            if ($response->successful() && isset($responseData['status']) && $responseData['status'] === 'success') {
                Log::info('=== SUCCES SMS Pro Africa ===', [
                    'data' => $responseData,
                ]);
                return [
                    'success' => true,
                    'data' => $responseData,
                    'error' => null,
                ];
            }

            // Erreur de l'API
            $errorMessage = $responseData['message'] ?? $responseData['error'] ?? ('HTTP ' . $response->status());
            Log::error('=== ECHEC SMS Pro Africa ===', [
                'status_code' => $response->status(),
                'error' => $errorMessage,
                'response' => $responseData,
            ]);

            return [
                'success' => false,
                'data' => $responseData,
                'error' => $errorMessage,
            ];

        } catch (\Exception $e) {
            Log::error('=== EXCEPTION SMS Pro Africa ===', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Formate le numéro de téléphone pour SMS Pro Africa
     * Retire le + et s'assure que le numéro commence par l'indicatif pays
     *
     * @param string $phone
     * @return string
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Retirer tous les caractères non numériques sauf le +
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // Retirer le + au début
        $phone = ltrim($phone, '+');

        return $phone;
    }

    /**
     * Envoie un code OTP par SMS
     *
     * @param string $recipient
     * @param string $otpCode
     * @return array
     */
    public function sendOtpSms(string $recipient, string $otpCode): array
    {
        $message = "SOBOA FOOT TIME - Votre code de verification: {$otpCode}";
        return $this->sendSms($recipient, $message);
    }

    /**
     * Envoie un SMS de bienvenue
     *
     * @param string $recipient
     * @param string $userName
     * @return array
     */
    public function sendWelcomeSms(string $recipient, string $userName): array
    {
        $message = "Bienvenue sur SOBOA FOOT TIME, {$userName}! Faites vos pronostics et gagnez des points. Bonne chance!";
        return $this->sendSms($recipient, $message);
    }
}
