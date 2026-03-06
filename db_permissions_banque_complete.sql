-- ============================================================
-- FICHIER : db_permissions_banque_complete.sql
-- USAGE   : À exécuter sur une base EXISTANTE (nouvelle ou déjà
--           partiellement initialisée).  INSERT IGNORE partout —
--           aucune donnée existante n'est modifiée.
-- CONTENU : TOUTES les permissions PER1-PER43 + TOUS les rôles
--           ROL1-ROL7 + TOUTES les affectations rôle/permission.
-- ============================================================

SET NAMES 'utf8mb4';
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. TOUS LES RÔLES (ROL1 → ROL7)
-- ============================================================
INSERT IGNORE INTO `tb_roles` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL1', 'Administrateur',    'Accès total au système',                                          NOW(), NOW()),
  ('EBEN-ROL2', 'Caissier',          'Gestion caisse, guichet et transactions',                         NOW(), NOW()),
  ('EBEN-ROL3', 'Directeur',         'Supervision générale',                                            NOW(), NOW()),
  ('EBEN-ROL4', 'Agent RH',          'Gestion des ressources humaines',                                 NOW(), NOW()),
  ('EBEN-ROL5', 'Superviseur',       'Supervision opérationnelle',                                      NOW(), NOW()),
  ('EBEN-ROL6', 'Chargé de crédit',  'Gestion complète des dossiers crédit, épargne et comptes clients', NOW(), NOW()),
  ('EBEN-ROL7', 'Comptable',         'Comptabilité, rapports financiers, validation des écritures',    NOW(), NOW());

-- ============================================================
-- 2. TOUTES LES PERMISSIONS (PER1 → PER43)
-- ============================================================

-- MODULE 1 — Administration
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER1',  'Accès Administration',        'Accès au panneau d\'administration',                    NOW(), NOW()),
  ('EBEN-PER2',  'Voir les rôles',              'Consultation des rôles',                                 NOW(), NOW()),
  ('EBEN-PER3',  'Gérer les rôles',             'Création et modification des rôles',                    NOW(), NOW()),
  ('EBEN-PER4',  'Voir les permissions',        'Consultation des permissions',                           NOW(), NOW()),
  ('EBEN-PER5',  'Gérer les permissions',       'Gestion des permissions',                               NOW(), NOW());

-- MODULE 2 — Ressources Humaines
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER6',  'Voir RH',                     'Accès au module RH',                                    NOW(), NOW()),
  ('EBEN-PER7',  'Créer agent',                 'Création d\'un nouvel agent',                           NOW(), NOW()),
  ('EBEN-PER8',  'Modifier agent',              'Modification d\'un agent',                              NOW(), NOW()),
  ('EBEN-PER9',  'Affectations',                'Gestion des affectations',                              NOW(), NOW());

-- MODULE 3 — Caisse & Guichet
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER10', 'Voir caisse',                 'Consultation des caisses',                              NOW(), NOW()),
  ('EBEN-PER11', 'Ouvrir caisse',               'Ouverture d\'une caisse/guichet',                       NOW(), NOW()),
  ('EBEN-PER12', 'Fermer caisse',               'Fermeture d\'une caisse/guichet',                       NOW(), NOW()),
  ('EBEN-PER13', 'Mouvements caisse',           'Enregistrement des mouvements',                         NOW(), NOW()),
  ('EBEN-PER14', 'Clôture caisse',              'Clôture journalière caisse',                            NOW(), NOW());

-- MODULE 4 — Clients
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER15', 'Voir clients',                'Consultation des clients',                              NOW(), NOW()),
  ('EBEN-PER16', 'Créer client',                'Enregistrement d\'un client',                           NOW(), NOW()),
  ('EBEN-PER17', 'Modifier client',             'Modification d\'un client',                             NOW(), NOW());

-- MODULE 5 — Comptes
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER18', 'Voir comptes',                'Consultation des comptes',                              NOW(), NOW()),
  ('EBEN-PER19', 'Créer compte',                'Ouverture d\'un compte',                                NOW(), NOW());

-- MODULE 6 — Devises
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER20', 'Voir devises',                'Consultation des devises',                              NOW(), NOW()),
  ('EBEN-PER21', 'Gérer devises',               'Gestion des devises et taux',                           NOW(), NOW());

