-- ============================================================
-- FICHIER : db_permissions_banque_complete.sql
-- USAGE   : À exécuter sur une base EXISTANTE.
--           Ajoute les permissions PER22-PER43 + rôles ROL6/ROL7.
--           INSERT IGNORE partout — aucune donnée modifiée.
-- ============================================================

SET NAMES 'utf8mb4';
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. NOUVELLES PERMISSIONS (PER22 → PER43)
-- ============================================================

-- MODULE 7 — Transactions bancaires
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER22', 'Effectuer dépôts',          'Saisir un dépôt sur un compte client',                       NOW(), NOW()),
  ('EBEN-PER23', 'Effectuer retraits',         'Saisir un retrait sur un compte client',                    NOW(), NOW()),
  ('EBEN-PER24', 'Effectuer virements',        'Initier un virement entre comptes',                         NOW(), NOW()),
  ('EBEN-PER25', 'Annuler transactions',       'Annuler ou reverser une opération bancaire',                NOW(), NOW()),
  ('EBEN-PER26', 'Valider transactions',       'Approuver les opérations en attente (double validation)',   NOW(), NOW());

-- MODULE 8 — Épargne
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER27', 'Voir produits épargne',      'Consulter les produits d\'épargne disponibles',             NOW(), NOW()),
  ('EBEN-PER28', 'Gérer produits épargne',     'Créer et modifier les produits d\'épargne',                 NOW(), NOW()),
  ('EBEN-PER29', 'Gérer comptes épargne',      'Ouvrir, alimenter et clôturer des comptes épargne',        NOW(), NOW());

-- MODULE 9 — Crédits / Prêts
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER30', 'Voir crédits',               'Consulter les dossiers de crédit',                          NOW(), NOW()),
  ('EBEN-PER31', 'Soumettre demande crédit',   'Créer une demande de prêt pour un client',                  NOW(), NOW()),
  ('EBEN-PER32', 'Instruire dossier crédit',   'Analyser et compléter un dossier de crédit',               NOW(), NOW()),
  ('EBEN-PER33', 'Approuver crédit',           'Accorder ou rejeter un crédit (niveau comité)',             NOW(), NOW()),
  ('EBEN-PER34', 'Gérer remboursements',       'Saisir les échéances et paiements de remboursement',       NOW(), NOW()),
  ('EBEN-PER35', 'Clôturer crédit',            'Marquer un crédit comme soldé ou en contentieux',          NOW(), NOW());

-- MODULE 10 — Rapports & Statistiques
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER36', 'Voir rapports opérationnels','Rapports journaliers caisse et transactions',                NOW(), NOW()),
  ('EBEN-PER37', 'Voir rapports financiers',   'Bilan, compte de résultat, situation financière',          NOW(), NOW()),
  ('EBEN-PER38', 'Exporter rapports',          'Exporter ou imprimer tous les rapports en PDF/Excel',      NOW(), NOW());

-- MODULE 11 — Comptabilité
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER39', 'Voir journal comptable',     'Consulter les écritures du journal comptable',              NOW(), NOW()),
  ('EBEN-PER40', 'Saisir écritures',           'Créer des écritures comptables manuelles',                 NOW(), NOW()),
  ('EBEN-PER41', 'Valider écritures',          'Approuver et lettrer les écritures comptables',            NOW(), NOW());

-- MODULE 12 — Audit & Sécurité
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER42', 'Voir journal d\'activité',   'Logs d\'audit : qui a fait quoi et quand',                 NOW(), NOW()),
  ('EBEN-PER43', 'Gérer paramètres sécurité',  'Politique mots de passe, tentatives login, blocages',      NOW(), NOW());

-- ============================================================
-- 2. NOUVEAUX RÔLES — ROL6 Chargé de crédit / ROL7 Comptable
-- ============================================================
INSERT IGNORE INTO `tb_roles` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL6', 'Chargé de crédit', 'Gestion complète des dossiers crédit, épargne et comptes clients', NOW(), NOW()),
  ('EBEN-ROL7', 'Comptable',        'Comptabilité, rapports financiers, validation des écritures',       NOW(), NOW());

-- ============================================================
-- 3. PERMISSIONS DES RÔLES
-- ============================================================

-- ── ROL2 Caissier : ajout transactions ──────────────────────
INSERT IGNORE INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL2', 'EBEN-PER22', NOW(), NOW()),   -- Dépôts
  ('EBEN-ROL2', 'EBEN-PER23', NOW(), NOW()),   -- Retraits
  ('EBEN-ROL2', 'EBEN-PER24', NOW(), NOW()),   -- Virements
  ('EBEN-ROL2', 'EBEN-PER29', NOW(), NOW()),   -- Comptes épargne
  ('EBEN-ROL2', 'EBEN-PER34', NOW(), NOW());   -- Remboursements

