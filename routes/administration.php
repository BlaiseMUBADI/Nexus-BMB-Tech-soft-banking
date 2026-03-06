<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Administration\UtilisateurController;
use App\Http\Controllers\Administration\ZoneController;
use App\Http\Controllers\Administration\PortefeuilleController;
use App\Http\Controllers\Administration\DeviseTauxController;
use App\Http\Controllers\Administration\GuichetController;
use App\Http\Controllers\Administration\RolesPermissionsController;

Route::middleware('auth')->prefix('administration')->group(function () {

    // ── Accès administration général (utilisateurs, zones, guichets) ───────
    Route::middleware('permission:EBEN-PER1')->group(function () {
        Route::get('/utilisateurs/nouveau',               [UtilisateurController::class, 'nouveau'])->name('administration.utilisateurs.nouveau');
        Route::get('/utilisateurs',                       [UtilisateurController::class, 'liste'])->name('administration.utilisateurs.liste');
        Route::get('/utilisateurs/{id}',                  [UtilisateurController::class, 'show'])->name('administration.utilisateurs.show');
        Route::get('/utilisateurs/{id}/edit',             [UtilisateurController::class, 'edit'])->name('administration.utilisateurs.edit');
        Route::post('/utilisateurs',                      [UtilisateurController::class, 'store'])->name('administration.utilisateurs.store');
        Route::put('/utilisateurs/{id}',                  [UtilisateurController::class, 'update'])->name('administration.utilisateurs.update');
        Route::delete('/utilisateurs/{id}',               [UtilisateurController::class, 'destroy'])->name('administration.utilisateurs.destroy');
        Route::get('/utilisateurs/agent-info/{matricule}',[UtilisateurController::class, 'agentInfo'])->name('administration.utilisateurs.agentInfo');

        Route::get('/zones-portfeuille',                  [ZoneController::class, 'index'])->name('administration.zones.index');
        Route::post('/zones/store',                       [ZoneController::class, 'store'])->name('administration.zones.store');
        Route::delete('/zones-portfeuille/{code_zone}',   [ZoneController::class, 'destroy'])->name('administration.zones.destroy');
        Route::get('/zones/data',                         [ZoneController::class, 'data'])->name('administration.zones.data');
        Route::get('/portefeuilles', fn () => redirect()->route('administration.zones.index', ['#tab-portefeuilles']))->name('administration.portefeuilles.index');
        Route::post('/portefeuilles',                     [PortefeuilleController::class, 'store'])->name('administration.portefeuilles.store');
        Route::delete('/portefeuilles/{id}',              [PortefeuilleController::class, 'destroy'])->name('administration.portefeuilles.destroy');

        Route::get('/guichets',                           [GuichetController::class, 'index'])->name('administration.guichets.index');
        Route::post('/guichets',                          [GuichetController::class, 'store'])->name('administration.guichets.store');
        Route::delete('/guichets/{id}',                   [GuichetController::class, 'destroy'])->name('administration.guichets.destroy');
        Route::post('/guichets/{id}/devise',              [GuichetController::class, 'addDevise'])->name('administration.guichets.addDevise');
    });

    // ── Rôles : consultation (EBEN-PER2) ────────────────────────────────────
    Route::middleware('permission:EBEN-PER2')->group(function () {
        Route::get('/roles-permissions',                  [RolesPermissionsController::class, 'index'])->name('administration.roles_permissions');
        Route::get('/roles/{role}',                       [RolesPermissionsController::class, 'show'])->name('administration.roles.show');
        Route::get('/roles-table',                        [RolesPermissionsController::class, 'rolesTable'])->name('administration.roles.table');
        Route::get('/user-roles-permissions/{user_id}',   [RolesPermissionsController::class, 'userRolesPermissionsList'])->name('administration.user-roles-permissions');
        Route::get('/role-permissions/{role_code}',       [RolesPermissionsController::class, 'rolePermissionsList'])->name('administration.role-permissions.list');
    });

    // ── Rôles : gestion (EBEN-PER3) ─────────────────────────────────────────
    Route::middleware('permission:EBEN-PER3')->group(function () {
        Route::post('/roles-permissions',                 [RolesPermissionsController::class, 'store'])->name('administration.roles_permissions.store');
        Route::delete('/roles/{role}',                    [RolesPermissionsController::class, 'destroy'])->name('administration.roles.destroy');
        Route::post('/role-permissions/attach',           [RolesPermissionsController::class, 'attachPermission'])->name('administration.role-permissions.attach');
        Route::post('/role-permissions/detach',           [RolesPermissionsController::class, 'detachPermission'])->name('administration.role-permissions.detach');
        Route::post('/user-roles/attach',                 [RolesPermissionsController::class, 'attachUserRole'])->name('administration.user-roles.attach');
        Route::post('/user-roles/detach',                 [RolesPermissionsController::class, 'detachUserRole'])->name('administration.user-roles.detach');
    });

    // ── Permissions : consultation (EBEN-PER4) ───────────────────────────────
    Route::middleware('permission:EBEN-PER4')->group(function () {
        Route::get('/permissions-table',                  [RolesPermissionsController::class, 'permissionsTable'])->name('administration.permissions.table');
    });

    // ── Permissions : gestion (EBEN-PER5) ───────────────────────────────────
    // ⚠  DÉSACTIVÉ : les permissions sont statiques (définies dans le code).
    //    Seul un développeur peut en créer via une migration.
    //    Route::middleware('permission:EBEN-PER5')->group(function () {
    //        Route::post('/permissions', [RolesPermissionsController::class, 'storePermission'])->name('administration.permissions.store');
    //    });
    // Route de secours pour capturer les appels résiduels et retourner 403
    Route::post('/permissions', fn() => abort(403, 'Les permissions sont gérées par le développeur.'))->name('administration.permissions.store');

    // ── Devises : consultation (EBEN-PER20) ─────────────────────────────────
    Route::middleware('permission:EBEN-PER20')->group(function () {
        Route::get('/devises-taux',                       [DeviseTauxController::class, 'index'])->name('administration.devises-taux.index');
    });

    // ── Devises : gestion (EBEN-PER21) ──────────────────────────────────────
    Route::middleware('permission:EBEN-PER21')->group(function () {
        Route::post('/devises-taux/devise',               [DeviseTauxController::class, 'storeDevise'])->name('administration.devises-taux.storeDevise');
        Route::post('/devises-taux/taux',                 [DeviseTauxController::class, 'storeTaux'])->name('administration.devises-taux.storeTaux');
        Route::delete('/devises-taux/taux/{id}',          [DeviseTauxController::class, 'destroyTaux'])->name('administration.devises-taux.destroyTaux');
        Route::delete('/devises-taux/devise/{code_iso}',  [DeviseTauxController::class, 'destroyDevise'])->name('administration.devises-taux.destroyDevise');
    });
});
