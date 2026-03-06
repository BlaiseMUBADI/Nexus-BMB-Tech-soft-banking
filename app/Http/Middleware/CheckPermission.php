<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware RBAC dynamique.
 *
 * Usage sur une route :  ->middleware('permission:EBEN-PER6')
 *
 * Le middleware vérifie en Base de données si l'utilisateur connecté
 * possède le code de permission demandé via ses rôles.
 * Le résultat est mis en cache (mémoire de la requête) pour éviter
 * plusieurs requêtes SQL par page.
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permissionCode): Response
    {
        // 1. Utilisateur non connecté → rediriger vers login
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Vérifier la permission dynamiquement
        /** @var User $user */
        $user = Auth::user();
        if (! $user->hasPermission($permissionCode)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Accès non autorisé. Permission requise : ' . $permissionCode,
                ], 403);
            }
            abort(403, 'Vous n\'avez pas la permission d\'accéder à cette ressource.');
        }

        return $next($request);
    }
}
