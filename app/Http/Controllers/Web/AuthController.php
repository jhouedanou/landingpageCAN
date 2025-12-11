<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (session('user_id')) {
            return redirect('/matches');
        }
        return view('auth.login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'name' => 'required|string|max:255',
        ]);

        try {
            $originalPhone = $request->phone;
            $phone = $this->formatPhone($request->phone);
            $whatsappNumber = $this->formatWhatsAppNumber($phone);

            Log::info('=== ENVOI OTP WHATSAPP ===', [
                'original_phone' => $originalPhone,
                'formatted_phone' => $phone,
                'whatsapp_number' => $whatsappNumber,
                'name' => $request->name,
            ]);

            $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $cacheKey = 'otp_' . $whatsappNumber;
            Cache::put($cacheKey, [
                'code' => $otpCode,
                'name' => $request->name,
                'phone' => $phone,
                'attempts' => 0,
            ], now()->addMinutes(10));

            Log::info('Code OTP genere', ['whatsapp_number' => $whatsappNumber, 'code' => $otpCode]);

            $result = $this->sendWhatsAppMessage($whatsappNumber, $otpCode);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Code envoye sur WhatsApp !',
                    'whatsapp_number' => $whatsappNumber,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l envoi du message WhatsApp.',
                    'error' => $result['error'] ?? 'Erreur inconnue',
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Exception sendOtp', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur technique. Reessayez.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function sendWhatsAppMessage(string $whatsappNumber, string $otpCode): array
    {
        Log::info('=== DEBUT sendWhatsAppMessage ===');

        $idInstance = config('services.greenapi.id_instance');
        $apiToken = config('services.greenapi.api_token');
        $baseUrl = config('services.greenapi.url');

        Log::info('Configuration Green API', [
            'id_instance' => $idInstance,
            'api_token' => $apiToken ? substr($apiToken, 0, 15) . '...' : 'NULL',
            'base_url' => $baseUrl,
        ]);

        if (!$idInstance || !$apiToken || !$baseUrl) {
            Log::error('Configuration Green API incomplete !', [
                'id_instance_set' => !empty($idInstance),
                'api_token_set' => !empty($apiToken),
                'base_url_set' => !empty($baseUrl),
            ]);
            return ['success' => false, 'error' => 'Configuration Green API incomplete'];
        }

        $url = "{$baseUrl}/waInstance{$idInstance}/sendMessage/{$apiToken}";

        Log::info('URL Green API', ['url' => $url]);

        $message = "🏆 CAN 2025 - SOBOA\n\nVotre code de verification privilège :\n\n👉 ```{$otpCode}``` 👈\n\n_(Appuyez sur le code pour le copier)_";

        $payload = [
            'chatId' => $whatsappNumber . '@c.us',
            'message' => $message,
        ];

        Log::info('Payload WhatsApp', $payload);

        try {
            Log::info('Envoi requete HTTP vers Green API...');

            $response = Http::timeout(30)->post($url, $payload);

            Log::info('Reponse Green API recue', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('=== SUCCES WhatsApp ===', ['data' => $data]);
                return ['success' => true, 'idMessage' => $data['idMessage'] ?? null];
            } else {
                Log::error('=== ECHEC WhatsApp ===', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return ['success' => false, 'error' => 'HTTP ' . $response->status() . ': ' . $response->body()];
            }
        } catch (\Exception $e) {
            Log::error('=== EXCEPTION WhatsApp ===', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        try {
            $phone = $this->formatPhone($request->phone);
            $whatsappNumber = $this->formatWhatsAppNumber($phone);

            $cacheKey = 'otp_' . $whatsappNumber;
            $otpData = Cache::get($cacheKey);

            if (!$otpData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code expire. Veuillez renvoyer un nouveau code.',
                ], 400);
            }

            if ($otpData['attempts'] >= 5) {
                Cache::forget($cacheKey);
                return response()->json([
                    'success' => false,
                    'message' => 'Trop de tentatives. Veuillez renvoyer un nouveau code.',
                ], 400);
            }

            $otpData['attempts']++;
            Cache::put($cacheKey, $otpData, now()->addMinutes(10));

            if ($otpData['code'] !== $request->code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code incorrect. ' . (5 - $otpData['attempts']) . ' tentative(s) restante(s).',
                ], 400);
            }

            Cache::forget($cacheKey);

            $user = User::where('phone', $phone)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $otpData['name'],
                    'phone' => $phone,
                    'password' => Hash::make(Str::random(32)),
                ]);
            } else {
                if ($user->name !== $otpData['name']) {
                    $user->update(['name' => $otpData['name']]);
                }
            }

            session(['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Connexion reussie !',
                'redirect' => '/matches',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur technique. Reessayez.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^\d+]/', '', $phone);

        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        if (str_starts_with($phone, '00')) {
            return '+' . substr($phone, 2);
        }

        if (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            return '+225' . $phone;
        }

        if (strlen($phone) === 9 && str_starts_with($phone, '7')) {
            return '+221' . $phone;
        }

        return '+225' . $phone;
    }

    private function formatWhatsAppNumber(string $phone): string
    {
        // Retirer le +
        $number = ltrim($phone, '+');

        // Validation et Formatage stricts

        // CÔTE D'IVOIRE (+225)
        // Doit avoir 13 chiffres au total : 225 + 10 chiffres (ex: 07xxxxxxxx)
        if (str_starts_with($number, '225')) {
            if (strlen($number) !== 13) {
                throw new \Exception("Numéro CI invalide. Le numéro doit comporter 10 chiffres après l'indicatif (+225).");
            }
            // Conversion au format 8 chiffres pour la CI (demande specifique)
            // On retire les 2 premiers chiffres du numéro local (index 3 et 4)
            // Ex: 225 07 48 34 82 21 -> 225 48 34 82 21
            // 225 (0,1,2) + local (3...12) -> on garde 225 et on prend à partir de l'index 5 (le 6ème caractère)
            // ATTENTION: substr est 0-indexed.
            // 2 2 5 0 7 4 8 3 4 8 2 2 1
            // 0 1 2 3 4 5 6 7 8 9 0 1 2
            // On veut garder '225' + '48348221' (à partir de l'index 5)

            $prefixCurrent = substr($number, 0, 3); // 225
            $suffix8 = substr($number, 5); // les 8 derniers chiffres

            $formatted = $prefixCurrent . $suffix8;

            Log::info('Conversion CI 8 chiffres', ['original' => $number, 'converted' => $formatted]);

            return $formatted;
        }

        // SÉNÉGAL (+221)
        // Doit avoir 12 chiffres au total : 221 + 9 chiffres (ex: 77xxxxxxx)
        if (str_starts_with($number, '221')) {
            if (strlen($number) !== 12) {
                throw new \Exception("Numéro SN invalide. Le numéro doit comporter 9 chiffres après l'indicatif (+221).");
            }
            return $number;
        }

        // Si ce n'est ni CI ni SN -> Erreur
        throw new \Exception("Pays non autorisé pour l'envoi d'OTP. Seuls CI (+225) et SN (+221) sont acceptés.");
    }

    public function logout(Request $request)
    {
        session()->forget('user_id');
        return redirect('/')->with('message', 'Vous avez ete deconnecte.');
    }
}
