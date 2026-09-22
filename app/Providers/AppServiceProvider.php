<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use App\Models\Credit\CreditDemande;
use App\Models\RH\Affectation;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Pagination Bootstrap 4 (AdminLTE) partout : sans cette déclaration,
        // Laravel 12 utilise ses vues Tailwind par défaut dont les chevrons
        // (<svg class="w-5 h-5">) s'étirent en pleine largeur dans cet
        // environnement Bootstrap (symptôme du « V » géant sur la liste des
        // dossiers en retard / recouvrement).
        Paginator::useBootstrapFour();

        // Injecter l'utilisateur connecté + ses permissions dans toutes les vues
        // afin que les contrôles conditionnels de permissions restent cohérents.
        //
        // PERFORMANCE (21/09/2026) : ce composer est déclaré sur '*' donc il
        // s'exécute pour CHAQUE vue rendue — y compris chaque @include/partiel.
        // Il recalculait donc plusieurs fois par page : 2 requêtes
        // information_schema (Schema::hasTable), 4 requêtes notifications,
        // 2 requêtes affectation/guichet et le compteur "en retard" (requête
        // EXISTS lourde) — ce qui alourdissait tous les menus.
        // Correctifs : calcul UNE seule fois par requête (mémo statique) et
        // agrégats coûteux mis en cache 60 s.
        View::composer('*', function (\Illuminate\View\View $view) {
            static $shared = null;

            if ($shared === null) {
                $shared = $this->buildSharedViewData();
            }

            $view->with($shared);
        });
    }

    /**
     * Données partagées par toutes les vues (topbar + sidebar), calculées
     * une seule fois par requête.
     *
     * @return array<string, mixed>
     */
    private function buildSharedViewData(): array
    {
        /** @var User|null $authUser */
        $authUser = Auth::check() ? Auth::user() : null;

        /** @var string[] $userPermCodes */
        $userPermCodes = $authUser ? $authUser->getPermissionCodes() : [];

        $latestUnreadNotifications = collect();
        $unreadNotificationCount = 0;
        $actionNotificationCount = 0;
        $unreadNotificationCategoryCounts = collect();

        if ($authUser && $this->tableExists('notifications')) {
            $latestUnreadNotifications = $authUser->unreadNotifications()
                ->latest()
                ->limit(8)
                ->get();

            // Compteurs (3 requêtes) mis en cache 30 s : ce sont des badges,
            // une latence de 30 s est acceptable et évite de scanner la table
            // notifications à chaque page.
            $notifCounts = Cache::remember('notif_counts_' . $authUser->id, 30, function () use ($authUser) {
                $actionCount = $authUser->unreadNotifications()
                    ->latest()
                    ->limit(50)
                    ->get()
                    ->filter(fn ($notification) => in_array(data_get($notification->data, 'type'), ['warning', 'danger', 'action_required'], true))
                    ->count();

                $categories = $authUser->unreadNotifications()
                    ->latest()
                    ->limit(100)
                    ->get()
                    ->map(fn ($notification) => data_get($notification->data, 'category', 'systeme'))
                    ->countBy()
                    ->all();

                return [
                    'total'      => $authUser->unreadNotifications()->count(),
                    'action'     => $actionCount,
                    'categories' => $categories,
                ];
            });

            $unreadNotificationCount = (int) ($notifCounts['total'] ?? 0);
            $actionNotificationCount = (int) ($notifCounts['action'] ?? 0);
            $unreadNotificationCategoryCounts = collect($notifCounts['categories'] ?? []);
        }

        // Type de guichet actuellement affecté à l'agent connecté (FIXE / MOBILE / CENTRAL / null)
        // Utilisé pour masquer dans le menu les fonctionnalités interdites aux guichets MOBILE
        // (ex : Opérations Administratives), en cohérence avec les restrictions déjà appliquées
        // côté contrôleur (DepenseController, RecetteController, OperationAdministrativeController).
        $guichetTypeActuel = null;
        if ($authUser && $authUser->agent_matricule && $this->tableExists('tb_affectations')) {
            $cached = Cache::remember('guichet_type_' . $authUser->agent_matricule, 60, function () use ($authUser) {
                $affectation = Affectation::where('agent_matricule', $authUser->agent_matricule)
                    ->where('Etat', 'ACTIF')
                    ->whereNotNull('guichet_id')
                    ->orderByDesc('date_debut')
                    ->with('guichet')
                    ->first();

                return ['type' => $affectation?->guichet?->type_guichet];
            });
            $guichetTypeActuel = $cached['type'] ?? null;
        }

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
        // Caché 60 s : requête EXISTS coûteuse exécutée sur toutes les pages.
        $alerteRecouvrementCount = 0;
        if ($authUser && $authUser->hasPermission('EBEN-PER90')) {
            $alerteRecouvrementCount = (int) Cache::remember(
                'alerte_recouvrement_count',
                60,
                fn () => CreditDemande::enRetardReel()->count()
            );
        }

        return [
            'authUser'                         => $authUser,
            'userPermCodes'                    => $userPermCodes,
            'guichetTypeActuel'                => $guichetTypeActuel,
            'latestUnreadNotifications'        => $latestUnreadNotifications,
            'unreadNotificationCount'          => $unreadNotificationCount,
            'actionNotificationCount'          => $actionNotificationCount,
            'unreadNotificationCategoryCounts' => $unreadNotificationCategoryCounts,
            'alerteRecouvrementCount'          => $alerteRecouvrementCount,
        ];
    }

    /**
     * Vérifie l'existence d'une table en mémoïsant le résultat pour la
     * requête (Schema::hasTable interroge information_schema à chaque appel).
     */
    private function tableExists(string $table): bool
    {
        static $cache = [];

        if (!array_key_exists($table, $cache)) {
            $cache[$table] = Schema::hasTable($table);
        }

        return $cache[$table];
    }
}
