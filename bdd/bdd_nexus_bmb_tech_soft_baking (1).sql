-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 30 avr. 2026 à 11:24
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bdd_nexus_bmb_tech_soft_baking`
--

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_affectations`
--

DROP TABLE IF EXISTS `tb_affectations`;
CREATE TABLE IF NOT EXISTS `tb_affectations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `agent_matricule` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `poste_id` bigint UNSIGNED NOT NULL,
  `guichet_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Guichet de caisse affecté (optionnel). NULL = agent hors caisse.',
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `Etat` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tb_affectations_agent_matricule_foreign` (`agent_matricule`),
  KEY `tb_affectations_poste_id_foreign` (`poste_id`),
  KEY `fk_affectation_guichet` (`guichet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_affectations_portefeuilles`
--

DROP TABLE IF EXISTS `tb_affectations_portefeuilles`;
CREATE TABLE IF NOT EXISTS `tb_affectations_portefeuilles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `portefeuille_id` bigint UNSIGNED NOT NULL,
  `agent_matricule` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `Etat` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIF',
  `motif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `effectue_par_user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_aff_pf_active` (`portefeuille_id`,`Etat`,`date_fin`),
  KEY `idx_aff_pf_agent_active` (`agent_matricule`,`Etat`,`date_fin`),
  KEY `idx_aff_pf_period` (`portefeuille_id`,`date_debut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_affectations_zones`
--

DROP TABLE IF EXISTS `tb_affectations_zones`;
CREATE TABLE IF NOT EXISTS `tb_affectations_zones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code_zone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `agent_matricule` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `Etat` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIF',
  `motif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `effectue_par_user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_aff_zone_active` (`code_zone`,`Etat`,`date_fin`),
  KEY `idx_aff_zone_agent_active` (`agent_matricule`,`Etat`,`date_fin`),
  KEY `idx_aff_zone_period` (`code_zone`,`date_debut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_agents`
--

DROP TABLE IF EXISTS `tb_agents`;
CREATE TABLE IF NOT EXISTS `tb_agents` (
  `matricule` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postnom` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prenom` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexe` enum('M','F') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `telephone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_embauche` date DEFAULT NULL,
  `statut` enum('actif','inactif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`matricule`),
  KEY `idx_tb_agents_matricule` (`matricule`),
  KEY `idx_tb_agents_matricule_fk_full` (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_caisses_guichets`
--

DROP TABLE IF EXISTS `tb_caisses_guichets`;
CREATE TABLE IF NOT EXISTS `tb_caisses_guichets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code_guichet` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_guichet` enum('FIXE','MOBILE','CENTRAL') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FIXE',
  `intitule` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut_operationnel` enum('OUVERT','FERME','SUSPENDU','EN_VERIFICATION') COLLATE utf8mb4_unicode_ci DEFAULT 'FERME',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tb_caisses_guichets_code_guichet_unique` (`code_guichet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_caisses_guichets_soldes`
--

DROP TABLE IF EXISTS `tb_caisses_guichets_soldes`;
CREATE TABLE IF NOT EXISTS `tb_caisses_guichets_soldes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `guichet_id` bigint UNSIGNED NOT NULL,
  `devise_code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `solde_en_caisse` decimal(18,2) NOT NULL DEFAULT '0.00',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_guichet_devise` (`guichet_id`,`devise_code`),
  KEY `fk_solde_devise` (`devise_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_clients`
--

DROP TABLE IF EXISTS `tb_clients`;
CREATE TABLE IF NOT EXISTS `tb_clients` (
  `matricule` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_zone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postnom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexe` enum('M','F') COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_naissance` date NOT NULL,
  `lieu_naissance` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `etat_civil` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_conjoint` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_piece_identite` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lieu_delivrance_piece` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_delivrance_piece` date NOT NULL,
  `numero_piece_identite` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secteur_activite` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_activite` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom_entreprise` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse_entreprise` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone_entreprise` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut_entreprise` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_annees_experience` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revenu_mensuel` decimal(15,2) DEFAULT NULL,
  `revenu_mensuel_devise` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `autres_details_activite` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `tb_clients_matricule_unique` (`matricule`),
  UNIQUE KEY `uq_tb_clients_piece_type_num` (`type_piece_identite`,`numero_piece_identite`),
  UNIQUE KEY `uq_tb_clients_email` (`email`),
  KEY `tb_zones_ibfk_11` (`code_zone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_cloture_caisse`
--

DROP TABLE IF EXISTS `tb_cloture_caisse`;
CREATE TABLE IF NOT EXISTS `tb_cloture_caisse` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `guichet_id` bigint UNSIGNED NOT NULL,
  `devise_code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `solde_comptable` decimal(18,2) NOT NULL,
  `solde_physique` decimal(18,2) NOT NULL,
  `ecart_caisse` decimal(18,2) NOT NULL,
  `detail_billetage` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `motif_ecart` text COLLATE utf8mb4_unicode_ci COMMENT 'Justification requise si écart ≠ 0',
  `statut_ecart` enum('EQUILIBRE','EXCEDENT','DEFICIT') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EQUILIBRE' COMMENT 'Résultat de la confrontation physique / système',
  `statut_validation` enum('EN_ATTENTE','VALIDE','REJETE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EN_ATTENTE' COMMENT 'Statut de validation par le superviseur',
  `validateur_matricule` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Matricule du superviseur ayant validé',
  `date_validation` timestamp NULL DEFAULT NULL COMMENT 'Date/heure de validation par le superviseur',
  `observations_superviseur` text COLLATE utf8mb4_unicode_ci COMMENT 'Commentaire du superviseur lors de la validation',
  `agent_cloturant` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_cloture` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_cloture_guichet` (`guichet_id`),
  KEY `fk_cloture_devise` (`devise_code`),
  KEY `fk_cloture_agent` (`agent_cloturant`),
  KEY `validateur_matricule` (`validateur_matricule`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `tb_commission_rules`
--

DROP TABLE IF EXISTS `tb_commission_rules`;
CREATE TABLE IF NOT EXISTS `tb_commission_rules` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `code_operation` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'TOUS',
  `type_compte` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'TOUS',
  `type_guichet` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'TOUS',
  `devise_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_zone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portefeuille_id` bigint UNSIGNED DEFAULT NULL,
  `montant_min` decimal(18,2) DEFAULT NULL,
  `montant_max` decimal(18,2) DEFAULT NULL,
  `mode_calcul` enum('FIXE','POURCENTAGE') COLLATE utf8mb4_general_ci NOT NULL,
  `valeur` decimal(18,4) NOT NULL,
  `priorite` int UNSIGNED NOT NULL DEFAULT '100',
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `est_actif` tinyint(1) NOT NULL DEFAULT '1',
  `observations` text COLLATE utf8mb4_general_ci,
  `created_by_agent` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_comm_rules_active_dates` (`est_actif`,`date_debut`,`date_fin`),
  KEY `idx_comm_rules_scope` (`code_operation`,`type_compte`,`type_guichet`),
  KEY `idx_comm_rules_context` (`devise_code`,`code_zone`,`portefeuille_id`),
  KEY `tb_comm_rules_portefeuille_fk` (`portefeuille_id`),
  KEY `tb_comm_rules_zone_fk` (`code_zone`),
  KEY `tb_agent` (`created_by_agent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_compta_ecritures`
--

DROP TABLE IF EXISTS `tb_compta_ecritures`;
CREATE TABLE IF NOT EXISTS `tb_compta_ecritures` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `journal_id` bigint UNSIGNED NOT NULL,
  `numero_compte` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `devise_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `libelle_ligne` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `debit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `ordre` int UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_compta_ecriture_compte_devise` (`numero_compte`,`devise_code`),
  KEY `idx_compta_ecriture_journal` (`journal_id`),
  KEY `fk_compta_ecriture_devise` (`devise_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_compta_journaux`
--

DROP TABLE IF EXISTS `tb_compta_journaux`;
CREATE TABLE IF NOT EXISTS `tb_compta_journaux` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code_journal` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'CAI',
  `reference_piece` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
  `transaction_id` bigint UNSIGNED DEFAULT NULL,
  `type_piece` enum('OPERATION','ANNULATION','REGULARISATION') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'OPERATION',
  `devise_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_general_ci NOT NULL,
  `statut` enum('COMPTABILISE','ANNULE') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'COMPTABILISE',
  `agent_matricule` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_ecriture` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tb_compta_journaux_reference_piece` (`reference_piece`),
  KEY `idx_compta_journal_trans_type` (`transaction_id`,`type_piece`),
  KEY `idx_compta_journal_date_devise` (`date_ecriture`,`devise_code`),
  KEY `fk_compta_journal_agent` (`agent_matricule`),
  KEY `fk_compta_journal_devise` (`devise_code`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `tb_comptes`
--

DROP TABLE IF EXISTS `tb_comptes`;
CREATE TABLE IF NOT EXISTS `tb_comptes` (
  `code_compte` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_matricule` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `devise` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('CC','RMB','GTC','DAT','EAV') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'CC=Compte Courant, RMB=Remboursement, GTC=Caution, DAT=Dépôt à Terme, EAV=Épargne & Vie',
  `portefeuille_id` bigint UNSIGNED DEFAULT NULL,
  `solde_reel` decimal(18,2) NOT NULL DEFAULT '0.00',
  `solde_bloque` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`code_compte`),
  UNIQUE KEY `uq_tb_comptes_client_type_devise` (`client_matricule`,`type`,`devise`),
  KEY `fk_compte_devise` (`devise`),
  KEY `fk_compte_portefeuille` (`portefeuille_id`),
  KEY `tb_comptes_ibfk_112` (`client_matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_analyses`
--

DROP TABLE IF EXISTS `tb_credit_analyses`;
CREATE TABLE IF NOT EXISTS `tb_credit_analyses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `credit_demande_id` bigint UNSIGNED NOT NULL,
  `analyseur_matricule` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revenu_mensuel_verifie` decimal(15,2) DEFAULT NULL,
  `capacite_remboursement` decimal(15,2) DEFAULT NULL,
  `ratio_endettement` decimal(6,2) DEFAULT NULL,
  `score_risque` enum('FAIBLE','MOYEN','ELEVE','TRES_ELEVE') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `historique_credit` text COLLATE utf8mb4_unicode_ci,
  `garanties_evaluees` text COLLATE utf8mb4_unicode_ci,
  `observations` text COLLATE utf8mb4_unicode_ci,
  `recommandation` enum('FAVORABLE','FAVORABLE_AVEC_RESERVE','DEFAVORABLE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant_recommande` decimal(15,2) DEFAULT NULL,
  `statut` enum('EN_COURS','COMPLETE','ANNULE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EN_COURS',
  `complete_le` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_analyse_demande` (`credit_demande_id`),
  KEY `idx_analyse_agt` (`analyseur_matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_audits`
--

DROP TABLE IF EXISTS `tb_credit_audits`;
CREATE TABLE IF NOT EXISTS `tb_credit_audits` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `credit_demande_id` bigint UNSIGNED NOT NULL,
  `acteur_matricule` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_action` enum('CREATION','SOUMISSION','ANALYSE_DEMARREE','ANALYSE_COMPLETE','VALIDATION_PARTIELLE','VALIDATION_COMPLETE','REJET','DEBLOCAGE','REMBOURSEMENT','ANNULATION','SUSPENSION','LEVER_SUSPENSION','SIGNALEMENT_SUSPECT','LEVER_SUSPICION','MODIFICATION') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ancien_statut` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nouveau_statut` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_audit_dem` (`credit_demande_id`),
  KEY `idx_audit_action` (`type_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_deblocages`
--

DROP TABLE IF EXISTS `tb_credit_deblocages`;
CREATE TABLE IF NOT EXISTS `tb_credit_deblocages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `credit_demande_id` bigint UNSIGNED NOT NULL,
  `agent_matricule` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `compte_debit_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guichet_solde_id` bigint UNSIGNED DEFAULT NULL,
  `compte_credit_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant_debloque` decimal(15,2) NOT NULL,
  `devise` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CDF',
  `frais_dossier` decimal(15,2) NOT NULL DEFAULT '0.00',
  `montant_net_verse` decimal(15,2) NOT NULL,
  `reference_transaction` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_ordre` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observations` text COLLATE utf8mb4_unicode_ci,
  `debloque_le` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tb_credit_deblocages_credit_demande_id_unique` (`credit_demande_id`),
  KEY `tb_credit_deblocages_compte_debit_id_foreign` (`compte_debit_id`),
  KEY `tb_credit_deblocages_compte_credit_id_foreign` (`compte_credit_id`),
  KEY `tb_credit_deblocages_guichet_solde_id_foreign` (`guichet_solde_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_demandes`
--

DROP TABLE IF EXISTS `tb_credit_demandes`;
CREATE TABLE IF NOT EXISTS `tb_credit_demandes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_dossier` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_matricule` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `compte_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portefeuille_id` bigint UNSIGNED DEFAULT NULL,
  `code_zone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `agent_createur_matricule` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `agent_analyse_matricule` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `montant_demande` decimal(15,2) NOT NULL,
  `devise` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CDF',
  `duree_mois` tinyint UNSIGNED NOT NULL,
  `taux_interet_mensuel` decimal(6,4) NOT NULL,
  `type_credit` enum('INDIVIDUEL','SOLIDAIRE','PME') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INDIVIDUEL',
  `objet_credit` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `garantie_description` text COLLATE utf8mb4_unicode_ci,
  `service_provenance` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Service ou département ayant référé le client',
  `referent_nom` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nom du référent dans le service',
  `montant_approuve` decimal(15,2) DEFAULT NULL,
  `montant_total_echeances` decimal(15,2) DEFAULT NULL,
  `total_interets` decimal(15,2) DEFAULT NULL,
  `statut_global` enum('BROUILLON','SOUMIS','EN_ANALYSE','EN_VALIDATION','PRET_A_DEBLOQUER','DEBLOQUE','EN_REMBOURSEMENT','EN_RETARD','SOLDE','ANNULE','SUSPENDU','SUSPECT') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BROUILLON',
  `est_annule` tinyint(1) NOT NULL DEFAULT '0',
  `motif_annulation` text COLLATE utf8mb4_unicode_ci,
  `annule_par_matricule` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `annule_le` timestamp NULL DEFAULT NULL,
  `est_suspendu` tinyint(1) NOT NULL DEFAULT '0',
  `motif_suspension` text COLLATE utf8mb4_unicode_ci,
  `suspendu_par_matricule` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suspendu_le` timestamp NULL DEFAULT NULL,
  `est_suspect` tinyint(1) NOT NULL DEFAULT '0',
  `motif_suspicion` text COLLATE utf8mb4_unicode_ci,
  `signale_par_matricule` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signale_le` timestamp NULL DEFAULT NULL,
  `soumis_le` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tb_credit_demandes_numero_dossier_unique` (`numero_dossier`),
  KEY `idx_crd_client` (`client_matricule`),
  KEY `idx_crd_zone` (`code_zone`),
  KEY `idx_crd_statut` (`statut_global`),
  KEY `idx_crd_portef` (`portefeuille_id`),
  KEY `idx_crd_agent` (`agent_createur_matricule`),
  KEY `tb_crd_compte_fk` (`compte_id`),
  KEY `idx_credit_demande_agent_analyse` (`agent_analyse_matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_echeances`
--

DROP TABLE IF EXISTS `tb_credit_echeances`;
CREATE TABLE IF NOT EXISTS `tb_credit_echeances` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `echeancier_id` bigint UNSIGNED NOT NULL,
  `numero_echeance` tinyint UNSIGNED NOT NULL,
  `date_echeance` date NOT NULL,
  `capital_restant_debut` decimal(15,2) NOT NULL,
  `capital_echeance` decimal(15,2) NOT NULL,
  `interet_echeance` decimal(15,2) NOT NULL,
  `total_echeance` decimal(15,2) NOT NULL,
  `capital_restant_fin` decimal(15,2) NOT NULL,
  `statut` enum('EN_ATTENTE','PAYE','PARTIELLEMENT_PAYE','EN_RETARD','REPORTE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EN_ATTENTE',
  `montant_paye` decimal(15,2) NOT NULL DEFAULT '0.00',
  `date_paiement_effectif` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ech_echeancier` (`echeancier_id`),
  KEY `idx_ech_date` (`date_echeance`),
  KEY `idx_ech_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_echeanciers`
--

DROP TABLE IF EXISTS `tb_credit_echeanciers`;
CREATE TABLE IF NOT EXISTS `tb_credit_echeanciers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `credit_demande_id` bigint UNSIGNED NOT NULL,
  `montant_capital` decimal(15,2) NOT NULL,
  `taux_mensuel` decimal(6,4) NOT NULL,
  `duree_mois` tinyint UNSIGNED NOT NULL,
  `date_premier_remboursement` date NOT NULL,
  `type_amortissement` enum('DEGRESSIF','LINEAIRE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DEGRESSIF',
  `total_capital` decimal(15,2) NOT NULL,
  `total_interets` decimal(15,2) NOT NULL,
  `total_general` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tb_credit_echeanciers_credit_demande_id_unique` (`credit_demande_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_pieces`
--

DROP TABLE IF EXISTS `tb_credit_pieces`;
CREATE TABLE IF NOT EXISTS `tb_credit_pieces` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `credit_demande_id` bigint UNSIGNED NOT NULL,
  `libelle` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_piece` enum('IDENTITE','DOMICILE','REVENU','GARANTIE','AUTRE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_fichier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `est_recu` tinyint(1) NOT NULL DEFAULT '0',
  `est_conforme` tinyint(1) DEFAULT NULL,
  `observations` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pieces_dem` (`credit_demande_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_remboursements`
--

DROP TABLE IF EXISTS `tb_credit_remboursements`;
CREATE TABLE IF NOT EXISTS `tb_credit_remboursements` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `credit_demande_id` bigint UNSIGNED NOT NULL,
  `echeance_id` bigint UNSIGNED DEFAULT NULL,
  `agent_matricule` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `compte_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant_recu` decimal(15,2) NOT NULL,
  `dont_capital` decimal(15,2) NOT NULL,
  `dont_interet` decimal(15,2) NOT NULL,
  `dont_penalite` decimal(15,2) NOT NULL DEFAULT '0.00',
  `devise` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CDF',
  `type_remboursement` enum('ECHEANCE','PARTIEL','ANTICIPE','PENALITE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ECHEANCE',
  `reference_caisse` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observations` text COLLATE utf8mb4_unicode_ci,
  `recu_le` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rembours_dem` (`credit_demande_id`),
  KEY `idx_rembours_ech` (`echeance_id`),
  KEY `tb_credit_remboursements_compte_id_foreign` (`compte_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_validations`
--

DROP TABLE IF EXISTS `tb_credit_validations`;
CREATE TABLE IF NOT EXISTS `tb_credit_validations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `credit_demande_id` bigint UNSIGNED NOT NULL,
  `type_validateur` enum('AGENT_CREDIT','CHARGE_OPERATIONS','CONTROLEUR','GERANT') COLLATE utf8mb4_unicode_ci NOT NULL,
  `validateur_matricule` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `decision` enum('EN_ATTENTE','APPROUVE','APPROUVE_AVEC_RESERVE','REJETE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EN_ATTENTE',
  `montant_valide` decimal(15,2) DEFAULT NULL,
  `duree_mois_validee` tinyint UNSIGNED DEFAULT NULL,
  `observations` text COLLATE utf8mb4_unicode_ci,
  `conditions` text COLLATE utf8mb4_unicode_ci,
  `ordre_etape` tinyint UNSIGNED NOT NULL,
  `etape_precedente_ok` tinyint(1) NOT NULL DEFAULT '0',
  `valide_le` timestamp NULL DEFAULT NULL,
  `signature_agent` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Matricule de l''agent signataire (auto)',
  `nom_signataire` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nom complet du signataire au moment de la validation',
  `ip_validation` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Adresse IP depuis laquelle la validation a été soumise',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_validation_type` (`credit_demande_id`,`type_validateur`),
  KEY `idx_valid_agt` (`validateur_matricule`),
  KEY `idx_valid_dec` (`decision`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_demandes_modification`
--

DROP TABLE IF EXISTS `tb_demandes_modification`;
CREATE TABLE IF NOT EXISTS `tb_demandes_modification` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint UNSIGNED NOT NULL COMMENT 'FK vers tb_transactions',
  `reference_operation` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Référence de l''opération initiale',
  `guichet_id` bigint UNSIGNED DEFAULT NULL COMMENT 'FK vers tb_caisses_guichets',
  `compte_code` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Compte client concerné',
  `client_nom` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nom du client (dénormalisation audit)',
  `type_operation` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Type original : DEPOT, RETRAIT...',
  `devise_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ancien_montant` decimal(15,2) DEFAULT NULL COMMENT 'Montant original',
  `anciennes_observations` text COLLATE utf8mb4_unicode_ci COMMENT 'Observations originales',
  `type_demande` enum('MODIFICATION','SUPPRESSION') COLLATE utf8mb4_unicode_ci NOT NULL,
  `agent_matricule` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Guichetier demandeur',
  `motif` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Motif obligatoire',
  `nouveau_montant` decimal(15,2) DEFAULT NULL COMMENT 'Nouveau montant demandé',
  `nouvelles_observations` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('EN_ATTENTE','APPROUVEE','REJETEE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EN_ATTENTE',
  `superviseur_matricule` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Superviseur ayant traité',
  `commentaire_superviseur` text COLLATE utf8mb4_unicode_ci,
  `traitee_le` timestamp NULL DEFAULT NULL COMMENT 'Date de traitement',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_statut_created` (`statut`,`created_at`),
  KEY `idx_agent_matricule` (`agent_matricule`),
  KEY `idx_superviseur_matricule` (`superviseur_matricule`),
  KEY `fk_demmod_transaction` (`transaction_id`),
  KEY `fk_demmod_guichet` (`guichet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_devises`
--

DROP TABLE IF EXISTS `tb_devises`;
CREATE TABLE IF NOT EXISTS `tb_devises` (
  `code_iso` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbole` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `est_reference` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code_iso`),
  UNIQUE KEY `uq_tb_devises_code_iso` (`code_iso`),
  KEY `idx_tb_devises_code_iso_fk` (`code_iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_mouvements_inter_caisses`
--

DROP TABLE IF EXISTS `tb_mouvements_inter_caisses`;
CREATE TABLE IF NOT EXISTS `tb_mouvements_inter_caisses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `guichet_source_id` bigint UNSIGNED DEFAULT NULL,
  `guichet_dest_id` bigint UNSIGNED DEFAULT NULL,
  `agent_initiateur` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_flux` enum('ALIMENTATION','DEGAGEMENT','TRANSFERT','DEMANDE_APPRO','DOTATION_MOBILE','REVERSEMENT_MOBILE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant` decimal(18,2) NOT NULL,
  `devise_code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_bordereau` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_mouvement` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('EN_ATTENTE','VALIDE','CONFIRME','ANNULE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CONFIRME',
  `validateur_matricule` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observations` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_reference_bordereau` (`reference_bordereau`),
  KEY `fk_mouv_guichet_src` (`guichet_source_id`),
  KEY `fk_mouv_guichet_dest` (`guichet_dest_id`),
  KEY `fk_mouv_agent` (`agent_initiateur`),
  KEY `fk_mouv_devise` (`devise_code`),
  KEY `validateur_matricule` (`validateur_matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_permissions`
--

DROP TABLE IF EXISTS `tb_permissions`;
CREATE TABLE IF NOT EXISTS `tb_permissions` (
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `tb_permissions_nom_unique` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_plan_comptable`
--

DROP TABLE IF EXISTS `tb_plan_comptable`;
CREATE TABLE IF NOT EXISTS `tb_plan_comptable` (
  `numero_compte` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `classe_ohada` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_compte` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `niveau` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `type_compte` enum('ACTIF','PASSIF','CHARGE','PRODUIT','MIXTE','HORS_BILAN') COLLATE utf8mb4_unicode_ci NOT NULL,
  `est_mouvementable` tinyint(1) NOT NULL DEFAULT '1',
  `est_actif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`numero_compte`),
  KEY `idx_plan_ohada_classe_num` (`classe_ohada`,`numero_compte`),
  KEY `idx_plan_ohada_parent` (`parent_compte`),
  KEY `idx_plan_ohada_actif` (`est_actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_portefeuilles_agents`
--

DROP TABLE IF EXISTS `tb_portefeuilles_agents`;
CREATE TABLE IF NOT EXISTS `tb_portefeuilles_agents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `agent_matricule` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom_portefeuille` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taux_commission_agent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_port_agent` (`agent_matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_postes`
--

DROP TABLE IF EXISTS `tb_postes`;
CREATE TABLE IF NOT EXISTS `tb_postes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_id` bigint UNSIGNED NOT NULL,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tb_postes_service_id_foreign` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_roles`
--

DROP TABLE IF EXISTS `tb_roles`;
CREATE TABLE IF NOT EXISTS `tb_roles` (
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `tb_roles_nom_unique` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_role_permission`
--

DROP TABLE IF EXISTS `tb_role_permission`;
CREATE TABLE IF NOT EXISTS `tb_role_permission` (
  `role_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`role_code`,`permission_code`),
  KEY `fk_rp_permission` (`permission_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_role_user`
--

DROP TABLE IF EXISTS `tb_role_user`;
CREATE TABLE IF NOT EXISTS `tb_role_user` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `role_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contrainte_user` (`user_id`),
  KEY `contrainte_role` (`role_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_services`
--

DROP TABLE IF EXISTS `tb_services`;
CREATE TABLE IF NOT EXISTS `tb_services` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_taux_echanges`
--

DROP TABLE IF EXISTS `tb_taux_echanges`;
CREATE TABLE IF NOT EXISTS `tb_taux_echanges` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `devise_source` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `devise_destination` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taux` decimal(18,4) NOT NULL,
  `date_application` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_devise_src` (`devise_source`),
  KEY `fk_devise_dest` (`devise_destination`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_transactions`
--

DROP TABLE IF EXISTS `tb_transactions`;
CREATE TABLE IF NOT EXISTS `tb_transactions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `compte_code` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent_matricule` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guichet_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Guichet ayant effectué l''opération',
  `devise_code` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Devise de la transaction (CDF, USD, EUR…)',
  `devise_dest` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `montant_dest` decimal(18,2) DEFAULT NULL,
  `taux_change` decimal(14,6) DEFAULT NULL,
  `observations` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('CONFIRME','ANNULE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CONFIRME',
  `date_operation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` enum('DEPOT','RETRAIT','VIREMENT','REMBOURSEMENT','CHANGE','PAIEMENT') COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant` decimal(18,2) NOT NULL,
  `montant_commission_total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `solde_compte_avant` decimal(18,2) DEFAULT NULL,
  `solde_compte_apres` decimal(18,2) DEFAULT NULL,
  `montant_total_client` decimal(18,2) DEFAULT NULL,
  `montant_net_client` decimal(18,2) DEFAULT NULL,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tb_transactions_reference_unique` (`reference`),
  KEY `tb_transactions_ibfk_1` (`compte_code`),
  KEY `tb_transactions_ibfk_2` (`agent_matricule`),
  KEY `idx_trans_guichet_date` (`guichet_id`,`date_operation`),
  KEY `idx_trans_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_transaction_commissions`
--

DROP TABLE IF EXISTS `tb_transaction_commissions`;
CREATE TABLE IF NOT EXISTS `tb_transaction_commissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint UNSIGNED NOT NULL,
  `commission_rule_id` bigint UNSIGNED DEFAULT NULL,
  `libelle` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `code_operation` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `type_compte` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type_guichet` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `devise_code` char(3) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `code_zone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portefeuille_id` bigint UNSIGNED DEFAULT NULL,
  `mode_calcul` enum('FIXE','POURCENTAGE') COLLATE utf8mb4_general_ci NOT NULL,
  `valeur_snapshot` decimal(18,4) NOT NULL,
  `base_calcul` decimal(18,2) NOT NULL DEFAULT '0.00',
  `montant_commission` decimal(18,2) NOT NULL DEFAULT '0.00',
  `date_application` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `agent_matricule` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guichet_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_trans_comm_tx_date` (`transaction_id`,`date_application`),
  KEY `idx_trans_comm_scope` (`code_operation`,`type_compte`,`type_guichet`),
  KEY `tb_trans_comm_rule_fk` (`commission_rule_id`),
  KEY `tb_trans_comm_portefeuille_fk` (`portefeuille_id`),
  KEY `tb_trans_comm_guichet_fk` (`guichet_id`),
  KEY `tb_trans_comm_zone_fk` (`code_zone`),
  KEY `tb_trans_comm_agent_fk` (`agent_matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_zones`
--

DROP TABLE IF EXISTS `tb_zones`;
CREATE TABLE IF NOT EXISTS `tb_zones` (
  `code_zone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `agent_commercial_matricule` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commune` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code_zone`),
  KEY `tb_zones_ibfk_1` (`agent_commercial_matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `agent_matricule` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `fk_agent_matricule` (`agent_matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `tb_affectations`
--
ALTER TABLE `tb_affectations`
  ADD CONSTRAINT `fk_affectation_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_affectations_agent_matricule_foreign` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_affectations_poste_id_foreign` FOREIGN KEY (`poste_id`) REFERENCES `tb_postes` (`id`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_affectations_portefeuilles`
--
ALTER TABLE `tb_affectations_portefeuilles`
  ADD CONSTRAINT `fk_aff_pf_agent` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_aff_pf_portefeuille` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_affectations_zones`
--
ALTER TABLE `tb_affectations_zones`
  ADD CONSTRAINT `fk_aff_zone_agent` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_caisses_guichets_soldes`
--
ALTER TABLE `tb_caisses_guichets_soldes`
  ADD CONSTRAINT `fk_solde_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solde_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_clients`
--
ALTER TABLE `tb_clients`
  ADD CONSTRAINT `tb_zones_ibfk_11` FOREIGN KEY (`code_zone`) REFERENCES `tb_zones` (`code_zone`);

--
-- Contraintes pour la table `tb_cloture_caisse`
--
ALTER TABLE `tb_cloture_caisse`
  ADD CONSTRAINT `fk_cloture_agent` FOREIGN KEY (`agent_cloturant`) REFERENCES `tb_agents` (`matricule`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cloture_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`),
  ADD CONSTRAINT `fk_cloture_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`),
  ADD CONSTRAINT `tb_cloture_caisse_ibfk_1` FOREIGN KEY (`validateur_matricule`) REFERENCES `tb_agents` (`matricule`);

--
-- Contraintes pour la table `tb_commission_rules`
--
ALTER TABLE `tb_commission_rules`
  ADD CONSTRAINT `fk_comm_rules_agent` FOREIGN KEY (`created_by_agent`) REFERENCES `tb_agents` (`matricule`),
  ADD CONSTRAINT `tb_comm_rules_devise_fk` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_comm_rules_portefeuille_fk` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_comm_rules_zone_fk` FOREIGN KEY (`code_zone`) REFERENCES `tb_zones` (`code_zone`) ON DELETE SET NULL;

--
-- Contraintes pour la table `tb_compta_ecritures`
--
ALTER TABLE `tb_compta_ecritures`
  ADD CONSTRAINT `fk_compta_ecriture_compte` FOREIGN KEY (`numero_compte`) REFERENCES `tb_plan_comptable` (`numero_compte`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_compta_ecriture_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_compta_ecriture_journal` FOREIGN KEY (`journal_id`) REFERENCES `tb_compta_journaux` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_compta_journaux`
--
ALTER TABLE `tb_compta_journaux`
  ADD CONSTRAINT `fk_compta_journal_agent` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_compta_journal_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_compta_journal_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `tb_transactions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_comptes`
--
ALTER TABLE `tb_comptes`
  ADD CONSTRAINT `fk_compte_devise` FOREIGN KEY (`devise`) REFERENCES `tb_devises` (`code_iso`),
  ADD CONSTRAINT `fk_compte_portefeuille` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents` (`id`),
  ADD CONSTRAINT `tb_comptes_ibfk_112` FOREIGN KEY (`client_matricule`) REFERENCES `tb_clients` (`matricule`);

--
-- Contraintes pour la table `tb_credit_analyses`
--
ALTER TABLE `tb_credit_analyses`
  ADD CONSTRAINT `tb_credit_analyses_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tb_credit_audits`
--
ALTER TABLE `tb_credit_audits`
  ADD CONSTRAINT `tb_credit_audits_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tb_credit_deblocages`
--
ALTER TABLE `tb_credit_deblocages`
  ADD CONSTRAINT `tb_credit_deblocages_compte_credit_id_foreign` FOREIGN KEY (`compte_credit_id`) REFERENCES `tb_comptes` (`code_compte`),
  ADD CONSTRAINT `tb_credit_deblocages_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes` (`id`),
  ADD CONSTRAINT `tb_credit_deblocages_guichet_solde_id_foreign` FOREIGN KEY (`guichet_solde_id`) REFERENCES `tb_caisses_guichets_soldes` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `tb_credit_demandes`
--
ALTER TABLE `tb_credit_demandes`
  ADD CONSTRAINT `tb_crd_client_fk` FOREIGN KEY (`client_matricule`) REFERENCES `tb_clients` (`matricule`),
  ADD CONSTRAINT `tb_crd_portefeuille_fk` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents` (`id`),
  ADD CONSTRAINT `tb_crd_zone_fk` FOREIGN KEY (`code_zone`) REFERENCES `tb_zones` (`code_zone`),
  ADD CONSTRAINT `tb_credit_demandes_compte_id_foreign` FOREIGN KEY (`compte_id`) REFERENCES `tb_comptes` (`code_compte`) ON DELETE RESTRICT,
  ADD CONSTRAINT `tb_credit_demandes_portefeuille_id_foreign` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents` (`id`) ON DELETE RESTRICT;

--
-- Contraintes pour la table `tb_credit_echeances`
--
ALTER TABLE `tb_credit_echeances`
  ADD CONSTRAINT `tb_credit_echeances_echeancier_id_foreign` FOREIGN KEY (`echeancier_id`) REFERENCES `tb_credit_echeanciers` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tb_credit_echeanciers`
--
ALTER TABLE `tb_credit_echeanciers`
  ADD CONSTRAINT `tb_credit_echeanciers_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tb_credit_pieces`
--
ALTER TABLE `tb_credit_pieces`
  ADD CONSTRAINT `tb_credit_pieces_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tb_credit_remboursements`
--
ALTER TABLE `tb_credit_remboursements`
  ADD CONSTRAINT `tb_credit_remboursements_compte_id_foreign` FOREIGN KEY (`compte_id`) REFERENCES `tb_comptes` (`code_compte`),
  ADD CONSTRAINT `tb_credit_remboursements_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes` (`id`),
  ADD CONSTRAINT `tb_credit_remboursements_echeance_id_foreign` FOREIGN KEY (`echeance_id`) REFERENCES `tb_credit_echeances` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `tb_credit_validations`
--
ALTER TABLE `tb_credit_validations`
  ADD CONSTRAINT `tb_credit_validations_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tb_demandes_modification`
--
ALTER TABLE `tb_demandes_modification`
  ADD CONSTRAINT `fk_demmod_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_demmod_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `tb_transactions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tb_mouvements_inter_caisses`
--
ALTER TABLE `tb_mouvements_inter_caisses`
  ADD CONSTRAINT `fk_mouv_agent` FOREIGN KEY (`agent_initiateur`) REFERENCES `tb_agents` (`matricule`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mouv_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`),
  ADD CONSTRAINT `fk_mouv_guichet_dest` FOREIGN KEY (`guichet_dest_id`) REFERENCES `tb_caisses_guichets` (`id`),
  ADD CONSTRAINT `fk_mouv_guichet_src` FOREIGN KEY (`guichet_source_id`) REFERENCES `tb_caisses_guichets` (`id`),
  ADD CONSTRAINT `tb_mouvements_inter_caisses_ibfk_1` FOREIGN KEY (`validateur_matricule`) REFERENCES `tb_agents` (`matricule`);

--
-- Contraintes pour la table `tb_portefeuilles_agents`
--
ALTER TABLE `tb_portefeuilles_agents`
  ADD CONSTRAINT `fk_port_agent` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_postes`
--
ALTER TABLE `tb_postes`
  ADD CONSTRAINT `tb_postes_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `tb_services` (`id`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_role_permission`
--
ALTER TABLE `tb_role_permission`
  ADD CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_code`) REFERENCES `tb_permissions` (`code`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_code`) REFERENCES `tb_roles` (`code`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_role_user`
--
ALTER TABLE `tb_role_user`
  ADD CONSTRAINT `contrainte_role` FOREIGN KEY (`role_code`) REFERENCES `tb_roles` (`code`) ON UPDATE CASCADE,
  ADD CONSTRAINT `contrainte_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_taux_echanges`
--
ALTER TABLE `tb_taux_echanges`
  ADD CONSTRAINT `fk_devise_dest` FOREIGN KEY (`devise_destination`) REFERENCES `tb_devises` (`code_iso`),
  ADD CONSTRAINT `fk_devise_src` FOREIGN KEY (`devise_source`) REFERENCES `tb_devises` (`code_iso`);

--
-- Contraintes pour la table `tb_transactions`
--
ALTER TABLE `tb_transactions`
  ADD CONSTRAINT `tb_transactions_guichet_fk` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_transactions_ibfk_1` FOREIGN KEY (`compte_code`) REFERENCES `tb_comptes` (`code_compte`),
  ADD CONSTRAINT `tb_transactions_ibfk_2` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`);

--
-- Contraintes pour la table `tb_transaction_commissions`
--
ALTER TABLE `tb_transaction_commissions`
  ADD CONSTRAINT `tb_trans_comm_agent_fk` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_trans_comm_guichet_fk` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_trans_comm_portefeuille_fk` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_trans_comm_rule_fk` FOREIGN KEY (`commission_rule_id`) REFERENCES `tb_commission_rules` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_trans_comm_tx_fk` FOREIGN KEY (`transaction_id`) REFERENCES `tb_transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_trans_comm_zone_fk` FOREIGN KEY (`code_zone`) REFERENCES `tb_zones` (`code_zone`) ON DELETE SET NULL;

--
-- Contraintes pour la table `tb_zones`
--
ALTER TABLE `tb_zones`
  ADD CONSTRAINT `tb_zones_ibfk_1` FOREIGN KEY (`agent_commercial_matricule`) REFERENCES `tb_agents` (`matricule`);

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_agent_matricule` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