-- MODULE 7 — Transactions bancaires
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER22', 'Effectuer dépôts',            'Saisir un dépôt sur un compte client',                  NOW(), NOW()),
  ('EBEN-PER23', 'Effectuer retraits',          'Saisir un retrait sur un compte client',                NOW(), NOW()),
  ('EBEN-PER24', 'Effectuer virements',         'Initier un virement entre comptes',                    NOW(), NOW()),
  ('EBEN-PER25', 'Annuler transactions',        'Annuler ou reverser une opération bancaire',            NOW(), NOW()),
  ('EBEN-PER26', 'Valider transactions',        'Approuver les opérations en attente (double validation)', NOW(), NOW());

-- MODULE 8 — Épargne
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER27', 'Voir produits épargne',       'Consulter les produits d\'épargne disponibles',         NOW(), NOW()),
  ('EBEN-PER28', 'Gérer produits épargne',      'Créer et modifier les produits d\'épargne',             NOW(), NOW()),
  ('EBEN-PER29', 'Gérer comptes épargne',       'Ouvrir, alimenter et clôturer des comptes épargne',    NOW(), NOW());

-- MODULE 9 — Crédits / Prêts
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER30', 'Voir crédits',                'Consulter les dossiers de crédit',                      NOW(), NOW()),
  ('EBEN-PER31', 'Soumettre demande crédit',    'Créer une demande de prêt pour un client',              NOW(), NOW()),
  ('EBEN-PER32', 'Instruire dossier crédit',    'Analyser et compléter un dossier de crédit',            NOW(), NOW()),
  ('EBEN-PER33', 'Approuver crédit',            'Accorder ou rejeter un crédit (niveau comité)',         NOW(), NOW()),
  ('EBEN-PER34', 'Gérer remboursements',        'Saisir les échéances et paiements de remboursement',   NOW(), NOW()),
  ('EBEN-PER35', 'Clôturer crédit',             'Marquer un crédit comme soldé ou en contentieux',      NOW(), NOW());

-- MODULE 10 — Rapports & Statistiques
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER36', 'Voir rapports opérationnels', 'Rapports journaliers caisse et transactions',           NOW(), NOW()),
  ('EBEN-PER37', 'Voir rapports financiers',    'Bilan, compte de résultat, situation financière',       NOW(), NOW()),
  ('EBEN-PER38', 'Exporter rapports',           'Exporter ou imprimer tous les rapports en PDF/Excel',  NOW(), NOW());

-- MODULE 11 — Comptabilité
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER39', 'Voir journal comptable',      'Consulter les écritures du journal comptable',          NOW(), NOW()),
  ('EBEN-PER40', 'Saisir écritures',            'Créer des écritures comptables manuelles',              NOW(), NOW()),
  ('EBEN-PER41', 'Valider écritures',           'Approuver et lettrer les écritures comptables',        NOW(), NOW());

-- MODULE 12 — Audit & Sécurité
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
  ('EBEN-PER42', 'Voir journal d\'activité',    'Logs d\'audit : qui a fait quoi et quand',              NOW(), NOW()),
  ('EBEN-PER43', 'Gérer paramètres sécurité',   'Politique mots de passe, tentatives login, blocages',  NOW(), NOW());

-- ============================================================
-- 3. TOUTES LES AFFECTATIONS RÔLE ↔ PERMISSION
-- ============================================================

-- ── ROL1 Administrateur — toutes les permissions ─────────────
-- (isAdmin() bypass en code : ces lignes sont un filet de
--  sécurité si le bypass est désactivé un jour)
INSERT IGNORE INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL1', 'EBEN-PER1',  NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER2',  NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER3',  NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER4',  NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER5',  NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER6',  NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER7',  NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER8',  NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER9',  NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER10', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER11', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER12', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER13', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER14', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER15', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER16', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER17', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER18', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER19', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER20', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER21', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER22', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER23', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER24', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER25', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER26', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER27', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER28', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER29', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER30', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER31', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER32', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER33', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER34', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER35', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER36', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER37', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER38', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER39', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER40', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER41', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER42', NOW(), NOW()),
  ('EBEN-ROL1', 'EBEN-PER43', NOW(), NOW());

