<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\RH\Agent;
use App\Models\RH\Role;
use App\Models\RH\Permission;
use App\Models\User;

/**
 * AdminSeeder — Données d'amorçage du système
 *
 * Insère dans l'ordre FK :
 *   1. tb_services          (référencé par tb_postes)
 *   2. tb_postes             (référencé par tb_affectations)
 *   3. tb_agents             (référencé par users)
 *   4. tb_roles              (référencé par tb_role_permission, tb_role_user)
 *   5. tb_permissions        (référencé par tb_role_permission)
 *   6. tb_role_permission    (ADMIN reçoit tous les droits)
 *   7. users                 (admin système)
 *   8. tb_role_user          (user admin → rôle ADMIN)
 *   9. tb_devises            (CDF référence, USD, EUR)
 *
 * Identifiants par défaut :
 *   Login    : bmb
 *   Mot de passe : Bmb@2026
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------------------------------------------
        // 1. Service — Direction Générale
        // ----------------------------------------------------------------
        DB::table('tb_services')->insertOrIgnore([
            [
                'id'          => 1,
                'nom'         => 'Direction Générale',
                'description' => 'Administration centrale du système',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        // ----------------------------------------------------------------
        // 2. Poste — Administrateur Système
        // ----------------------------------------------------------------
        DB::table('tb_postes')->insertOrIgnore([
            [
                'id'          => 1,
                'service_id'  => 1,
                'nom'         => 'Administrateur Système',
                'description' => 'Poste réservé au compte administrateur du système',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        // ----------------------------------------------------------------
        // 3. Agent admin — matricule auto-généré par Agent::boot()
        //    Format : AG-EBENKGA-YY-NNNNN  (ex: AG-EBENKGA-26-00001)
        // ----------------------------------------------------------------
        $agent = Agent::firstOrCreate(
            ['email' => 'bmb@bmb.cd'],
            [
                'nom'          => 'BMB',
                'postnom'      => 'ADMIN',
                'prenom'       => 'Système',
                'sexe'         => 'M',
                'date_embauche'=> now()->toDateString(),
                'statut'       => 'actif',
            ]
        );

        // ----------------------------------------------------------------
        // 4. Rôles — codes auto-générés par Role::boot()
        //    Format : EBEN-ROL1, EBEN-ROL2, ...
        // ----------------------------------------------------------------
        $rolesData = [
            ['nom' => 'Administrateur', 'description' => 'Accès total au système'],
            ['nom' => 'Caissier',       'description' => 'Gestion caisse et guichet'],
            ['nom' => 'Directeur',      'description' => 'Supervision générale'],
            ['nom' => 'Agent RH',       'description' => 'Gestion des ressources humaines'],
            ['nom' => 'Superviseur',    'description' => 'Supervision opérationnelle'],
        ];

        foreach ($rolesData as $r) {
            Role::firstOrCreate(['nom' => $r['nom']], ['description' => $r['description']]);
        }

        $adminRole = Role::where('nom', 'Administrateur')->first();

        // ----------------------------------------------------------------
        // 5. Permissions — codes auto-générés par Permission::boot()
        //    Format : EBEN-PER1, EBEN-PER2, ...
        // ----------------------------------------------------------------
        $permsData = [
            // Administration
            ['nom' => 'Accès Administration',  'description' => "Accès au panneau d'administration"],
            ['nom' => 'Voir les rôles',         'description' => 'Consultation des rôles'],
            ['nom' => 'Gérer les rôles',        'description' => 'Création et modification des rôles'],
            ['nom' => 'Voir les permissions',   'description' => 'Consultation des permissions'],
            ['nom' => 'Gérer les permissions',  'description' => 'Gestion des permissions'],
            // RH
            ['nom' => 'Voir RH',                'description' => 'Accès au module RH'],
            ['nom' => 'Créer agent',            'description' => "Création d'un nouvel agent"],
            ['nom' => 'Modifier agent',         'description' => "Modification d'un agent"],
            ['nom' => 'Affectations',           'description' => 'Gestion des affectations'],
            // Caisse / Guichet
            ['nom' => 'Voir caisse',            'description' => 'Consultation des caisses'],
            ['nom' => 'Ouvrir caisse',          'description' => "Ouverture d'une caisse/guichet"],
            ['nom' => 'Fermer caisse',          'description' => "Fermeture d'une caisse/guichet"],
            ['nom' => 'Mouvements caisse',      'description' => 'Enregistrement des mouvements'],
            ['nom' => 'Clôture caisse',         'description' => 'Clôture journalière caisse'],
            // Clients
            ['nom' => 'Voir clients',           'description' => 'Consultation des clients'],
            ['nom' => 'Créer client',           'description' => "Enregistrement d'un client"],
            ['nom' => 'Modifier client',        'description' => "Modification d'un client"],
            // Comptes bancaires
            ['nom' => 'Voir comptes',           'description' => 'Consultation des comptes'],
            ['nom' => 'Créer compte',           'description' => "Ouverture d'un compte"],
            // Devises
            ['nom' => 'Voir devises',           'description' => 'Consultation des devises'],
            ['nom' => 'Gérer devises',          'description' => 'Gestion des devises et taux'],
        ];

        foreach ($permsData as $p) {
            Permission::firstOrCreate(['nom' => $p['nom']], ['description' => $p['description']]);
        }

         // ----------------------------------------------------------------
         // 6. Rôle ADMIN → toutes les permissions
         // ----------------------------------------------------------------
         $allPermCodes = Permission::pluck('code');

         foreach ($allPermCodes as $permCode) {
             DB::table('tb_role_permission')->insertOrIgnore([
                 'role_code'       => $adminRole->code,
                 'permission_code' => $permCode,
                 'created_at'      => now(),
                 'updated_at'      => now(),
             ]);
         }

         // ----------------------------------------------------------------
         // 6b. Rôle CAISSIER → permissions spécifiques
         // ----------------------------------------------------------------
         $caissierRole = Role::where('nom', 'Caissier')->first();
         if ($caissierRole) {
             $caissierPermCodes = [
                 'EBEN-PER53',  // Voir liste crédits (Nécessaire pour accéder au module Crédit)
                 'EBEN-PER10',  // Voir caisse / guichet (menu)
                 'EBEN-PER11',  // Saisir opérations de caisse
                 'EBEN-PER25',  // Annuler opérations (si besoin)
                 // Add any other permissions caissier should have
             ];
             foreach ($caissierPermCodes as $permCode) {
                 // Ensure permission exists
                 if (Permission::where('code', $permCode)->exists()) {
                     DB::table('tb_role_permission')->insertOrIgnore([
                         'role_code'       => $caissierRole->code,
                         'permission_code' => $permCode,
                         'created_at'      => now(),
                         'updated_at'      => now(),
                     ]);
                 }
             }
         }

        // ----------------------------------------------------------------
        // 7. Utilisateur admin — via Eloquent pour déclencher les events
        // ----------------------------------------------------------------
        $user = User::firstOrCreate(
            ['email' => 'bmb@bmb.cd'],
            [
                'agent_matricule'   => $agent->matricule,
                'name'              => 'bmb',
                'password'          => Hash::make('Bmb@2026'),
                'etat'              => 'actif',
                'email_verified_at' => now(),
            ]
        );

        // ----------------------------------------------------------------
        // 8. Assignation rôle ADMIN → user bmb
        // ----------------------------------------------------------------
        DB::table('tb_role_user')->insertOrIgnore([
            'user_id'    => $user->id,
            'role_code'  => $adminRole->code,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ----------------------------------------------------------------
        // 9. Devises — CDF (référence), USD, EUR
        // ----------------------------------------------------------------
        DB::table('tb_devises')->insertOrIgnore([
            [
                'code_iso'      => 'CDF',
                'nom'           => 'Franc Congolais',
                'symbole'       => 'Fc',
                'est_reference' => 1,
                'created_at'    => now(),
                'updated_at'    => null,
            ],
            [
                'code_iso'      => 'USD',
                'nom'           => 'Dollar Américain',
                'symbole'       => '$',
                'est_reference' => 0,
                'created_at'    => now(),
                'updated_at'    => null,
            ],
            [
                'code_iso'      => 'EUR',
                'nom'           => 'Euro',
                'symbole'       => '€',
                'est_reference' => 0,
                'created_at'    => now(),
                'updated_at'    => null,
            ],
        ]);

        $this->command->info('✔ AdminSeeder — données d\'amorçage insérées avec succès.');
        $this->command->info('  Login : bmb | Mot de passe : Bmb@2026');
    }
}
