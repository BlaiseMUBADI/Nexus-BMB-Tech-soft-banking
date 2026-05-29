-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : jeu. 30 avr. 2026 à 11:23
-- Version du serveur : 10.11.16-MariaDB-deb12
-- Version de PHP : 8.2.30

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

-- --------------------------------------------------------

--
-- Structure de la table `tb_affectations_portefeuilles`
--

CREATE TABLE `tb_affectations_portefeuilles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `portefeuille_id` bigint(20) UNSIGNED NOT NULL,
  `agent_matricule` varchar(50) DEFAULT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `Etat` varchar(50) NOT NULL DEFAULT 'ACTIF',
  `motif` varchar(255) DEFAULT NULL,
  `effectue_par_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_affectations_zones`
--

CREATE TABLE `tb_affectations_zones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code_zone` varchar(50) NOT NULL,
  `agent_matricule` varchar(50) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `Etat` varchar(50) NOT NULL DEFAULT 'ACTIF',
  `motif` varchar(255) DEFAULT NULL,
  `effectue_par_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `devise_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_zone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `created_by_agent` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `devise_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `libelle` varchar(191) NOT NULL,
  `statut` enum('COMPTABILISE','ANNULE') NOT NULL DEFAULT 'COMPTABILISE',
  `agent_matricule` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `type` enum('CC','RMB','GTC','DAT','EAV') NOT NULL COMMENT 'CC=Compte Courant, RMB=Remboursement, GTC=Caution, DAT=Dépôt à Terme, EAV=Épargne & Vie',
  `portefeuille_id` bigint(20) UNSIGNED DEFAULT NULL,
  `solde_reel` decimal(18,2) NOT NULL DEFAULT 0.00,
  `solde_bloque` decimal(18,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_analyses`
--

CREATE TABLE `tb_credit_analyses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `credit_demande_id` bigint(20) UNSIGNED NOT NULL,
  `analyseur_matricule` varchar(30) NOT NULL,
  `revenu_mensuel_verifie` decimal(15,2) DEFAULT NULL,
  `capacite_remboursement` decimal(15,2) DEFAULT NULL,
  `ratio_endettement` decimal(6,2) DEFAULT NULL,
  `score_risque` enum('FAIBLE','MOYEN','ELEVE','TRES_ELEVE') DEFAULT NULL,
  `historique_credit` text DEFAULT NULL,
  `garanties_evaluees` text DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `recommandation` enum('FAVORABLE','FAVORABLE_AVEC_RESERVE','DEFAVORABLE') NOT NULL,
  `montant_recommande` decimal(15,2) DEFAULT NULL,
  `statut` enum('EN_COURS','COMPLETE','ANNULE') NOT NULL DEFAULT 'EN_COURS',
  `complete_le` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_audits`
--

CREATE TABLE `tb_credit_audits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `credit_demande_id` bigint(20) UNSIGNED NOT NULL,
  `acteur_matricule` varchar(30) DEFAULT NULL,
  `type_action` enum('CREATION','SOUMISSION','ANALYSE_DEMARREE','ANALYSE_COMPLETE','VALIDATION_PARTIELLE','VALIDATION_COMPLETE','REJET','DEBLOCAGE','REMBOURSEMENT','ANNULATION','SUSPENSION','LEVER_SUSPENSION','SIGNALEMENT_SUSPECT','LEVER_SUSPICION','MODIFICATION') NOT NULL,
  `ancien_statut` varchar(30) DEFAULT NULL,
  `nouveau_statut` varchar(30) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_deblocages`
--

CREATE TABLE `tb_credit_deblocages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `credit_demande_id` bigint(20) UNSIGNED NOT NULL,
  `agent_matricule` varchar(30) NOT NULL,
  `compte_debit_id` varchar(64) NOT NULL,
  `compte_credit_id` varchar(64) NOT NULL,
  `montant_debloque` decimal(15,2) NOT NULL,
  `devise` varchar(5) NOT NULL DEFAULT 'CDF',
  `frais_dossier` decimal(15,2) NOT NULL DEFAULT 0.00,
  `montant_net_verse` decimal(15,2) NOT NULL,
  `reference_transaction` varchar(50) DEFAULT NULL,
  `numero_ordre` varchar(30) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `debloque_le` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_demandes`
--

CREATE TABLE `tb_credit_demandes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_dossier` varchar(30) NOT NULL,
  `client_matricule` varchar(30) NOT NULL,
  `compte_id` varchar(64) NOT NULL,
  `portefeuille_id` bigint(20) UNSIGNED NOT NULL,
  `code_zone` varchar(20) NOT NULL,
  `agent_createur_matricule` varchar(30) NOT NULL,
  `montant_demande` decimal(15,2) NOT NULL,
  `devise` varchar(5) NOT NULL DEFAULT 'CDF',
  `duree_mois` tinyint(3) UNSIGNED NOT NULL,
  `taux_interet_mensuel` decimal(6,4) NOT NULL,
  `type_credit` enum('INDIVIDUEL','SOLIDAIRE','PME') NOT NULL DEFAULT 'INDIVIDUEL',
  `objet_credit` varchar(500) NOT NULL,
  `garantie_description` text DEFAULT NULL,
  `montant_approuve` decimal(15,2) DEFAULT NULL,
  `montant_total_echeances` decimal(15,2) DEFAULT NULL,
  `total_interets` decimal(15,2) DEFAULT NULL,
  `statut_global` enum('BROUILLON','SOUMIS','EN_ANALYSE','EN_VALIDATION','PRET_A_DEBLOQUER','DEBLOQUE','EN_REMBOURSEMENT','EN_RETARD','SOLDE','ANNULE','SUSPENDU','SUSPECT') NOT NULL DEFAULT 'BROUILLON',
  `est_annule` tinyint(1) NOT NULL DEFAULT 0,
  `motif_annulation` text DEFAULT NULL,
  `annule_par_matricule` varchar(30) DEFAULT NULL,
  `annule_le` timestamp NULL DEFAULT NULL,
  `est_suspendu` tinyint(1) NOT NULL DEFAULT 0,
  `motif_suspension` text DEFAULT NULL,
  `suspendu_par_matricule` varchar(30) DEFAULT NULL,
  `suspendu_le` timestamp NULL DEFAULT NULL,
  `est_suspect` tinyint(1) NOT NULL DEFAULT 0,
  `motif_suspicion` text DEFAULT NULL,
  `signale_par_matricule` varchar(30) DEFAULT NULL,
  `signale_le` timestamp NULL DEFAULT NULL,
  `soumis_le` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_echeances`
--

CREATE TABLE `tb_credit_echeances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `echeancier_id` bigint(20) UNSIGNED NOT NULL,
  `numero_echeance` tinyint(3) UNSIGNED NOT NULL,
  `date_echeance` date NOT NULL,
  `capital_restant_debut` decimal(15,2) NOT NULL,
  `capital_echeance` decimal(15,2) NOT NULL,
  `interet_echeance` decimal(15,2) NOT NULL,
  `total_echeance` decimal(15,2) NOT NULL,
  `capital_restant_fin` decimal(15,2) NOT NULL,
  `statut` enum('EN_ATTENTE','PAYE','PARTIELLEMENT_PAYE','EN_RETARD','REPORTE') NOT NULL DEFAULT 'EN_ATTENTE',
  `montant_paye` decimal(15,2) NOT NULL DEFAULT 0.00,
  `date_paiement_effectif` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_echeanciers`
--

CREATE TABLE `tb_credit_echeanciers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `credit_demande_id` bigint(20) UNSIGNED NOT NULL,
  `montant_capital` decimal(15,2) NOT NULL,
  `taux_mensuel` decimal(6,4) NOT NULL,
  `duree_mois` tinyint(3) UNSIGNED NOT NULL,
  `date_premier_remboursement` date NOT NULL,
  `type_amortissement` enum('DEGRESSIF','LINEAIRE') NOT NULL DEFAULT 'DEGRESSIF',
  `total_capital` decimal(15,2) NOT NULL,
  `total_interets` decimal(15,2) NOT NULL,
  `total_general` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_pieces`
--

CREATE TABLE `tb_credit_pieces` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `credit_demande_id` bigint(20) UNSIGNED NOT NULL,
  `libelle` varchar(200) NOT NULL,
  `type_piece` enum('IDENTITE','DOMICILE','REVENU','GARANTIE','AUTRE') NOT NULL,
  `nom_fichier` varchar(255) DEFAULT NULL,
  `est_recu` tinyint(1) NOT NULL DEFAULT 0,
  `est_conforme` tinyint(1) DEFAULT NULL,
  `observations` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_remboursements`
--

CREATE TABLE `tb_credit_remboursements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `credit_demande_id` bigint(20) UNSIGNED NOT NULL,
  `echeance_id` bigint(20) UNSIGNED DEFAULT NULL,
  `agent_matricule` varchar(30) NOT NULL,
  `compte_id` varchar(64) NOT NULL,
  `montant_recu` decimal(15,2) NOT NULL,
  `dont_capital` decimal(15,2) NOT NULL,
  `dont_interet` decimal(15,2) NOT NULL,
  `dont_penalite` decimal(15,2) NOT NULL DEFAULT 0.00,
  `devise` varchar(5) NOT NULL DEFAULT 'CDF',
  `type_remboursement` enum('ECHEANCE','PARTIEL','ANTICIPE','PENALITE') NOT NULL DEFAULT 'ECHEANCE',
  `reference_caisse` varchar(50) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `recu_le` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_credit_validations`
--

CREATE TABLE `tb_credit_validations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `credit_demande_id` bigint(20) UNSIGNED NOT NULL,
  `type_validateur` enum('AGENT_CREDIT','CHARGE_OPERATIONS','CONTROLEUR','GERANT') NOT NULL,
  `validateur_matricule` varchar(30) NOT NULL,
  `decision` enum('EN_ATTENTE','APPROUVE','APPROUVE_AVEC_RESERVE','REJETE') NOT NULL DEFAULT 'EN_ATTENTE',
  `montant_valide` decimal(15,2) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `conditions` text DEFAULT NULL,
  `ordre_etape` tinyint(3) UNSIGNED NOT NULL,
  `etape_precedente_ok` tinyint(1) NOT NULL DEFAULT 0,
  `valide_le` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Structure de la table `tb_plan_comptable`
--

CREATE TABLE `tb_plan_comptable` (
  `numero_compte` varchar(20) NOT NULL,
  `classe_ohada` char(1) DEFAULT NULL,
  `libelle` varchar(191) NOT NULL,
  `parent_compte` varchar(20) DEFAULT NULL,
  `niveau` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `type_compte` enum('ACTIF','PASSIF','CHARGE','PRODUIT','MIXTE','HORS_BILAN') NOT NULL,
  `est_mouvementable` tinyint(1) NOT NULL DEFAULT 1,
  `est_actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tb_portefeuilles_agents`
--

CREATE TABLE `tb_portefeuilles_agents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `agent_matricule` varchar(50) DEFAULT NULL,
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
  `code_zone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portefeuille_id` bigint(20) UNSIGNED DEFAULT NULL,
  `mode_calcul` enum('FIXE','POURCENTAGE') NOT NULL,
  `valeur_snapshot` decimal(18,4) NOT NULL,
  `base_calcul` decimal(18,2) NOT NULL DEFAULT 0.00,
  `montant_commission` decimal(18,2) NOT NULL DEFAULT 0.00,
  `date_application` timestamp NOT NULL DEFAULT current_timestamp(),
  `agent_matricule` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `agent_commercial_matricule` varchar(50) DEFAULT NULL,
  `commune` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Index pour la table `tb_affectations_portefeuilles`
--
ALTER TABLE `tb_affectations_portefeuilles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_aff_pf_active` (`portefeuille_id`,`Etat`,`date_fin`),
  ADD KEY `idx_aff_pf_agent_active` (`agent_matricule`,`Etat`,`date_fin`),
  ADD KEY `idx_aff_pf_period` (`portefeuille_id`,`date_debut`);

--
-- Index pour la table `tb_affectations_zones`
--
ALTER TABLE `tb_affectations_zones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_aff_zone_active` (`code_zone`,`Etat`,`date_fin`),
  ADD KEY `idx_aff_zone_agent_active` (`agent_matricule`,`Etat`,`date_fin`),
  ADD KEY `idx_aff_zone_period` (`code_zone`,`date_debut`);

--
-- Index pour la table `tb_agents`
--
ALTER TABLE `tb_agents`
  ADD PRIMARY KEY (`matricule`),
  ADD KEY `idx_tb_agents_matricule` (`matricule`),
  ADD KEY `idx_tb_agents_matricule_fk_full` (`matricule`);

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
  ADD KEY `tb_comm_rules_portefeuille_fk` (`portefeuille_id`),
  ADD KEY `tb_comm_rules_zone_fk` (`code_zone`),
  ADD KEY `tb_agent` (`created_by_agent`);

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
  ADD KEY `idx_compta_journal_date_devise` (`date_ecriture`,`devise_code`),
  ADD KEY `fk_compta_journal_agent` (`agent_matricule`),
  ADD KEY `fk_compta_journal_devise` (`devise_code`);

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
-- Index pour la table `tb_credit_analyses`
--
ALTER TABLE `tb_credit_analyses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_analyse_demande` (`credit_demande_id`),
  ADD KEY `idx_analyse_agt` (`analyseur_matricule`);

--
-- Index pour la table `tb_credit_audits`
--
ALTER TABLE `tb_credit_audits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_dem` (`credit_demande_id`),
  ADD KEY `idx_audit_action` (`type_action`);

--
-- Index pour la table `tb_credit_deblocages`
--
ALTER TABLE `tb_credit_deblocages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tb_credit_deblocages_credit_demande_id_unique` (`credit_demande_id`),
  ADD KEY `tb_credit_deblocages_compte_debit_id_foreign` (`compte_debit_id`),
  ADD KEY `tb_credit_deblocages_compte_credit_id_foreign` (`compte_credit_id`);

--
-- Index pour la table `tb_credit_demandes`
--
ALTER TABLE `tb_credit_demandes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tb_credit_demandes_numero_dossier_unique` (`numero_dossier`),
  ADD KEY `idx_crd_client` (`client_matricule`),
  ADD KEY `idx_crd_zone` (`code_zone`),
  ADD KEY `idx_crd_statut` (`statut_global`),
  ADD KEY `idx_crd_portef` (`portefeuille_id`),
  ADD KEY `idx_crd_agent` (`agent_createur_matricule`),
  ADD KEY `tb_crd_compte_fk` (`compte_id`);

--
-- Index pour la table `tb_credit_echeances`
--
ALTER TABLE `tb_credit_echeances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ech_echeancier` (`echeancier_id`),
  ADD KEY `idx_ech_date` (`date_echeance`),
  ADD KEY `idx_ech_statut` (`statut`);

--
-- Index pour la table `tb_credit_echeanciers`
--
ALTER TABLE `tb_credit_echeanciers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tb_credit_echeanciers_credit_demande_id_unique` (`credit_demande_id`);

--
-- Index pour la table `tb_credit_pieces`
--
ALTER TABLE `tb_credit_pieces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pieces_dem` (`credit_demande_id`);

--
-- Index pour la table `tb_credit_remboursements`
--
ALTER TABLE `tb_credit_remboursements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rembours_dem` (`credit_demande_id`),
  ADD KEY `idx_rembours_ech` (`echeance_id`),
  ADD KEY `tb_credit_remboursements_compte_id_foreign` (`compte_id`);

--
-- Index pour la table `tb_credit_validations`
--
ALTER TABLE `tb_credit_validations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_validation_type` (`credit_demande_id`,`type_validateur`),
  ADD KEY `idx_valid_agt` (`validateur_matricule`),
  ADD KEY `idx_valid_dec` (`decision`);

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
  ADD UNIQUE KEY `uq_tb_devises_code_iso` (`code_iso`),
  ADD KEY `idx_tb_devises_code_iso_fk` (`code_iso`);

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
  ADD PRIMARY KEY (`numero_compte`),
  ADD KEY `idx_plan_ohada_classe_num` (`classe_ohada`,`numero_compte`),
  ADD KEY `idx_plan_ohada_parent` (`parent_compte`),
  ADD KEY `idx_plan_ohada_actif` (`est_actif`);

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
  ADD KEY `tb_trans_comm_guichet_fk` (`guichet_id`),
  ADD KEY `tb_trans_comm_zone_fk` (`code_zone`),
  ADD KEY `tb_trans_comm_agent_fk` (`agent_matricule`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_affectations`
--
ALTER TABLE `tb_affectations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_affectations_portefeuilles`
--
ALTER TABLE `tb_affectations_portefeuilles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_affectations_zones`
--
ALTER TABLE `tb_affectations_zones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_caisses_guichets`
--
ALTER TABLE `tb_caisses_guichets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_caisses_guichets_soldes`
--
ALTER TABLE `tb_caisses_guichets_soldes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_cloture_caisse`
--
ALTER TABLE `tb_cloture_caisse`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_commission_rules`
--
ALTER TABLE `tb_commission_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_compta_ecritures`
--
ALTER TABLE `tb_compta_ecritures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_compta_journaux`
--
ALTER TABLE `tb_compta_journaux`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_credit_analyses`
--
ALTER TABLE `tb_credit_analyses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_credit_audits`
--
ALTER TABLE `tb_credit_audits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_credit_deblocages`
--
ALTER TABLE `tb_credit_deblocages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_credit_demandes`
--
ALTER TABLE `tb_credit_demandes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_credit_echeances`
--
ALTER TABLE `tb_credit_echeances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_credit_echeanciers`
--
ALTER TABLE `tb_credit_echeanciers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_credit_pieces`
--
ALTER TABLE `tb_credit_pieces`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_credit_remboursements`
--
ALTER TABLE `tb_credit_remboursements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_credit_validations`
--
ALTER TABLE `tb_credit_validations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_demandes_modification`
--
ALTER TABLE `tb_demandes_modification`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_mouvements_inter_caisses`
--
ALTER TABLE `tb_mouvements_inter_caisses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_portefeuilles_agents`
--
ALTER TABLE `tb_portefeuilles_agents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_postes`
--
ALTER TABLE `tb_postes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_role_user`
--
ALTER TABLE `tb_role_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_services`
--
ALTER TABLE `tb_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_taux_echanges`
--
ALTER TABLE `tb_taux_echanges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_transactions`
--
ALTER TABLE `tb_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tb_transaction_commissions`
--
ALTER TABLE `tb_transaction_commissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `fk_aff_zone_agent` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`) ON UPDATE CASCADE;

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
  ADD CONSTRAINT `tb_credit_deblocages_compte_debit_id_foreign` FOREIGN KEY (`compte_debit_id`) REFERENCES `tb_comptes` (`code_compte`),
  ADD CONSTRAINT `tb_credit_deblocages_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes` (`id`);

--
-- Contraintes pour la table `tb_credit_demandes`
--
ALTER TABLE `tb_credit_demandes`
  ADD CONSTRAINT `tb_crd_client_fk` FOREIGN KEY (`client_matricule`) REFERENCES `tb_clients` (`matricule`),
  ADD CONSTRAINT `tb_crd_compte_fk` FOREIGN KEY (`compte_id`) REFERENCES `tb_comptes` (`code_compte`),
  ADD CONSTRAINT `tb_crd_portefeuille_fk` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents` (`id`),
  ADD CONSTRAINT `tb_crd_zone_fk` FOREIGN KEY (`code_zone`) REFERENCES `tb_zones` (`code_zone`);

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
