<?php

/**
 * ============================================================
 * MIGRATION 2/3 — RESSOURCES HUMAINES + BANQUE CŒUR
 * ============================================================
 * Fichier : 2026_03_05_000002_rh_banque_core.php
 * Prérequis : 2026_03_05_000001_laravel_core.php exécuté avant.
 *
 * Tables créées (dans l'ordre des dépendances FK) :
 * ┌──────────────────────────────┬──────────────────────────────────┐
 * │ SECTION A — RH Organigramme  │ tb_services                      │
 * │                              │ tb_postes (→ tb_services)        │
 * │                              │ tb_agents                        │
 * ├──────────────────────────────┼──────────────────────────────────┤
 * │ SECTION B — Authentification │ users (→ tb_agents)              │
 * │                              │ tb_role_user (→ users, tb_roles) │
 * ├──────────────────────────────┼──────────────────────────────────┤
 * │ SECTION C — Organisation RH  │ tb_zones (→ tb_agents)          │
 * │                              │ tb_affectations (→ agents,postes)│
 * │                              │ tb_portefeuilles_agents          │
 * ├──────────────────────────────┼──────────────────────────────────┤
 * │ SECTION D — Référentiels     │ tb_devises                       │
 * │             Monétaires       │ tb_taux_echanges (→ tb_devises)  │
 * ├──────────────────────────────┼──────────────────────────────────┤
 * │ SECTION E — Clients & Comptes│ tb_clients (→ tb_zones)          │
 * │                              │ tb_comptes (→ clients,devises)   │
 * │                              │ tb_transactions (→ comptes)      │
 * └──────────────────────────────┴──────────────────────────────────┘
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
        // SECTION A — RESSOURCES HUMAINES (organigramme)
        // ========================================================

        // A1. Services de la banque (ex : Caisse, Crédit, Direction)
        Schema::create('tb_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nom', 191);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // A2. Postes de travail (ex : Caissier Principal, Chargé de Crédit)
        // Chaque poste appartient à un service.
        Schema::create('tb_postes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_id');
            $table->string('nom', 191);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('service_id', 'tb_postes_service_id_foreign')
                  ->references('id')->on('tb_services')
                  ->restrictOnDelete()->cascadeOnUpdate();
        });

        // A3. Agents — Table centrale référencée par toutes les entités métier.
        // (users, zones, affectations, transactions, guichets, clôtures…)
        Schema::create('tb_agents', function (Blueprint $table) {
            $table->string('matricule', 50)->primary();  // ex: AG001
            $table->string('nom', 191);
            $table->string('postnom', 191)->nullable();
            $table->string('prenom', 191)->nullable();
            $table->enum('sexe', ['M', 'F'])->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('telephone', 50)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('adresse', 191)->nullable();
            $table->string('photo', 255)->nullable();
            $table->date('date_embauche')->nullable();
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
        });

        // ========================================================
        // SECTION B — AUTHENTIFICATION ET ATTRIBUTION DES RÔLES
        // ========================================================

        // B1. Comptes utilisateurs Laravel (1 user = 1 agent)
        // agent_matricule peut être NULL (compte admin système sans agent)
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('agent_matricule', 50)->nullable(); // Taille alignée sur tb_agents.matricule (50)
            $table->string('name', 191);
            $table->string('email', 191)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 191);
            $table->string('remember_token', 100)->nullable();
            $table->string('etat', 20)->default('actif'); // actif | inactif | suspendu
            $table->timestamps();

            $table->foreign('agent_matricule', 'fk_agent_matricule')
                  ->references('matricule')->on('tb_agents')
                  ->nullOnDelete()->cascadeOnUpdate();
        });

        // B2. Pivot Users ↔ Rôles (un user peut avoir plusieurs rôles)
        // tb_roles est défini dans la migration 1/3.
        Schema::create('tb_role_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('role_code', 20);
            $table->timestamps();

            $table->foreign('user_id', 'contrainte_user')
                  ->references('id')->on('users')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreign('role_code', 'contrainte_role')
                  ->references('code')->on('tb_roles')
                  ->restrictOnDelete()->cascadeOnUpdate();
        });

        // ========================================================
        // SECTION C — ORGANISATION TERRITORIALE ET RH OPÉRATIONNEL
        // ========================================================

        // C1. Zones géographiques commerciales
        // Chaque zone est suivie par un agent commercial.
        Schema::create('tb_zones', function (Blueprint $table) {
            $table->string('code_zone', 50)->primary();  // ex: ZONE_GOMBE
            $table->string('nom', 100);
            $table->string('agent_commercial_matricule', 50);
            $table->string('commune', 100);
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();

            $table->foreign('agent_commercial_matricule', 'tb_zones_ibfk_1')
                  ->references('matricule')->on('tb_agents')
                  ->restrictOnDelete()->restrictOnUpdate();
        });

        // C2. Affectations des agents aux postes (historique complet)
        // Un agent peut être réaffecté — chaque période est une ligne.
        Schema::create('tb_affectations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('agent_matricule', 50);
            $table->unsignedBigInteger('poste_id');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();         // NULL = affectation en cours
            $table->string('Etat', 50);                  // ex: ACTIF, TERMINE, SUSPENDU
            $table->timestamps();

            $table->foreign('agent_matricule', 'tb_affectations_agent_matricule_foreign')
                  ->references('matricule')->on('tb_agents')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreign('poste_id', 'tb_affectations_poste_id_foreign')
                  ->references('id')->on('tb_postes')
                  ->restrictOnDelete()->cascadeOnUpdate();
        });

        // C3. Portefeuilles des agents commerciaux
        // Lie un agent à un ensemble de comptes clients (pour les commissions).
        Schema::create('tb_portefeuilles_agents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('agent_matricule', 50);
            $table->string('nom_portefeuille', 100);
            $table->decimal('taux_commission_agent', 5, 2)->default(0.00);
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('agent_matricule', 'fk_port_agent')
                  ->references('matricule')->on('tb_agents')
                  ->restrictOnDelete()->cascadeOnUpdate();
        });

        // ========================================================
        // SECTION D — RÉFÉRENTIELS MONÉTAIRES
        // Ces tables sont des référentiels purs (pas de dépendances).
        // Elles doivent être remplies avant toute opération bancaire.
        // ========================================================

        // D1. Devises gérées par la banque (ex : CDF, USD, EUR)
        // est_reference = 1 → devise pivot de référence (généralement CDF)
        Schema::create('tb_devises', function (Blueprint $table) {
            $table->string('code_iso', 3)->primary();    // ex: CDF, USD, EUR
            $table->string('nom', 50);                   // ex: Franc Congolais
            $table->string('symbole', 5);                // ex: FC, $, €
            $table->tinyInteger('est_reference')->default(0); // 1 = devise de référence
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
        });

        // D2. Taux de change entre devises (historique journalier)
        // Chaque modification de taux crée une nouvelle ligne.
        Schema::create('tb_taux_echanges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('devise_source', 3);           // ex: USD
            $table->string('devise_destination', 3);      // ex: CDF
            $table->decimal('taux', 18, 4);               // ex: 2800.0000
            $table->timestamp('date_application')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

            $table->foreign('devise_source', 'fk_devise_src')
                  ->references('code_iso')->on('tb_devises')
                  ->restrictOnDelete()->restrictOnUpdate();

            $table->foreign('devise_destination', 'fk_devise_dest')
                  ->references('code_iso')->on('tb_devises')
                  ->restrictOnDelete()->restrictOnUpdate();
        });

        // ========================================================
        // SECTION E — CLIENTS ET COMPTES BANCAIRES
        // ========================================================

        // E1. Clients de la banque (personnes physiques et morales)
        // Chaque client est localisé dans une zone commerciale.
        Schema::create('tb_clients', function (Blueprint $table) {
            $table->string('matricule', 191)->unique();   // ex: CLI-2026-0001
            $table->string('code_zone', 50);
            $table->string('nom', 191);
            $table->string('postnom', 191);
            $table->string('prenom', 191);
            $table->string('email', 191)->nullable();
            $table->string('telephone', 191)->nullable();
            $table->enum('sexe', ['M', 'F']);
            $table->date('date_naissance');
            $table->string('lieu_naissance', 191);
            $table->string('adresse', 191);
            $table->string('etat_civil', 191);
            $table->string('nom_conjoint', 191)->nullable();
            // Pièce d'identité
            $table->string('type_piece_identite', 191);
            $table->string('lieu_delivrance_piece', 191);
            $table->date('date_delivrance_piece');
            $table->string('numero_piece_identite', 191);
            $table->string('photo', 191)->nullable();
            // Informations professionnelles (optionnelles)
            $table->string('secteur_activite', 191)->nullable();
            $table->string('type_activite', 191)->nullable();
            $table->string('nom_entreprise', 191)->nullable();
            $table->string('adresse_entreprise', 191)->nullable();
            $table->string('telephone_entreprise', 191)->nullable();
            $table->string('statut_entreprise', 191)->nullable();
            $table->string('nombre_annees_experience', 191)->nullable();
            $table->decimal('revenu_mensuel', 15, 2)->nullable();
            $table->string('revenu_mensuel_devise', 10)->nullable();
            $table->string('autres_details_activite', 191)->nullable();
            $table->timestamps();

            $table->foreign('code_zone', 'tb_zones_ibfk_11')
                  ->references('code_zone')->on('tb_zones')
                  ->restrictOnDelete()->restrictOnUpdate();
        });

        // E2. Comptes bancaires (un client peut avoir plusieurs comptes)
        // Chaque compte est dans une seule devise.
        // portefeuille_id lie le compte à l'agent commercial référent.
        Schema::create('tb_comptes', function (Blueprint $table) {
            $table->string('code_compte', 64)->primary(); // ex: CDF-COURANT-00142
            $table->string('client_matricule', 191);
            $table->string('devise', 3);
            $table->enum('type', ['COURANT', 'EPARGNE_LIBRE', 'EPARGNE_BLOQUEE', 'CAUTION_CREDIT']);
            $table->unsignedBigInteger('portefeuille_id')->nullable(); // Agent commercial rattaché
            $table->decimal('solde_reel',   18, 2)->default(0.00);
            $table->decimal('solde_bloque', 18, 2)->default(0.00);    // Fonds bloqués (crédit / caution)
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->nullable();

            $table->foreign('devise', 'fk_compte_devise')
                  ->references('code_iso')->on('tb_devises')
                  ->restrictOnDelete()->restrictOnUpdate();

            $table->foreign('portefeuille_id', 'fk_compte_portefeuille')
                  ->references('id')->on('tb_portefeuilles_agents')
                  ->restrictOnDelete()->restrictOnUpdate();

            $table->foreign('client_matricule', 'tb_comptes_ibfk_112')
                  ->references('matricule')->on('tb_clients')
                  ->restrictOnDelete()->restrictOnUpdate();
        });

        // E3. Transactions sur comptes (dépôts, retraits, virements)
        // Chaque opération de guichet sur un compte crée une ligne ici.
        Schema::create('tb_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('compte_code', 64);
            $table->string('agent_matricule', 50);
            $table->enum('type', ['DEPOT', 'RETRAIT', 'VIREMENT', 'REMBOURSEMENT']);
            $table->decimal('montant', 18, 2);
            $table->string('reference', 50)->unique()->nullable(); // Numéro de reçu

            $table->foreign('compte_code', 'tb_transactions_ibfk_1')
                  ->references('code_compte')->on('tb_comptes')
                  ->restrictOnDelete()->restrictOnUpdate();

            $table->foreign('agent_matricule', 'tb_transactions_ibfk_2')
                  ->references('matricule')->on('tb_agents')
                  ->restrictOnDelete()->restrictOnUpdate();
        });

        // ====================================================
        // DONNÉES DE DÉMARRAGE — Services, Postes, Agents,
        //                        Users, Devises, Affectations
        // ====================================================
        $now = now();

        DB::table('tb_services')->insertOrIgnore([
            ['id' => 1, 'nom' => 'Direction Générale', 'description' => 'Administration centrale du système',        'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nom' => 'Caisse',             'description' => 'Gestion des guichets et opérations caisse', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nom' => 'Ressources Humaines', 'description' => 'Gestion du personnel et des affectations', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('tb_postes')->insertOrIgnore([
            ['id' => 1, 'service_id' => 1, 'nom' => 'Administrateur Système', 'description' => 'Poste réservé au compte administrateur', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'service_id' => 2, 'nom' => 'Caissier Principal',     'description' => 'Gestion du guichet principal',            'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'service_id' => 3, 'nom' => 'Responsable RH',         'description' => 'Gestion des agents et des affectations',  'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('tb_agents')->insertOrIgnore([
            ['matricule' => 'AG-EBENKGA-26-00001', 'nom' => 'BMB',     'postnom' => 'ADMIN', 'prenom' => 'Système', 'sexe' => 'M', 'date_naissance' => null, 'telephone' => null,             'email' => 'bmb@bmb.cd',             'adresse' => null,       'photo' => null, 'date_embauche' => now()->toDateString(), 'statut' => 'actif', 'created_at' => $now, 'updated_at' => $now],
            ['matricule' => 'AG-EBENKGA-26-00002', 'nom' => 'MULUMBA', 'postnom' => null,    'prenom' => 'Jean',    'sexe' => 'M', 'date_naissance' => '1990-06-15', 'telephone' => '+243810000002', 'email' => 'jean.caissier@bmb.cd', 'adresse' => 'Kinshasa', 'photo' => null, 'date_embauche' => now()->toDateString(), 'statut' => 'actif', 'created_at' => $now, 'updated_at' => $now],
            ['matricule' => 'AG-EBENKGA-26-00003', 'nom' => 'KASONGO', 'postnom' => null,    'prenom' => 'Marie',   'sexe' => 'F', 'date_naissance' => '1992-03-20', 'telephone' => '+243810000003', 'email' => 'marie.rh@bmb.cd',      'adresse' => 'Kinshasa', 'photo' => null, 'date_embauche' => now()->toDateString(), 'statut' => 'actif', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('tb_devises')->insertOrIgnore([
            ['code_iso' => 'CDF', 'nom' => 'Franc Congolais',  'symbole' => 'Fc', 'est_reference' => 1, 'created_at' => $now, 'updated_at' => null],
            ['code_iso' => 'USD', 'nom' => 'Dollar Américain', 'symbole' => '$',  'est_reference' => 0, 'created_at' => $now, 'updated_at' => null],
            ['code_iso' => 'EUR', 'nom' => 'Euro',             'symbole' => '€',  'est_reference' => 0, 'created_at' => $now, 'updated_at' => null],
        ]);

        // Utilisateurs : mot de passe bcrypt cost=12
        //   bmb@bmb.cd           → Bmb@2026
        //   jean.caissier@bmb.cd → Caissier@2026
        //   marie.rh@bmb.cd      → AgentRH@2026
        DB::table('users')->insertOrIgnore([
            ['agent_matricule' => 'AG-EBENKGA-26-00001', 'name' => 'bmb',           'email' => 'bmb@bmb.cd',           'email_verified_at' => $now, 'password' => '$2y$12$h2eVRVSTyES1nDSAzc91q.PGyeA8TvOdjWlEz5WXEo8OGop39HuTW', 'remember_token' => null, 'etat' => 'actif', 'created_at' => $now, 'updated_at' => $now],
            ['agent_matricule' => 'AG-EBENKGA-26-00002', 'name' => 'jean_caissier', 'email' => 'jean.caissier@bmb.cd', 'email_verified_at' => $now, 'password' => '$2y$12$o/m3X.G8ImNrB8WgzrankOb5R8trQnBbSwK4vVzYXa7WHjy6AIIfG', 'remember_token' => null, 'etat' => 'actif', 'created_at' => $now, 'updated_at' => $now],
            ['agent_matricule' => 'AG-EBENKGA-26-00003', 'name' => 'marie_rh',      'email' => 'marie.rh@bmb.cd',      'email_verified_at' => $now, 'password' => '$2y$12$j8T6Zy8w4q.zzZ1eNt/eeuak5l4H/PdUWwz7rmUNqNKJL7UQ7PR4m', 'remember_token' => null, 'etat' => 'actif', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Affectations initiales
        DB::table('tb_affectations')->insertOrIgnore([
            ['agent_matricule' => 'AG-EBENKGA-26-00001', 'poste_id' => 1, 'date_debut' => now()->toDateString(), 'date_fin' => null, 'Etat' => 'ACTIF', 'created_at' => $now, 'updated_at' => $now],
            ['agent_matricule' => 'AG-EBENKGA-26-00002', 'poste_id' => 2, 'date_debut' => now()->toDateString(), 'date_fin' => null, 'Etat' => 'ACTIF', 'created_at' => $now, 'updated_at' => $now],
            ['agent_matricule' => 'AG-EBENKGA-26-00003', 'poste_id' => 3, 'date_debut' => now()->toDateString(), 'date_fin' => null, 'Etat' => 'ACTIF', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Rôles → utilisateurs (id déterminés par l'ordre d'insertion)
        $users = DB::table('users')->whereIn('email', ['bmb@bmb.cd', 'jean.caissier@bmb.cd', 'marie.rh@bmb.cd'])->get()->keyBy('email');
        DB::table('tb_role_user')->insertOrIgnore([
            ['user_id' => $users['bmb@bmb.cd']->id,           'role_code' => 'EBEN-ROL1', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['jean.caissier@bmb.cd']->id, 'role_code' => 'EBEN-ROL2', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['marie.rh@bmb.cd']->id,      'role_code' => 'EBEN-ROL4', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    // ------------------------------------------------------------
    public function down(): void
    // ------------------------------------------------------------
    {
        // Suppression dans l'ordre inverse (enfants → parents)
        Schema::dropIfExists('tb_transactions');
        Schema::dropIfExists('tb_comptes');
        Schema::dropIfExists('tb_clients');
        Schema::dropIfExists('tb_taux_echanges');
        Schema::dropIfExists('tb_devises');
        Schema::dropIfExists('tb_portefeuilles_agents');
        Schema::dropIfExists('tb_affectations');
        Schema::dropIfExists('tb_zones');
        Schema::dropIfExists('tb_role_user');
        Schema::dropIfExists('users');
        Schema::dropIfExists('tb_agents');
        Schema::dropIfExists('tb_postes');
        Schema::dropIfExists('tb_services');
    }
};
