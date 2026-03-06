-- ==============================================================
-- NEXUS BMB TECH SOFT BANKING
-- FICHIER 1/3 : NOYAU LARAVEL + SÉCURITÉ (RBAC)
-- ==============================================================
-- Tables sans dépendances métier.
-- Doit être exécuté EN PREMIER, avant db_02 et db_03.
--
-- Contenu :
--   [Laravel] cache, cache_locks, failed_jobs, job_batches,
--             jobs, migrations, password_reset_tokens, sessions
--   [RBAC]    tb_roles, tb_permissions, tb_role_permission
-- ==============================================================
-- Base : bdd_nexus_bmb_tech_soft_baking
-- Moteur : InnoDB | Encodage : utf8mb4_unicode_ci
-- Généré le : 2026-03-05
-- ==============================================================

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS,   UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- --------------------------------------------------------------
-- SUPPRESSION (ordre enfants → parents)
-- --------------------------------------------------------------
DROP TABLE IF EXISTS `tb_role_permission`;
DROP TABLE IF EXISTS `tb_permissions`;
DROP TABLE IF EXISTS `tb_roles`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `migrations`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `cache`;

-- ==============================================================
-- SECTION A — TABLES INTERNES LARAVEL
-- ==============================================================

-- A1. Cache applicatif
CREATE TABLE `cache` (
  `key`        varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value`      mediumtext   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int          NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A2. Verrous de cache
CREATE TABLE `cache_locks` (
  `key`        varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner`      varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int          NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A3. Jobs échoués (queue)
CREATE TABLE `failed_jobs` (
  `id`         bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid`       varchar(191)    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue`      text            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload`    longtext        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception`  longtext        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at`  timestamp       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A4. Batches de jobs (queue batch)
CREATE TABLE `job_batches` (
  `id`             varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name`           varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs`     int     NOT NULL,
  `pending_jobs`   int     NOT NULL,
  `failed_jobs`    int     NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options`        mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at`   int     DEFAULT NULL,
  `created_at`     int     NOT NULL,
  `finished_at`    int     DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A5. File de jobs (queue worker)
CREATE TABLE `jobs` (
  `id`           bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue`        varchar(191)   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload`      longtext       CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts`     tinyint unsigned NOT NULL,
  `reserved_at`  int unsigned   DEFAULT NULL,
  `available_at` int unsigned   NOT NULL,
  `created_at`   int unsigned   NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A6. Historique des migrations
CREATE TABLE `migrations` (
  `id`        int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch`     int          NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A7. Tokens de réinitialisation de mot de passe
CREATE TABLE `password_reset_tokens` (
  `email`      varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token`      varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A8. Sessions utilisateurs
CREATE TABLE `sessions` (
  `id`            varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id`       bigint unsigned DEFAULT NULL,
  `ip_address`    varchar(45)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent`    text         CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload`       longtext     CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int          NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index`       (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- SECTION B — RBAC (Contrôle d'accès basé sur les rôles)
-- ==============================================================

-- B1. Rôles (ex : ADMIN, CAISSIER, DIRECTEUR)
CREATE TABLE `tb_roles` (
  `code`        varchar(20)  COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom`         varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at`  timestamp    NULL DEFAULT NULL,
  `updated_at`  timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `unique_nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- B2. Permissions atomiques (ex : VOIR_CAISSE, VALIDER_TRANSACTION)
CREATE TABLE `tb_permissions` (
  `code`        varchar(20)  COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom`         varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at`  timestamp    NULL DEFAULT NULL,
  `updated_at`  timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `unique_nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- B3. Table pivot Rôles ↔ Permissions
CREATE TABLE `tb_role_permission` (
  `role_code`       varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at`      timestamp   NULL DEFAULT NULL,
  `updated_at`      timestamp   NULL DEFAULT NULL,
  PRIMARY KEY (`role_code`, `permission_code`),
  KEY `fk_rp_permission` (`permission_code`),
  CONSTRAINT `fk_rp_role`       FOREIGN KEY (`role_code`)       REFERENCES `tb_roles`       (`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_code`) REFERENCES `tb_permissions` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- DONNÉES DE DÉMARRAGE — FICHIER 1/3
-- (Rôles, Permissions, Liaison ADMIN ↔ toutes permissions)
-- ==============================================================

-- Rôles système
-- Codes générés automatiquement par Role::boot() : EBEN-ROL1, EBEN-ROL2, ...
INSERT IGNORE INTO `tb_roles` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
('EBEN-ROL1', 'Administrateur', 'Accès total au système',              NOW(), NOW()),
('EBEN-ROL2', 'Caissier',       'Gestion caisse et guichet',           NOW(), NOW()),
('EBEN-ROL3', 'Directeur',      'Supervision générale',                NOW(), NOW()),
('EBEN-ROL4', 'Agent RH',       'Gestion des ressources humaines',     NOW(), NOW()),
('EBEN-ROL5', 'Superviseur',    'Supervision opérationnelle',          NOW(), NOW());

-- Permissions
-- Codes générés automatiquement par Permission::boot() : EBEN-PER1, EBEN-PER2, ...
INSERT IGNORE INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
-- Administration
('EBEN-PER1',  'Accès Administration', 'Accès au panneau d''administration',  NOW(), NOW()),
('EBEN-PER2',  'Voir les rôles',       'Consultation des rôles',              NOW(), NOW()),
('EBEN-PER3',  'Gérer les rôles',      'Création et modification des rôles',  NOW(), NOW()),
('EBEN-PER4',  'Voir les permissions', 'Consultation des permissions',        NOW(), NOW()),
('EBEN-PER5',  'Gérer les permissions','Gestion des permissions',             NOW(), NOW()),
-- RH
('EBEN-PER6',  'Voir RH',              'Accès au module RH',                  NOW(), NOW()),
('EBEN-PER7',  'Créer agent',          'Création d''un nouvel agent',         NOW(), NOW()),
('EBEN-PER8',  'Modifier agent',       'Modification d''un agent',            NOW(), NOW()),
('EBEN-PER9',  'Affectations',         'Gestion des affectations',            NOW(), NOW()),
-- Caisse / Guichet
('EBEN-PER10', 'Voir caisse',          'Consultation des caisses',            NOW(), NOW()),
('EBEN-PER11', 'Ouvrir caisse',        'Ouverture d''une caisse/guichet',     NOW(), NOW()),
('EBEN-PER12', 'Fermer caisse',        'Fermeture d''une caisse/guichet',     NOW(), NOW()),
('EBEN-PER13', 'Mouvements caisse',    'Enregistrement des mouvements',       NOW(), NOW()),
('EBEN-PER14', 'Clôture caisse',       'Clôture journalière caisse',          NOW(), NOW()),
-- Clients
('EBEN-PER15', 'Voir clients',         'Consultation des clients',            NOW(), NOW()),
('EBEN-PER16', 'Créer client',         'Enregistrement d''un client',         NOW(), NOW()),
('EBEN-PER17', 'Modifier client',      'Modification d''un client',           NOW(), NOW()),
-- Comptes bancaires
('EBEN-PER18', 'Voir comptes',         'Consultation des comptes',            NOW(), NOW()),
('EBEN-PER19', 'Créer compte',         'Ouverture d''un compte',              NOW(), NOW()),
-- Devises
('EBEN-PER20', 'Voir devises',         'Consultation des devises',            NOW(), NOW()),
('EBEN-PER21', 'Gérer devises',        'Gestion des devises et taux',         NOW(), NOW());

-- Assignation de toutes les permissions au rôle ADMIN (EBEN-ROL1)
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
('EBEN-ROL1', 'EBEN-PER21', NOW(), NOW());

-- ==============================================================
-- FIN DU FICHIER 1/3
-- Prochain fichier : db_02_rh_banque_core.sql
-- ==============================================================

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
