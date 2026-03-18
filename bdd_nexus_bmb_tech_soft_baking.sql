-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 18 mars 2026 à 11:13
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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_03_05_000001_laravel_core', 1),
(2, '2026_03_05_000002_rh_banque_core', 1),
(3, '2026_03_05_000003_caisse_guichet', 1),
(4, '2026_03_05_000004_add_guichet_to_affectations', 1),
(5, '2026_03_05_000005_seed_test_users', 2),
(7, '2026_03_06_000006_permissions_banque_complete', 3),
(8, '2026_03_07_103903_add_statut_observations_to_mouvements_inter_caisses', 4),
(9, '2026_03_07_105258_add_type_guichet_and_coffre_central', 5),
(10, '2026_03_07_210000_tresorerie_permissions_and_user', 6),
(11, '2026_03_07_220001_add_demande_appro_to_type_flux', 7),
(12, '2026_03_07_235000_add_motif_statut_to_cloture_caisse', 8),
(13, '2026_03_08_000001_add_en_verification_and_cloture_validation', 9),
(14, '2026_03_07_145751_add_timestamps_to_tb_caisses_guichets', 10),
(16, '2026_03_08_110000_extend_tb_transactions_for_guichet', 11),
(17, '2026_03_08_120000_drop_client_columns_from_tb_transactions', 12),
(18, '2026_03_10_200000_create_tb_demandes_modification', 13),
(19, '2026_03_13_238000_extend_tb_plan_comptable_for_ohada_structure', 14),
(20, '2026_03_13_239000_seed_full_ohada_chart_accounts', 15),
(21, '2026_03_10_000001_update_type_enum_tb_comptes', 16),
(22, '2026_03_13_210000_create_tb_commission_rules_and_transaction_commissions', 16),
(23, '2026_03_13_233000_add_accounting_snapshots_to_tb_transactions', 16),
(24, '2026_03_13_234000_create_tb_compta_journaux_and_ecritures', 16),
(25, '2026_03_13_235000_seed_ohada_accounts_and_comptabilite_permissions', 16),
(26, '2026_03_16_000012_credit_permissions', 17),
(27, '2026_03_16_000011_credit_module_tables', 18);

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

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('L9HRp3bYbqXeKhdcc6HBULO635S5pCMu5hPwrMwk', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRDQzTFA5WE85SmF3S3RiN0hpWTZXSFVUdzVyNWRocGhDUWF5YVE2TCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTc6Imh0dHA6Ly9sb2NhbGhvc3QvTmV4dXMtQk1CLVRlY2gtc29mdC1iYW5raW5nL3B1YmxpYy9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=', 1773829558),
('mCGx6VfE2YsBz8TOrPDJARnNuVTr78hQ5xLZMrDi', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNzRzYjZSSEhXUDJzUTZRb0Y4eGtxSGczMlNpaDNPRWd0NFp5YnRpYiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTc6Imh0dHA6Ly9sb2NhbGhvc3QvTmV4dXMtQk1CLVRlY2gtc29mdC1iYW5raW5nL3B1YmxpYy9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=', 1773829472),
('vMFuXhMwiidEr64JMZeSHoqVQO9qMp8d4IARWZfl', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY2wxa2d4NmtGam5nUk1aZWZZTzNDQ3VxVkRxSVRxc1JROU5sVHBoWiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTc6Imh0dHA6Ly9sb2NhbGhvc3QvTmV4dXMtQk1CLVRlY2gtc29mdC1iYW5raW5nL3B1YmxpYy9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=', 1773829472);

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_affectations`
--

INSERT INTO `tb_affectations` (`id`, `agent_matricule`, `poste_id`, `guichet_id`, `date_debut`, `date_fin`, `Etat`, `created_at`, `updated_at`) VALUES
(6, 'AG-EBENKGA-26-00005', 2, 10, '2026-03-06', NULL, 'ACTIF', '2026-03-06 22:17:45', '2026-03-07 09:13:49'),
(8, 'AG-EBENKGA-26-00001', 2, 11, '2026-03-07', NULL, 'ACTIF', '2026-03-07 09:14:52', '2026-03-07 09:14:52'),
(9, 'AG-EBENKGA-26-00004', 4, NULL, '2026-03-07', NULL, 'ACTIF', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
(10, 'AG-EBENKGA-26-00002', 2, 14, '2026-03-07', NULL, 'ACTIF', '2026-03-07 12:42:48', '2026-03-07 12:42:48'),
(11, 'AG-EBENKGA-26-00006', 2, 17, '2026-03-13', NULL, 'ACTIF', '2026-03-13 14:09:10', '2026-03-13 14:09:10'),
(12, 'AG-EBENKGA-26-00007', 2, 18, '2026-03-14', NULL, 'ACTIF', '2026-03-14 11:54:53', '2026-03-14 11:54:53');

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
  PRIMARY KEY (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_agents`
--

INSERT INTO `tb_agents` (`matricule`, `nom`, `postnom`, `prenom`, `sexe`, `date_naissance`, `telephone`, `email`, `adresse`, `photo`, `date_embauche`, `statut`, `created_at`, `updated_at`) VALUES
('AG-EBENKGA-26-00001', 'BMB', 'ADMIN', 'Système', 'M', NULL, NULL, 'bmb@bmb.cd', NULL, NULL, '2026-03-06', 'actif', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
('AG-EBENKGA-26-00002', 'MULUMBA', NULL, 'Jean', 'M', '1990-06-15', '+243810000002', 'jean.caissier@bmb.cd', 'Kinshasa', NULL, '2026-03-06', 'actif', '2026-03-06 11:11:41', '2026-03-06 11:11:41'),
('AG-EBENKGA-26-00003', 'KASONGO', NULL, 'Marie', 'F', '1992-03-20', '+243810000003', 'marie.rh@bmb.cd', 'Kinshasa', NULL, '2026-03-06', 'actif', '2026-03-06 11:11:41', '2026-03-06 11:11:41'),
('AG-EBENKGA-26-00004', 'MUBADI', 'BAKAJIKA', 'Blaise', 'M', '2026-03-04', '+24399999555', 'blaisemubadibakajika@gmail.com', 'Nganza', 'agents/1772808566_whatsapp-image-2024-02-23-a-222610-394d5d2cjpg', '2026-03-05', 'actif', '2026-03-06 13:49:26', '2026-03-06 13:49:26'),
('AG-EBENKGA-26-00005', 'MPUTU', 'TUDIKOLELE', 'Nice', 'F', '2026-03-04', '+24399999555', 'mputu@gmail.com', 'Nganza', 'agents/1772808651_nicejpg', '2026-03-05', 'actif', '2026-03-06 13:50:51', '2026-03-06 13:50:51'),
('AG-EBENKGA-26-00006', 'KALALA', 'KALALA', 'Junior', 'M', '2026-03-05', '+24399999555', 'kalala@gmail.com', 'Nganza', 'agents/1772808737_1767056067186jpg', '2026-03-05', 'actif', '2026-03-06 13:52:17', '2026-03-06 13:52:17'),
('AG-EBENKGA-26-00007', 'KAPUKU', 'KAPUKU', 'Jean', 'M', NULL, NULL, NULL, NULL, NULL, NULL, 'actif', '2026-03-14 11:54:34', '2026-03-14 11:54:34');

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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_caisses_guichets`
--

INSERT INTO `tb_caisses_guichets` (`id`, `code_guichet`, `type_guichet`, `intitule`, `statut_operationnel`, `created_at`, `updated_at`) VALUES
(1, 'COFFRE_01', 'CENTRAL', 'Coffre-Fort Central EBEN', 'OUVERT', '2026-03-07 10:02:55', '2026-03-07 10:02:55'),
(10, 'G01', 'FIXE', 'Principale', 'FERME', '2026-03-06 22:05:29', '2026-03-06 22:05:29'),
(11, 'G02', 'FIXE', 'Guichet ndesha', 'FERME', '2026-03-06 22:13:27', '2026-03-08 17:15:52'),
(14, 'G03', 'FIXE', 'Guichet Kananga 1', 'OUVERT', '2026-03-07 12:29:08', '2026-03-08 17:34:28'),
(17, 'G04', 'MOBILE', 'Guichet Batetela 1', 'FERME', '2026-03-07 20:47:15', '2026-03-13 18:02:31'),
(18, 'G05', 'FIXE', 'Kamayi', 'OUVERT', '2026-03-14 11:53:41', '2026-03-14 14:46:13');

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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_caisses_guichets_soldes`
--

INSERT INTO `tb_caisses_guichets_soldes` (`id`, `guichet_id`, `devise_code`, `solde_en_caisse`, `updated_at`) VALUES
(11, 10, 'CDF', 0.00, '2026-03-06 23:05:29'),
(12, 11, 'CDF', 0.00, '2026-03-06 23:13:27'),
(13, 11, 'USD', 0.00, '2026-03-08 18:15:52'),
(15, 1, 'CDF', 55000.00, '2026-03-13 19:02:27'),
(16, 1, 'EUR', 500.00, '2026-03-13 19:03:10'),
(17, 1, 'USD', 59500.00, '2026-03-14 13:04:05'),
(18, 14, 'CDF', 194500.00, '2026-03-14 15:46:33'),
(23, 17, 'CDF', 0.00, '2026-03-13 19:02:27'),
(24, 17, 'USD', 0.00, '2026-03-13 19:02:31'),
(25, 18, 'CDF', 500.00, '2026-03-17 08:51:49'),
(26, 18, 'USD', 0.00, '2026-03-14 13:04:05');

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
  KEY `tb_zones_ibfk_11` (`code_zone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_clients`
--

INSERT INTO `tb_clients` (`matricule`, `code_zone`, `nom`, `postnom`, `prenom`, `email`, `telephone`, `sexe`, `date_naissance`, `lieu_naissance`, `adresse`, `etat_civil`, `nom_conjoint`, `type_piece_identite`, `lieu_delivrance_piece`, `date_delivrance_piece`, `numero_piece_identite`, `photo`, `secteur_activite`, `type_activite`, `nom_entreprise`, `adresse_entreprise`, `telephone_entreprise`, `statut_entreprise`, `nombre_annees_experience`, `revenu_mensuel`, `revenu_mensuel_devise`, `autres_details_activite`, `created_at`, `updated_at`) VALUES
('CL-EBENKGA-26-00001', 'ZON-EBENKGA-26-00001', 'MUKENDI', 'MALUNDA', 'Cédrick', 'mukende@gmail.com', '25544', 'M', '2026-02-26', 'Kananga', 'Nganza', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2026-03-11', '+255555', 'clients/1772872385_recharger-telephone-intelligent_16734-98 (1).jpeg', 'Enseignement', 'Commerce', 'Nexus BMB Techn', 'Kamayi', '+2453333', 'Privé', '3', 500.00, NULL, NULL, '2026-03-07 07:33:05', '2026-03-07 07:33:05'),
('CL-EBENKGA-26-00002', 'ZON-EBENKGA-26-00002', 'TSHIAMALA', 'NTAMBUE', 'Jack', 'tshiamal@gmail.com', '25544', 'M', '2026-02-26', 'Kananga', 'Nganza', 'Marié', 'KANKU Marie', 'Carte nationale d\'identité', 'Kananga', '2026-03-11', '+255555', 'clients/1772872478_recharger-telephone-intelligent_16734-98 (1).png', 'Enseignement', 'Commerce', 'Nexus BMB Techn', 'Kamayi', '+2453333', 'Privé', '3', 500.00, NULL, NULL, '2026-03-07 07:34:38', '2026-03-07 07:34:38'),
('CL-EBENKGA-26-00003', 'ZON-EBENKGA-26-00003', 'NKASHAMA', 'KANKU', 'Joséphine', 'jsophine@gmail.com', '+2544', 'F', '2026-02-23', 'Kananga', 'kaaja', 'Célibataire', NULL, 'Passeport', 'Kananga', '2026-02-24', '6++224555', 'clients/1773414957_ESP32-CAM-Connexion USB.png', 'Enseignement', 'Agriculture', 'Nexus BMB Techn', 'Kamayi', '+2453333', 'Privé', '5', 500.00, NULL, NULL, '2026-03-13 14:15:57', '2026-03-13 14:15:57');

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
  KEY `validateur_matricule` (`validateur_matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_cloture_caisse`
--

INSERT INTO `tb_cloture_caisse` (`id`, `guichet_id`, `devise_code`, `solde_comptable`, `solde_physique`, `ecart_caisse`, `detail_billetage`, `motif_ecart`, `statut_ecart`, `statut_validation`, `validateur_matricule`, `date_validation`, `observations_superviseur`, `agent_cloturant`, `date_cloture`) VALUES
(1, 14, 'CDF', 1000000.00, 1000000.00, 0.00, '{\"5000\": 80, \"10000\": 20, \"20000\": 20}', NULL, 'EQUILIBRE', 'VALIDE', 'AG-EBENKGA-26-00001', '2026-03-07 13:58:25', NULL, 'AG-EBENKGA-26-00002', '2026-03-07 13:51:31'),
(2, 14, 'CDF', 5000.00, 5000.00, 0.00, '{\"5000\": 1}', NULL, 'EQUILIBRE', 'REJETE', 'AG-EBENKGA-26-00001', '2026-03-07 14:08:52', 'Ecart non justifié', 'AG-EBENKGA-26-00002', '2026-03-07 14:07:39'),
(3, 14, 'CDF', 5000.00, 5000.00, 0.00, '{\"5000\": 1}', NULL, 'EQUILIBRE', 'VALIDE', 'AG-EBENKGA-26-00001', '2026-03-07 14:11:54', NULL, 'AG-EBENKGA-26-00002', '2026-03-07 14:11:39'),
(4, 11, 'CDF', 0.00, 0.00, 0.00, '[]', NULL, 'EQUILIBRE', 'VALIDE', 'AG-EBENKGA-26-00001', '2026-03-08 17:15:43', NULL, 'AG-EBENKGA-26-00001', '2026-03-07 20:48:17'),
(5, 11, 'USD', 500.00, 500.00, 0.00, '{\"100\": 5}', NULL, 'EQUILIBRE', 'VALIDE', 'AG-EBENKGA-26-00001', '2026-03-08 17:15:52', NULL, 'AG-EBENKGA-26-00001', '2026-03-07 20:48:17'),
(6, 14, 'CDF', 65000.00, 65000.00, 0.00, '{\"5000\": 1, \"10000\": 1, \"50000\": 1}', NULL, 'EQUILIBRE', 'VALIDE', 'AG-EBENKGA-26-00001', '2026-03-07 20:52:55', NULL, 'AG-EBENKGA-26-00002', '2026-03-07 20:52:30'),
(7, 14, 'CDF', 0.00, 0.00, 0.00, '[]', NULL, 'EQUILIBRE', 'VALIDE', 'AG-EBENKGA-26-00001', '2026-03-07 21:05:00', NULL, 'AG-EBENKGA-26-00002', '2026-03-07 21:04:45'),
(8, 14, 'CDF', 5000.00, 5000.00, 0.00, '{\"5000\": 1}', NULL, 'EQUILIBRE', 'VALIDE', 'AG-EBENKGA-26-00001', '2026-03-08 17:27:07', NULL, 'AG-EBENKGA-26-00002', '2026-03-08 17:26:38'),
(9, 17, 'CDF', 55000.00, 55000.00, 0.00, '{\"5000\": 1, \"50000\": 1}', NULL, 'EQUILIBRE', 'VALIDE', 'AG-EBENKGA-26-00001', '2026-03-13 18:02:27', NULL, 'AG-EBENKGA-26-00006', '2026-03-13 18:01:12'),
(10, 17, 'USD', 55000.00, 55000.00, 0.00, '{\"50\": 100, \"100\": 500}', NULL, 'EQUILIBRE', 'VALIDE', 'AG-EBENKGA-26-00001', '2026-03-13 18:02:31', NULL, 'AG-EBENKGA-26-00006', '2026-03-13 18:01:12'),
(11, 18, 'CDF', 0.00, 0.00, 0.00, '[]', NULL, 'EQUILIBRE', 'VALIDE', 'AG-EBENKGA-26-00001', '2026-03-14 12:04:09', NULL, 'AG-EBENKGA-26-00007', '2026-03-14 12:03:53'),
(12, 18, 'USD', 4500.00, 4500.00, 0.00, '{\"50\": 10, \"100\": 40}', NULL, 'EQUILIBRE', 'VALIDE', 'AG-EBENKGA-26-00001', '2026-03-14 12:04:05', NULL, 'AG-EBENKGA-26-00007', '2026-03-14 12:03:53');

-- --------------------------------------------------------

--
-- Structure de la table `tb_commission_rules`
--

DROP TABLE IF EXISTS `tb_commission_rules`;
CREATE TABLE IF NOT EXISTS `tb_commission_rules` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_operation` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TOUS',
  `type_compte` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TOUS',
  `type_guichet` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TOUS',
  `devise_code` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_zone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portefeuille_id` bigint UNSIGNED DEFAULT NULL,
  `montant_min` decimal(18,2) DEFAULT NULL,
  `montant_max` decimal(18,2) DEFAULT NULL,
  `mode_calcul` enum('FIXE','POURCENTAGE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur` decimal(18,4) NOT NULL,
  `priorite` int UNSIGNED NOT NULL DEFAULT '100',
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
  KEY `tb_agent` (`created_by_agent`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_commission_rules`
--

INSERT INTO `tb_commission_rules` (`id`, `libelle`, `code_operation`, `type_compte`, `type_guichet`, `devise_code`, `code_zone`, `portefeuille_id`, `montant_min`, `montant_max`, `mode_calcul`, `valeur`, `priorite`, `date_debut`, `date_fin`, `est_actif`, `observations`, `created_by_agent`, `created_at`, `updated_at`) VALUES
(1, 'Fais de retrais', 'RETRAIT', 'TOUS', 'TOUS', 'USD', NULL, NULL, NULL, NULL, 'POURCENTAGE', 2.0000, 10, '2026-03-14', NULL, 1, NULL, 'AG-EBENKGA-26-00001', '2026-03-14 11:11:39', '2026-03-14 11:11:39');

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_compta_ecritures`
--

INSERT INTO `tb_compta_ecritures` (`id`, `journal_id`, `numero_compte`, `devise_code`, `libelle_ligne`, `debit`, `credit`, `ordre`, `created_at`, `updated_at`) VALUES
(1, 1, '2511', 'CDF', 'Diminution depot client', 5000.00, 0.00, 1, '2026-03-14 11:13:58', '2026-03-14 11:13:58'),
(2, 1, '5701', 'CDF', 'Decaissement retrait client', 0.00, 5000.00, 2, '2026-03-14 11:13:58', '2026-03-14 11:13:58'),
(3, 2, '2511', 'CDF', 'Diminution depot client', 5000.00, 0.00, 1, '2026-03-14 11:50:25', '2026-03-14 11:50:25'),
(4, 2, '5701', 'CDF', 'Decaissement retrait client', 0.00, 5000.00, 2, '2026-03-14 11:50:25', '2026-03-14 11:50:25'),
(5, 3, '5702', 'USD', 'Encaissement depot client', 5000.00, 0.00, 1, '2026-03-14 11:58:54', '2026-03-14 11:58:54'),
(6, 3, '2511', 'USD', 'Augmentation depot client', 0.00, 5000.00, 2, '2026-03-14 11:58:54', '2026-03-14 11:58:54'),
(7, 4, '2511', 'USD', 'Diminution depot client', 500.00, 0.00, 1, '2026-03-14 12:00:00', '2026-03-14 12:00:00'),
(8, 4, '5702', 'USD', 'Decaissement retrait client', 0.00, 500.00, 2, '2026-03-14 12:00:00', '2026-03-14 12:00:00'),
(9, 4, '2511', 'USD', 'Commission sur retrait - debit client', 10.00, 0.00, 3, '2026-03-14 12:00:00', '2026-03-14 12:00:00'),
(10, 4, '7061', 'USD', 'Produit commission retrait', 0.00, 10.00, 4, '2026-03-14 12:00:00', '2026-03-14 12:00:00'),
(11, 5, '5701', 'CDF', 'Encaissement depot client', 5000.00, 0.00, 1, '2026-03-14 14:46:33', '2026-03-14 14:46:33'),
(12, 5, '2511', 'CDF', 'Augmentation depot client', 0.00, 5000.00, 2, '2026-03-14 14:46:33', '2026-03-14 14:46:33'),
(13, 6, '5701', 'CDF', 'Encaissement depot client', 500.00, 0.00, 1, '2026-03-17 07:51:49', '2026-03-17 07:51:49'),
(14, 6, '2511', 'CDF', 'Augmentation depot client', 0.00, 500.00, 2, '2026-03-17 07:51:49', '2026-03-17 07:51:49');

-- --------------------------------------------------------

--
-- Structure de la table `tb_compta_journaux`
--

DROP TABLE IF EXISTS `tb_compta_journaux`;
CREATE TABLE IF NOT EXISTS `tb_compta_journaux` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code_journal` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CAI',
  `reference_piece` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` bigint UNSIGNED DEFAULT NULL,
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
  KEY `fk_compta_journal_devise` (`devise_code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_compta_journaux`
--

INSERT INTO `tb_compta_journaux` (`id`, `code_journal`, `reference_piece`, `transaction_id`, `type_piece`, `devise_code`, `libelle`, `statut`, `agent_matricule`, `date_ecriture`, `metadata`, `created_at`, `updated_at`) VALUES
(1, 'CAI', 'CPT-OP-20260314-121358-AG-E-20260314121358481', 15, 'OPERATION', 'CDF', 'Retrait OP-20260314-121358-AG-E', 'COMPTABILISE', 'AG-EBENKGA-26-00002', '2026-03-14 11:13:58', '{\"montant\": 5000, \"reverse\": false, \"commission\": 0, \"type_operation\": \"RETRAIT\", \"commission_trace\": {\"base\": 5000, \"mode\": \"FIXE\", \"valeur\": 0, \"libelle\": \"Aucune regle de commission applicable\", \"montant\": 0, \"rule_id\": null, \"has_rule\": false, \"snapshot_id\": 1}, \"transaction_reference\": \"OP-20260314-121358-AG-E\"}', '2026-03-14 11:13:58', '2026-03-14 11:13:58'),
(2, 'CAI', 'CPT-OP-20260314-125025-AG-E-20260314125025702', 16, 'OPERATION', 'CDF', 'Retrait OP-20260314-125025-AG-E', 'COMPTABILISE', 'AG-EBENKGA-26-00002', '2026-03-14 11:50:25', '{\"montant\": 5000, \"reverse\": false, \"commission\": 0, \"type_operation\": \"RETRAIT\", \"commission_trace\": {\"base\": 5000, \"mode\": \"FIXE\", \"valeur\": 0, \"libelle\": \"Aucune regle de commission applicable\", \"montant\": 0, \"rule_id\": null, \"has_rule\": false, \"snapshot_id\": 2}, \"transaction_reference\": \"OP-20260314-125025-AG-E\"}', '2026-03-14 11:50:25', '2026-03-14 11:50:25'),
(3, 'CAI', 'CPT-OP-20260314-125854-AG-E-20260314125854206', 17, 'OPERATION', 'USD', 'Dépôt OP-20260314-125854-AG-E', 'COMPTABILISE', 'AG-EBENKGA-26-00007', '2026-03-14 11:58:54', '{\"montant\": 5000, \"reverse\": false, \"commission\": 0, \"type_operation\": \"DEPOT\", \"commission_trace\": {\"base\": 5000, \"mode\": \"FIXE\", \"valeur\": 0, \"libelle\": \"Aucune regle de commission applicable\", \"montant\": 0, \"rule_id\": null, \"has_rule\": false, \"snapshot_id\": 3}, \"transaction_reference\": \"OP-20260314-125854-AG-E\"}', '2026-03-14 11:58:54', '2026-03-14 11:58:54'),
(4, 'CAI', 'CPT-OP-20260314-130000-AG-E-20260314130000966', 18, 'OPERATION', 'USD', 'Retrait OP-20260314-130000-AG-E', 'COMPTABILISE', 'AG-EBENKGA-26-00007', '2026-03-14 12:00:00', '{\"montant\": 500, \"reverse\": false, \"commission\": 10, \"type_operation\": \"RETRAIT\", \"commission_trace\": {\"base\": 500, \"mode\": \"POURCENTAGE\", \"valeur\": 2, \"libelle\": \"Fais de retrais\", \"montant\": 10, \"rule_id\": 1, \"has_rule\": true, \"snapshot_id\": 4}, \"transaction_reference\": \"OP-20260314-130000-AG-E\"}', '2026-03-14 12:00:00', '2026-03-14 12:00:00'),
(5, 'CAI', 'CPT-OP-20260314-154633-AG-E-20260314154633535', 19, 'OPERATION', 'CDF', 'Dépôt OP-20260314-154633-AG-E', 'COMPTABILISE', 'AG-EBENKGA-26-00002', '2026-03-14 14:46:33', '{\"montant\": 5000, \"reverse\": false, \"commission\": 0, \"type_operation\": \"DEPOT\", \"commission_trace\": {\"base\": 5000, \"mode\": \"FIXE\", \"valeur\": 0, \"libelle\": \"Aucune regle de commission applicable\", \"montant\": 0, \"rule_id\": null, \"has_rule\": false, \"snapshot_id\": 5}, \"transaction_reference\": \"OP-20260314-154633-AG-E\"}', '2026-03-14 14:46:33', '2026-03-14 14:46:33'),
(6, 'CAI', 'CPT-OP-20260317-085148-AG-E-20260317085149060', 20, 'OPERATION', 'CDF', 'Dépôt OP-20260317-085148-AG-E', 'COMPTABILISE', 'AG-EBENKGA-26-00007', '2026-03-17 07:51:49', '{\"montant\": 500, \"reverse\": false, \"commission\": 0, \"type_operation\": \"DEPOT\", \"commission_trace\": {\"base\": 500, \"mode\": \"FIXE\", \"valeur\": 0, \"libelle\": \"Aucune regle de commission applicable\", \"montant\": 0, \"rule_id\": null, \"has_rule\": false, \"snapshot_id\": 6}, \"transaction_reference\": \"OP-20260317-085148-AG-E\"}', '2026-03-17 07:51:49', '2026-03-17 07:51:49');

-- --------------------------------------------------------

--
-- Structure de la table `tb_comptes`
--

DROP TABLE IF EXISTS `tb_comptes`;
CREATE TABLE IF NOT EXISTS `tb_comptes` (
  `code_compte` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_matricule` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `devise` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('CC','RMB','GTC','DAT','EAV') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'CC=Compte Courant, RMB=Remboursement, GTC=Caution, DAT=Dépôt à Terme, EAV=Épargne & Vie',
  `portefeuille_id` bigint UNSIGNED DEFAULT NULL,
  `solde_reel` decimal(18,2) NOT NULL DEFAULT '0.00',
  `solde_bloque` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`code_compte`),
  KEY `fk_compte_devise` (`devise`),
  KEY `fk_compte_portefeuille` (`portefeuille_id`),
  KEY `tb_comptes_ibfk_112` (`client_matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_comptes`
--

INSERT INTO `tb_comptes` (`code_compte`, `client_matricule`, `devise`, `type`, `portefeuille_id`, `solde_reel`, `solde_bloque`, `created_at`, `updated_at`) VALUES
('243-52514-CC-00001CMX', 'CL-EBENKGA-26-00001', 'EUR', 'CC', NULL, 0.00, 0.00, '2026-03-13 14:17:07', '2026-03-13 15:17:07'),
('243-52514-CC-00002EXI', 'CL-EBENKGA-26-00003', 'CDF', 'CC', NULL, 50500.00, 0.00, '2026-03-13 14:22:37', '2026-03-17 08:51:49'),
('243-52514-DAT-00001THA', 'CL-EBENKGA-26-00003', 'EUR', 'DAT', NULL, 0.00, 0.00, '2026-03-13 14:22:52', '2026-03-13 15:22:52'),
('243-52514-EAV-00001REB', 'CL-EBENKGA-26-00002', 'CDF', 'EAV', NULL, 90000.00, 0.00, '2026-03-10 19:18:02', '2026-03-13 14:30:49'),
('243-52514-EAV-00002GKT', 'CL-EBENKGA-26-00003', 'USD', 'EAV', NULL, 59490.00, 0.00, '2026-03-13 14:16:42', '2026-03-14 13:00:00'),
('CMPT-EBENKGA-26-6ZT1-00003', 'CL-EBENKGA-26-00001', 'USD', 'CC', NULL, 0.00, 0.00, '2026-03-07 07:43:15', '2026-03-07 08:43:15'),
('CMPT-EBENKGA-26-GWGR-00001', 'CL-EBENKGA-26-00001', 'CDF', 'GTC', 1, 9500.00, 0.00, '2026-03-07 07:41:50', '2026-03-08 20:10:40'),
('CMPT-EBENKGA-26-VM3R-00002', 'CL-EBENKGA-26-00001', 'CDF', 'CC', NULL, 50000.00, 0.00, '2026-03-07 07:40:43', '2026-03-10 20:14:34');

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
  KEY `tb_credit_deblocages_compte_credit_id_foreign` (`compte_credit_id`)
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
  `compte_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `portefeuille_id` bigint UNSIGNED NOT NULL,
  `code_zone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `agent_createur_matricule` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant_demande` decimal(15,2) NOT NULL,
  `devise` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CDF',
  `duree_mois` tinyint UNSIGNED NOT NULL,
  `taux_interet_mensuel` decimal(6,4) NOT NULL,
  `type_credit` enum('INDIVIDUEL','SOLIDAIRE','PME') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INDIVIDUEL',
  `objet_credit` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `garantie_description` text COLLATE utf8mb4_unicode_ci,
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
  KEY `tb_credit_demandes_compte_id_foreign` (`compte_id`)
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
  `observations` text COLLATE utf8mb4_unicode_ci,
  `conditions` text COLLATE utf8mb4_unicode_ci,
  `ordre_etape` tinyint UNSIGNED NOT NULL,
  `etape_precedente_ok` tinyint(1) NOT NULL DEFAULT '0',
  `valide_le` timestamp NULL DEFAULT NULL,
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
  `transaction_id` bigint UNSIGNED NOT NULL,
  `reference_operation` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Référence de l''opération initiale',
  `guichet_id` bigint UNSIGNED DEFAULT NULL,
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
  KEY `tb_demandes_modification_superviseur_matricule_index` (`superviseur_matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_demandes_modification`
--

INSERT INTO `tb_demandes_modification` (`id`, `transaction_id`, `reference_operation`, `guichet_id`, `compte_code`, `client_nom`, `type_operation`, `devise_code`, `ancien_montant`, `anciennes_observations`, `type_demande`, `agent_matricule`, `motif`, `nouveau_montant`, `nouvelles_observations`, `statut`, `superviseur_matricule`, `commentaire_superviseur`, `traitee_le`, `created_at`, `updated_at`) VALUES
(1, 7, 'OP-20260310-201434-AG-E', 14, 'CMPT-EBENKGA-26-VM3R-00002', 'MUKENDI MALUNDA Cédrick', 'DEPOT', 'CDF', 50000.00, NULL, 'MODIFICATION', 'AG-EBENKGA-26-00002', 'J\'avais al saisis le montant', 40000.00, NULL, 'EN_ATTENTE', NULL, NULL, NULL, '2026-03-10 21:31:59', '2026-03-10 21:31:59');

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
  PRIMARY KEY (`code_iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_devises`
--

INSERT INTO `tb_devises` (`code_iso`, `nom`, `symbole`, `est_reference`, `created_at`, `updated_at`) VALUES
('CDF', 'Franc Congolais', 'Fc', 1, '2026-03-06 09:09:58', NULL),
('EUR', 'Euro', '€', 0, '2026-03-06 09:09:58', NULL),
('USD', 'Dollar Américain', '$', 0, '2026-03-06 09:09:58', NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_mouvements_inter_caisses`
--

INSERT INTO `tb_mouvements_inter_caisses` (`id`, `guichet_source_id`, `guichet_dest_id`, `agent_initiateur`, `type_flux`, `montant`, `devise_code`, `reference_bordereau`, `date_mouvement`, `statut`, `validateur_matricule`, `observations`) VALUES
(1, 1, 11, 'AG-EBENKGA-26-00001', 'DEMANDE_APPRO', 500.00, 'USD', 'REQ-20260307-131603-AG-E', '2026-03-07 12:16:03', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Sole démarrage | Approuvé'),
(2, 1, 14, 'AG-EBENKGA-26-00002', 'DEMANDE_APPRO', 500000.00, 'CDF', 'REQ-20260307-134352-AG-E', '2026-03-07 12:43:52', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Fond de demarrage guichet | Approuvé'),
(3, 1, 14, 'AG-EBENKGA-26-00002', 'DEMANDE_APPRO', 500000.00, 'CDF', 'REQ-20260307-140643-AG-E', '2026-03-07 13:06:43', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Approuvé'),
(4, 14, 1, 'AG-EBENKGA-26-00001', 'DEGAGEMENT', 1000000.00, 'CDF', 'DEG-20260307-145825-G03', '2026-03-07 13:58:25', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Dégagement automatique clôture G03'),
(5, 1, 14, 'AG-EBENKGA-26-00002', 'DEMANDE_APPRO', 5000.00, 'CDF', 'REQ-20260307-150115-AG-E', '2026-03-07 14:01:15', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Fonds de démarrage | Approuvé'),
(6, 14, 1, 'AG-EBENKGA-26-00001', 'DEGAGEMENT', 5000.00, 'CDF', 'DEG-20260307-151154-G03', '2026-03-07 14:11:54', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Dégagement automatique clôture G03'),
(7, 1, 14, 'AG-EBENKGA-26-00002', 'DEMANDE_APPRO', 5000.00, 'CDF', 'REQ-20260307-151641-AG-E', '2026-03-07 14:16:41', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Approuvé'),
(8, 1, 14, 'AG-EBENKGA-26-00002', 'DEMANDE_APPRO', 60000.00, 'CDF', 'REQ-20260307-152531-AG-E', '2026-03-07 14:25:31', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Approuvé'),
(17, 14, 1, 'AG-EBENKGA-26-00001', 'DEGAGEMENT', 65000.00, 'CDF', 'DEG-20260307-215255-G03', '2026-03-07 20:52:55', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Dégagement automatique clôture G03'),
(20, 14, 1, 'AG-EBENKGA-26-00001', 'DEGAGEMENT', 0.00, 'CDF', 'DEG-20260307-220500-G03', '2026-03-07 21:05:00', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Dégagement automatique clôture G03'),
(25, 11, 1, 'AG-EBENKGA-26-00001', 'DEGAGEMENT', 0.00, 'CDF', 'DEG-20260308-181543-G02-CDF', '2026-03-08 17:15:43', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Dégagement CDF — clôture G02'),
(26, 11, 1, 'AG-EBENKGA-26-00001', 'DEGAGEMENT', 500.00, 'USD', 'DEG-20260308-181552-G02-USD', '2026-03-08 17:15:52', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Dégagement USD — clôture G02'),
(27, 1, 14, 'AG-EBENKGA-26-00002', 'DEMANDE_APPRO', 5000.00, 'CDF', 'REQ-20260308-182517-AG-E', '2026-03-08 17:25:17', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Fond de démarrage de guichet | Approuvé'),
(28, 14, 1, 'AG-EBENKGA-26-00001', 'DEGAGEMENT', 5000.00, 'CDF', 'DEG-20260308-182707-G03-CDF', '2026-03-08 17:27:07', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Dégagement CDF — clôture G03'),
(29, NULL, 14, 'AG-EBENKGA-26-00002', 'DEMANDE_APPRO', 50000.00, 'CDF', 'REQ-20260308-183444-AG-E', '2026-03-08 17:34:44', 'ANNULE', 'AG-EBENKGA-26-00001', 'Rejeté : Tu as encore l\'argent dans la caisse'),
(30, 1, 14, 'AG-EBENKGA-26-00002', 'DEMANDE_APPRO', 50000.00, 'CDF', 'REQ-20260308-183557-AG-E', '2026-03-08 17:35:57', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Approuvé'),
(31, NULL, 14, 'AG-EBENKGA-26-00002', 'DEMANDE_APPRO', 1500.00, 'CDF', 'REQ-20260308-200955-AG-E', '2026-03-08 19:09:55', 'ANNULE', 'AG-EBENKGA-26-00001', 'Rejeté : Tu as d\'autre argent'),
(32, NULL, 1, 'AG-EBENKGA-26-00001', 'ALIMENTATION', 500000.00, 'USD', 'APP-20260313-144654-145275-7C73', '2026-03-13 13:46:54', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Approvisionnement externe — BANQUE'),
(33, NULL, 1, 'AG-EBENKGA-26-00001', 'ALIMENTATION', 50000000.00, 'EUR', 'APP-20260313-144709-261756-8256', '2026-03-13 13:47:09', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Approvisionnement externe — PARTENAIRE'),
(34, 17, 1, 'AG-EBENKGA-26-00001', 'DEGAGEMENT', 55000.00, 'CDF', 'DEG-20260313-190227-G04-CDF', '2026-03-13 18:02:27', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Dégagement CDF — clôture G04'),
(35, 17, 1, 'AG-EBENKGA-26-00001', 'DEGAGEMENT', 55000.00, 'USD', 'DEG-20260313-190231-G04-USD', '2026-03-13 18:02:31', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Dégagement USD — clôture G04'),
(36, NULL, 1, 'AG-EBENKGA-26-00001', 'ALIMENTATION', 500.00, 'EUR', 'APP-20260313-190310-389869-FB06', '2026-03-13 18:03:10', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Approvisionnement externe — BANQUE'),
(37, 18, 1, 'AG-EBENKGA-26-00001', 'DEGAGEMENT', 4500.00, 'USD', 'DEG-20260314-130405-G05-USD', '2026-03-14 12:04:05', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Dégagement USD — clôture G05'),
(38, 18, 1, 'AG-EBENKGA-26-00001', 'DEGAGEMENT', 0.00, 'CDF', 'DEG-20260314-130409-G05-CDF', '2026-03-14 12:04:09', 'CONFIRME', 'AG-EBENKGA-26-00001', 'Dégagement CDF — clôture G05');

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

--
-- Déchargement des données de la table `tb_permissions`
--

INSERT INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
('EBEN-PER1', 'Accès Administration', 'Accès au panneau d\'administration', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER10', 'Voir caisse', 'Consultation des caisses', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER11', 'Ouvrir caisse', 'Ouverture d\'une caisse/guichet', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER12', 'Fermer caisse', 'Fermeture d\'une caisse/guichet', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER13', 'Mouvements caisse', 'Enregistrement des mouvements', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER14', 'Clôture caisse', 'Clôture journalière caisse', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER15', 'Voir clients', 'Consultation des clients', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER16', 'Créer client', 'Enregistrement d\'un client', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER17', 'Modifier client', 'Modification d\'un client', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER18', 'Voir comptes', 'Consultation des comptes', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER19', 'Créer compte', 'Ouverture d\'un compte', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER2', 'Voir les rôles', 'Consultation des rôles', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER20', 'Voir devises', 'Consultation des devises', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER21', 'Gérer devises', 'Gestion des devises et taux', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER22', 'Effectuer dépôts', 'Saisir un dépôt sur un compte client', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER23', 'Effectuer retraits', 'Saisir un retrait sur un compte client', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER24', 'Effectuer virements', 'Initier un virement entre comptes', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER25', 'Annuler transactions', 'Annuler ou reverser une opération bancaire', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER26', 'Valider transactions', 'Approuver les opérations en attente (double validation)', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER27', 'Voir produits épargne', 'Consulter les produits d\'épargne disponibles', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER28', 'Gérer produits épargne', 'Créer et modifier les produits d\'épargne', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER29', 'Gérer comptes épargne', 'Ouvrir, alimenter et clôturer des comptes épargne', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER3', 'Gérer les rôles', 'Création et modification des rôles', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER30', 'Voir crédits', 'Consulter les dossiers de crédit', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER31', 'Soumettre demande crédit', 'Créer une demande de prêt pour un client', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER32', 'Instruire dossier crédit', 'Analyser et compléter un dossier de crédit', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER33', 'Approuver crédit', 'Accorder ou rejeter un crédit (niveau comité)', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER34', 'Gérer remboursements', 'Saisir les échéances et paiements de remboursement', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER35', 'Clôturer crédit', 'Marquer un crédit comme soldé ou en contentieux', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER36', 'Voir rapports opérationnels', 'Rapports journaliers caisse et transactions', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER37', 'Voir rapports financiers', 'Bilan, compte de résultat, situation financière', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER38', 'Exporter rapports', 'Exporter ou imprimer tous les rapports en PDF/Excel', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER39', 'Voir journal comptable', 'Consulter les écritures du journal comptable', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER4', 'Voir les permissions', 'Consultation des permissions', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER40', 'Saisir écritures', 'Créer des écritures comptables manuelles', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER41', 'Valider écritures', 'Approuver et lettrer les écritures comptables', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER42', 'Voir journal d\'activité', 'Logs d\'audit : qui a fait quoi et quand', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER43', 'Gérer paramètres sécurité', 'Politique mots de passe, tentatives login, blocages', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER44', 'Voir trésorerie', 'Accès au module trésorerie/coffre-fort (vue d\'ensemble et soldes)', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-PER45', 'Approvisionner coffre', 'Enregistrer un approvisionnement externe (banque → coffre)', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-PER46', 'Valider mouvements trésorerie', 'Approuver / rejeter les opérations coffre-fort en attente', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-PER47', 'Alimenter guichets', 'Transférer des fonds entre le coffre central et les guichets', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-PER48', 'Journal trésorerie', 'Consulter le journal complet de la caisse centrale (historique)', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-PER49', 'Voir comptabilite', 'Acces au module Comptabilite OHADA', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-PER5', 'Gérer les permissions', 'Gestion des permissions', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER50', 'Journal comptable', 'Consulter le journal des ecritures comptables', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-PER51', 'Plan comptable', 'Consulter le plan comptable OHADA', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-PER52', 'Grand livre', 'Consulter le grand livre comptable', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-PER53', 'Voir liste crédits', 'Accéder à la liste des dossiers crédit', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER54', 'Créer demande crédit', 'Saisir une nouvelle demande de crédit', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER55', 'Modifier demande brouillon', 'Modifier un dossier en statut BROUILLON', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER57', 'Voir détail dossier crédit', 'Consulter le détail complet d\'un dossier crédit', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER58', 'Saisir analyse crédit', 'Démarrer et saisir l\'analyse d\'un dossier crédit', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER59', 'Compléter analyse crédit', 'Marquer l\'analyse comme complète', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER6', 'Voir RH', 'Accès au module RH', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER60', 'Valider bloc Agent crédit', 'Validation niveau 1 – Chargé de crédit', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER61', 'Valider bloc Chargé opérations', 'Validation niveau 2 – Chargé des opérations', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER62', 'Valider bloc Contrôleur', 'Validation niveau 3 – Contrôleur interne', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER63', 'Valider bloc Gérant', 'Validation niveau 4 – Gérant / Directeur (validation finale)', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER64', 'Débloquer crédit', 'Effectuer le déblocage des fonds (PRET_A_DEBLOQUER → DEBLOQUE)', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER65', 'Enregistrer remboursement', 'Enregistrer un paiement d\'échéance ou remboursement', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER66', 'Annuler dossier crédit', 'Annuler définitivement un dossier crédit', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER67', 'Suspendre dossier crédit', 'Mettre un dossier en suspension temporaire', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER68', 'Signaler dossier suspect', 'Signaler un dossier comme suspect (fraude, irrégularité)', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER69', 'Lever suspension/suspicion', 'Lever une suspension ou un signalement de suspicion', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER7', 'Créer agent', 'Création d\'un nouvel agent', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER70', 'Tableau de bord crédit', 'Accéder au tableau de bord de supervision du crédit', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER71', 'Imprimer échéancier PDF', 'Générer et imprimer l\'échéancier de remboursement PDF', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER72', 'Historique audit dossier', 'Consulter le journal d\'audit complet d\'un dossier crédit', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-PER8', 'Modifier agent', 'Modification d\'un agent', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER9', 'Affectations', 'Gestion des affectations', '2026-03-06 12:02:52', '2026-03-06 12:02:52');

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

--
-- Déchargement des données de la table `tb_plan_comptable`
--

INSERT INTO `tb_plan_comptable` (`numero_compte`, `classe_ohada`, `libelle`, `parent_compte`, `niveau`, `type_compte`, `est_mouvementable`, `est_actif`) VALUES
('1', '1', 'Comptes de capitaux', NULL, 1, 'PASSIF', 0, 1),
('10', '1', 'Capital', '1', 2, 'PASSIF', 0, 1),
('101', '1', 'Capital social', '10', 3, 'PASSIF', 0, 1),
('1011', '1', 'Capital souscrit appele verse', '101', 4, 'PASSIF', 1, 1),
('102', '1', 'Capital souscrit non appele', '10', 3, 'PASSIF', 1, 1),
('103', '1', 'Capital souscrit appele non verse', '10', 3, 'PASSIF', 1, 1),
('104', '1', 'Primes liees au capital', '10', 3, 'PASSIF', 1, 1),
('105', '1', 'Ecarts de reevaluation', '10', 3, 'PASSIF', 1, 1),
('106', '1', 'Reserves', '10', 3, 'PASSIF', 1, 1),
('107', '1', 'Report a nouveau', '10', 3, 'PASSIF', 1, 1),
('108', '1', 'Resultat net en instance d affectation', '10', 3, 'PASSIF', 1, 1),
('109', '1', 'Actionnaires capital souscrit non appele', '10', 3, 'PASSIF', 1, 1),
('11', '1', 'Emprunts et dettes assimilees', '1', 2, 'PASSIF', 0, 1),
('12', '1', 'Dettes de location acquisition', '1', 2, 'PASSIF', 0, 1),
('13', '1', 'Provisions pour risques et charges', '1', 2, 'PASSIF', 0, 1),
('14', '1', 'Dettes financieres diverses', '1', 2, 'PASSIF', 0, 1),
('15', '1', 'Dettes rattachees a des participations', '1', 2, 'PASSIF', 0, 1),
('16', '1', 'Fonds affectes et subventions d investissement', '1', 2, 'PASSIF', 0, 1),
('17', '1', 'Autres fonds propres', '1', 2, 'PASSIF', 0, 1),
('18', '1', 'Comptes de liaison des etablissements', '1', 2, 'PASSIF', 0, 1),
('19', '1', 'Provisions financieres pour risques et charges', '1', 2, 'PASSIF', 0, 1),
('2', '2', 'Comptes d immobilisations', NULL, 1, 'ACTIF', 0, 1),
('20', '2', 'Charges immobilisees', '2', 2, 'ACTIF', 0, 1),
('21', '2', 'Immobilisations incorporelles', '2', 2, 'ACTIF', 0, 1),
('22', '2', 'Terrains', '2', 2, 'ACTIF', 0, 1),
('23', '2', 'Batiments installations techniques et agencements', '2', 2, 'ACTIF', 0, 1),
('24', '2', 'Materiel mobilier et actifs biologiques', '2', 2, 'ACTIF', 0, 1),
('25', '2', 'Avances et acomptes verses sur immobilisations', '2', 2, 'ACTIF', 0, 1),
('251', '2', 'Avances et acomptes sur immobilisations corporelles', '25', 3, 'ACTIF', 0, 1),
('2511', '2', 'Depots a vue clients', '251', 4, 'PASSIF', 1, 1),
('2512', '2', 'Depots a terme clients', '251', 4, 'PASSIF', 1, 1),
('26', '2', 'Titres de participation et autres immobilisations financieres', '2', 2, 'ACTIF', 0, 1),
('27', '2', 'Ecarts de conversion actif', '2', 2, 'ACTIF', 0, 1),
('28', '2', 'Amortissements', '2', 2, 'ACTIF', 0, 1),
('29', '2', 'Depreciations des immobilisations', '2', 2, 'ACTIF', 0, 1),
('3', '3', 'Comptes de stocks', NULL, 1, 'ACTIF', 0, 1),
('31', '3', 'Marchandises', '3', 2, 'ACTIF', 0, 1),
('32', '3', 'Matieres premieres et fournitures liees', '3', 2, 'ACTIF', 0, 1),
('33', '3', 'Autres approvisionnements', '3', 2, 'ACTIF', 0, 1),
('34', '3', 'Produits en cours', '3', 2, 'ACTIF', 0, 1),
('35', '3', 'Services en cours', '3', 2, 'ACTIF', 0, 1),
('36', '3', 'Produits finis', '3', 2, 'ACTIF', 0, 1),
('37', '3', 'Produits intermediaires et residuels', '3', 2, 'ACTIF', 0, 1),
('38', '3', 'Stocks en cours de route et en consignation', '3', 2, 'ACTIF', 0, 1),
('39', '3', 'Depreciations des stocks', '3', 2, 'ACTIF', 0, 1),
('4', '4', 'Comptes de tiers', NULL, 1, 'MIXTE', 0, 1),
('40', '4', 'Fournisseurs et comptes rattaches', '4', 2, 'MIXTE', 0, 1),
('41', '4', 'Clients et comptes rattaches', '4', 2, 'MIXTE', 0, 1),
('411', '4', 'Clients ordinaires', '41', 3, 'MIXTE', 0, 1),
('4111', '4', 'Comptes courants clients', '411', 4, 'PASSIF', 1, 1),
('4112', '4', 'Comptes epargne clients', '411', 4, 'PASSIF', 1, 1),
('412', '4', 'Clients effets a recevoir', '41', 3, 'MIXTE', 1, 1),
('42', '4', 'Personnel', '4', 2, 'MIXTE', 0, 1),
('43', '4', 'Organismes sociaux', '4', 2, 'MIXTE', 0, 1),
('44', '4', 'Etat et collectivites publiques', '4', 2, 'MIXTE', 0, 1),
('45', '4', 'Organismes internationaux', '4', 2, 'MIXTE', 0, 1),
('46', '4', 'Associes et groupe', '4', 2, 'MIXTE', 0, 1),
('47', '4', 'Debiteurs et crediteurs divers', '4', 2, 'MIXTE', 0, 1),
('471', '4', 'Comptes d attente', '47', 3, 'MIXTE', 0, 1),
('4711', '4', 'Compte transitoire operations de change', '471', 4, 'PASSIF', 1, 1),
('48', '4', 'Comptes de regularisation', '4', 2, 'MIXTE', 0, 1),
('49', '4', 'Depreciations et provisions des comptes de tiers', '4', 2, 'MIXTE', 0, 1),
('5', '5', 'Comptes de tresorerie', NULL, 1, 'ACTIF', 0, 1),
('50', '5', 'Titres de placement', '5', 2, 'ACTIF', 0, 1),
('51', '5', 'Valeurs a encaisser', '5', 2, 'ACTIF', 0, 1),
('52', '5', 'Banques etablissements financiers et assimiles', '5', 2, 'ACTIF', 0, 1),
('521', '5', 'Banques locales', '52', 3, 'ACTIF', 0, 1),
('5211', '5', 'Banque locale CDF', '521', 4, 'ACTIF', 1, 1),
('5212', '5', 'Banque locale USD', '521', 4, 'ACTIF', 1, 1),
('53', '5', 'Etablissements financiers et instruments monetaires', '5', 2, 'ACTIF', 0, 1),
('54', '5', 'Instruments de tresorerie', '5', 2, 'ACTIF', 0, 1),
('55', '5', 'Monnaie electronique', '5', 2, 'ACTIF', 0, 1),
('56', '5', 'Banques crediteurs', '5', 2, 'ACTIF', 0, 1),
('57', '5', 'Caisse', '5', 2, 'ACTIF', 0, 1),
('570', '5', 'Caisse principale', '57', 3, 'ACTIF', 0, 1),
('5701', '5', 'Caisse CDF', '570', 4, 'ACTIF', 1, 1),
('5702', '5', 'Caisse USD', '570', 4, 'ACTIF', 1, 1),
('5703', '5', 'Caisse EUR', '570', 4, 'ACTIF', 1, 1),
('58', '5', 'Virements internes', '5', 2, 'ACTIF', 0, 1),
('581', '5', 'Virements internes en cours', '58', 3, 'ACTIF', 0, 1),
('5811', '5', 'Virements internes en cours CDF', '581', 4, 'ACTIF', 1, 1),
('59', '5', 'Depreciations des comptes financiers', '5', 2, 'ACTIF', 0, 1),
('6', '6', 'Comptes de charges des activites ordinaires', NULL, 1, 'CHARGE', 0, 1),
('60', '6', 'Achats et variation de stocks', '6', 2, 'CHARGE', 0, 1),
('600', '6', 'Achats', '60', 3, 'CHARGE', 0, 1),
('6001', '6', 'Frais bancaires', '600', 4, 'CHARGE', 1, 1),
('61', '6', 'Transports', '6', 2, 'CHARGE', 0, 1),
('62', '6', 'Services exterieurs A', '6', 2, 'CHARGE', 0, 1),
('63', '6', 'Services exterieurs B', '6', 2, 'CHARGE', 0, 1),
('64', '6', 'Impots et taxes', '6', 2, 'CHARGE', 0, 1),
('65', '6', 'Autres charges', '6', 2, 'CHARGE', 0, 1),
('66', '6', 'Charges de personnel', '6', 2, 'CHARGE', 0, 1),
('67', '6', 'Frais financiers et charges assimilees', '6', 2, 'CHARGE', 0, 1),
('68', '6', 'Dotations aux amortissements provisions et depreciations', '6', 2, 'CHARGE', 0, 1),
('69', '6', 'Impots sur resultats', '6', 2, 'CHARGE', 0, 1),
('7', '7', 'Comptes de produits des activites ordinaires', NULL, 1, 'PRODUIT', 0, 1),
('70', '7', 'Ventes', '7', 2, 'PRODUIT', 0, 1),
('700', '7', 'Produits financiers courants', '70', 3, 'PRODUIT', 0, 1),
('7001', '7', 'Interets et produits assimiles', '700', 4, 'PRODUIT', 1, 1),
('701', '7', 'Ventes de produits finis', '70', 3, 'PRODUIT', 0, 1),
('702', '7', 'Ventes de produits intermediaires', '70', 3, 'PRODUIT', 0, 1),
('703', '7', 'Ventes de produits residuels', '70', 3, 'PRODUIT', 0, 1),
('704', '7', 'Travaux factures', '70', 3, 'PRODUIT', 0, 1),
('705', '7', 'Etudes facturees', '70', 3, 'PRODUIT', 0, 1),
('706', '7', 'Services vendus', '70', 3, 'PRODUIT', 0, 1),
('7061', '7', 'Commissions sur services bancaires', '706', 4, 'PRODUIT', 1, 1),
('707', '7', 'Produits accessoires', '70', 3, 'PRODUIT', 0, 1),
('7071', '7', 'Produits services guichet', '707', 4, 'PRODUIT', 1, 1),
('708', '7', 'Produits divers', '70', 3, 'PRODUIT', 0, 1),
('71', '7', 'Subventions d exploitation', '7', 2, 'PRODUIT', 0, 1),
('72', '7', 'Production immobilisee', '7', 2, 'PRODUIT', 0, 1),
('73', '7', 'Variations des stocks de biens et services produits', '7', 2, 'PRODUIT', 0, 1),
('74', '7', 'Produits divers', '7', 2, 'PRODUIT', 0, 1),
('75', '7', 'Transferts de charges', '7', 2, 'PRODUIT', 0, 1),
('76', '7', 'Revenus financiers et produits assimiles', '7', 2, 'PRODUIT', 0, 1),
('77', '7', 'Produits exceptionnels', '7', 2, 'PRODUIT', 0, 1),
('78', '7', 'Reprises de provisions et amortissements', '7', 2, 'PRODUIT', 0, 1),
('79', '7', 'Transferts de produits', '7', 2, 'PRODUIT', 0, 1),
('8', '8', 'Comptes des autres charges et autres produits', NULL, 1, 'HORS_BILAN', 0, 1),
('81', '8', 'Valeurs comptables des cessions d immobilisations', '8', 2, 'HORS_BILAN', 0, 1),
('82', '8', 'Produits des cessions d immobilisations', '8', 2, 'HORS_BILAN', 0, 1),
('83', '8', 'Charges hors activites ordinaires', '8', 2, 'HORS_BILAN', 0, 1),
('84', '8', 'Produits hors activites ordinaires', '8', 2, 'HORS_BILAN', 0, 1),
('85', '8', 'Dotations hors activites ordinaires', '8', 2, 'HORS_BILAN', 0, 1),
('86', '8', 'Reprises hors activites ordinaires', '8', 2, 'HORS_BILAN', 0, 1),
('87', '8', 'Participations des travailleurs', '8', 2, 'HORS_BILAN', 0, 1),
('88', '8', 'Subventions d equilibre', '8', 2, 'HORS_BILAN', 0, 1),
('89', '8', 'Bilan ouverture et cloture', '8', 2, 'HORS_BILAN', 0, 1);

-- --------------------------------------------------------

--
-- Structure de la table `tb_portefeuilles_agents`
--

DROP TABLE IF EXISTS `tb_portefeuilles_agents`;
CREATE TABLE IF NOT EXISTS `tb_portefeuilles_agents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `agent_matricule` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_portefeuille` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taux_commission_agent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_port_agent` (`agent_matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_portefeuilles_agents`
--

INSERT INTO `tb_portefeuilles_agents` (`id`, `agent_matricule`, `nom_portefeuille`, `taux_commission_agent`, `created_at`) VALUES
(1, 'AG-EBENKGA-26-00002', 'Portefeuille', 5.00, '2026-03-07 07:48:58');

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_postes`
--

INSERT INTO `tb_postes` (`id`, `service_id`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Administrateur Système', 'Poste réservé au compte administrateur du système', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
(2, 3, 'Guichitier', NULL, '2026-03-06 13:54:14', '2026-03-06 13:54:14'),
(4, 3, 'Chef Trésorier', 'Responsable du coffre-fort central et des flux inter-caisses', '2026-03-07 10:32:25', '2026-03-07 10:32:25');

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

--
-- Déchargement des données de la table `tb_roles`
--

INSERT INTO `tb_roles` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
('EBEN-ROL1', 'Administrateur', 'Accès total au système', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
('EBEN-ROL2', 'Caissier', 'Gestion caisse et guichet', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
('EBEN-ROL3', 'Directeur', 'Supervision générale', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
('EBEN-ROL4', 'Agent RH', 'Gestion des ressources humaines', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
('EBEN-ROL5', 'Superviseur', 'Supervision opérationnelle', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
('EBEN-ROL6', 'Chargé de crédit', 'Gestion complète des dossiers crédit, épargne et comptes clients', '2026-03-06 11:36:28', '2026-03-06 11:36:28'),
('EBEN-ROL7', 'Comptable', 'Comptabilité, rapports financiers, validation des écritures', '2026-03-06 11:36:28', '2026-03-06 11:36:28'),
('EBEN-ROL8', 'Trésorier', 'Gestion complète du coffre-fort central, approvisionnements et transferts', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL9', 'Agent Commercial', NULL, '2026-03-13 13:51:24', '2026-03-13 13:51:24');

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

--
-- Déchargement des données de la table `tb_role_permission`
--

INSERT INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
('EBEN-ROL1', 'EBEN-PER1', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER10', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER11', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER12', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER13', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER14', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER15', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER16', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER17', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER18', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER19', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER2', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER20', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER21', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER22', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER23', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER24', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER25', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER26', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER27', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER28', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER29', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER3', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER30', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER31', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER32', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER33', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER34', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER35', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER36', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER37', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER38', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER39', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER4', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER40', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER41', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER42', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER43', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER44', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL1', 'EBEN-PER45', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL1', 'EBEN-PER46', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL1', 'EBEN-PER47', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL1', 'EBEN-PER48', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL1', 'EBEN-PER49', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-ROL1', 'EBEN-PER5', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER50', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-ROL1', 'EBEN-PER51', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-ROL1', 'EBEN-PER52', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-ROL1', 'EBEN-PER53', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER54', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER55', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER57', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER58', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER59', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER6', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER60', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER61', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER62', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER63', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER64', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER65', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER66', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER67', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER68', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER69', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER7', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER70', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER71', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER72', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL1', 'EBEN-PER8', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER9', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER10', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER11', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER12', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER13', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER14', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER15', '2026-03-18 08:26:40', '2026-03-18 08:26:40'),
('EBEN-ROL2', 'EBEN-PER20', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER21', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER22', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER23', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER24', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER25', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER26', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER27', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER28', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER29', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER30', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER31', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER32', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER33', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER34', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER35', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER36', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER37', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER38', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER65', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL3', 'EBEN-PER1', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER10', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER15', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER18', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER2', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER20', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER26', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER27', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER30', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER33', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER36', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER37', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER38', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER39', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER4', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER42', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER44', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL3', 'EBEN-PER48', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL3', 'EBEN-PER49', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-ROL3', 'EBEN-PER50', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-ROL3', 'EBEN-PER52', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-ROL3', 'EBEN-PER53', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL3', 'EBEN-PER57', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL3', 'EBEN-PER6', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL3', 'EBEN-PER63', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL3', 'EBEN-PER66', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL3', 'EBEN-PER67', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL3', 'EBEN-PER68', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL3', 'EBEN-PER69', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL3', 'EBEN-PER70', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL3', 'EBEN-PER71', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL3', 'EBEN-PER72', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL4', 'EBEN-PER6', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL4', 'EBEN-PER7', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL4', 'EBEN-PER8', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL4', 'EBEN-PER9', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL5', 'EBEN-PER10', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL5', 'EBEN-PER15', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL5', 'EBEN-PER18', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL5', 'EBEN-PER2', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL5', 'EBEN-PER20', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL5', 'EBEN-PER26', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL5', 'EBEN-PER27', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL5', 'EBEN-PER30', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL5', 'EBEN-PER36', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL5', 'EBEN-PER37', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL5', 'EBEN-PER38', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL5', 'EBEN-PER42', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL5', 'EBEN-PER44', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL5', 'EBEN-PER48', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL5', 'EBEN-PER49', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-ROL5', 'EBEN-PER50', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-ROL5', 'EBEN-PER52', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-ROL5', 'EBEN-PER53', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL5', 'EBEN-PER57', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL5', 'EBEN-PER6', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL5', 'EBEN-PER62', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL5', 'EBEN-PER67', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL5', 'EBEN-PER68', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL5', 'EBEN-PER70', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL5', 'EBEN-PER71', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL5', 'EBEN-PER72', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL6', 'EBEN-PER15', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL6', 'EBEN-PER16', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL6', 'EBEN-PER18', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL6', 'EBEN-PER19', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL6', 'EBEN-PER27', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL6', 'EBEN-PER28', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL6', 'EBEN-PER29', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL6', 'EBEN-PER30', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL6', 'EBEN-PER31', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL6', 'EBEN-PER32', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL6', 'EBEN-PER34', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL6', 'EBEN-PER35', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL6', 'EBEN-PER53', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL6', 'EBEN-PER54', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL6', 'EBEN-PER55', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL6', 'EBEN-PER57', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL6', 'EBEN-PER58', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL6', 'EBEN-PER59', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL6', 'EBEN-PER60', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL6', 'EBEN-PER61', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL6', 'EBEN-PER64', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL6', 'EBEN-PER65', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL6', 'EBEN-PER70', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL6', 'EBEN-PER71', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL7', 'EBEN-PER18', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL7', 'EBEN-PER37', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL7', 'EBEN-PER38', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL7', 'EBEN-PER39', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL7', 'EBEN-PER40', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL7', 'EBEN-PER41', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL8', 'EBEN-PER10', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL8', 'EBEN-PER15', '2026-03-08 20:06:05', '2026-03-08 20:06:05'),
('EBEN-ROL8', 'EBEN-PER20', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL8', 'EBEN-PER36', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL8', 'EBEN-PER37', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL8', 'EBEN-PER38', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL8', 'EBEN-PER44', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL8', 'EBEN-PER45', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL8', 'EBEN-PER46', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL8', 'EBEN-PER47', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL8', 'EBEN-PER48', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL8', 'EBEN-PER49', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-ROL8', 'EBEN-PER50', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-ROL8', 'EBEN-PER51', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-ROL8', 'EBEN-PER52', '2026-03-13 20:17:13', '2026-03-13 20:17:13'),
('EBEN-ROL8', 'EBEN-PER53', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL8', 'EBEN-PER54', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL8', 'EBEN-PER55', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL8', 'EBEN-PER57', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL8', 'EBEN-PER71', '2026-03-16 21:20:37', '2026-03-16 21:20:37'),
('EBEN-ROL9', 'EBEN-PER10', '2026-03-13 13:51:45', '2026-03-13 13:51:45'),
('EBEN-ROL9', 'EBEN-PER11', '2026-03-13 13:51:48', '2026-03-13 13:51:48'),
('EBEN-ROL9', 'EBEN-PER12', '2026-03-13 13:51:51', '2026-03-13 13:51:51'),
('EBEN-ROL9', 'EBEN-PER13', '2026-03-13 13:51:51', '2026-03-13 13:51:51'),
('EBEN-ROL9', 'EBEN-PER14', '2026-03-13 13:51:51', '2026-03-13 13:51:51'),
('EBEN-ROL9', 'EBEN-PER15', '2026-03-13 13:53:48', '2026-03-13 13:53:48'),
('EBEN-ROL9', 'EBEN-PER18', '2026-03-13 13:53:53', '2026-03-13 13:53:53'),
('EBEN-ROL9', 'EBEN-PER22', '2026-03-13 13:55:03', '2026-03-13 13:55:03'),
('EBEN-ROL9', 'EBEN-PER23', '2026-03-13 13:55:03', '2026-03-13 13:55:03'),
('EBEN-ROL9', 'EBEN-PER24', '2026-03-13 13:55:03', '2026-03-13 13:55:03'),
('EBEN-ROL9', 'EBEN-PER25', '2026-03-13 13:55:03', '2026-03-13 13:55:03'),
('EBEN-ROL9', 'EBEN-PER26', '2026-03-13 13:55:03', '2026-03-13 13:55:03'),
('EBEN-ROL9', 'EBEN-PER30', '2026-03-13 13:54:50', '2026-03-13 13:54:50'),
('EBEN-ROL9', 'EBEN-PER31', '2026-03-13 13:54:50', '2026-03-13 13:54:50'),
('EBEN-ROL9', 'EBEN-PER32', '2026-03-13 13:54:50', '2026-03-13 13:54:50'),
('EBEN-ROL9', 'EBEN-PER33', '2026-03-13 13:54:50', '2026-03-13 13:54:50'),
('EBEN-ROL9', 'EBEN-PER34', '2026-03-13 13:54:50', '2026-03-13 13:54:50'),
('EBEN-ROL9', 'EBEN-PER35', '2026-03-13 13:54:50', '2026-03-13 13:54:50');

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_role_user`
--

INSERT INTO `tb_role_user` (`id`, `user_id`, `role_code`, `created_at`, `updated_at`) VALUES
(1, 1, 'EBEN-ROL1', '2026-03-06 09:09:58', '2026-03-06 09:09:58'),
(2, 2, 'EBEN-ROL2', '2026-03-06 11:11:41', '2026-03-06 11:11:41'),
(4, 2, 'EBEN-ROL2', '2026-03-06 11:36:28', '2026-03-06 11:36:28'),
(6, 3, 'EBEN-ROL4', NULL, NULL),
(7, 7, 'EBEN-ROL8', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
(8, 8, 'EBEN-ROL9', NULL, NULL),
(9, 9, 'EBEN-ROL2', NULL, NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_services`
--

INSERT INTO `tb_services` (`id`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Direction Générale', 'Administration centrale du système', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
(3, 'Caisse', NULL, '2026-03-06 13:53:33', '2026-03-06 13:53:33');

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_taux_echanges`
--

INSERT INTO `tb_taux_echanges` (`id`, `devise_source`, `devise_destination`, `taux`, `date_application`, `created_at`, `updated_at`) VALUES
(3, 'USD', 'CDF', 2500.0000, '2026-03-07 08:02:30', '2026-03-07 07:02:30', '2026-03-07 07:02:30'),
(4, 'CDF', 'USD', 0.0004, '2026-03-07 08:02:30', '2026-03-07 07:02:30', '2026-03-07 07:02:30');

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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_transactions`
--

INSERT INTO `tb_transactions` (`id`, `compte_code`, `agent_matricule`, `guichet_id`, `devise_code`, `devise_dest`, `montant_dest`, `taux_change`, `observations`, `statut`, `date_operation`, `type`, `montant`, `montant_commission_total`, `solde_compte_avant`, `solde_compte_apres`, `montant_total_client`, `montant_net_client`, `reference`, `created_at`, `updated_at`) VALUES
(1, 'CMPT-EBENKGA-26-GWGR-00001', 'AG-EBENKGA-26-00002', 14, 'CDF', NULL, NULL, NULL, 'Dépot', 'CONFIRME', '2026-03-08 18:28:58', 'DEPOT', 10000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260308-192858-AG-E', '2026-03-08 18:28:58', '2026-03-08 18:28:58'),
(2, 'CMPT-EBENKGA-26-GWGR-00001', 'AG-EBENKGA-26-00002', 14, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-08 18:31:49', 'RETRAIT', 1000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260308-193149-AG-E', '2026-03-08 18:31:49', '2026-03-08 18:31:49'),
(3, 'CMPT-EBENKGA-26-GWGR-00001', 'AG-EBENKGA-26-00002', 14, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-08 18:52:18', 'RETRAIT', 1500.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260308-195218-AG-E', '2026-03-08 18:52:18', '2026-03-08 18:52:18'),
(4, 'CMPT-EBENKGA-26-GWGR-00001', 'AG-EBENKGA-26-00002', 14, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-08 18:54:49', 'RETRAIT', 1500.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260308-195449-AG-E', '2026-03-08 18:54:49', '2026-03-08 18:54:49'),
(5, 'CMPT-EBENKGA-26-GWGR-00001', 'AG-EBENKGA-26-00002', 14, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-08 19:10:23', 'RETRAIT', 1500.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260308-201023-AG-E', '2026-03-08 19:10:23', '2026-03-08 19:10:23'),
(6, 'CMPT-EBENKGA-26-GWGR-00001', 'AG-EBENKGA-26-00002', 14, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-08 19:10:40', 'DEPOT', 5000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260308-201040-AG-E', '2026-03-08 19:10:40', '2026-03-08 19:10:40'),
(7, 'CMPT-EBENKGA-26-VM3R-00002', 'AG-EBENKGA-26-00002', 14, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-10 19:14:34', 'DEPOT', 50000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260310-201434-AG-E', '2026-03-10 19:14:34', '2026-03-10 19:14:34'),
(8, '243-52514-EAV-00001REB', 'AG-EBENKGA-26-00002', 14, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-10 21:33:30', 'DEPOT', 50000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260310-223330-AG-E', '2026-03-10 21:33:30', '2026-03-10 21:33:30'),
(9, '243-52514-EAV-00001REB', 'AG-EBENKGA-26-00002', 14, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-10 21:33:59', 'RETRAIT', 10000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260310-223359-AG-E', '2026-03-10 21:33:59', '2026-03-10 21:33:59'),
(10, '243-52514-EAV-00001REB', 'AG-EBENKGA-26-00002', 14, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-13 13:30:49', 'DEPOT', 50000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260313-143049-AG-E', '2026-03-13 13:30:49', '2026-03-13 13:30:49'),
(11, '243-52514-EAV-00002GKT', 'AG-EBENKGA-26-00006', 17, 'USD', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-13 14:19:06', 'DEPOT', 5000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260313-151906-AG-E', '2026-03-13 14:19:06', '2026-03-13 14:19:06'),
(12, '243-52514-CC-00002EXI', 'AG-EBENKGA-26-00006', 17, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-13 17:44:28', 'DEPOT', 5000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260313-184428-AG-E', '2026-03-13 17:44:28', '2026-03-13 17:44:28'),
(13, '243-52514-CC-00002EXI', 'AG-EBENKGA-26-00006', 17, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-13 17:52:23', 'DEPOT', 50000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260313-185223-AG-E', '2026-03-13 17:52:23', '2026-03-13 17:52:23'),
(14, '243-52514-EAV-00002GKT', 'AG-EBENKGA-26-00006', 17, 'USD', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-13 17:52:53', 'DEPOT', 50000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260313-185253-AG-E', '2026-03-13 17:52:53', '2026-03-13 17:52:53'),
(15, '243-52514-CC-00002EXI', 'AG-EBENKGA-26-00002', 14, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-14 11:13:58', 'RETRAIT', 5000.00, 0.00, 55000.00, 50000.00, 5000.00, -5000.00, 'OP-20260314-121358-AG-E', '2026-03-14 11:13:58', '2026-03-14 11:13:58'),
(16, '243-52514-CC-00002EXI', 'AG-EBENKGA-26-00002', 14, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-14 11:50:25', 'RETRAIT', 5000.00, 0.00, 50000.00, 45000.00, 5000.00, -5000.00, 'OP-20260314-125025-AG-E', '2026-03-14 11:50:25', '2026-03-14 11:50:25'),
(17, '243-52514-EAV-00002GKT', 'AG-EBENKGA-26-00007', 18, 'USD', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-14 11:58:54', 'DEPOT', 5000.00, 0.00, 55000.00, 60000.00, 5000.00, 5000.00, 'OP-20260314-125854-AG-E', '2026-03-14 11:58:54', '2026-03-14 11:58:54'),
(18, '243-52514-EAV-00002GKT', 'AG-EBENKGA-26-00007', 18, 'USD', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-14 12:00:00', 'RETRAIT', 500.00, 10.00, 60000.00, 59490.00, 510.00, -510.00, 'OP-20260314-130000-AG-E', '2026-03-14 12:00:00', '2026-03-14 12:00:00'),
(19, '243-52514-CC-00002EXI', 'AG-EBENKGA-26-00002', 14, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-14 14:46:33', 'DEPOT', 5000.00, 0.00, 45000.00, 50000.00, 5000.00, 5000.00, 'OP-20260314-154633-AG-E', '2026-03-14 14:46:33', '2026-03-14 14:46:33'),
(20, '243-52514-CC-00002EXI', 'AG-EBENKGA-26-00007', 18, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-17 07:51:49', 'DEPOT', 500.00, 0.00, 50000.00, 50500.00, 500.00, 500.00, 'OP-20260317-085148-AG-E', '2026-03-17 07:51:49', '2026-03-17 07:51:49');

-- --------------------------------------------------------

--
-- Structure de la table `tb_transaction_commissions`
--

DROP TABLE IF EXISTS `tb_transaction_commissions`;
CREATE TABLE IF NOT EXISTS `tb_transaction_commissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint UNSIGNED NOT NULL,
  `commission_rule_id` bigint UNSIGNED DEFAULT NULL,
  `libelle` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_operation` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_compte` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_guichet` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `devise_code` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_zone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portefeuille_id` bigint UNSIGNED DEFAULT NULL,
  `mode_calcul` enum('FIXE','POURCENTAGE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur_snapshot` decimal(18,4) NOT NULL,
  `base_calcul` decimal(18,2) NOT NULL DEFAULT '0.00',
  `montant_commission` decimal(18,2) NOT NULL DEFAULT '0.00',
  `date_application` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `agent_matricule` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guichet_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_trans_comm_tx_date` (`transaction_id`,`date_application`),
  KEY `idx_trans_comm_scope` (`code_operation`,`type_compte`,`type_guichet`),
  KEY `tb_trans_comm_rule_fk` (`commission_rule_id`),
  KEY `tb_trans_comm_zone_fk` (`code_zone`),
  KEY `tb_trans_comm_portefeuille_fk` (`portefeuille_id`),
  KEY `tb_trans_comm_guichet_fk` (`guichet_id`),
  KEY `tb_trans_comm_agent_fk` (`agent_matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_transaction_commissions`
--

INSERT INTO `tb_transaction_commissions` (`id`, `transaction_id`, `commission_rule_id`, `libelle`, `code_operation`, `type_compte`, `type_guichet`, `devise_code`, `code_zone`, `portefeuille_id`, `mode_calcul`, `valeur_snapshot`, `base_calcul`, `montant_commission`, `date_application`, `agent_matricule`, `guichet_id`, `created_at`, `updated_at`) VALUES
(1, 15, NULL, 'Aucune regle de commission applicable', 'RETRAIT', 'CC', 'FIXE', 'CDF', 'ZON-EBENKGA-26-00003', NULL, 'FIXE', 0.0000, 5000.00, 0.00, '2026-03-14 11:13:58', 'AG-EBENKGA-26-00002', 14, '2026-03-14 11:13:58', '2026-03-14 11:13:58'),
(2, 16, NULL, 'Aucune regle de commission applicable', 'RETRAIT', 'CC', 'FIXE', 'CDF', 'ZON-EBENKGA-26-00003', NULL, 'FIXE', 0.0000, 5000.00, 0.00, '2026-03-14 11:50:25', 'AG-EBENKGA-26-00002', 14, '2026-03-14 11:50:25', '2026-03-14 11:50:25'),
(3, 17, NULL, 'Aucune regle de commission applicable', 'DEPOT', 'EAV', 'FIXE', 'USD', 'ZON-EBENKGA-26-00003', NULL, 'FIXE', 0.0000, 5000.00, 0.00, '2026-03-14 11:58:54', 'AG-EBENKGA-26-00007', 18, '2026-03-14 11:58:54', '2026-03-14 11:58:54'),
(4, 18, 1, 'Fais de retrais', 'RETRAIT', 'EAV', 'FIXE', 'USD', 'ZON-EBENKGA-26-00003', NULL, 'POURCENTAGE', 2.0000, 500.00, 10.00, '2026-03-14 12:00:00', 'AG-EBENKGA-26-00007', 18, '2026-03-14 12:00:00', '2026-03-14 12:00:00'),
(5, 19, NULL, 'Aucune regle de commission applicable', 'DEPOT', 'CC', 'FIXE', 'CDF', 'ZON-EBENKGA-26-00003', NULL, 'FIXE', 0.0000, 5000.00, 0.00, '2026-03-14 14:46:33', 'AG-EBENKGA-26-00002', 14, '2026-03-14 14:46:33', '2026-03-14 14:46:33'),
(6, 20, NULL, 'Aucune regle de commission applicable', 'DEPOT', 'CC', 'FIXE', 'CDF', 'ZON-EBENKGA-26-00003', NULL, 'FIXE', 0.0000, 500.00, 0.00, '2026-03-17 07:51:49', 'AG-EBENKGA-26-00007', 18, '2026-03-17 07:51:49', '2026-03-17 07:51:49');

-- --------------------------------------------------------

--
-- Structure de la table `tb_zones`
--

DROP TABLE IF EXISTS `tb_zones`;
CREATE TABLE IF NOT EXISTS `tb_zones` (
  `code_zone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `agent_commercial_matricule` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commune` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code_zone`),
  KEY `tb_zones_ibfk_1` (`agent_commercial_matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_zones`
--

INSERT INTO `tb_zones` (`code_zone`, `nom`, `agent_commercial_matricule`, `commune`, `created_at`, `updated_at`) VALUES
('ZON-EBENKGA-26-00001', 'Nganza Nord', 'AG-EBENKGA-26-00002', 'Nganza', '2026-03-07 06:47:17', '2026-03-07 06:47:17'),
('ZON-EBENKGA-26-00002', 'Nganza Sud', 'AG-EBENKGA-26-00003', 'Nganza', '2026-03-07 06:47:35', '2026-03-07 06:47:35'),
('ZON-EBENKGA-26-00003', 'Zone  Batetela', 'AG-EBENKGA-26-00006', 'Kanange', '2026-03-13 14:11:04', '2026-03-13 14:11:04');

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `agent_matricule`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `etat`, `created_at`, `updated_at`) VALUES
(1, 'AG-EBENKGA-26-00001', 'bmb', 'bmb@bmb.cd', '2026-03-06 09:09:58', '$2y$12$b1CZZgerOu0kbKn5pnzuaOY5gffx18oF2PVj5mhGyw.d53G8XYqay', NULL, 'actif', '2026-03-06 09:09:58', '2026-03-06 09:09:58'),
(2, 'AG-EBENKGA-26-00002', 'jean_caissier', 'jean.caissier@bmb.cd', '2026-03-06 11:11:41', '$2y$12$o/m3X.G8ImNrB8WgzrankOb5R8trQnBbSwK4vVzYXa7WHjy6AIIfG', NULL, 'actif', '2026-03-06 11:11:41', '2026-03-06 11:11:41'),
(3, 'AG-EBENKGA-26-00003', 'marie_rh', 'marie.rh@bmb.cd', '2026-03-06 11:11:41', '$2y$12$j8T6Zy8w4q.zzZ1eNt/eeuak5l4H/PdUWwz7rmUNqNKJL7UQ7PR4m', NULL, 'actif', '2026-03-06 11:11:41', '2026-03-06 11:11:41'),
(6, 'AG-EBENKGA-26-00005', 'mp', 'mputu@gmail.com', NULL, '$2y$12$A3T4Dsa2LJSqmsv3h9/5f.Io2a0A.KFx36XZTJta4GZuMU11obRJu', NULL, 'actif', '2026-03-07 07:28:59', '2026-03-07 07:28:59'),
(7, 'AG-EBENKGA-26-00004', 'tresorier', 'tresorier@bmb.cd', '2026-03-07 10:32:25', '$2y$12$4B7/8qHDP9b7ACLK4/EEs.hkZOirTH4KPTTiu8At5cyiE/O.mbWi6', NULL, 'actif', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
(8, 'AG-EBENKGA-26-00006', 'kal', 'kalala@gmail.com', NULL, '$2y$12$4owapzTW08fzlrf3ifApXOir9gcNMTMzt4Py.Swh/xVB70GmwaBbK', NULL, 'actif', '2026-03-13 13:50:46', '2026-03-13 13:50:46'),
(9, 'AG-EBENKGA-26-00007', 'kp', 'aaa@gmail.com', NULL, '$2y$12$D.agdsCr99TgKl14dpxyju5Di9pgbOrSy0bJISlLlM9fuVQta5q2e', NULL, 'actif', '2026-03-14 11:55:54', '2026-03-14 11:55:54');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `tb_affectations`
--
ALTER TABLE `tb_affectations`
  ADD CONSTRAINT `fk_affectation_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_affectations_agent_matricule_foreign` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_affectations_poste_id_foreign` FOREIGN KEY (`poste_id`) REFERENCES `tb_postes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_caisses_guichets_soldes`
--
ALTER TABLE `tb_caisses_guichets_soldes`
  ADD CONSTRAINT `fk_solde_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_solde_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_clients`
--
ALTER TABLE `tb_clients`
  ADD CONSTRAINT `tb_zones_ibfk_11` FOREIGN KEY (`code_zone`) REFERENCES `tb_zones` (`code_zone`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `tb_cloture_caisse`
--
ALTER TABLE `tb_cloture_caisse`
  ADD CONSTRAINT `fk_cloture_agent` FOREIGN KEY (`agent_cloturant`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cloture_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_cloture_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `tb_cloture_caisse_ibfk_1` FOREIGN KEY (`validateur_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `tb_commission_rules`
--
ALTER TABLE `tb_commission_rules`
  ADD CONSTRAINT `tb_agent` FOREIGN KEY (`created_by_agent`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `tb_comm_rules_devise_fk` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_comm_rules_portefeuille_fk` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_comm_rules_zone_fk` FOREIGN KEY (`code_zone`) REFERENCES `tb_zones` (`code_zone`) ON DELETE SET NULL;

--
-- Contraintes pour la table `tb_compta_ecritures`
--
ALTER TABLE `tb_compta_ecritures`
  ADD CONSTRAINT `fk_compta_ecriture_compte` FOREIGN KEY (`numero_compte`) REFERENCES `tb_plan_comptable` (`numero_compte`) ON DELETE RESTRICT ON UPDATE CASCADE,
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
  ADD CONSTRAINT `fk_compte_devise` FOREIGN KEY (`devise`) REFERENCES `tb_devises` (`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_compte_portefeuille` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `tb_comptes_ibfk_112` FOREIGN KEY (`client_matricule`) REFERENCES `tb_clients` (`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT;

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
  ADD CONSTRAINT `tb_credit_deblocages_compte_credit_id_foreign` FOREIGN KEY (`compte_credit_id`) REFERENCES `tb_comptes` (`code_compte`) ON DELETE RESTRICT,
  ADD CONSTRAINT `tb_credit_deblocages_compte_debit_id_foreign` FOREIGN KEY (`compte_debit_id`) REFERENCES `tb_comptes` (`code_compte`) ON DELETE RESTRICT,
  ADD CONSTRAINT `tb_credit_deblocages_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes` (`id`) ON DELETE RESTRICT;

--
-- Contraintes pour la table `tb_credit_demandes`
--
ALTER TABLE `tb_credit_demandes`
  ADD CONSTRAINT `tb_credit_demandes_client_matricule_foreign` FOREIGN KEY (`client_matricule`) REFERENCES `tb_clients` (`matricule`) ON DELETE RESTRICT,
  ADD CONSTRAINT `tb_credit_demandes_code_zone_foreign` FOREIGN KEY (`code_zone`) REFERENCES `tb_zones` (`code_zone`) ON DELETE RESTRICT,
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
  ADD CONSTRAINT `tb_credit_remboursements_compte_id_foreign` FOREIGN KEY (`compte_id`) REFERENCES `tb_comptes` (`code_compte`) ON DELETE RESTRICT,
  ADD CONSTRAINT `tb_credit_remboursements_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes` (`id`) ON DELETE RESTRICT,
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
  ADD CONSTRAINT `tb_demandes_modification_guichet_id_foreign` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_demandes_modification_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `tb_transactions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tb_mouvements_inter_caisses`
--
ALTER TABLE `tb_mouvements_inter_caisses`
  ADD CONSTRAINT `fk_mouv_agent` FOREIGN KEY (`agent_initiateur`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mouv_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_mouv_guichet_dest` FOREIGN KEY (`guichet_dest_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_mouv_guichet_src` FOREIGN KEY (`guichet_source_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `tb_mouvements_inter_caisses_ibfk_1` FOREIGN KEY (`validateur_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `tb_portefeuilles_agents`
--
ALTER TABLE `tb_portefeuilles_agents`
  ADD CONSTRAINT `fk_port_agent` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_postes`
--
ALTER TABLE `tb_postes`
  ADD CONSTRAINT `tb_postes_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `tb_services` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_role_permission`
--
ALTER TABLE `tb_role_permission`
  ADD CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_code`) REFERENCES `tb_permissions` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_code`) REFERENCES `tb_roles` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_role_user`
--
ALTER TABLE `tb_role_user`
  ADD CONSTRAINT `contrainte_role` FOREIGN KEY (`role_code`) REFERENCES `tb_roles` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `contrainte_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_taux_echanges`
--
ALTER TABLE `tb_taux_echanges`
  ADD CONSTRAINT `fk_devise_dest` FOREIGN KEY (`devise_destination`) REFERENCES `tb_devises` (`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_devise_src` FOREIGN KEY (`devise_source`) REFERENCES `tb_devises` (`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `tb_transactions`
--
ALTER TABLE `tb_transactions`
  ADD CONSTRAINT `tb_transactions_guichet_fk` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_transactions_ibfk_1` FOREIGN KEY (`compte_code`) REFERENCES `tb_comptes` (`code_compte`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `tb_transactions_ibfk_2` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT;

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
  ADD CONSTRAINT `tb_zones_ibfk_1` FOREIGN KEY (`agent_commercial_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_agent_matricule` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
