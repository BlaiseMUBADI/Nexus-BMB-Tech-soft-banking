<?php

use App\Models\Caisse\ClotureCaisse;
use App\Models\Caisse\DemandeModification;
use App\Models\Caisse\MouvementInterCaisse;
use App\Models\Credit\CreditDeblocage;
use App\Models\Credit\CreditDemande;
use App\Models\Credit\CreditEcheance;
use App\Models\Caisse\Transaction;
use App\Models\RH\Agent;
use App\Models\User;
use App\Services\Notifications\NotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('credit:backfill-deblocage-transactions {--dry-run} {--deblocage-id=*}', function () {
    $dryRun = (bool) $this->option('dry-run');
    $ids = collect($this->option('deblocage-id'))->filter()->map(fn ($v) => (int) $v)->values();

    $query = CreditDeblocage::query()
        ->with('guichetSolde')
        ->orderBy('id');

    if ($ids->isNotEmpty()) {
        $query->whereIn('id', $ids->all());
    }

    $deblocages = $query->get();

    if ($deblocages->isEmpty()) {
        $this->warn('Aucun déblocage trouvé pour les critères fournis.');
        return;
    }

    $agentFallback = Agent::query()->value('matricule');
    $created = 0;
    $skipped = 0;

    foreach ($deblocages as $d) {
        $montant = (float) $d->montant_debloque;
        if ($montant <= 0 || empty($d->compte_credit_id)) {
            $skipped++;
            $this->line("[SKIP] Déblocage #{$d->id} invalide (montant/compte).");
            continue;
        }

        $netVerse = round($montant * 0.80, 2);
        $frais = round($montant * 0.04, 2);

        $dateOp = $d->debloque_le
            ? Carbon::parse($d->debloque_le)
            : Carbon::now();

        $refDepot = 'DEB-' . $d->id . '-D';
        $refFrais = 'DEB-' . $d->id . '-F';

        $agentMatricule = $d->agent_matricule ?: $agentFallback;
        if (empty($agentMatricule)) {
            $skipped++;
            $this->line("[SKIP] Déblocage #{$d->id}: aucun agent disponible pour respecter la FK tb_transactions.");
            continue;
        }

        $payloadDepot = [
            'compte_code' => $d->compte_credit_id,
            'agent_matricule' => $agentMatricule,
            'guichet_id' => $d->guichetSolde?->guichet_id,
            'devise_code' => $d->devise,
            'type' => Transaction::DEPOT,
            'montant' => $netVerse,
            'montant_commission_total' => 0,
            'solde_compte_avant' => null,
            'solde_compte_apres' => null,
            'montant_total_client' => $netVerse,
            'montant_net_client' => $netVerse,
            'reference' => $refDepot,
            'observations' => 'Backfill deblocage credit #' . $d->id . ' (80% RMB)',
            'statut' => Transaction::CONFIRME,
            'date_operation' => $dateOp,
        ];

        $payloadFrais = [
            'compte_code' => $d->compte_credit_id,
            'agent_matricule' => $agentMatricule,
            'guichet_id' => $d->guichetSolde?->guichet_id,
            'devise_code' => $d->devise,
            'type' => Transaction::RETRAIT,
            'montant' => $frais,
            'montant_commission_total' => 0,
            'solde_compte_avant' => null,
            'solde_compte_apres' => null,
            'montant_total_client' => $frais,
            'montant_net_client' => $frais,
            'reference' => $refFrais,
            'observations' => 'Backfill frais deblocage credit #' . $d->id . ' (4%)',
            'statut' => Transaction::CONFIRME,
            'date_operation' => $dateOp,
        ];

        if ($dryRun) {
            $existsDepot = Transaction::where('reference', $refDepot)->exists();
            $existsFrais = Transaction::where('reference', $refFrais)->exists();
            $this->line("[DRY] Déblocage #{$d->id} | depot=" . ($existsDepot ? 'existe' : 'a_creer') . ' | frais=' . ($existsFrais ? 'existe' : 'a_creer'));
            continue;
        }

        DB::transaction(function () use ($refDepot, $payloadDepot, $refFrais, $payloadFrais, &$created) {
            $txDepot = Transaction::where('reference', $refDepot)->first();
            if (!$txDepot) {
                Transaction::create($payloadDepot);
                $created++;
            }

            $txFrais = Transaction::where('reference', $refFrais)->first();
            if (!$txFrais) {
                Transaction::create($payloadFrais);
                $created++;
            }
        });

        $this->line("[OK] Déblocage #{$d->id} régularisé (refs {$refDepot} / {$refFrais}).");
    }

    if ($dryRun) {
        $this->info('Simulation terminée. Relancez sans --dry-run pour appliquer.');
        return;
    }

    $this->info("Régularisation terminée: {$created} écriture(s) créée(s), {$skipped} déblocage(s) ignoré(s).");
})->purpose('Backfill historique RMB (depot + frais) pour les déblocages crédit déjà traités');

