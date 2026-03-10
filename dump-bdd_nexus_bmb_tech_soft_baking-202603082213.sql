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
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026_03_05_000001_laravel_core',1),(2,'2026_03_05_000002_rh_banque_core',1),(3,'2026_03_05_000003_caisse_guichet',1),(4,'2026_03_05_000004_add_guichet_to_affectations',1),(5,'2026_03_05_000005_seed_test_users',2),(7,'2026_03_06_000006_permissions_banque_complete',3),(8,'2026_03_07_103903_add_statut_observations_to_mouvements_inter_caisses',4),(9,'2026_03_07_105258_add_type_guichet_and_coffre_central',5),(10,'2026_03_07_210000_tresorerie_permissions_and_user',6),(11,'2026_03_07_220001_add_demande_appro_to_type_flux',7),(12,'2026_03_07_235000_add_motif_statut_to_cloture_caisse',8),(13,'2026_03_08_000001_add_en_verification_and_cloture_validation',9),(14,'2026_03_07_145751_add_timestamps_to_tb_caisses_guichets',10),(16,'2026_03_08_110000_extend_tb_transactions_for_guichet',11),(17,'2026_03_08_120000_drop_client_columns_from_tb_transactions',12);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('mMgDTemzd6APpvVEFpuVeD3pGliyyl9qoUaCnxZ5',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTkFuR3RrUXdYZzBHSVhKSEs3cThZRk5QdjV3aFMyNWpiUVVLeDN4eiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTc6Imh0dHA6Ly9sb2NhbGhvc3QvTmV4dXMtQk1CLVRlY2gtc29mdC1iYW5raW5nL3B1YmxpYy9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=',1773001768),('qh1yH13X5YHrYcmSHSWM35cNvO8DIjOHRb4c4PNe',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiREFiVVpGV3NPdEtqWWdNdTBuSkNrbHh5SERjMGdUTjBFeHhnNGFheSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjM6Imh0dHA6Ly9sb2NhbGhvc3QvTmV4dXMtQk1CLVRlY2gtc29mdC1iYW5raW5nL3B1YmxpYy9yaC9zZXJ2aWNlcyI7czo1OiJyb3V0ZSI7czoxNDoic2VydmljZXMuaW5kZXgiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1773004084);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_affectations`
--

LOCK TABLES `tb_affectations` WRITE;
/*!40000 ALTER TABLE `tb_affectations` DISABLE KEYS */;
INSERT INTO `tb_affectations` VALUES (6,'AG-EBENKGA-26-00005',2,10,'2026-03-06',NULL,'ACTIF','2026-03-06 22:17:45','2026-03-07 09:13:49'),(8,'AG-EBENKGA-26-00001',2,11,'2026-03-07',NULL,'ACTIF','2026-03-07 09:14:52','2026-03-07 09:14:52'),(9,'AG-EBENKGA-26-00004',4,NULL,'2026-03-07',NULL,'ACTIF','2026-03-07 10:32:25','2026-03-07 10:32:25'),(10,'AG-EBENKGA-26-00002',2,14,'2026-03-07',NULL,'ACTIF','2026-03-07 12:42:48','2026-03-07 12:42:48');
/*!40000 ALTER TABLE `tb_affectations` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `tb_agents`
--

LOCK TABLES `tb_agents` WRITE;
/*!40000 ALTER TABLE `tb_agents` DISABLE KEYS */;
INSERT INTO `tb_agents` VALUES ('AG-EBENKGA-26-00001','BMB','ADMIN','Système','M',NULL,NULL,'bmb@bmb.cd',NULL,NULL,'2026-03-06','actif','2026-03-06 09:09:57','2026-03-06 09:09:57'),('AG-EBENKGA-26-00002','MULUMBA',NULL,'Jean','M','1990-06-15','+243810000002','jean.caissier@bmb.cd','Kinshasa',NULL,'2026-03-06','actif','2026-03-06 11:11:41','2026-03-06 11:11:41'),('AG-EBENKGA-26-00003','KASONGO',NULL,'Marie','F','1992-03-20','+243810000003','marie.rh@bmb.cd','Kinshasa',NULL,'2026-03-06','actif','2026-03-06 11:11:41','2026-03-06 11:11:41'),('AG-EBENKGA-26-00004','MUBADI','BAKAJIKA','Blaise','M','2026-03-04','+24399999555','blaisemubadibakajika@gmail.com','Nganza','agents/1772808566_whatsapp-image-2024-02-23-a-222610-394d5d2cjpg','2026-03-05','actif','2026-03-06 13:49:26','2026-03-06 13:49:26'),('AG-EBENKGA-26-00005','MPUTU','TUDIKOLELE','Nice','F','2026-03-04','+24399999555','mputu@gmail.com','Nganza','agents/1772808651_nicejpg','2026-03-05','actif','2026-03-06 13:50:51','2026-03-06 13:50:51'),('AG-EBENKGA-26-00006','KALALA','KALALA','Junior','M','2026-03-05','+24399999555','kalala@gmail.com','Nganza','agents/1772808737_1767056067186jpg','2026-03-05','actif','2026-03-06 13:52:17','2026-03-06 13:52:17');
/*!40000 ALTER TABLE `tb_agents` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_caisses_guichets`
--

