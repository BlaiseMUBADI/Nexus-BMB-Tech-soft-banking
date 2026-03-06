-- ============================================================
-- FICHIER : db_users_test.sql
-- USAGE   : À exécuter sur une base EXISTANTE sans perdre
--           vos données. Utilise INSERT IGNORE partout.
-- ============================================================
--
-- Ce fichier ajoute UNIQUEMENT :
--   1. Les permissions manquantes pour les rôles 2-5 (ROL2/3/4/5)
--   2. 2 agents test (MULUMBA Jean + KASONGO Marie)
--   3. 2 utilisateurs test
--   4. Leurs affectations
--   5. Leurs rôles
--
-- Aucun DROP, aucun CREATE TABLE, aucune suppression.
--
-- COMPTES CRÉÉS :
--   ┌─────────────────────┬────────────────────────┬──────────────┐
--   │ Utilisateur         │ Mot de passe           │ Rôle         │
--   ├─────────────────────┼────────────────────────┼──────────────┤
--   │ jean_caissier       │ Caissier@2026          │ Caissier     │
--   │ marie_rh            │ AgentRH@2026           │ Agent RH     │
--   └─────────────────────┴────────────────────────┴──────────────┘
--
-- Pour annuler manuellement, exécutez la section DELETE
-- commentée en bas du fichier.
-- ============================================================

SET NAMES 'utf8mb4';
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. PERMISSIONS MANQUANTES POUR LES RÔLES 2-5
--    (correction du bug initial : rôles sans permissions)
-- ============================================================

-- Caissier (EBEN-ROL2) — caisse + consultation clients/comptes
INSERT IGNORE INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL2', 'EBEN-PER10', NOW(), NOW()),   -- Voir caisse
  ('EBEN-ROL2', 'EBEN-PER11', NOW(), NOW()),   -- Ouvrir caisse
  ('EBEN-ROL2', 'EBEN-PER12', NOW(), NOW()),   -- Fermer caisse
  ('EBEN-ROL2', 'EBEN-PER13', NOW(), NOW()),   -- Mouvements caisse
  ('EBEN-ROL2', 'EBEN-PER14', NOW(), NOW()),   -- Clôture caisse
  ('EBEN-ROL2', 'EBEN-PER15', NOW(), NOW()),   -- Voir clients
  ('EBEN-ROL2', 'EBEN-PER18', NOW(), NOW());   -- Voir comptes

-- Directeur (EBEN-ROL3) — vision globale lecture seule
INSERT IGNORE INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL3', 'EBEN-PER1',  NOW(), NOW()),   -- Voir tableau de bord admin
  ('EBEN-ROL3', 'EBEN-PER2',  NOW(), NOW()),   -- Voir utilisateurs
  ('EBEN-ROL3', 'EBEN-PER4',  NOW(), NOW()),   -- Voir rôles & permissions
  ('EBEN-ROL3', 'EBEN-PER6',  NOW(), NOW()),   -- Voir agents
  ('EBEN-ROL3', 'EBEN-PER10', NOW(), NOW()),   -- Voir caisse
  ('EBEN-ROL3', 'EBEN-PER15', NOW(), NOW()),   -- Voir clients
  ('EBEN-ROL3', 'EBEN-PER18', NOW(), NOW()),   -- Voir comptes
  ('EBEN-ROL3', 'EBEN-PER20', NOW(), NOW());   -- Rapports / statistiques

-- Agent RH (EBEN-ROL4) — gestion RH complète
INSERT IGNORE INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL4', 'EBEN-PER6',  NOW(), NOW()),   -- Voir agents
  ('EBEN-ROL4', 'EBEN-PER7',  NOW(), NOW()),   -- Créer/modifier agents
  ('EBEN-ROL4', 'EBEN-PER8',  NOW(), NOW()),   -- Gérer affectations
  ('EBEN-ROL4', 'EBEN-PER9',  NOW(), NOW());   -- Gérer services & postes

-- Superviseur (EBEN-ROL5) — supervision transversale lecture
INSERT IGNORE INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
  ('EBEN-ROL5', 'EBEN-PER2',  NOW(), NOW()),   -- Voir utilisateurs
  ('EBEN-ROL5', 'EBEN-PER6',  NOW(), NOW()),   -- Voir agents
  ('EBEN-ROL5', 'EBEN-PER10', NOW(), NOW()),   -- Voir caisse
  ('EBEN-ROL5', 'EBEN-PER15', NOW(), NOW()),   -- Voir clients
  ('EBEN-ROL5', 'EBEN-PER18', NOW(), NOW()),   -- Voir comptes
  ('EBEN-ROL5', 'EBEN-PER20', NOW(), NOW());   -- Rapports / statistiques

