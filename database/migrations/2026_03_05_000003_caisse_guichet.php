<?php

/**
 * ============================================================
 * MIGRATION 3/3 — MODULE CAISSE GUICHET (MULTI-DEVISES)
 * ============================================================
 * Fichier : 2026_03_05_000003_caisse_guichet.php
 * Prérequis : migrations 1/3 et 2/3 exécutées avant.
 *             (tb_agents et tb_devises doivent exister)
 *
 * Tables créées (dans l'ordre des dépendances FK) :
 * ┌──────────────────────────────┬──────────────────────────────────┐
 * │ SECTION A — Comptabilité     │ tb_plan_comptable                │
 * ├──────────────────────────────┼──────────────────────────────────┤
 * │ SECTION B — Guichets         │ tb_caisses_guichets (mère)       │
 * ├──────────────────────────────┼──────────────────────────────────┤
 * │ SECTION C — Soldes           │ tb_caisses_guichets_soldes       │
 * │             Multi-devises    │   (→ guichets + tb_devises)      │
 * ├──────────────────────────────┼──────────────────────────────────┤
 * │ SECTION D — Flux de fonds    │ tb_mouvements_inter_caisses      │
 * │                              │   (→ guichets + agents + devises)│
 * ├──────────────────────────────┼──────────────────────────────────┤
 * │ SECTION E — Clôture          │ tb_cloture_caisse                │
 * │                              │   (→ guichets + agents + devises)│
 * └──────────────────────────────┴──────────────────────────────────┘
 *
 * ── Principe Multi-devises ───────────────────────────────────────
 * Un seul guichet (ex: G01) peut détenir simultanément un solde
 * en CDF ET en USD. Chaque solde est une ligne distincte dans
 * tb_caisses_guichets_soldes. La clôture se fait devise par devise.
 *
 * Mise à jour d'un solde dans Laravel :
 *   DB::table('tb_caisses_guichets_soldes')
 *     ->where('guichet_id', $id)
 *     ->where('devise_code', $devise)   // 'CDF' ou 'USD'
 *     ->increment('solde_en_caisse', $montant);
 * ─────────────────────────────────────────────────────────────────
 * ============================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // ------------------------------------------------------------
    public function up(): void
    // ------------------------------------------------------------
    {
        // ========================================================
        // SECTION A — PLAN COMPTABLE
        // Référentiel des numéros de comptes généraux de la banque.
        // Exemples :
        //   5701 → Caisse CDF   (ACTIF)
        //   5702 → Caisse USD   (ACTIF)
        //   2511 → Dépôts vue   (PASSIF)
        //   7001 → Intérêts     (PRODUIT)
        // ========================================================
        Schema::create('tb_plan_comptable', function (Blueprint $table) {
            $table->string('numero_compte', 20)->primary();
            $table->string('libelle', 191);
            $table->enum('type_compte', ['ACTIF', 'PASSIF', 'CHARGE', 'PRODUIT']);
        });

        // ========================================================
        // SECTION B — CAISSES / GUICHETS (table mère)
        // Représente un point de service physique.
        //
        // ⚠  Pas de colonne devise ni solde dans cette table.
        //    → Architecture multi-devises : les soldes sont dans
        //      tb_caisses_guichets_soldes (Section C).
        //
        // ⚠  Pas d'agent_matricule ici.
        //    → L'agent titulaire du guichet est géré par
        //      tb_affectations (migration 2/3, Section C).
        // ========================================================
        Schema::create('tb_caisses_guichets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code_guichet', 20)->unique(); // ex: G01, G02
            $table->string('intitule', 100);              // ex: Caisse Principale
            $table->enum('statut_operationnel', ['OUVERT', 'FERME', 'SUSPENDU'])
                  ->default('FERME');
            $table->timestamp('created_at')
                  ->nullable()
                  ->default(DB::raw('CURRENT_TIMESTAMP'));
        });

        // ========================================================
        // SECTION C — SOLDES MULTI-DEVISES PAR GUICHET
        // Une ligne = solde d'UN guichet dans UNE devise.
        //
        // La contrainte UNIQUE(guichet_id, devise_code) garantit
        // qu'il n'existe qu'un seul solde par couple (guichet, devise).
        //
        // ON DELETE CASCADE : si un guichet est supprimé, tous ses
        // soldes sont automatiquement effacés.
        // ========================================================
        Schema::create('tb_caisses_guichets_soldes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('guichet_id');
            $table->string('devise_code', 3);             // ex: CDF, USD
            $table->decimal('solde_en_caisse', 18, 2)->default(0.00);
            $table->timestamp('updated_at')
                  ->nullable()
                  ->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            // Un seul solde par couple (guichet + devise)
            $table->unique(['guichet_id', 'devise_code'], 'uk_guichet_devise');

            $table->foreign('guichet_id', 'fk_solde_guichet')
                  ->references('id')->on('tb_caisses_guichets')
                  ->restrictOnDelete()->restrictOnUpdate();

            $table->foreign('devise_code', 'fk_solde_devise')
                  ->references('code_iso')->on('tb_devises')
                  ->restrictOnDelete()->restrictOnUpdate();
        });

        // ========================================================
        // SECTION D — MOUVEMENTS INTER-CAISSES
        // Trace chaque mouvement de fonds entre guichets ou
        // depuis/vers le vault central (coffre principal).
        //
        // Valeurs des colonnes nullable :
        //   guichet_source_id = NULL → alimentation depuis le vault
        //   guichet_dest_id   = NULL → dégagement vers le vault
        //
        // Types de flux :
        //   ALIMENTATION → le vault approvisionne un guichet
        //   DEGAGEMENT   → un guichet reverse des fonds au vault
        //   TRANSFERT    → d'un guichet vers un autre guichet
        // ========================================================
        Schema::create('tb_mouvements_inter_caisses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('guichet_source_id')->nullable();
            $table->unsignedBigInteger('guichet_dest_id')->nullable();
            $table->string('agent_initiateur', 50);       // Matricule de l'agent
            $table->enum('type_flux', ['ALIMENTATION', 'DEGAGEMENT', 'TRANSFERT']);
            $table->decimal('montant', 18, 2);
            $table->string('devise_code', 3);
            $table->string('reference_bordereau', 50)
                  ->nullable()
                  ->unique('uk_reference_bordereau');     // Numéro de bordereau unique
            $table->timestamp('date_mouvement')
                  ->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('guichet_source_id', 'fk_mouv_guichet_src')
                  ->references('id')->on('tb_caisses_guichets')
                  ->restrictOnDelete()->restrictOnUpdate();

            $table->foreign('guichet_dest_id', 'fk_mouv_guichet_dest')
                  ->references('id')->on('tb_caisses_guichets')
                  ->restrictOnDelete()->restrictOnUpdate();

            $table->foreign('agent_initiateur', 'fk_mouv_agent')
                  ->references('matricule')->on('tb_agents')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreign('devise_code', 'fk_mouv_devise')
                  ->references('code_iso')->on('tb_devises')
                  ->restrictOnDelete()->restrictOnUpdate();
        });

        // ========================================================
        // SECTION E — CLÔTURE DE CAISSE
        // Arrêté de caisse effectué en fin de journée.
        //
        // ⚠  En multi-devises, l'agent fait UN arrêté par devise.
        //    Ex : Jean clôture G01-CDF puis G01-USD séparément.
        //
        // Colonnes clés :
        //   solde_comptable → solde système (lu dans Section C)
        //   solde_physique  → solde compté physiquement
        //   ecart_caisse    → = solde_physique - solde_comptable
        //                     (positif = excédent, négatif = déficit)
        //   detail_billetage → JSON des coupures comptées
        //                      ex: {"50000":3, "20000":5, "10000":2}
        // ========================================================
        Schema::create('tb_cloture_caisse', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('guichet_id');
            $table->string('devise_code', 3);
            $table->decimal('solde_comptable', 18, 2);     // Solde système
            $table->decimal('solde_physique',  18, 2);     // Solde compté
            $table->decimal('ecart_caisse',    18, 2);     // Différence
            $table->json('detail_billetage')->nullable();  // Coupures par valeur
            $table->string('agent_cloturant', 50);         // Qui a validé l'arrêté
            $table->timestamp('date_cloture')
                  ->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('guichet_id', 'fk_cloture_guichet')
                  ->references('id')->on('tb_caisses_guichets')
                  ->restrictOnDelete()->restrictOnUpdate();

            $table->foreign('devise_code', 'fk_cloture_devise')
                  ->references('code_iso')->on('tb_devises')
                  ->restrictOnDelete()->restrictOnUpdate();

            $table->foreign('agent_cloturant', 'fk_cloture_agent')
                  ->references('matricule')->on('tb_agents')
                  ->restrictOnDelete()->cascadeOnUpdate();
        });

        // ====================================================
        // DONNÉES DE DÉMARRAGE — Plan comptable, Guichets
        // ====================================================
        DB::table('tb_plan_comptable')->insertOrIgnore([
            ['numero_compte' => '5701', 'libelle' => 'Caisse CDF',                    'type_compte' => 'ACTIF'],
            ['numero_compte' => '5702', 'libelle' => 'Caisse USD',                    'type_compte' => 'ACTIF'],
            ['numero_compte' => '5703', 'libelle' => 'Caisse EUR',                    'type_compte' => 'ACTIF'],
            ['numero_compte' => '2511', 'libelle' => 'Dépôts à vue clients',          'type_compte' => 'PASSIF'],
            ['numero_compte' => '2512', 'libelle' => 'Dépôts à terme clients',        'type_compte' => 'PASSIF'],
            ['numero_compte' => '7001', 'libelle' => 'Intérêts et produits assimilés','type_compte' => 'PRODUIT'],
            ['numero_compte' => '6001', 'libelle' => 'Frais bancaires',               'type_compte' => 'CHARGE'],
            ['numero_compte' => '1011', 'libelle' => 'Capital social',                'type_compte' => 'PASSIF'],
        ]);

        DB::table('tb_caisses_guichets')->insertOrIgnore([
            ['code_guichet' => 'G01', 'intitule' => 'Guichet Principal CDF/USD', 'statut_operationnel' => 'FERME', 'created_at' => now()],
            ['code_guichet' => 'G02', 'intitule' => 'Guichet Secondaire CDF',    'statut_operationnel' => 'FERME', 'created_at' => now()],
        ]);
    }

    // ------------------------------------------------------------
    public function down(): void
    // ------------------------------------------------------------
    {
        // Suppression dans l'ordre inverse (enfants → parents)
        Schema::dropIfExists('tb_cloture_caisse');
        Schema::dropIfExists('tb_mouvements_inter_caisses');
        Schema::dropIfExists('tb_caisses_guichets_soldes');
        Schema::dropIfExists('tb_caisses_guichets');
        Schema::dropIfExists('tb_plan_comptable');
    }
};
