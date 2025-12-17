<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

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
