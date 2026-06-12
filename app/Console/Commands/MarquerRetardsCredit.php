<?php

namespace App\Console\Commands;

use App\Models\Credit\CreditDemande;
use App\Models\Credit\CreditAudit;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Commande planifiée : marquer les crédits/échéances en retard.
 *
 * Lance : php artisan credit:marquer-retards [--dry-run]
 * Planifier dans bootstrap/app.php ou dans le kernel avec ->daily()
 *
 * Actions :
 *  1. Pour chaque dossier EN_REMBOURSEMENT ou DEBLOQUE :
 *     – Passe les échéances EN_ATTENTE/PARTIELLEMENT_PAYE dépassées → EN_RETARD
 *     – Si au moins une échéance est en retard : dossier → EN_RETARD
 *     – Si toutes les échéances non-payées sont à jour : dossier → EN_REMBOURSEMENT
 *  2. Pour les dossiers déjà EN_RETARD :
 *     – Vérifie si les retards sont résolus et revient à EN_REMBOURSEMENT
 */
class MarquerRetardsCredit extends Command
{
    protected $signature   = 'credit:marquer-retards {--dry-run : Simuler sans modifier la base}';
    protected $description = 'Marque automatiquement les échéances crédit dépassées comme EN_RETARD et met à jour les statuts des dossiers.';

    public function handle(): int
    {
        $dryRun   = (bool) $this->option('dry-run');
        $today    = Carbon::today()->toDateString();
        $modifies = 0;
        $erreurs  = 0;

        $this->info(sprintf(
            '[%s] Début du traitement des retards crédit%s…',
            now()->format('Y-m-d H:i:s'),
            $dryRun ? ' (DRY-RUN)' : ''
        ));

        // ── Dossiers actifs (pouvant avoir des retards) ──────────────────
        $dossiers = CreditDemande::query()
            ->whereIn('statut_global', ['DEBLOQUE', 'EN_REMBOURSEMENT', 'EN_RETARD'])
            ->where('est_annule', false)
            ->with(['echeancier.echeances'])
            ->get();

        $this->line("  Dossiers actifs trouvés : {$dossiers->count()}");

        foreach ($dossiers as $dossier) {
            try {
                DB::transaction(function () use ($dossier, $today, $dryRun, &$modifies) {
                    $echeancier = $dossier->echeancier;
                    if (!$echeancier) {
                        return;
                    }

                    $echeances = $echeancier->echeances;
                    $aDesRetards = false;
                    $echRetardsNouveaux = 0;

                    foreach ($echeances as $echeance) {
                        // Sauter les échéances déjà payées
                        if ($echeance->statut === 'PAYE') {
                            continue;
                        }

                        $dateEcheance = $echeance->date_echeance instanceof Carbon
                            ? $echeance->date_echeance->toDateString()
                            : (string) $echeance->date_echeance;

                        $estDepassee = $dateEcheance < $today;

                        if ($estDepassee && $echeance->statut !== 'EN_RETARD') {
                            // Passer l'échéance en retard
                            if (!$dryRun) {
                                $echeance->update(['statut' => 'EN_RETARD']);
                            }
                            $echRetardsNouveaux++;
                            $aDesRetards = true;
                        } elseif ($estDepassee && $echeance->statut === 'EN_RETARD') {
                            $aDesRetards = true;
                        }
                    }

                    // ── Mise à jour du statut du dossier ───────────────────
                    $statutActuel  = $dossier->statut_global;
                    $nouveauStatut = null;

                    if ($aDesRetards && $statutActuel !== 'EN_RETARD') {
                        $nouveauStatut = 'EN_RETARD';
                    } elseif (!$aDesRetards && $statutActuel === 'EN_RETARD') {
                        $nouveauStatut = 'EN_REMBOURSEMENT';
                    }

                    if ($nouveauStatut !== null) {
                        if (!$dryRun) {
                            $dossier->update(['statut_global' => $nouveauStatut]);

                            // Trace audit
                            CreditAudit::create([
                                'credit_demande_id' => $dossier->id,
                                'acteur_matricule'  => 'SYSTEME',
                                'type_action'       => $nouveauStatut === 'EN_RETARD' ? 'PASSAGE_RETARD' : 'REMBOURSEMENT',
                                'ancien_statut'     => $statutActuel,
                                'nouveau_statut'    => $nouveauStatut,
                                'details'           => $nouveauStatut === 'EN_RETARD'
                                    ? "Marquage automatique retard : {$echRetardsNouveaux} échéance(s) dépassée(s)."
                                    : "Levée automatique du retard : toutes les échéances dues sont à jour.",
                                'ip_address'        => '127.0.0.1',
                            ]);
                        }
                        $modifies++;

                        $this->line(sprintf(
                            '  [%s] %s → %s → %s%s',
                            $dossier->numero_dossier,
                            $dossier->client?->nom . ' ' . $dossier->client?->prenom,
                            $statutActuel,
                            $nouveauStatut,
                            $dryRun ? ' (DRY-RUN)' : ''
                        ));
                    } elseif ($echRetardsNouveaux > 0) {
                        $this->line(sprintf(
                            '  [%s] %s échéance(s) passée(s) EN_RETARD',
                            $dossier->numero_dossier,
                            $echRetardsNouveaux
                        ));
                        $modifies++;
                    }
                });
            } catch (\Throwable $e) {
                $erreurs++;
                $this->error("  Erreur sur dossier {$dossier->numero_dossier} : " . $e->getMessage());
                Log::error('[credit:marquer-retards] Erreur', [
                    'dossier' => $dossier->numero_dossier,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        $this->info(sprintf(
            '[%s] Terminé. Dossiers/échéances modifiés : %d | Erreurs : %d',
            now()->format('Y-m-d H:i:s'),
            $modifies,
            $erreurs
        ));

        return $erreurs > 0 ? self::FAILURE : self::SUCCESS;
    }
}
