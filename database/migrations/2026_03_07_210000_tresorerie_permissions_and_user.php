<?php

/**
 * ============================================================
 * MIGRATION 7 – PERMISSIONS TRÉSORERIE + RÔLE + UTILISATEUR
 * ============================================================
 * Ajoute le module Trésorerie/Coffre-Fort comme entité RBAC
 * indépendante (séparée du module Administration).
 *
 * Permissions créées : EBEN-PER44 → EBEN-PER48
 * Rôle créé          : EBEN-ROL8  (Trésorier)
 *
 * Droits accordés :
 *  ┌───────────────────┬─────────────────────────────────────────┐
 *  │ Rôle              │ Permissions Trésorerie accordées         │
 *  ├───────────────────┼─────────────────────────────────────────┤
 *  │ ROL1 Administ.    │ PER44 → PER48  (total : PER1-PER48)     │
 *  │ ROL3 Directeur    │ PER44, PER48   (lecture + journal)       │
 *  │ ROL5 Superviseur  │ PER44, PER48   (lecture + journal)       │
 *  │ ROL8 Trésorier    │ PER44 → PER48 + PER10,36,37,38          │
 *  └───────────────────┴─────────────────────────────────────────┘
 *
 * Utilisateur de démarrage inséré à l'installation :
 *  Email : tresorier@bmb.cd   |  Mot de passe : Tresorier@2026
 *
 * ⚠  INSERT OR IGNORE partout – safe à ré-exécuter.
 * ============================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // ====================================================
        // 1. PERMISSIONS MODULE TRÉSORERIE  (PER44 → PER48)
        // ====================================================
        DB::table('tb_permissions')->insertOrIgnore([
            [
                'code'        => 'EBEN-PER44',
                'nom'         => 'Voir trésorerie',
                'description' => 'Accès au module trésorerie/coffre-fort (vue d\'ensemble et soldes)',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'code'        => 'EBEN-PER45',
                'nom'         => 'Approvisionner coffre',
                'description' => 'Enregistrer un approvisionnement externe (banque → coffre)',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'code'        => 'EBEN-PER46',
                'nom'         => 'Valider mouvements trésorerie',
                'description' => 'Approuver / rejeter les opérations coffre-fort en attente',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'code'        => 'EBEN-PER47',
                'nom'         => 'Alimenter guichets',
                'description' => 'Transférer des fonds entre le coffre central et les guichets',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'code'        => 'EBEN-PER48',
                'nom'         => 'Journal trésorerie',
                'description' => 'Consulter le journal complet de la caisse centrale (historique)',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);

        // ====================================================
        // 2. RÔLE EBEN-ROL8 — Trésorier
        // ====================================================
        DB::table('tb_roles')->insertOrIgnore([
            [
                'code'        => 'EBEN-ROL8',
                'nom'         => 'Trésorier',
                'description' => 'Gestion complète du coffre-fort central, approvisionnements et transferts',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);

        // ====================================================
        // 3. AFFECTATION ROL1 (Administrateur) → PER44-PER48
        //    (Le super-admin a tous les droits, y compris trésorerie)
        // ====================================================
        DB::table('tb_role_permission')->insertOrIgnore(
            array_map(
                fn($n) => ['role_code' => 'EBEN-ROL1', 'permission_code' => "EBEN-PER{$n}", 'created_at' => $now, 'updated_at' => $now],
                range(44, 48)
            )
        );

        // ====================================================
        // 4. AFFECTATION ROL3 (Directeur) → lecture trésorerie
        // ====================================================
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER44', 'created_at' => $now, 'updated_at' => $now], // Voir trésorerie
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER48', 'created_at' => $now, 'updated_at' => $now], // Journal
        ]);

        // ====================================================
        // 5. AFFECTATION ROL5 (Superviseur) → lecture trésorerie
        // ====================================================
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER44', 'created_at' => $now, 'updated_at' => $now], // Voir trésorerie
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER48', 'created_at' => $now, 'updated_at' => $now], // Journal
        ]);

        // ====================================================
        // 6. AFFECTATION ROL8 (Trésorier) — accès complet trésorerie
        // ====================================================
        DB::table('tb_role_permission')->insertOrIgnore([
            // Module trésorerie complet
            ['role_code' => 'EBEN-ROL8', 'permission_code' => 'EBEN-PER44', 'created_at' => $now, 'updated_at' => $now], // Voir trésorerie
            ['role_code' => 'EBEN-ROL8', 'permission_code' => 'EBEN-PER45', 'created_at' => $now, 'updated_at' => $now], // Approvisionner coffre
            ['role_code' => 'EBEN-ROL8', 'permission_code' => 'EBEN-PER46', 'created_at' => $now, 'updated_at' => $now], // Valider mouvements
            ['role_code' => 'EBEN-ROL8', 'permission_code' => 'EBEN-PER47', 'created_at' => $now, 'updated_at' => $now], // Alimenter guichets
            ['role_code' => 'EBEN-ROL8', 'permission_code' => 'EBEN-PER48', 'created_at' => $now, 'updated_at' => $now], // Journal
            // Accès lecture aux modules connexes
            ['role_code' => 'EBEN-ROL8', 'permission_code' => 'EBEN-PER10', 'created_at' => $now, 'updated_at' => $now], // Voir caisse
            ['role_code' => 'EBEN-ROL8', 'permission_code' => 'EBEN-PER20', 'created_at' => $now, 'updated_at' => $now], // Voir devises
            ['role_code' => 'EBEN-ROL8', 'permission_code' => 'EBEN-PER36', 'created_at' => $now, 'updated_at' => $now], // Rapports opérationnels
            ['role_code' => 'EBEN-ROL8', 'permission_code' => 'EBEN-PER37', 'created_at' => $now, 'updated_at' => $now], // Rapports financiers
            ['role_code' => 'EBEN-ROL8', 'permission_code' => 'EBEN-PER38', 'created_at' => $now, 'updated_at' => $now], // Exporter rapports
        ]);

        // ====================================================
        // 7. AGENT TRÉSORIER  — matricule AG-EBENKGA-26-00004
        // ====================================================
        DB::table('tb_agents')->insertOrIgnore([
            [
                'matricule'      => 'AG-EBENKGA-26-00004',
                'nom'            => 'ILUNGA',
                'postnom'        => null,
                'prenom'         => 'Prosper',
                'sexe'           => 'M',
                'date_naissance' => '1985-09-10',
                'telephone'      => '+243810000004',
                'email'          => 'tresorier@bmb.cd',
                'adresse'        => 'Kinshasa',
                'photo'          => null,
                'date_embauche'  => $now->toDateString(),
                'statut'         => 'actif',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
        ]);

        // ====================================================
        // 8. SERVICE & POSTE TRÉSORERIE (si absents)
        // ====================================================
        DB::table('tb_services')->insertOrIgnore([
            [
                'id'          => 3,
                'nom'         => 'Trésorerie & Finance',
                'description' => 'Gestion du coffre-fort central, flux monétaires et reporting financier',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);

        DB::table('tb_postes')->insertOrIgnore([
            [
                'id'          => 4,
                'service_id'  => 3,
                'nom'         => 'Chef Trésorier',
                'description' => 'Responsable du coffre-fort central et des flux inter-caisses',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);

        // ====================================================
        // 9. UTILISATEUR TRÉSORIER
        //    Login    : tresorier@bmb.cd
        //    Password : Tresorier@2026
        // ====================================================
        $tresorierEmail = 'tresorier@bmb.cd';

        DB::table('users')->insertOrIgnore([
            [
                'agent_matricule'   => 'AG-EBENKGA-26-00004',
                'name'              => 'tresorier',
                'email'             => $tresorierEmail,
                'email_verified_at' => $now,
                'password'          => Hash::make('Tresorier@2026'),
                'remember_token'    => null,
                'etat'              => 'actif',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ]);

        // Affectation ROL8 → user trésorier
        $tresorierUser = DB::table('users')->where('email', $tresorierEmail)->first();
        if ($tresorierUser) {
            DB::table('tb_role_user')->insertOrIgnore([
                [
                    'user_id'    => $tresorierUser->id,
                    'role_code'  => 'EBEN-ROL8',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        }

        // Affectation agent → poste Trésorier
        DB::table('tb_affectations')->insertOrIgnore([
            [
                'agent_matricule' => 'AG-EBENKGA-26-00004',
                'poste_id'        => 4,
                'guichet_id'      => null,
                'date_debut'      => $now->toDateString(),
                'date_fin'        => null,
                'Etat'            => 'ACTIF',
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
        ]);
    }

    // --------------------------------------------------------
    public function down(): void
    // --------------------------------------------------------
    {
        // 1. Supprimer le rôle du user trésorier
        $tresorierUser = DB::table('users')->where('email', 'tresorier@bmb.cd')->first();
        if ($tresorierUser) {
            DB::table('tb_role_user')->where('user_id', $tresorierUser->id)->delete();
        }

        // 2. Supprimer l'affectation
        DB::table('tb_affectations')->where('agent_matricule', 'AG-EBENKGA-26-00004')->delete();

        // 3. Supprimer le user
        DB::table('users')->where('email', 'tresorier@bmb.cd')->delete();

        // 4. Supprimer l'agent
        DB::table('tb_agents')->where('matricule', 'AG-EBENKGA-26-00004')->delete();

        // 5. Supprimer poste & service trésorerie
        DB::table('tb_postes')->where('id', 4)->delete();
        DB::table('tb_services')->where('id', 3)->delete();

        // 6. Supprimer les affectations rôle/permission créées ici
        $permCodes = array_map(fn($n) => "EBEN-PER{$n}", range(44, 48));

        DB::table('tb_role_permission')
            ->whereIn('permission_code', $permCodes)
            ->delete();

        DB::table('tb_role_permission')
            ->where('role_code', 'EBEN-ROL8')
            ->delete();

        // 7. Supprimer le rôle ROL8
        DB::table('tb_roles')->where('code', 'EBEN-ROL8')->delete();

        // 8. Supprimer les permissions PER44-PER48
        DB::table('tb_permissions')
            ->whereIn('code', $permCodes)
            ->delete();
    }
};
