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
    /**
     * Redirige la racine du site ('/') vers le tableau de bord.
     *
     * Remplace un ancien `Route::redirect('/', '/dashboard')` : ce helper
     * Laravel génère volontairement une URL RELATIVE à la racine du domaine
     * (ex: "/dashboard"), sans jamais tenir compte du sous-dossier dans lequel
     * l'application est réellement installée (ex: /Nexus-BMB-Tech-soft-banking/public).
     * Résultat en environnement WAMP (app hors racine du domaine) : le
     * navigateur était renvoyé vers http://localhost/dashboard (404) au lieu
     * de http://localhost/Nexus-BMB-Tech-soft-banking/public/dashboard.
     *
     * `redirect()->route('dashboard')` génère lui une URL ABSOLUE correcte
     * (inclut le sous-dossier), tout en restant compatible `route:cache`
     * puisqu'il s'agit d'une vraie action de contrôleur, pas d'une Closure.
     */
    public function redirectToDashboard()
    {
        return redirect()->route('dashboard');
    }

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
