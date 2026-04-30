<?php

use App\Http\Controllers\Credit\CreditController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MODULE CRÉDIT – Routes
|--------------------------------------------------------------------------
| Toutes les routes nécessitent :
|   - Authentification (auth)
|   - Permission de base (EBEN-PER53 = Voir liste crédits)
|
| Hiérarchie des permissions :
|   PER53 – Voir liste crédits (toutes les routes y accèdent)
|   PER54 – Créer demande       → create / store
|   PER55 – Modifier brouillon  → edit / update
|   PER56 – Soumettre           → soumettre
|   PER57 – Voir détail         → show
|   PER58/59 – Analyse          → analyse / storeAnalyse
|   PER60-63 – Validation       → validation / storeValidation
|   PER64 – Déblocage           → deblocage / storeDeblocage
|   PER65 – Remboursement       → remboursement / storeRemboursement
|   PER66 – Annuler             → annuler
|   PER67 – Suspendre           → suspendre / leverSuspension
|   PER68 – Signaler suspect    → signalerSuspect
|   PER69 – Lever suspicion     → leverSuspicion / leverSuspension
|   PER70 – Dashboard           → dashboard / supervision
|   PER71 – PDF                 → pdfEcheancier / pdfFiche
|   PER72 – Audit               → (intégré dans show)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'permission:EBEN-PER53'])
    ->prefix('credits')
    ->name('credit.')
    ->group(function () {

        // ── Dashboard & Supervision ──────────────────────────────────
        Route::middleware('permission:EBEN-PER61|EBEN-PER62|EBEN-PER63|EBEN-PER64|EBEN-PER65')->group(function () {
            Route::get('/dashboard',    [CreditController::class, 'dashboard'])->name('dashboard');
            Route::get('/supervision',  [CreditController::class, 'supervision'])->name('supervision');
        });

        // ── Liste des dossiers ───────────────────────────────────────
        Route::get('/', [CreditController::class, 'index'])->name('index');

        // ── Création ─────────────────────────────────────────────────
        Route::middleware('permission:EBEN-PER54')->group(function () {
            Route::get('/nouveau',  [CreditController::class, 'create'])->name('create');
        });

        Route::middleware('permission:EBEN-PER54')->group(function () {
            Route::post('/', [CreditController::class, 'store'])->name('store');
        });

        // ── AJAX helpers (pas de permission supplémentaire au-delà PER53) ──
        Route::get('/ajax/comptes-client',  [CreditController::class, 'getComptesClient'])->name('ajax.comptes_client');
        Route::get('/ajax/simuler',         [CreditController::class, 'simuler'])->name('ajax.simuler');

        // ── Détail d'un dossier ───────────────────────────────────────
        Route::middleware('permission:EBEN-PER57')->group(function () {
            Route::get('/{dossier}', [CreditController::class, 'show'])->name('show');
        });

        // ── Soumission ────────────────────────────────────────────────
        // PER56 = soumettre explicitement, PER53 = créateur peut aussi soumettre
        Route::middleware('permission:EBEN-PER56|EBEN-PER53')->group(function () {
            Route::post('/{dossier}/soumettre', [CreditController::class, 'soumettre'])->name('soumettre');
        });

        Route::middleware('permission:EBEN-PER61')->group(function () {
            Route::post('/{dossier}/affecter-analyse', [CreditController::class, 'affecterAnalyse'])->name('affecter_analyse');
        });

        // ── Analyse ───────────────────────────────────────────────────
        Route::middleware('permission:EBEN-PER58')->group(function () {
            Route::get('/{dossier}/analyse', [CreditController::class, 'analyse'])->name('analyse');
        });

        Route::middleware('permission:EBEN-PER58')->group(function () {
            Route::post('/{dossier}/analyse', [CreditController::class, 'storeAnalyse'])->name('analyse.store');
        });

        // ── Validation ────────────────────────────────────────────────
        Route::middleware('permission:EBEN-PER60|EBEN-PER61|EBEN-PER62|EBEN-PER63')->group(function () {
            Route::get('/{dossier}/validation', [CreditController::class, 'validation'])->name('validation');
        });

        Route::middleware('permission:EBEN-PER60|EBEN-PER61|EBEN-PER62|EBEN-PER63')->group(function () {
            Route::post('/{dossier}/validation', [CreditController::class, 'storeValidation'])->name('validation.store');
        });

        // ── Déblocage ─────────────────────────────────────────────────
        Route::middleware('permission:EBEN-PER64')->group(function () {
            Route::get('/{dossier}/deblocage', [CreditController::class, 'deblocage'])->name('deblocage');
        });

        Route::middleware('permission:EBEN-PER64')->group(function () {
            Route::post('/{dossier}/deblocage', [CreditController::class, 'storeDeblocage'])->name('deblocage.store');
        });

        // ── Remboursement ─────────────────────────────────────────────
        Route::middleware('permission:EBEN-PER65')->group(function () {
            Route::get('/{dossier}/remboursement', [CreditController::class, 'remboursement'])->name('remboursement');
        });

        Route::middleware('permission:EBEN-PER65')->group(function () {
            Route::post('/{dossier}/remboursement', [CreditController::class, 'storeRemboursement'])->name('remboursement.store');
        });

        // ── Actions transverses ───────────────────────────────────────
        Route::middleware('permission:EBEN-PER66')->post('/{dossier}/annuler', [CreditController::class, 'annuler'])->name('annuler');

        Route::middleware('permission:EBEN-PER67')->group(function () {
            Route::post('/{dossier}/suspendre',        [CreditController::class, 'suspendre'])->name('suspendre');
        });
        Route::middleware('permission:EBEN-PER69')->group(function () {
            Route::post('/{dossier}/lever-suspension', [CreditController::class, 'leverSuspension'])->name('lever_suspension');
            Route::post('/{dossier}/lever-suspicion',  [CreditController::class, 'leverSuspicion'])->name('lever_suspicion');
        });

        Route::middleware('permission:EBEN-PER68')->post('/{dossier}/signaler-suspect', [CreditController::class, 'signalerSuspect'])->name('signaler_suspect');

        // ── PDF ───────────────────────────────────────────────────────
        Route::middleware('permission:EBEN-PER71')->group(function () {
            Route::get('/{dossier}/pdf/echeancier', [CreditController::class, 'pdfEcheancier'])->name('pdf.echeancier');
            Route::get('/{dossier}/pdf/fiche',      [CreditController::class, 'pdfFiche'])->name('pdf.fiche');
        });
    });
