-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 17 fév. 2026 à 10:20
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `matricule` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `zone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
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
  UNIQUE KEY `clients_matricule_unique` (`matricule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`matricule`, `nom`, `postnom`, `prenom`, `email`, `telephone`, `sexe`, `date_naissance`, `lieu_naissance`, `adresse`, `etat_civil`, `nom_conjoint`, `zone`, `type_piece_identite`, `lieu_delivrance_piece`, `date_delivrance_piece`, `numero_piece_identite`, `photo`, `secteur_activite`, `type_activite`, `nom_entreprise`, `adresse_entreprise`, `telephone_entreprise`, `statut_entreprise`, `nombre_annees_experience`, `revenu_mensuel`, `revenu_mensuel_devise`, `autres_details_activite`, `created_at`, `updated_at`) VALUES
('CL-EBENKGA-26-00001', 'Blaise', 'MUBADI', 'Bakajika', 'exemple@email.com', '0123456789', 'M', '1995-08-17', 'Kananga', 'Nganza  N° 12', 'Marié', 'Nice', 'Urbain', 'Carte nationale d\'identité', 'Kananga', '2020-12-28', '08888025', 'clients/1771152283_IMG_8147.jpeg', 'Enseignement', 'Commerce', 'IPKO', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', '+243 55555', 'Agréee', '2', 50000.00, NULL, 'Ras', '2026-02-13 22:26:13', '2026-02-15 09:53:39'),
('CL-EBENKGA-26-00002', 'MPUTU', 'TUDIKOLELE', 'Clémence', 'exemple@email.com', '0123456789', 'M', '1994-02-04', 'Kinshasa', 'Ndesha', 'Divorcé', NULL, 'Urbain', 'Carte d\'électeur', 'Kananga', '2021-02-19', '000847', 'clients/Nice.jpg', 'Sorry', 'Commerce', 'KANKO', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', '0992463511', 'Agréee', '5', 50000.00, NULL, 'Ras', '2026-02-14 13:47:42', '2026-02-14 13:47:42'),
('CL-EBENKGA-26-00003', 'MPUTU', 'TUDIKOLELE', 'Clémence', 'exemple@email.com', '0123456789', 'M', '1994-02-04', 'Kinshasa', 'Ndesha', 'Marié', 'Nice MPUTU', 'Urbain', 'Carte d\'électeur', 'Kananga', '2021-02-19', '000847', 'clients/1771151216_IMG_8304.jpeg', 'Sorry', 'Commerce', 'KANKO', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', '0992463511', 'Agréee', '5', 50000.00, NULL, 'Ras', '2026-02-14 15:06:40', '2026-02-15 09:27:42'),
('CL-EBENKGA-26-00004', 'MPUTU', 'TUDIKOLELE', 'Clémence', 'exemple@email.com', '0123456789', 'M', '1994-02-04', 'Kinshasa', 'Ndesha', 'Célibataire', NULL, 'Urbain', 'Carte d\'électeur', 'Kananga', '2021-02-19', '000847', 'clients/1771085969_Blaise_1.jpeg', 'Sorry', 'Agriculture', 'KANKO', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', '0992463511', 'Agréee', '5', 50000.00, NULL, 'Ras', '2026-02-14 15:19:29', '2026-02-14 15:19:29'),
('CL-EBENKGA-26-00026', 'Faure', 'Noe', 'Mael', 'mael.faure22@email.com', '0600000022', 'M', '1998-10-22', '', '31 rue de Amiens', '', NULL, 'Nord', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00025', 'Lopez', 'Leo', 'Evan', 'evan.lopez21@email.com', '0600000021', 'M', '1997-09-21', '', '30 rue de Nîmes', '', NULL, 'Centre', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00024', 'Gauthier', 'Oscar', 'Axel', 'axel.gauthier20@email.com', '0600000020', 'M', '1996-08-20', '', '29 rue de Avignon', '', NULL, 'Ouest', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00023', 'Clement', 'Victor', 'Sacha', 'sacha.clement19@email.com', '0600000019', 'M', '1995-07-19', '', '28 rue de Perpignan', '', NULL, 'Est', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00022', 'Mathieu', 'Arthur', 'Ethan', 'ethan.mathieu18@email.com', '0600000018', 'M', '1994-06-18', '', '27 rue de Angers', '', NULL, 'Sud', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00021', 'Morin', 'Raphael', 'Hugo', 'hugo.morin17@email.com', '0600000017', 'M', '1993-05-17', '', '26 rue de Pau', '', NULL, 'Nord', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00020', 'Perrin', 'Mathis', 'Jules', 'jules.perrin16@email.com', '0600000016', 'M', '1992-04-16', '', '25 rue de Metz', '', NULL, 'Centre', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00019', 'Nicolas', 'Gabriel', 'Liam', 'liam.nicolas15@email.com', '0600000015', 'M', '1991-03-15', '', '24 rue de Reims', '', NULL, 'Ouest', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00018', 'Roussel', 'Antoine', 'Enzo', 'enzo.roussel14@email.com', '0600000014', 'M', '1990-02-14', '', '23 rue de Tours', '', NULL, 'Est', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00017', 'Henry', 'Julien', 'Nathan', 'nathan.henry13@email.com', '0600000013', 'M', '1999-01-13', '', '22 rue de Dijon', '', NULL, 'Sud', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00016', 'Guerin', 'Lucas', 'Adam', 'adam.guerin12@email.com', '0600000012', 'M', '1998-12-12', '', '21 rue de Limoges', '', NULL, 'Nord', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00015', 'Blanc', 'Paul', 'Noah', 'noah.blanc11@email.com', '0600000011', 'M', '1997-11-11', '', '20 rue de Brest', '', NULL, 'Centre', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00014', 'Roux', 'Charles', 'Eliot', 'eliot.roux10@email.com', '0600000010', 'M', '1996-10-10', '', '19 rue de Rennes', '', NULL, 'Ouest', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00013', 'Petit', 'Richard', 'Tom', 'tom.petit9@email.com', '0600000009', 'M', '1995-09-09', '', '18 rue de Strasbourg', '', NULL, 'Est', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00012', 'Garcia', 'Thomas', 'Leo', 'leo.garcia8@email.com', '0600000008', 'M', '1994-08-08', '', '17 rue de Marseille', '', NULL, 'Sud', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00011', 'Michel', 'Bernard', 'Alex', 'alex.michel7@email.com', '0600000007', 'M', '1987-07-07', '', '16 rue de Toulouse', '', NULL, 'Nord', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00010', 'Laurent', 'David', 'Max', 'max.laurent6@email.com', '0600000006', 'M', '1990-06-06', '', '15 rue de Bordeaux', '', NULL, 'Centre', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00009', 'Simon', 'Robert', 'Hugo', 'hugo.simon5@email.com', '0600000005', 'M', '1993-05-05', '', '14 rue de Nantes', '', NULL, 'Ouest', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00008', 'Moreau', 'Alain', 'Marc', 'marc.moreau4@email.com', '0600000004', 'M', '1991-04-04', '', '13 rue de Nice', '', NULL, 'Est', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00007', 'Lefevre', 'Louis', 'Luc', 'luc.lefevre3@email.com', '0600000003', 'M', '1992-03-03', '', '12 rue de Lille', '', NULL, 'Sud', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00006', 'Durand', 'Pierre', 'Paul', 'paul.durand2@email.com', '0600000002', 'M', '1988-02-02', '', '11 rue de Lyon', '', NULL, 'Nord', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00005', 'Dupont', 'Martin', 'Jean', 'jean.dupont1@email.com', '0600000001', 'M', '1990-01-01', '', '10 rue de Paris', '', NULL, 'Centre', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00027', 'Andre', 'Aaron', 'Nino', 'nino.andre23@email.com', '0600000023', 'M', '1999-11-23', '', '32 rue de Mulhouse', '', NULL, 'Sud', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00028', 'Mercier', 'Eliott', 'Loris', 'loris.mercier24@email.com', '0600000024', 'M', '1990-12-24', '', '33 rue de Caen', '', NULL, 'Est', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00029', 'Dupuis', 'Timéo', 'Noe', 'noe.dupuis25@email.com', '0600000025', 'M', '1991-01-25', '', '34 rue de Nancy', '', NULL, 'Ouest', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00030', 'Fournier', 'Malo', 'Ilan', 'ilan.fournier26@email.com', '0600000026', 'M', '1992-02-26', '', '35 rue de Poitiers', '', NULL, 'Centre', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00031', 'Girard', 'Eden', 'Lenny', 'lenny.girard27@email.com', '0600000027', 'M', '1993-03-27', '', '36 rue de Clermont', '', NULL, 'Nord', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00032', 'Bonnet', 'Lyam', 'Milan', 'milan.bonnet28@email.com', '0600000028', 'M', '1994-04-28', '', '37 rue de Besançon', '', NULL, 'Sud', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00033', 'Lemoine', 'Nolan', 'Matis', 'matis.lemoine29@email.com', '0600000029', 'M', '1995-05-29', '', '38 rue de Saint-Étienne', '', NULL, 'Est', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00034', 'Francois', 'Sami', 'Eliot', 'eliot.francois30@email.com', '0600000030', 'M', '1996-06-30', '', '39 rue de La Rochelle', '', NULL, 'Ouest', '', '', '2026-02-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 00:14:22', '2026-02-15 00:14:22'),
('CL-EBENKGA-26-00035', 'MPUTU', 'TUDIKOLELE', 'Clémence', NULL, NULL, 'M', '1994-02-04', 'Kinshasa', 'Ndesha', 'Célibataire', NULL, 'Urbain', 'Carte d\'électeur', 'Kananga', '2021-02-19', '000847', 'images_projet/clients/1771117605_Me Bernard.jpeg', 'Sorry', 'Commerce', 'KANKO', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', '0992463511', 'Agréee', '5', 50000.00, NULL, 'Ras', '2026-02-15 00:06:45', '2026-02-15 00:06:45'),
('CL-EBENKGA-26-00036', 'BIAKUSHILA', 'BAKAJIKA', 'Honoré', NULL, NULL, 'M', '1999-01-29', 'Mbuji-Mayi', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', 'Marié', 'KAPINGA Francine', 'Urbain', 'Carte d\'électeur', 'Kananga', '2026-02-12', '000847', 'clients/1771148097_WhatsApp Image 2025-11-29 à 14.58.40_de736946.jpeg', 'Education', 'Commerce', 'KANKO', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', '0992463511', 'Agree', '5', 500.00, NULL, 'RAS', '2026-02-15 08:34:57', '2026-02-15 08:34:57'),
('CL-EBENKGA-26-00037', 'KABUATILA', 'KABUATILA', 'Bernard', NULL, NULL, 'M', '1999-01-29', 'Mbuji-Mayi', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', 'Marié', 'Grace', 'Urbain', 'Permis de conduire', 'Kananga', '2026-02-12', '000847', 'clients/1771148229_DSC_2999.jpeg', 'Education', 'Agriculture', 'KANKO', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', '0992463511', 'Agree', '5', 500.00, NULL, 'RAS', '2026-02-15 08:37:09', '2026-02-15 08:37:09'),
('CL-EBENKGA-26-00038', 'KABUATILA', 'KABUATILA', 'Bernard', 'biakushila@gmail.com', '0992463511', 'M', '1999-01-29', 'Mbuji-Mayi', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', 'Célibataire', NULL, 'Urbain', 'Carte nationale d\'identité', 'Kananga', '2026-02-12', '000847', 'clients/1771148386_DSC_3007.jpeg', 'Education', 'Commerce', 'KANKO', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', '0992463511', 'Agree', '5', 500.00, NULL, 'RAS', '2026-02-15 08:39:46', '2026-02-15 08:39:46');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(8, '0001_01_01_000000_create_users_table', 6),
(9, '0001_01_01_000001_create_cache_table', 6),
(4, '2026_02_13_125454_create_clients_table', 2),
(5, '2026_02_14_150000_add_email_telephone_to_clients_table', 3),
(6, '2026_02_14_151000_add_matricule_to_clients_table', 4),
(7, '2026_02_15_230000_add_revenu_mensuel_devise_to_clients_table', 5),
(10, '0001_01_01_000002_create_jobs_table', 6),
(11, '2026_02_16_100000_create_rh_from_sql', 6);

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('WmQIKn40gPgwKPMY6qmLM32EThKiltnoTBrgedrt', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS0d0ckV6cjhrWlJNQnFjb2kyeEpORHJON1pmTFpUdlRlV1gzRUFvYSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjE6Imh0dHA6Ly9sb2NhbGhvc3QvTmV4dXMtQk1CLVRlY2gtc29mdC1iYW5raW5nL3B1YmxpYy9yaC9hZ2VudHMiO3M6NToicm91dGUiO3M6MTI6ImFnZW50cy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771286222),
('kBOdhNffYuTuQTLcdITK9JDEN8bDGDLzul7YNiIh', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRTJPclp1dUJjeTF5dnFqd3NUTGw0VVZQTTBjaGx2ajl3WFBFd3FuTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjE6Imh0dHA6Ly9sb2NhbGhvc3QvTmV4dXMtQk1CLVRlY2gtc29mdC1iYW5raW5nL3B1YmxpYy9yaC9hZ2VudHMiO3M6NToicm91dGUiO3M6MTI6ImFnZW50cy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771322624);

-- --------------------------------------------------------

--
-- Structure de la table `tb_affectations`
--

DROP TABLE IF EXISTS `tb_affectations`;
CREATE TABLE IF NOT EXISTS `tb_affectations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `agent_matricule` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `poste_id` bigint UNSIGNED NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tb_affectations_agent_matricule_foreign` (`agent_matricule`),
  KEY `tb_affectations_poste_id_foreign` (`poste_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tb_agents`
--

INSERT INTO `tb_agents` (`matricule`, `nom`, `postnom`, `prenom`, `sexe`, `date_naissance`, `telephone`, `email`, `adresse`, `photo`, `date_embauche`, `statut`, `created_at`, `updated_at`) VALUES
('AG-EBENKGA-26-00002', 'KABUE', 'NTUMBA', 'Joel', 'F', '1995-01-31', '+21', 'christophetshibangu117@gmail.com', 'Kanowa Pepiniere, N\'SELE, NGANZA , KANANGA', 'agents/1771284216_1767056067186jpg', '2025-06-05', 'actif', '2026-02-16 22:23:36', '2026-02-16 22:23:36');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
