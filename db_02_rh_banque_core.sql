-- ==============================================================
-- NEXUS BMB TECH SOFT BANKING
-- FICHIER 2/3 : RESSOURCES HUMAINES + BANQUE CŒUR
-- ==============================================================
-- Dépendances : db_01_laravel_core.sql doit être exécuté avant.
--
-- Contenu :
--   [RH]     tb_services, tb_postes, tb_agents
--   [AUTH]   users, tb_role_user
--   [RH]     tb_zones, tb_affectations, tb_portefeuilles_agents
--   [BANQUE] tb_devises, tb_taux_echanges
--   [BANQUE] tb_clients, tb_comptes, tb_transactions
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
DROP TABLE IF EXISTS `tb_transactions`;
DROP TABLE IF EXISTS `tb_comptes`;
DROP TABLE IF EXISTS `tb_clients`;
DROP TABLE IF EXISTS `tb_taux_echanges`;
DROP TABLE IF EXISTS `tb_devises`;
DROP TABLE IF EXISTS `tb_affectations`;
DROP TABLE IF EXISTS `tb_portefeuilles_agents`;
DROP TABLE IF EXISTS `tb_zones`;
DROP TABLE IF EXISTS `tb_role_user`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `tb_agents`;
DROP TABLE IF EXISTS `tb_postes`;
DROP TABLE IF EXISTS `tb_services`;

-- ==============================================================
-- SECTION A — RESSOURCES HUMAINES (organigramme)
-- ==============================================================