-- ── ROL3 Directeur : supervision + approbations ─────────────
INSERT IGNORE INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL3', 'EBEN-PER26', NOW(), NOW()),   -- Valider transactions
  ('EBEN-ROL3', 'EBEN-PER27', NOW(), NOW()),   -- Voir épargne
  ('EBEN-ROL3', 'EBEN-PER30', NOW(), NOW()),   -- Voir crédits
  ('EBEN-ROL3', 'EBEN-PER33', NOW(), NOW()),   -- Approuver crédit
  ('EBEN-ROL3', 'EBEN-PER36', NOW(), NOW()),   -- Rapports opérationnels
  ('EBEN-ROL3', 'EBEN-PER37', NOW(), NOW()),   -- Rapports financiers
  ('EBEN-ROL3', 'EBEN-PER38', NOW(), NOW()),   -- Exporter
  ('EBEN-ROL3', 'EBEN-PER39', NOW(), NOW()),   -- Voir journal comptable
  ('EBEN-ROL3', 'EBEN-PER42', NOW(), NOW());   -- Voir logs audit

-- ── ROL5 Superviseur : contrôle transversal ─────────────────
INSERT IGNORE INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL5', 'EBEN-PER26', NOW(), NOW()),   -- Valider transactions
  ('EBEN-ROL5', 'EBEN-PER27', NOW(), NOW()),   -- Voir épargne
  ('EBEN-ROL5', 'EBEN-PER30', NOW(), NOW()),   -- Voir crédits
  ('EBEN-ROL5', 'EBEN-PER36', NOW(), NOW()),   -- Rapports opérationnels
  ('EBEN-ROL5', 'EBEN-PER37', NOW(), NOW()),   -- Rapports financiers
  ('EBEN-ROL5', 'EBEN-PER38', NOW(), NOW()),   -- Exporter
  ('EBEN-ROL5', 'EBEN-PER42', NOW(), NOW());   -- Voir logs audit

-- ── ROL6 Chargé de crédit ────────────────────────────────────
INSERT IGNORE INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL6', 'EBEN-PER15', NOW(), NOW()),   -- Voir clients
  ('EBEN-ROL6', 'EBEN-PER16', NOW(), NOW()),   -- Créer clients
  ('EBEN-ROL6', 'EBEN-PER18', NOW(), NOW()),   -- Voir comptes
  ('EBEN-ROL6', 'EBEN-PER19', NOW(), NOW()),   -- Gérer comptes
  ('EBEN-ROL6', 'EBEN-PER27', NOW(), NOW()),   -- Voir épargne
  ('EBEN-ROL6', 'EBEN-PER28', NOW(), NOW()),   -- Gérer épargne
  ('EBEN-ROL6', 'EBEN-PER29', NOW(), NOW()),   -- Comptes épargne
  ('EBEN-ROL6', 'EBEN-PER30', NOW(), NOW()),   -- Voir crédits
  ('EBEN-ROL6', 'EBEN-PER31', NOW(), NOW()),   -- Soumettre crédit
  ('EBEN-ROL6', 'EBEN-PER32', NOW(), NOW()),   -- Instruire crédit
  ('EBEN-ROL6', 'EBEN-PER34', NOW(), NOW()),   -- Remboursements
  ('EBEN-ROL6', 'EBEN-PER35', NOW(), NOW());   -- Clôturer crédit

-- ── ROL7 Comptable ───────────────────────────────────────────
INSERT IGNORE INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL7', 'EBEN-PER18', NOW(), NOW()),   -- Voir comptes
  ('EBEN-ROL7', 'EBEN-PER37', NOW(), NOW()),   -- Rapports financiers
  ('EBEN-ROL7', 'EBEN-PER38', NOW(), NOW()),   -- Exporter
  ('EBEN-ROL7', 'EBEN-PER39', NOW(), NOW()),   -- Voir journal comptable
  ('EBEN-ROL7', 'EBEN-PER40', NOW(), NOW()),   -- Saisir écritures
  ('EBEN-ROL7', 'EBEN-PER41', NOW(), NOW());   -- Valider écritures

-- ============================================================
SET FOREIGN_KEY_CHECKS = 1;
-- ============================================================
-- FIN — 22 nouvelles permissions + 2 nouveaux rôles insérés.
-- ============================================================
