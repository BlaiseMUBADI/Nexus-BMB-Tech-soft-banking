<?php

/**
 * ============================================================
 * MIGRATION 6/6 – PERMISSIONS & RÔLES BANCAIRES COMPLETS
 * ============================================================
 * SOURCE UNIQUE pour toutes les données RBAC :
 *   - TOUS les rôles        ROL1 → ROL7
 *   - TOUTES les permissions PER1 → PER43 (12 modules)
 *   - TOUTES les affectations rôle/permission
 *
 * Déplacé ici depuis 000001 pour avoir un seul fichier de
 * référence. insertOrIgnore partout – safe à ré-exécuter.
 * Voir docs/permissions_referentiel.md pour la matrice complète.
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
        // 1. TOUS LES RÔLES (ROL1 → ROL7)
        // ====================================================
        DB::table('tb_roles')->insertOrIgnore([
            ['code' => 'EBEN-ROL1', 'nom' => 'Administrateur',    'description' => 'Accès total au système',                                                'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL2', 'nom' => 'Caissier',          'description' => 'Gestion caisse, guichet et transactions',                               'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL3', 'nom' => 'Directeur',         'description' => 'Supervision générale',                                                  'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL4', 'nom' => 'Agent RH',          'description' => 'Gestion des ressources humaines',                                       'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL5', 'nom' => 'Superviseur',       'description' => 'Supervision opérationnelle',                                            'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL6', 'nom' => 'Chargé de crédit',  'description' => 'Gestion complète des dossiers crédit, épargne et comptes clients',      'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL7', 'nom' => 'Comptable',         'description' => 'Comptabilité, rapports financiers, validation des écritures',           'created_at' => $now, 'updated_at' => $now],
        ]);

        // ====================================================
        // 2. TOUTES LES PERMISSIONS (PER1 → PER43)
        // ====================================================

        // MODULE 1 – Administration
        DB::table('tb_permissions')->insertOrIgnore([
            ['code' => 'EBEN-PER1',  'nom' => 'Accès Administration',  'description' => "Accès au panneau d'administration",          'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER2',  'nom' => 'Voir les rôles',        'description' => 'Consultation des rôles',                      'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER3',  'nom' => 'Gérer les rôles',       'description' => 'Création et modification des rôles',          'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER4',  'nom' => 'Voir les permissions',  'description' => 'Consultation des permissions',                'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER5',  'nom' => 'Gérer les permissions', 'description' => 'Gestion des permissions',                     'created_at' => $now, 'updated_at' => $now],
            // MODULE 2 – RH
            ['code' => 'EBEN-PER6',  'nom' => 'Voir RH',               'description' => 'Accès au module RH',                          'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER7',  'nom' => 'Créer agent',           'description' => "Création d'un nouvel agent",                  'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER8',  'nom' => 'Modifier agent',        'description' => "Modification d'un agent",                     'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER9',  'nom' => 'Affectations',          'description' => 'Gestion des affectations',                    'created_at' => $now, 'updated_at' => $now],
            // MODULE 3 – Caisse
            ['code' => 'EBEN-PER10', 'nom' => 'Voir caisse',           'description' => 'Consultation des caisses',                    'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER11', 'nom' => 'Ouvrir caisse',         'description' => "Ouverture d'une caisse/guichet",              'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER12', 'nom' => 'Fermer caisse',         'description' => "Fermeture d'une caisse/guichet",              'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER13', 'nom' => 'Mouvements caisse',     'description' => 'Enregistrement des mouvements',               'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER14', 'nom' => 'Clôture caisse',        'description' => 'Clôture journalière caisse',                  'created_at' => $now, 'updated_at' => $now],
            // MODULE 4 – Clients
            ['code' => 'EBEN-PER15', 'nom' => 'Voir clients',          'description' => 'Consultation des clients',                    'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER16', 'nom' => 'Créer client',          'description' => "Enregistrement d'un client",                  'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER17', 'nom' => 'Modifier client',       'description' => "Modification d'un client",                    'created_at' => $now, 'updated_at' => $now],
            // MODULE 5 – Comptes
            ['code' => 'EBEN-PER18', 'nom' => 'Voir comptes',          'description' => 'Consultation des comptes',                    'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER19', 'nom' => 'Créer compte',          'description' => "Ouverture d'un compte",                       'created_at' => $now, 'updated_at' => $now],
            // MODULE 6 – Devises
            ['code' => 'EBEN-PER20', 'nom' => 'Voir devises',          'description' => 'Consultation des devises',                    'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER21', 'nom' => 'Gérer devises',         'description' => 'Gestion des devises et taux',                 'created_at' => $now, 'updated_at' => $now],
            // MODULE 7 – Transactions bancaires
            ['code' => 'EBEN-PER22', 'nom' => 'Effectuer dépôts',            'description' => 'Saisir un dépôt sur un compte client',                          'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER23', 'nom' => 'Effectuer retraits',          'description' => 'Saisir un retrait sur un compte client',                         'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER24', 'nom' => 'Effectuer virements',         'description' => 'Initier un virement entre comptes',                              'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER25', 'nom' => 'Annuler transactions',        'description' => 'Annuler ou reverser une opération bancaire',                     'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER26', 'nom' => 'Valider transactions',        'description' => 'Approuver les opérations en attente (double validation)',        'created_at' => $now, 'updated_at' => $now],
            // MODULE 8 – Épargne
            ['code' => 'EBEN-PER27', 'nom' => 'Voir produits épargne',       'description' => "Consulter les produits d'épargne disponibles",                  'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER28', 'nom' => 'Gérer produits épargne',      'description' => "Créer et modifier les produits d'épargne",                      'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER29', 'nom' => 'Gérer comptes épargne',       'description' => 'Ouvrir, alimenter et clôturer des comptes épargne',             'created_at' => $now, 'updated_at' => $now],
            // MODULE 9 – Crédits / Prêts
            // [SUPPRIMÉ] Permissions Legacy (remplacées par EBEN-PER5x/6x)
            // ['code' => 'EBEN-PER30', 'nom' => 'Voir crédits', ...],
            // ['code' => 'EBEN-PER31', 'nom' => 'Soumettre demande crédit', ...],
            // ['code' => 'EBEN-PER32', 'nom' => 'Instruire dossier crédit', ...],
            // ['code' => 'EBEN-PER33', 'nom' => 'Approuver crédit', ...],
            // ['code' => 'EBEN-PER34', 'nom' => 'Gérer remboursements', ...],
            ['code' => 'EBEN-PER35', 'nom' => 'Clôturer crédit',             'description' => 'Marquer un crédit comme soldé ou en contentieux',              'created_at' => $now, 'updated_at' => $now],
            // MODULE 10 – Rapports & Statistiques
            ['code' => 'EBEN-PER36', 'nom' => 'Voir rapports opérationnels', 'description' => 'Rapports journaliers caisse et transactions',                   'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER37', 'nom' => 'Voir rapports financiers',    'description' => 'Bilan, compte de résultat, situation financière',              'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER38', 'nom' => 'Exporter rapports',           'description' => 'Exporter ou imprimer tous les rapports en PDF/Excel',          'created_at' => $now, 'updated_at' => $now],
            // MODULE 11 – Comptabilité
            ['code' => 'EBEN-PER39', 'nom' => 'Voir journal comptable',      'description' => 'Consulter les écritures du journal comptable',                  'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER40', 'nom' => 'Saisir écritures',            'description' => 'Créer des écritures comptables manuelles',                     'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER41', 'nom' => 'Valider écritures',           'description' => 'Approuver et lettrer les écritures comptables',                'created_at' => $now, 'updated_at' => $now],
            // MODULE 12 – Audit & Sécurité
            ['code' => 'EBEN-PER42', 'nom' => "Voir journal d'activité",     'description' => "Logs d'audit : qui a fait quoi et quand",                     'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER43', 'nom' => 'Gérer paramètres sécurité',   'description' => 'Politique mots de passe, tentatives login, blocages',          'created_at' => $now, 'updated_at' => $now],
        ]);

        // ====================================================
        // 3. TOUTES LES AFFECTATIONS RÔLE ↔ PERMISSION
        // ====================================================

        // ── ROL1 Administrateur – toutes les permissions ───────────────────────
        DB::table('tb_role_permission')->insertOrIgnore(
            array_map(fn($n) => ['role_code' => 'EBEN-ROL1', 'permission_code' => "EBEN-PER{$n}", 'created_at' => $now, 'updated_at' => $now], range(1, 43))
        );

        // ── ROL2 Caissier ────────────────────────────────────────────────────────
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER10', 'created_at' => $now, 'updated_at' => $now],  // Voir caisse
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER11', 'created_at' => $now, 'updated_at' => $now],  // Ouvrir caisse
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER12', 'created_at' => $now, 'updated_at' => $now],  // Fermer caisse
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER13', 'created_at' => $now, 'updated_at' => $now],  // Mouvements
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER14', 'created_at' => $now, 'updated_at' => $now],  // Clôture caisse
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER15', 'created_at' => $now, 'updated_at' => $now],  // Voir clients
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],  // Voir comptes
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER22', 'created_at' => $now, 'updated_at' => $now],  // Dépôts
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER23', 'created_at' => $now, 'updated_at' => $now],  // Retraits
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER24', 'created_at' => $now, 'updated_at' => $now],  // Virements
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER29', 'created_at' => $now, 'updated_at' => $now],  // Comptes épargne
            // [SUPPRIMÉ] ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER34', ...], // Remboursements
        ]);

        // ── ROL3 Directeur ───────────────────────────────────────────────────────
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER1',  'created_at' => $now, 'updated_at' => $now],  // Accès Admin
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER2',  'created_at' => $now, 'updated_at' => $now],  // Voir rôles
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER4',  'created_at' => $now, 'updated_at' => $now],  // Voir permissions
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER6',  'created_at' => $now, 'updated_at' => $now],  // Voir RH
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER10', 'created_at' => $now, 'updated_at' => $now],  // Voir caisse
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER15', 'created_at' => $now, 'updated_at' => $now],  // Voir clients
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],  // Voir comptes
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER20', 'created_at' => $now, 'updated_at' => $now],  // Voir devises
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER26', 'created_at' => $now, 'updated_at' => $now],  // Valider tx
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER27', 'created_at' => $now, 'updated_at' => $now],  // Voir épargne
            // [SUPPRIMÉ] ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER30', ...], // Voir crédits
            // [SUPPRIMÉ] ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER33', ...], // Approuver crédit
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER36', 'created_at' => $now, 'updated_at' => $now],  // Rapports opéra.
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER37', 'created_at' => $now, 'updated_at' => $now],  // Rapports finan.
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER38', 'created_at' => $now, 'updated_at' => $now],  // Exporter
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER39', 'created_at' => $now, 'updated_at' => $now],  // Journal comptable
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER42', 'created_at' => $now, 'updated_at' => $now],  // Logs audit
        ]);

        // ── ROL4 Agent RH ────────────────────────────────────────────────────────
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER6',  'created_at' => $now, 'updated_at' => $now],  // Voir RH
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER7',  'created_at' => $now, 'updated_at' => $now],  // Créer agent
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER8',  'created_at' => $now, 'updated_at' => $now],  // Modifier agent
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER9',  'created_at' => $now, 'updated_at' => $now],  // Affectations
        ]);

        // ── ROL5 Superviseur ─────────────────────────────────────────────────────
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER2',  'created_at' => $now, 'updated_at' => $now],  // Voir rôles
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER6',  'created_at' => $now, 'updated_at' => $now],  // Voir RH
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER10', 'created_at' => $now, 'updated_at' => $now],  // Voir caisse
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER15', 'created_at' => $now, 'updated_at' => $now],  // Voir clients
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],  // Voir comptes
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER20', 'created_at' => $now, 'updated_at' => $now],  // Voir devises
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER26', 'created_at' => $now, 'updated_at' => $now],  // Valider tx
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER27', 'created_at' => $now, 'updated_at' => $now],  // Voir épargne
            // [SUPPRIMÉ] ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER30', ...], // Voir crédits
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER36', 'created_at' => $now, 'updated_at' => $now],  // Rapports opéra.
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER37', 'created_at' => $now, 'updated_at' => $now],  // Rapports finan.
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER38', 'created_at' => $now, 'updated_at' => $now],  // Exporter
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER42', 'created_at' => $now, 'updated_at' => $now],  // Logs audit
        ]);

        // ── ROL6 Chargé de crédit ────────────────────────────────────────────────
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER15', 'created_at' => $now, 'updated_at' => $now],  // Voir clients
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER16', 'created_at' => $now, 'updated_at' => $now],  // Créer client
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],  // Voir comptes
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER19', 'created_at' => $now, 'updated_at' => $now],  // Créer compte
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER27', 'created_at' => $now, 'updated_at' => $now],  // Voir épargne
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER28', 'created_at' => $now, 'updated_at' => $now],  // Gérer épargne
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER29', 'created_at' => $now, 'updated_at' => $now],  // Comptes épargne
            // [SUPPRIMÉ] ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER30', ...], // Voir crédits
            // [SUPPRIMÉ] ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER31', ...], // Soumettre crédit
            // [SUPPRIMÉ] ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER32', ...], // Instruire crédit
            // [SUPPRIMÉ] ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER34', ...], // Remboursements
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER35', 'created_at' => $now, 'updated_at' => $now],  // Clôturer crédit
        ]);

        // ── ROL7 Comptable ───────────────────────────────────────────────────────
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],  // Voir comptes
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER37', 'created_at' => $now, 'updated_at' => $now],  // Rapports finan.
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER38', 'created_at' => $now, 'updated_at' => $now],  // Exporter
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER39', 'created_at' => $now, 'updated_at' => $now],  // Journal comptable
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER40', 'created_at' => $now, 'updated_at' => $now],  // Saisir écritures
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER41', 'created_at' => $now, 'updated_at' => $now],  // Valider écritures
        ]);
    }

    public function down(): void
    {
        // Suppression dans l'ordre inverse : d'abord les liens,
        // puis les données (la structure reste – gérée par 000001)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('tb_role_permission')->delete();
        DB::table('tb_permissions')->delete();
        DB::table('tb_roles')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
