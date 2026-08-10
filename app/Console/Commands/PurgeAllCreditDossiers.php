<?php

namespace App\Console\Commands;

use App\Models\Credit\CreditDemande;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Supprime DÉFINITIVEMENT tous les dossiers de crédit et toutes leurs
 * données liées (analyses, validations, pièces, déblocages, échéanciers,
 * échéances, remboursements, audits).
 *
 * ⚠️ DESTRUCTIF ET IRRÉVERSIBLE — aucune restauration possible après coup
 * (les modèles Credit n'utilisent PAS SoftDeletes, ce sont des suppressions
 * définitives en base). Toujours faire une sauvegarde de la base avant.
 *
 * N'affecte PAS :
 *  - tb_credit_commission_rules (configuration globale des commissions,
 *    pas des données de dossier)
 *  - Les clients, comptes, agents, zones, portefeuilles (parents, non
 *    supprimés par cette commande)
 *
 * Usage :
 *   php artisan credit:purge-all --dry-run   (simulation, aucune écriture)
 *   php artisan credit:purge-all             (demande confirmation)
 *   php artisan credit:purge-all --force     (sans confirmation - scripts/CI)
 */
class PurgeAllCreditDossiers extends Command
{
    protected $signature = 'credit:purge-all
        {--dry-run : Afficher ce qui serait supprimé sans rien modifier}
        {--force : Ne pas demander de confirmation}';

    protected $description = 'Supprime définitivement TOUS les dossiers de crédit et leurs données liées (irréversible).';

    /**
     * Ordre de suppression (enfants → parent) pour respecter les
     * contraintes de clé étrangère. Certaines tables sont en
     * "restrictOnDelete" (déblocages, remboursements) et DOIVENT être
     * vidées manuellement avant tb_credit_demandes ; les autres sont en
     * cascadeOnDelete mais on les vide aussi explicitement pour rester
     * indépendant du moteur de base de données.
     */
    private const TABLES_ENFANTS = [
        'tb_credit_remboursements' => 'credit_demande_id',
        'tb_credit_deblocages'     => 'credit_demande_id',
        'tb_credit_pieces'         => 'credit_demande_id',
        'tb_credit_validations'    => 'credit_demande_id',
        'tb_credit_analyses'       => 'credit_demande_id',
        'tb_credit_audits'         => 'credit_demande_id',
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $totalDossiers = CreditDemande::count();

        if ($totalDossiers === 0) {
            $this->info('Aucun dossier de crédit à supprimer.');
            return self::SUCCESS;
        }

        $this->warn("⚠️  Cette opération va supprimer DÉFINITIVEMENT {$totalDossiers} dossier(s) de crédit et TOUTES leurs données liées :");
        $this->line('   - échéanciers et échéances');
        $this->line('   - remboursements');
        $this->line('   - déblocages');
        $this->line('   - analyses, validations, pièces jointes (métadonnées)');
        $this->line('   - journal d\'audit crédit');
        $this->newLine();

        if (!$dryRun && !$this->option('force')) {
            if (!$this->confirm('Êtes-vous ABSOLUMENT certain de vouloir continuer ? Cette action est irréversible.')) {
                $this->info('Opération annulée.');
                return self::SUCCESS;
            }

            if ($this->ask('Tapez "SUPPRIMER" en majuscules pour confirmer définitivement') !== 'SUPPRIMER') {
                $this->error('Confirmation invalide. Opération annulée.');
                return self::SUCCESS;
            }
        }

        $dossierIds = CreditDemande::pluck('id');

        // Les échéances dépendent de tb_credit_echeanciers, elles-mêmes
        // liées au dossier — on les gère séparément (jointure indirecte).
        $echeancierIds = DB::table('tb_credit_echeanciers')
            ->whereIn('credit_demande_id', $dossierIds)
            ->pluck('id');

        $this->newLine();
        $this->info($dryRun ? '[DRY-RUN] Simulation — aucune donnée ne sera modifiée :' : 'Suppression en cours...');

        $counts = [];

        DB::transaction(function () use ($dossierIds, $echeancierIds, $dryRun, &$counts) {
            // 1. Échéances (dépendent des échéanciers, pas directement du dossier)
            $counts['tb_credit_echeances'] = DB::table('tb_credit_echeances')
                ->whereIn('echeancier_id', $echeancierIds)
                ->count();
            if (!$dryRun) {
                DB::table('tb_credit_echeances')->whereIn('echeancier_id', $echeancierIds)->delete();
            }

            // 2. Tables directement liées à credit_demande_id (restrict + cascade confondus)
            foreach (self::TABLES_ENFANTS as $table => $fk) {
                $counts[$table] = DB::table($table)->whereIn($fk, $dossierIds)->count();
                if (!$dryRun) {
                    DB::table($table)->whereIn($fk, $dossierIds)->delete();
                }
            }

            // 3. Échéanciers (après les échéances qui en dépendent)
            $counts['tb_credit_echeanciers'] = DB::table('tb_credit_echeanciers')
                ->whereIn('credit_demande_id', $dossierIds)
                ->count();
            if (!$dryRun) {
                DB::table('tb_credit_echeanciers')->whereIn('credit_demande_id', $dossierIds)->delete();
            }

            // 4. Enfin, les dossiers eux-mêmes
            $counts['tb_credit_demandes'] = $dossierIds->count();
            if (!$dryRun) {
                DB::table('tb_credit_demandes')->whereIn('id', $dossierIds)->delete();
            }
        });

        foreach ($counts as $table => $n) {
            $this->line(sprintf('  %-28s : %d ligne(s)', $table, $n));
        }

        $this->newLine();
        $this->info($dryRun
            ? 'Simulation terminée — relancez sans --dry-run pour appliquer réellement.'
            : "Terminé. {$counts['tb_credit_demandes']} dossier(s) de crédit et leurs données liées ont été supprimés définitivement.");

        return self::SUCCESS;
    }
}