-- ── ROL2 Caissier ────────────────────────────────────────────
INSERT IGNORE INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL2', 'EBEN-PER10', NOW(), NOW()),   -- Voir caisse
  ('EBEN-ROL2', 'EBEN-PER11', NOW(), NOW()),   -- Ouvrir caisse
  ('EBEN-ROL2', 'EBEN-PER12', NOW(), NOW()),   -- Fermer caisse
  ('EBEN-ROL2', 'EBEN-PER13', NOW(), NOW()),   -- Mouvements caisse
  ('EBEN-ROL2', 'EBEN-PER14', NOW(), NOW()),   -- Clôture caisse
  ('EBEN-ROL2', 'EBEN-PER15', NOW(), NOW()),   -- Voir clients
  ('EBEN-ROL2', 'EBEN-PER18', NOW(), NOW()),   -- Voir comptes
  ('EBEN-ROL2', 'EBEN-PER22', NOW(), NOW()),   -- Dépôts
  ('EBEN-ROL2', 'EBEN-PER23', NOW(), NOW()),   -- Retraits
  ('EBEN-ROL2', 'EBEN-PER24', NOW(), NOW()),   -- Virements
  ('EBEN-ROL2', 'EBEN-PER29', NOW(), NOW()),   -- Comptes épargne
  ('EBEN-ROL2', 'EBEN-PER34', NOW(), NOW());   -- Remboursements

-- ── ROL3 Directeur ───────────────────────────────────────────
INSERT IGNORE INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL3', 'EBEN-PER1',  NOW(), NOW()),   -- Accès Administration
  ('EBEN-ROL3', 'EBEN-PER2',  NOW(), NOW()),   -- Voir rôles
  ('EBEN-ROL3', 'EBEN-PER4',  NOW(), NOW()),   -- Voir permissions
  ('EBEN-ROL3', 'EBEN-PER6',  NOW(), NOW()),   -- Voir RH
  ('EBEN-ROL3', 'EBEN-PER10', NOW(), NOW()),   -- Voir caisse
  ('EBEN-ROL3', 'EBEN-PER15', NOW(), NOW()),   -- Voir clients
  ('EBEN-ROL3', 'EBEN-PER18', NOW(), NOW()),   -- Voir comptes
  ('EBEN-ROL3', 'EBEN-PER20', NOW(), NOW()),   -- Voir devises
  ('EBEN-ROL3', 'EBEN-PER26', NOW(), NOW()),   -- Valider transactions
  ('EBEN-ROL3', 'EBEN-PER27', NOW(), NOW()),   -- Voir épargne
  ('EBEN-ROL3', 'EBEN-PER30', NOW(), NOW()),   -- Voir crédits
  ('EBEN-ROL3', 'EBEN-PER33', NOW(), NOW()),   -- Approuver crédit
  ('EBEN-ROL3', 'EBEN-PER36', NOW(), NOW()),   -- Rapports opérationnels
  ('EBEN-ROL3', 'EBEN-PER37', NOW(), NOW()),   -- Rapports financiers
  ('EBEN-ROL3', 'EBEN-PER38', NOW(), NOW()),   -- Exporter
  ('EBEN-ROL3', 'EBEN-PER39', NOW(), NOW()),   -- Voir journal comptable
  ('EBEN-ROL3', 'EBEN-PER42', NOW(), NOW());   -- Voir logs audit

-- ── ROL4 Agent RH ────────────────────────────────────────────
INSERT IGNORE INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL4', 'EBEN-PER6',  NOW(), NOW()),   -- Voir RH
  ('EBEN-ROL4', 'EBEN-PER7',  NOW(), NOW()),   -- Créer agent
  ('EBEN-ROL4', 'EBEN-PER8',  NOW(), NOW()),   -- Modifier agent
  ('EBEN-ROL4', 'EBEN-PER9',  NOW(), NOW());   -- Affectations

-- ── ROL5 Superviseur ─────────────────────────────────────────
INSERT IGNORE INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL5', 'EBEN-PER2',  NOW(), NOW()),   -- Voir rôles
  ('EBEN-ROL5', 'EBEN-PER6',  NOW(), NOW()),   -- Voir RH
  ('EBEN-ROL5', 'EBEN-PER10', NOW(), NOW()),   -- Voir caisse
  ('EBEN-ROL5', 'EBEN-PER15', NOW(), NOW()),   -- Voir clients
  ('EBEN-ROL5', 'EBEN-PER18', NOW(), NOW()),   -- Voir comptes
  ('EBEN-ROL5', 'EBEN-PER20', NOW(), NOW()),   -- Voir devises
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
  ('EBEN-ROL6', 'EBEN-PER16', NOW(), NOW()),   -- Créer client
  ('EBEN-ROL6', 'EBEN-PER18', NOW(), NOW()),   -- Voir comptes
  ('EBEN-ROL6', 'EBEN-PER19', NOW(), NOW()),   -- Créer compte
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
-- FIN — 43 permissions (PER1-PER43) + 7 rôles (ROL1-ROL7)
--       + toutes les affectations rôle/permission en un seul
--       fichier.  Idempotent : sûr à ré-exécuter plusieurs fois.
-- ============================================================
