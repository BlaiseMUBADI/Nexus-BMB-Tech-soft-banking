<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Administration\UtilisateurController;

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
    Route::get('/roles_permissions', [App\Http\Controllers\Administration\RolesPermissionsController::class, 'index'])->name('administration.roles_permissions');
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
});
