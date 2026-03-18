-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mar. 17 mars 2026 à 08:52
-- Version du serveur : 10.11.15-MariaDB-deb12
-- Version de PHP : 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `coopa2747247`
--

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('coopec-eben-cache-bilonak|41.243.44.194', 'i:1;', 1773519523),
('coopec-eben-cache-bilonak|41.243.44.194:timer', 'i:1773519523;', 1773519523),
('coopec-eben-cache-bilondak|41.243.44.174', 'i:1;', 1773512107),
('coopec-eben-cache-bilondak|41.243.44.174:timer', 'i:1773512107;', 1773512107),
('coopec-eben-cache-biolondak|41.243.30.197', 'i:1;', 1773575026),
('coopec-eben-cache-biolondak|41.243.30.197:timer', 'i:1773575026;', 1773575026),
('coopec-eben-cache-btk|41.243.44.215', 'i:3;', 1773726857),
('coopec-eben-cache-btk|41.243.44.215:timer', 'i:1773726857;', 1773726857),
('coopec-eben-cache-btk|41.243.44.216', 'i:1;', 1773726995),
('coopec-eben-cache-btk|41.243.44.216:timer', 'i:1773726995;', 1773726995),
('coopec-eben-cache-btk|41.243.44.233', 'i:1;', 1773726966),
('coopec-eben-cache-btk|41.243.44.233:timer', 'i:1773726966;', 1773726966),
('coopec-eben-cache-dany|82.145.211.223', 'i:1;', 1773474935),
('coopec-eben-cache-dany|82.145.211.223:timer', 'i:1773474935;', 1773474935),
('coopec-eben-cache-jeanp|102.206.159.101', 'i:4;', 1773475505),
('coopec-eben-cache-jeanp|102.206.159.101:timer', 'i:1773475505;', 1773475505),
('coopec-eben-cache-lukeng|102.206.159.152', 'i:1;', 1773647618),
('coopec-eben-cache-lukeng|102.206.159.152:timer', 'i:1773647618;', 1773647618),
('coopec-eben-cache-lukeng|41.243.30.115', 'i:2;', 1773649175),
('coopec-eben-cache-lukeng|41.243.30.115:timer', 'i:1773649175;', 1773649175),
('coopec-eben-cache-non|41.243.44.195', 'i:3;', 1773646687),
('coopec-eben-cache-non|41.243.44.195:timer', 'i:1773646687;', 1773646687),
('coopec-eben-cache-non|41.243.44.230', 'i:1;', 1773646776),
('coopec-eben-cache-non|41.243.44.230:timer', 'i:1773646776;', 1773646776);

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(17, '2026_03_08_120000_drop_client_columns_from_tb_transactions', 12);

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('4AxFNxi2dnV9m5L9c3nFw1dpjOwatgLRqM42yTbJ', NULL, '74.125.208.7', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWlRhazdiZEk2UGltcjJkQ2lROHRyOEdWNDFmekN5bUtMVmRncXNmbiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNToiaHR0cHM6Ly9jb29wYWViZW4uaW5mby9wcm9maWxlL2VkaXQiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozNToiaHR0cHM6Ly9jb29wYWViZW4uaW5mby9wcm9maWxlL2VkaXQiO3M6NToicm91dGUiO3M6MTI6InByb2ZpbGUuZWRpdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773728748),
('4dlVieyNnPZHBceYTqQGMcrroyThupTsyK5dGdFN', NULL, '74.125.208.8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVkNZY2piVUFZTko3T2t0a3hQWjU3c2N1c0hyanAzbzh0VW11WHJXSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY29vcGFlYmVuLmluZm8vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773725742),
('535rfEwVC7xyi1iaBE1mH3bC94LAEXpgSg7r8PeZ', NULL, '102.206.156.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNmhWakFsODhlRVlucUEwUWhNZjBvRlo2eDBrWFBjS0YzaWhicmh4VyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY29vcGFlYmVuLmluZm8vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO319', 1773737535),
('5HUo5Ks7Po5dRbU4mHISyo8Qyf4S1VeLjgOPA7sV', NULL, '102.206.156.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS1BrbGI0dGtQQXY3S1NzZUoyNnVJRE11cGJvbGV2OGVjS1JFQ0FTVyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY29vcGFlYmVuLmluZm8vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO319', 1773737471),
('5Nk70dEpXnTP2wIPuHb2okaQV5y77651CtiT9eCW', NULL, '34.174.163.33', 'Mozilla/5.0 Firefox/33.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiallzWVp4bVpXOGR1T1ZYTzgyVGxqMmo1UXVOQ1RIc3BiUjRRYkh3VCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jb29wYWViZW4uaW5mby9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773716223),
('9Bh7Hl5tIHvwTP1nWKeWTXL9WaJiroCzPPRXomvb', NULL, '84.246.85.11', '2ip bot/1.1 (+https://2ip.io)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoielY1OENGcXB3NkExbThLcFF4cVJrc3dlN2JBU09hd012S0tCM3R0SyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2Nvb3BhZWJlbi5pbmZvIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jb29wYWViZW4uaW5mby9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773710959),
('bHaeSCqda77aq41er0rz4SPH6xlwV0KolzwYYecl', NULL, '74.125.208.9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOEZXUkloajhqeTU3UmI1OXhTS2djcEFINFhqQ2JWbm9YNkIweUpaSSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1MzoiaHR0cHM6Ly9jb29wYWViZW4uaW5mby9jb21wdGVzLWNsaWVudHMvY2xpZW50cy9jcmVhdGUiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo1MzoiaHR0cHM6Ly9jb29wYWViZW4uaW5mby9jb21wdGVzLWNsaWVudHMvY2xpZW50cy9jcmVhdGUiO3M6NToicm91dGUiO3M6MTQ6ImNsaWVudHMuY3JlYXRlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773725437),
('BHZvSKrci4dUdastPeMYH8hd8jHUG9T9koO7Ly49', 18, '41.243.44.206', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSmVmOHJIRGt2TG5PMEE1bEp3VDRNd3BWcGpyaEpHdDR6aGU3YmZERyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjEwMjoiaHR0cHM6Ly9jb29wYWViZW4uaW5mby9hZ2VudHMvcGhvdG8vMTc3MzE3NjYzNV9iYWVmYjQ0NS04NThkLTQyOTUtOTMzZi0yODg3YmM3MWRhNTMtMmpmaWY/dj0xNzczNzM3NTM1IjtzOjU6InJvdXRlIjtzOjEyOiJhZ2VudHMucGhvdG8iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxODt9', 1773737536),
('bOIADRyONCkNDPptpGkPYQd2QDdyueuRzbt4EWLr', 24, '41.222.199.78', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Mobile Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYnJXWTZNM0FEM1ptVkZ3dUduTXpzdFF5VWp2TkIycnB3RkR2UllnSCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjUzOiJodHRwczovL2Nvb3BhZWJlbi5pbmZvL2NvbXB0ZXMtY2xpZW50cy9jbGllbnRzL2NyZWF0ZSI7czo1OiJyb3V0ZSI7czoxNDoiY2xpZW50cy5jcmVhdGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyNDt9', 1773730537),
('BzEognPRZwz2H2gHEqP92DZ8p80RMpsly0I21XGS', NULL, '74.125.208.8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicjlBbGMxTnVBVklkbGNjZXdST1RBSm1EYXNZTTZoMVJjY1Qxbk9hdSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9jb29wYWViZW4uaW5mby9kYXNoYm9hcmQiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9jb29wYWViZW4uaW5mby9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773734939),
('c4d2S5XvsFiRnYLm4MhIoI50LEPKnfubKhYmrvEd', NULL, '74.125.208.8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMXMyZjExemd6d0x0RHh3M3JqQ1R5Qk5QVFBwNThmbXFsNDgyRzR5NiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY29vcGFlYmVuLmluZm8vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773728748),
('CMHshWM0KaBTOdijnnuO5ifie6KCcddjYmDjCxK2', NULL, '43.157.153.236', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY1FOS0lHdlVkQkU3ajdBQ0hFOVhCcTJkenR0SEJkRkI2a3JGVXUxNyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly93d3cuY29vcGFlYmVuLmluZm8vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773713034),
('e9hexl9wO3Mx2UH5Ubyrd3m5ghsPqeP0n3iRnGpK', 19, '41.243.44.141', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYUw5NVd6TXM3d3NzbHROZDhYamYzVmUzRjlaRVVmS25NRGZsTGd1diI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjUzOiJodHRwczovL2Nvb3BhZWJlbi5pbmZvL2NvbXB0ZXMtY2xpZW50cy9jbGllbnRzL2NyZWF0ZSI7czo1OiJyb3V0ZSI7czoxNDoiY2xpZW50cy5jcmVhdGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxOTt9', 1773731414),
('Ea2SL1LJ0EgO0SzxtWHWBVf8HHDNeMAzPNeVuCTt', NULL, '144.217.135.184', 'Mozilla/5.0 (compatible; Dataprovider.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYVZPbUFDaDFSNnBoUG9wOXUzVnFVZDFkQW1JSDZ1MWhKTm00aTA1eiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vd3d3LmNvb3BhZWJlbi5pbmZvL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773725608),
('G28reOk1JPT8biq4b1odMZY1lirGWqTqTRSqspav', 15, '41.243.44.157', 'Mozilla/5.0 (Linux; U; Android 14; fr-fr; TECNO BG6m Build/UP1A.231005.007) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.7258.143 Mobile Safari/537.36 PHX/20.8', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoieXlwbmlTV01KbzNPZDN5OG5qWDFzbVNpcTFYM3JCcGllUnU3RGpyVCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjk5OiJodHRwczovL2Nvb3BhZWJlbi5pbmZvL2FnZW50cy9waG90by8xNzczMTcyMTU5X3doYXRzYXBwLWltYWdlLTIwMjYtMDMtMTAtYXQtMDc1MTU1anBlZz92PTE3NzM3MzE5MzIiO3M6NToicm91dGUiO3M6MTI6ImFnZW50cy5waG90byI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE1O30=', 1773732012),
('H7fmpM9Ku7IQc59KXp6vsDKDBsfD3OQvBJIITlMo', 19, '41.243.44.205', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicVdXMk8yMjQzRmVWT1NhZWpRNWl5RWZoMXVYajgxUTBhNFN0eHJhWiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjYzOiJodHRwczovL2Nvb3BhZWJlbi5pbmZvL2NsaWVudHMvcGhvdG8vMTc3MzY3MzE5NV8xMDAwMDE3MDM2LmpwZWciO3M6NToicm91dGUiO3M6MTM6ImNsaWVudHMucGhvdG8iO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxOTt9', 1773735923),
('HqxvLT9RSZnfCPY1yah3Xn5SU4tfMaLStLxCHoqn', NULL, '43.130.57.76', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidUx0MTIzSk14N3E4YnBQdWZlbTNIYmtjNmp4clZFZnQwTm1yb080UyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2Nvb3BhZWJlbi5pbmZvIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9jb29wYWViZW4uaW5mbyI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773722835),
('I1v2Eo7kMOj0RqG4tqcj2kDJmnnpmzADbJGKE2FT', NULL, '102.206.157.77', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWGltV0dCalVFVnBaNVRDTEthV2pBTTZ5WWIzTDRiQ1hwa2Y2OENTMiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY29vcGFlYmVuLmluZm8vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO319', 1773736543),
('ID6586yj43P2wjgmJ28kKl5VX6Vfo56d60i2qhlc', NULL, '74.125.208.9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU251akIwSmFSQmY4NmZFd2dYeHExQ3AxN3BScUNNQnFCZmRKQjhjVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY29vcGFlYmVuLmluZm8vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773734940),
('j2v1ZKrFUoBikLpodvat4fS5kej4l8GhtZqq4mHi', NULL, '43.131.45.213', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiejlDMmN6aW5XZG1nUGRZdmpuS2ZFdDcxVVJYSnBtdGt6NXdaYVlSZSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNToiaHR0cDovL3d3dy5jb29wYWViZW4uaW5mbyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI1OiJodHRwOi8vd3d3LmNvb3BhZWJlbi5pbmZvIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773704687),
('k0m8nyHKM8zuDn2BAXb23ptvwZdcPyFntaQwp7zA', NULL, '43.135.133.241', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicFJwWmp4eGVjdWU0bWM1UXp5SE9HdVVjUWVrZlBqMlJJcFd6UlRoUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jb29wYWViZW4uaW5mby9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773702005),
('L8pYcvJWIme7TKEyULscLzFMLNG0Bepyj8PBevo1', NULL, '170.106.11.141', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoib1VWWjBrR0tpS1ZTRVI5ZWNpSzg0NmRibDZoazVDZWZ5cjMxd0dWQSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2Nvb3BhZWJlbi5pbmZvIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9jb29wYWViZW4uaW5mbyI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773709661),
('lcRrjwLHTcRdEbu35doVHqpgeQ275CFglZXaVjiK', NULL, '170.106.11.141', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS093bjd3bFZhVzVTZWMwV1NWaUwyUEFBTDR4aTd5SFR3OGFHeUYzUCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jb29wYWViZW4uaW5mby9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773709663),
('lDsQcEy4V60A8cAd9iVppWFiqu6PlR0oMlxntP6P', NULL, '111.172.249.49', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWXNISlZMd0ViS3llc0RrZjV1d1pnQWdqbE5SWmg1Qmd5TXNITFNGcSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly93d3cuY29vcGFlYmVuLmluZm8vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773727407),
('luM5RAuFXbOab0zETeetik07xWqwIXEGDg5kQK7b', NULL, '43.131.45.213', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMDFlWEFuOWo2dXQxc3J1ZUNqcWpHczFiRTV4YmUydGNkRXNPWU54TiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly93d3cuY29vcGFlYmVuLmluZm8vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773704689),
('m30xj9y4oKEQ3nJvVnWRnr0hdfIWJPb9PomDKbS9', NULL, '35.204.44.196', 'Scrapy/2.13.4 (+https://scrapy.org)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicHZMcTc3NWZFRUw2V3FyU2FTdXlmYWlValZCaHVXY3ZFQzFoUzd2SSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNjoiaHR0cHM6Ly93d3cuY29vcGFlYmVuLmluZm8iO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly93d3cuY29vcGFlYmVuLmluZm8vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773703554),
('M99H0z8ekErQJCpHf0Hr775ofmGaNPaEkmNIpJwe', NULL, '149.56.160.241', 'Mozilla/5.0 (compatible; Dataprovider.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUXpiZnlnbDMwM0oyUE9RS0ZjUXFBSmNWRVhxeWVPYWFKT09KQUtBbSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vd3d3LmNvb3BhZWJlbi5pbmZvL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773725623),
('MYy6BZig6XZ5RxSVzfEigWcDwATLtHynNJlocNWb', NULL, '144.217.135.184', 'Mozilla/5.0 (Linux; Android 10; SM-G981B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSGdVZml2ajhKellyVFBmQWdwS3hQTFF3T2paOWE4WUlReE1VS1l6MCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNToiaHR0cDovL3d3dy5jb29wYWViZW4uaW5mbyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vd3d3LmNvb3BhZWJlbi5pbmZvL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773725609),
('nBhVwibBypegENkBtJRxhO3yMi84fimGEsNiMVsV', 13, '154.73.22.82', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiT1ZiZ05QcGFHYUpwZncxR0VTVzBrRmRwVTM1SHVPR2tWS3g0YXZBTiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjUzOiJodHRwczovL2Nvb3BhZWJlbi5pbmZvL2NvbXB0ZXMtY2xpZW50cy9jbGllbnRzL2NyZWF0ZSI7czo1OiJyb3V0ZSI7czoxNDoiY2xpZW50cy5jcmVhdGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxMzt9', 1773726000),
('NtA56o6LGhxlXJleFB3YgT3eupj2FeXec4kPVEIz', 18, '41.243.44.247', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibldyeEp5MmxOTTIwcjkweTF3ODNSWWJKMFJ5Y3JHRTlpRWpXUWp3NCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTAyOiJodHRwczovL2Nvb3BhZWJlbi5pbmZvL2FnZW50cy9waG90by8xNzczMTc2NjM1X2JhZWZiNDQ1LTg1OGQtNDI5NS05MzNmLTI4ODdiYzcxZGE1My0yamZpZj92PTE3NzM3Mjg5MTYiO3M6NToicm91dGUiO3M6MTI6ImFnZW50cy5waG90byI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE4O30=', 1773728917),
('nXAwCs5Ckd0AuLGbNDr1a4yummnP2qtxDsRTqrcG', NULL, '43.135.133.241', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieUg2MzUxRURvVkdRNUZWdmVrUDJVd1RqbXNWNE5oT3pDempLMEw1dyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2Nvb3BhZWJlbi5pbmZvIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9jb29wYWViZW4uaW5mbyI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773702004),
('OO6zgC1T7Qh8rpPivokuEivpvBIxcjassKXvbzN7', NULL, '149.56.160.241', 'Mozilla/5.0 (compatible; Dataprovider.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSk1sUEg0Z1JVRGR0WjBIZ0xHZnZZWERZYlpJdWVLaGJ2M1BNR3BDNSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNToiaHR0cDovL3d3dy5jb29wYWViZW4uaW5mbyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vd3d3LmNvb3BhZWJlbi5pbmZvL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773725621),
('oqwQziWCKH8p2Vu0ISo1CIA6Fj3ssjxdjBhTeIhX', NULL, '43.164.197.209', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZ1czN1VudGYzQjJJZEZDQlhxU2EydUhuVUZIeUQ1MFEwSlpQakVuNiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNToiaHR0cDovL3d3dy5jb29wYWViZW4uaW5mbyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI1OiJodHRwOi8vd3d3LmNvb3BhZWJlbi5pbmZvIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773725671),
('prkSzfcbUYvw8YCJmG9Xueaf2uSJ1HSW92xJNdV1', NULL, '157.245.37.223', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYnpkY1l5RE5UNlB6QmNqUE8wYjg5UzlERUN6Z3lsaVM1UFRnWmVVZyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2Nvb3BhZWJlbi5pbmZvIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jb29wYWViZW4uaW5mby9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773706622),
('ptkrOZGQlFIWTE8bGTvjZ0C7VuUk841ZG6D6BpMA', 17, '41.243.44.141', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSkxPRERlZDdJNU1MUHI0THY2N3NuZ3FkbTFFNU96enRmeXkzQUhYMSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjY4OiJodHRwOi8vY29vcGFlYmVuLmluZm8vYWdlbnRzL3Bob3RvLzE3NzMxNzEyNTRfbWFyZ3VqcGVnP3Y9MTc3MzczNjU0NSI7czo1OiJyb3V0ZSI7czoxMjoiYWdlbnRzLnBob3RvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTc7fQ==', 1773736922),
('pXysP8HpuIsNLtL4UwijyogHTGYnyWmNLUDHZlno', NULL, '34.174.163.33', 'Mozilla/5.0 Firefox/33.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY2FKV21SR1dxNHRMQ3dlMk5YTHhhVkVpZFQ3SFVUQ1RsMlpQdmVWcCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2Nvb3BhZWJlbi5pbmZvIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9jb29wYWViZW4uaW5mbyI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773716221),
('r4SHGLG6MynwIOifMbnjszMyG3ehYR2GQUKllpY5', NULL, '149.56.160.241', 'Mozilla/5.0 (compatible; Dataprovider.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoid2FDTktVNDN5dHJIWmc1Z2tiNWRQdkNvYndwdGpvZEwxOFo2b1c3VyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNjoiaHR0cHM6Ly93d3cuY29vcGFlYmVuLmluZm8iO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czoyNjoiaHR0cHM6Ly93d3cuY29vcGFlYmVuLmluZm8iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773725623),
('rt25H5EqtBWNd6fD2JVyC88oXNgS0x8jC3CpQXiG', NULL, '41.243.44.168', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicmd2Ukp2emVTeE1YWmM2MHhkdHV3TUpObzUzVkVGdjFEYWV4akdFeiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2Nvb3BhZWJlbi5pbmZvIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jb29wYWViZW4uaW5mby9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773736109),
('S5LEtIfNkb2MCHCFA5YJP2B5mUoAVvCzMN54RrIo', NULL, '43.164.197.209', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYlFwZ2xaQlo5MXRjT1E3Vjd1djM5R0xSamR2QlNZY29KcTQwUzBZSyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly93d3cuY29vcGFlYmVuLmluZm8vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773725672),
('sL4CmqY5rrZTwgRtFWviYgvroA09yjWnfchOQRKL', NULL, '43.130.57.76', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibXVuQTZzUTRzOU53T1NFcXdlUlo3NWo0dGNlTkNqTklXUUt3TktwMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jb29wYWViZW4uaW5mby9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773722837),
('SpREob8Uf8hB2J1eZ7OBFvXs9GUqTmrq4ZE14LfO', NULL, '43.157.153.236', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYlk4OHRCUlFqVWU1b1RnUjd2Sldwd09nNDVxTXdVZmNHYUZjWVhtaiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNToiaHR0cDovL3d3dy5jb29wYWViZW4uaW5mbyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI1OiJodHRwOi8vd3d3LmNvb3BhZWJlbi5pbmZvIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773713032),
('t3c4zmsmxHNlKDivU7PhZluk5G7km8o4DnXtprhd', NULL, '34.174.163.33', 'Mozilla/5.0 Firefox/33.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMVVnTGRTbExGaVJrZmNaejdXbEtWVklubllWb3c2bGNrdVJHUVpHMCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2Nvb3BhZWJlbi5pbmZvIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jb29wYWViZW4uaW5mby9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773716220),
('tQuS54pkiWKCy7O95dKgAB5YMRfmIMD4wsHI1OHb', NULL, '74.125.208.8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUEV6cTFMdTlYQUVzYmN6dlVDbk93N251VDMydFRuc2RBYkEyc3cxUCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NjoiaHR0cHM6Ly9jb29wYWViZW4uaW5mby9jb21wdGVzLWNsaWVudHMvY2xpZW50cyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQ2OiJodHRwczovL2Nvb3BhZWJlbi5pbmZvL2NvbXB0ZXMtY2xpZW50cy9jbGllbnRzIjtzOjU6InJvdXRlIjtzOjEzOiJjbGllbnRzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773725742),
('TXcfKTkANw0gd2RZPK5FXpeus5N1fGaGPyWdaTHG', 17, '41.243.44.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNzVXZmRsZ3FlOFpUUGpHZjZkbEJRaXBlbEFRNXpEY2dDZzlEc0NUQiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjY4OiJodHRwOi8vY29vcGFlYmVuLmluZm8vYWdlbnRzL3Bob3RvLzE3NzMxNzEyNTRfbWFyZ3VqcGVnP3Y9MTc3MzczNTMzOSI7czo1OiJyb3V0ZSI7czoxMjoiYWdlbnRzLnBob3RvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTc7fQ==', 1773735732),
('TYuR4s6typFvn5zbm8xxo7uE0wvcjBUeLswc2JPx', 11, '102.206.157.77', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiazVyZ3AzaldFeXFYaUVrNU1BWWxhaGNZWG5yNThZOVo5Z0dFTHhnQyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjc2OiJodHRwczovL2Nvb3BhZWJlbi5pbmZvL2FnZW50cy9waG90by8xNzczMTY5NTUwXzE3Njc3ODEzNDgxMDRqcGc/dj0xNzczNzM3MTcxIjtzOjU6InJvdXRlIjtzOjEyOiJhZ2VudHMucGhvdG8iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxMTt9', 1773737532),
('u72oQOFIvJ4UkaxCs8BLETk8EoJJ725ZotePtati', NULL, '74.125.208.9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWDZsdENJTlltZkhkQWpoRUVSa1FxNndwUExta3pzUkVsRWdnTkYwRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY29vcGFlYmVuLmluZm8vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773725437),
('vjagzU152ug4ggwA7sZByTFh5bAROuPhOrc8QVmf', NULL, '111.172.249.49', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNlNFUWpDSFlNcTZLSWNnNFVDaTQ3cmxBNVZQNEd3dlZZbGVVZkZYcCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNToiaHR0cDovL3d3dy5jb29wYWViZW4uaW5mbyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI1OiJodHRwOi8vd3d3LmNvb3BhZWJlbi5pbmZvIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773727398),
('VvxV6Qg4ti8cDKqqYRjsiFeDaUUXDGV1bfH9uhFy', NULL, '84.246.85.11', '2ip bot/1.1 (+https://2ip.io)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNE1ocWdYMndPUHlFWk9aSld0a3ZxM2haNVZKaWlEdnpDMG5nT0g4dSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2Nvb3BhZWJlbi5pbmZvIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jb29wYWViZW4uaW5mby9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773710958),
('wKVEUhJPHpx2kqoTutLN5Lt6DyDX6z3v8uuKE0xk', NULL, '1.15.52.154', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVmR5MjZaNGNvemg1VGwzbk5hbktCOVYwVm9PT2dnOGhGUDh6c09oYSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2Nvb3BhZWJlbi5pbmZvIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jb29wYWViZW4uaW5mby9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773703651),
('X4avT5P9ftky9DElcJ2LrXvmFbDtCiGgiYZTLCwT', NULL, '154.73.22.93', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU29Bc01KNXVZN3dNTFZWWkhLNWp3Y25HYlRLd2tSdG1ib2kzR3lOUCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY29vcGFlYmVuLmluZm8vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO319', 1773736738),
('XblBOovNRlfa5TCZnghblJAmAoaOub5d0NXcZguW', NULL, '157.245.37.223', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZmwzSG53SGNTQ0pYNVRIenJYdkZ0QkQ0Rmo2YVBZekFHV2lsbG1CRyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMjoiaHR0cHM6Ly9jb29wYWViZW4uaW5mbyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI4OiJodHRwczovL2Nvb3BhZWJlbi5pbmZvL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773706624),
('XDEFemT4y9IpyvtxzWSMDwfnIXf5kHPPxtCLmZpV', 19, '41.243.44.170', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSmRhQ3JGaXRNSDRoSGhEeHFHU0tkamtTNXNnUTRhSmt5SWZ0bDhVUiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI4OiJodHRwczovL2Nvb3BhZWJlbi5pbmZvL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTk7fQ==', 1773734806),
('xDFVzeqFWbOVvrbUdp5V39z6ZNT6B8mrQpmqMTQD', 12, '154.73.22.80', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUDBwNFBwcGtiMTFHUGlSOE4xZVhYNXpFU0NxZnBaM0Zocm5kVjk4SyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTAyOiJodHRwczovL2Nvb3BhZWJlbi5pbmZvL2FnZW50cy9waG90by8xNzczMTczMDMxXzU2NmU1MTFkLTBhNjYtNDJkMy1hZjk1LTA2YTY0MTNhNmFkYy0yamZpZj92PTE3NzM3MzcyNjIiO3M6NToicm91dGUiO3M6MTI6ImFnZW50cy5waG90byI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjEyO30=', 1773737524),
('Y1jAEgMIdxmmcNpIUD4lrfBDdqxF8l4XSdCXxaCn', NULL, '84.246.85.11', '2ip bot/1.1 (+https://2ip.io)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiblZ5TDJKS0tWRXBZSHRrd09hekZOWDVzdWxnclh5M3drUGFpUVZzbiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2Nvb3BhZWJlbi5pbmZvIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jb29wYWViZW4uaW5mby9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773710959),
('yAoIboDIsoEWK30Drw2OkopM59JK9af3sfHcGWfY', 16, '154.73.22.93', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQWtqRkxLQ2pqWTdIT1Rwc1lsVW1QNG9BcWRMSWR0RVFYam1zams5ZyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjYzOiJodHRwczovL2Nvb3BhZWJlbi5pbmZvL2NsaWVudHMvcGhvdG8vMTc3MzY5NTY5NV8xMDAwMTIwMTA4LmpwZWciO3M6NToicm91dGUiO3M6MTM6ImNsaWVudHMucGhvdG8iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxNjt9', 1773732121),
('YwbG1W3pWcB4PnxSEOQovHVTbOfteOAFciHkXQUg', NULL, '34.90.191.83', 'Scrapy/2.13.4 (+https://scrapy.org)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidVZxeEk5ZTd4dmd6SGlUWFBUc1QxMmozdUd0aldVQTlwcGw1ZWg2OSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMjoiaHR0cHM6Ly9jb29wYWViZW4uaW5mbyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI4OiJodHRwczovL2Nvb3BhZWJlbi5pbmZvL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773702096);

-- --------------------------------------------------------

--
-- Structure de la table `tb_affectations`
--

CREATE TABLE `tb_affectations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `agent_matricule` varchar(50) NOT NULL,
  `poste_id` bigint(20) UNSIGNED NOT NULL,
  `guichet_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Guichet de caisse affecté (optionnel). NULL = agent hors caisse.',
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `Etat` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_affectations`
--

INSERT INTO `tb_affectations` (`id`, `agent_matricule`, `poste_id`, `guichet_id`, `date_debut`, `date_fin`, `Etat`, `created_at`, `updated_at`) VALUES
(15, 'AG-EBENKGA-26-00002', 1, NULL, '2026-03-10', NULL, 'ACTIF', '2026-03-10 18:07:01', '2026-03-10 18:07:01'),
(16, 'AG-EBENKGA-26-00011', 16, 20, '2026-03-10', NULL, 'ACTIF', '2026-03-10 19:24:15', '2026-03-10 19:24:15'),
(17, 'AG-EBENKGA-26-00003', 16, 21, '2026-03-10', NULL, 'ACTIF', '2026-03-10 19:24:34', '2026-03-10 19:24:34'),
(18, 'AG-EBENKGA-26-00005', 16, 22, '2026-03-10', NULL, 'ACTIF', '2026-03-10 19:24:53', '2026-03-10 19:24:53'),
(19, 'AG-EBENKGA-26-00007', 16, 23, '2026-03-10', NULL, 'ACTIF', '2026-03-10 19:25:45', '2026-03-10 19:25:45'),
(20, 'AG-EBENKGA-26-00008', 16, 26, '2026-03-10', NULL, 'ACTIF', '2026-03-10 19:26:04', '2026-03-10 19:26:04'),
(21, 'AG-EBENKGA-26-00010', 16, 25, '2026-03-10', NULL, 'ACTIF', '2026-03-10 19:26:19', '2026-03-10 19:26:19'),
(23, 'AG-EBENKGA-26-00009', 16, 24, '2026-03-10', NULL, 'ACTIF', '2026-03-10 19:28:48', '2026-03-10 19:28:48'),
(24, 'AG-EBENKGA-26-00012', 16, 27, '2026-03-10', NULL, 'ACTIF', '2026-03-10 20:06:43', '2026-03-10 20:06:43'),
(25, 'AG-EBENKGA-26-00004', 17, NULL, '2026-03-14', NULL, 'ACTIF', '2026-03-14 19:09:19', '2026-03-14 19:09:19'),
(26, 'AG-EBENKGA-26-00006', 18, NULL, '2026-03-14', NULL, 'ACTIF', '2026-03-14 19:29:57', '2026-03-14 19:29:57'),
(27, 'AG-EBENKGA-26-00013', 16, 29, '2026-03-15', NULL, 'ACTIF', '2026-03-15 09:27:47', '2026-03-15 09:27:47'),
(28, 'AG-EBENKGA-26-00014', 16, 30, '2026-03-15', NULL, 'ACTIF', '2026-03-15 13:36:24', '2026-03-15 13:36:24'),
(29, 'AG-EBENKGA-26-00002', 1, 31, '2026-03-17', NULL, 'ACTIF', '2026-03-17 07:28:55', '2026-03-17 07:28:55');

-- --------------------------------------------------------

--
-- Structure de la table `tb_agents`
--

CREATE TABLE `tb_agents` (
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(191) NOT NULL,
  `postnom` varchar(191) DEFAULT NULL,
  `prenom` varchar(191) DEFAULT NULL,
  `sexe` enum('M','F') DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `adresse` varchar(191) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `date_embauche` date DEFAULT NULL,
  `statut` enum('actif','inactif') NOT NULL DEFAULT 'actif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_agents`
--

INSERT INTO `tb_agents` (`matricule`, `nom`, `postnom`, `prenom`, `sexe`, `date_naissance`, `telephone`, `email`, `adresse`, `photo`, `date_embauche`, `statut`, `created_at`, `updated_at`) VALUES
('AG-EBENKGA-26-00001', 'BMB', 'ADMIN', 'Système', 'M', NULL, NULL, 'bmb@bmb.cd', NULL, NULL, '2026-03-06', 'actif', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
('AG-EBENKGA-26-00002', 'MPUTU', 'KAYEMBE', 'David', 'M', '1998-11-19', '+243844160909', 'dv.kayemb@gmail.com', 'AV DU RAIL N°52 KELEKEL', 'agents/1773169550_1767781348104jpg', '2026-03-10', 'actif', '2026-03-10 18:05:50', '2026-03-10 18:05:50'),
('AG-EBENKGA-26-00003', 'BILONDA', 'KANUMUMANYI', 'MARGUERITE', 'F', '1996-12-06', '0', 'margueritepatience77@gmail.com', 'AV KABALU  N°3 Q/  NSELE COMMUNE DE NGANZA', 'agents/1773171254_margujpeg', '2026-03-10', 'actif', '2026-03-10 18:34:14', '2026-03-10 18:34:14'),
('AG-EBENKGA-26-00004', 'MUYAYA', 'NGALAMULUME', 'AUGUSTIN', 'M', '1994-12-12', NULL, NULL, NULL, 'agents/1773171337_1767966642258jpg', NULL, 'actif', '2026-03-10 18:35:37', '2026-03-10 18:35:37'),
('AG-EBENKGA-26-00005', 'DIBATAYI', 'KAMBILA', 'MERVEILLE', 'F', NULL, '0983260271', 'mdibatayi@gmail.com', 'n° 16 quartier mabondo c/ lukonga,', 'agents/1773171527_whatsapp-image-2026-03-10-at-161704jpeg', NULL, 'actif', '2026-03-10 18:38:47', '2026-03-10 18:38:47'),
('AG-EBENKGA-26-00006', 'LUKADI', 'KAMBILA', 'SCHADRAC', 'M', NULL, NULL, NULL, NULL, 'agents/1773171581_img-20260109-wa0034-2jpg', NULL, 'actif', '2026-03-10 18:39:41', '2026-03-10 18:39:41'),
('AG-EBENKGA-26-00007', 'NDJOKO', 'NGOMBULA', 'JEAN JACQUES', 'M', NULL, '+243 991574076', 'jeanjacquesndjoko95@gmail.com', 'N° 13, Av Likasi, Q/ Malanji, C/ Kananga', 'agents/1773173138_whatsapp-image-2026-03-09-at-201154jpeg', '2026-03-10', 'actif', '2026-03-10 18:43:26', '2026-03-10 19:05:38'),
('AG-EBENKGA-26-00008', 'BUYAMBA', 'KABUNDI', 'THERESE', 'F', '1998-12-10', '0991168767', 'buyambatherese@gmail.com', 'Av de la révolution n 17, kubuwa , ndesha', 'agents/1773172159_whatsapp-image-2026-03-10-at-075155jpeg', NULL, 'actif', '2026-03-10 18:49:19', '2026-03-10 18:49:19'),
('AG-EBENKGA-26-00009', 'LUKENGU', 'ILUNGA', 'JEAN-PIERRE', 'M', '1995-01-01', NULL, NULL, '42 avenue Djokopunda ,  Quartier : MABONDO  COMMUNE : lukonga', 'agents/1773172371_whatsapp-image-2026-03-10-at-113325jpeg', '2026-03-10', 'actif', '2026-03-10 18:52:51', '2026-03-10 18:52:51'),
('AG-EBENKGA-26-00010', 'OKITO', 'DJU', 'BONIFACE', 'M', NULL, '0970506564', 'boniokito49@gmail.com', 'Av Kamayi N° 10 Q/ Kamayi C/ kananga', 'agents/1773172702_img-20260209-094438-4052jpg-1jpeg', '2026-03-10', 'actif', '2026-03-10 18:58:22', '2026-03-10 18:58:22'),
('AG-EBENKGA-26-00011', 'KAPINGA', 'TSHIKONKA', 'ROSE', 'F', '1999-08-08', '0974589684', 'rosekapinga1999@gmail.com', 'av nzoba  n\' 3 Q: KAMAYI prison c: kananga', 'agents/1773173031_566e511d-0a66-42d3-af95-06a6413a6adc-2jfif', '2026-03-10', 'actif', '2026-03-10 19:03:51', '2026-03-10 19:03:51'),
('AG-EBENKGA-26-00012', 'KAZIKA', 'KABATU', 'GRACE', 'M', NULL, '0992071122', 'gracekazika50@gmail.com', '12 TSHISHILU, PLATEAU/BIKUKU, KANANGA', 'agents/1773176635_baefb445-858d-4295-933f-2887bc71da53-2jfif', '2026-03-10', 'actif', '2026-03-10 20:03:55', '2026-03-10 20:03:55'),
('AG-EBENKGA-26-00013', 'Test', 'test', 'Test', 'M', NULL, NULL, NULL, NULL, NULL, NULL, 'actif', '2026-03-15 09:23:22', '2026-03-15 09:23:22'),
('AG-EBENKGA-26-00014', 'MALAZI', 'DIBUE', 'JOHN', 'M', NULL, '0998413632', 'johnmalazi50@gmail.com', 'N°15285, AVENUE DU CANAL, QUARTIER :  MALANDJI ,COMMUNE:  DE KANANGA', NULL, '2026-03-15', 'actif', '2026-03-15 12:24:16', '2026-03-15 12:24:16'),
('AG-EBENKGA-26-00015', 'MULUMBA', 'LUPONGO', 'FRANCOIS', 'M', '1992-03-27', '0999172779', 'francoismulumba@gmail.com', 'AV DU STADE N°10 Q/KAPANDA C/KATOKA', NULL, '2026-03-16', 'actif', '2026-03-16 07:25:49', '2026-03-16 07:25:49');

-- --------------------------------------------------------

--
-- Structure de la table `tb_caisses_guichets`
--

CREATE TABLE `tb_caisses_guichets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code_guichet` varchar(20) NOT NULL,
  `type_guichet` enum('FIXE','MOBILE','CENTRAL') NOT NULL DEFAULT 'FIXE',
  `intitule` varchar(100) NOT NULL,
  `statut_operationnel` enum('OUVERT','FERME','SUSPENDU','EN_VERIFICATION') DEFAULT 'FERME',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_caisses_guichets`
--

INSERT INTO `tb_caisses_guichets` (`id`, `code_guichet`, `type_guichet`, `intitule`, `statut_operationnel`, `created_at`, `updated_at`) VALUES
(1, 'COFFRE_01', 'CENTRAL', 'Coffre-Fort Central', 'OUVERT', '2026-03-13 13:47:15', '2026-03-13 13:47:15'),
(20, 'G001', 'MOBILE', 'ZONE BATETELA A', 'EN_VERIFICATION', '2026-03-10 19:16:44', '2026-03-17 07:47:40'),
(21, 'G002', 'MOBILE', 'ZONE  BATETELA B', 'FERME', '2026-03-10 19:17:19', '2026-03-10 19:17:19'),
(22, 'G003', 'MOBILE', 'ZONE  BATETELA C', 'FERME', '2026-03-10 19:17:53', '2026-03-10 19:17:53'),
(23, 'G004', 'MOBILE', 'ZONE  KAMAYI', 'OUVERT', '2026-03-10 19:18:35', '2026-03-17 07:15:04'),
(24, 'G005', 'MOBILE', 'ZONE  OFIDA', 'FERME', '2026-03-10 19:18:59', '2026-03-10 19:18:59'),
(25, 'G006', 'MOBILE', 'ZONE  STADE', 'FERME', '2026-03-10 19:19:27', '2026-03-10 19:19:27'),
(26, 'GOO7', 'MOBILE', 'ZONE  MAGAR', 'OUVERT', '2026-03-10 19:20:00', '2026-03-17 07:15:02'),
(27, 'G007', 'MOBILE', 'ZONE TSHISELEKA', 'FERME', '2026-03-10 20:06:04', '2026-03-10 20:06:04'),
(29, 'G008', 'MOBILE', 'Guichet_test', 'OUVERT', '2026-03-15 09:26:12', '2026-03-15 09:39:10'),
(30, 'G009', 'MOBILE', 'DAKU YABISO', 'FERME', '2026-03-15 13:34:45', '2026-03-15 13:34:45'),
(31, 'G010', 'FIXE', 'GUICHET RETRAIT 1', 'EN_VERIFICATION', '2026-03-17 07:25:39', '2026-03-17 07:45:08');

-- --------------------------------------------------------

--
-- Structure de la table `tb_caisses_guichets_soldes`
--

CREATE TABLE `tb_caisses_guichets_soldes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `guichet_id` bigint(20) UNSIGNED NOT NULL,
  `devise_code` varchar(3) NOT NULL,
  `solde_en_caisse` decimal(18,2) NOT NULL DEFAULT 0.00,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_caisses_guichets_soldes`
--

INSERT INTO `tb_caisses_guichets_soldes` (`id`, `guichet_id`, `devise_code`, `solde_en_caisse`, `updated_at`) VALUES
(1, 1, 'CDF', 1000.00, '2026-03-14 15:21:40'),
(2, 1, 'EUR', 0.00, '2026-03-13 13:50:11'),
(3, 1, 'USD', 0.00, '2026-03-13 13:50:18'),
(29, 20, 'CDF', 0.00, '2026-03-10 20:16:44'),
(30, 20, 'USD', 0.00, '2026-03-10 20:16:44'),
(31, 21, 'CDF', 0.00, '2026-03-10 20:17:19'),
(32, 21, 'USD', 0.00, '2026-03-10 20:17:19'),
(33, 22, 'CDF', 0.00, '2026-03-10 20:17:53'),
(34, 22, 'USD', 0.00, '2026-03-10 20:17:53'),
(35, 23, 'CDF', 0.00, '2026-03-10 20:18:35'),
(36, 23, 'USD', 0.00, '2026-03-10 20:18:35'),
(37, 24, 'CDF', 0.00, '2026-03-10 20:18:59'),
(38, 24, 'USD', 0.00, '2026-03-10 20:18:59'),
(39, 25, 'CDF', 0.00, '2026-03-10 20:19:27'),
(40, 25, 'USD', 0.00, '2026-03-10 20:19:27'),
(41, 26, 'CDF', 0.00, '2026-03-13 18:16:31'),
(42, 26, 'USD', 0.00, '2026-03-10 20:20:00'),
(43, 27, 'CDF', 0.00, '2026-03-10 21:06:04'),
(44, 27, 'USD', 0.00, '2026-03-10 21:06:04'),
(45, 29, 'CDF', 0.00, '2026-03-15 10:26:12'),
(46, 29, 'EUR', 0.00, '2026-03-15 10:26:12'),
(47, 29, 'USD', 0.00, '2026-03-15 10:26:12'),
(48, 30, 'CDF', 0.00, '2026-03-15 14:34:45'),
(49, 30, 'USD', 0.00, '2026-03-15 14:34:45'),
(50, 31, 'CDF', 0.00, '2026-03-17 08:25:39'),
(51, 31, 'USD', 0.00, '2026-03-17 08:25:39');

-- --------------------------------------------------------

--
-- Structure de la table `tb_clients`
--

CREATE TABLE `tb_clients` (
  `matricule` varchar(191) NOT NULL,
  `code_zone` varchar(50) NOT NULL,
  `nom` varchar(191) NOT NULL,
  `postnom` varchar(191) NOT NULL,
  `prenom` varchar(191) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `telephone` varchar(191) DEFAULT NULL,
  `sexe` enum('M','F') NOT NULL,
  `date_naissance` date NOT NULL,
  `lieu_naissance` varchar(191) NOT NULL,
  `adresse` varchar(191) NOT NULL,
  `etat_civil` varchar(191) NOT NULL,
  `nom_conjoint` varchar(191) DEFAULT NULL,
  `type_piece_identite` varchar(191) NOT NULL,
  `lieu_delivrance_piece` varchar(191) NOT NULL,
  `date_delivrance_piece` date NOT NULL,
  `numero_piece_identite` varchar(191) NOT NULL,
  `photo` varchar(191) DEFAULT NULL,
  `secteur_activite` varchar(191) DEFAULT NULL,
  `type_activite` varchar(191) DEFAULT NULL,
  `nom_entreprise` varchar(191) DEFAULT NULL,
  `adresse_entreprise` varchar(191) DEFAULT NULL,
  `telephone_entreprise` varchar(191) DEFAULT NULL,
  `statut_entreprise` varchar(191) DEFAULT NULL,
  `nombre_annees_experience` varchar(191) DEFAULT NULL,
  `revenu_mensuel` decimal(15,2) DEFAULT NULL,
  `revenu_mensuel_devise` varchar(10) DEFAULT NULL,
  `autres_details_activite` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_clients`
--

INSERT INTO `tb_clients` (`matricule`, `code_zone`, `nom`, `postnom`, `prenom`, `email`, `telephone`, `sexe`, `date_naissance`, `lieu_naissance`, `adresse`, `etat_civil`, `nom_conjoint`, `type_piece_identite`, `lieu_delivrance_piece`, `date_delivrance_piece`, `numero_piece_identite`, `photo`, `secteur_activite`, `type_activite`, `nom_entreprise`, `adresse_entreprise`, `telephone_entreprise`, `statut_entreprise`, `nombre_annees_experience`, `revenu_mensuel`, `revenu_mensuel_devise`, `autres_details_activite`, `created_at`, `updated_at`) VALUES
('CL-EBENKGA-26-00001', 'ZON-EBENKGA-26-00001', 'MPUTU', 'TSHIPAMBA', 'NATHALIE', NULL, '0970830330', 'F', '1980-05-02', 'MBUANYA', 'AV LOPORIE, N°22 Q/SALONGO MUIMBA,  C/NGANZA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-03-04', '34948583906', 'clients/1773215085_LOGO EBEN 1.jpeg', 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-11 06:44:45', '2026-03-11 06:44:45'),
('CL-EBENKGA-26-00002', 'ZON-EBENKGA-26-00006', 'BINGOBE', 'MANKONI', 'MARCEL', 'bingobemarcel@gmail.com', '0994730081', 'M', '1973-04-29', 'NTANDEMBELO', 'N°104, AV/ LULUA  Q/MALANDJI C/KANANGA', 'Marié', 'MARLENE MIMBIE', 'Carte d\'électeur', 'KANANGA', '2023-01-28', '34920168228', NULL, NULL, 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-11 09:33:32', '2026-03-11 09:33:32'),
('CL-EBENKGA-26-00003', 'ZON-EBENKGA-26-00006', 'DIBALA', 'NTUMBA', 'STAEL', 'staeldibala90@gmail.com', '+243831447381', 'M', '1991-06-17', 'KANANGA', 'N°6 AV/KASAVUBU Q/MALANDJI C/KANANGA', 'Marié', 'BIUMA BEATRICE', 'Carte d\'électeur', 'KANANGA', '2023-03-17', '34948397607', NULL, 'COMMERCE', 'Commerce', 'SASABON', 'AVENUE MAGAR', '+24331447381', 'PRIVE', NULL, NULL, NULL, NULL, '2026-03-12 10:23:53', '2026-03-12 10:23:53'),
('CL-EBENKGA-26-00004', 'ZON-EBENKGA-26-00008', 'LOYA', 'OMEKENGE', 'Jean', NULL, '0998988462', 'M', '1986-10-20', 'LODJA', '85, Kolwezi, KELE-KELE, KATOKA', 'Marié', 'MG WALO', 'Carte d\'électeur', 'Kananga Ville', '2023-02-07', '34929773139', 'clients/1773382752_1000318417.jpeg', 'Vente du riz et d\'arachides', 'Commerce', NULL, 'Likasi, côté Moulin', NULL, NULL, '15', 500000.00, NULL, NULL, '2026-03-13 05:19:12', '2026-03-13 05:19:12'),
('CL-EBENKGA-26-00005', 'ZON-EBENKGA-26-00007', 'Muteba', 'Muteba', 'Jacques', NULL, '0983841537', 'M', '2004-03-27', 'Tshikapa', 'Av ditekemena n°15 Q/ kele-kele C/ katoka', 'Célibataire', NULL, 'Carte d\'électeur', 'Kazumba', '2023-02-11', '35096977796', NULL, 'Pièces de rechange', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 05:37:43', '2026-03-13 05:37:43'),
('CL-EBENKGA-26-00006', 'ZON-EBENKGA-26-00001', 'Bumue', 'Kabasu', 'Rose', NULL, '0970067545', 'F', '1996-08-28', 'Kananga', 'Nganza, nsele,nganza', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2024-09-18', '30134732661', NULL, NULL, 'Commerce', 'Maman Rose business', 'Batetela A', NULL, NULL, '10', 2000000.00, NULL, NULL, '2026-03-13 05:40:15', '2026-03-13 05:40:15'),
('CL-EBENKGA-26-00007', 'ZON-EBENKGA-26-00008', 'TSHIANDA', 'KAMENGA', 'Aimée', NULL, '0992193120', 'F', '1991-12-24', 'Katende', '1, TUDIKOLELE, MPOKOLO, KATOKA', 'Marié', 'MUKENDI TSHIONYI ISAAC', 'Carte d\'électeur', 'Kananga Ville', '2023-03-05', '34933582189', 'clients/1773384021_1000317280.jpeg', 'Marchandises de cuisine', 'Commerce', NULL, NULL, NULL, NULL, '25', 100000.00, NULL, NULL, '2026-03-13 05:40:21', '2026-03-13 05:40:21'),
('CL-EBENKGA-26-00008', 'ZON-EBENKGA-26-00003', 'Tshibola', 'Kayembe', 'Véronique', NULL, '0977773430', 'F', '1970-12-28', 'Kananga', 'N°30 av de produits,q/ tshinsambi,c/kananga', 'Marié', 'André kanundowi', 'Carte nationale d\'identité', 'Kananga', '2023-12-16', '34923375889', NULL, NULL, 'Service', NULL, NULL, NULL, NULL, '14', NULL, NULL, NULL, '2026-03-13 05:43:58', '2026-03-13 05:43:58'),
('CL-EBENKGA-26-00009', 'ZON-EBENKGA-26-00002', 'LUFULUABO', 'MUSUBA', 'VÉRONIQUE', NULL, '0894263051', 'F', '1988-06-21', 'Kinshasa', 'Av.plateau, Q/maladji,C/kananga', 'Marié', 'MBO GAUTHIER', 'Carte d\'électeur', 'Kananga', '2023-02-12', '34918381791', NULL, 'Habit', 'Commerce', 'Kankash', NULL, NULL, 'Kankash', '3', NULL, NULL, NULL, '2026-03-13 05:50:59', '2026-03-13 05:50:59'),
('CL-EBENKGA-26-00010', 'ZON-EBENKGA-26-00001', 'Lukadi', 'Mubiayi', 'Rosalie', NULL, '0975328247', 'F', '1978-12-12', 'Kananga', 'Du rail, le kele,katoka', 'Marié', 'Augustin Muanza', 'Carte d\'électeur', 'Kananga', '2023-02-21', '34928982326', NULL, 'Vente du lait.', 'Commerce', NULL, NULL, NULL, NULL, '10', 200000.00, NULL, NULL, '2026-03-13 05:55:07', '2026-03-13 05:55:07'),
('CL-EBENKGA-26-00011', 'ZON-EBENKGA-26-00003', 'Kayembe', 'Kabasele', 'Olga', NULL, '098607721', 'F', '1992-08-25', 'Lubumbashi', 'Av. Mpokolo,q/ katoka 2 c/ katoka', 'Marié', 'Benga pierre', 'Carte nationale d\'identité', 'Kananga', '2023-02-05', '34930370487', NULL, NULL, 'Service', NULL, NULL, NULL, NULL, '9', NULL, NULL, NULL, '2026-03-13 05:58:24', '2026-03-13 05:58:24'),
('CL-EBENKGA-26-00012', 'ZON-EBENKGA-26-00007', 'Bambabibi', 'Mashala', 'Albertine', NULL, '0997332608', 'F', '1968-11-11', 'Kananga', 'Av Bandaka n°60 Q/katoka 2 C/ katoka', 'Marié', 'Tshiabukole Donatien', 'Carte d\'électeur', 'Kananga', '2023-02-02', '34931169414', NULL, 'Atelier de couture', 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 05:59:53', '2026-03-13 05:59:53'),
('CL-EBENKGA-26-00013', 'ZON-EBENKGA-26-00002', 'MBO', 'NGWANGO', 'GAUTIER', NULL, '0892398119', 'M', '1988-07-13', 'Kinshasa', 'Av.plateau, Q/maladji,C/kananga', 'Marié', 'VÉRONIQUE LUFULUABO', 'Carte d\'électeur', 'Kananga', '2023-02-12', '34948977425', NULL, NULL, NULL, NULL, NULL, '3', NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:00:46', '2026-03-13 06:00:46'),
('CL-EBENKGA-26-00014', 'ZON-EBENKGA-26-00008', 'LUBI', 'MULUMBA', 'Monique', NULL, '0970642700', 'F', '1989-11-24', 'Ndekesha', '2, BUKASA, MPOKOLO, KATOKA', 'Marié', 'Kalamba MUKENDI Moïse', 'Carte d\'électeur', 'Kananga Ville', '2023-03-08', '35106190142', 'clients/1773385445_1000317363.jpeg', 'Marchandises de cuisine, farines et arachides', 'Commerce', NULL, 'Likasi', NULL, NULL, '15', 200000.00, NULL, NULL, '2026-03-13 06:04:05', '2026-03-13 06:04:05'),
('CL-EBENKGA-26-00015', 'ZON-EBENKGA-26-00006', 'Kasonga', 'Kasonga', 'Pierre', NULL, '0961250336', 'M', '1980-05-05', 'Tshikula', 'Route ilebo n°5 mpemba, kananga', 'Marié', 'Céline mupanga', 'Carte d\'électeur', 'Kananga', '2023-03-02', '34916585116', NULL, NULL, 'Autre', 'G7', NULL, NULL, 'Transporteur', NULL, NULL, NULL, NULL, '2026-03-13 06:04:59', '2026-03-13 06:04:59'),
('CL-EBENKGA-26-00016', 'ZON-EBENKGA-26-00001', 'Tshitumbu', 'Kanyinda', 'Leonard', NULL, '0975639241', 'M', '1999-07-27', 'Kananga', 'Malandi,kananga', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-02-07', '34922773745', NULL, 'Friperie', 'Commerce', NULL, NULL, NULL, NULL, '15', 150000.00, NULL, NULL, '2026-03-13 06:08:34', '2026-03-13 06:08:34'),
('CL-EBENKGA-26-00017', 'ZON-EBENKGA-26-00002', 'TSHIABU', 'TSHITENGE', 'HENRIETTE', NULL, '0975384287', 'F', '1988-05-15', 'DEMBA', 'Av.kayembe, Q/katoka,C/katoka', 'Marié', 'TSHIM TSHITENGE', 'Carte d\'électeur', 'Kananga', '2023-02-21', '34928982311', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:10:48', '2026-03-13 06:10:48'),
('CL-EBENKGA-26-00018', 'ZON-EBENKGA-26-00006', 'Ntumba', 'Kalala', 'Lydie', NULL, '0971576460', 'F', '1995-04-04', 'Kananga', 'Iproma,malandji, kananga', 'Marié', 'Tshitala bruno', 'Carte d\'électeur', 'Kananga', '2003-02-04', '34926969097', NULL, 'Vente sac', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:14:16', '2026-03-13 06:14:16'),
('CL-EBENKGA-26-00019', 'ZON-EBENKGA-26-00001', 'Tshibola', 'Kasonga', 'Paulin', NULL, '0970249030', 'M', '1987-12-09', 'Kananga', 'Kabasele mpokolo katoka', 'Marié', 'Tshilanda verro', 'Carte d\'électeur', 'Kananga', '2023-03-07', '3572787826', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, '15', 850000.00, NULL, NULL, '2026-03-14 07:08:21', '2026-03-14 07:08:21'),
('CL-EBENKGA-26-00020', 'ZON-EBENKGA-26-00008', 'DIMUANGI', 'MUTUALA', 'Madeleine', NULL, '0990226820', 'F', '1987-06-03', 'Kananga', '22, MOYO, DIKONGAYI, LUKONGA', 'Marié', 'WETU NGANYI Adolph', 'Carte d\'électeur', 'Kananga Ville', '2023-02-23', '34938179068', 'clients/1773386306_1000317275.jpeg', 'Vente de planches', 'Commerce', NULL, NULL, NULL, NULL, '12', 1000000.00, NULL, NULL, '2026-03-13 06:18:26', '2026-03-13 06:18:26'),
('CL-EBENKGA-26-00021', 'ZON-EBENKGA-26-00007', 'Badibanga', 'Kapuku', 'Albert', NULL, '0975061945', 'M', '1988-11-11', 'Kananga', 'Av mpokolo n°06 Q/ Lumumba C/ lukonga', 'Marié', NULL, 'Carte d\'électeur', 'Kananga', '2023-02-11', '34938574833', NULL, 'Atelier de menuiserie', 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:18:35', '2026-03-13 06:18:35'),
('CL-EBENKGA-26-00022', 'ZON-EBENKGA-26-00006', 'Meta', 'Badibanga', 'Jackie', NULL, '0993011464', 'F', '1976-12-12', 'Kananga', 'Walikale2,malandji, kananga', 'Marié', 'Tshibuabua henri', 'Carte d\'électeur', 'Kananga', '2003-03-22', '34918996538', NULL, 'Vente boissons', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:18:56', '2026-03-13 06:18:56'),
('CL-EBENKGA-26-00023', 'ZON-EBENKGA-26-00002', 'BEBA', 'TSHITENGE', 'BETY', NULL, '0990745663', 'F', '1986-08-08', 'DEMBA', 'Av. Du canal,Q/ tshisambi,C/kananga', 'Marié', 'Jose Muanza', 'Carte d\'électeur', 'Kananga', '2023-03-08', '34918190461', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '14', 500.00, NULL, NULL, '2026-03-13 06:20:07', '2026-03-13 06:20:07'),
('CL-EBENKGA-26-00024', 'ZON-EBENKGA-26-00007', 'Bakatumana', 'Kalamba', 'Véronique', NULL, '0974038634', 'F', '1970-01-01', 'Zambie', 'Av du canal n° 15  Q/ kele-kele C/ katoka', 'Marié', NULL, 'Carte d\'électeur', 'Kananga', '2023-02-12', '349343375625', NULL, 'Atelier de couture', 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:24:16', '2026-03-13 06:24:16'),
('CL-EBENKGA-26-00025', 'ZON-EBENKGA-26-00006', 'Masengu', 'Ntekabimpa', 'Marceline', NULL, '0808415368', 'F', '1983-12-06', 'Kananga', 'Malandji, senel,kananga', 'Veuf', NULL, 'Carte d\'électeur', 'Kananga', '2003-03-10', '34928578581', NULL, 'Vente sucre et farine', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:29:56', '2026-03-13 06:29:56'),
('CL-EBENKGA-26-00026', 'ZON-EBENKGA-26-00008', 'KADANGA', 'BUKAMA', 'Christine', 'christinekadanga17@gmail.com', '0992684439', 'F', '1979-04-05', 'Mbuji mayi', '18, Tshishimbi, Bikuku, Plateau, Kananga', 'Veuf', NULL, 'Carte d\'électeur', 'Luiza', '2023-03-09', '35003382891', 'clients/1773386997_1000318451.jpeg', 'Agent DGM KANANGA', 'Service', NULL, NULL, NULL, NULL, '30', 1000000.00, NULL, NULL, '2026-03-13 06:29:57', '2026-03-13 06:29:57'),
('CL-EBENKGA-26-00027', 'ZON-EBENKGA-26-00002', 'MISENGA', 'BAKUTEKA', 'JULIE', NULL, '0994230611', 'F', '1993-01-01', 'Kananga', 'Av.beya,Q/ tshisambi,C/kananga', 'Marié', 'KUSOMBA ERICK', 'Carte d\'électeur', 'Kananga', '2023-03-02', '34918566784', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, 200.00, NULL, NULL, '2026-03-13 06:29:59', '2026-03-13 06:29:59'),
('CL-EBENKGA-26-00028', 'ZON-EBENKGA-26-00007', 'Bibi', 'Kayumba', 'Annie', NULL, '0995709786', 'F', '1993-03-25', 'Kananga', 'Av du peuple n°39 Q/ dikongayi', 'Marié', NULL, 'Carte d\'électeur', 'Kananga', '2023-02-02', '3493096536', NULL, 'Atelier de couture', 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:30:28', '2026-03-13 06:30:28'),
('CL-EBENKGA-26-00029', 'ZON-EBENKGA-26-00006', 'Mutombo', 'Mulowayi', 'Pascal', NULL, '0999457127', 'F', '1986-04-02', 'Kananga', 'Nganza 130,kambala,nganza', 'Marié', 'Ngalula bakajika', 'Carte d\'électeur', 'Kananga', '2023-03-25', '34953181129A', NULL, 'Divers', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:36:10', '2026-03-13 06:36:10'),
('CL-EBENKGA-26-00030', 'ZON-EBENKGA-26-00006', 'Mutombo', 'Mulowayi', 'Pascal', NULL, '0999457127', 'F', '1986-04-02', 'Kananga', 'Nganza 130,kambala,nganza', 'Marié', 'Ngalula bakajika', 'Carte d\'électeur', 'Kananga', '2023-03-25', '34953181129Z', NULL, 'Divers', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:36:12', '2026-03-13 06:36:12'),
('CL-EBENKGA-26-00031', 'ZON-EBENKGA-26-00006', 'Mutombo', 'Mulowayi', 'Pascal', NULL, '0999457127', 'F', '1986-04-02', 'Kananga', 'Nganza 130,kambala,nganza', 'Marié', 'Ngalula bakajika', 'Carte d\'électeur', 'Kananga', '2023-03-25', '34953181129D', NULL, 'Divers', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:36:14', '2026-03-13 06:36:14'),
('CL-EBENKGA-26-00032', 'ZON-EBENKGA-26-00006', 'Mutombo', 'Mulowayi', 'Pascal', NULL, '0999457127', 'F', '1986-04-02', 'Kananga', 'Nganza 130,kambala,nganza', 'Marié', 'Ngalula bakajika', 'Carte d\'électeur', 'Kananga', '2023-03-25', '34953181129F', NULL, 'Divers', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:36:16', '2026-03-13 06:36:16'),
('CL-EBENKGA-26-00033', 'ZON-EBENKGA-26-00006', 'Katukumbanyi', 'Katuashi', 'Pius', NULL, '0992951261', 'M', '1954-12-11', 'Balabala', 'Du mangier 36, kamayi, kananga', 'Marié', 'Mijinga Charlotte', 'Carte d\'électeur', 'Kananga', '2003-03-09', '34921186339', NULL, 'Pharmacie', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:42:39', '2026-03-13 06:42:39'),
('CL-EBENKGA-26-00034', 'ZON-EBENKGA-26-00006', 'Ngalula', 'Manyema', 'Lysette', NULL, '0974675972', 'F', '2003-01-21', 'Kananga', 'Dibatayi 16,tshinsambi, kananga', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2003-03-10', '34956771408', NULL, 'Vente Boissons', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:47:06', '2026-03-13 06:47:06'),
('CL-EBENKGA-26-00035', 'ZON-EBENKGA-26-00006', 'Lukadi', 'Lukadi', 'Verro', NULL, NULL, 'F', '1994-03-19', 'Tshikapa', 'Tulengele 20, plateau, kananga', 'Marié', 'Ntumba henri', 'Carte d\'électeur', 'Kananga', '2003-03-12', '34927389919D', NULL, NULL, 'Autre', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:58:10', '2026-03-13 06:58:10'),
('CL-EBENKGA-26-00036', 'ZON-EBENKGA-26-00006', 'Lukadi', 'Lukadi', 'Verro', NULL, NULL, 'F', '1994-03-19', 'Tshikapa', 'Tulengele 20, plateau, kananga', 'Marié', 'Ntumba henri', 'Carte d\'électeur', 'Kananga', '2003-03-12', '34927389919AS', NULL, NULL, 'Autre', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 06:58:12', '2026-03-13 06:58:12'),
('CL-EBENKGA-26-00037', 'ZON-EBENKGA-26-00003', 'Mutudi', 'Tshiame', 'Marguerite', NULL, '0995240445', 'F', '1975-12-21', 'Goma', 'Av, mukendi q/ mabondo c/ lukonga n\'° 15', 'Marié', 'Kambila Bernard', 'Carte nationale d\'identité', 'Kananga', '2023-02-16', '34936577293', 'clients/1773389722_1000120044.jpeg', NULL, 'Autre', NULL, NULL, NULL, NULL, '20', NULL, NULL, NULL, '2026-03-13 07:15:22', '2026-03-13 07:15:22'),
('CL-EBENKGA-26-00038', 'ZON-EBENKGA-26-00001', 'Misenga', 'Kabiena', 'Hélène', NULL, '0995110154', 'M', '1980-03-03', 'Kananga', 'Kasavubu,malandji, kananga', 'Marié', 'Beya samy', 'Carte d\'électeur', 'Kananga', '2023-02-02', '34918982936', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, '8', 450000.00, NULL, NULL, '2026-03-14 07:14:30', '2026-03-14 07:14:30'),
('CL-EBENKGA-26-00039', 'ZON-EBENKGA-26-00006', 'Kanyinku', 'Biakutuele', 'Timothée', NULL, '0978257490', 'M', '2004-03-10', 'Kananga', 'Kamakadi 7,Lumumba2 , lukonga', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-03', '34943581805', NULL, 'Pharmacie', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 07:19:42', '2026-03-14 07:19:42'),
('CL-EBENKGA-26-00040', 'ZON-EBENKGA-26-00003', 'Tshiabende', 'Tshimbawu', 'Elysée', NULL, '0971377772', 'F', '1965-08-28', 'Kananga', 'Av kasavubu, q/ dikongayi c/ lukonga n° 41', 'Marié', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-07', '34938373251DD', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '17', NULL, NULL, NULL, '2026-03-13 07:29:20', '2026-03-13 07:29:20'),
('CL-EBENKGA-26-00041', 'ZON-EBENKGA-26-00001', 'Kazubu', 'Kasawu', 'Martin', NULL, '0993565060', 'F', '1965-06-24', 'Kananga', 'Ndjoko punda,kele kele, katoka', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-01-25', '34929766948', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, '25', 250000.00, NULL, NULL, '2026-03-14 07:20:05', '2026-03-14 07:20:05'),
('CL-EBENKGA-26-00042', 'ZON-EBENKGA-26-00003', 'Bipendu', 'Kazadi', 'Marie', NULL, '0852889844', 'F', '1990-10-11', 'Kananga', 'Av du rail, q/ Béthel, c/ kananga', 'Marié', 'Mukenda bebel', 'Carte nationale d\'identité', 'Kananga', '2023-02-12', '34933774728', NULL, NULL, 'Service', NULL, NULL, NULL, NULL, '8', NULL, NULL, NULL, '2026-03-13 07:49:45', '2026-03-13 07:49:45'),
('CL-EBENKGA-26-00043', 'ZON-EBENKGA-26-00001', 'Olenga', 'Ndjudi', 'Matilde', NULL, '0993504294', 'F', '1998-05-17', 'Lodia', 'Ndjoko punda,kele kele, katoka', 'Marié', 'Antoine umandi', 'Carte d\'électeur', 'Kananga', '2023-03-07', '34934386557', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, '7', 150000.00, NULL, NULL, '2026-03-14 07:24:21', '2026-03-14 07:24:21'),
('CL-EBENKGA-26-00044', 'ZON-EBENKGA-26-00001', 'Misenga', 'Ntambue', 'Antoinette', NULL, '0998168733', 'F', '1983-10-26', 'Kananga', 'Du canal,malandji, kananga', 'Marié', 'Emmanuel', 'Carte d\'électeur', 'Kananga', '2023-02-27', '34918181469', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, '18', 500000.00, NULL, NULL, '2026-03-14 07:30:06', '2026-03-14 07:30:06'),
('CL-EBENKGA-26-00045', 'ZON-EBENKGA-26-00001', 'Katuma', 'Kalombo', 'Deborah', NULL, '0983615109', 'M', '1995-03-25', 'Kananga', 'Du produit,Tshisamba, kananga', 'Marié', 'Songo Lambert', 'Carte d\'électeur', 'Kananga', '2023-02-04', '34920384942', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, '4', 800000.00, NULL, NULL, '2026-03-14 07:43:05', '2026-03-14 07:43:05'),
('CL-EBENKGA-26-00046', 'ZON-EBENKGA-26-00004', 'KANKONDE', 'TSHIMBALANGA', 'PIERRE', NULL, '0973141851', 'M', '1954-12-24', 'TSHIDIMBA', 'N°33 AV KALAMBA, Q/ MALANDJI, C/ KANANGA', 'Marié', 'TSHIBOLA HELENE', 'Carte d\'électeur', 'KANANGA', '2023-02-04', '34921169636', 'clients/1773481388_1000015906.jpeg', 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 08:43:08', '2026-03-14 08:43:08'),
('CL-EBENKGA-26-00047', 'ZON-EBENKGA-26-00004', 'MBUYI', 'KANANGA', 'RÉGINE', NULL, '0857795383', 'F', '1987-07-20', 'KANANGA', 'N°30 Av/ Musangana Q/ Kamayi C/ Kananga', 'Marié', 'NTUMBA FLORENT', 'Carte d\'électeur', 'KANANGA', '2023-02-10', '35196165576', NULL, 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 09:01:10', '2026-03-14 09:01:10'),
('CL-EBENKGA-26-00048', 'ZON-EBENKGA-26-00003', 'Tshibola', 'Tshimpaka', 'Catherine', NULL, '0971410690', 'F', '1976-10-29', 'Kananga', 'Av du marché,q/ kamayi, c/ kananga n° 13', 'Marié', 'Nkongolo Abraham', 'Carte nationale d\'identité', 'Kananga', '2023-03-30', '34929993564', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '21', NULL, NULL, NULL, '2026-03-13 07:57:40', '2026-03-13 07:57:40'),
('CL-EBENKGA-26-00049', 'ZON-EBENKGA-26-00003', 'Kasongo', 'Kadima', 'François', NULL, '0995749646', 'F', '1994-11-25', 'Kananga', 'Av meta, Q/ mpokolo,c/ katoka n° 3', 'Marié', 'Thérèse Ngalula', 'Carte nationale d\'identité', 'Kananga', '2023-03-15', '34938375088', 'clients/1773392882_1000120067.jpeg', NULL, 'Autre', NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-13 08:08:02', '2026-03-13 08:08:02'),
('CL-EBENKGA-26-00050', 'ZON-EBENKGA-26-00008', 'KAPINGA', 'MULUMBA', 'Nana', NULL, '0978246128', 'F', '1978-07-05', 'KINSHASA', 'DABUA', 'Marié', 'Kenda souzane', 'Carte d\'électeur', 'Kananga Ville', '2023-02-05', '34941980555', 'clients/1773409633_1000318648.jpeg', 'Marchandises de cuisine', 'Commerce', NULL, NULL, NULL, NULL, '25', 250000.00, NULL, NULL, '2026-03-13 12:47:13', '2026-03-13 12:47:13'),
('CL-EBENKGA-26-00051', 'ZON-EBENKGA-26-00008', 'KABAKADUA', 'BISUYI', 'Madeleine', NULL, '0997845131', 'F', '1974-04-27', 'MIKALAYI', '12, Buya, MPOKOLO, KATOKA', 'Veuf', NULL, 'Carte d\'électeur', 'Kananga Ville', '2023-02-26', '34930778957', 'clients/1773410162_1000318646.jpeg', 'Poissons salés et fumés', 'Commerce', NULL, NULL, NULL, NULL, '35', 300000.00, NULL, NULL, '2026-03-13 12:56:02', '2026-03-13 12:56:02'),
('CL-EBENKGA-26-00052', 'ZON-EBENKGA-26-00008', 'BUNDU', 'TSHIPAMBA', 'Verro', NULL, '0991921359', 'F', '1979-10-20', 'Kananga', '08, Mboma, KELE-KELE, KATOKA', 'Marié', 'KUMONANGANA DIEUDONNÉ', 'Carte d\'électeur', 'Kananga Ville', '2023-02-23', '34209189501', 'clients/1773410846_1000318576.jpeg', 'Moulin', 'Service', NULL, NULL, NULL, NULL, '25', 2000000.00, NULL, NULL, '2026-03-13 13:07:26', '2026-03-13 13:07:26'),
('CL-EBENKGA-26-00053', 'ZON-EBENKGA-26-00008', 'MALU', 'MUKADILA', 'Thérèse', NULL, '0977118875', 'F', '1992-05-05', 'Kananga', 'Tshisumpa Ntambue, MPOKOLO, KATOKA', 'Marié', 'KALOMBA Félicien', 'Carte d\'électeur', 'Kananga Ville', '2023-02-09', '34933173883', 'clients/1773411352_1000318571.jpeg', 'Marchandises de cuisine', 'Commerce', NULL, NULL, NULL, NULL, '10', 200000.00, NULL, NULL, '2026-03-13 13:15:52', '2026-03-13 13:15:52'),
('CL-EBENKGA-26-00054', 'ZON-EBENKGA-26-00008', 'KABEYA', 'MANDE', 'Senghor', NULL, '0963875484', 'M', '1992-04-04', 'Kananga', '14, KABASELE, MPOKOLO, KATOKA', 'Marié', 'MUBABINGE HENRIETTE', 'Carte d\'électeur', 'Kananga Ville', '2023-02-02', '34933780547', 'clients/1773411950_1000318569.jpeg', 'Divers', 'Commerce', NULL, NULL, NULL, NULL, '12', 350000.00, NULL, NULL, '2026-03-13 13:25:50', '2026-03-13 13:25:50'),
('CL-EBENKGA-26-00055', 'ZON-EBENKGA-26-00008', 'NGALULA', 'BAKAJIKA', 'Esther', NULL, '0990772294', 'F', '1992-03-16', 'Lubumbashi', '27, de la carrière, malandji, Kananga', 'Marié', 'Michel BAKAJIKA', 'Carte d\'électeur', 'Kananga Ville', '2023-03-06', '34922769599', NULL, 'Huile de noix et noix de palme', 'Service', NULL, NULL, NULL, NULL, '15', 2500000.00, NULL, NULL, '2026-03-13 13:39:23', '2026-03-13 13:39:23'),
('CL-EBENKGA-26-00056', 'ZON-EBENKGA-26-00004', 'BUPISHI', 'IYOLO', 'VICTORINE', NULL, '0992702282', 'F', '1999-12-18', 'Katshimu', 'N°13 Av Likasi Q/ Malanji C/ Kananga', 'Marié', 'Jean Jacques Ndjoko', 'Carte d\'électeur', 'Kananga', '2023-02-23', '349527528334', 'clients/1773413402_1000007147.jpeg', 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 13:50:02', '2026-03-13 20:47:12'),
('CL-EBENKGA-26-00057', 'ZON-EBENKGA-26-00004', 'NTUMBA', 'Kadibu', 'Saoudien', NULL, '0997777709', 'M', '1986-04-13', 'Mbuji Mayi', 'N°02 Av Decade Q/ Kamayi C/ Kananga', 'Marié', 'Dionga Kande Kati', 'Carte d\'électeur', 'KANANGA', '2023-02-19', '34925980179', 'clients/1773413925_1000015626.jpeg', 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 13:58:45', '2026-03-13 13:58:45'),
('CL-EBENKGA-26-00058', 'ZON-EBENKGA-26-00004', 'MUKENGESHAYI', 'Kalala', 'Jean', NULL, '0999849715', 'M', '1999-08-13', 'Kananga', 'N°13, Av Ditalala Q/ Kamayi C/ Kananga', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-04', '34918373326', 'clients/1773414530_1000015627.jpeg', 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 14:08:50', '2026-03-13 14:08:50'),
('CL-EBENKGA-26-00059', 'ZON-EBENKGA-26-00004', 'Tshibinda', 'Manyayi', 'Valérie', NULL, '0992248422', 'M', '1986-08-06', 'Kananga', 'N° 20 Av Salongo, Q/ Mpokolo C/ Katoka', 'Marié', 'Francine Okako', 'Carte d\'électeur', 'Demba', '2023-02-19', '35155167306', 'clients/1773414802_1000015628.jpeg', 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 14:13:22', '2026-03-13 14:13:22'),
('CL-EBENKGA-26-00060', 'ZON-EBENKGA-26-00004', 'Muambayi', 'Ngalamulume', 'Adonis', NULL, '0820708998', 'M', '1997-12-12', 'Kananga', 'N° 17 Av Du Canal, Q/ Malanji, C/ Kananga', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-18', '349437949435', NULL, 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 14:17:52', '2026-03-13 14:17:52'),
('CL-EBENKGA-26-00061', 'ZON-EBENKGA-26-00006', 'Banshima', 'Kasanganayi', 'Bernadette', NULL, '0989089231', 'M', '1925-04-12', 'Demba', 'Révolution 17, lubuwa, ndesha', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2003-02-11', '34938375388', NULL, 'Vente savons', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 14:22:17', '2026-03-13 14:22:17'),
('CL-EBENKGA-26-00062', 'ZON-EBENKGA-26-00004', 'Mbuyi', 'Nkongolo', 'Tina', NULL, '0976325223', 'F', '2000-02-07', 'Mfuamba', 'N°01 Av Makasha Q/ Mobutu, C/ Kananga', 'Marié', 'Cibuabua Martin', 'Carte d\'électeur', 'KANANGA', '2023-03-14', '34945576236', NULL, 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 14:23:06', '2026-03-13 14:23:06'),
('CL-EBENKGA-26-00063', 'ZON-EBENKGA-26-00004', 'Tshibuabua', 'Ntumba', 'Aimer', NULL, '0970093726', 'F', '1999-11-08', 'Kamonya', 'N° 008 Av Nganza, Q/ Kamayi C/ Kananga', 'Marié', 'Léon Cibuabua', 'Carte d\'électeur', 'KANANGA', '2023-02-08', '35057574046', NULL, 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 14:27:04', '2026-03-13 14:27:04'),
('CL-EBENKGA-26-00064', 'ZON-EBENKGA-26-00004', 'Mbuyamba', 'Tshimanga', 'Blandine', NULL, '0990498629', 'F', '1975-01-02', 'Kongolo', 'N°07 Tshiala Q/ Kamayi C/ Kananga', 'Marié', 'Mudimba', 'Carte d\'électeur', 'KANANGA', '2023-02-05', '34925972626', NULL, 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 14:31:32', '2026-03-13 14:31:32'),
('CL-EBENKGA-26-00065', 'ZON-EBENKGA-26-00004', 'Mbuyi', 'Yalina', 'Marie', NULL, '0993985428', 'F', '1993-04-08', 'Tshikapa', 'N°09 Av Du Marché, Q/ Kamayi C/ Kananga', 'Marié', 'André Mpongo', 'Carte d\'électeur', 'KANANGA', '2023-02-15', '34922173499', NULL, 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 14:35:52', '2026-03-13 14:35:52'),
('CL-EBENKGA-26-00066', 'ZON-EBENKGA-26-00004', 'Mushiya', 'Ntambue', 'Rose', NULL, '0990296517', 'F', '1993-09-08', 'Bilomba', 'N°08 Av du Mangieur Q/ Kamayi C/ Kananga', 'Marié', 'Louis NTUMBA', 'Carte d\'électeur', 'KANANGA', '2023-03-09', '34922382768', NULL, 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 14:39:52', '2026-03-13 14:39:52'),
('CL-EBENKGA-26-00067', 'ZON-EBENKGA-26-00004', 'Lupetu', 'Kandolo', 'Antoinette', NULL, '0999849715', 'F', '2000-12-12', 'Kananga', 'N° 17 Av Du Canal, Q/ Malanji, C/ Kananga', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-14', '34926573559', NULL, 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 14:43:44', '2026-03-13 14:43:44'),
('CL-EBENKGA-26-00068', 'ZON-EBENKGA-26-00004', 'Misenga', 'Mukenge', 'Rachel', NULL, '0984875091', 'F', '1990-08-15', 'Kananga', 'N° 23 Av Du Canal, Q/ Kamayi C/ Kananga', 'Marié', 'Tshibambe Désiré', 'Carte d\'électeur', 'KANANGA', '2023-02-09', '34949379865', NULL, 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 14:47:40', '2026-03-13 14:47:40'),
('CL-EBENKGA-26-00069', 'ZON-EBENKGA-26-00004', 'Lupetu', 'Mulumba', 'Julienne', NULL, '0991774341', 'F', '2000-03-13', 'Kananga', 'N° 82 Av Luemba Q/ Nganza sud C/ Nganza', 'Marié', 'Célestin Mutshipayi', 'Carte d\'électeur', 'KANANGA', '2023-03-10', '34952106233', NULL, 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 14:51:46', '2026-03-13 14:51:46'),
('CL-EBENKGA-26-00070', 'ZON-EBENKGA-26-00004', 'Tshiyoyo', 'KASANKA', 'Léonard', NULL, '0812976981', 'M', '1981-09-14', 'Katende', 'N° 07 Av Route Kanyoka, Q/ Tshinsambi, C/ Kananga', 'Marié', 'LUSAMBA Martin', 'Carte d\'électeur', 'Kananga', '2023-03-31', '34919768698', NULL, 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 14:57:18', '2026-03-13 14:57:18'),
('CL-EBENKGA-26-00071', 'ZON-EBENKGA-26-00004', 'Mfuamba', 'Buabua', 'Joseph', NULL, '0993513183', 'M', '1997-07-13', 'Kananga', 'N°32 Av Yombo, Q/ Kamayi, C/ Kananga', 'Marié', 'Kapinga Sizel', 'Carte d\'électeur', 'KANANGA', '2023-02-27', '34926175864', NULL, 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 15:03:10', '2026-03-13 15:03:10'),
('CL-EBENKGA-26-00072', 'ZON-EBENKGA-26-00004', 'Kapinga', 'Ilunga', 'Christine', NULL, '0976300739', 'F', '2023-03-13', 'Kananga', 'N° 06 AV MBUANYA, Q/ PLATEAU, C/ KANANGA', 'Marié', 'Anaclet Ntambue', 'Carte d\'électeur', 'KANANGA', '2023-02-18', '34922175564', NULL, 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 15:08:13', '2026-03-13 15:08:13'),
('CL-EBENKGA-26-00073', 'ZON-EBENKGA-26-00001', 'Malu', 'Mukengeshayi', 'Marcel', NULL, '850145888', 'M', '1878-11-12', 'Kananga', 'Tshibala,kapanda, katoka', 'Marié', 'Rose kankolongo', 'Carte d\'électeur', 'Kananga', '2023-02-05', '34933171667', NULL, 'Divers', 'Commerce', NULL, NULL, NULL, NULL, '10', 500000.00, NULL, NULL, '2026-03-13 18:12:10', '2026-03-13 18:12:10'),
('CL-EBENKGA-26-00074', 'ZON-EBENKGA-26-00001', 'Mujangi', 'Mulamba', 'Madeleine', NULL, '0994587472', 'F', '1896-06-30', 'Bena leka', 'Butoke,malandji, kananga', 'Marié', 'Adolphe ngalamulume', 'Carte d\'électeur', 'Kananga', '2023-02-08', '34918175459', NULL, 'Divers', 'Commerce', NULL, NULL, NULL, NULL, '12', 1000000.00, NULL, NULL, '2026-03-13 18:25:37', '2026-03-13 18:25:37'),
('CL-EBENKGA-26-00075', 'ZON-EBENKGA-26-00001', 'Ngalula', 'Buakantua', 'Regine', NULL, '0994954359', 'F', '1979-06-22', 'Kananga', 'Kabasele, mpokolo,katoka', 'Marié', 'Ilunga clement', 'Carte d\'électeur', 'Kananga', '2023-02-04', '34934972534', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, '5', 180000.00, NULL, NULL, '2026-03-13 18:30:42', '2026-03-13 18:30:42'),
('CL-EBENKGA-26-00076', 'ZON-EBENKGA-26-00001', 'Ahido', 'Djudi', 'Thérèse', NULL, '0980555223', 'F', '1988-02-25', 'Lodia', 'Lulua, batetela, kananga', 'Marié', 'John', 'Carte d\'électeur', 'Konga kazembe', '2023-02-17', '34929780209', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, '9', 200000.00, NULL, NULL, '2026-03-13 18:36:48', '2026-03-13 18:36:48'),
('CL-EBENKGA-26-00077', 'ZON-EBENKGA-26-00001', 'Nzambi', 'Wamona kebe', 'Marie-José', NULL, '08205551108', 'F', '1965-09-05', 'Kananga', 'Dekese,tshibanda banda', 'Veuf', NULL, 'Carte d\'électeur', 'Kananga', '2023-02-11', '34945574053', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, '32', 50000.00, NULL, NULL, '2026-03-13 18:42:07', '2026-03-13 18:42:07'),
('CL-EBENKGA-26-00078', 'ZON-EBENKGA-26-00001', 'Elongo', 'Dihomo', 'Sabina', NULL, '0990022535', 'F', '1990-08-15', 'Enunda', 'Tshinsambi', 'Marié', 'Albert ohito', 'Carte d\'électeur', 'Mukenge', '2023-03-04', '34923581307', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, '2', 750000.00, NULL, NULL, '2026-03-13 18:48:48', '2026-03-13 18:48:48'),
('CL-EBENKGA-26-00079', 'ZON-EBENKGA-26-00001', 'Tshibola', 'Mulumba', 'Agnes', NULL, '0980483238', 'F', '1982-10-30', 'Likasi', 'Du marché, plateau, kananga', 'Marié', 'Pomo kebela', 'Carte d\'électeur', 'Kananga', '2023-02-25', '34927380967', NULL, 'Vente des maïs', 'Commerce', NULL, NULL, NULL, NULL, '25', 500000.00, NULL, NULL, '2026-03-13 18:59:06', '2026-03-13 18:59:06'),
('CL-EBENKGA-26-00080', 'ZON-EBENKGA-26-00001', 'Kapinga', 'Biduaya', 'Therese', NULL, '0970439426', 'F', '1887-05-20', 'Tshikapa', 'Bandundu', 'Marié', 'Tshitolo christian', 'Carte d\'électeur', 'Kananga', '2023-03-15', '34927781644', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, '6', 125000.00, NULL, NULL, '2026-03-13 19:06:34', '2026-03-13 19:06:34'),
('CL-EBENKGA-26-00081', 'ZON-EBENKGA-26-00001', 'Mbombo', 'Kadianda', 'Charlotte', NULL, '0975054819', 'F', '1986-03-01', 'Kananga', 'Du  manguiers', 'Marié', 'Luashi  michel', 'Carte d\'électeur', 'Kananga', '2023-10-03', '349209888698', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, '3', 200000.00, NULL, NULL, '2026-03-13 19:11:08', '2026-03-13 19:11:08'),
('CL-EBENKGA-26-00082', 'ZON-EBENKGA-26-00001', 'Manya', 'Yema', 'Evariste', NULL, '0993655088', 'M', '1980-10-10', 'Kinshasa', 'Tshisekedi', 'Célibataire', NULL, 'Autre', 'Kinshasa', '1860-03-04', '000man100680', NULL, 'Boutique d\'habillement', 'Commerce', NULL, NULL, NULL, NULL, '9', 200000.00, NULL, NULL, '2026-03-13 19:15:59', '2026-03-13 19:15:59'),
('CL-EBENKGA-26-00083', 'ZON-EBENKGA-26-00004', 'TSHIBOLA', 'TSHILUMBA', 'MADELINE', NULL, '0993794896', 'F', '1959-11-24', 'KANANGA', 'N° 13 Av Kankonda, Q/ Kamayi, C/ Kananga', 'Marié', 'LUBOYA MARTIN', 'Carte d\'électeur', 'KANANGA', '2023-01-29', '34922168358', 'clients/1773483229_1000015908.jpeg', 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 09:13:49', '2026-03-14 09:13:49'),
('CL-EBENKGA-26-00084', 'ZON-EBENKGA-26-00003', 'MUTOMBO', 'MULUMBA', 'PRINCE', NULL, '0994695164', 'M', '1980-06-29', 'Kananga', 'Av Kinshasa,Q/ kelekele, C/ katoka n° 45', 'Marié', 'Ngalula Véronique', 'Carte nationale d\'identité', 'Kananga', '2023-02-18', '34941983905', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '16', NULL, NULL, NULL, '2026-03-14 11:47:36', '2026-03-14 11:47:36'),
('CL-EBENKGA-26-00085', 'ZON-EBENKGA-26-00003', 'NTUMBA', 'MPINDA', 'ROSE', NULL, NULL, 'F', '1992-08-15', 'Kananga', 'Av luiza Q/tshibandabanda C/ndesha n° 56', 'Marié', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-01-31', '34942769258', NULL, 'Vendeuse de savons', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 11:56:00', '2026-03-14 11:56:00'),
('CL-EBENKGA-26-00086', 'ZON-EBENKGA-26-00003', 'BIYA', 'MUKUNA', 'BIJOUX', NULL, '0980316830', 'F', '1977-01-10', 'Kananga', 'Av Basonga Q/ ndesha , c/ ndesha n° 11', 'Marié', 'Francis', 'Carte nationale d\'identité', 'Kananga', '2023-02-22', '34945581771', NULL, 'Vendeuse de nattes', NULL, NULL, NULL, NULL, NULL, '30', 100000.00, NULL, NULL, '2026-03-14 12:09:40', '2026-03-14 12:09:40'),
('CL-EBENKGA-26-00087', 'ZON-EBENKGA-26-00003', 'MUSOKOKAYI', 'KANSHAMa', 'ALBERT', NULL, '0995598853', 'M', '1991-07-17', 'Tshikapa', 'Av de la syntheté', 'Marié', 'Kabatusuila claudine', 'Carte nationale d\'identité', 'Kananga', '2023-02-03', '34947972799', NULL, 'Vendeur de vélos', NULL, NULL, NULL, NULL, NULL, '20', NULL, NULL, NULL, '2026-03-14 12:21:43', '2026-03-14 12:21:43'),
('CL-EBENKGA-26-00088', 'ZON-EBENKGA-26-00002', 'BADIBANGA', 'KANKU', 'LOUIS', NULL, '0998620746', 'M', '1998-10-27', 'KANANGA', 'AV.PLATEAU;Q/ MALANDJI,C/KANANGA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-11', '34952171435', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 13:56:44', '2026-03-14 13:56:44'),
('CL-EBENKGA-26-00089', 'ZON-EBENKGA-26-00002', 'NTUMBA', 'MPOSHI', 'ELYSE', NULL, '0997201259', 'F', '1983-10-11', 'KANANGA', 'AV.TSHISEKEDI;N01;Q/NDESHA;C/NDESHA', 'Marié', 'IDRIS KABENGA', 'Carte d\'électeur', 'KANANGA', '2023-03-23', '34945580915', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '9', 1000000.00, NULL, NULL, '2026-03-14 14:18:18', '2026-03-14 14:18:18'),
('CL-EBENKGA-26-00090', 'ZON-EBENKGA-26-00002', 'DITEKEMENA', 'NGALAMULUME', 'ESPERANCE', NULL, '0971023172AV.', 'M', '1995-07-17', 'TSHIDIMBA', 'AV.KASAVUBU;Q/KELEKELE;Q/KATOKA', 'Marié', 'OLIVIER KADANGA', 'Carte d\'électeur', 'KANANGA', '2023-03-28', '34938985208', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '8', 500000.00, NULL, NULL, '2026-03-14 14:26:39', '2026-03-14 14:26:39'),
('CL-EBENKGA-26-00091', 'ZON-EBENKGA-26-00004', 'NGALULA', 'KALONJI', 'VICTOR', NULL, '0994824183', 'M', '1980-08-10', 'ILEBO', 'N° 10 Av decadet Q/ Kamayi C/ Kananga', 'Marié', 'TSHILOMBA MARIE', 'Carte d\'électeur', 'KANANGA', '2023-02-08', '34921973388', 'clients/1773502711_1000015919.jpeg', 'Commerce', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 14:38:31', '2026-03-14 14:38:31'),
('CL-EBENKGA-26-00092', 'ZON-EBENKGA-26-00002', 'YAMBA', 'YAMBA', 'FABIEN', NULL, '099861815', 'M', '1970-05-20', 'KANANGA', 'AV.LULUA,Q/TSHISALBI,C/KANANGA', 'Marié', 'NGINDU RACHEL', 'Carte d\'électeur', 'KANANGA', '2023-03-17', '34928384569', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 14:39:23', '2026-03-14 14:39:23'),
('CL-EBENKGA-26-00093', 'ZON-EBENKGA-26-00001', 'Muanza', 'Mpiana', 'Yakuba', NULL, '0975344968', 'M', '1986-04-24', 'Kananga', 'Route ilebo,lukonga', 'Marié', 'Panu passy', 'Carte d\'électeur', 'Kananga', '2023-08-07', '34938188866', NULL, 'Quinquelerie', 'Commerce', NULL, NULL, NULL, NULL, '7', 1000000.00, NULL, NULL, '2026-03-14 14:44:01', '2026-03-14 14:44:01'),
('CL-EBENKGA-26-00094', 'ZON-EBENKGA-26-00004', 'NDAYE', 'MUBIAYI', 'TANTINE', NULL, '0812547638', 'F', '1986-04-12', 'KANANGA', 'N°36 Av. Boma Q/ Kamayi, C/ Kananga', 'Marié', 'LUBOYA MARTIN', 'Carte d\'électeur', 'KANANGA', '2023-02-28', '31930978155', 'clients/1773503050_1000015923.jpeg', 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 14:44:10', '2026-03-14 14:44:10'),
('CL-EBENKGA-26-00095', 'ZON-EBENKGA-26-00002', 'KALUBI', 'MUSANKISHAYI', 'CHARLOTTE', NULL, '0971194444', 'F', '1988-09-10', 'KANANGA', 'AV.KASAVUBU,Q/MALANDJI,C/KANANGA', 'Marié', 'LUMINGU MILAMBO LEON', 'Carte d\'électeur', 'DEMBA', '2023-02-12', '35147184237', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, 20.00, NULL, NULL, '2026-03-14 14:47:26', '2026-03-14 14:47:26'),
('CL-EBENKGA-26-00096', 'ZON-EBENKGA-26-00004', 'KUTIYAYA', 'BELALUFU', 'ALPHONSINE', NULL, '0995245673', 'F', '1985-04-25', 'KANANGA', 'N°57 Av Du Manguier Q/ Kamayi C/ Kananga', 'Marié', 'Lukengu Jean', 'Carte d\'électeur', 'KANANGA', '2023-02-19', '34920980158', 'clients/1773503330_1000015931.jpeg', 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 14:48:51', '2026-03-14 14:48:51'),
('CL-EBENKGA-26-00097', 'ZON-EBENKGA-26-00001', 'Tshibuabua', 'Budimbu', 'Jean Paul', NULL, '0991699621', 'M', '1990-10-10', 'Kananga', 'Nkongolo, Tshinsambi, kananga', 'Marié', 'Kabedi  Carrine', 'Carte d\'électeur', 'Kananga', '2023-02-07', '34922970097', NULL, 'Vente des ciment', 'Commerce', NULL, NULL, NULL, NULL, '10', 500000.00, NULL, NULL, '2026-03-14 14:49:04', '2026-03-14 14:49:04'),
('CL-EBENKGA-26-00098', 'ZON-EBENKGA-26-00001', 'Mputu', 'Mputu', 'Simon', NULL, '0970465629', 'M', '1970-10-14', 'Kananga', 'Banzangungu, kele kele, katoka', 'Veuf', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-14', '34934575956', NULL, 'Peintre', 'Commerce', NULL, NULL, NULL, NULL, '12', 800000.00, NULL, NULL, '2026-03-14 14:55:31', '2026-03-14 14:55:31'),
('CL-EBENKGA-26-00099', 'ZON-EBENKGA-26-00002', 'MUANDE', 'KALONDA', 'AGNES', NULL, '0977090096', 'F', '1982-01-02', 'KANANGA', 'AV.DU COMMERCE,Q/TSHISAMBI,N37,C/KANANGA', 'Marié', 'NTUMBA', 'Carte d\'électeur', 'KANANGA', '2023-04-12', '30288936532', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '18', NULL, NULL, NULL, '2026-03-14 14:55:41', '2026-03-14 14:55:41'),
('CL-EBENKGA-26-00100', 'ZON-EBENKGA-26-00001', 'Kalala', 'Ngondo', 'Willy', NULL, '0998878982', 'M', '2082-03-09', 'Kananga', 'De la mission,Malandji, Kananga', 'Veuf', NULL, 'Carte d\'électeur', 'Kinshasa', '2023-03-14', '3491383523', NULL, 'Restaurant', 'Service', NULL, NULL, NULL, NULL, '15', 1500000.00, NULL, NULL, '2026-03-14 15:01:10', '2026-03-14 15:01:10'),
('CL-EBENKGA-26-00101', 'ZON-EBENKGA-26-00001', 'Mushiya', 'Kanku', 'Marceline', NULL, '0993003442', 'F', '1933-09-17', 'Tshikula', 'Du Sapin,Malandji, kananga', 'Marié', 'Ngalamulume Marcel', 'Carte d\'électeur', 'Kananga', '2023-02-15', '34941982021', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, '9', 100000.00, NULL, NULL, '2026-03-14 15:06:15', '2026-03-14 15:06:15'),
('CL-EBENKGA-26-00102', 'ZON-EBENKGA-26-00002', 'MPUTU', 'BOKIMO', 'ELODUE', NULL, '0984181407', 'F', '1997-04-01', 'TSHIKAPA', 'AV.KANYOKA,N26 Q/AZADA, C/KANANGA', 'Marié', 'SHAMBA SAMUEL', 'Carte d\'électeur', 'KANANGA', '2023-01-31', '34952769657', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 15:09:30', '2026-03-14 15:09:30'),
('CL-EBENKGA-26-00103', 'ZON-EBENKGA-26-00001', 'Tshibola', 'Manyayi', 'Mado', NULL, '0975891134', 'F', '1992-06-24', 'Kananga', 'Bukole,Ndesha,Ndesha', 'Marié', 'Albert  Ntambue', 'Carte d\'électeur', 'Kananga', '2023-02-14', '34538576682', NULL, 'Boutique d\'habillement', 'Commerce', NULL, NULL, NULL, NULL, '18', 1200000.00, NULL, NULL, '2026-03-14 15:12:03', '2026-03-14 15:12:03'),
('CL-EBENKGA-26-00104', 'ZON-EBENKGA-26-00001', 'Ngolela', 'Kadinga', 'Aimerance', NULL, '0972018418', 'F', '1977-08-26', 'Kananga', 'Tshiamua, katoka, katoka', 'Marié', 'Kayembe, Georges', 'Carte d\'électeur', 'Kananga', '2023-02-05', '34933370984', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, '4', 500000.00, NULL, NULL, '2026-03-14 15:16:08', '2026-03-14 15:16:08'),
('CL-EBENKGA-26-00105', 'ZON-EBENKGA-26-00002', 'MBELU', 'MULUMBA', 'CHARLY', NULL, '0976960161', 'F', '1980-03-19', 'KANANGA', 'AV.KANYOKA, N134, Q/TSHISAMBI, C/KANANGA', 'Marié', 'PAUL TSHIBUABUA', 'Carte d\'électeur', 'KANANGA', '2023-02-14', '34918370662', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '14', 3000000.00, NULL, NULL, '2026-03-14 15:16:25', '2026-03-14 15:16:25'),
('CL-EBENKGA-26-00106', 'ZON-EBENKGA-26-00001', 'Esambo', 'Akatshi', 'Pauline', NULL, '0973300345', 'F', '1974-07-15', 'Lodja', 'Banzangungu, kele kele, katoka', 'Divorcé', NULL, 'Carte d\'électeur', 'Lodja', '2023-02-03', '34938371247', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, '5', 500000.00, NULL, NULL, '2026-03-14 15:20:27', '2026-03-14 15:20:27'),
('CL-EBENKGA-26-00107', 'ZON-EBENKGA-26-00002', 'NDAYA', 'MPOSHI', 'JEANINE', NULL, '0977433057', 'F', '1986-06-18', 'KANANGA', 'AV LULUA Q/NKENGEKELE C/KANANGA', 'Marié', 'MPOSHI MARTIN', 'Carte d\'électeur', 'KANANGA', '2023-02-25', '34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '14', 3000000.00, NULL, NULL, '2026-03-14 15:28:22', '2026-03-14 15:28:22'),
('CL-EBENKGA-26-00108', 'ZON-EBENKGA-26-00002', 'NDAYA', 'MPOSHI', 'JEANINE', NULL, '0977433057', 'F', '1986-06-18', 'KANANGA', 'AV.LULUA, Q/ KELEKELE, C/ KANANGA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-03-25', '34945577768', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '12', 3000000.00, NULL, NULL, '2026-03-14 17:25:38', '2026-03-14 17:25:38'),
('CL-EBENKGA-26-00109', 'ZON-EBENKGA-26-00002', 'KALANGA', 'KANUMUMANYI', 'MARIE', NULL, '0971098708', 'F', '2003-12-12', 'TSHIKAPA', 'AV.KABALU, N03, Q/N\'sele, C/ NGANZA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-03-01', '34948987085', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 500000.00, NULL, NULL, '2026-03-14 17:44:48', '2026-03-14 17:44:48'),
('CL-EBENKGA-26-00110', 'ZON-EBENKGA-26-00002', 'NGALULA', 'KAZADI', 'ANASTASIE', NULL, '0973335834', 'F', '1987-04-02', 'MBUJIMAYI', 'AV.DE CADET, Q/ MALANDJI, C/KANANGA', 'Marié', 'PIERRE MULUMBA', 'Carte d\'électeur', 'KANANGA', '2023-12-27', '34928568527', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '18', 300000.00, NULL, NULL, '2026-03-14 17:52:55', '2026-03-14 17:52:55'),
('CL-EBENKGA-26-00111', 'ZON-EBENKGA-26-00002', 'BETU', 'TSHIBAMGU', 'BERTIN', NULL, '0994157279', 'M', '1987-12-10', 'KANANGA', 'AV.MUENEDITU, N23, Q/KAMILABI, C/ NDESHA', 'Marié', 'MBOMBO HENRIETTE', 'Carte d\'électeur', 'KANANGA', '2023-02-28', '34946384807', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '18', NULL, NULL, NULL, '2026-03-14 18:08:54', '2026-03-14 18:08:54'),
('CL-EBENKGA-26-00112', 'ZON-EBENKGA-26-00002', 'EMBEYA', 'TETE', 'JEAN', NULL, '0997324480', 'M', '1980-12-12', 'TSHIANDA', 'AV.LULUA,Q/ MLALANDJI, C/KANANGA', 'Marié', 'NGALULA GRACE', 'Carte d\'électeur', 'KANANGA', '2023-06-09', '34927768201', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '20', NULL, NULL, NULL, '2026-03-14 18:18:29', '2026-03-14 18:18:29'),
('CL-EBENKGA-26-00113', 'ZON-EBENKGA-26-00002', 'DIKANDA', 'KABINDA', 'ERICK', NULL, '0831550478', 'M', '1999-04-04', 'KANANGA', 'AV.KALUBO, N04, Q/KAMAYI, C/KANANGA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-25', '34952580762', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 18:24:14', '2026-03-14 18:24:14'),
('CL-EBENKGA-26-00114', 'ZON-EBENKGA-26-00002', 'KANGODIA', 'MUKENGE', 'ANDRE', NULL, '0998340935', 'M', '1999-09-08', 'TSHINGANA', 'AV.DU CANAL, Q/MALANDJI, C/ KANANGA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-12-27', '34922967493', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '11', NULL, NULL, NULL, '2026-03-14 18:29:11', '2026-03-14 18:29:11'),
('CL-EBENKGA-26-00115', 'ZON-EBENKGA-26-00002', 'MBUYI', 'MUBENGAYI', 'ANASTASIE', NULL, '0986004522', 'F', '1993-05-05', 'KANANGA', 'AV.TSHIAMUA, N52, Q/PLATEAU, C/ KANANGA', 'Marié', 'NGALAMULUME ALAIN', 'Carte d\'électeur', 'KANANGA', '2023-02-07', '349227970743', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '20', NULL, NULL, NULL, '2026-03-14 18:40:58', '2026-03-14 18:40:58'),
('CL-EBENKGA-26-00116', 'ZON-EBENKGA-26-00001', 'NYEMBA', 'KAKONKA', 'Marie', NULL, '0982787831', 'F', '1986-04-15', 'Kananga', '45 Av Basonga , q/Tshibandabanda C/ ndesha', 'Marié', 'Théodore MUAMBA FILS', 'Carte d\'électeur', 'Kananga', '2023-02-10', '458788', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 18:56:12', '2026-03-14 18:56:12'),
('CL-EBENKGA-26-00117', 'ZON-EBENKGA-26-00001', 'Tshikondo', 'Tshikondo', 'Emery', NULL, '0974589684', 'M', '2005-03-03', 'Kananga', 'Nzoba,KAMAYI, kananga', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-04', '3495596444', NULL, 'Vente des unités', 'Service', NULL, NULL, NULL, NULL, '2', 300000.00, NULL, NULL, '2026-03-14 19:05:31', '2026-03-14 19:05:31'),
('CL-EBENKGA-26-00118', 'ZON-EBENKGA-26-00001', 'Kempe', 'Nsumbula', 'Jeannette', NULL, '0994198764', 'F', '1979-05-10', 'Kananga', 'Mela 8, mpokolo,katoka', 'Marié', 'Jonathan', 'Carte d\'électeur', 'Kananga', '2023-03-16', '25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '12', 10000000.00, NULL, NULL, '2026-03-14 19:14:07', '2026-03-14 19:14:07'),
('CL-EBENKGA-26-00119', 'ZON-EBENKGA-26-00001', 'Kabedi', 'Nkole', 'Pauline', NULL, '0901401873', 'F', '1984-10-09', 'Kananga', 'Kalota,5, Mpokolo, katoka', 'Marié', 'Honoré kamba', 'Carte d\'électeur', 'Kananga', '2023-03-11', '21', NULL, 'Boutique d\'habillement', 'Commerce', NULL, NULL, NULL, NULL, '8', 5000000.00, NULL, NULL, '2026-03-14 19:18:18', '2026-03-14 19:18:18'),
('CL-EBENKGA-26-00120', 'ZON-EBENKGA-26-00001', 'Muyaya', 'Tshimbawu', 'John sky', NULL, '0990021738', 'M', '1993-11-29', 'Kananga', 'Boma, Nsele nganza', 'Marié', 'Naomie ngalula', 'Autre', 'Kananga', '2025-03-09', '20', NULL, 'Radio', 'Service', NULL, NULL, NULL, NULL, '18', 10000000.00, NULL, NULL, '2026-03-14 19:23:07', '2026-03-14 19:23:07'),
('CL-EBENKGA-26-00121', 'ZON-EBENKGA-26-00001', 'Bupele', 'Ntumba', 'Jilly', NULL, '0987364568', 'F', '2000-02-15', 'Kananga', 'Mande, snel, kananga', 'Divorcé', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-14', '15', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, '11', 500000.00, NULL, NULL, '2026-03-14 19:26:18', '2026-03-14 19:26:18'),
('CL-EBENKGA-26-00122', 'ZON-EBENKGA-26-00001', 'Kapongo', 'Pasua nzambi', 'Pascal', NULL, '0989835248', 'M', '1991-09-08', 'Kananga', 'Kasavubu , Malandji, kananga', 'Veuf', NULL, 'Carte d\'électeur', 'Kananga', '2023-02-26', '34930979639', NULL, 'Imprimerie', 'Service', NULL, NULL, NULL, NULL, '6', 8000000.00, NULL, NULL, '2026-03-14 19:31:36', '2026-03-14 19:31:36'),
('CL-EBENKGA-26-00123', 'ZON-EBENKGA-26-00009', 'Client Démon', 'test', 'Test', 'test@gmail.com', '+24399999555', 'M', '2026-02-24', 'Kananga', 'Nganza', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2026-03-07', '658495655', 'clients/1773570894_ESP32-CAM-Connexion USB.png', 'Enseignement', 'Commerce', 'Nexus BMB Techn', 'Kamayi', '+2453333', 'Privé', '1', 500.00, NULL, NULL, '2026-03-15 09:34:54', '2026-03-15 09:34:54'),
('CL-EBENKGA-26-00124', 'ZON-EBENKGA-26-00002', 'MIKONGO', 'SAMUAMBA', 'HABAQOUQ', NULL, '0973315412', 'M', '1995-08-01', 'NSELE', 'AV.DU RAIL, Q/KELE-KELE, C/KATOKA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-05', '34934571496', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-15 10:02:40', '2026-03-15 10:02:40'),
('CL-EBENKGA-26-00125', 'ZON-EBENKGA-26-00002', 'BAMUE', 'NDAYE', 'ANACLET', NULL, '0991626478', 'M', '2001-04-01', 'KANANGA', 'AV.DIBELENGE, Q/LUBUWA, C/NDESHA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-09', '34929173442', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 10:06:47', '2026-03-15 10:06:47'),
('CL-EBENKGA-26-00126', 'ZON-EBENKGA-26-00002', 'MUENYI', 'NGALAMULUME', 'ELYSE', NULL, '0972136908', 'F', '1981-07-25', 'DEMBA', 'AV.MUANZA, Q/TSHISAMBI, C/ KANANGA', 'Marié', 'POLO OTEPA', 'Carte d\'électeur', 'KANANGA', '2023-11-26', '34937003386', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 10:15:50', '2026-03-15 10:15:50'),
('CL-EBENKGA-26-00127', 'ZON-EBENKGA-26-00002', 'BOLIA', 'BOYIPIA', 'SILVIE', NULL, '0976802833', 'F', '1979-11-17', 'KINSHASA', 'AV.LUIZA, Q/LUBUWA, C/ NDESHA', 'Marié', 'FRANCOIS  OTETE', 'Carte d\'électeur', 'KANANGA', '2023-12-10', '34922781262', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 10:49:19', '2026-03-15 10:49:19'),
('CL-EBENKGA-26-00128', 'ZON-EBENKGA-26-00002', 'MASANKA', 'MUAMBA', 'CLARISSE', NULL, '0997306624', 'F', '1990-07-25', 'KANANGA', 'AV.DU KAPO,N05, Q/MALANDJI, C/KANANGA', 'Marié', 'BADIBANGA BADI', 'Carte d\'électeur', 'KANANGA', '2023-03-03', '34930970175', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 10:56:44', '2026-03-15 10:56:44');
INSERT INTO `tb_clients` (`matricule`, `code_zone`, `nom`, `postnom`, `prenom`, `email`, `telephone`, `sexe`, `date_naissance`, `lieu_naissance`, `adresse`, `etat_civil`, `nom_conjoint`, `type_piece_identite`, `lieu_delivrance_piece`, `date_delivrance_piece`, `numero_piece_identite`, `photo`, `secteur_activite`, `type_activite`, `nom_entreprise`, `adresse_entreprise`, `telephone_entreprise`, `statut_entreprise`, `nombre_annees_experience`, `revenu_mensuel`, `revenu_mensuel_devise`, `autres_details_activite`, `created_at`, `updated_at`) VALUES
('CL-EBENKGA-26-00129', 'ZON-EBENKGA-26-00002', 'KANKU', 'TSHIFUAKA', 'HELENE', NULL, '0992950083', 'F', '1984-01-08', 'KANANGA', 'AV.BOMA, N82, Q/ KELE-KELE', 'Marié', 'PATRICK KALENDA', 'Carte d\'électeur', 'KANANHA', '2023-02-06', '34929789215', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 11:02:33', '2026-03-15 11:02:33'),
('CL-EBENKGA-26-00130', 'ZON-EBENKGA-26-00002', 'TSHISALU', 'BUABUA', 'THERESE', NULL, '0997946177', 'F', '1978-07-15', 'KANANGA', 'AV.DU PRODUIT, Q/ KELE-KELE', 'Marié', 'TSHINKUNKU ROGER', 'Carte d\'électeur', 'KANANHA', '2023-02-25', '34973983206', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 11:13:51', '2026-03-15 11:13:51'),
('CL-EBENKGA-26-00131', 'ZON-EBENKGA-26-00002', 'SAMALOMBA', 'ALOMBA', 'VERONIQUE', NULL, '09986549780', 'F', '2023-02-12', 'LODJA', 'AV, DIOKO PUNDA,N34, Q/ KELE-KELE, C/KANANGA', 'Marié', 'DIKA GEROME', 'Carte d\'électeur', 'KANANHA', '2023-01-09', '34930973124', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 11:20:35', '2026-03-15 11:20:35'),
('CL-EBENKGA-26-00132', 'ZON-EBENKGA-26-00002', 'NGONGO', 'UNGANDOMBE', 'PATRICE', NULL, '0991683371', 'M', '1988-12-12', 'KONGAMBOLO', 'AV. PANDA, N04, Q/POKOLO, C/ KATOKA', 'Marié', 'ELOKE SOUZANE', 'Carte d\'électeur', 'KANANGA', '2023-03-20', '34929796006', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 11:37:35', '2026-03-15 11:37:35'),
('CL-EBENKGA-26-00133', 'ZON-EBENKGA-26-00002', 'TSHITUKA', 'MAYELE', 'ALPHONSINE', NULL, '0975040912', 'F', '1974-12-23', 'KANANGA', 'AV. BUSHALABUAMBA, N155, Q/ MALANDJI,  C/ KANANGA', 'Marié', 'FRANCOIS MUTSHIPAYI', 'Carte d\'électeur', 'KANANGA', '2023-02-18', '34938379342', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 11:43:09', '2026-03-15 11:43:09'),
('CL-EBENKGA-26-00134', 'ZON-EBENKGA-26-00002', 'NTAMBUE', 'MATALA', 'MATHIEU', NULL, '0994900621', 'M', '1982-05-05', 'MBULUNGU', 'AV. TSHITENGE,N03, Q/ MALANDJI, C/ KANANGA', 'Marié', 'SHIYAYI ANNA', 'Carte d\'électeur', 'KANANGA', '2023-02-28', '34919381976', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 11:52:15', '2026-03-15 11:52:15'),
('CL-EBENKGA-26-00135', 'ZON-EBENKGA-26-00002', 'AMBASEKA', 'TSHIBINDI', 'VERONIQUE', NULL, '0971907549', 'F', '1995-09-29', 'MBUJI MAYI', 'AV,ROUTE ILEBO, N56, Q/ TSHISAMI, C/ NDESHA', 'Marié', 'MBUYI JEAN PAUL', 'Carte d\'électeur', 'KANANGA', '2023-02-02', '35', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '8', NULL, NULL, NULL, '2026-03-15 12:43:51', '2026-03-15 12:43:51'),
('CL-EBENKGA-26-00136', 'ZON-EBENKGA-26-00002', 'HODI', 'TASSA', 'ROSE', NULL, '09991307', 'F', '1980-12-25', 'WEMBONYAMA', 'AV. KINSHASA, N45,Q/ KELE-KELE, C/ KATOKA', 'Marié', 'ANTOINE ONEMA', 'Carte d\'électeur', 'KANANGA', '2023-01-01', '33', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-15 12:47:40', '2026-03-15 12:47:40'),
('CL-EBENKGA-26-00137', 'ZON-EBENKGA-26-00002', 'NJIMBA', 'KANSHAMA', 'ALPHO', NULL, '0972746766', 'F', '1998-02-19', 'KANANGA', 'AV. LUSE, N12, Q/ TSHISAMBI, C/ KANANGA', 'Marié', 'FREDDY KASHAMA', 'Carte d\'électeur', 'KANANGA', '2023-01-01', '36', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-15 12:50:55', '2026-03-15 12:50:55'),
('CL-EBENKGA-26-00138', 'ZON-EBENKGA-26-00002', 'MPUTU', 'TSHINKUNKU', 'JACQUIS', NULL, '0982673861', 'F', '2000-01-01', 'KANANGA', 'AV. PRODUIT, Q/ KELE-KELE, C/ KATOKA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-03', '31', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '2', NULL, NULL, NULL, '2026-03-15 12:54:03', '2026-03-15 12:54:03'),
('CL-EBENKGA-26-00139', 'ZON-EBENKGA-26-00002', 'BUKUMBA', 'MPUTU', 'MARIE', NULL, '08278566695', 'F', '1984-10-20', 'KANANGA', 'AV.MATADI, N24, Q/ POKOLO, C/KATOKA', 'Marié', 'PANU AUGUSTIN', 'Carte d\'électeur', 'KANANGA', '2023-02-03', '37', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '6', NULL, NULL, NULL, '2026-03-15 12:58:58', '2026-03-15 12:58:58'),
('CL-EBENKGA-26-00140', 'ZON-EBENKGA-26-00002', 'MBELU', 'KANKU', 'ANTHO', NULL, '0977525039', 'F', '1980-12-05', 'KANANGA', 'AV. BUTOKE, N85, Q/MALANDJI, C/ KGA', 'Marié', 'TSHITENGE MEDAR', 'Carte d\'électeur', 'KANANGA', '2023-02-03', '38', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-15 13:02:44', '2026-03-15 13:02:44'),
('CL-EBENKGA-26-00141', 'ZON-EBENKGA-26-00002', 'KAPINGA', 'KAZADI', 'AIMERANCE', NULL, '0982961671', 'F', '1988-12-30', 'ILEBO', 'AV. BAZANGULU, Q/ KELE-KELE, C/ KATOKA', 'Marié', 'MUBENGAYI PATRICE', 'Carte d\'électeur', 'KANANGA', '2023-02-03', '39', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '8', NULL, NULL, NULL, '2026-03-15 13:05:53', '2026-03-15 13:05:53'),
('CL-EBENKGA-26-00142', 'ZON-EBENKGA-26-00002', 'TSHIMANGA', 'TSHIMANGA', 'AUGUSTIN', NULL, '0970984647', 'M', '1982-10-10', 'MIKALAYI', 'AV. DISPENAIRE, N30, Q/ TSHISAMBI, C/ KANANGA', 'Marié', 'KANKU RACHEL', 'Carte d\'électeur', 'KANANGA', '2023-04-03', '30', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '17', NULL, NULL, NULL, '2026-03-15 13:15:55', '2026-03-15 13:15:55'),
('CL-EBENKGA-26-00143', 'ZON-EBENKGA-26-00002', 'NGINDU', 'NGINDU', 'JHON', NULL, NULL, 'M', '2026-03-15', 'MIKALAYI', 'AV. DU CANAL, Q/ MALANDJI, C/ KANANGA', 'Marié', 'TSHIBOLA CLEMENCE', 'Carte d\'électeur', 'KANANGA', '2023-02-02', '349', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '17', NULL, NULL, NULL, '2026-03-15 13:22:28', '2026-03-15 13:22:28'),
('CL-EBENKGA-26-00144', 'ZON-EBENKGA-26-00002', 'TSHIOWA', 'NKOLE', 'CHRISTINE', NULL, '0991025924', 'F', '1987-02-02', 'KABONDO', 'AV. KALOTA, N18, Q/ MPOKOLO, C/ KATOKA', 'Marié', 'KALEBE MICHEL', 'Carte d\'électeur', 'KANANGA', '2023-02-02', '345', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '11', NULL, NULL, NULL, '2026-03-15 13:26:38', '2026-03-15 13:26:38'),
('CL-EBENKGA-26-00145', 'ZON-EBENKGA-26-00002', 'MULUMBA', 'MUKUMBA', 'NUUR', NULL, '0907576520', 'M', '1996-12-29', 'KANANGA', 'AV. KANYUKA, Q/ TSHISAMBI, C/ KGA', 'Veuf', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-02', '321', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '3', NULL, NULL, NULL, '2026-03-15 13:29:31', '2026-03-15 13:29:31'),
('CL-EBENKGA-26-00146', 'ZON-EBENKGA-26-00002', 'NYAOMUNA', 'ONDIYO', 'REBECCA', NULL, '0902297872', 'F', '2004-04-26', 'KANANGA', 'AV. KALENGA, N9, Q/ KAMULUMBA, C/ KANANGA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-02', '343', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '3', NULL, NULL, NULL, '2026-03-15 13:34:27', '2026-03-15 13:34:27'),
('CL-EBENKGA-26-00147', 'ZON-EBENKGA-26-00002', 'NTAMBUE', 'MPUTU', 'REBECCA', NULL, '0988614858', 'F', '2000-06-06', 'KANANGA', 'AV. TSHIMBULU, N11, Q/ TSHIBANDABANDA, C/ NDESHA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-02', '332', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '2026-03-15 13:38:44', '2026-03-15 13:38:44'),
('CL-EBENKGA-26-00148', 'ZON-EBENKGA-26-00002', 'MBOMBO', 'KABASELE', 'CHRISTINE', NULL, '0975109295', 'F', '1985-01-08', 'MBUJI MAYI', 'AV. MUPOYI, Q/  MALANDJI, C/ KANANGA', 'Marié', 'MBUYAMBA MICHEL', 'Carte d\'électeur', 'KANANGA', '2023-02-02', '314', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-15 13:42:09', '2026-03-15 13:42:09'),
('CL-EBENKGA-26-00149', 'ZON-EBENKGA-26-00002', 'MUKENDA', 'BANGUNDA', 'FIDEL', NULL, NULL, 'F', '2006-08-11', 'KANANGA', 'AV.LIKASI, N03, Q/ NDESHA, C/ KANANGA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-02', '337', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '2', NULL, NULL, NULL, '2026-03-15 13:54:19', '2026-03-15 13:54:19'),
('CL-EBENKGA-26-00150', 'ZON-EBENKGA-26-00002', 'TSHILA', 'OTETE', 'DORINE', NULL, '0974904382', 'F', '2002-07-22', 'KANANGA', 'AV. LUIZA, N47, Q/ TSHIBANDABANDA, C/ NDESHA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-02', '373', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '2', NULL, NULL, NULL, '2026-03-15 13:59:11', '2026-03-15 13:59:11'),
('CL-EBENKGA-26-00151', 'ZON-EBENKGA-26-00002', 'NGALULA', 'NJIMBA', 'HENRIETTE', NULL, '0975852525', 'F', '1989-01-01', 'KAZUMBA', 'AV. KASAVUBU, N39, Q/MALANDJI, C/KGA', 'Marié', 'MUFUTA FREDDY', 'Carte d\'électeur', 'KANANGA', '2023-02-05', '431', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '10', NULL, NULL, NULL, '2026-03-15 14:04:54', '2026-03-15 14:04:54'),
('CL-EBENKGA-26-00152', 'ZON-EBENKGA-26-00002', 'KAMUANGA', 'NKASHALA', 'CLEMENT', NULL, '0996579954', 'M', '1960-06-30', 'KANANGA', 'AV.DU CANAL , N46, Q/MALANDJI, C/ KGA', 'Marié', 'MBUYI CATERINE', 'Carte d\'électeur', 'KANANGA', '2023-02-15', '3492538', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '20', NULL, NULL, NULL, '2026-03-15 14:08:23', '2026-03-15 14:08:23'),
('CL-EBENKGA-26-00153', 'ZON-EBENKGA-26-00002', 'KOMBO', 'BASA', 'FABIEN', NULL, '0981062416', 'M', '1992-12-02', 'KANANGA', 'AV. KOLWEZI, N82, Q/ KELE-KELE, C/ KATOKA', 'Marié', 'KANKU HELENE', 'Carte d\'électeur', 'KANANGA', '2023-02-15', '349253', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '21', NULL, NULL, NULL, '2026-03-15 14:15:16', '2026-03-15 14:15:16'),
('CL-EBENKGA-26-00154', 'ZON-EBENKGA-26-00002', 'KAFUKA', 'MULUMBA', 'HELENE', NULL, '0824073450', 'F', '1986-10-17', 'KANANGA', 'AV. DU RAIL, N07, Q/ TSHISAMBI, C/ KGA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-15', '3333', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '21', NULL, NULL, NULL, '2026-03-15 14:33:42', '2026-03-15 14:33:42'),
('CL-EBENKGA-26-00155', 'ZON-EBENKGA-26-00002', 'BEYA', 'MUKENGE', 'YADOT', NULL, '0992068239', 'M', '1993-03-23', 'KANANGA', 'AV. TSHIBULU, N17, Q/ LUBUWA, C/ NDESHA', 'Marié', 'IVETE MUJINGA', 'Carte d\'électeur', 'KANANGA', '2023-02-22', '32222', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-15 14:36:59', '2026-03-15 14:36:59'),
('CL-EBENKGA-26-00156', 'ZON-EBENKGA-26-00002', 'KALONDA', 'KABASELE', 'MARIE', NULL, NULL, 'F', '1983-12-28', 'KANANGA', 'AV.TANTA, N11, Q/ KAMULUMBA, C/KANANGA', 'Marié', 'MBAYI ALEX', 'Carte d\'électeur', 'KANANGA', '2023-02-11', '3111', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '12', NULL, NULL, NULL, '2026-03-15 14:40:04', '2026-03-15 14:40:04'),
('CL-EBENKGA-26-00157', 'ZON-EBENKGA-26-00002', 'NYEMBA', 'TSHIMANGA', 'THERESE', NULL, '0831556103', 'F', '1993-04-28', 'KINSHASA', 'AV.DU COMMERCE, Q/ SNCC, C/ KANANGA', 'Marié', 'JEAN RENE', 'Carte d\'électeur', 'KANANGA', '2023-03-11', '3922', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '12', NULL, NULL, NULL, '2026-03-15 14:45:18', '2026-03-15 14:45:18'),
('CL-EBENKGA-26-00158', 'ZON-EBENKGA-26-00002', 'MBOMBO', 'MABUDI', 'DENISE', NULL, '0985812729', 'F', '1981-08-04', 'MUEKA', 'AV. BRASERIE, N04, Q/ INDUSTRIEL, C/ KGA', 'Marié', 'BALENGA BALEX', 'Carte d\'électeur', 'KANANGA', '2023-03-11', '3902', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '22', NULL, NULL, NULL, '2026-03-15 14:48:33', '2026-03-15 14:48:33'),
('CL-EBENKGA-26-00159', 'ZON-EBENKGA-26-00002', 'KABENGA', 'KABENGA', 'DORIS', NULL, '0972558976', 'M', '1993-08-07', 'BULUNGU', 'AV. DU CANAL, N19, Q/ TSHISAMBI, C/ KGA', 'Marié', 'TSHIBUABUA DENISE', 'Carte d\'électeur', 'KANANGA', '2023-03-11', '3434', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '6', NULL, NULL, NULL, '2026-03-15 14:51:56', '2026-03-15 14:51:56'),
('CL-EBENKGA-26-00160', 'ZON-EBENKGA-26-00002', 'BIKU', 'LUNGONZO', 'RODE', NULL, '0972558976', 'F', '1999-04-11', 'KANANGA', 'AV. TUBULUKU , N13, Q/ POKOLO, C/ KATOKA', 'Marié', 'PIERRE KABASELE', 'Carte d\'électeur', 'KANANGA', '2023-03-11', '3432', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-15 14:55:09', '2026-03-15 14:55:09'),
('CL-EBENKGA-26-00161', 'ZON-EBENKGA-26-00002', 'KATEMBUE', 'BAPELEDI', 'PATIENCE', NULL, '0853078998', 'F', '1993-01-14', 'KANANGA', 'AV. DIBELENGE, N61, Q/ LUBUWA, C/ NDESHA', 'Marié', 'PATRICK  KENGA', 'Carte d\'électeur', 'KANANGA', '2023-03-11', '232', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '2', NULL, NULL, NULL, '2026-03-15 14:58:42', '2026-03-15 14:58:42'),
('CL-EBENKGA-26-00162', 'ZON-EBENKGA-26-00002', 'MBUYI', 'LOBO', 'DORCAS', NULL, '0853078998', 'F', '2026-03-15', 'KANANGA', 'AV.DU CANAL', 'Marié', 'GAUTHIEUR PIEMA', 'Carte d\'électeur', 'KANANGA', '2023-03-11', '34110', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '2', NULL, NULL, NULL, '2026-03-15 15:24:29', '2026-03-15 15:24:29'),
('CL-EBENKGA-26-00163', 'ZON-EBENKGA-26-00001', 'Kalenga', 'Eko', 'Jen', NULL, '0973524624', 'M', '2007-02-12', 'Kananga', 'Lulua', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-15', '34933983806', NULL, 'Cabiniste', 'Commerce', NULL, NULL, NULL, NULL, '5', 500000.00, NULL, NULL, '2026-03-15 15:26:57', '2026-03-15 15:26:57'),
('CL-EBENKGA-26-00164', 'ZON-EBENKGA-26-00002', 'NGALULA', 'TSHIBOBA', 'ANNY', NULL, '0972326211', 'F', '2026-11-15', 'KANANGA', 'AV. DU MANGIER, N05, Q/ KAMAYI, C/KGA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-03-11', '34252787', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '8', NULL, NULL, NULL, '2026-03-15 15:30:09', '2026-03-15 15:30:09'),
('CL-EBENKGA-26-00165', 'ZON-EBENKGA-26-00001', 'Panu', 'Kabeya', 'Passy', NULL, '0972471388', 'F', '1999-03-21', 'Kananga', 'Route ilebo, Lubuwa, ndesha', 'Marié', 'Yakuba Muanza', 'Carte d\'électeur', 'Kananga', '2023-08-07', '34943372947', NULL, 'Quinquelerie', 'Commerce', 'Maison yakuba', 'De la mission', NULL, NULL, '15', 12000000.00, NULL, NULL, '2026-03-15 15:32:28', '2026-03-15 15:32:28'),
('CL-EBENKGA-26-00166', 'ZON-EBENKGA-26-00002', 'DEMBU', 'MUPOYI', 'REGINE', NULL, '0994290394', 'F', '2026-03-15', 'KANANGA', 'AV. KALENGE, N09, Q/ TSHISAMBI, C/KGA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-03-11', '9876', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '28', NULL, NULL, NULL, '2026-03-15 15:33:04', '2026-03-15 15:33:04'),
('CL-EBENKGA-26-00167', 'ZON-EBENKGA-26-00002', 'KAMBA', 'MUTOMBO', 'JEAN PIEERE', NULL, '0975489605', 'M', '2026-03-15', 'KANANGA', 'AV. DU PEUPLE, N25, Q/ MALANDJI, C/ KGA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-03-11', '88', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '2', NULL, NULL, NULL, '2026-03-15 15:35:37', '2026-03-15 15:35:37'),
('CL-EBENKGA-26-00168', 'ZON-EBENKGA-26-00001', 'Tshilemba', 'Kashilumba', 'Berth', NULL, '0978502969', 'F', '1992-06-15', 'Lubumbashi', 'Aéroport,Eva, kananga', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-02-20', '34919768356', NULL, 'Boutique', 'Commerce', NULL, NULL, NULL, NULL, '14', 1000000.00, NULL, NULL, '2026-03-15 15:36:59', '2026-03-15 15:36:59'),
('CL-EBENKGA-26-00169', 'ZON-EBENKGA-26-00002', 'KUAMBA', 'KALAMBA', 'YOLANDE', NULL, '0817401566', 'F', '1997-12-29', 'KANANGA', 'AV.ESPOIR, N28, Q/MALANDJI, C/ KGA', 'Marié', 'GASTON MPEMBU', 'Carte d\'électeur', 'KANANGA', '2023-03-11', '77', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-15 15:40:30', '2026-03-15 15:40:30'),
('CL-EBENKGA-26-00170', 'ZON-EBENKGA-26-00002', 'TSHIELA', 'KANYIMA', 'MARTHE', NULL, '0978162376', 'F', '2000-04-23', 'LUIZA', 'AV. MAMANYEMO, Q/ TSHISAMBI, C/ KGA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-03-11', '67', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-15 15:43:24', '2026-03-15 15:43:24'),
('CL-EBENKGA-26-00171', 'ZON-EBENKGA-26-00001', 'Beya', 'Tshimbawu', 'Sarrive', NULL, '0996559518', 'M', '1993-11-24', 'Kananga', 'Camp , plateau, kananga', 'Marié', 'Ngalula Sophie', 'Carte d\'électeur', 'Kananga', '2023-03-16', '34924169699', NULL, 'Boutique', 'Commerce', NULL, NULL, NULL, NULL, '9', 450000.00, NULL, NULL, '2026-03-15 15:46:54', '2026-03-15 15:46:54'),
('CL-EBENKGA-26-00172', 'ZON-EBENKGA-26-00002', 'MASUMBU', 'KABAMBA', 'CELESTINE', NULL, '0970333587', 'F', '2026-12-12', 'KANANGA', 'AV? TSHISAMBI,N11, c: kga', 'Marié', 'NESTOR TSHIPANGA', 'Carte d\'électeur', 'KANANGA', '2023-02-05', '310', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '8', NULL, NULL, NULL, '2026-03-15 15:48:29', '2026-03-15 15:48:29'),
('CL-EBENKGA-26-00173', 'ZON-EBENKGA-26-00001', 'Bakamusua', 'Mbuyi', 'Thérèse', NULL, '0974569885', 'F', '1979-11-25', 'Kananga', 'Ubundu,kele kele, katoka', 'Divorcé', NULL, 'Carte d\'électeur', 'Kananga', '2023-02-03', '34929770651', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, '7', 700000.00, NULL, NULL, '2026-03-15 15:50:47', '2026-03-15 15:50:47'),
('CL-EBENKGA-26-00174', 'ZON-EBENKGA-26-00001', 'Masanka', 'Mulumba', 'Astrid', NULL, '0995688947', 'F', '1976-11-18', 'Kananga', 'Lolasi,Malandji , kananga', 'Marié', 'Ongondo Damien', 'Carte d\'électeur', 'Kananga', '2023-12-10', '34941996636', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, '5', 420000.00, NULL, NULL, '2026-03-15 15:56:13', '2026-03-15 15:56:13'),
('CL-EBENKGA-26-00175', 'ZON-EBENKGA-26-00001', 'Akoka', 'Omambo', 'Ferdinand', NULL, '0999698860', 'M', '1967-03-10', 'Hotokodi', 'Kanyuka,mulunda, Lukonga', 'Marié', 'Olakengo Adel', 'Carte d\'électeur', 'Kananga', '2023-01-29', '34938768569', NULL, 'Boutique d\'habillement', 'Commerce', NULL, NULL, NULL, NULL, '20', 2000000.00, NULL, NULL, '2026-03-15 16:01:33', '2026-03-15 16:01:33'),
('CL-EBENKGA-26-00176', 'ZON-EBENKGA-26-00001', 'Muluishi', 'Tujibikile', 'Honoré', NULL, '0998399910', 'M', '1979-04-29', 'Kananga', 'Mulumba, Mpokolo katoka', 'Marié', 'Bitshilualia', 'Carte d\'électeur', 'Kananga', '2023-01-29', '34930568974', NULL, 'Boutique d\'habillement', 'Commerce', NULL, NULL, NULL, NULL, '9', 900000.00, NULL, NULL, '2026-03-15 16:06:29', '2026-03-15 16:06:29'),
('CL-EBENKGA-26-00177', 'ZON-EBENKGA-26-00002', 'NTUMBA', 'MUAMBA', 'MADELEINE', NULL, '0992464728', 'F', '1972-03-01', 'KGA', 'AV. BANDAKA, N04, Q/ KELE KELE, C/ KATOKA', 'Veuf', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-05', '3436', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '8', NULL, NULL, NULL, '2026-03-15 16:07:59', '2026-03-15 16:07:59'),
('CL-EBENKGA-26-00178', 'ZON-EBENKGA-26-00001', 'Danga', 'Okako', 'Mireille', NULL, '0818518132', 'F', '1988-04-07', 'Kananga', 'Bandaka,kele,kele, katoka', 'Marié', 'Fidèle Okeko', 'Autre', 'Kananga', '2023-03-10', '36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', 850000.00, NULL, NULL, '2026-03-15 16:11:15', '2026-03-15 16:11:15'),
('CL-EBENKGA-26-00179', 'ZON-EBENKGA-26-00002', 'LUSAMBA', 'MANDE', 'LUCIENE', NULL, '0970307648', 'F', '1992-01-25', 'KANANFGA', 'AV. BALATUSHIPA, Q/ KELE KELE', 'Marié', 'HUBERT MULUMBA', 'Carte d\'électeur', 'KANANGA', '2023-02-05', '3115', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '8', NULL, NULL, NULL, '2026-03-15 16:14:01', '2026-03-15 16:14:01'),
('CL-EBENKGA-26-00180', 'ZON-EBENKGA-26-00002', 'KABANGU', 'MBUYI', 'CANDICE', NULL, '0972942098', 'F', '1992-11-25', 'KOLWEZI', 'AV', 'Marié', 'JOSEPH MUKENDI', 'Carte d\'électeur', 'KANANGA', '2023-02-05', '910', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '3', NULL, NULL, NULL, '2026-03-15 16:16:56', '2026-03-15 16:16:56'),
('CL-EBENKGA-26-00181', 'ZON-EBENKGA-26-00001', 'Kapinga', 'Kabundi', 'Marie', NULL, '0974039272', 'F', '1986-05-05', 'Kananga', 'Lulua,kele kele katoka', 'Marié', 'Jean efunda', 'Autre', 'Kananga', '2024-03-17', '120', NULL, 'Boutique d\'habillement', 'Commerce', NULL, NULL, NULL, NULL, '12', 1200000.00, NULL, NULL, '2026-03-15 16:17:27', '2026-03-15 16:17:27'),
('CL-EBENKGA-26-00182', 'ZON-EBENKGA-26-00002', 'KAYAYA', 'TSHIKAYA', 'JEANNETE', NULL, '0990733545', 'F', '1996-08-28', 'KANANGA', 'AV. WALIKALE, N05, Q/ MALANDJI, C/ KGA', 'Marié', 'BAKAMBA JOSEPH', 'Carte d\'électeur', 'KANANGA', '2023-02-05', '7657', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '3', NULL, NULL, NULL, '2026-03-15 16:20:59', '2026-03-15 16:20:59'),
('CL-EBENKGA-26-00183', 'ZON-EBENKGA-26-00001', 'Ndibu', 'Kayombo', 'Jean Pierre', NULL, '0970977124', 'M', '1976-10-09', 'Kananga', 'Du canal,Malandji, kananga', 'Veuf', NULL, 'Carte d\'électeur', 'Kananga', '2024-02-24', '34929783084', NULL, 'Divers', 'Commerce', NULL, NULL, NULL, NULL, '15', 2500000.00, NULL, NULL, '2026-03-15 16:21:15', '2026-03-15 16:21:15'),
('CL-EBENKGA-26-00184', 'ZON-EBENKGA-26-00002', 'BILONDA', 'KALONJI', 'ALPHO', NULL, '097370070', 'F', '1998-09-09', 'KGA', 'AV.', 'Marié', 'MIKOBI FRANCOIS', 'Carte d\'électeur', 'KANANGA', '2023-02-05', '518', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '3', NULL, NULL, NULL, '2026-03-15 16:23:50', '2026-03-16 13:59:12'),
('CL-EBENKGA-26-00185', 'ZON-EBENKGA-26-00001', 'Mutombo', 'Kayembe', 'Oscar', NULL, '0999273590', 'M', '1986-03-13', 'Kananga', 'Du canal, Malandji, kananga', 'Marié', 'Tuseku Anny', 'Carte d\'électeur', 'Kananga', '2024-03-17', '36109', NULL, 'Vente des souliers', 'Commerce', NULL, NULL, NULL, NULL, '20', 30000000.00, NULL, NULL, '2026-03-15 16:25:28', '2026-03-15 16:25:28'),
('CL-EBENKGA-26-00186', 'ZON-EBENKGA-26-00002', 'MUNDA', 'LUFULUABU', 'CHANTAL', NULL, '0999677182', 'F', '1985-02-12', 'KANANGA', 'AV. DU COMMERCE, N05, Q/ MALANDJI, C/ KGA', 'Marié', 'BATUAKUILE CHRISTOPH', 'Carte d\'électeur', 'KANANGA', '2023-02-05', '934', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-15 16:27:41', '2026-03-15 16:27:41'),
('CL-EBENKGA-26-00187', 'ZON-EBENKGA-26-00001', 'Kapinga', 'Tshiyombo', 'Rose', NULL, '0813027722', 'F', '1990-12-12', 'Lubumbashi', 'KAMAYI, kananga', 'Divorcé', NULL, 'Carte d\'électeur', 'Kananga', '2025-03-28', '126', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, '7', 1200000.00, NULL, NULL, '2026-03-15 16:29:47', '2026-03-15 16:29:47'),
('CL-EBENKGA-26-00188', 'ZON-EBENKGA-26-00002', 'KAPINGA', 'KANGOMBE', 'ROSE', NULL, '0992725145', 'F', '1981-11-11', 'KGA', 'AV. MUAMBA, N18, Q/ PLATEAU, C/KGA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-05', '57576', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '17', NULL, NULL, NULL, '2026-03-15 16:31:48', '2026-03-15 16:31:48'),
('CL-EBENKGA-26-00189', 'ZON-EBENKGA-26-00001', 'Ehadi', 'Lohalo', 'Bijoux', NULL, '0980624842', 'F', '1986-08-23', 'Lodja', 'Obundu ,kele kele katoka', 'Marié', 'Esuka Faustin', 'Carte d\'électeur', 'Kananga', '2023-02-28', '34923579641', NULL, 'Boutique d\'habillement', 'Commerce', NULL, NULL, NULL, NULL, '15', 1000000.00, NULL, NULL, '2026-03-15 16:34:14', '2026-03-15 16:34:14'),
('CL-EBENKGA-26-00190', 'ZON-EBENKGA-26-00001', 'Mudimbi', 'Mulumba', 'Jean Pierre', NULL, '0993236301', 'M', '1989-01-01', 'Tshikapa', 'Du commerce,Ndesha', 'Marié', 'Véronique Ngombe', 'Carte d\'électeur', 'Kananga', '2023-03-31', '34943772959', NULL, 'Boutique d\'habillement', 'Commerce', NULL, NULL, NULL, NULL, '18', 2000000.00, NULL, NULL, '2026-03-15 16:38:10', '2026-03-15 16:38:10'),
('CL-EBENKGA-26-00191', 'ZON-EBENKGA-26-00001', 'Kapia', 'Bakubila', 'Ambroise', NULL, '0975648622', 'M', '1997-11-18', 'Ndemba', 'Matamba,Ndesha', 'Célibataire', NULL, 'Autre', 'Kananga', '2025-03-10', '109', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, '3', 1000000.00, NULL, NULL, '2026-03-15 16:40:58', '2026-03-15 16:40:58'),
('CL-EBENKGA-26-00192', 'ZON-EBENKGA-26-00004', 'BAKAPETEKA', 'KABUE', 'CHOUCHOU', NULL, '0965865356', 'F', '1999-12-12', 'TSHIKAPA', 'KAMBALA, N°20 C/ NGANZA', 'Marié', 'BERTRAND BUPOLE', 'Carte d\'électeur', 'KANANGA', '2023-02-16', '34950578381', 'clients/1773601010_1000015933.jpeg', 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 17:56:50', '2026-03-15 17:56:50'),
('CL-EBENKGA-26-00193', 'ZON-EBENKGA-26-00001', 'Lokoso', 'Akake', 'Solange', NULL, '0980420172', 'F', '1986-03-28', 'Ilebo', 'Du canal KAMAYI, kananga', 'Marié', 'Jacques Ekango', 'Carte d\'électeur', 'Kananga', '2023-03-03', '349335811903', NULL, 'Boutique d\'habillement', 'Commerce', NULL, NULL, NULL, NULL, '6', 1200000.00, NULL, NULL, '2026-03-15 17:57:55', '2026-03-15 17:57:55'),
('CL-EBENKGA-26-00194', 'ZON-EBENKGA-26-00001', 'MUKUNA', 'Ntumba', 'Faustine', NULL, '0978622896', 'F', '1993-06-18', 'Mbuji mayi', 'Matamba, Ndesha', 'Marié', 'Emmanuel Buanga', 'Carte d\'électeur', 'Kananga', '2023-03-31', '34922769393', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, '12', 500000.00, NULL, NULL, '2026-03-15 18:01:33', '2026-03-15 18:01:33'),
('CL-EBENKGA-26-00195', 'ZON-EBENKGA-26-00004', 'KAKANE', 'MUYAYA', 'IGETTE', NULL, '0977168585', 'F', '1989-08-09', 'LUBUMBASHI', 'N°25 Av MALOLE Q/ PLATEAU, C/ KANANGA', 'Marié', 'DELPIERO TSHIBAMBA', 'Carte d\'électeur', 'KANANGA', '2023-02-01', '34925368311', 'clients/1773601380_1000015935.jpeg', 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 18:03:00', '2026-03-15 18:03:00'),
('CL-EBENKGA-26-00196', 'ZON-EBENKGA-26-00004', 'NYASHI', 'TSHIDIKI', 'CONSTANTINE', NULL, '0824545674', 'F', '1984-08-27', 'KANANGA', 'N°18 Av MUKALAMUSHI Q/ KAMAYI, C/ KANANGA', 'Marié', 'GILBERT ILUNGA', 'Carte d\'électeur', 'KANANGA', '2023-03-01', '34922382514', 'clients/1773601756_1000015929.jpeg', 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 18:09:16', '2026-03-15 18:09:16'),
('CL-EBENKGA-26-00197', 'ZON-EBENKGA-26-00001', 'Mujinga', 'Kasamba', 'Rebecca', NULL, '0971286785', 'F', '1978-03-17', 'Mubinza', 'Nzoba, KAMAYI kananga', 'Marié', 'Mashiswa', 'Autre', 'Kananga', '2024-03-19', '234', NULL, 'Vente d\'eau', 'Commerce', NULL, NULL, NULL, NULL, '19', 750000.00, NULL, NULL, '2026-03-15 18:11:50', '2026-03-15 18:11:50'),
('CL-EBENKGA-26-00198', 'ZON-EBENKGA-26-00001', 'Mukuna', 'Tshienda', 'Alidor', NULL, '0973560856', 'M', '1996-06-10', 'Tshikapa', 'Nganndu,Malandji, kananga', 'Célibataire', NULL, 'Permis de conduire', 'Kananga', '2022-03-17', '891', NULL, 'Transport', 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 18:14:48', '2026-03-15 18:14:48'),
('CL-EBENKGA-26-00199', 'ZON-EBENKGA-26-00001', 'Dembo', 'NDjudi', 'Souzane', NULL, '0982376808', 'F', '1993-01-01', 'Lodja', 'Diokopunda,kele kele katoka', 'Marié', 'Omombe jean Paul', 'Carte d\'électeur', 'Kananga', '2023-03-15', '34934981054', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 18:18:25', '2026-03-15 18:18:25'),
('CL-EBENKGA-26-00200', 'ZON-EBENKGA-26-00001', 'Odjambeya', 'Ohote', 'Angel', NULL, '0995208102', 'F', '1982-05-20', 'Lodja', 'Lulua, kele kele katoka', 'Marié', 'Osesa Gabriel', 'Carte d\'électeur', 'Kananga', '2023-02-14', '3493973487', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 18:22:03', '2026-03-15 18:22:03'),
('CL-EBENKGA-26-00201', 'ZON-EBENKGA-26-00004', 'TSHITUKA', 'MUKENDI', 'HÉLÈNE', NULL, '0999849715', 'F', '1970-01-12', 'TSHIDIMBA', 'N°09 Av TÉLÉCOM Q/ KAMAYI C/ KANANGA', 'Marié', 'MPUTU SHABANTU GLOIRE', 'Carte d\'électeur', 'KANANGA', '2023-02-21', '34925980928', 'clients/1773602537_1000015924.jpeg', 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 18:22:17', '2026-03-15 18:22:17'),
('CL-EBENKGA-26-00202', 'ZON-EBENKGA-26-00001', 'Engudi', 'Washi', 'Juliette', NULL, '0999116855', 'F', '1974-08-18', 'Lodja', 'Banzangungu, kele kele katoka', 'Marié', 'Wema André', 'Carte d\'électeur', 'Kananga', '2023-08-02', '34929773896', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 18:25:34', '2026-03-15 18:25:34'),
('CL-EBENKGA-26-00203', 'ZON-EBENKGA-26-00004', 'NTANGA', 'NKONGOLO', 'NATHALIE', NULL, '0987643267', 'F', '1997-01-22', 'TSHIKAPA', 'N° 11 AV KABONGO Q/ KATOKA II C/ KATOKA', 'Marié', 'VINCENT KABUE', 'Carte d\'électeur', 'KANANGA', '2023-02-08', '34934973054', 'clients/1773602887_1000015921.jpeg', 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 18:28:07', '2026-03-15 18:28:07'),
('CL-EBENKGA-26-00204', 'ZON-EBENKGA-26-00001', 'Ndomba', 'Badibanga', 'Antoinette', NULL, '0994784697', 'F', '1982-12-12', 'Kananga', 'Walkal2 Malandji kananga', 'Marié', 'Jean Kambulu', 'Autre', 'Kananga', '2023-03-19', '567', NULL, 'Atelier de couture', 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 18:43:34', '2026-03-15 18:43:34'),
('CL-EBENKGA-26-00205', 'ZON-EBENKGA-26-00001', 'Mukanga', 'Akatshi', 'Thérèse', NULL, '0978168726', 'F', '1979-05-10', 'Lodja', 'Lulua kele kele katoka', 'Veuf', NULL, 'Autre', 'Kananga', '2025-03-16', '630', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 18:45:57', '2026-03-15 18:45:57'),
('CL-EBENKGA-26-00206', 'ZON-EBENKGA-26-00001', 'Nsafi', 'Lusanga', 'Lydie', NULL, '0972172485', 'F', '1994-01-14', 'Kabinda', 'Kasavu, Malandji kananga', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kinshasa', '2026-04-11', '786', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 18:49:32', '2026-03-15 18:49:32'),
('CL-EBENKGA-26-00207', 'ZON-EBENKGA-26-00004', 'NGALULA', 'MBUYI', 'REBECCA', NULL, '0987457777', 'F', '1988-05-17', 'KANANGA', 'N°25 Av Tshiala Q/ Kamayi, C/ Kananga', 'Marié', 'MULUMBA DOMINIQUE', 'Carte d\'électeur', 'KANANGA', '2026-02-05', '349245553097', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 18:58:43', '2026-03-15 18:58:43'),
('CL-EBENKGA-26-00208', 'ZON-EBENKGA-26-00001', 'Malamba', 'Tshilombo', 'Stéphane', NULL, '0994729469', 'M', '1975-04-12', 'Kananga', 'Balanganayi Mpokolo katoka', 'Marié', 'Ntumba Thérèse', 'Carte d\'électeur', 'Kananga', '2022-03-15', '931170129', NULL, 'Divers', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 18:59:46', '2026-03-15 18:59:46'),
('CL-EBENKGA-26-00209', 'ZON-EBENKGA-26-00001', 'Bampangidi', 'Kande', 'Véronique', NULL, '099698858', 'F', '1989-03-19', 'Kananga', 'Decadet kamayi, kananga', 'Marié', 'Kabamba jean pierre', 'Carte d\'électeur', 'Kananga', '2023-02-13', '34928541246', NULL, 'Vente des plastiques', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 19:04:03', '2026-03-15 19:04:03'),
('CL-EBENKGA-26-00210', 'ZON-EBENKGA-26-00004', 'SHAMASHANGA', 'SHAMASHANGA', 'BONIFACE', NULL, '0994572254', 'M', '1983-09-22', 'MUEKA', 'N° 24 Av KALOMBO MFUMU Q/ KAMAYI C/ KANANGA', 'Marié', 'Jeanne MILOLO', 'Carte d\'électeur', 'KANANGA', '2023-02-23', '34926547985', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 19:04:26', '2026-03-15 19:04:26'),
('CL-EBENKGA-26-00211', 'ZON-EBENKGA-26-00001', 'Tshiala', 'Tshifuembe', 'Merveil', NULL, '098412174', 'F', '2005-02-01', 'Kananga', 'Du canal kasavubu kananga', 'Célibataire', NULL, 'Autre', 'Kananga', '2022-01-16', '587', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 19:08:02', '2026-03-15 19:08:02'),
('CL-EBENKGA-26-00212', 'ZON-EBENKGA-26-00004', 'MAMU', 'NTUMBA', 'AISHA', NULL, '0992026753', 'F', '1968-06-20', 'TSHIKAPA', 'N° 20 Av Salongo, Q/ Mpokolo C/ Katoka', 'Marié', 'KALUMBA ANDRE', 'Carte d\'électeur', 'KANANGA', '2023-03-27', '34925677563', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 19:12:47', '2026-03-15 19:12:47'),
('CL-EBENKGA-26-00213', 'ZON-EBENKGA-26-00001', 'Muawuke', 'Ntumba', 'Fatuma', NULL, '0994090196', 'F', '1983-02-04', 'Kananga', 'Bandaka, kele kele katoka', 'Marié', 'Tshibuabua Emile', 'Carte d\'électeur', 'Kananga', '2021-03-14', '100', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 19:14:09', '2026-03-15 19:14:09'),
('CL-EBENKGA-26-00214', 'ZON-EBENKGA-26-00004', 'MALU', 'TSHILUMBA', 'BERNADETTE', NULL, '0991574567', 'F', '1980-03-18', 'KANANGA', 'N°01 Av Makasha Q/ Mobutu, C/ Kananga', 'Marié', 'NKOMBUA BRAVE', 'Carte d\'électeur', 'KANANGA', '2023-02-14', '34924674556', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 19:16:45', '2026-03-15 19:16:45'),
('CL-EBENKGA-26-00215', 'ZON-EBENKGA-26-00001', 'Loketembo', 'Mukanga', 'Silas', NULL, '0971427950', 'M', '1979-06-03', 'Shudi', 'Banzangungu kele kele katoka', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-05-18', '340', NULL, 'Savonnerie', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 19:21:40', '2026-03-15 19:21:40'),
('CL-EBENKGA-26-00216', 'ZON-EBENKGA-26-00004', 'MUKADI', 'NSOMBAMANYA', 'RAPHAËL', NULL, '0996747535', 'M', '1996-02-13', 'DEMBA', 'N° 209 Av Du Canal, Q/ Malanji, C/ Kananga', 'Marié', 'MARIE KAPINGA', 'Carte d\'électeur', 'KANANGA', '2023-02-22', '349250035907', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 19:23:38', '2026-03-15 19:23:38'),
('CL-EBENKGA-26-00217', 'ZON-EBENKGA-26-00001', 'Tshitebua', 'Kaba', 'Merveil', NULL, '0975862438', 'F', '2005-04-30', 'Mbuji mayi', 'Kasavubu malandji kananga', 'Marié', 'Kanyinda john', 'Carte d\'électeur', 'Kananga', '2023-08-13', '420', NULL, 'Friperie', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 19:25:10', '2026-03-15 19:25:10'),
('CL-EBENKGA-26-00218', 'ZON-EBENKGA-26-00001', 'Mudinganyi', 'Mulamba', 'Jose', NULL, '0977293518', 'F', '1986-08-18', 'Kinshasa', 'Lubumbashi kele kele katoka', 'Marié', 'Kabeya jean', 'Carte d\'électeur', 'Kananga', '2024-05-31', '678', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 19:32:48', '2026-03-15 19:32:48'),
('CL-EBENKGA-26-00219', 'ZON-EBENKGA-26-00001', 'Katema', 'Kabutakapua', 'Augustin', NULL, '0998204966', 'F', '2002-08-04', 'Kananga', 'Lomela, Ndesha', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2025-09-12', '984', NULL, 'Marché noir', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 19:37:05', '2026-03-15 19:37:05'),
('CL-EBENKGA-26-00220', 'ZON-EBENKGA-26-00001', 'Nyunga', 'Ngalamulume', 'Elysée', NULL, '0997760743', 'F', '1999-11-11', 'Kananga', 'Mbombo,Epro, katoka', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-12', '44', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 19:41:06', '2026-03-15 19:41:06'),
('CL-EBENKGA-26-00221', 'ZON-EBENKGA-26-00001', 'Mputu', 'Mpuka', 'Antoinette', NULL, '0972159813', 'F', '1990-03-15', 'Kananga', 'Mueka, Tshibanda banda Ndesha', 'Marié', 'Mukanga  Alfred', 'Carte d\'électeur', 'Kananga', '2024-03-12', '58', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 19:45:47', '2026-03-15 19:45:47'),
('CL-EBENKGA-26-00222', 'ZON-EBENKGA-26-00001', 'Ngondo', 'Ngalamulume', 'Elysée', NULL, '0973153913', 'F', '1968-03-15', 'Kananga', 'Kalamba, Malandji kananga', 'Veuf', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-18', '70', NULL, 'Vente des souliers', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 19:49:58', '2026-03-15 19:49:58'),
('CL-EBENKGA-26-00223', 'ZON-EBENKGA-26-00001', 'Ngondo', 'Ngalamulume', 'Elysée', NULL, '0973153913', 'F', '1968-02-16', 'Kananga', 'Kalamba Malandji kananga', 'Veuf', NULL, 'Carte d\'électeur', 'Kananga', '2023-05-12', '312', NULL, 'Vente des souliers', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 03:36:40', '2026-03-16 03:36:40'),
('CL-EBENKGA-26-00224', 'ZON-EBENKGA-26-00001', 'Mbombo', 'Kasanda', 'Anny', NULL, '0997559452', 'F', '1992-08-21', 'Kananga', 'Dibindi Tshibanda banda Ndesha', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2025-02-13', '98', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 03:40:49', '2026-03-16 03:40:49'),
('CL-EBENKGA-26-00225', 'ZON-EBENKGA-26-00001', 'Ntumba', 'Ngalamulume', 'Clarisse', NULL, '0900189570', 'F', '1991-12-25', 'Tshikapa', 'Sanga bantu kamayi kananga', 'Marié', 'Steve Tshibangu', 'Autre', 'Kananga', '2023-04-23', '910', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 03:46:44', '2026-03-16 03:46:44'),
('CL-EBENKGA-26-00226', 'ZON-EBENKGA-26-00001', 'Tshibola', 'Kakese', 'Marie', NULL, '0995598676', 'F', '1983-07-15', 'Kananga', 'Kapumbu, katoka', 'Divorcé', NULL, 'Carte nationale d\'identité', 'Kananga', '2025-01-16', '230', NULL, 'Boutique d\'habillement', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 03:49:49', '2026-03-16 03:49:49'),
('CL-EBENKGA-26-00227', 'ZON-EBENKGA-26-00001', 'Desembo', 'Okenge', 'Verronica', NULL, '0993815474', 'F', '1997-05-05', 'Kananga', 'Lulua kele kele katoka', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2024-09-21', '413', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 03:52:55', '2026-03-16 03:52:55'),
('CL-EBENKGA-26-00228', 'ZON-EBENKGA-26-00001', 'Ngalula', 'Makita', 'Brigitte', NULL, '0970701818', 'F', '1986-04-03', 'Kananga', 'Lulua cinquantenaire kananga', 'Marié', 'Nganndu Moïse', 'Carte d\'électeur', 'Kananga', '2023-03-19', '78', NULL, 'Boutique d\'habillement', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 06:32:26', '2026-03-16 06:32:26'),
('CL-EBENKGA-26-00229', 'ZON-EBENKGA-26-00003', 'PANDA', 'MPUTU', 'OBEIN', NULL, '0973389184', 'F', '2002-01-11', 'Kananga', 'Av lualuete Q/ Mpokolo C/ katoka n° 2', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-03-12', '3494', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '2', NULL, NULL, NULL, '2026-03-16 06:42:08', '2026-03-16 06:42:08'),
('CL-EBENKGA-26-00230', 'ZON-EBENKGA-26-00001', 'Kapinga', 'Mubenga', 'Godet', NULL, '0998998820', 'F', '1990-02-02', 'Kananga', 'Tshinsambi', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-05-05', '99', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 06:46:05', '2026-03-16 06:46:05'),
('CL-EBENKGA-26-00231', 'ZON-EBENKGA-26-00006', 'Tshibola', 'Mutombo', 'Monique', NULL, '0979125236', 'F', '1993-02-26', 'Tshikula', 'Dibatayi 30,tshinsambi, kananga', 'Marié', 'Nkole bakadipanda Timothée', 'Carte d\'électeur', 'Kananga', '2023-02-23', '34920178425', NULL, 'Atelier', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 06:46:44', '2026-03-16 06:46:44'),
('CL-EBENKGA-26-00232', 'ZON-EBENKGA-26-00008', 'KAPINGA', 'MPETEMBE', 'Hélène', NULL, NULL, 'F', '1972-06-13', 'KABUE', '21, NGANZA, KAPANDA,  KATOKA', 'Veuf', NULL, 'Carte d\'électeur', 'Kananga', '2023-02-20', '34929980137', NULL, 'Marchandises de cuisine', 'Commerce', NULL, NULL, NULL, NULL, '10', 200000.00, NULL, NULL, '2026-03-16 06:52:01', '2026-03-16 06:52:01'),
('CL-EBENKGA-26-00233', 'ZON-EBENKGA-26-00001', 'Ngoya', 'Mpemba', 'Odette', NULL, '0983690779', 'F', '1985-06-28', 'Lubumbashi', 'Misumba', 'Marié', 'Célestin', 'Carte d\'électeur', 'Kananga', '2023-05-27', '97', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 06:53:26', '2026-03-16 06:53:26'),
('CL-EBENKGA-26-00234', 'ZON-EBENKGA-26-00008', 'MANYINA', 'MBALA', 'Marguerite', NULL, '0997078154', 'F', '1988-02-05', 'Muene Ditu', 'KAPANDA, KATOKA', 'Marié', 'LUSHIKU BENOIT', 'Carte d\'électeur', 'Muene Ditu', '2023-03-27', '35238388693', NULL, 'Moulin', 'Service', NULL, NULL, NULL, NULL, '15', 150000.00, NULL, NULL, '2026-03-16 06:57:24', '2026-03-16 06:57:24'),
('CL-EBENKGA-26-00235', 'ZON-EBENKGA-26-00006', 'Misenga', 'Buabua', 'Agnès', NULL, '0991168767', 'F', '1999-07-05', 'Kananga', 'Révolution 17,lubuwa, ndesha', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-04', '34944982136', NULL, NULL, 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 06:58:06', '2026-03-16 06:58:06'),
('CL-EBENKGA-26-00236', 'ZON-EBENKGA-26-00003', 'BADiBAKE', 'KEMAYI', 'BERNARD', NULL, '0991327672', 'M', '1983-02-28', 'Kananga', 'Av Muenaditu Q/ kamilabi C/ ndesha n° 39', 'Marié', 'ODiA Bernadette', 'Carte nationale d\'identité', 'Kananga', '2023-02-16', '34946378893', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-16 06:58:14', '2026-03-16 06:58:14'),
('CL-EBENKGA-26-00237', 'ZON-EBENKGA-26-00001', 'Kabongo', 'Kabamba', 'Pierre', NULL, '0999014591', 'M', '1986-05-25', 'Kananga', 'Luiza Malandji Kananga', 'Marié', 'Mputu Charlotte', 'Carte d\'électeur', 'Kananga', '2023-03-09', '33941590206', NULL, 'Friperie', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:00:02', '2026-03-16 07:00:02'),
('CL-EBENKGA-26-00238', 'ZON-EBENKGA-26-00002', 'MUJINGA', 'TSHIEPELA', 'LOUIS', NULL, '09773777535', 'M', '1992-05-05', 'MUENE DITU', 'AV.SALONGO, Nº14,Q/ TSHISABI,C/KGA', 'Marié', 'GODELIVE KENGELE', 'Carte d\'électeur', 'Kga', '2023-02-19', '34920580717', NULL, 'DIVERS', 'Commerce', NULL, NULL, NULL, NULL, '2', NULL, NULL, NULL, '2026-03-16 07:01:35', '2026-03-16 07:01:35'),
('CL-EBENKGA-26-00239', 'ZON-EBENKGA-26-00006', 'Bapa', 'Nzadi', 'Béatrice', NULL, '0972534137', 'F', '1981-05-24', 'Luebo', 'Katoka1,katoka', 'Marié', 'Muepu Henri', 'Carte d\'électeur', 'Kananga', '2023-01-27', '3493297286', NULL, 'Vente souliers', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:02:18', '2026-03-16 07:02:18'),
('CL-EBENKGA-26-00240', 'ZON-EBENKGA-26-00007', 'BULELA', 'BUABUA', 'ANNY', NULL, '0970976277', 'F', '1979-06-28', 'KINSHASA', 'AV KASAVUBU N° 63 Q/ MALANDJI C/KANANGA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2023-01-26', '34922766757', NULL, NULL, 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:03:07', '2026-03-16 07:03:07'),
('CL-EBENKGA-26-00241', 'ZON-EBENKGA-26-00008', 'TUBENZELE', 'KAPUKU', 'Marie', NULL, '0979509022', 'F', '1982-12-12', 'Kananga', 'KANYUKA, MULANDA, KATOKA', 'Marié', 'ILUNGA Fabrice', 'Carte d\'électeur', 'Kananga Ville', '2023-02-03', '34938771101', NULL, 'Vente de planches', 'Commerce', NULL, NULL, NULL, NULL, '25', 3500000.00, NULL, NULL, '2026-03-16 07:03:30', '2026-03-16 07:03:30'),
('CL-EBENKGA-26-00242', 'ZON-EBENKGA-26-00010', 'KUYONA', 'BAKATUMANA', 'REBECCA', NULL, '0980035127', 'F', '1990-12-05', 'KANANGA', 'N°53, DIBINDI, LUBUWA, NDESHA', 'Marié', 'NGALAMULUME EMMANUEL', 'Carte nationale d\'identité', 'KANANGA', '2023-03-10', '34943381052', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:05:06', '2026-03-16 07:05:06'),
('CL-EBENKGA-26-00243', 'ZON-EBENKGA-26-00006', 'Kabasele', 'Badibanga', 'Célestin', NULL, '0997322867', 'F', '1975-06-18', 'Kananga', 'Du canal 375,malandji, kananga', 'Marié', 'Pauline bipendu', 'Carte d\'électeur', 'Kananga', '2023-03-16', '34931186757', NULL, 'Atelier', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:06:58', '2026-03-16 07:06:58'),
('CL-EBENKGA-26-00244', 'ZON-EBENKGA-26-00004', 'NDJOKA', 'KASONGO', 'CHARLY', NULL, '0999923486', 'F', '1981-03-01', 'KANANGA', 'N°09 Av KABONGO Q/ PLATEAU C/ KANANGA', 'Marié', 'OMASOMBO', 'Carte d\'électeur', 'KANANGA', '2026-02-28', '349245', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:07:06', '2026-03-16 07:07:06'),
('CL-EBENKGA-26-00245', 'ZON-EBENKGA-26-00007', 'TSHIBAMBA', 'BEYA', 'OMER', NULL, '0992215137', 'M', '1985-07-15', 'KANANGA', 'AV HUBOU N° 08 Q/ MPOKOLO C/KATOKA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2023-03-02', '34932984629', NULL, 'Transport', 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:09:07', '2026-03-16 07:09:07'),
('CL-EBENKGA-26-00246', 'ZON-EBENKGA-26-00001', 'Milolo', 'Mbuyi', 'Mireille', NULL, '0994136796', 'F', '1995-07-04', 'Tshikapa', 'Du Rail KAMAYI Kananga', 'Marié', 'Kama Albert', 'Autre', 'Kananga', '2022-03-27', '49', NULL, 'Boutique d\'habillement', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:09:10', '2026-03-16 07:09:10'),
('CL-EBENKGA-26-00247', 'ZON-EBENKGA-26-00008', 'SHKO', 'OYUNGU', 'Chantal', NULL, '0976588415', 'F', '1979-05-25', 'Kananga', '42, OBUNDU,  KELE-KELE, KATOKA', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga Ville', '2023-02-16', '34933185512', NULL, 'Vente du riz', 'Commerce', NULL, NULL, NULL, NULL, '15', 1500000.00, NULL, NULL, '2026-03-16 07:09:12', '2026-03-16 07:09:12'),
('CL-EBENKGA-26-00248', 'ZON-EBENKGA-26-00004', 'TSHITUKA', 'LUSONGO', 'ALPHONSINE', NULL, '0985531026', 'F', '1982-05-13', 'KANANGA', 'N°17 Q/ Mpokolo, C/ KATOKA', 'Marié', 'KABATA PAULIN', 'Carte d\'électeur', 'KANANGA', '2023-02-10', '349250', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:12:16', '2026-03-16 07:12:16'),
('CL-EBENKGA-26-00249', 'ZON-EBENKGA-26-00006', 'Mata', 'Makuma', 'Thoms', NULL, '0987550183', 'M', '1996-06-10', 'Banga', 'Likasi 45,malandji, kananga', 'Marié', 'Mireille minoro', 'Carte d\'électeur', 'Kananga', '2023-03-04', '32', NULL, 'Quinquelerie', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:12:18', '2026-03-16 07:12:18'),
('CL-EBENKGA-26-00250', 'ZON-EBENKGA-26-00001', 'Ngalula', 'Buabua', 'Christine', NULL, '0991961917', 'F', '1976-03-10', 'Kananga', 'Du canal Kamulumba kananga', 'Célibataire', NULL, 'Autre', 'Kananga', '2023-04-13', '17', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:12:27', '2026-03-16 07:12:27'),
('CL-EBENKGA-26-00251', 'ZON-EBENKGA-26-00002', 'MABILA', 'MUDIPANU', 'ALPHONSE', NULL, '0972142841', 'M', '2003-05-25', 'KGA', 'AV.KATENDE, Q/NDESHA2, C/NDESHA', 'Célibataire', NULL, 'Carte d\'électeur', 'KGA', '2023-02-11', '34942373126', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '2026-03-16 07:12:57', '2026-03-16 07:12:57'),
('CL-EBENKGA-26-00252', 'ZON-EBENKGA-26-00007', 'KAPINGA', 'KALALA', 'ALICE', NULL, '0977315399', 'F', '1984-12-12', 'LUBUMBASHI', 'AV KOLWEZI N°58 Q/KELE-KELE C/KATOKA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-24', '34929782834', NULL, 'VENTE DE MAÏS', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:14:52', '2026-03-16 07:14:52'),
('CL-EBENKGA-26-00253', 'ZON-EBENKGA-26-00001', 'Tshiamala', 'Bakatakabua', 'Léontine', NULL, '0974581062', 'F', '1988-12-27', 'Kananga', 'Du canal Malandji kananga', 'Marié', 'Miamu Mboyo jean', 'Carte d\'électeur', 'Kananga', '2023-01-08', '74', NULL, 'Vente d\'habit', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:16:38', '2026-03-16 07:16:38'),
('CL-EBENKGA-26-00254', 'ZON-EBENKGA-26-00004', 'KATONDO', 'MULAMBA', 'Jean Michel', NULL, '0994947644', 'M', '1977-08-20', 'TSHIKULA', 'N° 08 Av NGANZA, Q/ NGANZA NORD C/ NGANZA', 'Marié', 'BULELA THÉRÈSE', 'Carte d\'électeur', 'KANANGA', '2023-03-17', '34947592716', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:16:48', '2026-03-16 07:16:48'),
('CL-EBENKGA-26-00255', 'ZON-EBENKGA-26-00006', 'Lukadi', 'Lukadi', 'Verro', NULL, '09', 'F', '1994-03-19', 'Tshikapa', 'Tulengele 20, plateau, kananga', 'Marié', 'Ntumba henri', 'Carte d\'électeur', 'Kananga', '2023-03-12', '34927389919', NULL, 'Vente farine', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:17:29', '2026-03-16 07:17:29'),
('CL-EBENKGA-26-00256', 'ZON-EBENKGA-26-00008', 'MUANDA', 'NGALANKONGOLO', 'Jacques', NULL, NULL, 'F', '1991-10-15', 'Kananga', '19, TUBULUNKU, MPOKOLO, KATOKA', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga Ville', '2023-01-27', '349133567075', NULL, 'Moulin', 'Service', NULL, NULL, NULL, NULL, '12', 2000000.00, NULL, NULL, '2026-03-16 07:17:29', '2026-03-16 07:17:29'),
('CL-EBENKGA-26-00257', 'ZON-EBENKGA-26-00002', 'KANKU', 'TSHIPAMBA', 'MARGUERITE', NULL, '0978063206', 'F', '1999-02-28', 'TSHIKULA', 'AV.MATADI, Nº22,Q/N\'SELE,C/NGANZA', 'Célibataire', NULL, 'Carte d\'électeur', 'KGA', '2023-02-22', '34939773355', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:17:41', '2026-03-16 07:17:41'),
('CL-EBENKGA-26-00258', 'ZON-EBENKGA-26-00004', 'TSHIBUABUA', 'Wa TSHIBUABUA', 'AGNES / TSHITSHI', NULL, '0970790552', 'F', '1988-03-14', 'KANANGA', 'N°18 Av Kalusenga, Q/ TÉLÉCOM, C/ NGANZA', 'Marié', 'ANDRÉ MANGOLE', 'Carte d\'électeur', 'KANANGA', '2023-03-16', '349248', NULL, 'VENTE D\'EPICES', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:20:59', '2026-03-16 07:20:59'),
('CL-EBENKGA-26-00259', 'ZON-EBENKGA-26-00007', 'MUKINAYI', 'NTAMBWE', 'NICODÈME', NULL, '0991978686', 'M', '1996-12-17', 'KANANGA', 'AV KALOTA N° 3 Q/ MPOKOLO C/KATOKA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-10', '34943572525', NULL, 'Travailleur', 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:22:17', '2026-03-16 07:22:17'),
('CL-EBENKGA-26-00260', 'ZON-EBENKGA-26-00010', 'FUAMBA', 'BADIBANGA', 'STANDARD', NULL, '0994632559', 'M', '1771-11-12', 'LUBONDAYI', 'N°10, TSHIBANGU, KATOKA 2 , KATOKA', 'Marié', 'MBOZO SOUZANE', 'Carte d\'électeur', 'Kananga', '2023-02-21', '34930978094', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:22:30', '2026-03-16 07:22:30'),
('CL-EBENKGA-26-00261', 'ZON-EBENKGA-26-00004', 'MULANGA', 'MVITA', 'LOUISE', NULL, '0983181788', 'F', '1956-11-11', 'LIKASI', 'N°54 AV MFUAMBA, Q/ KAMAYI, C/ KANANGA', 'Veuf', NULL, 'Carte d\'électeur', 'KANANGA', '2023-01-22', '3498673', NULL, 'VENTE D\'EPICES', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:24:43', '2026-03-16 07:24:43');
INSERT INTO `tb_clients` (`matricule`, `code_zone`, `nom`, `postnom`, `prenom`, `email`, `telephone`, `sexe`, `date_naissance`, `lieu_naissance`, `adresse`, `etat_civil`, `nom_conjoint`, `type_piece_identite`, `lieu_delivrance_piece`, `date_delivrance_piece`, `numero_piece_identite`, `photo`, `secteur_activite`, `type_activite`, `nom_entreprise`, `adresse_entreprise`, `telephone_entreprise`, `statut_entreprise`, `nombre_annees_experience`, `revenu_mensuel`, `revenu_mensuel_devise`, `autres_details_activite`, `created_at`, `updated_at`) VALUES
('CL-EBENKGA-26-00262', 'ZON-EBENKGA-26-00008', 'KAYAYA', 'KANDA', 'Marie', NULL, '0998282195', 'F', '1986-10-04', 'Kananga', '3, TSHIBUYA, KAPANDA, KATOKA', 'Marié', 'Olivier KANKONDE', 'Carte d\'électeur', 'Kananga Ville', '2023-03-16', '3493417950', NULL, 'Marchandises de cuisine', 'Commerce', NULL, NULL, NULL, NULL, '25', 200000.00, NULL, NULL, '2026-03-16 07:26:51', '2026-03-16 07:26:51'),
('CL-EBENKGA-26-00263', 'ZON-EBENKGA-26-00004', 'MUADI', 'MASHALA', 'ANITA', NULL, '0971295577', 'F', '2010-02-22', 'MUENA DITU', 'N° 17 Av TSHIBAMBULA, Q/ MPOKOLO, C/ KATOKA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-01-26', '349765', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:29:08', '2026-03-16 07:29:08'),
('CL-EBENKGA-26-00264', 'ZON-EBENKGA-26-00007', 'MBUYI', 'KABUAYI', 'ANNY', NULL, NULL, 'F', '1983-05-12', 'TSHIKAPA', 'AV DU CENTRE N°38 Q/ TSHIBANDABANDA', 'Marié', NULL, 'Carte d\'électeur', 'KANANG', '2023-02-12', '34945575197', NULL, NULL, 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:29:19', '2026-03-16 07:29:19'),
('CL-EBENKGA-26-00265', 'ZON-EBENKGA-26-00010', 'NTUMBA', 'NGALAMULUME', 'AGNÈS', NULL, '0970929549', 'F', '1783-09-03', 'KANANGA', 'N°10, KIMKOLE, PLATEAU, KANANGA', 'Marié', 'NTAMBUE LUABA FRANÇOIS', 'Carte d\'électeur', 'KANANGA', '2023-03-18', '34929780542', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:31:35', '2026-03-16 07:31:35'),
('CL-EBENKGA-26-00266', 'ZON-EBENKGA-26-00008', 'NTUMBA', 'KASONGO', 'Marthe', NULL, '0973141100', 'F', '1966-06-02', 'Kananga', '46, KALONDA, KAMUPONGO, NDESHA', 'Marié', 'NTUMBA Hubert', 'Carte d\'électeur', 'Kananga Ville', '2026-02-24', '34945178583', NULL, 'Marchandises de cuisine', 'Commerce', NULL, NULL, NULL, NULL, '15', 350000.00, NULL, NULL, '2026-03-16 07:33:01', '2026-03-16 07:33:01'),
('CL-EBENKGA-26-00267', 'ZON-EBENKGA-26-00004', 'KAYAYA', 'TSHISUNGU', 'CLÉMENCE', NULL, '0994567657', 'F', '1996-06-14', 'KANANGA', 'N° 34 Av DU MANGUIER, Q/ KAMAYI, C/ KANANGA', 'Marié', 'FRANÇOIS TSHISHIKU', 'Carte d\'électeur', 'KANANGA', '2023-03-11', '349243', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:34:54', '2026-03-16 07:34:54'),
('CL-EBENKGA-26-00268', 'ZON-EBENKGA-26-00003', 'BIKALE', 'NKASHAMA', 'EVARISTE', NULL, '0962686869', 'M', '1996-07-06', 'Kananga', 'Av Mikalayi Q/ lubuwa C/ Ndesha', 'Marié', 'Célestine kudiakuimpe', 'Carte nationale d\'identité', 'Kananga', '2023-03-10', '34', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '6', NULL, NULL, NULL, '2026-03-16 07:35:41', '2026-03-16 07:35:41'),
('CL-EBENKGA-26-00269', 'ZON-EBENKGA-26-00007', 'MBOMBO', 'KABASELE', 'JOSÉE', NULL, '0979873267', 'F', '1978-09-10', 'KANANGA', 'Av DE CADET Q/ KAMAYI C/ KANANGA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-24', '34930978585', NULL, NULL, 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:36:31', '2026-03-16 07:36:31'),
('CL-EBENKGA-26-00270', 'ZON-EBENKGA-26-00008', 'TSHILENGA', 'MUTOMBO', 'Jean Pierre', NULL, '0996617014', 'M', '1990-06-18', 'Mbuji mayi', '08, TSHIKELE, MPOKOLO, KATOKA', 'Marié', 'MUTUALAYI Micheline', 'Carte d\'électeur', 'Kananga Ville', '2023-03-16', '3493', NULL, 'Moto', 'Service', NULL, NULL, NULL, NULL, '12', 250000.00, NULL, NULL, '2026-03-16 07:39:28', '2026-03-16 07:39:28'),
('CL-EBENKGA-26-00271', 'ZON-EBENKGA-26-00006', 'Ntambue', 'Mutekemena', 'Roger', NULL, '0970170290', 'M', '1995-11-17', 'Kananga', 'De l\'école 25,tukombe,katoka', 'Marié', 'Bintu mado', 'Carte d\'électeur', 'Kananga', '2023-03-01', '34944973632', NULL, 'Vente Boissons', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:40:10', '2026-03-16 07:40:10'),
('CL-EBENKGA-26-00272', 'ZON-EBENKGA-26-00010', 'BIPENDU', 'MUKENGE', 'MARIE', NULL, NULL, 'F', '1976-05-15', 'KANANGA', 'Avenue', 'Marié', 'LUBOYA', 'Carte d\'électeur', 'KANANGA', '2023-03-08', '34929795179', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:40:28', '2026-03-16 07:40:28'),
('CL-EBENKGA-26-00273', 'ZON-EBENKGA-26-00007', 'MUSUAMBA', 'BADINENGANYI', 'ESTHER', NULL, '0997937463', 'F', '1965-10-28', 'KANANGA', 'AV TSHIKAPA N°8 Q/ TSHIBANDABANDA C/ NDESHA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2023-01-29', '34945769373', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:42:59', '2026-03-16 07:42:59'),
('CL-EBENKGA-26-00274', 'ZON-EBENKGA-26-00006', 'Ngalula', 'Kasenda', 'Marie Louise', NULL, '0828195731', 'F', '1980-03-15', 'Mutoto', 'Révolution, mpokolo, katoka', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-16', '50', NULL, 'Vente Boissons', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:48:12', '2026-03-16 07:48:12'),
('CL-EBENKGA-26-00275', 'ZON-EBENKGA-26-00006', 'Ngalula', 'Kabadi', 'Berthe', NULL, '0973067186', 'F', '1989-01-16', 'Kananga', 'Likasi ,malandji, kananga', 'Marié', 'Dintshiantshia', 'Carte d\'électeur', 'Kananga', '2023-03-16', '53', NULL, 'Vente Boissons', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:51:14', '2026-03-16 07:51:14'),
('CL-EBENKGA-26-00276', 'ZON-EBENKGA-26-00008', 'KANKU', 'TSHIBUMBU', 'Laurette', NULL, '0994563709', 'F', '1987-06-18', 'Kananga', '08, Marché, KELE-KELE, KATOKA', 'Marié', 'Fidèle MUELA', 'Carte d\'électeur', 'Kananga Ville', '2023-03-16', '3492', NULL, 'Restaurant', 'Service', NULL, NULL, NULL, NULL, '12', 250000.00, NULL, NULL, '2026-03-16 07:51:34', '2026-03-16 07:51:34'),
('CL-EBENKGA-26-00277', 'ZON-EBENKGA-26-00010', 'BENYI', 'BULOBO', 'MARIE', NULL, '0900185482', 'F', '2000-05-05', 'DIBOKO', 'DU CANAL TSHIBANDA, NDESHA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-04-05', '34920183799', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:52:30', '2026-03-16 07:52:30'),
('CL-EBENKGA-26-00278', 'ZON-EBENKGA-26-00006', 'Ongondjo', 'Ongondjo', 'Damien', NULL, '0991750342', 'M', '1979-11-10', 'Kin', 'Likasi,snel', 'Marié', 'Masanka', 'Carte d\'électeur', 'Kananga', '2023-03-16', '23', NULL, 'Agence', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:54:35', '2026-03-16 07:54:35'),
('CL-EBENKGA-26-00279', 'ZON-EBENKGA-26-00006', 'Biansambu', 'Nsala', 'Marie José', NULL, '0822564485', 'F', '1967-02-02', 'Kananga', 'Route kanyuka,nganza , kananga', 'Veuf', NULL, 'Carte d\'électeur', 'Kananga', '2023-02-26', '34952189981', NULL, 'Vente Boissons', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:58:43', '2026-03-16 07:58:43'),
('CL-EBENKGA-26-00280', 'ZON-EBENKGA-26-00010', 'TSHIOTA', 'KUINSHIDI', 'MARIE', NULL, '0973569921', 'F', '1986-02-24', 'KANANGA', 'N°20, BUKAMA, LUBUWA, NDESHA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-10', '34942974434', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 07:59:19', '2026-03-16 07:59:19'),
('CL-EBENKGA-26-00281', 'ZON-EBENKGA-26-00006', 'Milolo', 'Bidilukinu', 'Thérèse', NULL, '0990664940', 'F', '1964-11-24', 'Kananga', 'Tshibata, tshinsambi, kananga', 'Veuf', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-12', '34921580341', NULL, 'Vente Boissons', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 08:02:29', '2026-03-16 08:02:29'),
('CL-EBENKGA-26-00282', 'ZON-EBENKGA-26-00005', 'MADIYA', 'KATSHUNGA', 'LYDYE', NULL, '0814308403', 'F', '1984-04-02', 'Lubumbashi', '13 avenue du manguier, commune de kananga', 'Marié', 'Mukaya papy', 'Carte nationale d\'identité', 'Kananga', '2023-03-09', '34926179875', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 08:04:41', '2026-03-16 08:04:41'),
('CL-EBENKGA-26-00283', 'ZON-EBENKGA-26-00008', 'KUMONANGANA', 'MULUMBA', 'Dieudonné', NULL, '0991921359', 'M', '1965-01-01', 'Kananga', '33, KISANGANI, KELE-KELE, KATOKA', 'Marié', 'TSHIBUABUA Berthe', 'Carte d\'électeur', 'Kananga Ville', '2023-03-16', '34935', NULL, 'Moulin', 'Service', NULL, NULL, NULL, NULL, '25', 25000000.00, NULL, NULL, '2026-03-16 08:04:46', '2026-03-16 08:04:46'),
('CL-EBENKGA-26-00284', 'ZON-EBENKGA-26-00006', 'Kaboko', 'Nkongolo', 'Natalie', NULL, '0823501778', 'F', '1994-05-27', 'Kananga', 'Du canal 36, malandji kananga', 'Marié', 'Gédéon ntumba', 'Carte d\'électeur', 'Kinshasa', '2026-04-29', '30065723486', NULL, 'Vente sacs', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 08:10:29', '2026-03-16 08:10:29'),
('CL-EBENKGA-26-00285', 'ZON-EBENKGA-26-00003', 'KISIMBA', 'KISIMBA', 'PIERRE', NULL, '0990795428', 'M', '1990-10-05', 'Kananga', 'Av Ditekemena Q/ Mukole C/ katoka n° 14', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-12', '34922769934', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '15', NULL, NULL, NULL, '2026-03-16 08:12:22', '2026-03-16 08:12:22'),
('CL-EBENKGA-26-00286', 'ZON-EBENKGA-26-00008', 'MUSHIYA', 'BUABU', 'Hélène', NULL, '0997233727', 'F', '1990-03-15', 'LUBAMI', '65, KINSHASA, KELE-KELE, KATOKA', 'Marié', 'TSHINGAMBU ELIE', 'Carte d\'électeur', 'Kananga Ville', '2023-03-16', '3509', NULL, 'Clous', 'Commerce', NULL, NULL, NULL, NULL, '20', 200000.00, NULL, NULL, '2026-03-16 08:18:41', '2026-03-16 08:18:41'),
('CL-EBENKGA-26-00287', 'ZON-EBENKGA-26-00003', 'NGALULA', 'KABASELE', 'ANGEL', NULL, '0986836125', 'F', '1991-05-25', 'Kananga', 'Av indépendance Q/ itabayi C/ lukonga n° 47', 'Marié', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-03-12', '34941591826', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '10', NULL, NULL, NULL, '2026-03-16 08:20:57', '2026-03-16 08:20:57'),
('CL-EBENKGA-26-00288', 'ZON-EBENKGA-26-00006', 'Bipendu', 'Mukuna', 'Chantal', NULL, '0973384785', 'F', '1983-05-12', 'Kananga', 'Kafua20, tshinsambi, kananga', 'Marié', 'Anaclet madilz', 'Carte d\'électeur', 'Kananga', '2023-02-02', '34920569641', NULL, 'Vente babouches', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 08:27:31', '2026-03-16 08:27:31'),
('CL-EBENKGA-26-00289', 'ZON-EBENKGA-26-00003', 'KABANTU', 'LUSE', 'DOMINIQUE', NULL, '0991327563', 'M', '1999-04-09', 'DEMBA', 'Av Route ilebo Q/ Lumumba C/ Lukonga', 'Marié', 'Claudine Nkuna', 'Carte nationale d\'identité', 'Kananga', '2023-02-18', '34943371843', NULL, NULL, 'Service', NULL, NULL, NULL, NULL, '2', NULL, NULL, NULL, '2026-03-16 08:28:28', '2026-03-16 08:28:28'),
('CL-EBENKGA-26-00290', 'ZON-EBENKGA-26-00006', 'Mbiye', 'Ngindu', 'Annie', NULL, '0977761814', 'F', '1956-05-05', 'Kananga', 'Unaco21, kamayi, kananga', 'Veuf', NULL, 'Carte d\'électeur', 'Kananga', '2023-01-27', '34922167508', NULL, 'Vente Boissons', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 08:30:29', '2026-03-16 08:30:29'),
('CL-EBENKGA-26-00291', 'ZON-EBENKGA-26-00006', 'Wema', 'Dimandja', 'André', NULL, '0999116859', 'M', '1970-07-26', 'Lodja', 'Banzangubgu32, kelekele,katoy', 'Marié', 'Engudi Juliette', 'Carte d\'électeur', 'Kananga', '2023-02-05', '34929772081', NULL, 'Vente d\'habits', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 08:38:39', '2026-03-16 08:38:39'),
('CL-EBENKGA-26-00292', 'ZON-EBENKGA-26-00010', 'NZAMBI', 'NTAMBUE', 'ELIZABETH', NULL, '0999074565', 'F', '1972-03-06', 'NDEKESHA', 'N° 1472, LUKENGU, MPOKOLO KATOKA', 'Marié', 'Muambayi', 'Carte d\'électeur', 'KANANGA', '2023-02-19', '34930776262', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 11:55:51', '2026-03-16 11:55:51'),
('CL-EBENKGA-26-00293', 'ZON-EBENKGA-26-00006', 'Mputu', 'Kande', 'Charlotte', NULL, '0972421899', 'F', '1980-08-05', 'Kananga', 'Kamayi, kananga', 'Marié', 'Ngoya ngandu Simon', 'Carte d\'électeur', 'Kananga', '2023-03-24', '34933578477', NULL, 'Vente sacs', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 12:43:23', '2026-03-16 12:43:23'),
('CL-EBENKGA-26-00294', 'ZON-EBENKGA-26-00006', 'Kalambayi', 'Tshiamala', 'Daddy', NULL, '0999313319', 'M', '1979-05-25', 'Kolwezi', 'Bakua bisama 23,plateau kananga', 'Marié', 'Mianda sarha', 'Carte d\'électeur', 'Kananga', '2023-03-03', '34928369476', NULL, 'Divers', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 12:47:50', '2026-03-16 12:47:50'),
('CL-EBENKGA-26-00295', 'ZON-EBENKGA-26-00006', 'Tshipamba', 'Tshibamba', 'Gabriel', NULL, '0986864519', 'M', '1997-11-02', 'Kananga', 'Ndesha09, kapanda, katoka', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-16', '300', NULL, 'Atelier', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:06:42', '2026-03-16 13:06:42'),
('CL-EBENKGA-26-00296', 'ZON-EBENKGA-26-00006', 'Mushinde', 'Luangi', 'Verro', NULL, '0973697903', 'F', '1982-11-17', 'Mbujimayi', 'Dispensaire 15, tshinsambi, kananga', 'Marié', 'Mukulu honore', 'Carte d\'électeur', 'Kananga', '2023-03-16', '302', NULL, 'Atelier', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:25:01', '2026-03-16 13:25:01'),
('CL-EBENKGA-26-00297', 'ZON-EBENKGA-26-00006', 'Mbombo', 'Mulengele', 'Adolphine', NULL, '0992930805', 'F', '1986-12-25', 'Kananga', 'Mutoyi, malandji, kananga', 'Marié', 'Dieudonné mukendi', 'Carte d\'électeur', 'Kananga', '2026-03-12', '400', NULL, 'Vente sacs', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:27:51', '2026-03-16 13:27:51'),
('CL-EBENKGA-26-00298', 'ZON-EBENKGA-26-00011', 'BANKANGA', 'MUTEBA', 'CHARLOTTE', NULL, NULL, 'F', '2006-04-02', 'KANANGA', '24 TSHIDILU, MPOKOLO, KATOKA', 'Célibataire', NULL, 'Carte nationale d\'identité', 'KANANGA', '2023-03-02', '35', NULL, 'DIVERS', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:32:00', '2026-03-16 13:32:00'),
('CL-EBENKGA-26-00299', 'ZON-EBENKGA-26-00006', 'Ngalula', 'Mbayi', 'Elysée', NULL, '0974821343', 'F', '2000-03-27', 'Kananga', 'Du rail,kabanza, kananga', 'Marié', 'Augustin tshikele', 'Carte d\'électeur', 'Kananga', '2023-03-13', '301', NULL, 'Divers', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:34:18', '2026-03-16 13:34:18'),
('CL-EBENKGA-26-00300', 'ZON-EBENKGA-26-00011', 'MUAMBA', 'NKONGOLO', 'GEDEON', NULL, '0981690515', 'M', '2003-03-25', 'KANANGA', '10 KANDAKANDA, NDESHA, NDESHA', 'Célibataire', NULL, 'Carte nationale d\'identité', 'KANANGA', '2023-03-10', '34929390004', NULL, 'VENTE D\'HABIT', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:38:59', '2026-03-16 13:38:59'),
('CL-EBENKGA-26-00301', 'ZON-EBENKGA-26-00006', 'Kananga', 'Tshishimbi', 'Naomie grâce', NULL, '0978043181', 'F', '1995-03-16', 'Kinshasa', 'Lubondayi , plateau, kananga', 'Marié', 'Kalala karlos', 'Carte d\'électeur', 'Kananga', '2023-03-27', '35144982646', NULL, 'Vente babouches', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:41:00', '2026-03-16 13:41:00'),
('CL-EBENKGA-26-00302', 'ZON-EBENKGA-26-00010', 'MASHI MIMPE', 'KABEYA', 'ROSE', NULL, '0978347246', 'F', '1984-12-10', 'KANANGA', 'N°31, KAZUMBA, KAMILABI, NDESHA', 'Marié', 'BAMUNANGA ANDRE', 'Carte d\'électeur', 'KANANGA', '2023-02-01', '34934970393', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:41:25', '2026-03-16 13:41:25'),
('CL-EBENKGA-26-00303', 'ZON-EBENKGA-26-00011', 'BENABIABU', 'BASAKAYI', 'GRACE', NULL, '0976732014', 'F', '1990-02-25', 'KANANGA', '10, DU PRODUIT, TSHINSAMBI, KANANGA', 'Marié', 'KANKONDE ANDRE', 'Carte nationale d\'identité', 'KANANGA', '2023-02-25', '34934984083', NULL, 'VENTE D\'HABIT', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:43:39', '2026-03-16 13:43:39'),
('CL-EBENKGA-26-00304', 'ZON-EBENKGA-26-00010', 'TUSEKU', 'TSHITALA', 'RUTH', NULL, '0992139267', 'F', '1999-05-18', 'TSHIKAPA', 'BAKATUSHIPA, KELE-KELE, KATOKA', 'Marié', 'LUKUSA ZACHARIE', 'Carte d\'électeur', 'KANANGA', '2023-02-28', '34931187415', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:45:56', '2026-03-16 13:45:56'),
('CL-EBENKGA-26-00305', 'ZON-EBENKGA-26-00011', 'TSHITUKA', 'MUKENDI', 'ROSE', NULL, '0823881545', 'F', '1979-11-10', 'KANANGA', '4, CIRCULAIRE, KAMUPONGO, NDESHA', 'Marié', 'KABAMBA ILUNGA', 'Carte nationale d\'identité', 'KANANGA', '2023-03-10', '34944984182', NULL, 'TAILLEUR', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:47:25', '2026-03-16 13:47:25'),
('CL-EBENKGA-26-00306', 'ZON-EBENKGA-26-00006', 'Lubula', 'Mukenge', 'Jonathan', NULL, '0995597256', 'M', '2001-04-15', 'Kinshasa', 'Mukalamushi5, plateau, kananga', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-05', '34925384487', NULL, 'Minishop', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:48:19', '2026-03-16 13:48:19'),
('CL-EBENKGA-26-00307', 'ZON-EBENKGA-26-00010', 'NTUMBA', 'KABENGELE', 'SARAH', NULL, '0983680718', 'F', '1977-10-10', 'KANANGA', 'N°10, DISPENSER, TSHISAMBI, KANANGA', 'Marié', 'LUKUSA ZACHARIE', 'Carte d\'électeur', 'KANANGA', '2023-02-21', '34930977823', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:50:46', '2026-03-16 13:50:46'),
('CL-EBENKGA-26-00308', 'ZON-EBENKGA-26-00011', 'KASONGA', 'KASONGA', 'CASAMIR', NULL, '0827014045', 'M', '1982-01-12', 'KANANGA', '20, MALANDJI, MALANDJI, KANANGA', 'Marié', 'MADO NYEMBA', 'Carte nationale d\'identité', 'KANANGA', '2023-01-25', '34922966276', NULL, 'VENTE D\'HABIT', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:54:13', '2026-03-16 13:54:13'),
('CL-EBENKGA-26-00309', 'ZON-EBENKGA-26-00004', 'KUPUWA', 'KALOMBO', 'BIJOUX', NULL, '0991774493', 'F', '1967-12-21', 'KANANGA', 'N° 1552, Av Walikale, Q/ Malanji C/ KANANGA', 'Célibataire', NULL, 'Carte d\'électeur', 'Demba', '2023-03-05', '35144983515', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:55:18', '2026-03-16 13:55:18'),
('CL-EBENKGA-26-00310', 'ZON-EBENKGA-26-00010', 'MUJINGA', 'KAMPULA', 'THÉRÈSE', NULL, '0810125573', 'F', '1976-03-05', 'KAMINA', 'N°28, DE L\'ÉGLISE, MOBUTU, KANANGA', 'Marié', 'Antoine TAMINA', 'Carte d\'électeur', 'KANANGA', '2023-02-18', '34927175476', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:57:45', '2026-03-16 13:57:45'),
('CL-EBENKGA-26-00311', 'ZON-EBENKGA-26-00004', 'LUSAMBA', 'TSHIPAMBA', 'MADELINE', NULL, '0990782101', 'F', '1980-01-06', 'KANANGA', 'N°8 AV DECADET Q/ KAMAYI, C/ KANANGA', 'Marié', 'YALINA', 'Carte d\'électeur', 'KANANGA', '2026-03-13', '349279', 'clients/1773673195_1000017036.jpeg', 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 13:59:55', '2026-03-16 13:59:55'),
('CL-EBENKGA-26-00312', 'ZON-EBENKGA-26-00004', 'MISENGA', 'NKUMBI', 'MIMIE', NULL, '0991398860', 'F', '1976-10-31', 'KOLWEZI', 'N° 05 AV DU CANAL, Q/ MALANJI, C/ KANANGA', 'Marié', 'NZENGU JEAN PIERRE', 'Carte d\'électeur', 'KANANGA', '2023-03-10', '23170799725', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:04:31', '2026-03-16 14:04:31'),
('CL-EBENKGA-26-00313', 'ZON-EBENKGA-26-00006', 'Ntumba', 'Mutombo', 'Joséphine', NULL, '0993986168', 'F', '1978-05-05', 'Kananga', 'Produit35, kamulumbe, kananga', 'Marié', 'Mukutulayi casmire', 'Carte d\'électeur', 'Kananga', '2023-03-05', '34944793079', NULL, 'Vente begnet', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:04:59', '2026-03-16 14:04:59'),
('CL-EBENKGA-26-00314', 'ZON-EBENKGA-26-00010', 'MUKEBA', 'NTUMBA', 'BRUNO', NULL, '0990489811', 'M', '2000-08-08', 'KANANGA', 'N°18,ILUNGA DIBUE, TSHISAMBI, KANANGA', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-08', '34928580832', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:07:26', '2026-03-16 14:07:26'),
('CL-EBENKGA-26-00315', 'ZON-EBENKGA-26-00004', 'MUJINGA', 'BETU', 'ALPHONSINE', NULL, '0997618671', 'F', '1977-09-19', 'KANANGA', 'N° 07 AV  KANKINDA, Q/ KAMAYI, C/ KANANGA', 'Marié', 'Paul MULAMBA', 'Carte d\'électeur', 'KANANGA', '2023-03-31', '34922179596', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:08:46', '2026-03-16 14:08:46'),
('CL-EBENKGA-26-00316', 'ZON-EBENKGA-26-00006', 'Kapinga', 'Kamande', 'Madeleine', NULL, '0995083058', 'F', '1978-05-21', 'Kananga', 'Kasaï, tshinsambi, kananga', 'Marié', 'Muanza mukadi Robert', 'Carte d\'électeur', 'Kananga', '2023-03-15', '34931774449', NULL, 'Élevage', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:08:54', '2026-03-16 14:08:54'),
('CL-EBENKGA-26-00317', 'ZON-EBENKGA-26-00010', 'KAPIAMBA', 'ILUNGA', 'FRANÇOIS', NULL, '0992490022', 'M', '1971-07-30', 'TSHIDIMBA', 'N°7/LUSE/TSHISAMBI/KANANGA', 'Marié', 'TUPEMUNYI MARIE', 'Carte d\'électeur', 'KANANGA', '2023-02-22', '34918392887', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:12:04', '2026-03-16 14:12:04'),
('CL-EBENKGA-26-00318', 'ZON-EBENKGA-26-00004', 'OMOYI', 'NSENGA', 'HENRIETTE', NULL, '0998675432', 'F', '1982-03-03', 'KANANGA', 'N°13, Av Ditalala Q/ Kamayi C/ Kananga', 'Marié', 'MUYAYA GRÉGOIRE', 'Carte d\'électeur', 'KANANGA', '2023-03-03', '34925883404', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:12:07', '2026-03-16 14:12:07'),
('CL-EBENKGA-26-00319', 'ZON-EBENKGA-26-00006', 'Batuamba', 'Nkongolo', 'Esther', NULL, '0973793901', 'F', '1997-06-17', 'Kananga', 'Likasi10,malandji, kananga', 'Marié', 'Touby', 'Carte d\'électeur', 'Kananga', '2023-03-16', '34928576598', NULL, 'Ventes des sacs', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:13:40', '2026-03-16 14:13:40'),
('CL-EBENKGA-26-00320', 'ZON-EBENKGA-26-00004', 'KAYAYA', 'TSHIMANGA', 'ANNY', NULL, '0970612182', 'F', '1981-07-17', 'KANANGA', 'N° 23 Av Likasi Q/Malanji, C/ KANANGA', 'Marié', 'MBUYI', 'Carte d\'électeur', 'KANANGA', '2023-03-06', '34947791475', NULL, 'VENTE des poissons', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:16:09', '2026-03-16 14:16:09'),
('CL-EBENKGA-26-00321', 'ZON-EBENKGA-26-00011', 'KALULAMBI', 'KALUKUNDU', 'ESTHER', NULL, '0988503569', 'F', '2000-03-16', 'KANANGA', '10,TSHINSENSA, KAMULUMBA, KANANGA', 'Divorcé', NULL, 'Carte nationale d\'identité', 'KANANGA', '2023-03-16', '36', NULL, 'VENTE D\'HABIT', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:16:36', '2026-03-16 14:16:36'),
('CL-EBENKGA-26-00322', 'ZON-EBENKGA-26-00006', 'Mukuna', 'Kapuku', 'Elie', NULL, 'Kananga', 'M', '2002-04-04', '35147186038', 'Lomela,tshibandabanda, ndesha', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-26', '35147186038', NULL, 'Divers', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:17:37', '2026-03-16 14:17:37'),
('CL-EBENKGA-26-00323', 'ZON-EBENKGA-26-00007', 'MPINDA', 'NGALAMULUME', 'JACQUES', NULL, '0977553127', 'M', '1989-12-12', 'KANANGA', 'AV MAKOLO N°52 Q/ MPOKOLO C/ KATOKA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-12', '34944777612', NULL, 'PHARMACIE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:20:25', '2026-03-16 14:20:25'),
('CL-EBENKGA-26-00324', 'ZON-EBENKGA-26-00011', 'MPUTU', 'MANGENDA', 'MADO', NULL, NULL, 'F', '1979-04-15', 'KANANGA', '12, MUKENGESHAYI, MPOKOLO, KATOKA', 'Marié', 'JEAN', 'Carte nationale d\'identité', 'KANANGA', '2023-07-02', '34932571018', NULL, 'VENTE DES POULES', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:20:53', '2026-03-16 14:20:53'),
('CL-EBENKGA-26-00325', 'ZON-EBENKGA-26-00004', 'MALUNDU', 'BAKALOWA', 'JEAN CLAUDE', NULL, '0972104140', 'M', '1980-02-20', 'KANANGA', 'N° 09 Av KALUSENGA, Q/ KAMAYI, C/ KANANGA', 'Marié', 'META MULUMBA', 'Carte d\'électeur', 'KANANGA', '2023-03-14', '3498764', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:21:19', '2026-03-16 14:21:19'),
('CL-EBENKGA-26-00326', 'ZON-EBENKGA-26-00011', 'NTUMBA', 'MWANZA', 'MARIE', NULL, '0970367801', 'F', '1973-11-22', 'KANANGA', '14,CIMETIERE, TSHINSAMBI, KANANGA', 'Marié', 'BERNARD MVITA', 'Carte nationale d\'identité', 'KANANGA', '2023-02-12', '34931174582', NULL, 'DIVERS', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:24:53', '2026-03-16 14:24:53'),
('CL-EBENKGA-26-00327', 'ZON-EBENKGA-26-00004', 'ILUNGA', 'BRTU', 'INNOCENT', NULL, '0982377222', 'M', '1995-04-08', 'KANANGA', 'N° 28 AV MUSUASUA, Q/ KAMAYI, C/ KANANGA', 'Marié', 'TSHIBUABUA FELLY', 'Carte d\'électeur', 'KANANGA', '2023-03-02', '349567', NULL, 'VENTE D\'EPICES', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:24:57', '2026-03-16 14:24:57'),
('CL-EBENKGA-26-00328', 'ZON-EBENKGA-26-00010', 'MALU', 'TSHIMBILA', 'MERVEILLE', NULL, '0982281595', 'F', '2000-09-10', 'KANANGA', 'N°2/TSHISAMBI/BIANKY/NDESHA', 'Marié', 'DOME JORDAN', 'Carte d\'électeur', 'KANANGA', '2023-02-21', '34943376765', NULL, 'ÉPICE', 'Commerce', NULL, NULL, NULL, NULL, '2', NULL, NULL, NULL, '2026-03-16 14:25:17', '2026-03-16 14:25:17'),
('CL-EBENKGA-26-00329', 'ZON-EBENKGA-26-00011', 'TSHITUKA', 'TSHIKUNGA', 'JOSEPH', NULL, '0828003585', 'M', '1987-01-20', 'KANANGA', '2, DISPENSAIRE, MPOKOLO, KATOKA', 'Marié', 'THERESE MILEMBU', 'Carte nationale d\'identité', 'KANANGA', '2023-02-26', '35086982829', NULL, 'VENTE DE CARBURANT', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:28:51', '2026-03-16 14:28:51'),
('CL-EBENKGA-26-00330', 'ZON-EBENKGA-26-00004', 'MULUMBA', 'KAPUMBA', 'DONATIEN', NULL, '0972056853', 'M', '1982-06-06', 'LIKASI', 'N° 23, Av Kamukulu, Q/ Muimba, C/ NGANZA', 'Marié', 'Kapinga Elysée', 'Carte d\'électeur', 'KANANGA', '2023-02-16', '34956541', NULL, 'Électricité', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:28:59', '2026-03-16 14:28:59'),
('CL-EBENKGA-26-00331', 'ZON-EBENKGA-26-00007', 'MBOMBO', 'TSHIOWA', 'ROSALIE', NULL, '0977843177', 'F', '1978-08-08', 'KANANGA', 'AV TSHISUPANTAMBUE N° Q/ MPOKOLO C/ KATOKA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2023-03-02', '34933985246', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:30:00', '2026-03-16 14:30:00'),
('CL-EBENKGA-26-00332', 'ZON-EBENKGA-26-00006', 'Masheke', 'Ngwama', 'Jordan', NULL, '0995043502', 'M', '1997-03-16', 'Bangamakondo', 'Ditalala 27, kelekele, katoka', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-22', '34934392013', NULL, 'Vente moto', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:30:25', '2026-03-16 14:30:25'),
('CL-EBENKGA-26-00333', 'ZON-EBENKGA-26-00010', 'BILONDA', 'KAYEMBE', 'JEANNETTE', NULL, '0993911451', 'F', '1986-10-13', 'TSHIMBULU', 'N°13/KINDU/ SELE/ NGAZA', 'Marié', 'NDAYE LIÉVIN', 'Carte d\'électeur', 'KANANGA', '2023-03-08', '34948176197', NULL, 'Le Poisson', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:31:01', '2026-03-16 14:31:01'),
('CL-EBENKGA-26-00334', 'ZON-EBENKGA-26-00011', 'MISENGA', 'KANKONDE', 'MICHELINE', NULL, '0992751873', 'F', '1976-08-27', 'KANANGA', '4, KAZADI, MPOKOLO, KATOKA', 'Marié', 'MUBENGA THEODORE', 'Carte nationale d\'identité', 'KANANGA', '2023-03-17', '34933192672', NULL, 'VENTE D\'HABIT', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:32:17', '2026-03-16 14:32:17'),
('CL-EBENKGA-26-00335', 'ZON-EBENKGA-26-00004', 'BANTU', 'NEWUSHUYE TSHINYI', 'JACKY', NULL, '0995283618', 'F', '1985-03-02', 'KANANGA', 'N° 45 Av Bakole Q/ Plateau C/ KANANGA', 'Marié', 'NTUMBA DAVID', 'Carte d\'électeur', 'KANANGA', '2023-02-01', '34956421', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:32:53', '2026-03-16 14:32:53'),
('CL-EBENKGA-26-00336', 'ZON-EBENKGA-26-00004', 'NDAMBU', 'MUTELA', 'VÉRONIQUE', NULL, '0995568128', 'F', '1987-07-23', 'TSHIKAPA', 'N° 19, AV Mayindombe, Q/ NGANZA NORD, C/ NGANZA', 'Marié', 'FISTON TSHILOBA', 'Carte d\'électeur', 'KANANGA', '2023-03-09', '35690724', NULL, 'VENTE DES POISSONS', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:36:08', '2026-03-16 14:36:08'),
('CL-EBENKGA-26-00337', 'ZON-EBENKGA-26-00006', 'Makinda', 'Makuma', 'Timothée', NULL, '0975582881', 'M', '1981-03-07', 'Banga', 'Likasi, malandji, kananga', 'Marié', 'Deborah', 'Carte d\'électeur', 'Kinshasa', '2023-03-16', '800', NULL, 'Quinquelerie', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:36:33', '2026-03-16 14:36:33'),
('CL-EBENKGA-26-00338', 'ZON-EBENKGA-26-00010', 'NGALULA', 'KADIMA', 'Sylvie', NULL, '0990405967', 'F', '1976-08-04', 'LIKASI', 'N°/KIVU/ KELE-KELE/ KATOKA', 'Marié', 'KATENDE BIDILUKINU', 'Carte d\'électeur', 'KANANGA', '2023-02-08', '34934572923', NULL, 'Le poisson', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:36:48', '2026-03-16 14:36:48'),
('CL-EBENKGA-26-00339', 'ZON-EBENKGA-26-00004', 'MASHALA', 'MFUAMBA', 'CHARLY', NULL, '0986754234', 'M', '1983-03-14', 'Luiza', 'N° 23 AV KATOKA II, Q/ MPOKOLO, C/ KATOKA', 'Marié', 'LYLY MANYONGA', 'Carte d\'électeur', 'KANANGA', '2023-03-21', '345862', NULL, 'VENTE DES DIVERS', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:39:15', '2026-03-16 14:39:15'),
('CL-EBENKGA-26-00340', 'ZON-EBENKGA-26-00006', 'Mputu', 'Tshipamba', 'Marie', NULL, '0993712473', 'F', '1995-02-22', 'Kananga', 'Mupoyi36 malandji', 'Marié', 'Nkongolo Moïse', 'Carte d\'électeur', 'Kananga', '2023-03-16', '200', NULL, 'Divers', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:41:24', '2026-03-16 14:41:24'),
('CL-EBENKGA-26-00341', 'ZON-EBENKGA-26-00004', 'TSHIMANGA', 'MUAMBA', 'Georges', NULL, '0962254818', 'M', '1990-05-15', 'KANANGA', 'N° 19 AV MUANZA, Q/ KAMAYI, C/ KANANGA', 'Marié', 'MUENYI ANGEL', 'Carte d\'électeur', 'KANANGA', '2023-03-10', '34562348', NULL, 'MEUNIER', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:42:00', '2026-03-16 14:42:00'),
('CL-EBENKGA-26-00342', 'ZON-EBENKGA-26-00011', 'MBOMBO', 'TSHIEBUE', 'ALPHONSINE', NULL, '0987904597', 'F', '1986-06-11', 'KANANGA', '2, MPOKOLO, LUBUWA, NDESHA', 'Marié', 'MATONDO TSHIMANGA FILS', 'Carte nationale d\'identité', 'KANANGA', '2023-02-15', '34938177800', NULL, 'VENTE D\'HABIT', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:43:13', '2026-03-16 14:43:13'),
('CL-EBENKGA-26-00343', 'ZON-EBENKGA-26-00004', 'KASONGA', 'KABIPANGI', 'PAUL', NULL, '0994948824', 'M', '1965-02-10', 'TSHIKULA', 'N°03 AV, LUKENGU, Q/ NGANZA NORD, C/ NGANZA', 'Marié', 'MISENGA MAWEJA ANNY', 'Carte d\'électeur', 'KANANGA', '2023-03-01', '34956724', NULL, 'VENTE des poissons', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:46:17', '2026-03-16 14:46:17'),
('CL-EBENKGA-26-00344', 'ZON-EBENKGA-26-00011', 'MPUTU', 'MUAMBA', 'DAVID', NULL, '0976405613', 'M', '1995-12-12', 'KANANGA', '13, MUKENDI, KAPANDA, KATOKA', 'Marié', 'KAPINGA ESTHER', 'Carte nationale d\'identité', 'KANANGA', '2023-02-26', '34933579497', NULL, 'VENTE D\'HABIT', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:46:52', '2026-03-16 14:46:52'),
('CL-EBENKGA-26-00345', 'ZON-EBENKGA-26-00003', 'NGONDO', 'MUYA', 'THÉRÈSE', NULL, '0976968943', 'F', '1991-11-11', 'Lubumbashi', 'Av lulua Q/tshinsambi C/ kananga', 'Marié', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-09', '349433', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-16 14:48:37', '2026-03-16 14:48:37'),
('CL-EBENKGA-26-00346', 'ZON-EBENKGA-26-00011', 'MUKUNA', 'LUSHIKU', 'ROGER', NULL, '0975047374', 'M', '2000-09-25', 'KANANGA', '95, LULUA, TSHIBANDABANDA, NDESHA', 'Marié', 'NTAMBA NADINE', 'Carte nationale d\'identité', 'KANANGA', '2023-02-07', '34933969221', NULL, 'VENTE D\'HABIT', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:50:37', '2026-03-16 14:50:37'),
('CL-EBENKGA-26-00347', 'ZON-EBENKGA-26-00006', 'Mibanga', 'Mikobi', 'Jacqueline', NULL, '0971085062', 'F', '1984-12-14', 'Likasi', 'Malandji, kananga', 'Marié', 'Bope Albert', 'Carte d\'électeur', 'Kananga', '2023-03-12', '349571799027', NULL, 'Ventes des téléphones', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:51:25', '2026-03-16 14:51:25'),
('CL-EBENKGA-26-00348', 'ZON-EBENKGA-26-00003', 'KANANGA', 'BASUA', 'MARIE', NULL, NULL, 'F', '1983-11-13', 'Konyi', 'Q/kamupongo C/Ndesha n°14', 'Marié', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-05', '34944172165', NULL, 'Vente de savons', NULL, NULL, NULL, NULL, NULL, '6', NULL, NULL, NULL, '2026-03-16 14:55:18', '2026-03-16 14:55:18'),
('CL-EBENKGA-26-00349', 'ZON-EBENKGA-26-00006', 'Tshibola', 'Badimu', 'Floreine', NULL, '0980491353', 'F', '2004-12-31', 'Kananga', 'Kabeya 13, plateau, kananga', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-05', '34927984666', NULL, 'Étudiant', 'Autre', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:58:13', '2026-03-16 14:58:13'),
('CL-EBENKGA-26-00350', 'ZON-EBENKGA-26-00011', 'BIPENDU', 'NTUMBA', 'BIJOUX', NULL, '0999886958', 'F', '1989-08-05', 'KANANGA', '42, TULUMA, MPOKOLO, KATOKA', 'Marié', 'KABATANTSHI MABE', 'Carte nationale d\'identité', 'KANANGA', '2023-03-02', '34934587163', NULL, 'VENTE D\'HABIT', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 14:58:39', '2026-03-16 14:58:39'),
('CL-EBENKGA-26-00351', 'ZON-EBENKGA-26-00003', 'KAPAPILA', 'NSANGAMINA', 'ADOLPHE', NULL, '0997737991', 'M', '1963-06-17', 'Tshibala', 'Av du peuple,C/ kananga Q/ snel n°6', 'Marié', 'Alpho', 'Carte nationale d\'identité', 'Kananga', '2023-02-01', '34951571455', NULL, 'Pasteur', NULL, NULL, NULL, '15', NULL, NULL, NULL, NULL, NULL, '2026-03-16 15:03:41', '2026-03-16 15:03:41'),
('CL-EBENKGA-26-00352', 'ZON-EBENKGA-26-00003', 'TSHITENGA', 'TSHITENGA', 'RAPHE', NULL, '0982648242', 'M', '2003-11-05', 'Kananga', 'Av luanga lueta Q/ Mpokolo C/ katoka n° 2', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-10', '349437', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-16 15:08:53', '2026-03-16 15:08:53'),
('CL-EBENKGA-26-00353', 'ZON-EBENKGA-26-00003', 'KAYEMBE', 'KABASELE', 'TIMOTHÉE', NULL, '0994729052', 'M', '1970-04-02', 'Demba', 'Av lumubiste Q/ nganza nord C/ngaza', 'Marié', 'Vicky', 'Carte nationale d\'identité', 'Kananga', '2023-01-31', '34951570156', NULL, 'Pasteur', NULL, NULL, NULL, NULL, NULL, '10', NULL, NULL, NULL, '2026-03-16 15:13:45', '2026-03-16 15:13:45'),
('CL-EBENKGA-26-00354', 'ZON-EBENKGA-26-00006', 'Nsomba', 'Mutombo', 'Christine', NULL, '0976577050', 'F', '1960-04-23', 'Kananga', 'Route kanyuka 49, tshinsambi', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-16', '80', NULL, 'Ventes boissons', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 15:17:48', '2026-03-16 15:17:48'),
('CL-EBENKGA-26-00355', 'ZON-EBENKGA-26-00003', 'NTUMBA', 'MPUTU', 'ESPOIR', NULL, '0973389184', 'M', '1888-05-11', 'Bulungu', 'Av luanga lueta,Q/ Mpokolo C/ katoka n° 2', 'Marié', 'Rose NTUMBA', 'Carte nationale d\'identité', 'Kananga', '2023-02-10', '349478', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '18', NULL, NULL, NULL, '2026-03-16 15:18:20', '2026-03-16 15:18:20'),
('CL-EBENKGA-26-00356', 'ZON-EBENKGA-26-00006', 'Kawulu', 'Mutombo', 'Elly', NULL, '0998595490', 'F', '1968-12-25', 'Muenaditu', 'Du rail 01, plateau, kananga', 'Marié', 'Kabisampese', 'Carte d\'électeur', 'Kananga', '2023-03-12', '60', NULL, 'Vente des boissons', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 15:21:14', '2026-03-16 15:21:14'),
('CL-EBENKGA-26-00357', 'ZON-EBENKGA-26-00011', 'KAPINGA', 'MUBAKA', 'MARIE', NULL, '097572823', 'F', '1976-12-16', 'KANANGA', '38, MUKENGESHAYI,, MPOKOLO, KATOKA', 'Marié', 'KABIENA DIEUDONNE', 'Carte nationale d\'identité', 'KANANGA', '2023-02-21', '34930776789', NULL, 'VENTE D\'HABI', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 15:21:34', '2026-03-16 15:21:34'),
('CL-EBENKGA-26-00358', 'ZON-EBENKGA-26-00011', 'NTUMBA', 'MULAMBA', 'GIRESSE', NULL, '0981097467', 'M', '1991-02-15', 'LUBUMBASHI', '19, BIKUKU, PLATEAU, KANANGA', 'Marié', 'KADIEBUE AHISHA', 'Carte nationale d\'identité', 'KANANGA', '2023-02-26', '34945977207', NULL, 'VENTE DE BIJOUX', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 15:28:22', '2026-03-16 15:28:22'),
('CL-EBENKGA-26-00359', 'ZON-EBENKGA-26-00011', 'KAPUKU', 'BUKUSA', 'WILLIAM', NULL, '0977666170', 'M', '1997-06-06', 'MBUJI MAYI', '18, TSHISEKEDI, MPPKOLO,', 'Marié', 'KANKI ESTHER', 'Carte nationale d\'identité', 'KANANGA', '2023-02-10', '350791760376', NULL, 'VENTE DES HABITS', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 15:36:31', '2026-03-16 15:36:31'),
('CL-EBENKGA-26-00360', 'ZON-EBENKGA-26-00005', 'Tshibola', 'Tshibamba', 'Béatrice', NULL, '0974674530', 'F', '1979-08-08', 'Kananga', 'Decadet 14 Q/ KAMAYI C/ KANANGA', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-12', '3922172825', NULL, 'Commerce', 'Agriculture', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 15:40:13', '2026-03-16 15:40:13'),
('CL-EBENKGA-26-00361', 'ZON-EBENKGA-26-00011', 'MILOLO', 'KANKONDE', 'ANGELIQUE', NULL, '0996793157', 'F', '1971-07-07', 'KANANGA', '7, TSHISEKEDI, MPOKOLO, KATOKA', 'Marié', 'TSHISEKEDI TSHITOKO', 'Carte nationale d\'identité', 'KANANGA', '2023-02-26', '34940979624', NULL, 'VENTE DES POULES', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 15:40:48', '2026-03-16 15:40:48'),
('CL-EBENKGA-26-00362', 'ZON-EBENKGA-26-00001', 'Tshitebua', 'Mulaja', 'Nicolas', NULL, '0985363086', 'M', '1983-12-24', 'Kananga', 'Aéroport, Rva, kananga', 'Marié', 'Mangabu Dorcas', 'Carte d\'électeur', 'Kananga', '2023-03-25', '34948173655', NULL, 'Médecin', 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 15:44:45', '2026-03-16 15:44:45'),
('CL-EBENKGA-26-00363', 'ZON-EBENKGA-26-00001', 'Kapadi', 'Kamulombo', 'Bavon', NULL, '0991935135', 'M', '1997-08-18', 'Muena ditu', 'Sncc bikuku kananga', 'Marié', 'Tshibuabua Rosina', 'Carte d\'électeur', 'Kananga', '2023-03-16', '34930981768', NULL, 'Travailler', 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 15:48:36', '2026-03-16 15:48:36'),
('CL-EBENKGA-26-00364', 'ZON-EBENKGA-26-00006', 'Nsapu', 'Mutuale', 'Alina', NULL, '0991757173', 'F', '1990-06-15', 'Kananga', 'Likasi 212, malandji', 'Marié', 'José Ilunga', 'Carte d\'électeur', 'Kananga', '2023-03-16', '73', NULL, 'Ventes des souliers', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 15:49:07', '2026-03-16 15:49:07'),
('CL-EBENKGA-26-00365', 'ZON-EBENKGA-26-00011', 'MBUYI', 'NTUMBA', 'THERESE', NULL, '0995164238', 'F', '2000-04-10', 'KANANGA', '12, NZUJI, MPOKOLO, KATOKA', 'Marié', 'CHARLES KAKUMBA', 'Carte nationale d\'identité', 'KANANGA', '2023-02-16', '34930171543', NULL, 'VENTE DES HABITS', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 15:51:47', '2026-03-16 15:51:47'),
('CL-EBENKGA-26-00366', 'ZON-EBENKGA-26-00001', 'Sekelayi', 'Kabuya', 'Aimé', NULL, '0991680971', 'M', '1978-11-24', 'Kananga', 'Kuango,salongo muimba,nganza', 'Marié', 'Marie kapinga', 'Carte d\'électeur', 'Kananga', '2025-01-15', '116820', NULL, 'Travailler', 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 15:51:52', '2026-03-16 15:51:52'),
('CL-EBENKGA-26-00367', 'ZON-EBENKGA-26-00006', 'Kapinga', 'Lukusa', 'Souzane', NULL, '0991442744', 'F', '1991-03-05', 'Mbujimayi', 'Du canal 2, tshinsambi', 'Marié', 'Hervé Ilunga', 'Carte d\'électeur', 'Kananga', '2023-03-16', '82', NULL, 'Vente de l\'eau', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 15:56:31', '2026-03-16 15:56:31'),
('CL-EBENKGA-26-00368', 'ZON-EBENKGA-26-00001', 'Mujinga', 'Kabengele', 'Berth', NULL, NULL, 'M', '1977-09-05', 'Ndekesha', 'Mpokolo', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-19', '3493377768', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 15:58:12', '2026-03-16 15:58:12'),
('CL-EBENKGA-26-00369', 'ZON-EBENKGA-26-00006', 'Bamue', 'Nkongolo', 'Joel', NULL, '0991442744', 'F', '2000-03-05', 'Kananga', 'Du canal 2, tshinsambi', 'Marié', 'Hervé Ilunga', 'Carte d\'électeur', 'Kananga', '2023-03-16', '63', NULL, 'Vente d\'unité', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 16:00:06', '2026-03-16 16:00:06'),
('CL-EBENKGA-26-00370', 'ZON-EBENKGA-26-00006', 'Nkongolo', 'Kayanda', 'Godet', NULL, '0977257475', 'F', '1991-11-26', 'Tshikapa', 'Du canal, biancky', 'Marié', 'Jean cimanga', 'Carte d\'électeur', 'Kananga', '2023-03-16', '54', NULL, 'Mini shop', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 16:03:30', '2026-03-16 16:03:30'),
('CL-EBENKGA-26-00371', 'ZON-EBENKGA-26-00004', 'MALAKI', 'MALAKI', 'MERVEILLE', NULL, '0975263976', 'F', '1982-03-24', 'KANANGA', 'N° 16 AV Bakole, Q Plateau C KANANGA', 'Marié', 'André BENA', 'Carte d\'électeur', 'KANANGA', '2023-03-10', '35945275', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 16:07:21', '2026-03-16 16:07:21'),
('CL-EBENKGA-26-00372', 'ZON-EBENKGA-26-00004', 'MASHEKI', 'FUNDUNDU', 'DAVID', NULL, '0990022507', 'M', '1967-02-02', 'KIKWIT', 'N° 48 Av KALOMBO MFUMU, Q KAMAYI C KANANGA', 'Marié', 'ANGEL AMBO', 'Carte d\'électeur', 'KANANGA', '2023-02-14', '3495476588', NULL, 'VENTE DE MAÏS', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 16:11:34', '2026-03-16 16:11:34'),
('CL-EBENKGA-26-00373', 'ZON-EBENKGA-26-00001', 'Tshimpamba', 'MBuyi', 'François', NULL, NULL, 'M', '2020-03-20', 'Kananga', 'Lulua kele kele katoka', 'Célibataire', NULL, 'Autre', 'Kananga', '2023-03-14', '586', NULL, 'Marché noir', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 16:13:29', '2026-03-16 16:13:29'),
('CL-EBENKGA-26-00374', 'ZON-EBENKGA-26-00004', 'MBUANYA', 'KASONGA', 'MESCHAK', NULL, '0970164375', 'M', '1997-09-02', 'MFUAMBA', 'N° 86 Av du Rails Q/ TSHINSAMBI C/ KANANGA', 'Marié', 'ANGEL LUENDU', 'Carte d\'électeur', 'KANANGA', '2023-02-09', '3548527', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 16:17:11', '2026-03-16 16:17:11'),
('CL-EBENKGA-26-00375', 'ZON-EBENKGA-26-00004', 'TSHIBANGU', 'MUSEKA', 'MATHIEU', NULL, '09936264738', 'M', '1975-03-27', 'LIKASI', 'N° 53 AV MUKALAMUSHI Q KAMAYI C KANANGA', 'Marié', 'ANGEL BUNDULA', 'Carte d\'électeur', 'KANANGA', '2023-03-09', '34953627', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 16:22:03', '2026-03-16 16:22:03'),
('CL-EBENKGA-26-00376', 'ZON-EBENKGA-26-00004', 'NDUMBULA', 'MILAMBO', 'ANTOINETTE', NULL, '+243 808 375 232', 'F', '1973-06-15', 'LUBUMBASHI', 'N° 50 Av decadet Q Kamayi C KANANGA', 'Marié', 'Maurice MUYAFU', 'Carte d\'électeur', 'KANANGA', '2023-02-16', '349563857', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 16:47:29', '2026-03-16 16:47:29'),
('CL-EBENKGA-26-00377', 'ZON-EBENKGA-26-00008', 'NGANDU', 'NSENGA', 'Betty', NULL, '0979473142', 'F', '1982-09-28', 'Kananga', '08, KANDJINDJI, KATOKA II MPOKOLO, KATOKA', 'Marié', 'NSENGA Albert', 'Carte d\'électeur', 'Kananga Ville', '2023-03-15', '354', NULL, 'Huile de noix et noix de palme', 'Commerce', NULL, NULL, NULL, NULL, '12', 2000000.00, NULL, NULL, '2026-03-16 17:02:33', '2026-03-16 17:02:33'),
('CL-EBENKGA-26-00378', 'ZON-EBENKGA-26-00008', 'TSHITADI', 'TSHITADI', 'Oscar', NULL, '0979340652', 'M', '1982-02-02', 'Mbuji mayi', 'MIYAWU, KAPANDA, KATOKA', 'Marié', 'KUPA Antoinette', 'Carte d\'électeur', 'Kananga Ville', '2023-03-14', '3493545', NULL, 'Vente de planches', 'Commerce', NULL, NULL, NULL, NULL, '7', 150000.00, NULL, NULL, '2026-03-16 17:07:13', '2026-03-16 17:07:13'),
('CL-EBENKGA-26-00379', 'ZON-EBENKGA-26-00003', 'KABEDI', 'MUKALA', 'SOPHIE', NULL, NULL, 'F', '1994-10-10', 'Kananga', 'Q/ Malandji C/ kananga', 'Marié', 'Boniface ngandu', 'Carte nationale d\'identité', 'Kananga', '2023-03-05', '34936636333', 'clients/1773684586_1000122786.jpeg', 'Enseignante', NULL, NULL, NULL, NULL, NULL, '7', NULL, NULL, NULL, '2026-03-16 17:09:46', '2026-03-16 17:09:46'),
('CL-EBENKGA-26-00380', 'ZON-EBENKGA-26-00008', 'MASANKA', 'MUSUBE', 'Véronique', NULL, '0971930490', 'F', '1977-04-14', 'Kananga', '16, LIKASI, MALANDJI, KANANGA', 'Marié', 'BANKANDOWA Théo', 'Carte d\'électeur', 'Kananga Ville', '2023-03-13', '349456', NULL, 'Vente de planches', 'Commerce', NULL, NULL, NULL, NULL, '35', 3000000.00, NULL, NULL, '2026-03-16 17:11:49', '2026-03-16 17:11:49'),
('CL-EBENKGA-26-00381', 'ZON-EBENKGA-26-00008', 'NYABU', 'MINGA', 'Rachel', NULL, '0972870715', 'F', '2004-08-23', 'Kananga', '2, KISANGANI, KELE-KELE, KATOKA', 'Marié', 'TSHIMANGA NTUMBA', 'Carte d\'électeur', 'Kananga Ville', '2023-03-08', '34936', NULL, 'Vente d\'huile de palme', 'Commerce', NULL, NULL, NULL, NULL, '6', 100000.00, NULL, NULL, '2026-03-16 17:16:25', '2026-03-16 17:16:25'),
('CL-EBENKGA-26-00382', 'ZON-EBENKGA-26-00003', 'KAPINGA', 'NYENGELE', 'VERRO', NULL, '0978834097', 'F', '2006-12-29', 'Kananga', 'Av du commerce,Q/ tshinsambi C/ kananga n° 25', 'Marié', 'Alfred MAYi', 'Carte nationale d\'identité', 'Kananga', '2023-07-09', '34945782126', NULL, NULL, 'Service', NULL, NULL, NULL, NULL, '2', NULL, NULL, NULL, '2026-03-16 17:17:15', '2026-03-16 17:17:15'),
('CL-EBENKGA-26-00383', 'ZON-EBENKGA-26-00008', 'MUNAKEBE', 'NGANDU', 'Thérèse', NULL, '0976841432', 'F', '1980-06-16', 'Kananga', '35, MUKENGESHAYI, MPOKOLO, KATOKA', 'Marié', 'NDIASHI Laurent', 'Carte d\'électeur', 'Kananga Ville', '2023-02-14', '3592', NULL, 'Moulin', 'Service', NULL, NULL, NULL, NULL, '25', 250000.00, NULL, NULL, '2026-03-16 17:20:48', '2026-03-16 17:20:48'),
('CL-EBENKGA-26-00384', 'ZON-EBENKGA-26-00008', 'KELENDE', 'LUKUNA', 'Jean', NULL, '0998920233', 'M', '1982-07-15', 'Kananga', '55, MATAMBA, NDESHA, NDESHA', 'Marié', 'DINGA Anny', 'Carte d\'électeur', 'Kananga Ville', '2023-03-06', '3291', NULL, 'Marchandises de cuisine', 'Commerce', NULL, NULL, NULL, NULL, '12', 120000.00, NULL, NULL, '2026-03-16 17:23:53', '2026-03-16 17:23:53'),
('CL-EBENKGA-26-00385', 'ZON-EBENKGA-26-00008', 'BANDEJA', 'MAKABU', 'Aimée', NULL, '0972336431', 'F', '1993-03-23', 'Kananga', '12, IPROMA, SNEL, KANANGA', 'Marié', 'NTUMBA Junior', 'Carte d\'électeur', 'Kananga Ville', '2023-03-13', '3298', NULL, 'Vente de planches', 'Commerce', NULL, NULL, NULL, NULL, '15', 2000000.00, NULL, NULL, '2026-03-16 17:26:56', '2026-03-16 17:26:56'),
('CL-EBENKGA-26-00386', 'ZON-EBENKGA-26-00008', 'NKONGOLO', 'MALUMBA', 'Jean', NULL, '0973839372', 'M', '1982-03-16', 'BUNKONDE', '13, LUKIBU, MPOKOLO, KATOKA', 'Marié', 'Angel TSHIBUKA', 'Carte d\'électeur', 'Kananga', '2023-03-08', '3589', NULL, 'Marchandises de cuisine', 'Commerce', NULL, NULL, NULL, NULL, '15', 300000.00, NULL, NULL, '2026-03-16 17:29:47', '2026-03-16 17:29:47'),
('CL-EBENKGA-26-00387', 'ZON-EBENKGA-26-00008', 'KOKO', 'OMELE', 'Alphonse', NULL, '0998645021', 'M', '1978-03-15', 'LODJA', '02, ILEBO, TSHINGESHI, LUKONGA', 'Marié', 'ELOKE MAMIE', 'Carte d\'électeur', 'LODJA', '2023-03-08', '35131986325', 'clients/1773686027_1000319410.jpeg', 'Vente du riz et d\'arachide', 'Commerce', NULL, 'Likasi', NULL, NULL, '13', 2000000.00, NULL, NULL, '2026-03-16 17:33:47', '2026-03-16 17:33:47'),
('CL-EBENKGA-26-00388', 'ZON-EBENKGA-26-00006', 'Tshitende', 'Ntumba', 'Marcel', NULL, '0981693848', 'M', '2006-03-27', 'Kananga', 'Malandji /snel', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-16', '43', NULL, 'Vente des téléphones', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 17:35:11', '2026-03-16 17:35:11'),
('CL-EBENKGA-26-00389', 'ZON-EBENKGA-26-00008', 'ESAMBO', 'KONDE', 'Martin', NULL, '0991129502', 'F', '1986-06-05', 'LODJA', '91, KOLWEZI, KELE-KELE, KATOKA', 'Marié', 'ALOKO', 'Carte d\'électeur', 'LODJA', '2023-02-14', '3512', NULL, 'Vente du riz et d\'arachides', 'Commerce', NULL, 'Likasi, côté Moulin', NULL, NULL, '15', 3000000.00, NULL, NULL, '2026-03-16 17:37:25', '2026-03-16 17:37:25'),
('CL-EBENKGA-26-00390', 'ZON-EBENKGA-26-00006', 'Olenga', 'Lorandiola', 'Micheline', NULL, '0975187793', 'F', '1988-10-16', 'Tshiumbe', 'Kelekele,katoka', 'Marié', 'Michel', 'Carte d\'électeur', 'Kananga', '2023-03-16', '90', NULL, 'Vente chaussures', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 17:39:07', '2026-03-16 17:39:07'),
('CL-EBENKGA-26-00391', 'ZON-EBENKGA-26-00008', 'BISAMBA', 'MULAMBA', 'Anny', NULL, '0995102635', 'F', '2000-01-05', 'Kananga', 'KAPANDA, KATOKA', 'Marié', 'Crispin KAYEMBE', 'Carte d\'électeur', 'Kananga Ville', '2023-03-20', '2345', NULL, 'Marchandises de cuisine', 'Commerce', NULL, NULL, NULL, NULL, '12', 250000.00, NULL, NULL, '2026-03-16 17:40:13', '2026-03-16 17:40:13'),
('CL-EBENKGA-26-00392', 'ZON-EBENKGA-26-00008', 'MISENGA', 'MUANZA', 'Cécile', NULL, '0991457771', 'F', '1986-07-07', 'Kananga', '08, MADILA, KANYUKA, KANANGA', 'Marié', 'TSHINTU José', 'Carte d\'électeur', 'Kananga Ville', '2023-01-02', '3244', NULL, 'Vende de carburant', 'Commerce', NULL, NULL, NULL, NULL, '15', 500000.00, NULL, NULL, '2026-03-16 17:43:06', '2026-03-16 17:43:06');
INSERT INTO `tb_clients` (`matricule`, `code_zone`, `nom`, `postnom`, `prenom`, `email`, `telephone`, `sexe`, `date_naissance`, `lieu_naissance`, `adresse`, `etat_civil`, `nom_conjoint`, `type_piece_identite`, `lieu_delivrance_piece`, `date_delivrance_piece`, `numero_piece_identite`, `photo`, `secteur_activite`, `type_activite`, `nom_entreprise`, `adresse_entreprise`, `telephone_entreprise`, `statut_entreprise`, `nombre_annees_experience`, `revenu_mensuel`, `revenu_mensuel_devise`, `autres_details_activite`, `created_at`, `updated_at`) VALUES
('CL-EBENKGA-26-00393', 'ZON-EBENKGA-26-00008', 'BALUFU', 'MUDIKOLELA', 'Robert', NULL, '0983955382', 'M', '1994-12-12', 'BILOMBA', '17, MIYAWU, KAPANDA, KATOKA', 'Marié', 'Esther TSHIBOLA', 'Carte d\'électeur', 'BILOMBA', '2023-03-07', '35692805641', NULL, 'Restaurant', 'Service', NULL, NULL, NULL, NULL, '13', 450000.00, NULL, NULL, '2026-03-16 17:46:17', '2026-03-16 17:46:17'),
('CL-EBENKGA-26-00394', 'ZON-EBENKGA-26-00003', 'MBOMBO', 'KATENDE', 'MARLEINE', NULL, '0972081027', 'F', '1994-10-11', 'Tshikapa', 'Av du canal Q/tshinsambi C/kananga n° 54', 'Marié', 'Richard', 'Carte nationale d\'identité', 'Kananga', '2023-02-10', '3498', NULL, 'Vente de plastica', NULL, NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-16 17:47:59', '2026-03-16 17:47:59'),
('CL-EBENKGA-26-00395', 'ZON-EBENKGA-26-00008', 'MUSWAYI', 'LUBOYA', 'Mado', NULL, '0996932910', 'F', '1984-02-13', 'MUEKA', 'TSHIBASHI, DIKONGAYI, LUKONGA', 'Marié', 'André ILUNGA', 'Carte d\'électeur', 'Kananga Ville', '2026-03-16', '3476', 'clients/1773686982_1000317276.jpeg', 'Vente de planches', 'Commerce', NULL, NULL, NULL, NULL, '20', 2000000.00, NULL, NULL, '2026-03-16 17:49:42', '2026-03-16 17:49:42'),
('CL-EBENKGA-26-00396', 'ZON-EBENKGA-26-00003', 'BINGA', 'BUABUA', 'BIJOUX', NULL, '0990742453', 'F', '1992-03-13', 'Kananga', 'Av Mpokolo Q/katoka2 C/ katoka', 'Marié', 'Joseph kayembe', 'Carte nationale d\'identité', 'Kananga', '2023-03-10', '3497', NULL, 'Vente de savons', NULL, NULL, NULL, NULL, NULL, '16', 300000.00, NULL, NULL, '2026-03-16 17:53:23', '2026-03-16 17:53:23'),
('CL-EBENKGA-26-00397', 'ZON-EBENKGA-26-00008', 'TSHIABU', 'LUKUSA', 'Thérèse', NULL, '0979706523', 'F', '1977-06-15', 'Kananga', '35, TULUME, KATOKA II, KATOKA', 'Marié', 'ONOYA Robert', 'Carte d\'électeur', 'Kananga Ville', '2023-03-06', '34934177491', 'clients/1773687296_1000319399.jpeg', 'Divers', 'Commerce', NULL, NULL, NULL, NULL, '30', 250000.00, NULL, NULL, '2026-03-16 17:54:56', '2026-03-16 17:54:56'),
('CL-EBENKGA-26-00398', 'ZON-EBENKGA-26-00008', 'MULONDA', 'NTUMBA', 'Jean', NULL, '0972998566', 'M', '1992-12-11', 'Kananga', '19, KIVU, KELE-KELE, KATOKA', 'Marié', 'Josée BAPEDI', 'Carte d\'électeur', 'Kananga Ville', '2023-03-07', '34933987011', NULL, 'Boutique divers', 'Commerce', NULL, NULL, NULL, NULL, '12', 600000.00, NULL, NULL, '2026-03-16 17:59:05', '2026-03-16 17:59:05'),
('CL-EBENKGA-26-00399', 'ZON-EBENKGA-26-00008', 'TSHIMBILA', 'MUYAYA', 'Excellent', NULL, '0997539287', 'M', '1988-04-24', 'LUNYEKE', '07, MANONO, MPOKOLO, KATOKA', 'Marié', 'Thérèse KATEBA', 'Carte d\'électeur', 'Kananga Ville', '2023-03-20', '23455', NULL, 'Boutique et moto', 'Commerce', NULL, NULL, NULL, NULL, '15', 1500000.00, NULL, NULL, '2026-03-16 18:02:58', '2026-03-16 18:02:58'),
('CL-EBENKGA-26-00400', 'ZON-EBENKGA-26-00008', 'NGALAMULUME', 'KAZUMBA', 'Benoit', NULL, '0973690030', 'M', '2026-03-16', 'Kananga', '16, KAMAYI, MPOKOLO, KATOKA', 'Marié', 'KANKU', 'Carte d\'électeur', 'Kananga', '2026-03-16', '2332', NULL, 'Moulin', 'Service', NULL, NULL, NULL, NULL, '25', 3000000.00, NULL, NULL, '2026-03-16 18:07:38', '2026-03-16 18:07:38'),
('CL-EBENKGA-26-00401', 'ZON-EBENKGA-26-00008', 'MPUTU', 'KAPUKU', 'Clément', NULL, '098649802', 'M', '2005-01-05', 'Kananga', '07, DIBAYA, NDESHA, NDESHA', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga Ville', '2026-03-16', '1234', NULL, 'Vente de boissons alcooliques', 'Commerce', NULL, NULL, NULL, NULL, '5', 100000.00, NULL, NULL, '2026-03-16 18:11:33', '2026-03-16 18:11:33'),
('CL-EBENKGA-26-00402', 'ZON-EBENKGA-26-00008', 'MATANGA', 'MULANGU', 'Katty', NULL, '0995009479', 'F', '1982-08-20', 'Kananga', '25,  TUBULUKU, KATOKA, KATOKA', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga Ville', '2026-03-16', '112233', NULL, 'Huile de noix et noix de palme', 'Commerce', NULL, NULL, NULL, NULL, '23', 2000000.00, NULL, NULL, '2026-03-16 18:15:11', '2026-03-16 18:15:11'),
('CL-EBENKGA-26-00403', 'ZON-EBENKGA-26-00008', 'MUAMBA', 'NKOLE', 'Davina', NULL, '0995009477', 'F', '1999-02-12', 'Kananga', '36, MUKENGESHAYI, MPOKOLO, KATOKA', 'Marié', 'Marcel TSHIPAMBA', 'Carte d\'électeur', 'Kananga Ville', '2026-03-16', '223341', NULL, 'Huile de noix et noix de palme', 'Commerce', NULL, NULL, NULL, NULL, '5', 500000.00, NULL, NULL, '2026-03-16 18:19:31', '2026-03-16 18:19:31'),
('CL-EBENKGA-26-00404', 'ZON-EBENKGA-26-00008', 'BIBOMBA', 'KALALA', 'Pascal', NULL, '0992934170', 'M', '1992-02-04', 'Kananga', '16, LIKASI, KELE-KELE, KATOKA', 'Marié', 'Rose TSHIBOLA', 'Carte d\'électeur', 'Kananga Ville', '2023-03-14', '34953188376', NULL, 'Boutique divers', 'Commerce', NULL, NULL, NULL, NULL, '15', 750000.00, NULL, NULL, '2026-03-16 18:26:22', '2026-03-16 18:26:22'),
('CL-EBENKGA-26-00405', 'ZON-EBENKGA-26-00003', 'MIkOMBE', 'TSHIAYIMA', 'JOSUÉ', NULL, '0970235042', 'M', '2001-07-06', 'Kananga', 'Av kasangidi,Q/katoka2 C/katoka n°21', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-20', '34932980459', 'clients/1773689207_1000120121.jpeg', NULL, 'Commerce', NULL, NULL, NULL, NULL, '3', NULL, NULL, NULL, '2026-03-16 18:26:47', '2026-03-16 18:26:47'),
('CL-EBENKGA-26-00406', 'ZON-EBENKGA-26-00008', 'BITSHILUALUA', 'MADIYA', 'Marceline', NULL, '0990154635', 'M', '1978-06-03', 'TSHIKAPA', '155, WALIKALI I, MALANDJI, KANANGA', 'Marié', 'Mbutu Kanisá Norbert', 'Carte d\'électeur', 'Kananga Ville', '2024-09-03', '34922575726', NULL, 'Divers', 'Commerce', NULL, NULL, NULL, NULL, '2', 250000.00, NULL, NULL, '2026-03-16 18:32:08', '2026-03-16 18:32:08'),
('CL-EBENKGA-26-00407', 'ZON-EBENKGA-26-00003', 'KALUMBA', 'MULUMBA', 'FRANÇOIS', NULL, '09911223155', 'M', '1991-05-05', 'Kananga', 'Av luanga lueta Q/ Mpokolo C/katoka n°12', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-05', '34933569762', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '6', NULL, NULL, NULL, '2026-03-16 18:37:34', '2026-03-16 18:37:34'),
('CL-EBENKGA-26-00408', 'ZON-EBENKGA-26-00007', 'BILOLO', 'KAYEMBE', 'PHILO', NULL, '0971723065', 'M', '1995-07-10', 'BENA MASONO', 'AV DIBELAYI N°46 Q/ MPOKOLO C/ KATOKA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-25', '34931572411', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 18:41:20', '2026-03-16 18:41:20'),
('CL-EBENKGA-26-00409', 'ZON-EBENKGA-26-00011', 'TSHIYOYO', 'NGALAMULUME', 'HELENE', NULL, '093937042', 'F', '1999-06-10', 'KANANGA', '10, GAR, AZDA,KANANGA', 'Marié', 'KANUNDEYA KAPENA', 'Carte nationale d\'identité', 'KANANGA', '2023-02-03', '3513817876', NULL, 'VENTE D\'HABIT', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 18:48:01', '2026-03-16 18:48:01'),
('CL-EBENKGA-26-00410', 'ZON-EBENKGA-26-00007', 'NGALULA', 'BUABUA', 'JEANNE', NULL, '0984694233', 'F', '1972-12-10', 'ILEBO', 'AV MBUYA N°164 Q/ KAMILABI', 'Marié', NULL, 'Carte d\'électeur', 'DEMBA', '2023-12-31', '33487701684', NULL, 'VENTE DE MAÏS', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 18:48:46', '2026-03-16 18:48:46'),
('CL-EBENKGA-26-00411', 'ZON-EBENKGA-26-00007', 'MBATSHI', 'NKOLELA', 'ESTHER', NULL, '0999119203', 'F', '1977-06-22', 'KANANGA', 'AV RÉVOLUTION N°220 Q/ MPOKOLO C/ KATOKA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2023-03-01', '34930180903', NULL, NULL, 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 18:53:49', '2026-03-16 18:53:49'),
('CL-EBENKGA-26-00412', 'ZON-EBENKGA-26-00007', 'MPEMBA', 'MUAMBA', 'DALY', NULL, '0976764304', 'F', '1993-05-09', 'KANANGA', 'AV KAMUANDU N°31 Q/ TSHIBANDABANDA C/NDESHA', 'Veuf', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-16', '3494277778', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 18:59:09', '2026-03-16 18:59:09'),
('CL-EBENKGA-26-00413', 'ZON-EBENKGA-26-00005', 'Kamba', 'Kabeya', 'THETHE', NULL, '0977160725', 'F', '1984-08-11', 'Mbuji-mayi', 'Numéro 10 ,avenue de l\'athné ,Q) kamayi', 'Marié', 'André', 'Autre', 'Kananga', '2026-03-16', 'Permis de conduire', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 19:02:32', '2026-03-16 19:02:32'),
('CL-EBENKGA-26-00414', 'ZON-EBENKGA-26-00007', 'ELAMENJI', 'MUAMBA', 'MARIAM', NULL, '0970104829', 'F', '1994-04-12', 'KANANGA', 'AV MAKOLO N°24 Q/ MPOKOLO C/ KATOKA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-06', '3493774516', NULL, NULL, 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 19:03:45', '2026-03-16 19:03:45'),
('CL-EBENKGA-26-00415', 'ZON-EBENKGA-26-00007', 'TSHILENDE', 'KANYINDA', 'FRANÇOIS', NULL, '0994364692', 'M', '1970-08-30', 'KANANGA', 'AV LUIZA N°31 Q/ TSHIBANDABANDA C/ NDESHA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-02', '34925770133', NULL, 'PASTEUR', 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 19:12:39', '2026-03-16 19:12:39'),
('CL-EBENKGA-26-00416', 'ZON-EBENKGA-26-00005', 'Mujinga', 'Biduaya', 'Anne', NULL, '0998428039', 'F', '1989-03-02', 'Luebo', 'Numéro 1 av tshisekedi ,Q) mabondo c) lukonga', 'Marié', 'Mulaba Maurice', 'Carte nationale d\'identité', 'Kananga', '2023-03-16', '34936175227', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 19:15:42', '2026-03-16 19:15:42'),
('CL-EBENKGA-26-00417', 'ZON-EBENKGA-26-00007', 'MANDE', 'BADIBANGA', 'LOSE', NULL, '0976741079', 'M', '1984-10-13', 'KANANGA', 'AV NYOKA N° 6 Q/ MPOKOLO C/ KATOKA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-09', '34932372785', NULL, 'VENTE DE PIÈCES DE RECHANGE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 19:17:29', '2026-03-16 19:17:29'),
('CL-EBENKGA-26-00418', 'ZON-EBENKGA-26-00010', 'BUAPUA', 'CÉLESTINE 2', 'THÉRÈSE', NULL, '0972754921', 'F', '1972-10-30', 'KANANGA', 'N°26/BUKONDE/NDESHA/NDESHA', 'Marié', 'VINCENT KABANGA', 'Carte d\'électeur', 'KANANGA', '2023-02-22', '34945581683', NULL, 'Le Poisson', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 19:19:17', '2026-03-16 19:19:17'),
('CL-EBENKGA-26-00419', 'ZON-EBENKGA-26-00007', 'KASONGA', 'KAYEMBE', 'LIVE', NULL, '0991245285', 'M', '1972-11-10', 'KANANGA', 'AV DE LA RÉVOLUTION N° 678 Q/ MPOKOLO C/ KATOKA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2026-03-10', '34945981831', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 19:23:07', '2026-03-16 19:23:07'),
('CL-EBENKGA-26-00420', 'ZON-EBENKGA-26-00005', 'Ngalula', 'Beya', 'Jully', NULL, '0996525726', 'F', '1979-06-27', 'Mbulungu', '34 avenue du canal Q) malandji , commune de kananga', 'Marié', 'Tishishine', 'Carte nationale d\'identité', 'Kananga', '2023-01-27', '34928568042', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 19:25:05', '2026-03-16 19:25:05'),
('CL-EBENKGA-26-00421', 'ZON-EBENKGA-26-00003', 'NGALULA', 'NDAYE', 'NELLY', NULL, NULL, 'F', '1984-03-18', 'Kananga', 'Av Mulowayi Q/ kamilabi C/ndesha', 'Marié', 'Jean Célestin kabasele', 'Carte nationale d\'identité', 'Kananga', '2023-02-08', '3495', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '18', NULL, NULL, NULL, '2026-03-16 19:29:13', '2026-03-16 19:29:13'),
('CL-EBENKGA-26-00422', 'ZON-EBENKGA-26-00010', 'BUAPUA', 'BEYA', 'CÉLESTINE', NULL, '0977137045', 'F', '1976-07-03', 'KANANGA', 'N°7/ KAMUPONGO/ NDESHA/NDESHA', 'Marié', 'MUJINGA MUAMBA', 'Carte d\'électeur', 'KANANGA', '2023-02-05', '34941974161', NULL, 'Le poisson', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 19:32:16', '2026-03-16 19:32:16'),
('CL-EBENKGA-26-00423', 'ZON-EBENKGA-26-00003', 'MUJINGA', 'TSHILOMBA', 'ANTHO', NULL, '0970950989', 'F', '1994-06-10', 'Kananga', 'Q/  campsnccC/ kananga', 'Marié', 'Jhon badibanga', 'Carte nationale d\'identité', 'Kananga', '2023-02-07', '3496', NULL, 'Vente de savons', NULL, NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, '2026-03-16 19:34:02', '2026-03-16 19:34:02'),
('CL-EBENKGA-26-00424', 'ZON-EBENKGA-26-00005', 'Bindingisha', 'Mutombo', 'Jully', NULL, '0988349084', 'F', '2007-12-26', 'Kananga', 'Numéro 34 avenue Ngenza Q) mabondo c) kananga', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-03-16', '3428495857', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 19:36:55', '2026-03-16 19:36:55'),
('CL-EBENKGA-26-00425', 'ZON-EBENKGA-26-00003', 'TSHILOMBA', 'TSHILOMBA', 'JEANNETTE', NULL, '0990702052', 'F', '1988-04-05', 'Kananga', 'Av kasavubu Q/Biancky C/kananga', 'Marié', 'Kabongo Godet', 'Carte nationale d\'identité', 'Kananga', '2023-02-10', '3493', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '15', NULL, NULL, NULL, '2026-03-16 19:42:05', '2026-03-16 19:42:05'),
('CL-EBENKGA-26-00426', 'ZON-EBENKGA-26-00005', 'MBUYI', 'Kabasu', 'Grâce', NULL, '0976940356', 'M', '2027-02-02', 'Tshimbulu', '29 avenue du manguier,Q) kamayi c) kananga', 'Marié', 'Mbeze Elysée', 'Carte nationale d\'identité', 'Kananga', '2023-03-16', '346838477', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 19:42:11', '2026-03-16 19:42:11'),
('CL-EBENKGA-26-00427', 'ZON-EBENKGA-26-00005', 'Nkengela', 'Mpenga', 'Esther', NULL, '0997871237', 'F', '1979-04-04', 'Kolwezi', '03/ avenue lukengu/ Q kamulumba c) kananga', 'Marié', 'Augustin katumba', 'Carte nationale d\'identité', 'Kananga', '2023-02-16', '34950578163', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 19:49:13', '2026-03-16 19:49:13'),
('CL-EBENKGA-26-00428', 'ZON-EBENKGA-26-00003', 'KABUYA', 'MUBENGA', 'HUBERT', NULL, '0976653787', 'M', '1975-05-27', 'Kananga', 'Av route ilebo Q/Tshibashi C/ lukonga', 'Marié', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-16', '34941776785', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '9', NULL, NULL, NULL, '2026-03-16 19:54:21', '2026-03-16 19:54:21'),
('CL-EBENKGA-26-00429', 'ZON-EBENKGA-26-00005', 'Tshiama', 'Mukenge', 'Esther', NULL, '0973189147', 'F', '1976-05-05', 'Lubumbashi', '43 avenue Dibaya , Q) kamayi) commune  kananga', 'Marié', 'Jacky kabamba', 'Carte d\'électeur', 'Kananga', '2023-02-02', '34922374357', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 19:56:52', '2026-03-16 19:56:52'),
('CL-EBENKGA-26-00430', 'ZON-EBENKGA-26-00003', 'NTUMBA', 'BAKAJIKA', 'FRANÇOIS', NULL, '0977658038', 'M', '1994-03-11', 'Tshikapa', 'Av lulua Q/cinquantainaire C/kananga', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-11-13', '34945981397', 'clients/1773695059_1000120135.jpeg', NULL, 'Service', NULL, NULL, NULL, NULL, '6', 260000.00, NULL, NULL, '2026-03-16 20:04:19', '2026-03-16 20:04:19'),
('CL-EBENKGA-26-00431', 'ZON-EBENKGA-26-00003', 'KALANGA', 'ILUNGA', 'ROVINA', NULL, '0993166202', 'F', '1998-07-15', 'Kinshasa', 'Q/ Malandji C/ kananga', 'Marié', 'Mpoyi Théophile', 'Carte nationale d\'identité', 'Kananga', '2023-02-24', '349379', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '10', NULL, NULL, NULL, '2026-03-16 20:09:52', '2026-03-16 20:09:52'),
('CL-EBENKGA-26-00432', 'ZON-EBENKGA-26-00005', 'Mbelu', 'Mbombo', 'Bertine', NULL, '0983875878', 'F', '2026-03-16', 'Kananga', '27 avenue tshinkunku', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '1988-03-02', '34290978487', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 20:13:11', '2026-03-16 20:13:11'),
('CL-EBENKGA-26-00433', 'ZON-EBENKGA-26-00003', 'KABASUBABU', 'KABASUBABU', 'MOZARD', NULL, '0981658891', 'M', '2004-03-12', 'Kananga', 'Av tshilenga Q/ kamilabi C/ ndesha', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-01-31', '34946569166', 'clients/1773695695_1000120108.jpeg', NULL, 'Service', NULL, NULL, NULL, NULL, '2', NULL, NULL, NULL, '2026-03-16 20:14:55', '2026-03-16 20:14:55'),
('CL-EBENKGA-26-00434', 'ZON-EBENKGA-26-00003', 'NGALULA', 'TSHIBANGU', 'ELYSÉE', NULL, NULL, 'F', '1974-06-20', 'Kananga', 'Av Basonga Q/ Tshibandabanda C/ ndesha n° 10', 'Marié', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-25', '349377', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '10', NULL, NULL, NULL, '2026-03-16 20:22:57', '2026-03-16 20:22:57'),
('CL-EBENKGA-26-00435', 'ZON-EBENKGA-26-00005', 'Badibanga', 'Mukenge', 'Dieudonné', NULL, '0987274150', 'M', '2004-11-11', 'Kananga', '26 ,  avenue Dibaya Q) tshibanada banda c) Ndesha', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-09', '34951577034', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-16 20:24:42', '2026-03-16 20:24:42'),
('CL-EBENKGA-26-00436', 'ZON-EBENKGA-26-00003', 'KAPINGA', 'MUTOMBO', 'ELYSÉE', NULL, '0997919329', 'F', '1986-03-02', 'Kananga', 'Av likasi Q/ kelekele C/katoka', 'Marié', 'Justin', 'Carte nationale d\'identité', 'Kananga', '2023-03-02', '349479908847', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '3', 600000.00, NULL, NULL, '2026-03-16 20:28:50', '2026-03-16 20:28:50'),
('CL-EBENKGA-26-00437', 'ZON-EBENKGA-26-00003', 'BATENA', 'MUKADI', 'AUGUSTIN', NULL, '0961053314', 'M', '2004-04-18', 'Ndemba', 'Av Dibaya Q/Tshibandabanda C/ ndesha', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-03-01', '349567', 'clients/1773696798_1000120098.jpeg', NULL, 'Service', NULL, NULL, NULL, NULL, '2', 250000.00, NULL, NULL, '2026-03-16 20:33:18', '2026-03-16 20:33:18'),
('CL-EBENKGA-26-00438', 'ZON-EBENKGA-26-00003', 'LENO', 'ELONDJI', 'MARCEL', NULL, '0994061923', 'M', '1978-12-15', 'Mbuji mayi', 'Av ubindu Q/ kelekele C/ katoka n° 32', 'Marié', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-03-08', '349780', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '15', 300000.00, NULL, NULL, '2026-03-16 20:37:59', '2026-03-16 20:37:59'),
('CL-EBENKGA-26-00439', 'ZON-EBENKGA-26-00003', 'MBUYI', 'MUDIMBI', 'FELLY', NULL, '0979521741', 'F', '1978-12-22', 'Kananga', 'Av kabue Q/ ndesha C/ ndesha', 'Veuf', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-18', '34942378654', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '17', 150000.00, NULL, NULL, '2026-03-16 20:42:31', '2026-03-16 20:42:31'),
('CL-EBENKGA-26-00440', 'ZON-EBENKGA-26-00003', 'TSHIBOLA', 'ILUNGA', 'MADO', NULL, '0997000657', 'F', '1976-06-26', 'Tshimbulu', 'Av du dépôt Q/ Mitete  C/ kananga n°25', 'Marié', 'Mulamba Boaw', 'Carte nationale d\'identité', 'Kananga', '2023-03-05', '34918410648', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '20', 200000.00, NULL, NULL, '2026-03-16 20:50:32', '2026-03-16 20:50:32'),
('CL-EBENKGA-26-00441', 'ZON-EBENKGA-26-00003', 'BADIBANGA', 'BEYA', 'LEONARD', NULL, '097809233', 'M', '1976-06-10', 'Kananga', 'Av du canal Q/industriel C/ kananga n°13', 'Marié', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-23', '34923579308', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '20', 6000000.00, NULL, NULL, '2026-03-16 20:55:25', '2026-03-16 20:55:25'),
('CL-EBENKGA-26-00442', 'ZON-EBENKGA-26-00003', 'MUKANA', 'KABASELE', 'SOLANGE', NULL, '0976605453', 'F', '1998-11-04', 'Lubumbashi', 'Av Mukendi Q/ Mabondo C/ Lukonga n° 15', 'Veuf', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-14', '34936375558', 'clients/1773698648_1000123001.jpeg', NULL, 'Commerce', NULL, NULL, NULL, NULL, '15', 300000.00, NULL, NULL, '2026-03-16 21:04:08', '2026-03-16 21:04:08'),
('CL-EBENKGA-26-00443', 'ZON-EBENKGA-26-00003', 'ILUNGA', 'MABIKA', 'ANDRÉ', NULL, '0979437286', 'M', '2000-11-04', 'ILEBO', 'Av LUALAMA Q/Malandji C/ kananga n° 26', 'Marié', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-03-23', '35689612432', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '9', NULL, NULL, NULL, '2026-03-16 21:10:51', '2026-03-16 21:10:51'),
('CL-EBENKGA-26-00444', 'ZON-EBENKGA-26-00007', 'BAMBI', 'LUPEMBA', 'VICTORINE', NULL, '0978367975', 'F', '1989-03-28', 'KANANGA', 'AV MUSUASUA N°3 Q/ MPOKOLO C/ KATOKA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2023-12-31', '34931184725', NULL, 'PIÈCES DE RECHANGE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 04:34:25', '2026-03-17 04:34:25'),
('CL-EBENKGA-26-00445', 'ZON-EBENKGA-26-00007', 'NYUNGA', 'KAFUAYI', 'HÉLÈNE', NULL, '0977374017', 'F', '1955-12-24', 'KANANGA', 'AV BUKASA N°14 Q/ DIKONGAYI C/ LUKONGA', 'Marié', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-14', '34938175212', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 04:39:50', '2026-03-17 04:39:50'),
('CL-EBENKGA-26-00446', 'ZON-EBENKGA-26-00003', 'MULUMBA', 'KAYEMBE', 'ALBERT', NULL, '0974125260', 'M', '1968-10-16', 'Kananga', 'Av du canal Q/ Malandji C/ kananga', 'Marié', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-22', '349370', NULL, 'Vente d\'habits', NULL, NULL, NULL, NULL, NULL, '31', NULL, NULL, NULL, '2026-03-17 05:02:10', '2026-03-17 05:02:10'),
('CL-EBENKGA-26-00447', 'ZON-EBENKGA-26-00003', 'NGALAMULUME', 'BADIONONA', 'AUGUSTIN', NULL, '0979852050', 'M', '1999-11-25', 'Mbuji mayi', 'Av kalamba Q/ kamilabi C/ ndesha n° 04', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-03-11', '34946370441', NULL, NULL, 'Service', NULL, NULL, NULL, NULL, '8', NULL, NULL, NULL, '2026-03-17 05:09:50', '2026-03-17 05:09:50'),
('CL-EBENKGA-26-00448', 'ZON-EBENKGA-26-00004', 'KANKU', 'MBUYI', 'CELESTINE', NULL, '0993456890', 'F', '1959-07-07', 'BENA TSHITOLO', 'N° 35 AV Ditalala, Q KAMAYI C KANANGA', 'Marié', 'NTUMBA BERTIN', 'Carte d\'électeur', 'KANANGA', '2023-01-28', '34921377', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 05:11:16', '2026-03-17 05:11:16'),
('CL-EBENKGA-26-00449', 'ZON-EBENKGA-26-00004', 'NGALULA', 'MPONDA', 'GERMAINE', NULL, '0996383639', 'F', '1994-06-30', 'KANANGA', 'N° 65 AV KATOLO, Q/ KAMAYI C KANANGA', 'Marié', 'JEAN TSHIBUABUA', 'Carte d\'électeur', 'KANANGA', '2023-03-01', '34862739', NULL, 'VENTE DE POISSONS', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 05:16:25', '2026-03-17 05:16:25'),
('CL-EBENKGA-26-00450', 'ZON-EBENKGA-26-00003', 'NSANTU', 'KABUE', 'REMY', NULL, '0978994835', 'M', '2003-06-06', 'Kananga', 'Q/ Mabondo C/ lukonga av du 24 n° 21', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-17', '349372', NULL, NULL, 'Autre', NULL, NULL, NULL, NULL, '5', 250.00, NULL, NULL, '2026-03-17 05:16:31', '2026-03-17 05:16:31'),
('CL-EBENKGA-26-00451', 'ZON-EBENKGA-26-00003', 'BANDEJA', 'TSHIAMUANA', 'REBECCA', NULL, '0973359664', 'F', '1974-09-17', 'Kananga', 'Av kalekase Q/ kapanda C/ katoka n° 8', 'Marié', NULL, 'Carte nationale d\'identité', 'Kananga', '2003-03-05', '34920', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '13', 100000.00, NULL, NULL, '2026-03-17 05:22:08', '2026-03-17 05:22:08'),
('CL-EBENKGA-26-00452', 'ZON-EBENKGA-26-00004', 'NTUMBA', 'KALOMBA', 'FRANCONE', NULL, '0986537582', 'F', '1997-09-26', 'KANANGA', 'N° 25 Av KALUSENGA, Q KAMAYI C KANANGA', 'Marié', 'BENJAMIN TSHOYOYO', 'Carte d\'électeur', 'KANANGA', '2023-01-28', '346729373', NULL, 'VENTE DE VIANDE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 05:25:18', '2026-03-17 05:25:18'),
('CL-EBENKGA-26-00453', 'ZON-EBENKGA-26-00004', 'NTUMBA', 'KANKONDE', 'JUSTIN', NULL, '09936548648', 'M', '1985-07-20', 'KANANGA', 'N° 36 Av LUENDU Q TSHINSAMBI, C/ KANANGA', 'Marié', 'JULIENNE TSHEBUE', 'Carte d\'électeur', 'KANANGA', '2023-03-01', '349256392', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 05:28:21', '2026-03-17 05:28:21'),
('CL-EBENKGA-26-00454', 'ZON-EBENKGA-26-00003', 'NGALAMULUME', 'NGALAMULUME', 'THÉO JAC', NULL, '0973964830', 'M', '1971-04-01', 'Lulengele', 'Av Sangayembo Q/ tukombe C/ katoka n° 59', 'Marié', 'Kapinga marie', 'Carte nationale d\'identité', 'Kananga', '2023-02-01', '34935369493', NULL, 'Pasteur', NULL, NULL, NULL, NULL, NULL, '10', NULL, NULL, NULL, '2026-03-17 05:35:49', '2026-03-17 05:35:49'),
('CL-EBENKGA-26-00455', 'ZON-EBENKGA-26-00003', 'MUYAYA', 'NKINDA', 'ALBERT', NULL, '0977531126', 'M', '1995-05-25', 'Kananga', 'Av katende Q/ ndesha C/ ndesha n° 64', 'Marié', 'Lyly', 'Carte nationale d\'identité', 'Kananga', '2023-02-19', '34942920908', NULL, NULL, 'Service', NULL, NULL, NULL, NULL, '4', NULL, NULL, NULL, '2026-03-17 05:43:46', '2026-03-17 05:43:46'),
('CL-EBENKGA-26-00456', 'ZON-EBENKGA-26-00004', 'MUJINGA', 'KALALA', 'SOLANGE', NULL, '0990448919', 'F', '1990-04-25', 'KANANGA', 'N°34, Av Azda, Q/ Onze, C/ KANANGA', 'Marié', 'KABUE Samy', 'Carte d\'électeur', 'KANANGA', '2023-02-15', '3495426', NULL, 'COMMERCE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 05:43:58', '2026-03-17 05:43:58'),
('CL-EBENKGA-26-00457', 'ZON-EBENKGA-26-00001', 'Mbiya', 'Kalonji', 'Dieudonné', NULL, '0994652075', 'M', '1973-06-24', 'Kananga', 'Emo, Mpokolo katoka', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-03', '34931182972', NULL, 'Quinquelerie', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 05:46:57', '2026-03-17 05:46:57'),
('CL-EBENKGA-26-00458', 'ZON-EBENKGA-26-00004', 'LUSAMBA', 'TSHIPAMBA', 'ROSE', NULL, '0984559029', 'F', '1968-06-11', 'KANANGA', 'N° 26 Av KALUSENGA Q KAMAYI C KANANGA', 'Marié', 'TSHIBAMBULA FRANÇOIS', 'Carte d\'électeur', 'KANANGA', '2023-03-13', '359352836', NULL, 'VENTE D\'EPICES', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 05:48:52', '2026-03-17 05:48:52'),
('CL-EBENKGA-26-00459', 'ZON-EBENKGA-26-00001', 'Tshilumba', 'Kena', 'Mike', NULL, '0999122873', 'M', '2004-08-28', 'Tshikapa', 'Du marché KAMAYI Kananga', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-02-03', '34927374213', NULL, 'Photographe', 'Service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 05:50:16', '2026-03-17 05:50:16'),
('CL-EBENKGA-26-00460', 'ZON-EBENKGA-26-00004', 'MPUTU', 'KATAWA', 'ELYSÉE', NULL, '08254362836', 'F', '1983-04-17', 'KANANGA', 'N° 67 Av TUYEPAMUE, Q KAMAYI C KANANGA', 'Marié', 'Froid Lubi', 'Carte d\'électeur', 'KANANGA', '2023-02-16', '359863718', NULL, 'VENTE D\'EPICES', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 05:51:22', '2026-03-17 05:51:22'),
('CL-EBENKGA-26-00461', 'ZON-EBENKGA-26-00003', 'MUTELA', 'NKUNA', 'GUELLORD', NULL, '0999368222', 'M', '1995-04-17', 'Kananga', 'Av dekese Q/ Tshibandabanda C/ ndesha n° 17', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-02-03', '34949187809', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '8', 300000.00, NULL, NULL, '2026-03-17 05:51:23', '2026-03-17 05:51:23'),
('CL-EBENKGA-26-00462', 'ZON-EBENKGA-26-00001', 'Makenga', 'Badibanga', 'Maguy', NULL, '0832327099', 'F', '1974-07-28', 'Kananga', 'Walkal2 Malandji kananga', 'Marié', 'Katompa Lukusa', 'Carte d\'électeur', 'Kananga', '2023-02-08', '34945776594', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 05:55:07', '2026-03-17 05:55:07'),
('CL-EBENKGA-26-00463', 'ZON-EBENKGA-26-00004', 'NTUMBA', 'MFUAMBA', 'ADEL', NULL, '09945382653', 'F', '1978-03-04', 'KANANGA', 'N° 45 Av Likasi Q Malanji C KANANGA', 'Marié', 'JEAN THÉO MANDJONDO', 'Carte d\'électeur', 'KANANGA', '2023-02-24', '35924637379', NULL, 'PHARMACIE', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 05:55:31', '2026-03-17 05:55:31'),
('CL-EBENKGA-26-00464', 'ZON-EBENKGA-26-00003', 'TSHIBANDA', 'CITADI', 'JOSEPH NEVILLE', NULL, '0973318553', 'M', '2004-03-25', 'Kananga', 'Av maman yemo bus Q/ Azda C/ kananga', 'Célibataire', NULL, 'Carte nationale d\'identité', 'Kananga', '2023-03-10', '3193278008', NULL, NULL, 'Commerce', NULL, NULL, NULL, NULL, '8', NULL, NULL, NULL, '2026-03-17 06:01:21', '2026-03-17 06:01:21'),
('CL-EBENKGA-26-00465', 'ZON-EBENKGA-26-00004', 'ODIA', 'BAKAJIKA', 'SARAH', NULL, '099646846', 'F', '1976-03-13', 'KANANGA', 'N°03 Av TSHINSENSE Q KAMAYI C KANANGA', 'Marié', 'KANGU JHON', 'Carte d\'électeur', 'KANANGA', '2023-03-14', '349858467', 'clients/1773730996_1000017029.jpeg', 'VENTE des poissons', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 06:03:16', '2026-03-17 06:03:16'),
('CL-EBENKGA-26-00466', 'ZON-EBENKGA-26-00001', 'Nganda', 'Otenga', 'Paul', NULL, '0828836150', 'M', '1992-03-28', 'Lodja', 'Lubundi kele kele katoka', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-21', '34930900516', NULL, 'Vente d\'habit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 06:05:43', '2026-03-17 06:05:43'),
('CL-EBENKGA-26-00467', 'ZON-EBENKGA-26-00006', 'Tshonda', 'Omasombo', 'Nathalis', NULL, '0994545213', 'M', '1987-03-17', 'Kananga', 'Kelekele, katoka', 'Veuf', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-17', '10', NULL, 'Vente d\'habits', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 06:09:09', '2026-03-17 06:09:09'),
('CL-EBENKGA-26-00468', 'ZON-EBENKGA-26-00004', 'LUPETU', 'MULUMBA', 'JULIENNE', NULL, '099547636', 'F', '1996-07-31', 'KANANGA', 'N°46 C/KANANGA Q KAMAYI', 'Marié', 'CHOUCHOU Ngombe', 'Carte d\'électeur', 'KANANGA', '2023-03-22', '349547774', NULL, 'VENTE des poissons', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 06:09:23', '2026-03-17 06:09:23'),
('CL-EBENKGA-26-00469', 'ZON-EBENKGA-26-00001', 'Kabasele', 'Ntambue', 'Vicky', NULL, '0977238243', 'F', '2002-04-04', 'Kananga', 'Espoir Malandji kananga', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-01-07', '120', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 06:10:17', '2026-03-17 06:10:17'),
('CL-EBENKGA-26-00470', 'ZON-EBENKGA-26-00001', 'Ntanga', 'Mukendi', 'Jolie', NULL, '0973668162', 'F', '1986-10-13', 'Kananga', 'Kinshasa kele kele katoka', 'Veuf', NULL, 'Carte d\'électeur', 'Kananga', '2023-05-11', '235', NULL, 'Vente des babouche', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 06:13:53', '2026-03-17 06:13:53'),
('CL-EBENKGA-26-00471', 'ZON-EBENKGA-26-00006', 'TSHONDA', 'OMASOMBO', 'NATHALIS', NULL, '0994545213', 'M', '2000-03-17', 'Kananga', 'Kananga', 'Célibataire', NULL, 'Carte d\'électeur', 'kananga', '2023-02-08', '2549488448', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 06:15:08', '2026-03-17 06:15:08'),
('CL-EBENKGA-26-00472', 'ZON-EBENKGA-26-00006', 'PASUA NZAMBI', 'BEYA', 'GÉDÉON', NULL, '0977746083', 'F', '1999-03-09', 'Kananga', 'Av nganza, Q/PLATEAU C/KANANGA', 'Célibataire', NULL, 'Carte d\'électeur', 'Kananga', '2023-03-03', '2546487884', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 06:18:50', '2026-03-17 06:18:50'),
('CL-EBENKGA-26-00473', 'ZON-EBENKGA-26-00003', 'KAMBILA', 'KAMBILA', 'BERNARD SAMUEL', NULL, '0994693602', 'M', '1860-10-04', 'Demba', 'Av Mukendi Q/ MABONDO C/lukonga n° 15', 'Marié', 'Marguerite tshiame', 'Carte nationale d\'identité', 'Kananga', '2023-02-07', '34936572562', NULL, 'Pasteur', NULL, NULL, NULL, NULL, NULL, '30', NULL, NULL, NULL, '2026-03-17 06:20:16', '2026-03-17 06:20:16'),
('CL-EBENKGA-26-00474', 'ZON-EBENKGA-26-00001', 'Ngalula', 'Malu', 'Sylvie', NULL, '0977041077', 'F', '2002-07-07', 'Kananga', 'Du canal Malandji kananga', 'Marié', 'Trésor Kanku', 'Carte d\'électeur', 'Kananga', '2025-03-20', '720', NULL, 'Boutique d\'habillement', 'Commerce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 06:33:26', '2026-03-17 06:33:26'),
('CL-EBENKGA-26-00475', 'ZON-EBENKGA-26-00006', 'KAYEMBE', 'MPUTU', 'DAVID', 'dv.kayemb@gmail.com', '+243844160909', 'M', '1980-11-19', 'KANANGA', 'AV DU RAIL N°52 KELEKELE', 'Célibataire', NULL, 'Carte d\'électeur', 'KANANGA', '2023-02-02', '34947245742782628', 'clients/1773734521_1767781348104.jpeg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17 07:02:01', '2026-03-17 07:02:01');

-- --------------------------------------------------------

--
-- Structure de la table `tb_cloture_caisse`
--

CREATE TABLE `tb_cloture_caisse` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `guichet_id` bigint(20) UNSIGNED NOT NULL,
  `devise_code` varchar(3) NOT NULL,
  `solde_comptable` decimal(18,2) NOT NULL,
  `solde_physique` decimal(18,2) NOT NULL,
  `ecart_caisse` decimal(18,2) NOT NULL,
  `detail_billetage` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`detail_billetage`)),
  `motif_ecart` text DEFAULT NULL COMMENT 'Justification requise si écart ≠ 0',
  `statut_ecart` enum('EQUILIBRE','EXCEDENT','DEFICIT') NOT NULL DEFAULT 'EQUILIBRE' COMMENT 'Résultat de la confrontation physique / système',
  `statut_validation` enum('EN_ATTENTE','VALIDE','REJETE') NOT NULL DEFAULT 'EN_ATTENTE' COMMENT 'Statut de validation par le superviseur',
  `validateur_matricule` varchar(20) DEFAULT NULL COMMENT 'Matricule du superviseur ayant validé',
  `date_validation` timestamp NULL DEFAULT NULL COMMENT 'Date/heure de validation par le superviseur',
  `observations_superviseur` text DEFAULT NULL COMMENT 'Commentaire du superviseur lors de la validation',
  `agent_cloturant` varchar(50) NOT NULL,
  `date_cloture` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_cloture_caisse`
--

INSERT INTO `tb_cloture_caisse` (`id`, `guichet_id`, `devise_code`, `solde_comptable`, `solde_physique`, `ecart_caisse`, `detail_billetage`, `motif_ecart`, `statut_ecart`, `statut_validation`, `validateur_matricule`, `date_validation`, `observations_superviseur`, `agent_cloturant`, `date_cloture`) VALUES
(22, 26, 'CDF', 250000.00, 250000.00, 0.00, '{\"1000\":5,\"5000\":9,\"10000\":4,\"20000\":8}', NULL, 'EQUILIBRE', 'REJETE', 'AG-EBENKGA-26-00002', '2026-03-13 13:56:28', '[Rejet CDF] jhj', 'AG-EBENKGA-26-00008', '2026-03-11 07:27:52'),
(23, 26, 'USD', 0.00, 0.00, 0.00, '[]', NULL, 'EQUILIBRE', 'REJETE', 'AG-EBENKGA-26-00002', '2026-03-13 13:56:28', '[Rejet CDF] jhj', 'AG-EBENKGA-26-00008', '2026-03-11 07:27:52'),
(24, 26, 'CDF', 0.00, 0.00, 0.00, '[]', NULL, 'EQUILIBRE', 'VALIDE', 'AG-EBENKGA-26-00002', '2026-03-14 14:41:01', NULL, 'AG-EBENKGA-26-00008', '2026-03-13 17:44:04'),
(25, 26, 'USD', 0.00, 0.00, 0.00, '[]', NULL, 'EQUILIBRE', 'VALIDE', 'AG-EBENKGA-26-00002', '2026-03-14 14:41:08', NULL, 'AG-EBENKGA-26-00008', '2026-03-13 17:44:04'),
(26, 26, 'CDF', 0.00, 0.00, 0.00, '[]', NULL, 'EQUILIBRE', 'VALIDE', 'AG-EBENKGA-26-00002', '2026-03-17 07:12:10', NULL, 'AG-EBENKGA-26-00008', '2026-03-14 19:01:19'),
(27, 26, 'USD', 0.00, 0.00, 0.00, '[]', NULL, 'EQUILIBRE', 'VALIDE', 'AG-EBENKGA-26-00002', '2026-03-17 07:12:17', NULL, 'AG-EBENKGA-26-00008', '2026-03-14 19:01:19'),
(28, 31, 'CDF', 0.00, 0.00, 0.00, '[]', NULL, 'EQUILIBRE', 'EN_ATTENTE', NULL, NULL, NULL, 'AG-EBENKGA-26-00002', '2026-03-17 07:45:08'),
(29, 31, 'USD', 0.00, 0.00, 0.00, '[]', NULL, 'EQUILIBRE', 'EN_ATTENTE', NULL, NULL, NULL, 'AG-EBENKGA-26-00002', '2026-03-17 07:45:08'),
(30, 20, 'CDF', 0.00, 0.00, 0.00, '[]', NULL, 'EQUILIBRE', 'REJETE', 'AG-EBENKGA-26-00002', '2026-03-17 07:46:52', '[Rejet CDF] veuillez revoir votre billatge', 'AG-EBENKGA-26-00011', '2026-03-17 07:45:29'),
(31, 20, 'USD', 0.00, 0.00, 0.00, '[]', NULL, 'EQUILIBRE', 'REJETE', 'AG-EBENKGA-26-00002', '2026-03-17 07:46:52', '[Rejet CDF] veuillez revoir votre billatge', 'AG-EBENKGA-26-00011', '2026-03-17 07:45:29'),
(32, 20, 'CDF', 0.00, 0.00, 0.00, '[]', NULL, 'EQUILIBRE', 'EN_ATTENTE', NULL, NULL, NULL, 'AG-EBENKGA-26-00011', '2026-03-17 07:47:40'),
(33, 20, 'USD', 0.00, 0.00, 0.00, '[]', NULL, 'EQUILIBRE', 'EN_ATTENTE', NULL, NULL, NULL, 'AG-EBENKGA-26-00011', '2026-03-17 07:47:40');

-- --------------------------------------------------------

--
-- Structure de la table `tb_commission_rules`
--

CREATE TABLE `tb_commission_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `libelle` varchar(150) NOT NULL,
  `code_operation` varchar(50) NOT NULL DEFAULT 'TOUS',
  `type_compte` varchar(20) NOT NULL DEFAULT 'TOUS',
  `type_guichet` varchar(20) NOT NULL DEFAULT 'TOUS',
  `devise_code` char(3) DEFAULT NULL,
  `code_zone` varchar(50) DEFAULT NULL,
  `portefeuille_id` bigint(20) UNSIGNED DEFAULT NULL,
  `montant_min` decimal(18,2) DEFAULT NULL,
  `montant_max` decimal(18,2) DEFAULT NULL,
  `mode_calcul` enum('FIXE','POURCENTAGE') NOT NULL,
  `valeur` decimal(18,4) NOT NULL,
  `priorite` int(10) UNSIGNED NOT NULL DEFAULT 100,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `est_actif` tinyint(1) NOT NULL DEFAULT 1,
  `observations` text DEFAULT NULL,
  `created_by_agent` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tb_commission_rules`
--

INSERT INTO `tb_commission_rules` (`id`, `libelle`, `code_operation`, `type_compte`, `type_guichet`, `devise_code`, `code_zone`, `portefeuille_id`, `montant_min`, `montant_max`, `mode_calcul`, `valeur`, `priorite`, `date_debut`, `date_fin`, `est_actif`, `observations`, `created_by_agent`, `created_at`, `updated_at`) VALUES
(1, 'FRAIS DE RETRAIT', 'RETRAIT', 'CC', 'TOUS', 'CDF', NULL, NULL, 31000.00, 60000.00, 'POURCENTAGE', 4.0000, 10, '2026-03-14', NULL, 1, NULL, 'AG-EBENKGA-26-00002', '2026-03-14 14:29:35', '2026-03-14 14:29:35');

-- --------------------------------------------------------

--
-- Structure de la table `tb_compta_ecritures`
--

CREATE TABLE `tb_compta_ecritures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `journal_id` bigint(20) UNSIGNED NOT NULL,
  `numero_compte` varchar(20) NOT NULL,
  `devise_code` varchar(3) DEFAULT NULL,
  `libelle_ligne` varchar(191) DEFAULT NULL,
  `debit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `ordre` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_compta_journaux`
--

CREATE TABLE `tb_compta_journaux` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code_journal` varchar(20) NOT NULL DEFAULT 'CAI',
  `reference_piece` varchar(80) NOT NULL,
  `transaction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type_piece` enum('OPERATION','ANNULATION','REGULARISATION') NOT NULL DEFAULT 'OPERATION',
  `devise_code` varchar(3) DEFAULT NULL,
  `libelle` varchar(191) NOT NULL,
  `statut` enum('COMPTABILISE','ANNULE') NOT NULL DEFAULT 'COMPTABILISE',
  `agent_matricule` varchar(50) DEFAULT NULL,
  `date_ecriture` timestamp NOT NULL DEFAULT current_timestamp(),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_comptes`
--

CREATE TABLE `tb_comptes` (
  `code_compte` varchar(64) NOT NULL,
  `client_matricule` varchar(191) NOT NULL,
  `devise` varchar(3) NOT NULL,
  `type` enum('CC','RMB','GTC','DAT','EAV') NOT NULL COMMENT 'CC=Compte Courant, RMB=Remboursement, GTC=Caution, DAT=Depot a Terme, EAV=Epargne et Vie',
  `portefeuille_id` bigint(20) UNSIGNED DEFAULT NULL,
  `solde_reel` decimal(18,2) NOT NULL DEFAULT 0.00,
  `solde_bloque` decimal(18,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_comptes`
--

INSERT INTO `tb_comptes` (`code_compte`, `client_matricule`, `devise`, `type`, `portefeuille_id`, `solde_reel`, `solde_bloque`, `created_at`, `updated_at`) VALUES
('243-52514-CC-00001XPR', 'CL-EBENKGA-26-00001', 'CDF', 'CC', NULL, 0.00, 0.00, '2026-03-11 07:19:32', '2026-03-13 18:16:31'),
('243-52514-CC-00002LKF', 'CL-EBENKGA-26-00002', 'CDF', 'CC', NULL, 0.00, 0.00, '2026-03-11 09:35:19', '2026-03-11 10:35:19'),
('243-52514-CC-00003RCT', 'CL-EBENKGA-26-00003', 'USD', 'CC', NULL, 0.00, 0.00, '2026-03-12 10:25:10', '2026-03-12 11:25:10'),
('243-52514-CC-00004PFT', 'CL-EBENKGA-26-00021', 'CDF', 'CC', NULL, 0.00, 0.00, '2026-03-13 14:20:37', '2026-03-13 15:20:37'),
('243-52514-CC-00005XPC', 'CL-EBENKGA-26-00076', 'CDF', 'CC', NULL, 0.00, 0.00, '2026-03-14 06:58:45', '2026-03-14 07:58:45'),
('243-52514-CC-00006FWG', 'CL-EBENKGA-26-00033', 'CDF', 'CC', NULL, 0.00, 0.00, '2026-03-14 07:00:24', '2026-03-14 08:00:24'),
('243-52514-CC-00007URJ', 'CL-EBENKGA-26-00009', 'CDF', 'CC', NULL, 0.00, 0.00, '2026-03-14 07:01:01', '2026-03-14 08:01:01'),
('243-52514-CC-00008ZHT', 'CL-EBENKGA-26-00123', 'USD', 'CC', NULL, 0.00, 0.00, '2026-03-15 09:36:52', '2026-03-15 10:36:52'),
('243-52514-CC-00009DOH', 'CL-EBENKGA-26-00475', 'CDF', 'CC', NULL, 0.00, 0.00, '2026-03-17 07:05:10', '2026-03-17 08:05:10'),
('243-52514-CC-00010OBR', 'CL-EBENKGA-26-00475', 'USD', 'CC', NULL, 0.00, 0.00, '2026-03-17 07:06:41', '2026-03-17 08:06:41'),
('243-52514-RMB-00001XOJ', 'CL-EBENKGA-26-00475', 'CDF', 'RMB', NULL, 0.00, 0.00, '2026-03-17 07:33:44', '2026-03-17 08:33:44');

-- --------------------------------------------------------

--
-- Structure de la table `tb_demandes_modification`
--

CREATE TABLE `tb_demandes_modification` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK vers tb_transactions',
  `reference_operation` varchar(60) DEFAULT NULL COMMENT 'Référence de l''opération initiale',
  `guichet_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'FK vers tb_caisses_guichets',
  `compte_code` varchar(60) DEFAULT NULL COMMENT 'Compte client concerné',
  `client_nom` varchar(200) DEFAULT NULL COMMENT 'Nom du client (dénormalisation audit)',
  `type_operation` varchar(30) DEFAULT NULL COMMENT 'Type original : DEPOT, RETRAIT...',
  `devise_code` varchar(10) DEFAULT NULL,
  `ancien_montant` decimal(15,2) DEFAULT NULL COMMENT 'Montant original',
  `anciennes_observations` text DEFAULT NULL COMMENT 'Observations originales',
  `type_demande` enum('MODIFICATION','SUPPRESSION') NOT NULL,
  `agent_matricule` varchar(60) DEFAULT NULL COMMENT 'Guichetier demandeur',
  `motif` text NOT NULL COMMENT 'Motif obligatoire',
  `nouveau_montant` decimal(15,2) DEFAULT NULL COMMENT 'Nouveau montant demandé',
  `nouvelles_observations` text DEFAULT NULL,
  `statut` enum('EN_ATTENTE','APPROUVEE','REJETEE') NOT NULL DEFAULT 'EN_ATTENTE',
  `superviseur_matricule` varchar(60) DEFAULT NULL COMMENT 'Superviseur ayant traité',
  `commentaire_superviseur` text DEFAULT NULL,
  `traitee_le` timestamp NULL DEFAULT NULL COMMENT 'Date de traitement',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_devises`
--

CREATE TABLE `tb_devises` (
  `code_iso` varchar(3) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `symbole` varchar(5) NOT NULL,
  `est_reference` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_devises`
--

INSERT INTO `tb_devises` (`code_iso`, `nom`, `symbole`, `est_reference`, `created_at`, `updated_at`) VALUES
('CDF', 'Franc Congolais', 'Fc', 1, '2026-03-06 09:09:58', NULL),
('EUR', 'Euro', 'EUR', 0, '2026-03-06 09:09:58', NULL),
('USD', 'Dollar Américain', '$', 0, '2026-03-06 09:09:58', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `tb_mouvements_inter_caisses`
--

CREATE TABLE `tb_mouvements_inter_caisses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `guichet_source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `guichet_dest_id` bigint(20) UNSIGNED DEFAULT NULL,
  `agent_initiateur` varchar(50) NOT NULL,
  `type_flux` enum('ALIMENTATION','DEGAGEMENT','TRANSFERT','DEMANDE_APPRO','DOTATION_MOBILE','REVERSEMENT_MOBILE') NOT NULL,
  `montant` decimal(18,2) NOT NULL,
  `devise_code` varchar(3) NOT NULL,
  `reference_bordereau` varchar(50) DEFAULT NULL,
  `date_mouvement` timestamp NOT NULL DEFAULT current_timestamp(),
  `statut` enum('EN_ATTENTE','VALIDE','CONFIRME','ANNULE') NOT NULL DEFAULT 'CONFIRME',
  `validateur_matricule` varchar(50) DEFAULT NULL,
  `observations` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_mouvements_inter_caisses`
--

INSERT INTO `tb_mouvements_inter_caisses` (`id`, `guichet_source_id`, `guichet_dest_id`, `agent_initiateur`, `type_flux`, `montant`, `devise_code`, `reference_bordereau`, `date_mouvement`, `statut`, `validateur_matricule`, `observations`) VALUES
(44, NULL, 1, 'AG-EBENKGA-26-00002', 'ALIMENTATION', 1000.00, 'CDF', 'APP-20260314-152140-983718-263C', '2026-03-14 14:21:40', 'CONFIRME', 'AG-EBENKGA-26-00002', 'Approvisionnement externe — BANQUE : APPRO COF'),
(45, 26, 1, 'AG-EBENKGA-26-00002', 'DEGAGEMENT', 0.00, 'CDF', 'DEG-20260314-154101-GOO7-CDF', '2026-03-14 14:41:01', 'CONFIRME', 'AG-EBENKGA-26-00002', 'Dégagement CDF — clôture GOO7'),
(46, 26, 1, 'AG-EBENKGA-26-00002', 'DEGAGEMENT', 0.00, 'USD', 'DEG-20260314-154108-GOO7-USD', '2026-03-14 14:41:08', 'CONFIRME', 'AG-EBENKGA-26-00002', 'Dégagement USD — clôture GOO7'),
(47, 26, 1, 'AG-EBENKGA-26-00002', 'DEGAGEMENT', 0.00, 'CDF', 'DEG-20260317-081210-GOO7-CDF', '2026-03-17 07:12:11', 'CONFIRME', 'AG-EBENKGA-26-00002', 'Dégagement CDF — clôture GOO7'),
(48, 26, 1, 'AG-EBENKGA-26-00002', 'DEGAGEMENT', 0.00, 'USD', 'DEG-20260317-081217-GOO7-USD', '2026-03-17 07:12:17', 'CONFIRME', 'AG-EBENKGA-26-00002', 'Dégagement USD — clôture GOO7');

-- --------------------------------------------------------

--
-- Structure de la table `tb_permissions`
--

CREATE TABLE `tb_permissions` (
  `code` varchar(20) NOT NULL,
  `nom` varchar(191) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
('EBEN-PER5', 'Gérer les permissions', 'Gestion des permissions', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER6', 'Voir RH', 'Accès au module RH', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER7', 'Créer agent', 'Création d\'un nouvel agent', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER8', 'Modifier agent', 'Modification d\'un agent', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-PER9', 'Affectations', 'Gestion des affectations', '2026-03-06 12:02:52', '2026-03-06 12:02:52');

-- --------------------------------------------------------

--
-- Structure de la table `tb_plan_comptable`
--

CREATE TABLE `tb_plan_comptable` (
  `numero_compte` varchar(20) NOT NULL,
  `libelle` varchar(191) NOT NULL,
  `type_compte` enum('ACTIF','PASSIF','CHARGE','PRODUIT') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_portefeuilles_agents`
--

CREATE TABLE `tb_portefeuilles_agents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `agent_matricule` varchar(50) NOT NULL,
  `nom_portefeuille` varchar(100) NOT NULL,
  `taux_commission_agent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_postes`
--

CREATE TABLE `tb_postes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_postes`
--

INSERT INTO `tb_postes` (`id`, `service_id`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Administrateur Système', 'Poste réservé au compte administrateur du système', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
(12, 11, 'Superviseur de Crédit', 'Responsable de crédit', '2026-03-10 18:25:58', '2026-03-10 18:25:58'),
(13, 11, 'Agent de Crédit', 'Responsable d\'un portefeuille', '2026-03-10 18:26:31', '2026-03-10 18:26:31'),
(16, 11, 'Agent  Commercial', 'Responsable d\'une zone', '2026-03-10 19:22:57', '2026-03-10 19:22:57'),
(17, 1, 'Gérant', 'Il s\'occupe de la gérance de la coopérative', '2026-03-14 19:04:14', '2026-03-14 19:04:14'),
(18, 11, 'CHARGÉ DES OPÉRATIONS', NULL, '2026-03-14 19:28:38', '2026-03-14 19:28:38');

-- --------------------------------------------------------

--
-- Structure de la table `tb_roles`
--

CREATE TABLE `tb_roles` (
  `code` varchar(20) NOT NULL,
  `nom` varchar(191) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_roles`
--

INSERT INTO `tb_roles` (`code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
('EBEN-ROL1', 'Administrateur', 'Accès total au système', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
('EBEN-ROL10', 'Encodage Cleint', NULL, '2026-03-09 09:54:46', '2026-03-09 09:54:46'),
('EBEN-ROL11', 'Charge des opérations', 'responsable des produits', '2026-03-13 13:49:05', '2026-03-13 13:49:05'),
('EBEN-ROL12', 'GÉRANT', 'Il s\'occupe de la gérance de la coopérative', '2026-03-14 19:05:13', '2026-03-14 19:05:13'),
('EBEN-ROL2', 'Caissier', 'Gestion caisse et guichet', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
('EBEN-ROL3', 'Directeur', 'Supervision générale', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
('EBEN-ROL4', 'Agent RH', 'Gestion des ressources humaines', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
('EBEN-ROL5', 'Superviseur', 'Supervision opérationnelle', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
('EBEN-ROL6', 'Chargé de crédit', 'Gestion complète des dossiers crédit, épargne et comptes clients', '2026-03-06 11:36:28', '2026-03-06 11:36:28'),
('EBEN-ROL7', 'Comptable', 'Comptabilité, rapports financiers, validation des écritures', '2026-03-06 11:36:28', '2026-03-06 11:36:28'),
('EBEN-ROL8', 'Trésorier', 'Gestion complète du coffre-fort central, approvisionnements et transferts', '2026-03-07 10:32:25', '2026-03-07 10:32:25'),
('EBEN-ROL9', 'AGENT COMMERCIAL', 'C\'est agent d\'un agent commercial sur terrain', '2026-03-09 09:10:20', '2026-03-09 09:10:20');

-- --------------------------------------------------------

--
-- Structure de la table `tb_role_permission`
--

CREATE TABLE `tb_role_permission` (
  `role_code` varchar(20) NOT NULL,
  `permission_code` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
('EBEN-ROL1', 'EBEN-PER5', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER6', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER7', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER8', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL1', 'EBEN-PER9', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL10', 'EBEN-PER15', '2026-03-09 09:55:25', '2026-03-09 09:55:25'),
('EBEN-ROL10', 'EBEN-PER16', '2026-03-09 09:55:06', '2026-03-09 09:55:06'),
('EBEN-ROL10', 'EBEN-PER17', '2026-03-09 09:55:31', '2026-03-09 09:55:31'),
('EBEN-ROL11', 'EBEN-PER15', '2026-03-13 13:49:59', '2026-03-13 13:49:59'),
('EBEN-ROL11', 'EBEN-PER18', '2026-03-13 13:50:11', '2026-03-13 13:50:11'),
('EBEN-ROL11', 'EBEN-PER19', '2026-03-13 13:50:15', '2026-03-13 13:50:15'),
('EBEN-ROL11', 'EBEN-PER27', '2026-03-13 13:50:48', '2026-03-13 13:50:48'),
('EBEN-ROL11', 'EBEN-PER28', '2026-03-13 13:50:51', '2026-03-13 13:50:51'),
('EBEN-ROL11', 'EBEN-PER29', '2026-03-13 13:50:55', '2026-03-13 13:50:55'),
('EBEN-ROL12', 'EBEN-PER18', '2026-03-14 19:06:21', '2026-03-14 19:06:21'),
('EBEN-ROL12', 'EBEN-PER19', '2026-03-14 19:06:24', '2026-03-14 19:06:24'),
('EBEN-ROL12', 'EBEN-PER25', '2026-03-14 19:06:36', '2026-03-14 19:06:36'),
('EBEN-ROL12', 'EBEN-PER26', '2026-03-14 19:06:53', '2026-03-14 19:06:53'),
('EBEN-ROL12', 'EBEN-PER27', '2026-03-14 19:07:01', '2026-03-14 19:07:01'),
('EBEN-ROL12', 'EBEN-PER28', '2026-03-14 19:07:04', '2026-03-14 19:07:04'),
('EBEN-ROL12', 'EBEN-PER29', '2026-03-14 19:07:04', '2026-03-14 19:07:04'),
('EBEN-ROL12', 'EBEN-PER30', '2026-03-14 19:07:10', '2026-03-14 19:07:10'),
('EBEN-ROL12', 'EBEN-PER31', '2026-03-14 19:07:10', '2026-03-14 19:07:10'),
('EBEN-ROL12', 'EBEN-PER32', '2026-03-14 19:07:10', '2026-03-14 19:07:10'),
('EBEN-ROL12', 'EBEN-PER33', '2026-03-14 19:07:10', '2026-03-14 19:07:10'),
('EBEN-ROL12', 'EBEN-PER34', '2026-03-14 19:07:10', '2026-03-14 19:07:10'),
('EBEN-ROL12', 'EBEN-PER35', '2026-03-14 19:07:10', '2026-03-14 19:07:10'),
('EBEN-ROL12', 'EBEN-PER36', '2026-03-14 19:07:21', '2026-03-14 19:07:21'),
('EBEN-ROL12', 'EBEN-PER37', '2026-03-14 19:07:21', '2026-03-14 19:07:21'),
('EBEN-ROL12', 'EBEN-PER38', '2026-03-14 19:07:21', '2026-03-14 19:07:21'),
('EBEN-ROL12', 'EBEN-PER7', '2026-03-14 19:05:47', '2026-03-14 19:05:47'),
('EBEN-ROL12', 'EBEN-PER9', '2026-03-14 19:05:55', '2026-03-14 19:05:55'),
('EBEN-ROL2', 'EBEN-PER10', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER11', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER12', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER13', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER14', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER15', '2026-03-06 12:42:55', '2026-03-06 12:42:55'),
('EBEN-ROL2', 'EBEN-PER18', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER20', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER21', '2026-03-06 12:42:08', '2026-03-06 12:42:08'),
('EBEN-ROL2', 'EBEN-PER22', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
('EBEN-ROL2', 'EBEN-PER23', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
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
('EBEN-ROL3', 'EBEN-PER6', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
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
('EBEN-ROL5', 'EBEN-PER6', '2026-03-06 12:02:52', '2026-03-06 12:02:52'),
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
('EBEN-ROL9', 'EBEN-PER10', '2026-03-14 14:39:58', '2026-03-14 14:39:58'),
('EBEN-ROL9', 'EBEN-PER11', '2026-03-14 14:39:58', '2026-03-14 14:39:58'),
('EBEN-ROL9', 'EBEN-PER12', '2026-03-14 14:39:58', '2026-03-14 14:39:58'),
('EBEN-ROL9', 'EBEN-PER13', '2026-03-14 14:39:58', '2026-03-14 14:39:58'),
('EBEN-ROL9', 'EBEN-PER14', '2026-03-14 14:39:58', '2026-03-14 14:39:58'),
('EBEN-ROL9', 'EBEN-PER15', '2026-03-13 16:59:03', '2026-03-13 16:59:03'),
('EBEN-ROL9', 'EBEN-PER16', '2026-03-09 09:14:37', '2026-03-09 09:14:37'),
('EBEN-ROL9', 'EBEN-PER22', '2026-03-13 17:42:15', '2026-03-13 17:42:15'),
('EBEN-ROL9', 'EBEN-PER23', '2026-03-13 17:42:18', '2026-03-13 17:42:18'),
('EBEN-ROL9', 'EBEN-PER24', '2026-03-14 14:40:09', '2026-03-14 14:40:09'),
('EBEN-ROL9', 'EBEN-PER26', '2026-03-14 14:40:17', '2026-03-14 14:40:17');

-- --------------------------------------------------------

--
-- Structure de la table `tb_role_user`
--

CREATE TABLE `tb_role_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_code` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_role_user`
--

INSERT INTO `tb_role_user` (`id`, `user_id`, `role_code`, `created_at`, `updated_at`) VALUES
(22, 1, 'EBEN-ROL1', '2026-03-10 18:17:27', '2026-03-10 18:17:27'),
(23, 11, 'EBEN-ROL1', NULL, NULL),
(24, 17, 'EBEN-ROL9', NULL, NULL),
(25, 15, 'EBEN-ROL9', NULL, NULL),
(26, 16, 'EBEN-ROL9', NULL, NULL),
(27, 13, 'EBEN-ROL9', NULL, NULL),
(28, 12, 'EBEN-ROL9', NULL, NULL),
(29, 18, 'EBEN-ROL9', NULL, NULL),
(30, 14, 'EBEN-ROL9', NULL, NULL),
(31, 19, 'EBEN-ROL9', NULL, NULL),
(32, 20, 'EBEN-ROL11', NULL, NULL),
(33, 21, 'EBEN-ROL12', NULL, NULL),
(34, 22, 'EBEN-ROL9', NULL, NULL),
(35, 23, 'EBEN-ROL9', NULL, NULL),
(36, 24, 'EBEN-ROL9', NULL, NULL),
(37, 25, 'EBEN-ROL9', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `tb_services`
--

CREATE TABLE `tb_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_services`
--

INSERT INTO `tb_services` (`id`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Direction Générale', 'Administration centrale du système', '2026-03-06 09:09:57', '2026-03-06 09:09:57'),
(3, 'Caisse', NULL, '2026-03-06 13:53:33', '2026-03-06 13:53:33'),
(11, 'DIRECTION DES OPERATIONS', 'chargé de la production', '2026-03-09 19:16:11', '2026-03-09 19:16:11'),
(12, 'CONSEIL D\'ADMINISTRATION', 'membre d\'organes', '2026-03-10 18:31:26', '2026-03-10 18:31:26');

-- --------------------------------------------------------

--
-- Structure de la table `tb_taux_echanges`
--

CREATE TABLE `tb_taux_echanges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `devise_source` varchar(3) NOT NULL,
  `devise_destination` varchar(3) NOT NULL,
  `taux` decimal(18,4) NOT NULL,
  `date_application` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `tb_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `compte_code` varchar(64) DEFAULT NULL,
  `agent_matricule` varchar(50) NOT NULL,
  `guichet_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Guichet ayant effectué l''opération',
  `devise_code` char(3) DEFAULT NULL COMMENT 'Devise de la transaction (CDF, USD, EUR…)',
  `devise_dest` char(3) DEFAULT NULL,
  `montant_dest` decimal(18,2) DEFAULT NULL,
  `taux_change` decimal(14,6) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `statut` enum('CONFIRME','ANNULE') NOT NULL DEFAULT 'CONFIRME',
  `date_operation` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` enum('DEPOT','RETRAIT','VIREMENT','REMBOURSEMENT','CHANGE','PAIEMENT') NOT NULL,
  `montant` decimal(18,2) NOT NULL,
  `montant_commission_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `solde_compte_avant` decimal(18,2) DEFAULT NULL,
  `solde_compte_apres` decimal(18,2) DEFAULT NULL,
  `montant_total_client` decimal(18,2) DEFAULT NULL,
  `montant_net_client` decimal(18,2) DEFAULT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_transactions`
--

INSERT INTO `tb_transactions` (`id`, `compte_code`, `agent_matricule`, `guichet_id`, `devise_code`, `devise_dest`, `montant_dest`, `taux_change`, `observations`, `statut`, `date_operation`, `type`, `montant`, `montant_commission_total`, `solde_compte_avant`, `solde_compte_apres`, `montant_total_client`, `montant_net_client`, `reference`, `created_at`, `updated_at`) VALUES
(22, '243-52514-CC-00001XPR', 'AG-EBENKGA-26-00008', 26, 'CDF', NULL, NULL, NULL, 'paie du mois de janvier 2026', 'CONFIRME', '2026-03-11 07:21:22', 'DEPOT', 200000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260311-082122-AG-E', '2026-03-11 07:21:22', '2026-03-11 07:21:22'),
(23, '243-52514-CC-00001XPR', 'AG-EBENKGA-26-00008', 26, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-11 07:22:45', 'DEPOT', 50000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260311-082245-AG-E', '2026-03-11 07:22:45', '2026-03-11 07:22:45'),
(24, '243-52514-CC-00001XPR', 'AG-EBENKGA-26-00008', 26, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-13 17:13:32', 'RETRAIT', 250000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260313-181332-AG-E', '2026-03-13 17:13:32', '2026-03-13 17:13:32'),
(25, '243-52514-CC-00001XPR', 'AG-EBENKGA-26-00008', 26, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-13 17:15:54', 'DEPOT', 5000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260313-181554-AG-E', '2026-03-13 17:15:54', '2026-03-13 17:15:54'),
(26, '243-52514-CC-00001XPR', 'AG-EBENKGA-26-00008', 26, 'CDF', NULL, NULL, NULL, NULL, 'CONFIRME', '2026-03-13 17:16:31', 'RETRAIT', 5000.00, 0.00, NULL, NULL, NULL, NULL, 'OP-20260313-181631-AG-E', '2026-03-13 17:16:31', '2026-03-13 17:16:31');

-- --------------------------------------------------------

--
-- Structure de la table `tb_transaction_commissions`
--

CREATE TABLE `tb_transaction_commissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `commission_rule_id` bigint(20) UNSIGNED DEFAULT NULL,
  `libelle` varchar(150) NOT NULL,
  `code_operation` varchar(50) NOT NULL,
  `type_compte` varchar(20) DEFAULT NULL,
  `type_guichet` varchar(20) DEFAULT NULL,
  `devise_code` char(3) DEFAULT NULL,
  `code_zone` varchar(50) DEFAULT NULL,
  `portefeuille_id` bigint(20) UNSIGNED DEFAULT NULL,
  `mode_calcul` enum('FIXE','POURCENTAGE') NOT NULL,
  `valeur_snapshot` decimal(18,4) NOT NULL,
  `base_calcul` decimal(18,2) NOT NULL DEFAULT 0.00,
  `montant_commission` decimal(18,2) NOT NULL DEFAULT 0.00,
  `date_application` timestamp NOT NULL DEFAULT current_timestamp(),
  `agent_matricule` varchar(50) DEFAULT NULL,
  `guichet_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_zones`
--

CREATE TABLE `tb_zones` (
  `code_zone` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `agent_commercial_matricule` varchar(50) NOT NULL,
  `commune` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_zones`
--

INSERT INTO `tb_zones` (`code_zone`, `nom`, `agent_commercial_matricule`, `commune`, `created_at`, `updated_at`) VALUES
('ZON-EBENKGA-26-00001', 'BATETELA  A', 'AG-EBENKGA-26-00011', 'Katoka', '2026-03-10 19:07:14', '2026-03-10 19:07:14'),
('ZON-EBENKGA-26-00002', 'BATETELA B', 'AG-EBENKGA-26-00003', 'Katoka', '2026-03-10 19:08:01', '2026-03-10 19:08:01'),
('ZON-EBENKGA-26-00003', 'BATETELA C', 'AG-EBENKGA-26-00005', 'Kanange', '2026-03-10 19:08:46', '2026-03-10 19:08:46'),
('ZON-EBENKGA-26-00004', 'KAMAYI', 'AG-EBENKGA-26-00007', 'Kanange', '2026-03-10 19:09:22', '2026-03-10 19:09:22'),
('ZON-EBENKGA-26-00005', 'OFFIDA', 'AG-EBENKGA-26-00009', 'Kanange', '2026-03-10 19:09:42', '2026-03-10 19:09:42'),
('ZON-EBENKGA-26-00006', 'MAGAR', 'AG-EBENKGA-26-00008', 'Kanange', '2026-03-10 19:10:27', '2026-03-10 19:10:27'),
('ZON-EBENKGA-26-00007', 'STADE', 'AG-EBENKGA-26-00010', 'Katoka', '2026-03-10 19:11:25', '2026-03-10 19:11:25'),
('ZON-EBENKGA-26-00008', 'TSHISELEKA', 'AG-EBENKGA-26-00012', 'Katoka', '2026-03-10 20:04:48', '2026-03-10 20:04:48'),
('ZON-EBENKGA-26-00009', 'Zone Test', 'AG-EBENKGA-26-00013', 'Kanange', '2026-03-15 09:29:22', '2026-03-15 09:29:22'),
('ZON-EBENKGA-26-00010', 'DAKU YABISO', 'AG-EBENKGA-26-00014', 'Katoka', '2026-03-15 21:02:52', '2026-03-15 21:02:52'),
('ZON-EBENKGA-26-00011', 'MARCHE CENTRAL', 'AG-EBENKGA-26-00015', 'Ndesha', '2026-03-16 07:50:36', '2026-03-16 07:50:36');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `agent_matricule` varchar(50) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `etat` varchar(20) NOT NULL DEFAULT 'actif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `agent_matricule`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `etat`, `created_at`, `updated_at`) VALUES
(1, 'AG-EBENKGA-26-00001', 'bmb', 'bmb@bmb.cd', '2026-03-06 09:09:58', '$2y$12$b1CZZgerOu0kbKn5pnzuaOY5gffx18oF2PVj5mhGyw.d53G8XYqay', NULL, 'actif', '2026-03-06 09:09:58', '2026-03-06 09:09:58'),
(11, 'AG-EBENKGA-26-00002', 'dav', 'dv.kayemb@gmail.com', NULL, '$2y$12$Zr2n0ZphA8lTXVOvS4QlEuu9KnXEfVttuPcWG9DBJvJA3SwzdbvWC', 'qZuuT6Ba9zYIAIWSUNS0FY3RhNMErn7xHMy0ouvGp3oLfVB5kJTC8TADnSIJ', 'actif', '2026-03-10 18:23:05', '2026-03-10 18:23:05'),
(12, 'AG-EBENKGA-26-00011', 'KAPINGA', 'rosekapinga1999@gmail.com', NULL, '$2y$12$gqz3yhpaCMgUUNsSPs34rOPps7cM00nzj91h14H3ON9gvUxdSpWgG', NULL, 'actif', '2026-03-10 19:31:36', '2026-03-10 19:31:36'),
(13, 'AG-EBENKGA-26-00010', 'DJU', 'boniokito49@gmail.com', NULL, '$2y$12$cNjjQXmDSnOK./NWsscLK.ZPyXoEZMP/QmAHOnErei8FqnzDiZML.', NULL, 'actif', '2026-03-10 19:33:17', '2026-03-10 19:33:17'),
(14, 'AG-EBENKGA-26-00009', 'Lukeng', 'jp@gmail.com', NULL, '$2y$12$cuipAk0mima9sSolDMzt4eZuPoxiMEhSK4AkGFOHj4zhF8ByhdYqG', NULL, 'inactif', '2026-03-10 19:37:13', '2026-03-16 07:15:30'),
(15, 'AG-EBENKGA-26-00008', 'BKT', 'buyambatherese@gmail.com', NULL, '$2y$12$rL461R3xYceXs.mrOe2nPOSU/MH6jLXyn2cY2G1y/.d0UNsr57zp2', NULL, 'actif', '2026-03-10 19:38:53', '2026-03-10 19:38:53'),
(16, 'AG-EBENKGA-26-00005', 'DIBATAYI', 'mdibatayi@gmail.com', NULL, '$2y$12$QFccZNo.Z8EwbOBCuJEnpuDyXmKkU6HTsUn.T/CoGqlwktfZT8S2O', NULL, 'actif', '2026-03-10 19:40:59', '2026-03-10 19:40:59'),
(17, 'AG-EBENKGA-26-00003', 'BILONDAK', 'margueritepatience77@gmail.com', NULL, '$2y$12$U08Nk26osCGiDcee1vQtle9RMfkAs5o6GOd.oGlKF9k0xD4d0Ymta', NULL, 'actif', '2026-03-10 19:44:12', '2026-03-10 19:44:12'),
(18, 'AG-EBENKGA-26-00012', 'KAZK', 'gracekazika50@gmail.com', NULL, '$2y$12$rmdYuY6yZ1svwZ3EWaxmLOVHWirFzHVToSQQFsJ9GhXzK06Mw2GC6', NULL, 'actif', '2026-03-10 20:09:44', '2026-03-17 05:28:13'),
(19, 'AG-EBENKGA-26-00007', 'NDJOKO', 'jeanjacquesndjoko95@gmail.com', NULL, '$2y$12$yETM19.j8ou78fhb171OD.kXeoCGgKxObnDlAllqF3Cd4oNwS0iju', NULL, 'actif', '2026-03-13 13:41:17', '2026-03-13 13:41:17'),
(20, 'AG-EBENKGA-26-00006', 'dany', 'schadraclukadi@gmail.com', NULL, '$2y$12$4bpapmuYs9tgdIQevdD0Hu166hdMtxXPIQNVKN3NpsB3r99Br6Fla', NULL, 'actif', '2026-03-13 14:12:36', '2026-03-13 14:18:52'),
(21, 'AG-EBENKGA-26-00004', 'Bonel', 'augustinmuyaya@gmail.com', NULL, '$2y$12$662Ft9MIEIvJ.iynN.tQiuw.BTtG5GQZomde0c1ko8zNJErgz75FK', NULL, 'actif', '2026-03-14 19:11:44', '2026-03-16 19:03:27'),
(22, 'AG-EBENKGA-26-00013', 'test', 'test@gmail.com', NULL, '$2y$12$S9HvJ2PyMudAn.BwDmUEiuQJ8fXmGrUkiRTxPGdafgc1rXsVcM0HG', NULL, 'actif', '2026-03-15 09:24:55', '2026-03-15 09:24:55'),
(23, 'AG-EBENKGA-26-00014', 'DIBUE', 'johnmalazi50@gmail.com', NULL, '$2y$12$pYKjl.LUQ9MKoLs8S7sCqO6AqXRuHwfq.Zlf3M16ljaZZ7SalwxF6', NULL, 'actif', '2026-03-15 13:38:38', '2026-03-15 13:38:38'),
(24, 'AG-EBENKGA-26-00009', 'LJP', 'jeampy@gmail.com', NULL, '$2y$12$MPvneFXjwHXTW/LhwyFf7.sKgn.ySN/VieJLJ50gZ67vB6EqRoiKO', NULL, 'actif', '2026-03-16 07:16:29', '2026-03-16 07:20:15'),
(25, 'AG-EBENKGA-26-00015', 'MULUMBA', 'francoismulumba@gmail.com', NULL, '$2y$12$S2WiAyjoKcLGf5BxJDanku7N3vSfzMT0hR1iSafXYpLAymINRyfKW', NULL, 'actif', '2026-03-16 07:29:02', '2026-03-16 07:29:02');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `tb_affectations`
--
ALTER TABLE `tb_affectations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tb_affectations_agent_matricule_foreign` (`agent_matricule`),
  ADD KEY `tb_affectations_poste_id_foreign` (`poste_id`),
  ADD KEY `fk_affectation_guichet` (`guichet_id`);

--
-- Index pour la table `tb_agents`
--
ALTER TABLE `tb_agents`
  ADD PRIMARY KEY (`matricule`);

--
-- Index pour la table `tb_caisses_guichets`
--
ALTER TABLE `tb_caisses_guichets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tb_caisses_guichets_code_guichet_unique` (`code_guichet`);

--
-- Index pour la table `tb_caisses_guichets_soldes`
--
ALTER TABLE `tb_caisses_guichets_soldes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_guichet_devise` (`guichet_id`,`devise_code`),
  ADD KEY `fk_solde_devise` (`devise_code`);

--
-- Index pour la table `tb_clients`
--
ALTER TABLE `tb_clients`
  ADD UNIQUE KEY `tb_clients_matricule_unique` (`matricule`),
  ADD UNIQUE KEY `uq_tb_clients_piece_type_num` (`type_piece_identite`,`numero_piece_identite`),
  ADD UNIQUE KEY `uq_tb_clients_email` (`email`),
  ADD KEY `tb_zones_ibfk_11` (`code_zone`);

--
-- Index pour la table `tb_cloture_caisse`
--
ALTER TABLE `tb_cloture_caisse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cloture_guichet` (`guichet_id`),
  ADD KEY `fk_cloture_devise` (`devise_code`),
  ADD KEY `fk_cloture_agent` (`agent_cloturant`),
  ADD KEY `validateur_matricule` (`validateur_matricule`);

--
-- Index pour la table `tb_commission_rules`
--
ALTER TABLE `tb_commission_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_comm_rules_active_dates` (`est_actif`,`date_debut`,`date_fin`),
  ADD KEY `idx_comm_rules_scope` (`code_operation`,`type_compte`,`type_guichet`),
  ADD KEY `idx_comm_rules_context` (`devise_code`,`code_zone`,`portefeuille_id`),
  ADD KEY `tb_comm_rules_portefeuille_fk` (`portefeuille_id`);

--
-- Index pour la table `tb_compta_ecritures`
--
ALTER TABLE `tb_compta_ecritures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_compta_ecriture_compte_devise` (`numero_compte`,`devise_code`),
  ADD KEY `idx_compta_ecriture_journal` (`journal_id`),
  ADD KEY `fk_compta_ecriture_devise` (`devise_code`);

--
-- Index pour la table `tb_compta_journaux`
--
ALTER TABLE `tb_compta_journaux`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tb_compta_journaux_reference_piece` (`reference_piece`),
  ADD KEY `idx_compta_journal_trans_type` (`transaction_id`,`type_piece`),
  ADD KEY `idx_compta_journal_date_devise` (`date_ecriture`,`devise_code`);

--
-- Index pour la table `tb_comptes`
--
ALTER TABLE `tb_comptes`
  ADD PRIMARY KEY (`code_compte`),
  ADD UNIQUE KEY `uq_tb_comptes_client_type_devise` (`client_matricule`,`type`,`devise`),
  ADD KEY `fk_compte_devise` (`devise`),
  ADD KEY `fk_compte_portefeuille` (`portefeuille_id`),
  ADD KEY `tb_comptes_ibfk_112` (`client_matricule`);

--
-- Index pour la table `tb_demandes_modification`
--
ALTER TABLE `tb_demandes_modification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_statut_created` (`statut`,`created_at`),
  ADD KEY `idx_agent_matricule` (`agent_matricule`),
  ADD KEY `idx_superviseur_matricule` (`superviseur_matricule`),
  ADD KEY `fk_demmod_transaction` (`transaction_id`),
  ADD KEY `fk_demmod_guichet` (`guichet_id`);

--
-- Index pour la table `tb_devises`
--
ALTER TABLE `tb_devises`
  ADD PRIMARY KEY (`code_iso`),
  ADD UNIQUE KEY `uq_tb_devises_code_iso` (`code_iso`);

--
-- Index pour la table `tb_mouvements_inter_caisses`
--
ALTER TABLE `tb_mouvements_inter_caisses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_reference_bordereau` (`reference_bordereau`),
  ADD KEY `fk_mouv_guichet_src` (`guichet_source_id`),
  ADD KEY `fk_mouv_guichet_dest` (`guichet_dest_id`),
  ADD KEY `fk_mouv_agent` (`agent_initiateur`),
  ADD KEY `fk_mouv_devise` (`devise_code`),
  ADD KEY `validateur_matricule` (`validateur_matricule`);

--
-- Index pour la table `tb_permissions`
--
ALTER TABLE `tb_permissions`
  ADD PRIMARY KEY (`code`),
  ADD UNIQUE KEY `tb_permissions_nom_unique` (`nom`);

--
-- Index pour la table `tb_plan_comptable`
--
ALTER TABLE `tb_plan_comptable`
  ADD PRIMARY KEY (`numero_compte`);

--
-- Index pour la table `tb_portefeuilles_agents`
--
ALTER TABLE `tb_portefeuilles_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_port_agent` (`agent_matricule`);

--
-- Index pour la table `tb_postes`
--
ALTER TABLE `tb_postes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tb_postes_service_id_foreign` (`service_id`);

--
-- Index pour la table `tb_roles`
--
ALTER TABLE `tb_roles`
  ADD PRIMARY KEY (`code`),
  ADD UNIQUE KEY `tb_roles_nom_unique` (`nom`);

--
-- Index pour la table `tb_role_permission`
--
ALTER TABLE `tb_role_permission`
  ADD PRIMARY KEY (`role_code`,`permission_code`),
  ADD KEY `fk_rp_permission` (`permission_code`);

--
-- Index pour la table `tb_role_user`
--
ALTER TABLE `tb_role_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contrainte_user` (`user_id`),
  ADD KEY `contrainte_role` (`role_code`);

--
-- Index pour la table `tb_services`
--
ALTER TABLE `tb_services`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tb_taux_echanges`
--
ALTER TABLE `tb_taux_echanges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_devise_src` (`devise_source`),
  ADD KEY `fk_devise_dest` (`devise_destination`);

--
-- Index pour la table `tb_transactions`
--
ALTER TABLE `tb_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tb_transactions_reference_unique` (`reference`),
  ADD KEY `tb_transactions_ibfk_1` (`compte_code`),
  ADD KEY `tb_transactions_ibfk_2` (`agent_matricule`),
  ADD KEY `idx_trans_guichet_date` (`guichet_id`,`date_operation`),
  ADD KEY `idx_trans_statut` (`statut`);

--
-- Index pour la table `tb_transaction_commissions`
--
ALTER TABLE `tb_transaction_commissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_trans_comm_tx_date` (`transaction_id`,`date_application`),
  ADD KEY `idx_trans_comm_scope` (`code_operation`,`type_compte`,`type_guichet`),
  ADD KEY `tb_trans_comm_rule_fk` (`commission_rule_id`),
  ADD KEY `tb_trans_comm_portefeuille_fk` (`portefeuille_id`),
  ADD KEY `tb_trans_comm_guichet_fk` (`guichet_id`);

--
-- Index pour la table `tb_zones`
--
ALTER TABLE `tb_zones`
  ADD PRIMARY KEY (`code_zone`),
  ADD KEY `tb_zones_ibfk_1` (`agent_commercial_matricule`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `fk_agent_matricule` (`agent_matricule`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `tb_affectations`
--
ALTER TABLE `tb_affectations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `tb_caisses_guichets`
--
ALTER TABLE `tb_caisses_guichets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT pour la table `tb_caisses_guichets_soldes`
--
ALTER TABLE `tb_caisses_guichets_soldes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT pour la table `tb_cloture_caisse`
--
ALTER TABLE `tb_cloture_caisse`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT pour la table `tb_commission_rules`
--
ALTER TABLE `tb_commission_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `tb_compta_ecritures`
--
ALTER TABLE `tb_compta_ecritures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `tb_compta_journaux`
--
ALTER TABLE `tb_compta_journaux`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `tb_demandes_modification`
--
ALTER TABLE `tb_demandes_modification`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_mouvements_inter_caisses`
--
ALTER TABLE `tb_mouvements_inter_caisses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT pour la table `tb_portefeuilles_agents`
--
ALTER TABLE `tb_portefeuilles_agents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `tb_postes`
--
ALTER TABLE `tb_postes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `tb_role_user`
--
ALTER TABLE `tb_role_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT pour la table `tb_services`
--
ALTER TABLE `tb_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `tb_taux_echanges`
--
ALTER TABLE `tb_taux_echanges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `tb_transactions`
--
ALTER TABLE `tb_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `tb_transaction_commissions`
--
ALTER TABLE `tb_transaction_commissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  ADD CONSTRAINT `tb_comm_rules_portefeuille_fk` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `fk_compta_journal_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `tb_transactions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `tb_comptes`
--
ALTER TABLE `tb_comptes`
  ADD CONSTRAINT `fk_compte_devise` FOREIGN KEY (`devise`) REFERENCES `tb_devises` (`code_iso`),
  ADD CONSTRAINT `fk_compte_portefeuille` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents` (`id`),
  ADD CONSTRAINT `tb_comptes_ibfk_112` FOREIGN KEY (`client_matricule`) REFERENCES `tb_clients` (`matricule`);

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
  ADD CONSTRAINT `tb_trans_comm_guichet_fk` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_trans_comm_portefeuille_fk` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_trans_comm_rule_fk` FOREIGN KEY (`commission_rule_id`) REFERENCES `tb_commission_rules` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_trans_comm_tx_fk` FOREIGN KEY (`transaction_id`) REFERENCES `tb_transactions` (`id`) ON DELETE CASCADE;

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
