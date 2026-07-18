<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Administration\UtilisateurController;
use App\Http\Controllers\Administration\ZoneController;
use App\Http\Controllers\Administration\PortefeuilleController;
use App\Http\Controllers\Administration\DeviseTauxController;
use App\Http\Controllers\Administration\GuichetController;
use App\Http\Controllers\Administration\RolesPermissionsController;
use App\Http\Controllers\Administration\SmsTestController;
use App\Http\Controllers\Administration\AuditLogController;

Route::middleware('auth')->prefix('administration')->group(function () {

    // Journal d'activité (audit) — permission EBEN-PER42
    Route::middleware('permission:EBEN-PER42')->group(function () {
        Route::get('/journal-activite', [AuditLogController::class, 'index'])->name('administration.journal_activite');
    });

    
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
        Route::put('/zones-portfeuille/{code_zone}',      [ZoneController::class, 'update'])->name('administration.zones.update');
        Route::delete('/zones-portfeuille/{code_zone}',   [ZoneController::class, 'destroy'])->name('administration.zones.destroy');
        Route::get('/zones/data',                         [ZoneController::class, 'data'])->name('administration.zones.data');
        Route::get('/portefeuilles', fn () => redirect()->route('administration.zones.index', ['#tab-portefeuilles']))->name('administration.portefeuilles.index');
        Route::post('/portefeuilles',                     [PortefeuilleController::class, 'store'])->name('administration.portefeuilles.store');
        Route::put('/portefeuilles/{id}',                 [PortefeuilleController::class, 'update'])->name('administration.portefeuilles.update');
        Route::delete('/portefeuilles/{id}',              [PortefeuilleController::class, 'destroy'])->name('administration.portefeuilles.destroy');

        Route::get('/guichets',                           [GuichetController::class, 'index'])->name('administration.guichets.index');
        Route::post('/guichets',                          [GuichetController::class, 'store'])->name('administration.guichets.store');
        Route::put('/guichets/{id}',                      [GuichetController::class, 'update'])->name('administration.guichets.update');
        Route::delete('/guichets/{id}',                   [GuichetController::class, 'destroy'])->name('administration.guichets.destroy');
        Route::post('/guichets/{id}/devise',              [GuichetController::class, 'addDevise'])->name('administration.guichets.addDevise');
        Route::post('/guichets/alimenter',                [GuichetController::class, 'alimenter'])->name('administration.guichets.alimenter');
        Route::get('/guichets/alimentations',             [GuichetController::class, 'historiqueAlimentations'])->name('administration.guichets.alimentations');
        Route::get('/guichets/coffre-balances',           [GuichetController::class, 'coffreBalances'])->name('administration.guichets.coffreBalances');
    });

    
    Route::middleware('permission:EBEN-PER2')->group(function () {
        Route::get('/roles-permissions',                  [RolesPermissionsController::class, 'index'])->name('administration.roles_permissions');
        Route::get('/roles/{role}',                       [RolesPermissionsController::class, 'show'])->name('administration.roles.show');
        Route::get('/roles-table',                        [RolesPermissionsController::class, 'rolesTable'])->name('administration.roles.table');
        Route::get('/user-roles-permissions/{user_id}',   [RolesPermissionsController::class, 'userRolesPermissionsList'])->name('administration.user-roles-permissions');
        Route::get('/role-permissions/{role_code}',       [RolesPermissionsController::class, 'rolePermissionsList'])->name('administration.role-permissions.list');
    });

    
    Route::middleware('permission:EBEN-PER3')->group(function () {
        Route::post('/roles-permissions',                 [RolesPermissionsController::class, 'store'])->name('administration.roles_permissions.store');
        Route::delete('/roles/{role}',                    [RolesPermissionsController::class, 'destroy'])->name('administration.roles.destroy');
        Route::post('/user-roles/attach',                 [RolesPermissionsController::class, 'attachUserRole'])->name('administration.user-roles.attach');
        Route::post('/user-roles/detach',                 [RolesPermissionsController::class, 'detachUserRole'])->name('administration.user-roles.detach');
    });

    
    Route::middleware('permission:EBEN-PER5')->group(function () {
        Route::post('/role-permissions/attach',           [RolesPermissionsController::class, 'attachPermission'])->name('administration.role-permissions.attach');
        Route::post('/role-permissions/detach',           [RolesPermissionsController::class, 'detachPermission'])->name('administration.role-permissions.detach');
    });

   
    Route::middleware('permission:EBEN-PER4')->group(function () {
        Route::get('/permissions-table',                  [RolesPermissionsController::class, 'permissionsTable'])->name('administration.permissions.table');
    });

  
    Route::post('/permissions', fn() => abort(403, 'Les permissions sont gérées par le développeur.'))->name('administration.permissions.store');

    
    Route::middleware('permission:EBEN-PER20')->group(function () {
        Route::get('/devises-taux',                       [DeviseTauxController::class, 'index'])->name('administration.devises-taux.index');
    });

    
    Route::middleware('permission:EBEN-PER21')->group(function () {
        Route::post('/devises-taux/devise',               [DeviseTauxController::class, 'storeDevise'])->name('administration.devises-taux.storeDevise');
        Route::post('/devises-taux/taux',                 [DeviseTauxController::class, 'storeTaux'])->name('administration.devises-taux.storeTaux');
        Route::delete('/devises-taux/taux/{id}',          [DeviseTauxController::class, 'destroyTaux'])->name('administration.devises-taux.destroyTaux');
        Route::delete('/devises-taux/devise/{code_iso}',  [DeviseTauxController::class, 'destroyDevise'])->name('administration.devises-taux.destroyDevise');
    });

    // Envoi de SMS = action coûteuse/sensible → réservée aux administrateurs (EBEN-PER1)
    Route::middleware('permission:EBEN-PER1')->group(function () {
        Route::get('/sms-test', [SmsTestController::class, 'index'])->name('administration.sms_test.index');
        Route::post('/sms-test', [SmsTestController::class, 'send'])->name('administration.sms_test.send');
    });
});
