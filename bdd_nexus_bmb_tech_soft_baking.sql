-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 23 fév. 2026 à 13:41
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
  `key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `uuid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `queue` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('cGTbfDRmHEQ3rMaEjcr1VS11D5L4qjLswhVFb1Xp', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQWtucm5FMmtLdFFuQkdkaHVxMEdsalRTbUo5Ym16QVN2Sk8xNE9EOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6ODQ6Imh0dHA6Ly9sb2NhbGhvc3QvTmV4dXMtQk1CLVRlY2gtc29mdC1iYW5raW5nL3B1YmxpYy9hZG1pbmlzdHJhdGlvbi9yb2xlcy1wZXJtaXNzaW9ucyI7czo1OiJyb3V0ZSI7czozMjoiYWRtaW5pc3RyYXRpb24ucm9sZXNfcGVybWlzc2lvbnMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771854052);

-- --------------------------------------------------------

--
-- Structure de la table `tb_affectations`
--

DROP TABLE IF EXISTS `tb_affectations`;
CREATE TABLE IF NOT EXISTS `tb_affectations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `agent_matricule` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `poste_id` bigint UNSIGNED NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `Etat` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tb_affectations_agent_matricule_foreign` (`agent_matricule`),
  KEY `tb_affectations_poste_id_foreign` (`poste_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_affectations`
--

INSERT INTO `tb_affectations` (`id`, `agent_matricule`, `poste_id`, `date_debut`, `date_fin`, `created_at`, `updated_at`, `Etat`) VALUES
(1, 'AG-EBENKGA-26-00002', 2, '2026-02-12', '2026-03-08', '2026-02-22 00:00:37', '2026-02-22 00:00:37', ''),
(2, 'AG-EBENKGA-26-00002', 2, '2026-02-04', '2026-02-06', '2026-02-22 00:03:41', '2026-02-22 00:03:41', '');

-- --------------------------------------------------------

--
-- Structure de la table `tb_agents`
--

DROP TABLE IF EXISTS `tb_agents`;
CREATE TABLE IF NOT EXISTS `tb_agents` (
  `matricule` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `postnom` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prenom` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexe` enum('M','F') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `telephone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_embauche` date DEFAULT NULL,
  `statut` enum('actif','inactif') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_agents`
--

INSERT INTO `tb_agents` (`matricule`, `nom`, `postnom`, `prenom`, `sexe`, `date_naissance`, `telephone`, `email`, `adresse`, `photo`, `date_embauche`, `statut`, `created_at`, `updated_at`) VALUES
('AG-EBENKGA-26-00001', 'KAPUKUA', 'KAPUKUA', 'Jean', 'M', '1999-05-04', '0992463511', 'blaisemubadi2019@gmail.com', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', 'agents/1771853368_img-20251129-wa0026jpg', '2026-02-05', 'actif', '2026-02-23 12:29:28', '2026-02-23 12:29:28'),
('AG-EBENKGA-26-00002', 'KABUE', 'NTUMBA', 'Joel', 'F', '1995-01-31', '+21', 'christophetshibangu117@gmail.com', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', 'agents/1771284216_1767056067186jpg', '2025-06-05', 'actif', '2026-02-16 20:23:36', '2026-02-16 20:23:36'),
('AG-EBENKGA-26-00003', 'NTAMBUE', 'NTUMBUE', 'Sylvain', 'M', '1997-02-28', '0992463511', 'blaisemubadi2020@gmail.com', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', 'agents/1771853469_nicejpg', '2026-02-05', 'actif', '2026-02-23 12:31:09', '2026-02-23 12:31:09');

-- --------------------------------------------------------

--
-- Structure de la table `tb_clients`
--

DROP TABLE IF EXISTS `tb_clients`;
CREATE TABLE IF NOT EXISTS `tb_clients` (
  `matricule` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `postnom` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexe` enum('M','F') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_naissance` date NOT NULL,
  `lieu_naissance` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `etat_civil` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_conjoint` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_piece_identite` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lieu_delivrance_piece` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_delivrance_piece` date NOT NULL,
  `numero_piece_identite` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secteur_activite` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_activite` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom_entreprise` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse_entreprise` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone_entreprise` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut_entreprise` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_annees_experience` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revenu_mensuel` decimal(15,2) DEFAULT NULL,
  `revenu_mensuel_devise` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `autres_details_activite` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `clients_matricule_unique` (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_clients`
--

INSERT INTO `tb_clients` (`matricule`, `nom`, `postnom`, `prenom`, `email`, `telephone`, `sexe`, `date_naissance`, `lieu_naissance`, `adresse`, `etat_civil`, `nom_conjoint`, `zone`, `type_piece_identite`, `lieu_delivrance_piece`, `date_delivrance_piece`, `numero_piece_identite`, `photo`, `secteur_activite`, `type_activite`, `nom_entreprise`, `adresse_entreprise`, `telephone_entreprise`, `statut_entreprise`, `nombre_annees_experience`, `revenu_mensuel`, `revenu_mensuel_devise`, `autres_details_activite`, `created_at`, `updated_at`) VALUES
('CL-EBENKGA-26-00002', 'MPUTU', 'TUDIKOLELE', 'Clémence', 'exemple@email.com', '0123456789', 'M', '1994-02-04', 'Kinshasa', 'Ndesha', 'Divorcé', NULL, 'Urbain', 'Carte d\'électeur', 'Kananga', '2021-02-19', '000847', 'clients/Nice.jpg', 'Sorry', 'Commerce', 'KANKO', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', '0992463511', 'Agréee', '5', 50000.00, NULL, 'Ras', '2026-02-14 11:47:42', '2026-02-14 11:47:42'),
('CL-EBENKGA-26-00003', 'MPUTU', 'TUDIKOLELE', 'Clémence', 'exemple@email.com', '0123456789', 'M', '1994-02-04', 'Kinshasa', 'Ndesha', 'Marié', 'Nice MPUTU', 'Urbain', 'Carte d\'électeur', 'Kananga', '2021-02-19', '000847', 'clients/1771151216_IMG_8304.jpeg', 'Sorry', 'Commerce', 'KANKO', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', '0992463511', 'Agréee', '5', 50000.00, NULL, 'Ras', '2026-02-14 13:06:40', '2026-02-15 07:27:42'),
('CL-EBENKGA-26-00004', 'MPUTU', 'TUDIKOLELE', 'Clémence', 'exemple@email.com', '0123456789', 'M', '1994-02-04', 'Kinshasa', 'Ndesha', 'Célibataire', NULL, 'Urbain', 'Carte d\'électeur', 'Kananga', '2021-02-19', '000847', 'clients/1771085969_Blaise_1.jpeg', 'Sorry', 'Agriculture', 'KANKO', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', '0992463511', 'Agréee', '5', 50000.00, NULL, 'Ras', '2026-02-14 13:19:29', '2026-02-14 13:19:29'),
('CL-EBENKGA-26-00005', 'MPUTU', 'TUDIKOLELE', 'Clémence', 'blaisemusbadi2019@gmail.com', '0992463511', 'F', '2027-01-29', 'Mbuji-Mayi', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', 'Marié', 'MUBADI', 'Urbain', 'Carte nationale d\'identité', 'Kananga', '2026-02-03', '000847', 'clients/1771853822_Blaise_1.jpeg', 'Education', 'Commerce', 'KANKO', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', '0992463511', 'Agréee', '2', 200.00, NULL, 'Ras', '2026-02-23 12:37:02', '2026-02-23 12:37:02'),
('CL-EBENKGA-26-00001', 'MPUTU', 'TUDIKOLELE', 'Clémence', 'blaisemusbadi2019@gmail.com', '0992463511', 'M', '2027-01-29', 'Mbuji-Mayi', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', 'Marié', 'MUBADI', 'Urbain', 'Carte nationale d\'identité', 'Kananga', '2026-02-03', '000847', 'clients/1771853894_Blaise_2.jpeg', 'Education', 'Commerce', 'KANKO', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', '0992463511', 'Agréee', '2', 200.00, NULL, 'Ras', '2026-02-23 12:38:14', '2026-02-23 12:38:14');

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
  UNIQUE KEY `unique_nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_permissions`
--

INSERT INTO `tb_permissions` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
('EBEN-PER1', 'Charger Clientèle', NULL, '2026-02-23 09:29:46', '2026-02-23 09:29:46'),
('EBEN-PER10', 'Suivi de crédit', NULL, '2026-02-23 09:53:26', '2026-02-23 09:53:26'),
('EBEN-PER2', 'Agent de crédit', NULL, '2026-02-23 09:30:03', '2026-02-23 09:30:03'),
('EBEN-PER3', 'Gérer les clients', NULL, '2026-02-23 09:50:50', '2026-02-23 09:50:50'),
('EBEN-PER4', 'Gérer les comptes', NULL, '2026-02-23 09:51:03', '2026-02-23 09:51:03'),
('EBEN-PER5', 'Créer Une demande', NULL, '2026-02-23 09:51:14', '2026-02-23 09:51:14'),
('EBEN-PER6', 'Administrations', NULL, '2026-02-23 09:51:38', '2026-02-23 09:51:38'),
('EBEN-PER7', 'Voir les transactions', NULL, '2026-02-23 09:52:49', '2026-02-23 09:52:49'),
('EBEN-PER8', 'Encodage des fonds', NULL, '2026-02-23 09:52:57', '2026-02-23 09:52:57'),
('EBEN-PER9', 'Trasaction', NULL, '2026-02-23 09:53:12', '2026-02-23 09:53:12');

-- --------------------------------------------------------

--
-- Structure de la table `tb_postes`
--

DROP TABLE IF EXISTS `tb_postes`;
CREATE TABLE IF NOT EXISTS `tb_postes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_id` bigint UNSIGNED NOT NULL,
  `nom` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tb_postes_service_id_foreign` (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_postes`
--

INSERT INTO `tb_postes` (`id`, `service_id`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(2, 3, 'Caisse', 'Central', '2026-02-20 06:40:25', '2026-02-20 06:40:25'),
(3, 7, 'Nourritue', NULL, '2026-02-20 06:40:39', '2026-02-20 06:40:39'),
(4, 3, 'Autres', NULL, '2026-02-20 07:41:29', '2026-02-20 07:41:29'),
(5, 3, 'Moi', NULL, '2026-02-23 08:17:55', '2026-02-23 08:17:55');

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
  UNIQUE KEY `nom` (`nom`),
  UNIQUE KEY `unique_nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_roles`
--

INSERT INTO `tb_roles` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
('EBEN-ROL1', 'Agent de crédit', NULL, '2026-02-23 09:29:38', '2026-02-23 09:29:38'),
('EBEN-ROL2', 'Charger clientèle', NULL, '2026-02-23 09:41:36', '2026-02-23 09:41:36'),
('EBEN-ROL3', 'Administrateur', NULL, '2026-02-23 09:51:52', '2026-02-23 09:51:52');

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
  KEY `permission_code` (`permission_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_role_permission`
--

INSERT INTO `tb_role_permission` (`role_code`, `permission_code`, `created_at`, `updated_at`) VALUES
('EBEN-ROL1', 'EBEN-PER1', NULL, NULL),
('EBEN-ROL1', 'EBEN-PER2', NULL, NULL),
('EBEN-ROL3', 'EBEN-PER1', NULL, NULL),
('EBEN-ROL3', 'EBEN-PER10', NULL, NULL),
('EBEN-ROL3', 'EBEN-PER2', NULL, NULL),
('EBEN-ROL3', 'EBEN-PER3', NULL, NULL),
('EBEN-ROL3', 'EBEN-PER4', NULL, NULL),
('EBEN-ROL3', 'EBEN-PER5', NULL, NULL),
('EBEN-ROL3', 'EBEN-PER6', NULL, NULL),
('EBEN-ROL3', 'EBEN-PER7', NULL, NULL),
('EBEN-ROL3', 'EBEN-PER8', NULL, NULL),
('EBEN-ROL3', 'EBEN-PER9', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `tb_role_user`
--

DROP TABLE IF EXISTS `tb_role_user`;
CREATE TABLE IF NOT EXISTS `tb_role_user` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `role_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contrainte_role` (`role_code`),
  KEY `contrainte_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_role_user`
--

INSERT INTO `tb_role_user` (`id`, `user_id`, `role_code`, `created_at`, `updated_at`) VALUES
(4, 7, 'EBEN-ROL2', NULL, NULL),
(6, 8, 'EBEN-ROL3', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `tb_services`
--

DROP TABLE IF EXISTS `tb_services`;
CREATE TABLE IF NOT EXISTS `tb_services` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_services`
--

INSERT INTO `tb_services` (`id`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Caisse', NULL, '2026-02-17 10:57:22', '2026-02-17 10:57:22'),
(2, 'Comptabilité', NULL, '2026-02-17 11:00:40', '2026-02-17 11:00:40'),
(3, 'Laboratoire', NULL, '2026-02-20 06:00:00', '2026-02-20 06:00:00'),
(4, 'Ressources Humaines', NULL, '2026-02-17 11:03:08', '2026-02-17 11:03:08'),
(5, 'faculté', NULL, '2026-02-17 11:04:22', '2026-02-17 11:04:22'),
(6, 'Cafétariat', NULL, '2026-02-17 11:04:39', '2026-02-17 11:04:39'),
(7, 'Polyclinique', NULL, '2026-02-17 11:05:55', '2026-02-17 11:05:55');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `agent_matricule` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `etat` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'actif',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `fk_agent_matricule` (`agent_matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `agent_matricule`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `etat`) VALUES
(7, 'AG-EBENKGA-26-00002', 'bmb', 'christophetshibangu117@gmail.com', NULL, '$2y$12$9NmdYR14mv2F.zFFE2dVO.Agr199lL5UVM4giOK69pFW3l1OI/nnG', NULL, '2026-02-22 10:21:57', '2026-02-22 10:21:57', 'actif'),
(8, 'AG-EBENKGA-26-00001', 'ras', 'blaisemubadi2019@gmail.com', NULL, '$2y$12$i/N.hT8m5M9v7QE/6/RC9OBIQFWCixHLsNhUQf7av9pNDN5gBctGm', NULL, '2026-02-23 12:40:16', '2026-02-23 12:40:16', 'actif');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `tb_affectations`
--
ALTER TABLE `tb_affectations`
  ADD CONSTRAINT `tb_affectations_agent_matricule_foreign` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_affectations_poste_id_foreign` FOREIGN KEY (`poste_id`) REFERENCES `tb_postes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_postes`
--
ALTER TABLE `tb_postes`
  ADD CONSTRAINT `tb_postes_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `tb_services` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_role_user`
--
ALTER TABLE `tb_role_user`
  ADD CONSTRAINT `contrainte_role` FOREIGN KEY (`role_code`) REFERENCES `tb_roles` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `contrainte_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_agent_matricule` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
