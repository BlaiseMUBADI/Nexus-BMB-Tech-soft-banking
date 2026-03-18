-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: bdd_nexus_bmb_tech_soft_baking
-- ------------------------------------------------------
-- Server version	8.4.7

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_affectations`
--

DROP TABLE IF EXISTS `tb_affectations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_affectations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_matricule` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `poste_id` bigint unsigned NOT NULL,
  `guichet_id` bigint unsigned DEFAULT NULL COMMENT 'Guichet de caisse affecté (optionnel). NULL = agent hors caisse.',
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `Etat` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tb_affectations_agent_matricule_foreign` (`agent_matricule`),
  KEY `tb_affectations_poste_id_foreign` (`poste_id`),
  KEY `fk_affectation_guichet` (`guichet_id`),
  CONSTRAINT `fk_affectation_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `tb_affectations_agent_matricule_foreign` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `tb_affectations_poste_id_foreign` FOREIGN KEY (`poste_id`) REFERENCES `tb_postes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_agents`
--

DROP TABLE IF EXISTS `tb_agents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_agents` (
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
  PRIMARY KEY (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_caisses_guichets`
--

DROP TABLE IF EXISTS `tb_caisses_guichets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_caisses_guichets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code_guichet` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_guichet` enum('FIXE','MOBILE','CENTRAL') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FIXE',
  `intitule` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut_operationnel` enum('OUVERT','FERME','SUSPENDU','EN_VERIFICATION') COLLATE utf8mb4_unicode_ci DEFAULT 'FERME',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tb_caisses_guichets_code_guichet_unique` (`code_guichet`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_caisses_guichets_soldes`
--

DROP TABLE IF EXISTS `tb_caisses_guichets_soldes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_caisses_guichets_soldes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `guichet_id` bigint unsigned NOT NULL,
  `devise_code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `solde_en_caisse` decimal(18,2) NOT NULL DEFAULT '0.00',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_guichet_devise` (`guichet_id`,`devise_code`),
  KEY `fk_solde_devise` (`devise_code`),
  CONSTRAINT `fk_solde_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_solde_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_clients`
--

DROP TABLE IF EXISTS `tb_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_clients` (
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
  KEY `tb_zones_ibfk_11` (`code_zone`),
  CONSTRAINT `tb_zones_ibfk_11` FOREIGN KEY (`code_zone`) REFERENCES `tb_zones` (`code_zone`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_cloture_caisse`
--

