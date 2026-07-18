<?php

namespace App\Services\Comptabilite;

use App\Models\Caisse\Transaction;
use App\Models\Comptabilite\JournalComptable;

class OhadaAccountingService
{
    public function postTransaction(Transaction $transaction, array $overrides = []): ?JournalComptable
    {
        return $this->createJournal($transaction, 'OPERATION', $overrides, false);
    }

    public function postReversal(Transaction $transaction, string $motif = 'Annulation operation', array $overrides = []): ?JournalComptable
    {
        $overrides['motif'] = $motif;
        return $this->createJournal($transaction, 'ANNULATION', $overrides, true);
    }

    private function createJournal(Transaction $transaction, string $typePiece, array $overrides, bool $reverse): ?JournalComptable
    {
        // Sécurité comptable : interdire toute nouvelle écriture sur un exercice CLOTURE
        $dateEcriture = $transaction->date_operation ?? now();
        $exercice = \App\Models\Comptabilite\ExerciceComptable::pourDate($dateEcriture);
        if ($exercice && $exercice->estCloture()) {
            throw new \RuntimeException(
                "Impossible d'enregistrer cette écriture : l'exercice comptable {$exercice->annee} est clôturé. " .
                "Contactez le service comptabilité."
            );
        }

        $montant = isset($overrides['montant']) ? (float) $overrides['montant'] : (float) $transaction->montant;
        $commission = isset($overrides['commission']) ? (float) $overrides['commission'] : (float) ($transaction->montant_commission_total ?? 0);
        $devise = $overrides['devise_code'] ?? $transaction->devise_code;
        $montantDest = isset($overrides['montant_dest']) ? (float) $overrides['montant_dest'] : (float) ($transaction->montant_dest ?? 0);
        $deviseDest = $overrides['devise_dest'] ?? $transaction->devise_dest;

        $lines = $this->buildLines($transaction, $montant, $commission, $devise, $montantDest, $deviseDest, $overrides);
        if (empty($lines)) {
            return null;
        }

        if ($reverse) {
            $lines = array_map(function (array $line) {
                $debit = (float) ($line['debit'] ?? 0);
                $credit = (float) ($line['credit'] ?? 0);
                $line['debit'] = $credit;
                $line['credit'] = $debit;
                return $line;
            }, $lines);
        }

        $reference = $this->buildReference($transaction->reference, $typePiece);
        $motif = trim((string) ($overrides['motif'] ?? ''));

        $journal = JournalComptable::create([
            'code_journal' => 'CAI',
            'reference_piece' => $reference,
            'transaction_id' => $transaction->id,
            'type_piece' => $typePiece,
            'devise_code' => $devise,
            'libelle' => $motif !== ''
                ? ($motif . ' - ' . Transaction::typeLabel($transaction->type) . ' ' . $transaction->reference)
                : (Transaction::typeLabel($transaction->type) . ' ' . $transaction->reference),
            'statut' => 'COMPTABILISE',
            'agent_matricule' => $overrides['agent_matricule'] ?? $transaction->agent_matricule,
            'date_ecriture' => now(),
            'metadata' => [
                'transaction_reference' => $transaction->reference,
                'type_operation' => $transaction->type,
                'montant' => $montant,
                'commission' => $commission,
                'commission_trace' => $overrides['commission_trace'] ?? null,
                'reverse' => $reverse,
            ],
        ]);

        foreach ($lines as $index => $line) {
            $journal->ecritures()->create([
                'numero_compte' => $line['numero_compte'],
                'devise_code' => $line['devise_code'] ?? $devise,
                'libelle_ligne' => $line['libelle_ligne'] ?? $journal->libelle,
                'debit' => round((float) ($line['debit'] ?? 0), 2),
                'credit' => round((float) ($line['credit'] ?? 0), 2),
                'ordre' => $index + 1,
            ]);
        }

        return $journal;
    }

