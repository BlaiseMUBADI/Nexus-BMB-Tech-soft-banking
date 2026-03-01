<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Administration\UtilisateurController;
use App\Http\Controllers\Administration\ZoneController;
use App\Http\Controllers\Administration\PortefeuilleController;

Route::prefix('administration')->group(function () {
    // AJAX : attacher/détacher un rôle à un utilisateur
    Route::post('/user-roles/attach', [App\Http\Controllers\Administration\RolesPermissionsController::class, 'attachUserRole']);
    Route::post('/user-roles/detach', [App\Http\Controllers\Administration\RolesPermissionsController::class, 'detachUserRole']);
    // AJAX : liste des rôles et permissions d'un utilisateur
    Route::get('/user-roles-permissions/{user_id}', [App\Http\Controllers\Administration\RolesPermissionsController::class, 'userRolesPermissionsList']);

    //Route::get('/permissions-table', [App\Http\Controllers\Administration\RolesPermissionsController::class, 'permissionsTable'])->name('administration.permissions.table');

    // Route pour supprimer un rôle spécifique (corrige l'erreur de route manquante)
    Route::delete('/roles/{role}', [App\Http\Controllers\Administration\RolesPermissionsController::class, 'destroy'])->name('administration.roles.destroy');
    Route::post('/roles-permissions', [App\Http\Controllers\Administration\RolesPermissionsController::class, 'store'])->name('administration.roles_permissions.store');
    Route::post('/permissions', [App\Http\Controllers\Administration\RolesPermissionsController::class, 'storePermission'])->name('administration.permissions.store');

    // Route pour afficher un rôle spécifique (corrige l'erreur de route manquante)
    Route::get('/roles/{role}', [App\Http\Controllers\Administration\RolesPermissionsController::class, 'show'])->name('administration.roles.show');
    Route::get('/roles-permissions', [App\Http\Controllers\Administration\RolesPermissionsController::class, 'index'])->name('administration.roles_permissions');
    Route::get('/roles-table', [App\Http\Controllers\Administration\RolesPermissionsController::class, 'rolesTable'])->name('administration.roles.table');
    Route::get('/utilisateurs/nouveau', [UtilisateurController::class, 'nouveau'])->name('administration.utilisateurs.nouveau');
    Route::get('/utilisateurs', [UtilisateurController::class, 'liste'])->name('administration.utilisateurs.liste');
    Route::get('/utilisateurs/{id}', [UtilisateurController::class, 'show'])->name('administration.utilisateurs.show');
    Route::get('/utilisateurs/{id}/edit', [UtilisateurController::class, 'edit'])->name('administration.utilisateurs.edit');
    Route::get('/permissions-table', [App\Http\Controllers\Administration\RolesPermissionsController::class, 'permissionsTable'])->name('administration.permissions.table');

    Route::post('/utilisateurs', [UtilisateurController::class, 'store'])->name('administration.utilisateurs.store');
    // Route AJAX pour infos agent (utilisée dans la création utilisateur)
    Route::get('/utilisateurs/agent-info/{matricule}', [App\Http\Controllers\Administration\UtilisateurController::class, 'agentInfo']);
    // Suppression utilisateur
    Route::delete('/utilisateurs/{id}', [UtilisateurController::class, 'destroy'])->name('administration.utilisateurs.destroy');
    // Modification utilisateur
    Route::put('/utilisateurs/{id}', [UtilisateurController::class, 'update'])->name('administration.utilisateurs.update');

    // AJAX : liste des permissions d'un rôle (avec cochage)
    Route::get('/role-permissions/{role_code}', [App\Http\Controllers\Administration\RolesPermissionsController::class, 'rolePermissionsList']);
    // AJAX : attacher/détacher une permission à un rôle
    Route::post('/role-permissions/attach', [App\Http\Controllers\Administration\RolesPermissionsController::class, 'attachPermission']);
    Route::post('/role-permissions/detach', [App\Http\Controllers\Administration\RolesPermissionsController::class, 'detachPermission']);

    // Zones / Portfeuille

    Route::get('/zones-portfeuille', [ZoneController::class, 'index'])->name('administration.zones.index');
    Route::post('/zones-portfeuille', [ZoneController::class, 'store'])->name('administration.zones.store');
    Route::delete('/zones-portfeuille/{code_zone}', [ZoneController::class, 'destroy'])->name('administration.zones.destroy');
    Route::get('/zones/data', [App\Http\Controllers\Administration\ZoneController::class, 'data'])->name('administration.zones.data');


    // Page liste des portefeuilles d'agents
    Route::get('/portefeuilles', [App\Http\Controllers\Administration\PortefeuilleController::class, 'index'])->name('administration.portefeuilles.index');
    // Portefeuilles d'agents
    Route::post('/portefeuilles', [PortefeuilleController::class, 'store'])->name('administration.portefeuilles.store');
    // Suppression portefeuille d'agent
    Route::delete('/portefeuilles/{id}', [PortefeuilleController::class, 'destroy'])->name('administration.portefeuilles.destroy');
  
 
    // Gestion des devises et taux
    Route::get('/devises-taux', [App\Http\Controllers\Administration\DeviseTauxController::class, 'index'])->name('administration.devises_taux.index');
    Route::post('/devises-taux/devise', [App\Http\Controllers\Administration\DeviseTauxController::class, 'storeDevise'])->name('administration.devises_taux.storeDevise');
    Route::post('/devises-taux/taux', [App\Http\Controllers\Administration\DeviseTauxController::class, 'storeTaux'])->name('administration.devises_taux.storeTaux');
    // Suppression taux d'échange
    Route::delete('/devises-taux/taux/{id}', [App\Http\Controllers\Administration\DeviseTauxController::class, 'destroyTaux'])->name('administration.devises_taux.destroyTaux');

    // Suppression devise
    Route::delete('/devises-taux/devise/{code_iso}', [App\Http\Controllers\Administration\DeviseTauxController::class, 'destroyDevise'])->name('administration.devises_taux.destroyDevise');

    });
