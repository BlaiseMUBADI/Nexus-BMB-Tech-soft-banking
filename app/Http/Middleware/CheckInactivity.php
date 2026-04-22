<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckInactivity
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $timeout      = (int) config('session.inactivity_timeout', 600);
            $lastActivity = session('_last_activity');
            $now          = time();

            if ($lastActivity && ($now - $lastActivity) > $timeout) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json(['session_expired' => true], 401);
                }

                return redirect()->route('login')
                    ->with('status', 'Votre session a expiré pour cause d\'inactivité.');
            }

            session(['_last_activity' => $now]);
        }

        return $next($request);
    }
}
