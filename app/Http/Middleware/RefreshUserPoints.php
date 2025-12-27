<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class RefreshUserPoints
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $userId = session('user_id');

        // Si pas de session, essayer de reconnecter via le cookie remember_token
        if (!$userId) {
            $rememberToken = $request->cookie('remember_token');
            if ($rememberToken) {
                $user = User::where('remember_token', $rememberToken)->first();
                if ($user) {
                    // Reconnexion automatique via remember_token
                    session([
                        'user_id' => $user->id,
                        'user_points' => $user->points_total ?? 0,
                        'predictor_name' => $user->name
                    ]);
                    $userId = $user->id;
                    Log::info('Reconnexion automatique via remember_token (middleware)', ['user_id' => $user->id]);
                }
            }
        }

        if ($userId) {
            $user = User::find($userId);

            if ($user) {
                // Update session with current points and name from database
                session([
                    'user_points' => $user->points_total,
                    'predictor_name' => $user->name
                ]);
            }
        }

        return $next($request);
    }
}
