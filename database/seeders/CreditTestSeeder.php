<?php

namespace Database\Seeders;

use App\Models\Clients\Compte;
use App\Models\Credit\CreditAnalyse;
use App\Models\Credit\CreditAudit;
use App\Models\Credit\CreditDeblocage;
use App\Models\Credit\CreditDemande;
use App\Models\Credit\CreditEcheance;
use App\Models\Credit\CreditEcheancier;
use App\Models\Credit\CreditPiece;
use App\Models\Credit\CreditRemboursement;
use App\Models\Credit\CreditValidation;
use App\Services\Credit\AmortissementService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * CreditTestSeeder — Jeu de données de test complet
 *
 * Crée 20 dossiers de crédit couvrant TOUS les scénarios métier :
 *  1.  BROUILLON (dossier ouvert, non soumis)
 *  2.  SOUMIS (en attente d'affectation)
 *  3.  EN_ANALYSE (agent affecté, analyse en cours)
 *  4.  EN_ANALYSE – analyse sauvée EN_COURS (pas encore complète)
 *  5.  EN_VALIDATION – bloc 1 (Agent crédit) en attente
 *  6.  EN_VALIDATION – bloc 1 validé, bloc 2 (Contrôleur) en attente
 *  7.  EN_VALIDATION – blocs 1+2 validés, bloc 3 (ChOps) en attente
 *  8.  EN_VALIDATION – blocs 1+2+3 validés, bloc 4 (Gérant) en attente
 *  9.  PRET_A_DEBLOQUER (4 validations OK)
 * 10.  DEBLOQUE (déblocage effectué, pas encore de remboursement)
 * 11.  EN_REMBOURSEMENT – 1 paiement enregistré (normal)
 * 12.  EN_REMBOURSEMENT – plusieurs paiements (partiel + normal)
 * 13.  EN_REMBOURSEMENT – paiement partiel en cours
 * 14.  EN_RETARD – échéances dépassées non payées
 * 15.  EN_RETARD – avec pénalités appliquées
 * 16.  SOLDE – tous les remboursements effectués
 * 17.  SOLDE – remboursement anticipé (ANTICIPE)
 * 18.  ANNULE – rejet lors de la validation
 * 19.  SUSPENDU – suspension administrative
 * 20.  SUSPECT – signalé suspect par la direction
 *
 * Lance : php artisan db:seed --class=CreditTestSeeder
 */
class CreditTestSeeder extends Seeder
{
    private AmortissementService $amortissement;

    // ── Constantes de référence ──────────────────────────────────────────
    private const PORTEFEUILLE_ID      = 3;
    private const ZONE_CODE            = 'ZON-EBENKGA-26-00003';
    private const COFFRE_SOLDE_ID      = 1;   // CDF – Coffre central
    private const COFFRE_SOLDE_USD_ID  = 3;   // USD – Coffre central
    private const GUICHET_ID           = 1;   // Coffre-Fort Central

    // Agents de test (créés par la migration 2026_04_28_000005)
    private const AG_DEMANDEUR    = 'AG-CRD-TST-0001';
    private const AG_CHARGE_OPS   = 'AG-CRD-TST-0002';
    private const AG_CREDIT       = 'AG-CRD-TST-0003';
    private const AG_CONTROLEUR   = 'AG-CRD-TST-0004';
    private const AG_GERANT       = 'AG-CRD-TST-0005';

    // 20 clients existants (CL-EBENKGA-26-00001 → 00020)
    private const CLIENTS = [
        ['CL-EBENKGA-26-00001', 'ZON-EBENKGA-26-00001'],
        ['CL-EBENKGA-26-00002', 'ZON-EBENKGA-26-00006'],
        ['CL-EBENKGA-26-00003', 'ZON-EBENKGA-26-00006'],
        ['CL-EBENKGA-26-00004', 'ZON-EBENKGA-26-00008'],
        ['CL-EBENKGA-26-00005', 'ZON-EBENKGA-26-00007'],
        ['CL-EBENKGA-26-00006', 'ZON-EBENKGA-26-00001'],
        ['CL-EBENKGA-26-00007', 'ZON-EBENKGA-26-00008'],
        ['CL-EBENKGA-26-00008', 'ZON-EBENKGA-26-00012'],
        ['CL-EBENKGA-26-00009', 'ZON-EBENKGA-26-00002'],
        ['CL-EBENKGA-26-00010', 'ZON-EBENKGA-26-00001'],
        ['CL-EBENKGA-26-00011', 'ZON-EBENKGA-26-00003'],
        ['CL-EBENKGA-26-00012', 'ZON-EBENKGA-26-00007'],
        ['CL-EBENKGA-26-00013', 'ZON-EBENKGA-26-00002'],
        ['CL-EBENKGA-26-00014', 'ZON-EBENKGA-26-00008'],
        ['CL-EBENKGA-26-00015', 'ZON-EBENKGA-26-00006'],
        ['CL-EBENKGA-26-00016', 'ZON-EBENKGA-26-00001'],
        ['CL-EBENKGA-26-00017', 'ZON-EBENKGA-26-00002'],
        ['CL-EBENKGA-26-00018', 'ZON-EBENKGA-26-00006'],
        ['CL-EBENKGA-26-00019', 'ZON-EBENKGA-26-00001'],
        ['CL-EBENKGA-26-00020', 'ZON-EBENKGA-26-00008'],
    ];

    public function run(): void
    {
        $this->amortissement = app(AmortissementService::class);

        $this->command->info('CreditTestSeeder — Création de 20 dossiers de crédit de test…');

        // Supprimer uniquement les dossiers de test existants pour relancer proprement
        $existants = CreditDemande::where('agent_createur_matricule', self::AG_DEMANDEUR)
            ->pluck('id');

        if ($existants->isNotEmpty()) {
            $this->command->warn("  Suppression de {$existants->count()} dossier(s) de test existants…");
            CreditAudit::whereIn('credit_demande_id', $existants)->delete();
            CreditRemboursement::whereIn('credit_demande_id', $existants)->delete();
            CreditEcheance::whereIn('echeancier_id',
                CreditEcheancier::whereIn('credit_demande_id', $existants)->pluck('id')
            )->delete();
            CreditEcheancier::whereIn('credit_demande_id', $existants)->delete();
            CreditDeblocage::whereIn('credit_demande_id', $existants)->delete();
            CreditValidation::whereIn('credit_demande_id', $existants)->delete();
            CreditPiece::whereIn('credit_demande_id', $existants)->delete();
            CreditAnalyse::whereIn('credit_demande_id', $existants)->delete();
            CreditDemande::whereIn('id', $existants)->delete();
        }

        $scenarios = [
            // idx, montant, devise, duree, taux, type, mois_debut_relatif
            [0,  500000,  'CDF', 6,  5.5, 'INDIVIDUEL', -5],  // 1. BROUILLON
            [1,  250000,  'CDF', 12, 4.0, 'INDIVIDUEL', -4],  // 2. SOUMIS
            [2,  800000,  'CDF', 9,  5.0, 'SOLIDAIRE',  -4],  // 3. EN_ANALYSE
            [3,  300000,  'CDF', 6,  5.5, 'INDIVIDUEL', -3],  // 4. EN_ANALYSE (EN_COURS)
            [4,  1000000, 'CDF', 12, 4.5, 'PME',        -3],  // 5. EN_VALIDATION bloc1
            [5,  600000,  'CDF', 6,  5.0, 'INDIVIDUEL', -3],  // 6. EN_VALIDATION bloc2
            [6,  450000,  'CDF', 9,  4.0, 'SOLIDAIRE',  -2],  // 7. EN_VALIDATION bloc3
            [7,  700000,  'CDF', 12, 5.5, 'PME',        -2],  // 8. EN_VALIDATION bloc4
            [8,  900000,  'CDF', 6,  5.0, 'INDIVIDUEL', -2],  // 9. PRET_A_DEBLOQUER
            [9,  1500000, 'CDF', 12, 4.5, 'PME',        -6],  // 10. DEBLOQUE
            [10, 500,     'USD', 6,  5.5, 'INDIVIDUEL', -8],  // 11. EN_REMBOURSEMENT (1 pmt)
            [11, 800000,  'CDF', 9,  5.0, 'SOLIDAIRE',  -10], // 12. EN_REMBOURSEMENT (plusieurs)
            [12, 300000,  'CDF', 6,  4.0, 'INDIVIDUEL', -5],  // 13. EN_REMBOURSEMENT (partiel)
            [13, 1200000, 'CDF', 12, 5.5, 'PME',        -15], // 14. EN_RETARD
            [14, 600000,  'CDF', 6,  5.0, 'INDIVIDUEL', -9],  // 15. EN_RETARD (avec pénalité)
            [15, 400,     'USD', 12, 4.5, 'INDIVIDUEL', -14], // 16. SOLDE complet
            [16, 750000,  'CDF', 6,  5.5, 'INDIVIDUEL', -8],  // 17. SOLDE anticipé
            [17, 500000,  'CDF', 9,  5.0, 'SOLIDAIRE',  -3],  // 18. ANNULE (rejet validation)
            [18, 350000,  'CDF', 6,  4.0, 'INDIVIDUEL', -2],  // 19. SUSPENDU
            [19, 900000,  'CDF', 12, 5.5, 'PME',        -4],  // 20. SUSPECT
        ];

        foreach ($scenarios as $i => [$idx, $montant, $devise, $duree, $taux, $type, $moisDebut]) {
            [$clientMatricule, $codeZone] = self::CLIENTS[$idx];
            $scenarioNum = $idx + 1;
            $this->command->line("  Scénario {$scenarioNum}/20…");

            $dateCreation = now()->addMonths($moisDebut);
            $this->creerScenario($scenarioNum, $clientMatricule, $codeZone, $montant, $devise, $duree, $taux, $type, $dateCreation);
        }

        $this->command->info('✔ CreditTestSeeder terminé — 20 dossiers créés.');
        $this->command->info('  Comptes: credit.demandeur@test.local (PER54/56)');
        $this->command->info('  Agents : AG-CRD-TST-0001 à 0005');
    }

    private function creerScenario(
        int    $num,
        string $clientMatricule,
        string $codeZone,
        float  $montant,
        string $devise,
        int    $duree,
        float  $taux,
        string $type,
        Carbon $dateCreation
    ): void {
        $objets = [
            1  => 'Achat de matériel agricole – semences et intrants',
            2  => 'Fonds de roulement commerce de détail',
            3  => 'Acquisition de stock pour épicerie',
            4  => 'Financement équipement de couture',
            5  => 'Extension atelier menuiserie – machines',
            6  => 'Achat motos de livraison',
            7  => 'Financement formation professionnelle',
            8  => "Achat de matériel d'exploitation agricole",
            9  => 'Investissement commerce textiles importés',
            10 => 'Création salon de coiffure',
            11 => 'Achat équipements restauration',
            12 => 'Financement camping-car transport commun',
            13 => 'Investissement dans un atelier de soudure',
            14 => 'Achat terrain et construction maison',
            15 => 'Fonds de démarrage petite boulangerie',
            16 => 'Acquisition véhicule de commerce',
            17 => 'Financement stocks pharmacie',
            18 => 'Achat matériel informatique',
            19 => 'Financement étude universitaire enfant',
            20 => 'Agrandissement magasin vêtements',
        ];

        $garanties = [
            1  => 'Titre foncier parcelle N°4521 + caution solidaire du conjoint',
            2  => 'Marchandises en stock (inventaire valorisé) + caution',
            3  => 'Salaire mensuel 750 USD cédé à titre de garantie',
            4  => 'Machine à coudre industrielle (valeur 1 200 USD)',
            5  => 'Machines atelier + caution du fournisseur',
            6  => '2 motos (valeur 1 800 USD) + caution solidaire',
            7  => 'Attestation de revenus + caution familiale',
            8  => 'Tracteur (valeur 8 000 USD) + hypothèque parcelle',
            9  => 'Stocks entrepôt (inventaire) + caution commerciale',
            10 => 'Matériel salon (valeur 800 USD) + loyer sécurisé',
            11 => 'Équipements restaurant (valeur 2 500 USD)',
            12 => 'Véhicule transport (valeur 12 000 USD) + assurance',
            13 => 'Outillage atelier + caution solidaire épouse',
            14 => 'Titre foncier + assurance vie',
            15 => 'Équipements boulangerie + caution familiale',
            16 => 'Logbook véhicule + assurance tout-risque',
            17 => 'Stock pharmacie (inventaire) + diplôme pharmacien',
            18 => 'Équipements informatiques + caution',
            19 => 'Relevés de notes + engagement parental',
            20 => 'Fonds de commerce + bail notarié',
        ];

        DB::transaction(function () use (
            $num, $clientMatricule, $codeZone, $montant, $devise, $duree, $taux, $type, $dateCreation,
            $objets, $garanties
        ) {
            $calcul = $this->amortissement->simuler($montant, $taux, $duree);

            // ── Créer le dossier ─────────────────────────────────────────
            $dossier = CreditDemande::create([
                'client_matricule'         => $clientMatricule,
                'portefeuille_id'          => self::PORTEFEUILLE_ID,
                'code_zone'                => $codeZone,
                'agent_createur_matricule' => self::AG_DEMANDEUR,
                'agent_analyse_matricule'  => null,
                'montant_demande'          => $montant,
                'devise'                   => $devise,
                'duree_mois'               => $duree,
                'taux_interet_mensuel'     => $taux,
                'type_credit'              => $type,
                'objet_credit'             => $objets[$num],
                'garantie_description'     => $garanties[$num],
                'montant_total_echeances'  => $calcul['total_general'],
                'total_interets'           => $calcul['total_interets'],
                'statut_global'            => 'BROUILLON',
                'created_at'               => $dateCreation,
                'updated_at'               => $dateCreation,
            ]);

            $this->creerPieces($dossier, $dateCreation);
            $this->creerBlocsValidation($dossier);
            $this->logAuditSystem($dossier, 'CREATION', null, 'BROUILLON', $dateCreation);

            // ── Passer au scénario cible ──────────────────────────────────
            match ($num) {
                1  => null, // BROUILLON — rien à faire
                2  => $this->passerSoumis($dossier, $dateCreation),
                3  => $this->passerEnAnalyse($dossier, $dateCreation),
                4  => $this->passerEnAnalyseEnCours($dossier, $dateCreation),
                5  => $this->passerEnValidationBloc1($dossier, $dateCreation, $montant),
                6  => $this->passerEnValidationBloc2($dossier, $dateCreation, $montant),
                7  => $this->passerEnValidationBloc3($dossier, $dateCreation, $montant),
                8  => $this->passerEnValidationBloc4($dossier, $dateCreation, $montant),
                9  => $this->passerPretADebloquer($dossier, $dateCreation, $montant),
                10 => $this->passerDebloque($dossier, $dateCreation, $montant, $devise, $duree, $taux),
                11 => $this->passerEnRemboursement1Pmt($dossier, $dateCreation, $montant, $devise, $duree, $taux),
                12 => $this->passerEnRemboursementMulti($dossier, $dateCreation, $montant, $devise, $duree, $taux),
                13 => $this->passerEnRemboursementPartiel($dossier, $dateCreation, $montant, $devise, $duree, $taux),
                14 => $this->passerEnRetard($dossier, $dateCreation, $montant, $devise, $duree, $taux, false),
                15 => $this->passerEnRetard($dossier, $dateCreation, $montant, $devise, $duree, $taux, true),
                16 => $this->passerSoldeComplet($dossier, $dateCreation, $montant, $devise, $duree, $taux),
                17 => $this->passerSoldeAnticipe($dossier, $dateCreation, $montant, $devise, $duree, $taux),
                18 => $this->passerAnnule($dossier, $dateCreation, $montant),
                19 => $this->passerSuspendu($dossier, $dateCreation, $montant),
                20 => $this->passerSuspect($dossier, $dateCreation, $montant),
                default => null,
            };
        });
    }

    // ── Helpers de transition ────────────────────────────────────────────

    private function passerSoumis(CreditDemande $d, Carbon $dc): void
    {
        $dc1 = $dc->copy()->addDays(1);
        $d->update(['statut_global' => 'SOUMIS', 'soumis_le' => $dc1, 'updated_at' => $dc1]);
        $this->logAuditSystem($d, 'SOUMISSION', 'BROUILLON', 'SOUMIS', $dc1);
    }

    private function passerEnAnalyse(CreditDemande $d, Carbon $dc): void
    {
        $this->passerSoumis($d, $dc->copy());
        $dc2 = $dc->copy()->addDays(2);
        $d->update(['statut_global' => 'EN_ANALYSE', 'agent_analyse_matricule' => self::AG_CREDIT, 'updated_at' => $dc2]);
        $this->logAuditSystem($d, 'ANALYSE_DEMARREE', 'SOUMIS', 'EN_ANALYSE', $dc2);
    }

    private function passerEnAnalyseEnCours(CreditDemande $d, Carbon $dc): void
    {
        $this->passerEnAnalyse($d, $dc->copy());
        $dc3 = $dc->copy()->addDays(3);
        CreditAnalyse::create([
            'credit_demande_id'      => $d->id,
            'analyseur_matricule'    => self::AG_CREDIT,
            'revenu_mensuel_verifie' => 450000,
            'capacite_remboursement' => 300000,
            'ratio_endettement'      => 25.5,
            'score_risque'           => 'FAIBLE',
            'historique_credit'      => 'Bon historique, aucun incident de paiement antérieur.',
            'garanties_evaluees'     => 'Garanties évaluées à 120% du montant demandé.',
            'observations'           => 'Analyse en cours – complément de pièces en attente.',
            'recommandation'         => 'FAVORABLE',
            'montant_recommande'     => $d->montant_demande,
            'statut'                 => 'EN_COURS',
            'created_at'             => $dc3,
            'updated_at'             => $dc3,
        ]);
        $this->logAuditSystem($d, 'ANALYSE_DEMARREE', 'EN_ANALYSE', 'EN_ANALYSE', $dc3);
    }

    private function passerEnValidationBloc1(CreditDemande $d, Carbon $dc, float $montant): void
    {
        $this->terminerAnalyse($d, $dc->copy(), $montant);
        // Bloc 1 en attente – déjà activé par terminerAnalyse
    }

    private function passerEnValidationBloc2(CreditDemande $d, Carbon $dc, float $montant): void
    {
        $this->terminerAnalyse($d, $dc->copy(), $montant);
        $dc2 = $dc->copy()->addDays(5);
        $this->validerBloc($d, 'AGENT_CREDIT', 1, $montant, $dc2);
    }

    private function passerEnValidationBloc3(CreditDemande $d, Carbon $dc, float $montant): void
    {
        $this->passerEnValidationBloc2($d, $dc->copy(), $montant);
        $dc3 = $dc->copy()->addDays(7);
        $this->validerBloc($d, 'CONTROLEUR', 2, $montant, $dc3);
    }

    private function passerEnValidationBloc4(CreditDemande $d, Carbon $dc, float $montant): void
    {
        $this->passerEnValidationBloc3($d, $dc->copy(), $montant);
        $dc4 = $dc->copy()->addDays(9);
        $this->validerBloc($d, 'CHARGE_OPERATIONS', 3, $montant, $dc4);
    }

    private function passerPretADebloquer(CreditDemande $d, Carbon $dc, float $montant): void
    {
        $this->passerEnValidationBloc4($d, $dc->copy(), $montant);
        $dc5 = $dc->copy()->addDays(11);
        $this->validerBloc($d, 'GERANT', 4, $montant, $dc5, true);
    }

    private function passerDebloque(CreditDemande $d, Carbon $dc, float $montant, string $devise, int $duree, float $taux): void
    {
        $this->passerPretADebloquer($d, $dc->copy(), $montant);
        $dateDeblocage = $dc->copy()->addDays(14);
        $datePremier   = $dateDeblocage->copy()->addMonth()->startOfMonth();
        $this->effectuerDeblocage($d, $montant, $devise, $dateDeblocage, $datePremier, $duree, $taux);
    }

    private function passerEnRemboursement1Pmt(CreditDemande $d, Carbon $dc, float $montant, string $devise, int $duree, float $taux): void
    {
        $this->passerDebloque($d, $dc->copy(), $montant, $devise, $duree, $taux);
        $echeancier = $d->fresh()->echeancier()->with('echeances')->first();
        $echeance1  = $echeancier->echeances->sortBy('numero_echeance')->first();
        if ($echeance1) {
            $datePmt = Carbon::parse($echeance1->date_echeance);
            $this->enregistrerRemboursement($d, $echeance1, $datePmt, 'ECHEANCE');
        }
    }

    private function passerEnRemboursementMulti(CreditDemande $d, Carbon $dc, float $montant, string $devise, int $duree, float $taux): void
    {
        $this->passerDebloque($d, $dc->copy(), $montant, $devise, $duree, $taux);
        $echeancier = $d->fresh()->echeancier()->with('echeances')->first();
        $echeances  = $echeancier->echeances->sortBy('numero_echeance')->values();
        // Payer les 3 premières échéances
        foreach ($echeances->take(3) as $ech) {
            $datePmt = Carbon::parse($ech->date_echeance);
            $this->enregistrerRemboursement($d, $ech, $datePmt, 'ECHEANCE');
        }
    }

    private function passerEnRemboursementPartiel(CreditDemande $d, Carbon $dc, float $montant, string $devise, int $duree, float $taux): void
    {
        $this->passerDebloque($d, $dc->copy(), $montant, $devise, $duree, $taux);
        $echeancier = $d->fresh()->echeancier()->with('echeances')->first();
        $echeance1  = $echeancier->echeances->sortBy('numero_echeance')->first();
        if ($echeance1) {
            // Paiement partiel : 60% du total dû
            $montantPartiel = round((float) $echeance1->total_echeance * 0.6, 2);
            $capitalPartiel = round((float) $echeance1->capital_echeance * 0.6, 2);
            $interetPartiel = round($montantPartiel - $capitalPartiel, 2);

            $datePmt = Carbon::parse($echeance1->date_echeance);
            $this->enregistrerRemboursementMontant($d, $echeance1, $montantPartiel, $capitalPartiel, $interetPartiel, $datePmt, 'PARTIEL');
        }
    }

    private function passerEnRetard(CreditDemande $d, Carbon $dc, float $montant, string $devise, int $duree, float $taux, bool $avecPenalite): void
    {
        $this->passerDebloque($d, $dc->copy(), $montant, $devise, $duree, $taux);
        $echeancier = $d->fresh()->echeancier()->with('echeances')->first();

        // Payer 1ère échéance normalement
        $echeances = $echeancier->echeances->sortBy('numero_echeance')->values();
        $ech1 = $echeances->get(0);
        if ($ech1) {
            $datePmt1 = Carbon::parse($ech1->date_echeance);
            $this->enregistrerRemboursement($d, $ech1, $datePmt1, 'ECHEANCE');
        }

        // Marquer les échéances suivantes en retard (dates passées)
        foreach ($echeances->skip(1)->take(3) as $ech) {
            $ech->update(['statut' => 'EN_RETARD']);
        }

        // Enregistrer une pénalité si demandé
        if ($avecPenalite) {
            $ech2 = $echeances->get(1);
            if ($ech2) {
                $penalite = round((float) $ech2->total_echeance * 0.02, 2);
            CreditRemboursement::create([
                'credit_demande_id'  => $d->id,
                'echeance_id'        => $ech2->id,
                'agent_matricule'    => self::AG_DEMANDEUR,
                'compte_id'          => $d->compte_id,
                'montant_recu'       => $penalite,
                'dont_capital'       => 0,
                'dont_interet'       => 0,
                'dont_penalite'      => $penalite,
                'devise'             => $devise,
                'type_remboursement' => 'PENALITE',
                'reference_caisse'   => 'PEN-TST-' . $d->id,
                'observations'       => 'Pénalité de retard 2%',
                'recu_le'            => now()->subDays(5),
            ]);
            }
        }

        $d->update(['statut_global' => 'EN_RETARD']);
        $this->logAuditSystem($d, 'REMBOURSEMENT', 'EN_REMBOURSEMENT', 'EN_RETARD', now()->subDays(2),
            'Passage automatique EN_RETARD – échéances dépassées non payées');
    }

    private function passerSoldeComplet(CreditDemande $d, Carbon $dc, float $montant, string $devise, int $duree, float $taux): void
    {
        $this->passerDebloque($d, $dc->copy(), $montant, $devise, $duree, $taux);
        $echeancier = $d->fresh()->echeancier()->with('echeances')->first();

        // Payer TOUTES les échéances
        foreach ($echeancier->echeances->sortBy('numero_echeance') as $ech) {
            $datePmt = Carbon::parse($ech->date_echeance);
            // Si la date est dans le futur on la ramène au passé pour un test cohérent
            if ($datePmt->isFuture()) {
                $datePmt = now()->subDays(rand(1, 10));
            }
            $this->enregistrerRemboursement($d, $ech, $datePmt, 'ECHEANCE');
        }

        // Libérer la caution GTC
        $this->libererCautionGtc($d, $devise);
        $d->update(['statut_global' => 'SOLDE']);
        $this->logAuditSystem($d, 'REMBOURSEMENT', 'EN_REMBOURSEMENT', 'SOLDE', now()->subDays(1));
    }

    private function passerSoldeAnticipe(CreditDemande $d, Carbon $dc, float $montant, string $devise, int $duree, float $taux): void
    {
        $this->passerDebloque($d, $dc->copy(), $montant, $devise, $duree, $taux);
        $echeancier = $d->fresh()->echeancier()->with('echeances')->first();
        $echeances  = $echeancier->echeances->sortBy('numero_echeance')->values();

        // Payer 2 premières normalement
        foreach ($echeances->take(2) as $ech) {
            $this->enregistrerRemboursement($d, $ech, Carbon::parse($ech->date_echeance), 'ECHEANCE');
        }

        // Puis remboursement anticipé de tout le capital restant
        $capitalRestant = (float) $echeances->get(2)?->capital_restant_debut ?? 0;
        if ($capitalRestant > 0) {
            $dateAnticipe = now()->subDays(3);
            // Marquer toutes les échéances restantes comme payées
            foreach ($echeances->skip(2) as $ech) {
                $ech->update([
                    'statut'                 => 'PAYE',
                    'montant_paye'           => $ech->total_echeance,
                    'date_paiement_effectif' => $dateAnticipe->toDateString(),
                ]);
            }
            CreditRemboursement::create([
                'credit_demande_id'  => $d->id,
                'echeance_id'        => null,
                'agent_matricule'    => self::AG_DEMANDEUR,
                'compte_id'          => $d->compte_id,
                'montant_recu'       => $capitalRestant,
                'dont_capital'       => $capitalRestant,
                'dont_interet'       => 0,
                'dont_penalite'      => 0,
                'devise'             => $devise,
                'type_remboursement' => 'ANTICIPE',
                'reference_caisse'   => 'ANTI-TST-' . $d->id,
                'observations'       => 'Remboursement anticipé total – solde du capital restant',
                'recu_le'            => $dateAnticipe,
            ]);
        }

        $this->libererCautionGtc($d, $devise);
        $d->update(['statut_global' => 'SOLDE']);
        $this->logAuditSystem($d, 'REMBOURSEMENT', 'EN_REMBOURSEMENT', 'SOLDE', now()->subDays(3));
    }

    private function passerAnnule(CreditDemande $d, Carbon $dc, float $montant): void
    {
        $this->terminerAnalyse($d, $dc->copy(), $montant);
        // Rejet par le Contrôleur au bloc 2
        $dc2 = $dc->copy()->addDays(5);
        $this->validerBloc($d, 'AGENT_CREDIT', 1, $montant, $dc2);
        $dc3 = $dc->copy()->addDays(7);
        $this->rejeterBloc($d, 'CONTROLEUR', 2, $dc3);
    }

    private function passerSuspendu(CreditDemande $d, Carbon $dc, float $montant): void
    {
        $this->terminerAnalyse($d, $dc->copy(), $montant);
        $dc2 = $dc->copy()->addDays(8);
        $d->update([
            'statut_global'          => 'SUSPENDU',
            'est_suspendu'           => true,
            'motif_suspension'       => 'Vérification complémentaire du dossier en cours – suspicion de faux revenus.',
            'suspendu_par_matricule' => self::AG_CHARGE_OPS,
            'suspendu_le'            => $dc2,
            'updated_at'             => $dc2,
        ]);
        $this->logAuditSystem($d, 'SUSPENSION', 'EN_VALIDATION', 'SUSPENDU', $dc2);
    }

    private function passerSuspect(CreditDemande $d, Carbon $dc, float $montant): void
    {
        $this->terminerAnalyse($d, $dc->copy(), $montant);
        $dc2 = $dc->copy()->addDays(6);
        $d->update([
            'statut_global'          => 'SUSPECT',
            'est_suspect'            => true,
            'motif_suspicion'        => 'Doublon détecté – le même client a une demande en cours dans une autre agence.',
            'signale_par_matricule'  => self::AG_GERANT,
            'signale_le'             => $dc2,
            'updated_at'             => $dc2,
        ]);
        $this->logAuditSystem($d, 'SIGNALEMENT_SUSPECT', 'EN_VALIDATION', 'SUSPECT', $dc2);
    }

    // ── Helpers internes ────────────────────────────────────────────────

    private function terminerAnalyse(CreditDemande $d, Carbon $dc, float $montant): void
    {
        $this->passerEnAnalyse($d, $dc->copy());
        $dc3 = $dc->copy()->addDays(4);

        CreditAnalyse::create([
            'credit_demande_id'      => $d->id,
            'analyseur_matricule'    => self::AG_CREDIT,
            'revenu_mensuel_verifie' => 600000,
            'capacite_remboursement' => 400000,
            'ratio_endettement'      => 22.5,
            'score_risque'           => 'FAIBLE',
            'historique_credit'      => 'Aucun incident. Crédit précédent remboursé en avance.',
            'garanties_evaluees'     => 'Garanties conformes et suffisantes (130% du montant).',
            'observations'           => 'Dossier complet et cohérent. Recommandation favorable.',
            'recommandation'         => 'FAVORABLE',
            'montant_recommande'     => $montant,
            'statut'                 => 'COMPLETE',
            'complete_le'            => $dc3,
            'created_at'             => $dc3,
            'updated_at'             => $dc3,
        ]);

        $d->update([
            'statut_global'       => 'EN_VALIDATION',
            'montant_approuve'    => $montant,
            'updated_at'          => $dc3,
        ]);

        // Activer le bloc 1
        $d->validations()->where('ordre_etape', 1)->update(['etape_precedente_ok' => true]);
        $this->logAuditSystem($d, 'ANALYSE_COMPLETE', 'EN_ANALYSE', 'EN_VALIDATION', $dc3);
    }

    private function validerBloc(CreditDemande $d, string $typeValidateur, int $ordre, float $montant, Carbon $dc, bool $dernierBloc = false): void
    {
        $agMap = [
            'AGENT_CREDIT'      => self::AG_CREDIT,
            'CONTROLEUR'        => self::AG_CONTROLEUR,
            'CHARGE_OPERATIONS' => self::AG_CHARGE_OPS,
            'GERANT'            => self::AG_GERANT,
        ];
        $agentMatricule = $agMap[$typeValidateur] ?? self::AG_DEMANDEUR;

        $d->validations()->where('type_validateur', $typeValidateur)->update([
            'validateur_matricule' => $agentMatricule,
            'decision'             => 'APPROUVE',
            'montant_valide'       => $montant,
            'observations'         => "Dossier examiné et approuvé – aucune réserve. Validation {$typeValidateur}.",
            'valide_le'            => $dc,
            'signature_agent'      => $agentMatricule,
            'nom_signataire'       => 'Agent Test ' . $typeValidateur,
            'ip_validation'        => '127.0.0.1',
            'updated_at'           => $dc,
        ]);

        $d->update(['montant_approuve' => $montant, 'updated_at' => $dc]);
        $this->logAuditSystem($d, 'VALIDATION_PARTIELLE', 'EN_VALIDATION', 'EN_VALIDATION', $dc,
            "Bloc {$typeValidateur} approuvé");

        // Activer le bloc suivant ou finaliser
        if (!$dernierBloc) {
            $d->validations()->where('ordre_etape', $ordre + 1)->update(['etape_precedente_ok' => true]);
        } else {
            $d->update(['statut_global' => 'PRET_A_DEBLOQUER', 'updated_at' => $dc]);
            $this->logAuditSystem($d, 'VALIDATION_COMPLETE', 'EN_VALIDATION', 'PRET_A_DEBLOQUER', $dc);
        }
    }

    private function rejeterBloc(CreditDemande $d, string $typeValidateur, int $ordre, Carbon $dc): void
    {
        $agMap = ['CONTROLEUR' => self::AG_CONTROLEUR, 'GERANT' => self::AG_GERANT];
        $agent = $agMap[$typeValidateur] ?? self::AG_CHARGE_OPS;

        $d->validations()->where('type_validateur', $typeValidateur)->update([
            'validateur_matricule' => $agent,
            'decision'             => 'REJETE',
            'observations'         => 'Dossier rejeté – informations financières non conformes aux critères internes.',
            'valide_le'            => $dc,
            'signature_agent'      => $agent,
            'nom_signataire'       => 'Agent Test ' . $typeValidateur,
            'ip_validation'        => '127.0.0.1',
            'updated_at'           => $dc,
        ]);

        $d->update([
            'statut_global'         => 'ANNULE',
            'est_annule'            => true,
            'motif_annulation'      => 'Dossier rejeté lors de la validation par ' . $typeValidateur,
            'annule_par_matricule'  => $agent,
            'annule_le'             => $dc,
            'updated_at'            => $dc,
        ]);
        $this->logAuditSystem($d, 'REJET', 'EN_VALIDATION', 'ANNULE', $dc, "Rejet par {$typeValidateur}");
    }

    private function effectuerDeblocage(
        CreditDemande $d,
        float  $montant,
        string $devise,
        Carbon $dateDeblocage,
        Carbon $datePremier,
        int    $duree,
        float  $taux
    ): void {
        $montantBrut = round($montant, 2);
        $caution     = round($montantBrut * 0.20, 2);
        $frais       = round($montantBrut * 0.04, 2);
        $netVerse    = round($montantBrut - $caution - $frais, 2);

        // Compte RMB
        $compteRmb = Compte::firstOrCreate(
            ['client_matricule' => $d->client_matricule, 'type' => 'RMB', 'devise' => $devise],
            ['solde_reel' => 0, 'solde_bloque' => 0, 'portefeuille_id' => null]
        );

        // Provision initiale (24%)
        $provision = round($montantBrut * 0.24, 2);
        $compteRmb->increment('solde_reel', $provision);

        // Déblocage : RMB +100%, puis -20% vers GTC, -4% frais
        $compteRmb->increment('solde_reel', $montantBrut);
        $compteRmb->decrement('solde_reel', $caution);
        $compteRmb->decrement('solde_reel', $frais);

        // Compte GTC
        $compteGtc = Compte::firstOrCreate(
            ['client_matricule' => $d->client_matricule, 'type' => 'GTC', 'devise' => $devise],
            ['solde_reel' => 0, 'solde_bloque' => 0, 'portefeuille_id' => null]
        );
        $compteGtc->increment('solde_reel', $caution);
        $compteGtc->increment('solde_bloque', $caution);

        $ref = 'DEB-' . $d->numero_dossier . '-' . $dateDeblocage->format('YmdHis');

        CreditDeblocage::create([
            'credit_demande_id'    => $d->id,
            'agent_matricule'      => self::AG_CHARGE_OPS,
            'compte_debit_id'      => 'COFFRE_01',
            'guichet_solde_id'     => ($devise === 'USD') ? self::COFFRE_SOLDE_USD_ID : self::COFFRE_SOLDE_ID,
            'compte_credit_id'     => $compteRmb->code_compte,
            'montant_debloque'     => $montantBrut,
            'montant_caution'      => $caution,
            'devise'               => $devise,
            'frais_dossier'        => $frais,
            'montant_net_verse'    => $netVerse,
            'reference_transaction'=> $ref . '-D',
            'numero_ordre'         => $ref . '-C',
            'observations'         => "Déblocage test scénario #{$d->id}",
            'debloque_le'          => $dateDeblocage,
        ]);

        // Générer l'échéancier
        $this->amortissement->genererEtSauvegarder($d, $datePremier);

        $d->update([
            'statut_global' => 'DEBLOQUE',
            'compte_id'     => $compteRmb->code_compte,
            'updated_at'    => $dateDeblocage,
        ]);
        $this->logAuditSystem($d, 'DEBLOCAGE', 'PRET_A_DEBLOQUER', 'DEBLOQUE', $dateDeblocage,
            "Montant: {$montantBrut} {$devise}");
    }

    private function enregistrerRemboursement(
        CreditDemande  $d,
        CreditEcheance $ech,
        Carbon         $datePmt,
        string         $type
    ): void {
        $total   = round((float) $ech->total_echeance, 2);
        $capital = round((float) $ech->capital_echeance, 2);
        $interet = round($total - $capital, 2);
        $this->enregistrerRemboursementMontant($d, $ech, $total, $capital, $interet, $datePmt, $type);
    }

    private function enregistrerRemboursementMontant(
        CreditDemande  $d,
        CreditEcheance $ech,
        float          $montantRecu,
        float          $dontCapital,
        float          $dontInteret,
        Carbon         $datePmt,
        string         $type
    ): void {
        $total = round((float) $ech->total_echeance, 2);
        $nouveauMontantPaye = round((float) $ech->montant_paye + $montantRecu, 2);
        $nouveauStatut = $nouveauMontantPaye >= $total ? 'PAYE' : 'PARTIELLEMENT_PAYE';

        CreditRemboursement::create([
            'credit_demande_id'  => $d->id,
            'echeance_id'        => $ech->id,
            'agent_matricule'    => self::AG_DEMANDEUR,
            'compte_id'          => $d->compte_id,
            'montant_recu'       => $montantRecu,
            'dont_capital'       => $dontCapital,
            'dont_interet'       => $dontInteret,
            'dont_penalite'      => 0,
            'devise'             => $d->devise,
            'type_remboursement' => $type,
            'reference_caisse'   => 'TST-' . $d->id . '-ECH' . $ech->numero_echeance,
            'observations'       => "Remboursement test éch. #{$ech->numero_echeance}",
            'recu_le'            => $datePmt,
        ]);

        $ech->update([
            'montant_paye'           => $nouveauMontantPaye,
            'statut'                 => $nouveauStatut,
            'date_paiement_effectif' => $datePmt->toDateString(),
        ]);

        if ($d->fresh()->statut_global === 'DEBLOQUE') {
            $d->update(['statut_global' => 'EN_REMBOURSEMENT']);
        }
    }

    private function libererCautionGtc(CreditDemande $d, string $devise): void
    {
        $compteGtc = Compte::where('client_matricule', $d->client_matricule)
            ->where('type', 'GTC')->where('devise', $devise)->first();

        if ($compteGtc && $compteGtc->solde_bloque > 0) {
            $montant = (float) $compteGtc->solde_bloque;
            // Vider le GTC
            $compteGtc->update(['solde_reel' => 0, 'solde_bloque' => 0]);
            // Restituer la caution au compte RMB du client
            $compteRmb = Compte::where('client_matricule', $d->client_matricule)
                ->where('type', 'RMB')->where('devise', $devise)->first();
            if ($compteRmb) {
                $compteRmb->increment('solde_reel', $montant);
            }
        }
    }

    private function creerPieces(CreditDemande $d, Carbon $dc): void
    {
        $pieces = [
            ['libelle' => "Copie de la carte d'identité nationale", 'type_piece' => 'IDENTITE', 'est_recu' => true, 'est_conforme' => true],
            ['libelle' => 'Justificatif de domicile', 'type_piece' => 'DOMICILE', 'est_recu' => true, 'est_conforme' => true],
            ['libelle' => 'Justificatif de revenus (bulletin, attestation)', 'type_piece' => 'REVENU', 'est_recu' => true, 'est_conforme' => true],
            ['libelle' => 'Formulaire de demande de crédit signé', 'type_piece' => 'AUTRE', 'est_recu' => true, 'est_conforme' => true],
        ];
        foreach ($pieces as $p) {
            CreditPiece::create(array_merge($p, [
                'credit_demande_id' => $d->id,
                'created_at' => $dc,
                'updated_at' => $dc,
            ]));
        }
    }

    private function creerBlocsValidation(CreditDemande $d): void
    {
        $blocs = [
            ['type_validateur' => 'AGENT_CREDIT',      'ordre_etape' => 1],
            ['type_validateur' => 'CONTROLEUR',        'ordre_etape' => 2],
            ['type_validateur' => 'CHARGE_OPERATIONS', 'ordre_etape' => 3],
            ['type_validateur' => 'GERANT',            'ordre_etape' => 4],
        ];
        foreach ($blocs as $b) {
            CreditValidation::create(array_merge($b, [
                'credit_demande_id'    => $d->id,
                'validateur_matricule' => '',
                'decision'             => 'EN_ATTENTE',
                'etape_precedente_ok'  => false,
            ]));
        }
    }

    private function logAuditSystem(
        CreditDemande $d,
        string        $action,
        ?string       $ancien,
        ?string       $nouveau,
        Carbon        $date,
        ?string       $details = null
    ): void {
        CreditAudit::create([
            'credit_demande_id' => $d->id,
            'acteur_matricule'  => self::AG_DEMANDEUR,
            'type_action'       => $action,
            'ancien_statut'     => $ancien,
            'nouveau_statut'    => $nouveau,
            'details'           => $details ?? ("Seeder: transition {$ancien} → {$nouveau}"),
            'ip_address'        => '127.0.0.1',
            'created_at'        => $date,
            'updated_at'        => $date,
        ]);
    }
}
