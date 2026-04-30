<?php

use App\Models\Credit\CreditDeblocage;
use App\Models\Caisse\Transaction;
use App\Models\RH\Agent;
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
