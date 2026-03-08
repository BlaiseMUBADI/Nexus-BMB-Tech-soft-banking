<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Extend tb_transactions pour couvrir toutes les opérations guichet :
 *
 * Avant : table simple liée aux comptes (DEPOT, RETRAIT, VIREMENT, REMBOURSEMENT)
 * Après : table unifiée caisse — comptes + espèces sans compte + change + paiements
 *
 * Changements :
 *  1. compte_code → nullable  (opérations espèces sans compte client)
 *  2. Ajout guichet_id        (quel guichet a effectué l'opération)
 *  3. Ajout devise_code       (devise de l'opération)
 *  4. Ajout CHANGE + PAIEMENT dans l'enum type
 *  5. Ajout client_nom/client_ref (clients sans compte)
 *  6. Ajout colonnes CHANGE   (devise_dest, montant_dest, taux_change)
 *  7. Ajout observations, statut, date_operation
 *  8. Ajout timestamps        (created_at, updated_at)
 *  9. Étendre type_flux de tb_mouvements_inter_caisses (DOTATION_MOBILE, REVERSEMENT_MOBILE)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Rendre compte_code nullable ──────────────────────────────────
        // Nécessaire pour les opérations en espèces sans compte client
        DB::statement("
            ALTER TABLE tb_transactions
            MODIFY COLUMN compte_code VARCHAR(64) NULL
        ");

        // ── 2-8. Ajouter les nouvelles colonnes ──────────────────────────────
        Schema::table('tb_transactions', function (Blueprint $table) {

            // Guichet émetteur
            $table->unsignedBigInteger('guichet_id')
                  ->nullable()
                  ->after('agent_matricule')
                  ->comment('Guichet ayant effectué l\'opération');

            // Devise principale de l'opération
            $table->char('devise_code', 3)
                  ->nullable()
                  ->after('guichet_id')
                  ->comment('Devise de la transaction (CDF, USD, EUR…)');

            // Client sans compte (opérations espèces sans compte bancaire)
            $table->string('client_nom', 150)->nullable()->after('devise_code');
            $table->string('client_ref', 50)->nullable()->after('client_nom')
                  ->comment('Réf. externe, passeport, ID…');

            // Colonnes dédiées au CHANGE de devises
            $table->char('devise_dest', 3)->nullable()->after('client_ref');
            $table->decimal('montant_dest', 18, 2)->nullable()->after('devise_dest');
            $table->decimal('taux_change', 14, 6)->nullable()->after('montant_dest');

            // Informations complémentaires
            $table->text('observations')->nullable()->after('taux_change');
            $table->enum('statut', ['CONFIRME', 'ANNULE'])
                  ->default('CONFIRME')
                  ->after('observations');
            $table->timestamp('date_operation')
                  ->useCurrent()
                  ->after('statut');

            // Timestamps Laravel
            $table->timestamps();

            // FK guichet
            $table->foreign('guichet_id', 'tb_transactions_guichet_fk')
                  ->references('id')->on('tb_caisses_guichets')
                  ->nullOnDelete();

            // Index utiles
            $table->index(['guichet_id', 'date_operation'], 'idx_trans_guichet_date');
            $table->index('statut', 'idx_trans_statut');
        });

        // ── 4. Étendre l'enum type ────────────────────────────────────────────
        // MySQL ne permet pas ALTER COLUMN sur enum via Blueprint, on utilise statement
        DB::statement("
            ALTER TABLE tb_transactions
            MODIFY COLUMN type
            ENUM('DEPOT','RETRAIT','VIREMENT','REMBOURSEMENT','CHANGE','PAIEMENT')
            NOT NULL
        ");

        // ── 9. Étendre type_flux de tb_mouvements_inter_caisses ─────────────
        DB::statement("
            ALTER TABLE tb_mouvements_inter_caisses
            MODIFY COLUMN type_flux
            ENUM('ALIMENTATION','DEGAGEMENT','TRANSFERT','DEMANDE_APPRO','DOTATION_MOBILE','REVERSEMENT_MOBILE')
            NOT NULL
        ");
    }

    public function down(): void
    {
        // Retour enum type_flux
        DB::statement("
            ALTER TABLE tb_mouvements_inter_caisses
            MODIFY COLUMN type_flux
            ENUM('ALIMENTATION','DEGAGEMENT','TRANSFERT','DEMANDE_APPRO')
            NOT NULL
        ");

        // Retour enum type
        DB::statement("
            ALTER TABLE tb_transactions
            MODIFY COLUMN type
            ENUM('DEPOT','RETRAIT','VIREMENT','REMBOURSEMENT')
            NOT NULL
        ");

        Schema::table('tb_transactions', function (Blueprint $table) {
            $table->dropForeign('tb_transactions_guichet_fk');
            $table->dropIndex('idx_trans_guichet_date');
            $table->dropIndex('idx_trans_statut');
            $table->dropColumn([
                'guichet_id','devise_code','client_nom','client_ref',
                'devise_dest','montant_dest','taux_change',
                'observations','statut','date_operation',
                'created_at','updated_at',
            ]);
        });

        DB::statement("
            ALTER TABLE tb_transactions
            MODIFY COLUMN compte_code VARCHAR(64) NOT NULL
        ");
    }
};