    private function buildLines(Transaction $transaction, float $montant, float $commission, ?string $devise, float $montantDest, ?string $deviseDest, array $overrides = []): array
    {
        if ($montant <= 0) {
            return [];
        }

        $caisseCompteSource = $this->resolveCashAccount($devise);
        $compteDepotClient = '2511';
        $compteProduitCommission = '7061';
        $compteProduitService = '7071';
        $compteChargeRemboursement = '6001';
        $compteTransitoireChange = '4711';

        $lines = [];

        switch ($transaction->type) {
            case Transaction::DEPOT:
                $lines[] = $this->line($caisseCompteSource, $devise, 'Encaissement depot client', $montant, 0);
                $lines[] = $this->line($compteDepotClient, $devise, 'Augmentation depot client', 0, $montant);
                if ($transaction->compte_code && $commission > 0) {
                    $lines[] = $this->line($compteDepotClient, $devise, 'Commission sur depot - debit client', $commission, 0);
                    $lines[] = $this->line($compteProduitCommission, $devise, 'Produit commission depot', 0, $commission);
                }
                break;

            case Transaction::RETRAIT:
                $lines[] = $this->line($compteDepotClient, $devise, 'Diminution depot client', $montant, 0);
                $lines[] = $this->line($caisseCompteSource, $devise, 'Decaissement retrait client', 0, $montant);
                if ($transaction->compte_code && $commission > 0) {
                    $lines[] = $this->line($compteDepotClient, $devise, 'Commission sur retrait - debit client', $commission, 0);
                    $lines[] = $this->line($compteProduitCommission, $devise, 'Produit commission retrait', 0, $commission);
                }
                break;

            case Transaction::PAIEMENT:
                $lines[] = $this->line($caisseCompteSource, $devise, 'Encaissement paiement service', $montant, 0);
                $lines[] = $this->line($compteProduitService, $devise, 'Produit service guichet', 0, $montant);
                break;

            case Transaction::REMBOURSEMENT:
                $lines[] = $this->line($compteChargeRemboursement, $devise, 'Charge remboursement guichet', $montant, 0);
                $lines[] = $this->line($caisseCompteSource, $devise, 'Decaissement remboursement', 0, $montant);
                break;

            case Transaction::DEPENSE:
                // Compte de charge déterminé dynamiquement par la catégorie de dépense
                // (voir CategorieDepense::numero_compte_charge) — jamais codé en dur.
                $compteCharge = $overrides['compte_charge'] ?? '6581'; // repli : autres charges diverses
                $lines[] = $this->line($compteCharge, $devise, 'Charge - dépense de caisse', $montant, 0);
                $lines[] = $this->line($caisseCompteSource, $devise, 'Décaissement dépense', 0, $montant);
                break;

            case Transaction::RECETTE:
                // Compte de produit déterminé dynamiquement par la catégorie de recette
                // (voir CategorieRecette::numero_compte_produit) — jamais codé en dur.
                $compteProduit = $overrides['compte_produit'] ?? '7581'; // repli : autres produits divers
                $lines[] = $this->line($caisseCompteSource, $devise, 'Encaissement recette de caisse', $montant, 0);
                $lines[] = $this->line($compteProduit, $devise, 'Produit - recette de caisse', 0, $montant);
                break;

            case Transaction::CHANGE:
                $caisseCompteDest = $this->resolveCashAccount($deviseDest);
                $effectiveDest = $montantDest > 0 ? $montantDest : $montant;

                $lines[] = $this->line($caisseCompteSource, $devise, 'Entree devise source', $montant, 0);
                $lines[] = $this->line($compteTransitoireChange, $devise, 'Contrepartie change devise source', 0, $montant);
                $lines[] = $this->line($compteTransitoireChange, $deviseDest ?: $devise, 'Contrepartie change devise destination', $effectiveDest, 0);
                $lines[] = $this->line($caisseCompteDest, $deviseDest ?: $devise, 'Sortie devise destination', 0, $effectiveDest);
                break;
        }

        return $lines;
    }

    private function line(string $compte, ?string $devise, string $libelle, float $debit, float $credit): array
    {
        return [
            'numero_compte' => $compte,
            'devise_code' => $devise,
            'libelle_ligne' => $libelle,
            'debit' => $debit,
            'credit' => $credit,
        ];
    }

    private function resolveCashAccount(?string $devise): string
    {
        return match (strtoupper((string) $devise)) {
            'USD' => '5702',
            'EUR' => '5703',
            default => '5701',
        };
    }

    private function buildReference(?string $transactionReference, string $typePiece): string
    {
        $base = $transactionReference ?: 'OP';
        $suffix = now()->format('YmdHisv');
        $prefix = match ($typePiece) {
            'ANNULATION' => 'ANL',
            'REGULARISATION' => 'REG',
            default => 'CPT',
        };

        return substr($prefix . '-' . $base . '-' . $suffix, 0, 80);
    }
}
