<?php

use App\Http\Controllers\Credit\CreditController;
use App\Http\Controllers\Credit\CreditCommissionController;
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
|   PER67 – Suspendre           → suspendre
|   PER68 – Signaler suspect    → signalerSuspect
|   PER69 – Lever suspension/suspicion → leverSuspension / leverSuspicion
|   PER61-64 – Supervision      → dashboard / supervision / en_cours
|   PER70 – Rapport frais       → rapport_frais (voir plus bas)
|   PER71 – PDF                 → pdfEcheancier / pdfFiche
|   PER72 – Audit               → (intégré dans show)
|   PER73 – Pièces justificatives → pieces.update
|--------------------------------------------------------------------------
*/

// NOTE : le portail (middleware ci-dessous) n'exige plus UNIQUEMENT EBEN-PER53.
// Avant, TOUTE route nichée (y compris celles protégées par PER70, PER58,
// PER60-64, PER10|PER111, etc.) exigeait EN PLUS PER53 à cause du merge des
// middlewares de groupe Laravel — un rôle disposant par exemple de PER70 SEUL
// (sans PER53) voyait le menu correspondant (gate OR côté sidebar) mais
// obtenait un 403 au clic sur CHAQUE lien. Le portail exige maintenant AU
// MOINS UNE permission crédit quelconque (garde-fou anti-accès générique),
// et chaque sous-groupe continue d'exiger sa permission précise comme avant.
Route::middleware(['auth', 'permission:EBEN-PER53|EBEN-PER54|EBEN-PER55|EBEN-PER56|EBEN-PER57|EBEN-PER58|EBEN-PER60|EBEN-PER61|EBEN-PER62|EBEN-PER63|EBEN-PER64|EBEN-PER66|EBEN-PER67|EBEN-PER68|EBEN-PER69|EBEN-PER70|EBEN-PER71|EBEN-PER73|EBEN-PER10|EBEN-PER111|EBEN-PER113|EBEN-PER118'])
    ->prefix('credits')
    ->name('credit.')
    ->group(function () {

        // ── Dashboard & Supervision ──────────────────────────────────
        Route::middleware('permission:EBEN-PER61|EBEN-PER62|EBEN-PER63|EBEN-PER64')->group(function () {
            Route::get('/dashboard',    [CreditController::class, 'dashboard'])->name('dashboard');
            Route::get('/supervision',  [CreditController::class, 'supervision'])->name('supervision');
            Route::get('/en-cours',     [CreditController::class, 'enCours'])->name('en_cours');
        });

        // ── Liste des dossiers ───────────────────────────────────────
        // Garde explicite : PER53 (accès de base) OU l'une des permissions
        // "de vue filtrée" utilisées par les raccourcis du sous-menu Crédits
        // (Dossiers à analyser=PER58, à valider=PER60-63, Déblocage=PER64,
        // Remboursement=PER10|PER111, Rapport frais=PER70) — ces liens pointent
        // tous vers CETTE même route avec un paramètre `statut`/`vue` différent.
        Route::middleware('permission:EBEN-PER53|EBEN-PER58|EBEN-PER60|EBEN-PER61|EBEN-PER62|EBEN-PER63|EBEN-PER64|EBEN-PER10|EBEN-PER111|EBEN-PER70')->group(function () {
            Route::get('/', [CreditController::class, 'index'])->name('index');
            Route::get('/print', [CreditController::class, 'printListe'])->name('print.liste');
        });

        // ── Tombée d'échéances (permission dédiée EBEN-PER118, distincte de PER53) ──
        Route::middleware('permission:EBEN-PER118')->group(function () {
            Route::get('/echeances', [CreditController::class, 'echeances'])->name('echeances');
            Route::get('/echeances/print', [CreditController::class, 'printEcheances'])->name('echeances.print');
        });

        // ── Grille de commissions crédit (Admin/Gérant uniquement) ──
        Route::middleware('permission:EBEN-PER63')->prefix('commissions')->name('commissions.')->group(function () {
            Route::get('/', [CreditCommissionController::class, 'index'])->name('index');
            Route::post('/', [CreditCommissionController::class, 'store'])->name('store');
            Route::put('/{rule}', [CreditCommissionController::class, 'update'])->name('update');
            Route::delete('/{rule}', [CreditCommissionController::class, 'destroy'])->name('destroy');
        });

        // ── Création ─────────────────────────────────────────────────
        Route::middleware('permission:EBEN-PER54')->group(function () {
            Route::get('/nouveau',  [CreditController::class, 'create'])->name('create');
            Route::post('/', [CreditController::class, 'store'])->name('store');
        });

        // ── Édition brouillon (PER55) ─────────────────────────────
        Route::middleware('permission:EBEN-PER55')->group(function () {
            Route::get('/{dossier}/editer',  [CreditController::class, 'edit'])->name('edit');
            Route::put('/{dossier}/editer',  [CreditController::class, 'update'])->name('update');
        });

        // ── AJAX helpers (utilisés par le formulaire de création, PER54) ──
        Route::middleware('permission:EBEN-PER53|EBEN-PER54|EBEN-PER55')->group(function () {
            Route::get('/ajax/comptes-client',  [CreditController::class, 'getComptesClient'])->name('ajax.comptes_client');
            Route::get('/ajax/simuler',         [CreditController::class, 'simuler'])->name('ajax.simuler');
        });

        // ── Rapport frais déblocage (AVANT /{dossier}) ──────────────
        Route::middleware('permission:EBEN-PER70')->group(function () {
            Route::get('/rapport-frais', [CreditController::class, 'rapportFrais'])->name('rapport_frais');
        });

        // ── Détail d'un dossier ───────────────────────────────────────
        Route::middleware('permission:EBEN-PER57')->group(function () {
            Route::get('/{dossier}', [CreditController::class, 'show'])->name('show');
        });

        // ── Pièces justificatives (EBEN-PER73) ─────────────────────────
        Route::middleware('permission:EBEN-PER73')->group(function () {
            Route::post('/{dossier}/pieces/{piece}', [CreditController::class, 'updatePiece'])->name('pieces.update');
        });
        Route::middleware('permission:EBEN-PER57')->group(function () {
            Route::get('/{dossier}/pieces/{piece}/fichier', [CreditController::class, 'piecesFichier'])->name('pieces.fichier');
        });

        // ── Prélèvement auto toggle (EBEN-PER113 = modification config crédit) ──
        Route::middleware('permission:EBEN-PER113')->post('/{dossier}/toggle-prelevement-auto', [CreditController::class, 'togglePrelevementAuto'])
            ->name('toggle.prelevement.auto');

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
        Route::middleware('permission:EBEN-PER10|EBEN-PER111')->group(function () {
            Route::get('/{dossier}/remboursement', [CreditController::class, 'remboursement'])->name('remboursement');
        });

        Route::middleware('permission:EBEN-PER10|EBEN-PER111')->group(function () {
            Route::post('/{dossier}/remboursement', [CreditController::class, 'storeRemboursement'])->name('remboursement.store');
            Route::post('/{dossier}/reglement-auto', [CreditController::class, 'reglementAutoEcheance'])->name('reglement.auto.echeance');
        });

        // ─ Actions transverses ───────────────────────────────────────
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

// Relevé de compte crédit - permission Caisse/Guichet (indépendant de EBEN-PER53)
Route::middleware(['auth', 'permission:EBEN-PER112'])
    ->prefix('credits')
    ->name('credit.')
    ->group(function () {
        Route::get('/{dossier}/pdf/releve', [CreditController::class, 'releveCredit'])->name('pdf.releve');
    });
