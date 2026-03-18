<?php

/**
 * ============================================================
 * MODULE CRÉDIT – TABLES BASE DE DONNÉES
 * ============================================================
 * 9 tables créées :
 *   1. tb_credit_demandes       – Dossier principal
 *   2. tb_credit_analyses       – Analyse du dossier
 *   3. tb_credit_validations    – Bloc validation (4 acteurs)
 *   4. tb_credit_pieces         – Documents justificatifs
 *   5. tb_credit_deblocages     – Acte de déblocage
 *   6. tb_credit_echeanciers    – En-tête de l'échéancier
 *   7. tb_credit_echeances      – Lignes de l'échéancier (1/mois)
 *   8. tb_credit_remboursements – Paiements reçus
 *   9. tb_credit_audits         – Journal d'événements du dossier
 * ============================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ====================================================
        // 1. tb_credit_demandes – Dossier principal
        // ====================================================
        Schema::create('tb_credit_demandes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_dossier', 30)->unique(); // ex: CRD-EBEN-2026-00001

            // Qui fait la demande
            $table->string('client_matricule', 30);
            $table->string('compte_id', 64);                   // code_compte de déblocage
            $table->unsignedBigInteger('portefeuille_id');
            $table->string('code_zone', 20);
            $table->string('agent_createur_matricule', 30);

            // Caractéristiques du crédit
            $table->decimal('montant_demande', 15, 2);
            $table->string('devise', 5)->default('CDF');
            $table->unsignedTinyInteger('duree_mois');
            $table->decimal('taux_interet_mensuel', 6, 4);
            $table->enum('type_credit', ['INDIVIDUEL', 'SOLIDAIRE', 'PME'])->default('INDIVIDUEL');
            $table->string('objet_credit', 500);
            $table->text('garantie_description')->nullable();

            // Montants calculés à la création
            $table->decimal('montant_approuve', 15, 2)->nullable();
            $table->decimal('montant_total_echeances', 15, 2)->nullable();
            $table->decimal('total_interets', 15, 2)->nullable();

            // Statut global du workflow
            $table->enum('statut_global', [
                'BROUILLON',
                'SOUMIS',
                'EN_ANALYSE',
                'EN_VALIDATION',
                'PRET_A_DEBLOQUER',
                'DEBLOQUE',
                'EN_REMBOURSEMENT',
                'EN_RETARD',
                'SOLDE',
                'ANNULE',
                'SUSPENDU',
                'SUSPECT',
            ])->default('BROUILLON');

            // Drapeaux transverses (bloquants)
            $table->boolean('est_annule')->default(false);
            $table->text('motif_annulation')->nullable();
            $table->string('annule_par_matricule', 30)->nullable();
            $table->timestamp('annule_le')->nullable();

            $table->boolean('est_suspendu')->default(false);
            $table->text('motif_suspension')->nullable();
            $table->string('suspendu_par_matricule', 30)->nullable();
            $table->timestamp('suspendu_le')->nullable();

            $table->boolean('est_suspect')->default(false);
            $table->text('motif_suspicion')->nullable();
            $table->string('signale_par_matricule', 30)->nullable();
            $table->timestamp('signale_le')->nullable();

            $table->timestamp('soumis_le')->nullable();
            $table->timestamps();

            // Index
            $table->index('client_matricule', 'idx_crd_client');
            $table->index('code_zone', 'idx_crd_zone');
            $table->index('statut_global', 'idx_crd_statut');
            $table->index('portefeuille_id', 'idx_crd_portef');
            $table->index('agent_createur_matricule', 'idx_crd_agent');

            // Foreign keys
            $table->foreign('client_matricule')->references('matricule')->on('tb_clients')->restrictOnDelete();
            $table->foreign('compte_id')->references('code_compte')->on('tb_comptes')->restrictOnDelete();
            $table->foreign('portefeuille_id')->references('id')->on('tb_portefeuilles_agents')->restrictOnDelete();
            $table->foreign('code_zone')->references('code_zone')->on('tb_zones')->restrictOnDelete();
        });

        // ====================================================
        // 2. tb_credit_analyses – Analyse du dossier
        // ====================================================
        Schema::create('tb_credit_analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credit_demande_id');

            $table->string('analyseur_matricule', 30);

            // Données analyse
            $table->decimal('revenu_mensuel_verifie', 15, 2)->nullable();
            $table->decimal('capacite_remboursement', 15, 2)->nullable();
            $table->decimal('ratio_endettement', 6, 2)->nullable(); // en %
            $table->enum('score_risque', ['FAIBLE', 'MOYEN', 'ELEVE', 'TRES_ELEVE'])->nullable();

            // Résultats
            $table->text('historique_credit')->nullable();
            $table->text('garanties_evaluees')->nullable();
            $table->text('observations')->nullable();
            $table->enum('recommandation', ['FAVORABLE', 'FAVORABLE_AVEC_RESERVE', 'DEFAVORABLE']);
            $table->decimal('montant_recommande', 15, 2)->nullable();

            // Statut
            $table->enum('statut', ['EN_COURS', 'COMPLETE', 'ANNULE'])->default('EN_COURS');
            $table->timestamp('complete_le')->nullable();
            $table->timestamps();

            $table->unique('credit_demande_id', 'uq_analyse_demande');
            $table->index('analyseur_matricule', 'idx_analyse_agt');

            $table->foreign('credit_demande_id')->references('id')->on('tb_credit_demandes')->cascadeOnDelete();
        });

        // ====================================================
        // 3. tb_credit_validations – Bloc validation (4 acteurs)
        // ====================================================
        Schema::create('tb_credit_validations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credit_demande_id');

            $table->enum('type_validateur', [
                'AGENT_CREDIT',
                'CHARGE_OPERATIONS',
                'CONTROLEUR',
                'GERANT',
            ]);
            $table->string('validateur_matricule', 30);

            // Décision
            $table->enum('decision', [
                'EN_ATTENTE',
                'APPROUVE',
                'APPROUVE_AVEC_RESERVE',
                'REJETE',
            ])->default('EN_ATTENTE');
            $table->decimal('montant_valide', 15, 2)->nullable();
            $table->text('observations')->nullable();
            $table->text('conditions')->nullable();

            // Ordre et verrouillage
            $table->unsignedTinyInteger('ordre_etape'); // 1=Agent,2=ChOps,3=Ctrl,4=Gérant
            $table->boolean('etape_precedente_ok')->default(false);

            $table->timestamp('valide_le')->nullable();
            $table->timestamps();

            $table->unique(['credit_demande_id', 'type_validateur'], 'uq_validation_type');
            $table->index('validateur_matricule', 'idx_valid_agt');
            $table->index('decision', 'idx_valid_dec');

            $table->foreign('credit_demande_id')->references('id')->on('tb_credit_demandes')->cascadeOnDelete();
        });

        // ====================================================
        // 4. tb_credit_pieces – Documents justificatifs
        // ====================================================
        Schema::create('tb_credit_pieces', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credit_demande_id');

            $table->string('libelle', 200);
            $table->enum('type_piece', ['IDENTITE', 'DOMICILE', 'REVENU', 'GARANTIE', 'AUTRE']);
            $table->string('nom_fichier', 255)->nullable();
            $table->boolean('est_recu')->default(false);
            $table->boolean('est_conforme')->nullable(); // NULL = non contrôlé
            $table->string('observations', 500)->nullable();
            $table->timestamps();

            $table->index('credit_demande_id', 'idx_pieces_dem');
            $table->foreign('credit_demande_id')->references('id')->on('tb_credit_demandes')->cascadeOnDelete();
        });

        // ====================================================
        // 5. tb_credit_deblocages – Acte de déblocage
        // ====================================================
        Schema::create('tb_credit_deblocages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credit_demande_id')->unique();

            $table->string('agent_matricule', 30);
            $table->string('compte_debit_id', 64);
            $table->string('compte_credit_id', 64);

            $table->decimal('montant_debloque', 15, 2);
            $table->string('devise', 5)->default('CDF');
            $table->decimal('frais_dossier', 15, 2)->default(0);
            $table->decimal('montant_net_verse', 15, 2);

            $table->string('reference_transaction', 50)->nullable();
            $table->string('numero_ordre', 30)->nullable();
            $table->text('observations')->nullable();
            $table->timestamp('debloque_le');
            $table->timestamps();

            $table->foreign('credit_demande_id')->references('id')->on('tb_credit_demandes')->restrictOnDelete();
            $table->foreign('compte_debit_id')->references('code_compte')->on('tb_comptes')->restrictOnDelete();
            $table->foreign('compte_credit_id')->references('code_compte')->on('tb_comptes')->restrictOnDelete();
        });

        // ====================================================
        // 6. tb_credit_echeanciers – En-tête de l'échéancier
        // ====================================================
        Schema::create('tb_credit_echeanciers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credit_demande_id')->unique();

            $table->decimal('montant_capital', 15, 2);
            $table->decimal('taux_mensuel', 6, 4);
            $table->unsignedTinyInteger('duree_mois');
            $table->date('date_premier_remboursement');
            $table->enum('type_amortissement', ['DEGRESSIF', 'LINEAIRE'])->default('DEGRESSIF');

            $table->decimal('total_capital', 15, 2);
            $table->decimal('total_interets', 15, 2);
            $table->decimal('total_general', 15, 2);
            $table->timestamps();

            $table->foreign('credit_demande_id')->references('id')->on('tb_credit_demandes')->cascadeOnDelete();
        });

        // ====================================================
        // 7. tb_credit_echeances – Lignes de l'échéancier
        // ====================================================
        Schema::create('tb_credit_echeances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('echeancier_id');

            $table->unsignedTinyInteger('numero_echeance');
            $table->date('date_echeance');
            $table->decimal('capital_restant_debut', 15, 2);
            $table->decimal('capital_echeance', 15, 2);
            $table->decimal('interet_echeance', 15, 2);
            $table->decimal('total_echeance', 15, 2);
            $table->decimal('capital_restant_fin', 15, 2);

            $table->enum('statut', [
                'EN_ATTENTE',
                'PAYE',
                'PARTIELLEMENT_PAYE',
                'EN_RETARD',
                'REPORTE',
            ])->default('EN_ATTENTE');
            $table->decimal('montant_paye', 15, 2)->default(0);
            $table->date('date_paiement_effectif')->nullable();
            $table->timestamps();

            $table->index('echeancier_id', 'idx_ech_echeancier');
            $table->index('date_echeance', 'idx_ech_date');
            $table->index('statut', 'idx_ech_statut');

            $table->foreign('echeancier_id')->references('id')->on('tb_credit_echeanciers')->cascadeOnDelete();
        });

        // ====================================================
        // 8. tb_credit_remboursements – Paiements reçus
        // ====================================================
        Schema::create('tb_credit_remboursements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credit_demande_id');
            $table->unsignedBigInteger('echeance_id')->nullable(); // NULL si remboursement anticipé

            $table->string('agent_matricule', 30);
            $table->string('compte_id', 64);

            $table->decimal('montant_recu', 15, 2);
            $table->decimal('dont_capital', 15, 2);
            $table->decimal('dont_interet', 15, 2);
            $table->decimal('dont_penalite', 15, 2)->default(0);
            $table->string('devise', 5)->default('CDF');

            $table->enum('type_remboursement', [
                'ECHEANCE',
                'PARTIEL',
                'ANTICIPE',
                'PENALITE',
            ])->default('ECHEANCE');
            $table->string('reference_caisse', 50)->nullable();
            $table->text('observations')->nullable();
            $table->timestamp('recu_le');
            $table->timestamps();

            $table->index('credit_demande_id', 'idx_rembours_dem');
            $table->index('echeance_id', 'idx_rembours_ech');

            $table->foreign('credit_demande_id')->references('id')->on('tb_credit_demandes')->restrictOnDelete();
            $table->foreign('echeance_id')->references('id')->on('tb_credit_echeances')->nullOnDelete();
            $table->foreign('compte_id')->references('code_compte')->on('tb_comptes')->restrictOnDelete();
        });

        // ====================================================
        // 9. tb_credit_audits – Journal d'événements
        // ====================================================
        Schema::create('tb_credit_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credit_demande_id');

            $table->string('acteur_matricule', 30)->nullable();
            $table->enum('type_action', [
                'CREATION',
                'SOUMISSION',
                'ANALYSE_DEMARREE',
                'ANALYSE_COMPLETE',
                'VALIDATION_PARTIELLE',
                'VALIDATION_COMPLETE',
                'REJET',
                'DEBLOCAGE',
                'REMBOURSEMENT',
                'ANNULATION',
                'SUSPENSION',
                'LEVER_SUSPENSION',
                'SIGNALEMENT_SUSPECT',
                'LEVER_SUSPICION',
                'MODIFICATION',
            ]);
            $table->string('ancien_statut', 30)->nullable();
            $table->string('nouveau_statut', 30)->nullable();
            $table->text('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('credit_demande_id', 'idx_audit_dem');
            $table->index('type_action', 'idx_audit_action');

            $table->foreign('credit_demande_id')->references('id')->on('tb_credit_demandes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_credit_audits');
        Schema::dropIfExists('tb_credit_remboursements');
        Schema::dropIfExists('tb_credit_echeances');
        Schema::dropIfExists('tb_credit_echeanciers');
        Schema::dropIfExists('tb_credit_deblocages');
        Schema::dropIfExists('tb_credit_pieces');
        Schema::dropIfExists('tb_credit_validations');
        Schema::dropIfExists('tb_credit_analyses');
        Schema::dropIfExists('tb_credit_demandes');
    }
};