LOCK TABLES `tb_caisses_guichets` WRITE;
/*!40000 ALTER TABLE `tb_caisses_guichets` DISABLE KEYS */;
INSERT INTO `tb_caisses_guichets` VALUES (1,'COFFRE_01','CENTRAL','Coffre-Fort Central EBEN','OUVERT','2026-03-07 10:02:55','2026-03-07 10:02:55'),(10,'G01','FIXE','Principale','FERME','2026-03-06 22:05:29','2026-03-06 22:05:29'),(11,'G02','FIXE','Guichet ndesha','FERME','2026-03-06 22:13:27','2026-03-08 17:15:52'),(14,'G03','FIXE','Guichet Kananga 1','OUVERT','2026-03-07 12:29:08','2026-03-08 17:34:28'),(17,'G04','MOBILE','Guichet Batetela 1','FERME','2026-03-07 20:47:15','2026-03-07 20:47:15');
/*!40000 ALTER TABLE `tb_caisses_guichets` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_caisses_guichets_soldes`
--

LOCK TABLES `tb_caisses_guichets_soldes` WRITE;
/*!40000 ALTER TABLE `tb_caisses_guichets_soldes` DISABLE KEYS */;
INSERT INTO `tb_caisses_guichets_soldes` VALUES (11,10,'CDF',0.00,'2026-03-06 23:05:29'),(12,11,'CDF',0.00,'2026-03-06 23:13:27'),(13,11,'USD',0.00,'2026-03-08 18:15:52'),(15,1,'CDF',11999999950000.00,'2026-03-08 18:36:06'),(16,1,'EUR',10000000000.00,'2026-03-07 11:52:34'),(17,1,'USD',10100888590000.00,'2026-03-08 18:15:52'),(18,14,'CDF',59500.00,'2026-03-08 20:10:40'),(23,17,'CDF',0.00,'2026-03-07 21:47:15'),(24,17,'USD',0.00,'2026-03-07 21:47:15');
/*!40000 ALTER TABLE `tb_caisses_guichets_soldes` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `tb_clients`
--

LOCK TABLES `tb_clients` WRITE;
/*!40000 ALTER TABLE `tb_clients` DISABLE KEYS */;
INSERT INTO `tb_clients` VALUES ('CL-EBENKGA-26-00001','ZON-EBENKGA-26-00001','MUKENDI','MALUNDA','Cédrick','mukende@gmail.com','25544','M','2026-02-26','Kananga','Nganza','Célibataire',NULL,'Carte nationale d\'identité','Kananga','2026-03-11','+255555','clients/1772872385_recharger-telephone-intelligent_16734-98 (1).jpeg','Enseignement','Commerce','Nexus BMB Techn','Kamayi','+2453333','Privé','3',500.00,NULL,NULL,'2026-03-07 07:33:05','2026-03-07 07:33:05'),('CL-EBENKGA-26-00002','ZON-EBENKGA-26-00002','TSHIAMALA','NTAMBUE','Jack','tshiamal@gmail.com','25544','M','2026-02-26','Kananga','Nganza','Marié','KANKU Marie','Carte nationale d\'identité','Kananga','2026-03-11','+255555','clients/1772872478_recharger-telephone-intelligent_16734-98 (1).png','Enseignement','Commerce','Nexus BMB Techn','Kamayi','+2453333','Privé','3',500.00,NULL,NULL,'2026-03-07 07:34:38','2026-03-07 07:34:38');
/*!40000 ALTER TABLE `tb_clients` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_cloture_caisse`
--

LOCK TABLES `tb_cloture_caisse` WRITE;
/*!40000 ALTER TABLE `tb_cloture_caisse` DISABLE KEYS */;
INSERT INTO `tb_cloture_caisse` VALUES (1,14,'CDF',1000000.00,1000000.00,0.00,'{\"5000\": 80, \"10000\": 20, \"20000\": 20}',NULL,'EQUILIBRE','VALIDE','AG-EBENKGA-26-00001','2026-03-07 13:58:25',NULL,'AG-EBENKGA-26-00002','2026-03-07 13:51:31'),(2,14,'CDF',5000.00,5000.00,0.00,'{\"5000\": 1}',NULL,'EQUILIBRE','REJETE','AG-EBENKGA-26-00001','2026-03-07 14:08:52','Ecart non justifié','AG-EBENKGA-26-00002','2026-03-07 14:07:39'),(3,14,'CDF',5000.00,5000.00,0.00,'{\"5000\": 1}',NULL,'EQUILIBRE','VALIDE','AG-EBENKGA-26-00001','2026-03-07 14:11:54',NULL,'AG-EBENKGA-26-00002','2026-03-07 14:11:39'),(4,11,'CDF',0.00,0.00,0.00,'[]',NULL,'EQUILIBRE','VALIDE','AG-EBENKGA-26-00001','2026-03-08 17:15:43',NULL,'AG-EBENKGA-26-00001','2026-03-07 20:48:17'),(5,11,'USD',500.00,500.00,0.00,'{\"100\": 5}',NULL,'EQUILIBRE','VALIDE','AG-EBENKGA-26-00001','2026-03-08 17:15:52',NULL,'AG-EBENKGA-26-00001','2026-03-07 20:48:17'),(6,14,'CDF',65000.00,65000.00,0.00,'{\"5000\": 1, \"10000\": 1, \"50000\": 1}',NULL,'EQUILIBRE','VALIDE','AG-EBENKGA-26-00001','2026-03-07 20:52:55',NULL,'AG-EBENKGA-26-00002','2026-03-07 20:52:30'),(7,14,'CDF',0.00,0.00,0.00,'[]',NULL,'EQUILIBRE','VALIDE','AG-EBENKGA-26-00001','2026-03-07 21:05:00',NULL,'AG-EBENKGA-26-00002','2026-03-07 21:04:45'),(8,14,'CDF',5000.00,5000.00,0.00,'{\"5000\": 1}',NULL,'EQUILIBRE','VALIDE','AG-EBENKGA-26-00001','2026-03-08 17:27:07',NULL,'AG-EBENKGA-26-00002','2026-03-08 17:26:38');
/*!40000 ALTER TABLE `tb_cloture_caisse` ENABLE KEYS */;
UNLOCK TABLES;

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
  `type` enum('COURANT','EPARGNE_LIBRE','EPARGNE_BLOQUEE','CAUTION_CREDIT') COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Dumping data for table `tb_comptes`