-- A1. Services de la banque (ex : Caisse, Crédit, Direction)
CREATE TABLE `tb_services` (
  `id`          bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom`         varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text         CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at`  timestamp    NULL DEFAULT NULL,
  `updated_at`  timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A2. Postes (ex : Caissier Principal, Chargé de Crédit)
-- Dépend de : tb_services
CREATE TABLE `tb_postes` (
  `id`          bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_id`  bigint unsigned NOT NULL,
  `nom`         varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text         CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at`  timestamp    NULL DEFAULT NULL,
  `updated_at`  timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tb_postes_service_id_foreign` (`service_id`),
  CONSTRAINT `tb_postes_service_id_foreign`
    FOREIGN KEY (`service_id`) REFERENCES `tb_services` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A3. Agents (employés de la banque)
-- Table centrale référencée par users, tb_zones, tb_affectations, etc.
CREATE TABLE `tb_agents` (
  `matricule`    varchar(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom`          varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `postnom`      varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prenom`       varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexe`         enum('M','F') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_naissance`  date      DEFAULT NULL,
  `telephone`    varchar(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email`        varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse`      varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_embauche` date        DEFAULT NULL,
  `statut`       enum('actif','inactif') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif',
  `created_at`   timestamp    NULL DEFAULT NULL,
  `updated_at`   timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- SECTION B — AUTHENTIFICATION UTILISATEURS
-- ==============================================================

-- B1. Comptes utilisateurs Laravel (liés à un agent)
-- Dépend de : tb_agents
CREATE TABLE `users` (
  `id`                bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_matricule`   varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name`              varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email`             varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp    NULL DEFAULT NULL,
  `password`          varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token`    varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat`              varchar(20)  COLLATE utf8mb4_unicode_ci DEFAULT 'actif',
  `created_at`        timestamp    NULL DEFAULT NULL,
  `updated_at`        timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `fk_agent_matricule` (`agent_matricule`),
  CONSTRAINT `fk_agent_matricule`
    FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- B2. Table pivot Utilisateurs ↔ Rôles (tb_roles défini dans db_01)
-- Dépend de : users, tb_roles (fichier 1)
CREATE TABLE `tb_role_user` (
  `id`         bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id`    bigint unsigned NOT NULL,
  `role_code`  varchar(20)    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp      NULL DEFAULT NULL,
  `updated_at` timestamp      NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contrainte_role` (`role_code`),
  KEY `contrainte_user` (`user_id`),
  CONSTRAINT `contrainte_role`
    FOREIGN KEY (`role_code`) REFERENCES `tb_roles` (`code`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `contrainte_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- SECTION C — ORGANISATION TERRITORIALE ET RH OPÉRATIONNEL
-- ==============================================================

-- C1. Zones géographiques commerciales
-- Dépend de : tb_agents
CREATE TABLE `tb_zones` (
  `code_zone`                  varchar(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom`                        varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `agent_commercial_matricule` varchar(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `commune`                    varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at`                 timestamp    NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`                 timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`code_zone`),
  KEY `tb_zones_ibfk_1` (`agent_commercial_matricule`),
  CONSTRAINT `tb_zones_ibfk_1`
    FOREIGN KEY (`agent_commercial_matricule`) REFERENCES `tb_agents` (`matricule`)
    ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- C2. Affectations des agents aux postes
-- Dépend de : tb_agents, tb_postes
CREATE TABLE `tb_affectations` (
  `id`              bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_matricule` varchar(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `poste_id`        bigint unsigned NOT NULL,
  -- Guichet de caisse titulaire (nullable — agent hors caisse si NULL)
  -- FK ajoutée via ALTER TABLE dans db_03 (tb_caisses_guichets est dans ce fichier)
  `guichet_id`      bigint unsigned DEFAULT NULL COMMENT 'Guichet de caisse; FK vers tb_caisses_guichets ajoutée par db_03',
  `date_debut`      date         NOT NULL,
  `date_fin`        date         DEFAULT NULL,
  `Etat`            varchar(50)  COLLATE utf8mb4_unicode_ci NOT NULL,  -- ex: ACTIF, TERMINE
  `created_at`      timestamp    NULL DEFAULT NULL,
  `updated_at`      timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tb_affectations_agent_matricule_foreign` (`agent_matricule`),
  KEY `tb_affectations_poste_id_foreign`        (`poste_id`),
  KEY `fk_affectation_guichet`                  (`guichet_id`),
  CONSTRAINT `tb_affectations_agent_matricule_foreign`
    FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `tb_affectations_poste_id_foreign`
    FOREIGN KEY (`poste_id`) REFERENCES `tb_postes` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
  -- CONSTRAINT fk_affectation_guichet FK ajoutée dans db_03_caisse_guichet.sql
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- C3. Portefeuilles des agents commerciaux (commissions)
-- Dépend de : tb_agents
CREATE TABLE `tb_portefeuilles_agents` (
  `id`                    bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_matricule`       varchar(50)   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_portefeuille`      varchar(100)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `taux_commission_agent` decimal(5,2)  DEFAULT '0.00',
  `created_at`            timestamp     NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_port_agent` (`agent_matricule`),
  CONSTRAINT `fk_port_agent`
    FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- SECTION D — RÉFÉRENTIELS MONÉTAIRES
-- ==============================================================

-- D1. Devises (ex : CDF, USD, EUR)
-- Table racine sans dépendances — référencée partout
CREATE TABLE `tb_devises` (
  `code_iso`      varchar(3)  COLLATE utf8mb4_unicode_ci NOT NULL,  -- ex: CDF, USD
  `nom`           varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbole`       varchar(5)  COLLATE utf8mb4_unicode_ci NOT NULL,
  `est_reference` tinyint(1)  DEFAULT '0',                          -- 1 = devise de référence (CDF)
  `created_at`    timestamp   NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    timestamp   NULL DEFAULT NULL,
  PRIMARY KEY (`code_iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- D2. Taux de change entre devises
-- Dépend de : tb_devises
CREATE TABLE `tb_taux_echanges` (
  `id`                  bigint unsigned NOT NULL AUTO_INCREMENT,
  `devise_source`       varchar(3)   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `devise_destination`  varchar(3)   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `taux`                decimal(18,4) NOT NULL,
  `date_application`    timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at`          timestamp    NULL DEFAULT NULL,
  `updated_at`          timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_devise_src`  (`devise_source`),
  KEY `fk_devise_dest` (`devise_destination`),
  CONSTRAINT `fk_devise_src`
    FOREIGN KEY (`devise_source`)      REFERENCES `tb_devises` (`code_iso`)
    ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_devise_dest`
    FOREIGN KEY (`devise_destination`) REFERENCES `tb_devises` (`code_iso`)
    ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- SECTION E — CLIENTS ET COMPTES BANCAIRES
-- ==============================================================

-- E1. Clients de la banque
-- Dépend de : tb_zones
CREATE TABLE `tb_clients` (
  `matricule`               varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_zone`               varchar(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom`                     varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `postnom`                 varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom`                  varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email`                   varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone`               varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexe`                    enum('M','F') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_naissance`          date         NOT NULL,
  `lieu_naissance`          varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse`                 varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `etat_civil`              varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_conjoint`            varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_piece_identite`     varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lieu_delivrance_piece`   varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_delivrance_piece`   date         NOT NULL,
  `numero_piece_identite`   varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo`                   varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  -- Informations professionnelles
  `secteur_activite`        varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_activite`           varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom_entreprise`          varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse_entreprise`      varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone_entreprise`    varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut_entreprise`       varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_annees_experience` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revenu_mensuel`          decimal(15,2) DEFAULT NULL,
  `revenu_mensuel_devise`   varchar(10)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `autres_details_activite` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at`              timestamp    NULL DEFAULT NULL,
  `updated_at`              timestamp    NULL DEFAULT NULL,
  UNIQUE KEY `clients_matricule_unique` (`matricule`),
  KEY `tb_zones_ibfk_11` (`code_zone`),
  CONSTRAINT `tb_zones_ibfk_11`
    FOREIGN KEY (`code_zone`) REFERENCES `tb_zones` (`code_zone`)
    ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- E2. Comptes bancaires des clients
-- Dépend de : tb_devises, tb_portefeuilles_agents, tb_clients
CREATE TABLE `tb_comptes` (
  `code_compte`       varchar(64)  COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_matricule`  varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `devise`            varchar(3)   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type`              enum('COURANT','EPARGNE_LIBRE','EPARGNE_BLOQUEE','CAUTION_CREDIT')
                      CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `portefeuille_id`   bigint unsigned DEFAULT NULL,        -- Agent commercial rattaché
  `solde_reel`        decimal(18,2)  DEFAULT '0.00',
  `solde_bloque`      decimal(18,2)  DEFAULT '0.00',
  `created_at`        timestamp      NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        datetime       DEFAULT NULL,
  PRIMARY KEY (`code_compte`),
  KEY `fk_compte_devise`        (`devise`),
  KEY `fk_compte_portefeuille`  (`portefeuille_id`),
  KEY `tb_comptes_ibfk_112`     (`client_matricule`),
  CONSTRAINT `fk_compte_devise`
    FOREIGN KEY (`devise`)            REFERENCES `tb_devises`             (`code_iso`)
    ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_compte_portefeuille`
    FOREIGN KEY (`portefeuille_id`)   REFERENCES `tb_portefeuilles_agents` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tb_comptes_ibfk_112`
    FOREIGN KEY (`client_matricule`)  REFERENCES `tb_clients`             (`matricule`)
    ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- E3. Transactions sur comptes (dépôts, retraits, virements)
-- Dépend de : tb_comptes, tb_agents
CREATE TABLE `tb_transactions` (
  `id`              bigint unsigned NOT NULL AUTO_INCREMENT,
  `compte_code`     varchar(64)  COLLATE utf8mb4_unicode_ci NOT NULL,
  `agent_matricule` varchar(50)  COLLATE utf8mb4_unicode_ci NOT NULL,
  `type`            enum('DEPOT','RETRAIT','VIREMENT','REMBOURSEMENT')
                    COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant`         decimal(18,2) NOT NULL,
  `reference`       varchar(50)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `compte_code`     (`compte_code`),
  KEY `agent_matricule` (`agent_matricule`),
  CONSTRAINT `tb_transactions_ibfk_1`
    FOREIGN KEY (`compte_code`)     REFERENCES `tb_comptes` (`code_compte`)
    ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tb_transactions_ibfk_2`
    FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents`  (`matricule`)
    ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- DONNÉES DE DÉMARRAGE — FICHIER 2/3
-- (Services, Postes, Agents, Users, Rôles, Devises)
-- ==============================================================

-- 1. Services de base
INSERT IGNORE INTO `tb_services` (`id`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Direction Générale', 'Administration centrale du système',        NOW(), NOW()),
(2, 'Caisse',             'Gestion des guichets et opérations caisse', NOW(), NOW()),
(3, 'Ressources Humaines','Gestion du personnel et des affectations',  NOW(), NOW());

-- 2. Postes de base
INSERT IGNORE INTO `tb_postes` (`id`, `service_id`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Administrateur Système', 'Poste réservé au compte administrateur du système', NOW(), NOW()),
(2, 2, 'Caissier Principal',     'Gestion du guichet principal',                       NOW(), NOW()),
(3, 3, 'Responsable RH',         'Gestion des agents et des affectations',             NOW(), NOW());

-- 3. Agents
--    Matricules générés par Agent::boot() : AG-EBENKGA-YY-NNNNN
INSERT IGNORE INTO `tb_agents`
    (`matricule`, `nom`, `postnom`, `prenom`, `sexe`, `date_naissance`,
     `telephone`, `email`, `adresse`, `photo`, `date_embauche`, `statut`,
     `created_at`, `updated_at`)
VALUES
    -- Agent admin système
    ('AG-EBENKGA-26-00001', 'BMB', 'ADMIN', 'Système', 'M', NULL,
     NULL, 'bmb@bmb.cd', NULL, NULL, CURDATE(), 'actif', NOW(), NOW()),
    -- Caissier test
    ('AG-EBENKGA-26-00002', 'MULUMBA', NULL, 'Jean', 'M', '1990-06-15',
     '+243810000002', 'jean.caissier@bmb.cd', 'Kinshasa', NULL, CURDATE(), 'actif', NOW(), NOW()),
    -- Agent RH test
    ('AG-EBENKGA-26-00003', 'KASONGO', NULL, 'Marie', 'F', '1992-03-20',
     '+243810000003', 'marie.rh@bmb.cd', 'Kinshasa', NULL, CURDATE(), 'actif', NOW(), NOW());

-- 4. Devises — CDF (référence), USD, EUR
INSERT IGNORE INTO `tb_devises` (`code_iso`, `nom`, `symbole`, `est_reference`, `created_at`, `updated_at`) VALUES
('CDF', 'Franc Congolais',  'Fc', 1, NOW(), NULL),
('USD', 'Dollar Américain', '$',  0, NOW(), NULL),
('EUR', 'Euro',             '€',  0, NOW(), NULL);

-- 5. Utilisateurs
--    Mots de passe (bcrypt cost=12) :
--      bmb@bmb.cd          → Bmb@2026
--      jean.caissier@bmb.cd → Caissier@2026
--      marie.rh@bmb.cd     → AgentRH@2026
INSERT IGNORE INTO `users`
    (`agent_matricule`, `name`, `email`, `email_verified_at`, `password`,
     `remember_token`, `etat`, `created_at`, `updated_at`)
VALUES
    ('AG-EBENKGA-26-00001', 'bmb',         'bmb@bmb.cd',           NOW(),
     '$2y$12$h2eVRVSTyES1nDSAzc91q.PGyeA8TvOdjWlEz5WXEo8OGop39HuTW',
     NULL, 'actif', NOW(), NOW()),
    ('AG-EBENKGA-26-00002', 'jean_caissier', 'jean.caissier@bmb.cd', NOW(),
     '$2y$12$o/m3X.G8ImNrB8WgzrankOb5R8trQnBbSwK4vVzYXa7WHjy6AIIfG',
     NULL, 'actif', NOW(), NOW()),
    ('AG-EBENKGA-26-00003', 'marie_rh',     'marie.rh@bmb.cd',      NOW(),
     '$2y$12$j8T6Zy8w4q.zzZ1eNt/eeuak5l4H/PdUWwz7rmUNqNKJL7UQ7PR4m',
     NULL, 'actif', NOW(), NOW());

-- 6. Affectations initiales
INSERT IGNORE INTO `tb_affectations` (`agent_matricule`, `poste_id`, `guichet_id`, `date_debut`, `date_fin`, `Etat`, `created_at`, `updated_at`) VALUES
('AG-EBENKGA-26-00001', 1, NULL, CURDATE(), NULL, 'ACTIF', NOW(), NOW()),
('AG-EBENKGA-26-00002', 2, NULL, CURDATE(), NULL, 'ACTIF', NOW(), NOW()),
('AG-EBENKGA-26-00003', 3, NULL, CURDATE(), NULL, 'ACTIF', NOW(), NOW());

-- 7. Assignation des rôles aux utilisateurs
--    bmb          → EBEN-ROL1 (Administrateur)
--    jean_caissier → EBEN-ROL2 (Caissier)
--    marie_rh     → EBEN-ROL4 (Agent RH)
INSERT IGNORE INTO `tb_role_user` (`user_id`, `role_code`, `created_at`, `updated_at`)
SELECT `id`, 'EBEN-ROL1', NOW(), NOW() FROM `users` WHERE `email` = 'bmb@bmb.cd';

INSERT IGNORE INTO `tb_role_user` (`user_id`, `role_code`, `created_at`, `updated_at`)
SELECT `id`, 'EBEN-ROL2', NOW(), NOW() FROM `users` WHERE `email` = 'jean.caissier@bmb.cd';

INSERT IGNORE INTO `tb_role_user` (`user_id`, `role_code`, `created_at`, `updated_at`)
SELECT `id`, 'EBEN-ROL4', NOW(), NOW() FROM `users` WHERE `email` = 'marie.rh@bmb.cd';

-- ==============================================================
-- FIN DU FICHIER 2/3
-- Prochain fichier : db_03_caisse_guichet.sql
-- ==============================================================

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
