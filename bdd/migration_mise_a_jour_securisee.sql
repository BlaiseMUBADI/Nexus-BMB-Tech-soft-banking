-- =========================================================
-- SCRIPT DE MISE À JOUR SÉCURISÉE (Structure + Données Statiques)
-- Cible : Base en ligne (coopa2747247)
-- Objectif : Aligner la structure et les permissions avec le local SANS toucher aux données métier.
-- =========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- 1. MISE À JOUR DE LA STRUCTURE DES TABLES (ALTER TABLE)
-- --------------------------------------------------------

-- Ajout de la colonne prelevement_auto_autorise dans tb_credit_demandes
ALTER TABLE `tb_credit_demandes` 
ADD COLUMN IF NOT EXISTS `prelevement_auto_autorise` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Autorise le prélèvement automatique même avant la date d''échéance' AFTER `statut_global`;

-- Élargissement de la colonne numero_ordre dans tb_credit_deblocages (pour supporter les nouveaux formats)
ALTER TABLE `tb_credit_deblocages` 
MODIFY COLUMN `numero_ordre` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL;

-- Ajout de la colonne transaction_id dans tb_credit_remboursements
ALTER TABLE `tb_credit_remboursements` 
ADD COLUMN IF NOT EXISTS `transaction_id` bigint UNSIGNED DEFAULT NULL AFTER `recu_le`;

-- Ajout de la clé étrangère/index sur transaction_id si elle n'existe pas déjà
ALTER TABLE `tb_credit_remboursements`
ADD KEY IF NOT EXISTS `tb_credit_remboursements_transaction_id_foreign` (`transaction_id`);

-- --------------------------------------------------------
-- 2. MISE À JOUR DES DONNÉES STATIQUES (INSERT IGNORE)
-- L'utilisation de INSERT IGNORE garantit que les lignes existantes ne sont pas écrasées.
-- --------------------------------------------------------

-- 2.1. Nouvelles Permissions (Ajoutées récemment pour la caisse et les crédits)
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
('EBEN-PER109', 'Remboursements Caisse/Guichet', 'Consulter la liste des dossiers crédit en cours de remboursement depuis le module Caisse', '2026-06-02 21:16:11', '2026-06-02 21:16:11'),
('EBEN-PER111', 'Remboursements crédit (Caisse)', 'Consulter la liste des dossiers en cours de remboursement depuis le module Caisse/Guichet', '2026-06-02 21:16:12', '2026-06-02 21:16:12'),
('EBEN-PER112', 'Imprimer relevé crédit', 'Imprimer le relevé de compte crédit (PDF) depuis le module Caisse/Guichet', '2026-06-05 12:02:47', '2026-06-05 12:02:47');

-- 2.2. Rôles (Sécurisation de l'existence des rôles principaux)
INSERT IGNORE INTO `tb_roles` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
('EBEN-ROL1', 'Administrateur', 'Accès total au système', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
('EBEN-ROL2', 'Caissier', 'Gestion caisse et guichet', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
('EBEN-ROL3', 'Directeur', 'Supervision générale', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
('EBEN-ROL6', 'Chargé de crédit', 'Gestion complète des dossiers crédit, épargne et comptes clients', '2026-03-06 11:36:28', '2026-03-06 11:36:28'),
('EBEN-ROL8', 'Trésorier', 'Gestion complète du coffre-fort central, approvisionnements et transferts', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL11', 'Charge des opérations', 'responsable des produits', '2026-03-13 13:49:05', '2026-03-13 13:49:05'),
('EBEN-ROL12', 'GÉRANT', 'Il s\'occupe de la gérance de la coopérative', '2026-03-14 19:05:13', '2026-03-14 19:05:13'),
('EBEN-ROL14', 'CONTROLEUR INTERNE', 's\'assure le control', '2026-03-25 15:36:46', '2026-03-25 15:36:46');

-- 2.3. Assignations Rôle-Permission (Pour les nouvelles permissions)
INSERT IGNORE INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
('EBEN-ROL1', 'EBEN-PER109', '2026-06-02 21:16:11', '2026-06-02 21:16:11'),
('EBEN-ROL1', 'EBEN-PER111', '2026-06-02 21:16:12', '2026-06-02 21:16:12'),
('EBEN-ROL1', 'EBEN-PER112', '2026-06-05 12:02:47', '2026-06-05 12:02:47'),
('EBEN-ROL2', 'EBEN-PER109', '2026-06-02 21:16:11', '2026-06-02 21:16:11'),
('EBEN-ROL2', 'EBEN-PER111', '2026-06-02 21:16:12', '2026-06-02 21:16:12'),
('EBEN-ROL2', 'EBEN-PER112', '2026-06-07 11:56:16', '2026-06-07 11:56:16'),
('EBEN-ROL6', 'EBEN-PER109', '2026-06-02 21:16:11', '2026-06-02 21:16:11'),
('EBEN-ROL11', 'EBEN-PER109', '2026-06-02 21:16:11', '2026-06-02 21:16:11');

-- 2.4. Assignations Utilisateur-Rôle (Exemple pour les nouveaux utilisateurs de test ou récents)
-- Ajustez les user_id selon vos besoins réels sur le serveur
INSERT IGNORE INTO `tb_role_user` (`user_id`, `role_code`, `created_at`, `updated_at`) VALUES
(47, 'EBEN-ROL2', '2026-05-03 12:00:05', '2026-05-03 12:00:05');

COMMIT;