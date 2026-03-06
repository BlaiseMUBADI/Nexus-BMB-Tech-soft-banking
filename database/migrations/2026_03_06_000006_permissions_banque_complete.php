<?php

/**
 * ============================================================
 * MIGRATION 6/6 — PERMISSIONS BANCAIRES COMPLÈTES
 * ============================================================
 * Ajoute les permissions manquantes (PER22-PER43) et
 * 2 nouveaux rôles (ROL6 Chargé de crédit, ROL7 Comptable).
 *
 * SAFE sur base existante : insertOrIgnore partout.
 * Voir docs/permissions_referentiel.md pour la description
 * complète de chaque permission.
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
        // 1. NOUVELLES PERMISSIONS (PER22 → PER43)
        // ====================================================
        DB::table('tb_permissions')->insertOrIgnore([

            // MODULE 7 — Transactions bancaires
            ['code' => 'EBEN-PER22', 'nom' => 'Effectuer dépôts',         'description' => 'Saisir un dépôt sur un compte client',                          'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER23', 'nom' => 'Effectuer retraits',       'description' => 'Saisir un retrait sur un compte client',                         'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER24', 'nom' => 'Effectuer virements',      'description' => 'Initier un virement entre comptes',                              'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER25', 'nom' => 'Annuler transactions',     'description' => 'Annuler ou reverser une opération bancaire',                     'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER26', 'nom' => 'Valider transactions',     'description' => 'Approuver les opérations en attente (double validation)',        'created_at' => $now, 'updated_at' => $now],

            // MODULE 8 — Épargne
            ['code' => 'EBEN-PER27', 'nom' => 'Voir produits épargne',    'description' => 'Consulter les produits d\'épargne disponibles',                 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER28', 'nom' => 'Gérer produits épargne',   'description' => 'Créer et modifier les produits d\'épargne',                     'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER29', 'nom' => 'Gérer comptes épargne',    'description' => 'Ouvrir, alimenter et clôturer des comptes épargne',             'created_at' => $now, 'updated_at' => $now],

            // MODULE 9 — Crédits / Prêts
            ['code' => 'EBEN-PER30', 'nom' => 'Voir crédits',             'description' => 'Consulter les dossiers de crédit',                              'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER31', 'nom' => 'Soumettre demande crédit', 'description' => 'Créer une demande de prêt pour un client',                      'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER32', 'nom' => 'Instruire dossier crédit', 'description' => 'Analyser et compléter un dossier de crédit',                    'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER33', 'nom' => 'Approuver crédit',         'description' => 'Accorder ou rejeter un crédit (niveau comité)',                 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER34', 'nom' => 'Gérer remboursements',     'description' => 'Saisir les échéances et paiements de remboursement',            'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER35', 'nom' => 'Clôturer crédit',          'description' => 'Marquer un crédit comme soldé ou en contentieux',               'created_at' => $now, 'updated_at' => $now],

            // MODULE 10 — Rapports & Statistiques
            ['code' => 'EBEN-PER36', 'nom' => 'Voir rapports opérationnels', 'description' => 'Rapports journaliers caisse et transactions',                'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER37', 'nom' => 'Voir rapports financiers', 'description' => 'Bilan, compte de résultat, situation financière',               'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER38', 'nom' => 'Exporter rapports',        'description' => 'Exporter ou imprimer tous les rapports en PDF/Excel',           'created_at' => $now, 'updated_at' => $now],

            // MODULE 11 — Comptabilité
            ['code' => 'EBEN-PER39', 'nom' => 'Voir journal comptable',   'description' => 'Consulter les écritures du journal comptable',                  'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER40', 'nom' => 'Saisir écritures',         'description' => 'Créer des écritures comptables manuelles',                      'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER41', 'nom' => 'Valider écritures',        'description' => 'Approuver et lettrer les écritures comptables',                 'created_at' => $now, 'updated_at' => $now],

            // MODULE 12 — Audit & Sécurité
            ['code' => 'EBEN-PER42', 'nom' => 'Voir journal d\'activité', 'description' => 'Logs d\'audit : qui a fait quoi et quand',                     'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER43', 'nom' => 'Gérer paramètres sécurité','description' => 'Politique mots de passe, tentatives login, blocages',          'created_at' => $now, 'updated_at' => $now],
        ]);

        // ====================================================
        // 2. NOUVEAUX RÔLES — ROL6 et ROL7
        // ====================================================
        DB::table('tb_roles')->insertOrIgnore([
            ['code' => 'EBEN-ROL6', 'nom' => 'Chargé de crédit', 'description' => 'Gestion complète des dossiers crédit, épargne et comptes clients', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL7', 'nom' => 'Comptable',        'description' => 'Comptabilité, rapports financiers, validation des écritures',       'created_at' => $now, 'updated_at' => $now],
        ]);

        // ====================================================
        // 3. PERMISSIONS DES NOUVEAUX RÔLES
        // ====================================================

        // ── ROL2 Caissier : ajout des permissions transactions ──────────────
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER22', 'created_at' => $now, 'updated_at' => $now],  // Dépôts
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER23', 'created_at' => $now, 'updated_at' => $now],  // Retraits
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER24', 'created_at' => $now, 'updated_at' => $now],  // Virements
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER29', 'created_at' => $now, 'updated_at' => $now],  // Comptes épargne
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER34', 'created_at' => $now, 'updated_at' => $now],  // Remboursements
        ]);

        // ── ROL3 Directeur : supervision + approbations ──────────────────────
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER26', 'created_at' => $now, 'updated_at' => $now],  // Valider tx
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER27', 'created_at' => $now, 'updated_at' => $now],  // Voir épargne
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER30', 'created_at' => $now, 'updated_at' => $now],  // Voir crédits
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER33', 'created_at' => $now, 'updated_at' => $now],  // Approuver crédit
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER36', 'created_at' => $now, 'updated_at' => $now],  // Rapports opéra.
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER37', 'created_at' => $now, 'updated_at' => $now],  // Rapports finan.
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER38', 'created_at' => $now, 'updated_at' => $now],  // Exporter
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER39', 'created_at' => $now, 'updated_at' => $now],  // Voir journal cpta
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER42', 'created_at' => $now, 'updated_at' => $now],  // Voir logs audit
        ]);

        // ── ROL5 Superviseur : contrôle transversal ───────────────────────────
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER26', 'created_at' => $now, 'updated_at' => $now],  // Valider tx
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER27', 'created_at' => $now, 'updated_at' => $now],  // Voir épargne
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER30', 'created_at' => $now, 'updated_at' => $now],  // Voir crédits
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER36', 'created_at' => $now, 'updated_at' => $now],  // Rapports opéra.
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER37', 'created_at' => $now, 'updated_at' => $now],  // Rapports finan.
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER38', 'created_at' => $now, 'updated_at' => $now],  // Exporter
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER42', 'created_at' => $now, 'updated_at' => $now],  // Voir logs audit
        ]);

        // ── ROL6 Chargé de crédit ─────────────────────────────────────────────
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER15', 'created_at' => $now, 'updated_at' => $now],  // Voir clients
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER16', 'created_at' => $now, 'updated_at' => $now],  // Créer clients
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],  // Voir comptes
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER19', 'created_at' => $now, 'updated_at' => $now],  // Gérer comptes
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER27', 'created_at' => $now, 'updated_at' => $now],  // Voir épargne
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER28', 'created_at' => $now, 'updated_at' => $now],  // Gérer épargne
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER29', 'created_at' => $now, 'updated_at' => $now],  // Comptes épargne
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER30', 'created_at' => $now, 'updated_at' => $now],  // Voir crédits
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER31', 'created_at' => $now, 'updated_at' => $now],  // Soumettre crédit
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER32', 'created_at' => $now, 'updated_at' => $now],  // Instruire crédit
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER34', 'created_at' => $now, 'updated_at' => $now],  // Remboursements
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER35', 'created_at' => $now, 'updated_at' => $now],  // Clôturer crédit
        ]);

        // ── ROL7 Comptable ────────────────────────────────────────────────────
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],  // Voir comptes
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER37', 'created_at' => $now, 'updated_at' => $now],  // Rapports finan.
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER38', 'created_at' => $now, 'updated_at' => $now],  // Exporter
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER39', 'created_at' => $now, 'updated_at' => $now],  // Voir journal cpta
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER40', 'created_at' => $now, 'updated_at' => $now],  // Saisir écritures
            ['role_code' => 'EBEN-ROL7', 'permission_code' => 'EBEN-PER41', 'created_at' => $now, 'updated_at' => $now],  // Valider écritures
        ]);
    }

    public function down(): void
    {
        DB::table('tb_role_permission')
            ->whereIn('role_code', ['EBEN-ROL2','EBEN-ROL3','EBEN-ROL5','EBEN-ROL6','EBEN-ROL7'])
            ->whereIn('permission_code', array_map(fn($n) => "EBEN-PER{$n}", range(22, 43)))
            ->delete();

        DB::table('tb_roles')
            ->whereIn('code', ['EBEN-ROL6', 'EBEN-ROL7'])
            ->delete();

        DB::table('tb_permissions')
            ->whereIn('code', array_map(fn($n) => "EBEN-PER{$n}", range(22, 43)))
            ->delete();
    }
};