-- ============================================================
-- 2. AGENTS TEST
-- ============================================================
INSERT IGNORE INTO `tb_agents`
  (`matricule`, `nom`, `postnom`, `prenom`, `sexe`, `date_naissance`,
   `telephone`, `email`, `adresse`, `photo`, `date_embauche`, `statut`,
   `created_at`, `updated_at`)
VALUES
  ('AG-EBENKGA-26-00002', 'MULUMBA', NULL, 'Jean',  'M', '1990-06-15',
   '+243810000002', 'jean.caissier@bmb.cd', 'Kinshasa', NULL, CURDATE(), 'actif',
   NOW(), NOW()),
  ('AG-EBENKGA-26-00003', 'KASONGO', NULL, 'Marie', 'F', '1992-03-20',
   '+243810000003', 'marie.rh@bmb.cd',      'Kinshasa', NULL, CURDATE(), 'actif',
   NOW(), NOW());

-- ============================================================
-- 3. UTILISATEURS TEST
--    Hashes bcrypt cost=12 :
--      jean_caissier  / Caissier@2026
--      marie_rh       / AgentRH@2026
-- ============================================================
INSERT IGNORE INTO `users`
  (`agent_matricule`, `name`, `email`, `email_verified_at`,
   `password`, `remember_token`, `etat`, `created_at`, `updated_at`)
VALUES
  ('AG-EBENKGA-26-00002', 'jean_caissier', 'jean.caissier@bmb.cd', NOW(),
   '$2y$12$o/m3X.G8ImNrB8WgzrankOb5R8trQnBbSwK4vVzYXa7WHjy6AIIfG',
   NULL, 'actif', NOW(), NOW()),
  ('AG-EBENKGA-26-00003', 'marie_rh',      'marie.rh@bmb.cd',      NOW(),
   '$2y$12$j8T6Zy8w4q.zzZ1eNt/eeuak5l4H/PdUWwz7rmUNqNKJL7UQ7PR4m',
   NULL, 'actif', NOW(), NOW());

-- ============================================================
-- 4. AFFECTATIONS TEST
--    poste_id 2 = Caissier Principal  (service Caisse)
--    poste_id 3 = Responsable RH      (service RH)
-- ============================================================
INSERT IGNORE INTO `tb_affectations`
  (`agent_matricule`, `poste_id`, `guichet_id`, `date_debut`, `date_fin`,
   `Etat`, `created_at`, `updated_at`)
VALUES
  ('AG-EBENKGA-26-00002', 2, NULL, CURDATE(), NULL, 'ACTIF', NOW(), NOW()),
  ('AG-EBENKGA-26-00003', 3, NULL, CURDATE(), NULL, 'ACTIF', NOW(), NOW());

-- ============================================================
-- 5. ASSIGNATION DES RÔLES
--    Utilise des sous-requêtes pour résoudre les IDs
--    (auto-increment → non déterministe)
-- ============================================================
INSERT IGNORE INTO `tb_role_user` (`user_id`, `role_code`, `created_at`, `updated_at`)
VALUES
  ((SELECT `id` FROM `users` WHERE `email` = 'jean.caissier@bmb.cd' LIMIT 1),
   'EBEN-ROL2', NOW(), NOW()),
  ((SELECT `id` FROM `users` WHERE `email` = 'marie.rh@bmb.cd' LIMIT 1),
   'EBEN-ROL4', NOW(), NOW());

-- ============================================================
SET FOREIGN_KEY_CHECKS = 1;
-- ============================================================
-- FIN — Vos données existantes sont intactes.
-- ============================================================


-- ============================================================
-- ANNULATION (exécuter manuellement si nécessaire)
-- ============================================================
/*
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM `tb_role_user`
  WHERE `user_id` IN (
    SELECT `id` FROM `users`
    WHERE `email` IN ('jean.caissier@bmb.cd', 'marie.rh@bmb.cd')
  );

DELETE FROM `tb_affectations`
  WHERE `agent_matricule` IN ('AG-EBENKGA-26-00002', 'AG-EBENKGA-26-00003');

DELETE FROM `users`
  WHERE `email` IN ('jean.caissier@bmb.cd', 'marie.rh@bmb.cd');

DELETE FROM `tb_agents`
  WHERE `matricule` IN ('AG-EBENKGA-26-00002', 'AG-EBENKGA-26-00003');

-- (Optionnel) Supprimer les permissions des rôles 2-5 si vous voulez revenir en arrière :
-- DELETE FROM `tb_role_permission`
--   WHERE `role_code` IN ('EBEN-ROL2', 'EBEN-ROL3', 'EBEN-ROL4', 'EBEN-ROL5');

SET FOREIGN_KEY_CHECKS = 1;
*/