DROP TABLE IF EXISTS `tb_cloture_caisse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_cloture_caisse` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `guichet_id` bigint unsigned NOT NULL,
  `devise_code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `solde_comptable` decimal(18,2) NOT NULL,
  `solde_physique` decimal(18,2) NOT NULL,
  `ecart_caisse` decimal(18,2) NOT NULL,
  `detail_billetage` json DEFAULT NULL,
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
  KEY `validateur_matricule` (`validateur_matricule`),
  CONSTRAINT `fk_cloture_agent` FOREIGN KEY (`agent_cloturant`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_cloture_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cloture_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tb_cloture_caisse_ibfk_1` FOREIGN KEY (`validateur_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_commission_rules`
--

DROP TABLE IF EXISTS `tb_commission_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_commission_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_operation` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TOUS',
  `type_compte` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TOUS',
  `type_guichet` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TOUS',
  `devise_code` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_zone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portefeuille_id` bigint unsigned DEFAULT NULL,
  `montant_min` decimal(18,2) DEFAULT NULL,
  `montant_max` decimal(18,2) DEFAULT NULL,
  `mode_calcul` enum('FIXE','POURCENTAGE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur` decimal(18,4) NOT NULL,
  `priorite` int unsigned NOT NULL DEFAULT '100',
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `est_actif` tinyint(1) NOT NULL DEFAULT '1',
  `observations` text COLLATE utf8mb4_unicode_ci,
  `created_by_agent` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_comm_rules_active_dates` (`est_actif`,`date_debut`,`date_fin`),
  KEY `idx_comm_rules_scope` (`code_operation`,`type_compte`,`type_guichet`),
  KEY `idx_comm_rules_context` (`devise_code`,`code_zone`,`portefeuille_id`),
  KEY `tb_comm_rules_zone_fk` (`code_zone`),
  KEY `tb_comm_rules_portefeuille_fk` (`portefeuille_id`),
  KEY `tb_agent` (`created_by_agent`),
  CONSTRAINT `tb_agent` FOREIGN KEY (`created_by_agent`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tb_comm_rules_devise_fk` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`) ON DELETE SET NULL,
  CONSTRAINT `tb_comm_rules_portefeuille_fk` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tb_comm_rules_zone_fk` FOREIGN KEY (`code_zone`) REFERENCES `tb_zones` (`code_zone`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_compta_ecritures`
--

DROP TABLE IF EXISTS `tb_compta_ecritures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_compta_ecritures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `journal_id` bigint unsigned NOT NULL,
  `numero_compte` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `devise_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `libelle_ligne` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `debit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `ordre` int unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_compta_ecriture_compte_devise` (`numero_compte`,`devise_code`),
  KEY `idx_compta_ecriture_journal` (`journal_id`),
  KEY `fk_compta_ecriture_devise` (`devise_code`),
  CONSTRAINT `fk_compta_ecriture_compte` FOREIGN KEY (`numero_compte`) REFERENCES `tb_plan_comptable` (`numero_compte`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_compta_ecriture_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_compta_ecriture_journal` FOREIGN KEY (`journal_id`) REFERENCES `tb_compta_journaux` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_compta_journaux`
--

DROP TABLE IF EXISTS `tb_compta_journaux`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_compta_journaux` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code_journal` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CAI',
  `reference_piece` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` bigint unsigned DEFAULT NULL,
  `type_piece` enum('OPERATION','ANNULATION','REGULARISATION') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OPERATION',
  `devise_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` enum('COMPTABILISE','ANNULE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'COMPTABILISE',
  `agent_matricule` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_ecriture` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tb_compta_journaux_reference_piece_unique` (`reference_piece`),
  KEY `idx_compta_journal_trans_type` (`transaction_id`,`type_piece`),
  KEY `idx_compta_journal_date_devise` (`date_ecriture`,`devise_code`),
  KEY `fk_compta_journal_agent` (`agent_matricule`),
  KEY `fk_compta_journal_devise` (`devise_code`),
  CONSTRAINT `fk_compta_journal_agent` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_compta_journal_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_compta_journal_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `tb_transactions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_comptes`
--

DROP TABLE IF EXISTS `tb_comptes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_comptes` (
  `code_compte` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_matricule` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `devise` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('CC','RMB','GTC','DAT','EAV') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'CC=Compte Courant, RMB=Remboursement, GTC=Caution, DAT=Dépôt à Terme, EAV=Épargne & Vie',
  `portefeuille_id` bigint unsigned DEFAULT NULL,
  `solde_reel` decimal(18,2) NOT NULL DEFAULT '0.00',
  `solde_bloque` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`code_compte`),
  KEY `fk_compte_devise` (`devise`),
  KEY `fk_compte_portefeuille` (`portefeuille_id`),
  KEY `tb_comptes_ibfk_112` (`client_matricule`),
  CONSTRAINT `fk_compte_devise` FOREIGN KEY (`devise`) REFERENCES `tb_devises` (`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_compte_portefeuille` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tb_comptes_ibfk_112` FOREIGN KEY (`client_matricule`) REFERENCES `tb_clients` (`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_demandes_modification`
--

DROP TABLE IF EXISTS `tb_demandes_modification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_demandes_modification` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint unsigned NOT NULL,
  `reference_operation` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Référence de l''opération initiale',
  `guichet_id` bigint unsigned DEFAULT NULL,
  `compte_code` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Compte client concerné',
  `client_nom` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nom du client (dénormalisation audit)',
  `type_operation` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Type original : DEPOT, RETRAIT...',
  `devise_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ancien_montant` decimal(15,2) DEFAULT NULL COMMENT 'Montant original',
  `anciennes_observations` text COLLATE utf8mb4_unicode_ci COMMENT 'Observations originales',
  `type_demande` enum('MODIFICATION','SUPPRESSION') COLLATE utf8mb4_unicode_ci NOT NULL,
  `agent_matricule` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Guichetier demandeur',
  `motif` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Motif obligatoire de la demande',
  `nouveau_montant` decimal(15,2) DEFAULT NULL COMMENT 'Nouveau montant demandé',
  `nouvelles_observations` text COLLATE utf8mb4_unicode_ci COMMENT 'Nouvelles observations demandées',
  `statut` enum('EN_ATTENTE','APPROUVEE','REJETEE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EN_ATTENTE',
  `superviseur_matricule` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Superviseur ayant traité',
  `commentaire_superviseur` text COLLATE utf8mb4_unicode_ci,
  `traitee_le` timestamp NULL DEFAULT NULL COMMENT 'Date de traitement par le superviseur',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tb_demandes_modification_transaction_id_foreign` (`transaction_id`),
  KEY `tb_demandes_modification_guichet_id_foreign` (`guichet_id`),
  KEY `tb_demandes_modification_statut_created_at_index` (`statut`,`created_at`),
  KEY `tb_demandes_modification_agent_matricule_index` (`agent_matricule`),
  KEY `tb_demandes_modification_superviseur_matricule_index` (`superviseur_matricule`),
  CONSTRAINT `tb_demandes_modification_guichet_id_foreign` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tb_demandes_modification_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `tb_transactions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_devises`
--

DROP TABLE IF EXISTS `tb_devises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_devises` (
  `code_iso` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbole` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `est_reference` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code_iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_mouvements_inter_caisses`
--

DROP TABLE IF EXISTS `tb_mouvements_inter_caisses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_mouvements_inter_caisses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `guichet_source_id` bigint unsigned DEFAULT NULL,
  `guichet_dest_id` bigint unsigned DEFAULT NULL,
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
  KEY `validateur_matricule` (`validateur_matricule`),
  CONSTRAINT `fk_mouv_agent` FOREIGN KEY (`agent_initiateur`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_mouv_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_mouv_guichet_dest` FOREIGN KEY (`guichet_dest_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_mouv_guichet_src` FOREIGN KEY (`guichet_source_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tb_mouvements_inter_caisses_ibfk_1` FOREIGN KEY (`validateur_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_permissions`
--

DROP TABLE IF EXISTS `tb_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_permissions` (
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `tb_permissions_nom_unique` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_plan_comptable`
--

DROP TABLE IF EXISTS `tb_plan_comptable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_plan_comptable` (
  `numero_compte` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `classe_ohada` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_compte` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `niveau` tinyint unsigned NOT NULL DEFAULT '1',
  `type_compte` enum('ACTIF','PASSIF','CHARGE','PRODUIT','MIXTE','HORS_BILAN') COLLATE utf8mb4_unicode_ci NOT NULL,
  `est_mouvementable` tinyint(1) NOT NULL DEFAULT '1',
  `est_actif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`numero_compte`),
  KEY `idx_plan_ohada_classe_num` (`classe_ohada`,`numero_compte`),
  KEY `idx_plan_ohada_parent` (`parent_compte`),
  KEY `idx_plan_ohada_actif` (`est_actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_portefeuilles_agents`
--

DROP TABLE IF EXISTS `tb_portefeuilles_agents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_portefeuilles_agents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_matricule` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_portefeuille` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taux_commission_agent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_port_agent` (`agent_matricule`),
  CONSTRAINT `fk_port_agent` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_postes`
--

DROP TABLE IF EXISTS `tb_postes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_postes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint unsigned NOT NULL,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tb_postes_service_id_foreign` (`service_id`),
  CONSTRAINT `tb_postes_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `tb_services` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_role_permission`
--

DROP TABLE IF EXISTS `tb_role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_role_permission` (
  `role_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`role_code`,`permission_code`),
  KEY `fk_rp_permission` (`permission_code`),
  CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_code`) REFERENCES `tb_permissions` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_code`) REFERENCES `tb_roles` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_role_user`
--

DROP TABLE IF EXISTS `tb_role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_role_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `role_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contrainte_user` (`user_id`),
  KEY `contrainte_role` (`role_code`),
  CONSTRAINT `contrainte_role` FOREIGN KEY (`role_code`) REFERENCES `tb_roles` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `contrainte_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_roles`
--

DROP TABLE IF EXISTS `tb_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_roles` (
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `tb_roles_nom_unique` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_services`
--

DROP TABLE IF EXISTS `tb_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_taux_echanges`
--

DROP TABLE IF EXISTS `tb_taux_echanges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_taux_echanges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `devise_source` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `devise_destination` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taux` decimal(18,4) NOT NULL,
  `date_application` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_devise_src` (`devise_source`),
  KEY `fk_devise_dest` (`devise_destination`),
  CONSTRAINT `fk_devise_dest` FOREIGN KEY (`devise_destination`) REFERENCES `tb_devises` (`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_devise_src` FOREIGN KEY (`devise_source`) REFERENCES `tb_devises` (`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_transaction_commissions`
--

DROP TABLE IF EXISTS `tb_transaction_commissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_transaction_commissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint unsigned NOT NULL,
  `commission_rule_id` bigint unsigned DEFAULT NULL,
  `libelle` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_operation` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_compte` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_guichet` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `devise_code` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_zone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portefeuille_id` bigint unsigned DEFAULT NULL,
  `mode_calcul` enum('FIXE','POURCENTAGE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur_snapshot` decimal(18,4) NOT NULL,
  `base_calcul` decimal(18,2) NOT NULL DEFAULT '0.00',
  `montant_commission` decimal(18,2) NOT NULL DEFAULT '0.00',
  `date_application` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `agent_matricule` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guichet_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_trans_comm_tx_date` (`transaction_id`,`date_application`),
  KEY `idx_trans_comm_scope` (`code_operation`,`type_compte`,`type_guichet`),
  KEY `tb_trans_comm_rule_fk` (`commission_rule_id`),
  KEY `tb_trans_comm_zone_fk` (`code_zone`),
  KEY `tb_trans_comm_portefeuille_fk` (`portefeuille_id`),
  KEY `tb_trans_comm_guichet_fk` (`guichet_id`),
  KEY `tb_trans_comm_agent_fk` (`agent_matricule`),
  CONSTRAINT `tb_trans_comm_agent_fk` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE SET NULL,
  CONSTRAINT `tb_trans_comm_guichet_fk` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tb_trans_comm_portefeuille_fk` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tb_trans_comm_rule_fk` FOREIGN KEY (`commission_rule_id`) REFERENCES `tb_commission_rules` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tb_trans_comm_tx_fk` FOREIGN KEY (`transaction_id`) REFERENCES `tb_transactions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tb_trans_comm_zone_fk` FOREIGN KEY (`code_zone`) REFERENCES `tb_zones` (`code_zone`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_transactions`
--

DROP TABLE IF EXISTS `tb_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `compte_code` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent_matricule` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guichet_id` bigint unsigned DEFAULT NULL COMMENT 'Guichet ayant effectué l''opération',
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
  KEY `idx_trans_statut` (`statut`),
  CONSTRAINT `tb_transactions_guichet_fk` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tb_transactions_ibfk_1` FOREIGN KEY (`compte_code`) REFERENCES `tb_comptes` (`code_compte`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tb_transactions_ibfk_2` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_zones`
--

DROP TABLE IF EXISTS `tb_zones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_zones` (
  `code_zone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `agent_commercial_matricule` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commune` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code_zone`),
  KEY `tb_zones_ibfk_1` (`agent_commercial_matricule`),
  CONSTRAINT `tb_zones_ibfk_1` FOREIGN KEY (`agent_commercial_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
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
  KEY `fk_agent_matricule` (`agent_matricule`),
  CONSTRAINT `fk_agent_matricule` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'bdd_nexus_bmb_tech_soft_baking'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-15 11:19:02
