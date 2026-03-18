<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\RH\Affectation;

/**
 * Middleware pour vérifier l'état actif de l'utilisateur et de son affectation.
 * 
 * Si l'utilisateur ou son affectation est marqué comme inactif,
 * il sera déconnecté immédiatement.
 */
class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        /** @var User $user */
        $user = Auth::user();

        // L'état utilisateur est stocké en minuscules dans users.etat.
        if (strtolower((string) $user->etat) !== 'actif') {
            return $this->logoutWithMessage(
                $request,
                'Votre compte a été désactivé. Veuillez contacter l\'administrateur.'
            );
        }

        // Vérifier l'affectation uniquement si l'agent possède des affectations.
        if ($user->agent_matricule) {
            $hasAnyAffectation = Affectation::where('agent_matricule', $user->agent_matricule)->exists();

            if ($hasAnyAffectation) {
                $hasActiveAffectation = Affectation::where('agent_matricule', $user->agent_matricule)
                    ->whereRaw('UPPER(Etat) = ?', ['ACTIF'])
                    ->exists();

                if (! $hasActiveAffectation) {
                    return $this->logoutWithMessage(
                        $request,
                        'Votre affectation a été désactivée. Veuillez contacter l\'administrateur.'
                    );
                }
            }
        }

        return $next($request);
    }

    private function logoutWithMessage(Request $request, string $message): Response
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 401);
        }

        return redirect()->route('login')->with('error', $message);
    }
}
