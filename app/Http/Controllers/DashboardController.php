<?php

namespace App\Http\Controllers;

use App\Models\Credit\CreditDemande;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Anciennement des Closures directement dans routes/web.php (dashboard,
 * heartbeat) — déplacées ici car Laravel refuse de mettre en cache les
 * routes basées sur une Closure (`php artisan route:cache` échoue avec
 * "Unable to prepare route [...] for serialization. Uses Closure.").
 *
 * Si un tel échec se produit en production ET qu'un ancien cache de routes
 * existe déjà (bootstrap/cache/routes-v7.php d'un déploiement précédent),
 * la commande route:cache échoue silencieusement SANS écraser cet ancien
 * cache périmé — Laravel continue alors à servir indéfiniment cet ancien
 * jeu de routes, qui peut ne plus contenir des routes ajoutées depuis
 * (ex : "Route [clients.index] not defined" alors que la route existe
 * bien dans le code actuel).
 */
class DashboardController extends Controller
{
    public function index()
    {
        // Alerte : compte les dossiers avec au moins une échéance dépassée
        // (EN_ATTENTE ou EN_RETARD avec date < aujourd'hui)
        $today = Carbon::now()->toDateString();
        $alerteRecouvrementCount = CreditDemande::whereNotIn('statut_global', ['SOLDE', 'ANNULE'])
            ->whereHas('echeancier.echeances', function ($query) use ($today) {
                $query->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD'])
                      ->where('date_echeance', '<', $today);
            })
            ->count();

        return view('dashboard', compact('alerteRecouvrementCount'));
    }

    /** Prolonge la session en mettant à jour _last_activity (heartbeat AJAX). */
    public function heartbeat(Request $request)
    {
        session(['_last_activity' => time()]);
        $remaining = (int) config('session.inactivity_timeout', 600);
        return response()->json(['ok' => true, 'remaining' => $remaining]);
    }

    /** Journal des erreurs JavaScript côté client → storage/logs/laravel.log */
    public function logFrontendError(Request $request)
    {
        \Illuminate\Support\Facades\Log::warning('[Frontend JS] ' . $request->input('message', '?'), [
            'context'     => $request->input('context'),
            'http_status' => $request->input('status'),
            'user_id'     => \Illuminate\Support\Facades\Auth::id(),
            'ip'          => $request->ip(),
        ]);
        return response()->json(['ok' => true]);
    }
}
