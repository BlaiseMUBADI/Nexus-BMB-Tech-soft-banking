<?php

/**
 * ============================================================
 * MIGRATION 6/6 â€” PERMISSIONS & RÃ”LES BANCAIRES COMPLETS
 * ============================================================
 * SOURCE UNIQUE pour toutes les donnÃ©es RBAC :
 *   - TOUS les rÃ´les        ROL1 â†’ ROL7
 *   - TOUTES les permissions PER1 â†’ PER43 (12 modules)
 *   - TOUTES les affectations rÃ´le/permission
 *
 * DÃ©placÃ© ici depuis 000001 pour avoir un seul fichier de
 * rÃ©fÃ©rence. insertOrIgnore partout â€” safe Ã  rÃ©-exÃ©cuter.
 * Voir docs/permissions_referentiel.md pour la matrice complÃ¨te.
 * ============================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // ====================================================
        // 1. TOUS LES RÃ”LES (ROL1 â†’ ROL7)
        // ====================================================
        DB::table('tb_roles')->insertOrIgnore([
            ['code' => 'EBEN-ROL1', 'nom' => 'Administrateur',   'description' => 'AccÃ¨s total au systÃ¨me',                                           'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL2', 'nom' => 'Caissier',         'description' => 'Gestion caisse, guichet et transactions',                          'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL3', 'nom' => 'Directeur',        'description' => 'Supervision gÃ©nÃ©rale',                                             'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL4', 'nom' => 'Agent RH',         'description' => 'Gestion des ressources humaines',                                  'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL5', 'nom' => 'Superviseur',      'description' => 'Supervision opÃ©rationnelle',                                       'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL6', 'nom' => 'ChargÃ© de crÃ©dit', 'description' => 'Gestion complÃ¨te des dossiers crÃ©dit, Ã©pargne et comptes clients', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL7', 'nom' => 'Comptable',        'description' => 'ComptabilitÃ©, rapports financiers, validation des Ã©critures',      'created_at' => $now, 'updated_at' => $now],
        ]);

        // ====================================================
        // 2. TOUTES LES PERMISSIONS (PER1 â†’ PER43)
        // ====================================================

        // MODULE 1 â€” Administration
        DB::table('tb_permissions')->insertOrIgnore([
            ['code' => 'EBEN-PER1',  'nom' => 'AccÃ¨s Administration',  'description' => "AccÃ¨s au panneau d'administration",         'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER2',  'nom' => 'Voir les rÃ´les',        'description' => 'Consultation des rÃ´les',                     'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER3',  'nom' => 'GÃ©rer les rÃ´les',       'description' => 'CrÃ©ation et modification des rÃ´les',         'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER4',  'nom' => 'Voir les permissions',  'description' => 'Consultation des permissions',               'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER5',  'nom' => 'GÃ©rer les permissions', 'description' => 'Gestion des permissions',                    'created_at' => $now, 'updated_at' => $now],
            // MODULE 2 â€” RH
            ['code' => 'EBEN-PER6',  'nom' => 'Voir RH',               'description' => 'AccÃ¨s au module RH',                         'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER7',  'nom' => 'CrÃ©er agent',           'description' => "CrÃ©ation d'un nouvel agent",                 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER8',  'nom' => 'Modifier agent',        'description' => "Modification d'un agent",                    'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER9',  'nom' => 'Affectations',          'description' => 'Gestion des affectations',                   'created_at' => $now, 'updated_at' => $now],
            // MODULE 3 â€” Caisse
            ['code' => 'EBEN-PER10', 'nom' => 'Voir caisse',           'description' => 'Consultation des caisses',                   'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER11', 'nom' => 'Ouvrir caisse',         'description' => "Ouverture d'une caisse/guichet",             'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER12', 'nom' => 'Fermer caisse',         'description' => "Fermeture d'une caisse/guichet",             'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER13', 'nom' => 'Mouvements caisse',     'description' => 'Enregistrement des mouvements',              'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER14', 'nom' => 'ClÃ´ture caisse',        'description' => 'ClÃ´ture journaliÃ¨re caisse',                 'created_at' => $now, 'updated_at' => $now],
            // MODULE 4 â€” Clients
            ['code' => 'EBEN-PER15', 'nom' => 'Voir clients',          'description' => 'Consultation des clients',                   'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER16', 'nom' => 'CrÃ©er client',          'description' => "Enregistrement d'un client",                 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER17', 'nom' => 'Modifier client',       'description' => "Modification d'un client",                   'created_at' => $now, 'updated_at' => $now],
            // MODULE 5 â€” Comptes
            ['code' => 'EBEN-PER18', 'nom' => 'Voir comptes',          'description' => 'Consultation des comptes',                   'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER19', 'nom' => 'CrÃ©er compte',          'description' => "Ouverture d'un compte",                      'created_at' => $now, 'updated_at' => $now],
            // MODULE 6 â€” Devises
            ['code' => 'EBEN-PER20', 'nom' => 'Voir devises',          'description' => 'Consultation des devises',                   'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER21', 'nom' => 'GÃ©rer devises',         'description' => 'Gestion des devises et taux',                'created_at' => $now, 'updated_at' => $now],
            // MODULE 7 â€” Transactions bancaires
            ['code' => 'EBEN-PER22', 'nom' => 'Effectuer dÃ©pÃ´ts',            'description' => 'Saisir un dÃ©pÃ´t sur un compte client',                         'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER23', 'nom' => 'Effectuer retraits',          'description' => 'Saisir un retrait sur un compte client',                        'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER24', 'nom' => 'Effectuer virements',         'description' => 'Initier un virement entre comptes',                             'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER25', 'nom' => 'Annuler transactions',        'description' => 'Annuler ou reverser une opÃ©ration bancaire',                    'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER26', 'nom' => 'Valider transactions',        'description' => 'Approuver les opÃ©rations en attente (double validation)',       'created_at' => $now, 'updated_at' => $now],
            // MODULE 8 â€” Ã‰pargne
            ['code' => 'EBEN-PER27', 'nom' => 'Voir produits Ã©pargne',       'description' => "Consulter les produits d'Ã©pargne disponibles",                 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER28', 'nom' => 'GÃ©rer produits Ã©pargne',      'description' => "CrÃ©er et modifier les produits d'Ã©pargne",                     'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER29', 'nom' => 'GÃ©rer comptes Ã©pargne',       'description' => 'Ouvrir, alimenter et clÃ´turer des comptes Ã©pargne',           'created_at' => $now, 'updated_at' => $now],
            // MODULE 9 â€” CrÃ©dits / PrÃªts
            ['code' => 'EBEN-PER30', 'nom' => 'Voir crÃ©dits',                'description' => 'Consulter les dossiers de crÃ©dit',                             'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER31', 'nom' => 'Soumettre demande crÃ©dit',    'description' => 'CrÃ©er une demande de prÃªt pour un client',                     'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER32', 'nom' => 'Instruire dossier crÃ©dit',    'description' => 'Analyser et complÃ©ter un dossier de crÃ©dit',                  'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER33', 'nom' => 'Approuver crÃ©dit',            'description' => 'Accorder ou rejeter un crÃ©dit (niveau comitÃ©)',                'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER34', 'nom' => 'GÃ©rer remboursements',        'description' => 'Saisir les Ã©chÃ©ances et paiements de remboursement',          'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER35', 'nom' => 'ClÃ´turer crÃ©dit',             'description' => 'Marquer un crÃ©dit comme soldÃ© ou en contentieux',             'created_at' => $now, 'updated_at' => $now],
            // MODULE 10 â€” Rapports & Statistiques
            ['code' => 'EBEN-PER36', 'nom' => 'Voir rapports opÃ©rationnels', 'description' => 'Rapports journaliers caisse et transactions',                  'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER37', 'nom' => 'Voir rapports financiers',    'description' => 'Bilan, compte de rÃ©sultat, situation financiÃ¨re',             'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER38', 'nom' => 'Exporter rapports',           'description' => 'Exporter ou imprimer tous les rapports en PDF/Excel',         'created_at' => $now, 'updated_at' => $now],
            // MODULE 11 â€” ComptabilitÃ©
            ['code' => 'EBEN-PER39', 'nom' => 'Voir journal comptable',      'description' => 'Consulter les Ã©critures du journal comptable',                 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER40', 'nom' => 'Saisir Ã©critures',            'description' => 'CrÃ©er des Ã©critures comptables manuelles',                    'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER41', 'nom' => 'Valider Ã©critures',           'description' => 'Approuver et lettrer les Ã©critures comptables',               'created_at' => $now, 'updated_at' => $now],
            // MODULE 12 â€” Audit & SÃ©curitÃ©
            ['code' => 'EBEN-PER42', 'nom' => "Voir journal d'activitÃ©",     'description' => "Logs d'audit : qui a fait quoi et quand",                    'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER43', 'nom' => 'GÃ©rer paramÃ¨tres sÃ©curitÃ©',   'description' => 'Politique mots de passe, tentatives login, blocages',         'created_at' => $now, 'updated_at' => $now],
        ]);

        // ====================================================
        // 3. TOUTES LES AFFECTATIONS RÃ”LE â†” PERMISSION
        // ====================================================

        // â”€â”€ ROL1 Administrateur â€” toutes les permissions â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        DB::table('tb_role_permission')->insertOrIgnore(
            array_map(fn($n) => ['role_code' => 'EBEN-ROL1', 'permission_code' => "EBEN-PER{$n}", 'created_at' => $now, 'updated_at' => $now], range(1, 43))
        );

        // â”€â”€ ROL2 Caissier â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER10', 'created_at' => $now, 'updated_at' => $now],  // Voir caisse
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER11', 'created_at' => $now, 'updated_at' => $now],  // Ouvrir caisse
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER12', 'created_at' => $now, 'updated_at' => $now],  // Fermer caisse
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER13', 'created_at' => $now, 'updated_at' => $now],  // Mouvements
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER14', 'created_at' => $now, 'updated_at' => $now],  // ClÃ´ture caisse
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER15', 'created_at' => $now, 'updated_at' => $now],  // Voir clients
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],  // Voir comptes
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER22', 'created_at' => $now, 'updated_at' => $now],  // DÃ©pÃ´ts
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER23', 'created_at' => $now, 'updated_at' => $now],  // Retraits
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER24', 'created_at' => $now, 'updated_at' => $now],  // Virements
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER29', 'created_at' => $now, 'updated_at' => $now],  // Comptes Ã©pargne
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER34', 'created_at' => $now, 'updated_at' => $now],  // Remboursements
        ]);

        // â”€â”€ ROL3 Directeur â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER1',  'created_at' => $now, 'updated_at' => $now],  // AccÃ¨s Admin
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER2',  'created_at' => $now, 'updated_at' => $now],  // Voir rÃ´les
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER4',  'created_at' => $now, 'updated_at' => $now],  // Voir permissions
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER6',  'created_at' => $now, 'updated_at' => $now],  // Voir RH
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER10', 'created_at' => $now, 'updated_at' => $now],  // Voir caisse
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER15', 'created_at' => $now, 'updated_at' => $now],  // Voir clients
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],  // Voir comptes
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER20', 'created_at' => $now, 'updated_at' => $now],  // Voir devises
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER26', 'created_at' => $now, 'updated_at' => $now],  // Valider tx
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER27', 'created_at' => $now, 'updated_at' => $now],  // Voir Ã©pargne
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER30', 'created_at' => $now, 'updated_at' => $now],  // Voir crÃ©dits
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER33', 'created_at' => $now, 'updated_at' => $now],  // Approuver crÃ©dit
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER36', 'created_at' => $now, 'updated_at' => $now],  // Rapports opÃ©ra.
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER37', 'created_at' => $now, 'updated_at' => $now],  // Rapports finan.
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER38', 'created_at' => $now, 'updated_at' => $now],  // Exporter
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER39', 'created_at' => $now, 'updated_at' => $now],  // Journal comptable
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER42', 'created_at' => $now, 'updated_at' => $now],  // Logs audit
        ]);

        // â”€â”€ ROL4 Agent RH â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER6',  'created_at' => $now, 'updated_at' => $now],  // Voir RH
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER7',  'created_at' => $now, 'updated_at' => $now],  // CrÃ©er agent
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER8',  'created_at' => $now, 'updated_at' => $now],  // Modifier agent
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER9',  'created_at' => $now, 'updated_at' => $now],  // Affectations
        ]);

        // â”€â”€ ROL5 Superviseur â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER2',  'created_at' => $now, 'updated_at' => $now],  // Voir rÃ´les
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER6',  'created_at' => $now, 'updated_at' => $now],  // Voir RH
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER10', 'created_at' => $now, 'updated_at' => $now],  // Voir caisse
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER15', 'created_at' => $now, 'updated_at' => $now],  // Voir clients
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],  // Voir comptes
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER20', 'created_at' => $now, 'updated_at' => $now],  // Voir devises
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER26', 'created_at' => $now, 'updated_at' => $now],  // Valider tx
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER27', 'created_at' => $now, 'updated_at' => $now],  // Voir Ã©pargne
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER30', 'created_at' => $now, 'updated_at' => $now],  // Voir crÃ©dits
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER36', 'created_at' => $now, 'updated_at' => $now],  // Rapports opÃ©ra.
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER37', 'created_at' => $now, 'updated_at' => $now],  // Rapports finan.
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER38', 'created_at' => $now, 'updated_at' => $now],  // Exporter
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER42', 'created_at' => $now, 'updated_at' => $now],  // Logs audit
        ]);

        // â”€â”€ ROL6 ChargÃ© de crÃ©dit â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER15', 'created_at' => $now, 'updated_at' => $now],  // Voir clients
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER16', 'created_at' => $now, 'updated_at' => $now],  // CrÃ©er client
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],  // Voir comptes
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER19', 'created_at' => $now, 'updated_at' => $now],  // CrÃ©er compte
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER27', 'created_at' => $now, 'updated_at' => $now],  // Voir Ã©pargne
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER28', 'created_at' => $now, 'updated_at' => $now],  // GÃ©rer Ã©pargne
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER29', 'created_at' => $now, 'updated_at' => $now],  // Comptes Ã©pargne
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER30', 'created_at' => $now, 'updated_at' => $now],  // Voir crÃ©dits
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER31', 'created_at' => $now, 'updated_at' => $now],  // Soumettre crÃ©dit
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER32', 'created_at' => $now, 'updated_at' => $now],  // Instruire crÃ©dit
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER34', 'created_at' => $now, 'updated_at' => $now],  // Remboursements
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER35', 'created_at' => $now, 'updated_at' => $now],  // ClÃ´turer crÃ©dit
        ]);

        // â”€â”€ ROL7 Comptable â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],  // Voir comptes
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER37', 'created_at' => $now, 'updated_at' => $now],  // Rapports finan.
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER38', 'created_at' => $now, 'updated_at' => $now],  // Exporter
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER39', 'created_at' => $now, 'updated_at' => $now],  // Journal comptable
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER40', 'created_at' => $now, 'updated_at' => $now],  // Saisir Ã©critures
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER41', 'created_at' => $now, 'updated_at' => $now],  // Valider Ã©critures
        ]);
    }

    public function down(): void
    {
        // Suppression dans l'ordre inverse : d'abord les liens,
        // puis les donnÃ©es (la structure reste â€” gÃ©rÃ©e par 000001)
        DB::table('tb_role_permission')->delete();
        DB::table('tb_permissions')->delete();
        DB::table('tb_roles')->delete();
    }
};