--

LOCK TABLES `tb_comptes` WRITE;
/*!40000 ALTER TABLE `tb_comptes` DISABLE KEYS */;
INSERT INTO `tb_comptes` VALUES ('CMPT-EBENKGA-26-6ZT1-00003','CL-EBENKGA-26-00001','USD','COURANT',NULL,0.00,0.00,'2026-03-07 07:43:15','2026-03-07 08:43:15'),('CMPT-EBENKGA-26-GWGR-00001','CL-EBENKGA-26-00001','CDF','CAUTION_CREDIT',1,9500.00,0.00,'2026-03-07 07:41:50','2026-03-08 20:10:40'),('CMPT-EBENKGA-26-VM3R-00002','CL-EBENKGA-26-00001','CDF','COURANT',NULL,0.00,0.00,'2026-03-07 07:40:43','2026-03-07 08:40:43');
/*!40000 ALTER TABLE `tb_comptes` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `tb_devises`
--

LOCK TABLES `tb_devises` WRITE;
/*!40000 ALTER TABLE `tb_devises` DISABLE KEYS */;
INSERT INTO `tb_devises` VALUES ('CDF','Franc Congolais','Fc',1,'2026-03-06 09:09:58',NULL),('EUR','Euro','€',0,'2026-03-06 09:09:58',NULL),('USD','Dollar Américain','$',0,'2026-03-06 09:09:58',NULL);
/*!40000 ALTER TABLE `tb_devises` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_mouvements_inter_caisses`
--

LOCK TABLES `tb_mouvements_inter_caisses` WRITE;
/*!40000 ALTER TABLE `tb_mouvements_inter_caisses` DISABLE KEYS */;
INSERT INTO `tb_mouvements_inter_caisses` VALUES (1,1,11,'AG-EBENKGA-26-00001','DEMANDE_APPRO',500.00,'USD','REQ-20260307-131603-AG-E','2026-03-07 12:16:03','CONFIRME','AG-EBENKGA-26-00001','Sole démarrage | Approuvé'),(2,1,14,'AG-EBENKGA-26-00002','DEMANDE_APPRO',500000.00,'CDF','REQ-20260307-134352-AG-E','2026-03-07 12:43:52','CONFIRME','AG-EBENKGA-26-00001','Fond de demarrage guichet | Approuvé'),(3,1,14,'AG-EBENKGA-26-00002','DEMANDE_APPRO',500000.00,'CDF','REQ-20260307-140643-AG-E','2026-03-07 13:06:43','CONFIRME','AG-EBENKGA-26-00001','Approuvé'),(4,14,1,'AG-EBENKGA-26-00001','DEGAGEMENT',1000000.00,'CDF','DEG-20260307-145825-G03','2026-03-07 13:58:25','CONFIRME','AG-EBENKGA-26-00001','Dégagement automatique clôture G03'),(5,1,14,'AG-EBENKGA-26-00002','DEMANDE_APPRO',5000.00,'CDF','REQ-20260307-150115-AG-E','2026-03-07 14:01:15','CONFIRME','AG-EBENKGA-26-00001','Fonds de démarrage | Approuvé'),(6,14,1,'AG-EBENKGA-26-00001','DEGAGEMENT',5000.00,'CDF','DEG-20260307-151154-G03','2026-03-07 14:11:54','CONFIRME','AG-EBENKGA-26-00001','Dégagement automatique clôture G03'),(7,1,14,'AG-EBENKGA-26-00002','DEMANDE_APPRO',5000.00,'CDF','REQ-20260307-151641-AG-E','2026-03-07 14:16:41','CONFIRME','AG-EBENKGA-26-00001','Approuvé'),(8,1,14,'AG-EBENKGA-26-00002','DEMANDE_APPRO',60000.00,'CDF','REQ-20260307-152531-AG-E','2026-03-07 14:25:31','CONFIRME','AG-EBENKGA-26-00001','Approuvé'),(17,14,1,'AG-EBENKGA-26-00001','DEGAGEMENT',65000.00,'CDF','DEG-20260307-215255-G03','2026-03-07 20:52:55','CONFIRME','AG-EBENKGA-26-00001','Dégagement automatique clôture G03'),(20,14,1,'AG-EBENKGA-26-00001','DEGAGEMENT',0.00,'CDF','DEG-20260307-220500-G03','2026-03-07 21:05:00','CONFIRME','AG-EBENKGA-26-00001','Dégagement automatique clôture G03'),(25,11,1,'AG-EBENKGA-26-00001','DEGAGEMENT',0.00,'CDF','DEG-20260308-181543-G02-CDF','2026-03-08 17:15:43','CONFIRME','AG-EBENKGA-26-00001','Dégagement CDF — clôture G02'),(26,11,1,'AG-EBENKGA-26-00001','DEGAGEMENT',500.00,'USD','DEG-20260308-181552-G02-USD','2026-03-08 17:15:52','CONFIRME','AG-EBENKGA-26-00001','Dégagement USD — clôture G02'),(27,1,14,'AG-EBENKGA-26-00002','DEMANDE_APPRO',5000.00,'CDF','REQ-20260308-182517-AG-E','2026-03-08 17:25:17','CONFIRME','AG-EBENKGA-26-00001','Fond de démarrage de guichet | Approuvé'),(28,14,1,'AG-EBENKGA-26-00001','DEGAGEMENT',5000.00,'CDF','DEG-20260308-182707-G03-CDF','2026-03-08 17:27:07','CONFIRME','AG-EBENKGA-26-00001','Dégagement CDF — clôture G03'),(29,NULL,14,'AG-EBENKGA-26-00002','DEMANDE_APPRO',50000.00,'CDF','REQ-20260308-183444-AG-E','2026-03-08 17:34:44','ANNULE','AG-EBENKGA-26-00001','Rejeté : Tu as encore l\'argent dans la caisse'),(30,1,14,'AG-EBENKGA-26-00002','DEMANDE_APPRO',50000.00,'CDF','REQ-20260308-183557-AG-E','2026-03-08 17:35:57','CONFIRME','AG-EBENKGA-26-00001','Approuvé'),(31,NULL,14,'AG-EBENKGA-26-00002','DEMANDE_APPRO',1500.00,'CDF','REQ-20260308-200955-AG-E','2026-03-08 19:09:55','ANNULE','AG-EBENKGA-26-00001','Rejeté : Tu as d\'autre argent');
/*!40000 ALTER TABLE `tb_mouvements_inter_caisses` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `tb_permissions`
--

LOCK TABLES `tb_permissions` WRITE;
/*!40000 ALTER TABLE `tb_permissions` DISABLE KEYS */;
INSERT INTO `tb_permissions` VALUES ('EBEN-PER1','Accès Administration','Accès au panneau d\'administration','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER10','Voir caisse','Consultation des caisses','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER11','Ouvrir caisse','Ouverture d\'une caisse/guichet','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER12','Fermer caisse','Fermeture d\'une caisse/guichet','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER13','Mouvements caisse','Enregistrement des mouvements','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER14','Clôture caisse','Clôture journalière caisse','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER15','Voir clients','Consultation des clients','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER16','Créer client','Enregistrement d\'un client','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER17','Modifier client','Modification d\'un client','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER18','Voir comptes','Consultation des comptes','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER19','Créer compte','Ouverture d\'un compte','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER2','Voir les rôles','Consultation des rôles','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER20','Voir devises','Consultation des devises','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER21','Gérer devises','Gestion des devises et taux','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER22','Effectuer dépôts','Saisir un dépôt sur un compte client','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER23','Effectuer retraits','Saisir un retrait sur un compte client','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER24','Effectuer virements','Initier un virement entre comptes','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER25','Annuler transactions','Annuler ou reverser une opération bancaire','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER26','Valider transactions','Approuver les opérations en attente (double validation)','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER27','Voir produits épargne','Consulter les produits d\'épargne disponibles','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER28','Gérer produits épargne','Créer et modifier les produits d\'épargne','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER29','Gérer comptes épargne','Ouvrir, alimenter et clôturer des comptes épargne','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER3','Gérer les rôles','Création et modification des rôles','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER30','Voir crédits','Consulter les dossiers de crédit','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER31','Soumettre demande crédit','Créer une demande de prêt pour un client','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER32','Instruire dossier crédit','Analyser et compléter un dossier de crédit','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER33','Approuver crédit','Accorder ou rejeter un crédit (niveau comité)','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER34','Gérer remboursements','Saisir les échéances et paiements de remboursement','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER35','Clôturer crédit','Marquer un crédit comme soldé ou en contentieux','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER36','Voir rapports opérationnels','Rapports journaliers caisse et transactions','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER37','Voir rapports financiers','Bilan, compte de résultat, situation financière','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER38','Exporter rapports','Exporter ou imprimer tous les rapports en PDF/Excel','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER39','Voir journal comptable','Consulter les écritures du journal comptable','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER4','Voir les permissions','Consultation des permissions','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER40','Saisir écritures','Créer des écritures comptables manuelles','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER41','Valider écritures','Approuver et lettrer les écritures comptables','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER42','Voir journal d\'activité','Logs d\'audit : qui a fait quoi et quand','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER43','Gérer paramètres sécurité','Politique mots de passe, tentatives login, blocages','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER44','Voir trésorerie','Accès au module trésorerie/coffre-fort (vue d\'ensemble et soldes)','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-PER45','Approvisionner coffre','Enregistrer un approvisionnement externe (banque → coffre)','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-PER46','Valider mouvements trésorerie','Approuver / rejeter les opérations coffre-fort en attente','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-PER47','Alimenter guichets','Transférer des fonds entre le coffre central et les guichets','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-PER48','Journal trésorerie','Consulter le journal complet de la caisse centrale (historique)','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-PER5','Gérer les permissions','Gestion des permissions','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER6','Voir RH','Accès au module RH','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER7','Créer agent','Création d\'un nouvel agent','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER8','Modifier agent','Modification d\'un agent','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-PER9','Affectations','Gestion des affectations','2026-03-06 12:02:52','2026-03-06 12:02:52');
/*!40000 ALTER TABLE `tb_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_plan_comptable`
--

DROP TABLE IF EXISTS `tb_plan_comptable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_plan_comptable` (
  `numero_compte` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_compte` enum('ACTIF','PASSIF','CHARGE','PRODUIT') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`numero_compte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_plan_comptable`
--

LOCK TABLES `tb_plan_comptable` WRITE;
/*!40000 ALTER TABLE `tb_plan_comptable` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_plan_comptable` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `tb_portefeuilles_agents`
--

LOCK TABLES `tb_portefeuilles_agents` WRITE;
/*!40000 ALTER TABLE `tb_portefeuilles_agents` DISABLE KEYS */;
INSERT INTO `tb_portefeuilles_agents` VALUES (1,'AG-EBENKGA-26-00002','Portefeuille',5.00,'2026-03-07 07:48:58');
/*!40000 ALTER TABLE `tb_portefeuilles_agents` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `tb_postes`
--

LOCK TABLES `tb_postes` WRITE;
/*!40000 ALTER TABLE `tb_postes` DISABLE KEYS */;
INSERT INTO `tb_postes` VALUES (1,1,'Administrateur Système','Poste réservé au compte administrateur du système','2026-03-06 09:09:57','2026-03-06 09:09:57'),(2,3,'Guichitier',NULL,'2026-03-06 13:54:14','2026-03-06 13:54:14'),(4,3,'Chef Trésorier','Responsable du coffre-fort central et des flux inter-caisses','2026-03-07 10:32:25','2026-03-07 10:32:25');
/*!40000 ALTER TABLE `tb_postes` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `tb_role_permission`
--

LOCK TABLES `tb_role_permission` WRITE;
/*!40000 ALTER TABLE `tb_role_permission` DISABLE KEYS */;
INSERT INTO `tb_role_permission` VALUES ('EBEN-ROL1','EBEN-PER1','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER10','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER11','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER12','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER13','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER14','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER15','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER16','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER17','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER18','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER19','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER2','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER20','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER21','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER22','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER23','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER24','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER25','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER26','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER27','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER28','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER29','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER3','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER30','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER31','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER32','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER33','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER34','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER35','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER36','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER37','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER38','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER39','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER4','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER40','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER41','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER42','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER43','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER44','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL1','EBEN-PER45','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL1','EBEN-PER46','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL1','EBEN-PER47','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL1','EBEN-PER48','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL1','EBEN-PER5','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER6','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER7','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER8','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL1','EBEN-PER9','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL2','EBEN-PER10','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL2','EBEN-PER11','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL2','EBEN-PER12','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL2','EBEN-PER13','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL2','EBEN-PER14','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL2','EBEN-PER15','2026-03-06 12:42:55','2026-03-06 12:42:55'),('EBEN-ROL2','EBEN-PER18','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL2','EBEN-PER20','2026-03-06 12:42:08','2026-03-06 12:42:08'),('EBEN-ROL2','EBEN-PER21','2026-03-06 12:42:08','2026-03-06 12:42:08'),('EBEN-ROL2','EBEN-PER22','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL2','EBEN-PER23','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL2','EBEN-PER24','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL2','EBEN-PER25','2026-03-06 12:42:08','2026-03-06 12:42:08'),('EBEN-ROL2','EBEN-PER26','2026-03-06 12:42:08','2026-03-06 12:42:08'),('EBEN-ROL2','EBEN-PER27','2026-03-06 12:42:08','2026-03-06 12:42:08'),('EBEN-ROL2','EBEN-PER28','2026-03-06 12:42:08','2026-03-06 12:42:08'),('EBEN-ROL2','EBEN-PER29','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL2','EBEN-PER30','2026-03-06 12:42:08','2026-03-06 12:42:08'),('EBEN-ROL2','EBEN-PER31','2026-03-06 12:42:08','2026-03-06 12:42:08'),('EBEN-ROL2','EBEN-PER32','2026-03-06 12:42:08','2026-03-06 12:42:08'),('EBEN-ROL2','EBEN-PER33','2026-03-06 12:42:08','2026-03-06 12:42:08'),('EBEN-ROL2','EBEN-PER34','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL2','EBEN-PER35','2026-03-06 12:42:08','2026-03-06 12:42:08'),('EBEN-ROL2','EBEN-PER36','2026-03-06 12:42:08','2026-03-06 12:42:08'),('EBEN-ROL2','EBEN-PER37','2026-03-06 12:42:08','2026-03-06 12:42:08'),('EBEN-ROL2','EBEN-PER38','2026-03-06 12:42:08','2026-03-06 12:42:08'),('EBEN-ROL3','EBEN-PER1','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER10','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER15','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER18','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER2','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER20','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER26','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER27','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER30','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER33','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER36','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER37','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER38','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER39','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER4','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER42','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL3','EBEN-PER44','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL3','EBEN-PER48','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL3','EBEN-PER6','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL4','EBEN-PER6','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL4','EBEN-PER7','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL4','EBEN-PER8','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL4','EBEN-PER9','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL5','EBEN-PER10','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL5','EBEN-PER15','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL5','EBEN-PER18','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL5','EBEN-PER2','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL5','EBEN-PER20','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL5','EBEN-PER26','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL5','EBEN-PER27','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL5','EBEN-PER30','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL5','EBEN-PER36','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL5','EBEN-PER37','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL5','EBEN-PER38','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL5','EBEN-PER42','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL5','EBEN-PER44','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL5','EBEN-PER48','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL5','EBEN-PER6','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL6','EBEN-PER15','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL6','EBEN-PER16','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL6','EBEN-PER18','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL6','EBEN-PER19','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL6','EBEN-PER27','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL6','EBEN-PER28','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL6','EBEN-PER29','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL6','EBEN-PER30','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL6','EBEN-PER31','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL6','EBEN-PER32','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL6','EBEN-PER34','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL6','EBEN-PER35','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL7','EBEN-PER18','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL7','EBEN-PER37','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL7','EBEN-PER38','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL7','EBEN-PER39','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL7','EBEN-PER40','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL7','EBEN-PER41','2026-03-06 12:02:52','2026-03-06 12:02:52'),('EBEN-ROL8','EBEN-PER10','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL8','EBEN-PER15','2026-03-08 20:06:05','2026-03-08 20:06:05'),('EBEN-ROL8','EBEN-PER20','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL8','EBEN-PER36','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL8','EBEN-PER37','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL8','EBEN-PER38','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL8','EBEN-PER44','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL8','EBEN-PER45','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL8','EBEN-PER46','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL8','EBEN-PER47','2026-03-07 10:32:25','2026-03-07 10:32:25'),('EBEN-ROL8','EBEN-PER48','2026-03-07 10:32:25','2026-03-07 10:32:25');
/*!40000 ALTER TABLE `tb_role_permission` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_role_user`
--

LOCK TABLES `tb_role_user` WRITE;
/*!40000 ALTER TABLE `tb_role_user` DISABLE KEYS */;
INSERT INTO `tb_role_user` VALUES (1,1,'EBEN-ROL1','2026-03-06 09:09:58','2026-03-06 09:09:58'),(2,2,'EBEN-ROL2','2026-03-06 11:11:41','2026-03-06 11:11:41'),(4,2,'EBEN-ROL2','2026-03-06 11:36:28','2026-03-06 11:36:28'),(6,3,'EBEN-ROL4',NULL,NULL),(7,7,'EBEN-ROL8','2026-03-07 10:32:25','2026-03-07 10:32:25');
/*!40000 ALTER TABLE `tb_role_user` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `tb_roles`
--

LOCK TABLES `tb_roles` WRITE;
/*!40000 ALTER TABLE `tb_roles` DISABLE KEYS */;
INSERT INTO `tb_roles` VALUES ('EBEN-ROL1','Administrateur','Accès total au système','2026-03-06 09:09:57','2026-03-06 09:09:57'),('EBEN-ROL2','Caissier','Gestion caisse et guichet','2026-03-06 09:09:57','2026-03-06 09:09:57'),('EBEN-ROL3','Directeur','Supervision générale','2026-03-06 09:09:57','2026-03-06 09:09:57'),('EBEN-ROL4','Agent RH','Gestion des ressources humaines','2026-03-06 09:09:57','2026-03-06 09:09:57'),('EBEN-ROL5','Superviseur','Supervision opérationnelle','2026-03-06 09:09:57','2026-03-06 09:09:57'),('EBEN-ROL6','Chargé de crédit','Gestion complète des dossiers crédit, épargne et comptes clients','2026-03-06 11:36:28','2026-03-06 11:36:28'),('EBEN-ROL7','Comptable','Comptabilité, rapports financiers, validation des écritures','2026-03-06 11:36:28','2026-03-06 11:36:28'),('EBEN-ROL8','Trésorier','Gestion complète du coffre-fort central, approvisionnements et transferts','2026-03-07 10:32:25','2026-03-07 10:32:25');
/*!40000 ALTER TABLE `tb_roles` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `tb_services`
--

LOCK TABLES `tb_services` WRITE;
/*!40000 ALTER TABLE `tb_services` DISABLE KEYS */;
INSERT INTO `tb_services` VALUES (1,'Direction Générale','Administration centrale du système','2026-03-06 09:09:57','2026-03-06 09:09:57'),(3,'Caisse',NULL,'2026-03-06 13:53:33','2026-03-06 13:53:33');
/*!40000 ALTER TABLE `tb_services` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `tb_taux_echanges`
--

LOCK TABLES `tb_taux_echanges` WRITE;
/*!40000 ALTER TABLE `tb_taux_echanges` DISABLE KEYS */;
INSERT INTO `tb_taux_echanges` VALUES (3,'USD','CDF',2500.0000,'2026-03-07 08:02:30','2026-03-07 07:02:30','2026-03-07 07:02:30'),(4,'CDF','USD',0.0004,'2026-03-07 08:02:30','2026-03-07 07:02:30','2026-03-07 07:02:30');
/*!40000 ALTER TABLE `tb_taux_echanges` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_transactions`
--

LOCK TABLES `tb_transactions` WRITE;
/*!40000 ALTER TABLE `tb_transactions` DISABLE KEYS */;
INSERT INTO `tb_transactions` VALUES (1,'CMPT-EBENKGA-26-GWGR-00001','AG-EBENKGA-26-00002',14,'CDF',NULL,NULL,NULL,'Dépot','CONFIRME','2026-03-08 18:28:58','DEPOT',10000.00,'OP-20260308-192858-AG-E','2026-03-08 18:28:58','2026-03-08 18:28:58'),(2,'CMPT-EBENKGA-26-GWGR-00001','AG-EBENKGA-26-00002',14,'CDF',NULL,NULL,NULL,NULL,'CONFIRME','2026-03-08 18:31:49','RETRAIT',1000.00,'OP-20260308-193149-AG-E','2026-03-08 18:31:49','2026-03-08 18:31:49'),(3,'CMPT-EBENKGA-26-GWGR-00001','AG-EBENKGA-26-00002',14,'CDF',NULL,NULL,NULL,NULL,'CONFIRME','2026-03-08 18:52:18','RETRAIT',1500.00,'OP-20260308-195218-AG-E','2026-03-08 18:52:18','2026-03-08 18:52:18'),(4,'CMPT-EBENKGA-26-GWGR-00001','AG-EBENKGA-26-00002',14,'CDF',NULL,NULL,NULL,NULL,'CONFIRME','2026-03-08 18:54:49','RETRAIT',1500.00,'OP-20260308-195449-AG-E','2026-03-08 18:54:49','2026-03-08 18:54:49'),(5,'CMPT-EBENKGA-26-GWGR-00001','AG-EBENKGA-26-00002',14,'CDF',NULL,NULL,NULL,NULL,'CONFIRME','2026-03-08 19:10:23','RETRAIT',1500.00,'OP-20260308-201023-AG-E','2026-03-08 19:10:23','2026-03-08 19:10:23'),(6,'CMPT-EBENKGA-26-GWGR-00001','AG-EBENKGA-26-00002',14,'CDF',NULL,NULL,NULL,NULL,'CONFIRME','2026-03-08 19:10:40','DEPOT',5000.00,'OP-20260308-201040-AG-E','2026-03-08 19:10:40','2026-03-08 19:10:40');
/*!40000 ALTER TABLE `tb_transactions` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `tb_zones`
--

LOCK TABLES `tb_zones` WRITE;
/*!40000 ALTER TABLE `tb_zones` DISABLE KEYS */;
INSERT INTO `tb_zones` VALUES ('ZON-EBENKGA-26-00001','Nganza Nord','AG-EBENKGA-26-00002','Nganza','2026-03-07 06:47:17','2026-03-07 06:47:17'),('ZON-EBENKGA-26-00002','Nganza Sud','AG-EBENKGA-26-00003','Nganza','2026-03-07 06:47:35','2026-03-07 06:47:35');
/*!40000 ALTER TABLE `tb_zones` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'AG-EBENKGA-26-00001','bmb','bmb@bmb.cd','2026-03-06 09:09:58','$2y$12$b1CZZgerOu0kbKn5pnzuaOY5gffx18oF2PVj5mhGyw.d53G8XYqay',NULL,'actif','2026-03-06 09:09:58','2026-03-06 09:09:58'),(2,'AG-EBENKGA-26-00002','jean_caissier','jean.caissier@bmb.cd','2026-03-06 11:11:41','$2y$12$o/m3X.G8ImNrB8WgzrankOb5R8trQnBbSwK4vVzYXa7WHjy6AIIfG',NULL,'actif','2026-03-06 11:11:41','2026-03-06 11:11:41'),(3,'AG-EBENKGA-26-00003','marie_rh','marie.rh@bmb.cd','2026-03-06 11:11:41','$2y$12$j8T6Zy8w4q.zzZ1eNt/eeuak5l4H/PdUWwz7rmUNqNKJL7UQ7PR4m',NULL,'actif','2026-03-06 11:11:41','2026-03-06 11:11:41'),(6,'AG-EBENKGA-26-00005','mp','mputu@gmail.com',NULL,'$2y$12$A3T4Dsa2LJSqmsv3h9/5f.Io2a0A.KFx36XZTJta4GZuMU11obRJu',NULL,'actif','2026-03-07 07:28:59','2026-03-07 07:28:59'),(7,'AG-EBENKGA-26-00004','tresorier','tresorier@bmb.cd','2026-03-07 10:32:25','$2y$12$4B7/8qHDP9b7ACLK4/EEs.hkZOirTH4KPTTiu8At5cyiE/O.mbWi6',NULL,'actif','2026-03-07 10:32:25','2026-03-07 10:32:25');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

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

-- Dump completed on 2026-03-08 22:13:08
