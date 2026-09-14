<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Credit\CreditDemande;
use App\Models\RH\Affectation;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Injecter l'utilisateur connecté + ses permissions dans toutes les vues
        // afin que les contrôles conditionnels de permissions restent cohérents.
        View::composer('*', function (\Illuminate\View\View $view) {
            /** @var User|null $authUser */
            $authUser = Auth::check() ? Auth::user() : null;

            /** @var string[] $userPermCodes */
            $userPermCodes = $authUser ? $authUser->getPermissionCodes() : [];

            $latestUnreadNotifications = collect();
            $unreadNotificationCount = 0;
            $actionNotificationCount = 0;
            $unreadNotificationCategoryCounts = collect();

            if ($authUser && Schema::hasTable('notifications')) {
                $latestUnreadNotifications = $authUser->unreadNotifications()
                    ->latest()
                    ->limit(8)
                    ->get();

                $unreadNotificationCount = (int) $authUser->unreadNotifications()->count();
                $actionNotificationCount = (int) $authUser->unreadNotifications()
                    ->latest()
                    ->limit(50)
                    ->get()
                    ->filter(function ($notification) {
                        return in_array(data_get($notification->data, 'type'), ['warning', 'danger', 'action_required'], true);
                    })
                    ->count();

                $unreadNotificationCategoryCounts = $authUser->unreadNotifications()
                    ->latest()
                    ->limit(100)
                    ->get()
                    ->map(function ($notification) {
                        return data_get($notification->data, 'category', 'systeme');
                    })
                    ->countBy();
            }

            // Type de guichet actuellement affecté à l'agent connecté (FIXE / MOBILE / CENTRAL / null)
            // Utilisé pour masquer dans le menu les fonctionnalités interdites aux guichets MOBILE
            // (ex : Opérations Administratives), en cohérence avec les restrictions déjà appliquées
            // côté contrôleur (DepenseController, RecetteController, OperationAdministrativeController).
            $guichetTypeActuel = null;
            if ($authUser && $authUser->agent_matricule && Schema::hasTable('tb_affectations')) {
                $affectation = Affectation::where('agent_matricule', $authUser->agent_matricule)
                    ->where('Etat', 'ACTIF')
                    ->whereNotNull('guichet_id')
                    ->orderByDesc('date_debut')
                    ->with('guichet')
                    ->first();
                $guichetTypeActuel = $affectation?->guichet?->type_guichet;
            }

            $view->with('authUser', $authUser);
            $view->with('userPermCodes', $userPermCodes);
            $view->with('guichetTypeActuel', $guichetTypeActuel);
            $view->with('latestUnreadNotifications', $latestUnreadNotifications);
            $view->with('unreadNotificationCount', $unreadNotificationCount);
            $view->with('actionNotificationCount', $actionNotificationCount);
            $view->with('unreadNotificationCategoryCounts', $unreadNotificationCategoryCounts);

            // Compteur global (badge sidebar + tableau de bord) : dossiers actifs
            // avec au moins une échéance dépassée. IMPORTANT : ce composer de vue
            // s'exécute pour CHAQUE vue et écrase toute valeur passée par un
            // contrôleur (ex: DashboardController::index()) — c'est donc ICI,
            // et UNIQUEMENT ici, que la définition doit être corrigée (bug
            // constaté le 09/09/2026 : corriger DashboardController seul n'avait
            // aucun effet visible, ce composer réécrasait ensuite la valeur).
            // PARTIELLEMENT_PAYE inclus : une échéance en retard partiellement
            // réglée reste due — même règle que RecouvrementController::index().
            // scopeEnRetardReel() = source unique (cf. CreditDemande) — utilisée
            // ici, dans DashboardController et RecouvrementController pour
            // garantir un total IDENTIQUE partout où "en retard" est affiché.
            $alerteRecouvrementCount = 0;
            if ($authUser && $authUser->hasPermission('EBEN-PER90')) {
                $alerteRecouvrementCount = CreditDemande::enRetardReel()->count();
            }
            $view->with('alerteRecouvrementCount', $alerteRecouvrementCount);
        });
    }
}