// ══════════════════════════════════════════════════════════════
//  NOTIFICATIONS PROACTIVES — à lancer via le scheduler toutes les heures
//  php artisan notifications:proactive [--dry-run]
// ══════════════════════════════════════════════════════════════
Artisan::command('notifications:proactive {--dry-run}', function () {
    $dryRun  = (bool) $this->option('dry-run');
    $service = app(NotificationService::class);
    $sent    = 0;

    $this->info('[Proactive] Démarrage — ' . now()->toDateTimeString() . ($dryRun ? ' [DRY-RUN]' : ''));

    // ─── 1. Échéances crédit en retard ───────────────────────────────────
    // Identifie les dossiers EN_REMBOURSEMENT ayant au moins une échéance
    // dont la date est dépassée et le statut encore EN_ATTENTE / EN_RETARD.
    $echeancesEnRetard = CreditEcheance::whereIn('statut', ['EN_ATTENTE', 'EN_RETARD'])
        ->where('date_echeance', '<', now()->toDateString())
        ->with(['echeancier.demande' => function ($q) {
            $q->whereIn('statut_global', ['EN_REMBOURSEMENT', 'EN_RETARD'])
              ->whereNotNull('agent_createur_matricule');
        }])
        ->limit(200)
        ->get()
        ->filter(fn ($e) => $e->echeancier?->demande !== null);

    $dossiersAlertes = $echeancesEnRetard
        ->groupBy(fn ($e) => $e->echeancier->demande->id);

    foreach ($dossiersAlertes as $dossierId => $echeances) {
        $dossier      = $echeances->first()->echeancier->demande;
        $nbRetard     = $echeances->count();
        $plusVieille  = $echeances->min('date_echeance');
        $joursRetard  = (int) now()->diffInDays(Carbon::parse($plusVieille));

        $message = sprintf(
            'Le dossier %s a %d échéance(s) en retard (depuis %d jour(s)). Dernière date prévue : %s.',
            $dossier->numero_dossier,
            $nbRetard,
            $joursRetard,
            Carbon::parse($plusVieille)->format('d/m/Y')
        );

        $acteurs = User::whereIn('agent_matricule', array_filter([
            $dossier->agent_createur_matricule,
            $dossier->agent_analyse_matricule,
        ]))->get();

        if (!$dryRun) {
            $service->notifyUsers($acteurs, 'Échéance(s) crédit en retard', $message, [
                'type'       => 'danger',
                'category'   => 'credit',
                'icon'       => 'fas fa-exclamation-circle',
                'action_url' => route('credit.remboursement', $dossier),
            ]);
            $service->notifyUsersWithPermission('EBEN-PER111', 'Échéance(s) crédit en retard', $message, [
                'type'       => 'warning',
                'category'   => 'credit',
                'icon'       => 'fas fa-exclamation-circle',
                'action_url' => route('credit.remboursement', $dossier),
            ]);
        }
        $sent++;
        $this->line("[CREDIT-RETARD] Dossier #{$dossier->numero_dossier} — {$nbRetard} échéance(s) en retard.");
    }

    // ─── 2. Demandes de ravitaillement EN_ATTENTE depuis plus de 4h ──────
    $demandesStales = MouvementInterCaisse::where('type_flux', 'DEMANDE_APPRO')
        ->where('statut', 'EN_ATTENTE')
        ->where('date_mouvement', '<', now()->subHours(4))
        ->limit(50)
        ->get();

    if ($demandesStales->isNotEmpty()) {
        $count = $demandesStales->count();
        $message = "{$count} demande(s) de ravitaillement en attente depuis plus de 4 heures.";
        if (!$dryRun) {
            $service->notifyUsersWithPermission('EBEN-PER46', 'Demandes ravitaillement non traitées', $message, [
                'type'       => 'warning',
                'category'   => 'tresorerie',
                'icon'       => 'fas fa-clock',
                'action_url' => route('tresorerie.etat-coffre'),
            ]);
        }
        $sent++;
        $this->line("[RAVIT-STALE] {$count} demande(s) en attente depuis > 4h.");
    }

    // ─── 3. Dotations mobiles EN_ATTENTE depuis plus de 2h ───────────────
    $dotationsStales = MouvementInterCaisse::where('type_flux', 'DOTATION_MOBILE')
        ->where('statut', 'EN_ATTENTE')
        ->where('date_mouvement', '<', now()->subHours(2))
        ->limit(50)
        ->get();

    if ($dotationsStales->isNotEmpty()) {
        $count = $dotationsStales->count();
        $message = "{$count} demande(s) de dotation mobile en attente depuis plus de 2 heures.";
        if (!$dryRun) {
            $service->notifyUsersWithPermission('EBEN-PER46', 'Dotations mobiles non traitées', $message, [
                'type'       => 'warning',
                'category'   => 'caisse',
                'icon'       => 'fas fa-clock',
                'action_url' => route('tresorerie.etat-coffre'),
            ]);
        }
        $sent++;
        $this->line("[DOT-STALE] {$count} dotation(s) mobile en attente depuis > 2h.");
    }

    // ─── 4. Clôtures guichet EN_VERIFICATION depuis plus de 2h ──────────
    $cloturesStales = ClotureCaisse::where('statut_validation', ClotureCaisse::VALIDATION_EN_ATTENTE)
        ->where('date_cloture', '<', now()->subHours(2))
        ->limit(50)
        ->get();

    if ($cloturesStales->isNotEmpty()) {
        $count = $cloturesStales->count();
        $message = "{$count} clôture(s) guichet en attente de validation superviseur depuis plus de 2 heures.";
        if (!$dryRun) {
            $service->notifyUsersWithPermission('EBEN-PER46', 'Clôtures guichet en attente', $message, [
                'type'       => 'warning',
                'category'   => 'caisse',
                'icon'       => 'fas fa-hourglass-half',
                'action_url' => route('tresorerie.etat-coffre'),
            ]);
        }
        $sent++;
        $this->line("[CLOTURE-STALE] {$count} clôture(s) en attente depuis > 2h.");
    }

    // ─── 5. Demandes de modification EN_ATTENTE depuis plus de 4h ────────
    $modifStales = DemandeModification::where('statut', DemandeModification::EN_ATTENTE)
        ->where('created_at', '<', now()->subHours(4))
        ->limit(50)
        ->get();

    if ($modifStales->isNotEmpty()) {
        $count = $modifStales->count();
        $message = "{$count} demande(s) de modification d'opération non traitée(s) depuis plus de 4 heures.";
        if (!$dryRun) {
            $service->notifyUsersWithPermission('EBEN-PER44', 'Demandes modification non traitées', $message, [
                'type'       => 'warning',
                'category'   => 'caisse',
                'icon'       => 'fas fa-clock',
                'action_url' => route('caisses.demandes.modification.page'),
            ]);
        }
        $sent++;
        $this->line("[MODIF-STALE] {$count} demande(s) modification en attente depuis > 4h.");
    }

    // ─── 6. Dossiers crédit SOUMIS non affectés depuis plus de 24h ───────
    $dossiersNonAffectes = CreditDemande::where('statut_global', 'SOUMIS')
        ->whereNull('agent_analyse_matricule')
        ->where('soumis_le', '<', now()->subHours(24))
        ->limit(50)
        ->get();

    if ($dossiersNonAffectes->isNotEmpty()) {
        $count = $dossiersNonAffectes->count();
        $message = "{$count} dossier(s) crédit soumis n'ont pas encore été affectés à un agent de crédit après 24 heures.";
        if (!$dryRun) {
            $service->notifyUsersWithPermission('EBEN-PER61', 'Dossiers crédit non affectés', $message, [
                'type'       => 'warning',
                'category'   => 'credit',
                'icon'       => 'fas fa-file-exclamation',
                'action_url' => route('credit.index'),
            ]);
        }
        $sent++;
        $this->line("[CREDIT-STALE] {$count} dossier(s) soumis non affectés depuis > 24h.");
    }

    $this->info("[Proactive] Terminé — {$sent} type(s) d'alerte envoyé(s)." . ($dryRun ? ' [DRY-RUN — aucun envoi réel]' : ''));
})->purpose('Envoie les notifications proactives (retards, demandes stales). À planifier toutes les heures.');

