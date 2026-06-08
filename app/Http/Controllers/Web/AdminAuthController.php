<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminAuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion admin
     */
    public function showLoginForm()
    {
        if (session('user_id')) {
            $user = User::find(session('user_id'));
            if ($user && in_array($user->role, ['admin', 'soboa'])) {
                return redirect('/admin');
            }
        }
        return view('admin.auth.login');
    }

    /**
     * Authentifie l'admin avec username et mot de passe
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        // Récupérer les identifiants depuis config (fonctionne avec config:cache)
        $adminUsername = config('app.admin.username');
        $adminPassword = config('app.admin.password');

        // Identifiants Soboa (configurés via .env, jamais en dur)
        $soboaUsername = config('app.soboa.username');
        $soboaPassword = config('app.soboa.password');

        Log::info('Tentative de connexion admin', ['username' => $username]);

        // Vérification des identifiants admin (comparaison à temps constant)
        if ($this->credentialsMatch($username, $password, $adminUsername, $adminPassword)) {
            // Créer ou récupérer l'utilisateur admin
            $admin = User::where('role', 'admin')->first();

            if (!$admin) {
                $admin = User::create([
                    'name' => 'Administrateur',
                    'phone' => '+2250000000000',
                    'role' => 'admin',
                    'is_admin' => true,
                ]);
                Log::info('Utilisateur admin créé', ['id' => $admin->id]);
            }

            $request->session()->regenerate();
            session(['user_id' => $admin->id]);
            Log::info('Connexion admin réussie', ['user_id' => $admin->id]);
            return redirect()->route('admin.dashboard')->with('success', 'Bienvenue, Administrateur !');
        }

        // Vérification des identifiants Soboa (comparaison à temps constant)
        if ($this->credentialsMatch($username, $password, $soboaUsername, $soboaPassword)) {
            // Créer ou récupérer l'utilisateur soboa
            $soboa = User::where('role', 'soboa')->first();

            if (!$soboa) {
                $soboa = User::create([
                    'name' => 'Soboa',
                    'phone' => '+2250000000001',
                    'role' => 'soboa',
                    'is_admin' => false,
                ]);
                Log::info('Utilisateur soboa créé', ['id' => $soboa->id]);
            }

            $request->session()->regenerate();
            session(['user_id' => $soboa->id]);
            Log::info('Connexion soboa réussie', ['user_id' => $soboa->id]);
            return redirect()->route('admin.dashboard')->with('success', 'Bienvenue, Soboa !');
        }

        Log::warning('Échec connexion admin - identifiants incorrects', ['username' => $username]);
        return back()->withErrors(['credentials' => 'Identifiants incorrects.'])->withInput();
    }

    /**
     * Compare des identifiants à temps constant.
     * Échoue (false) si les identifiants attendus ne sont pas configurés.
     */
    private function credentialsMatch(?string $username, ?string $password, ?string $expectedUsername, ?string $expectedPassword): bool
    {
        if (empty($expectedUsername) || empty($expectedPassword)) {
            return false;
        }

        return hash_equals($expectedUsername, (string) $username)
            && hash_equals($expectedPassword, (string) $password);
    }

    /**
     * Déconnexion admin
     */
    public function logout()
    {
        session()->forget('user_id');
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/')->with('success', 'Déconnexion réussie.');
    }
}
