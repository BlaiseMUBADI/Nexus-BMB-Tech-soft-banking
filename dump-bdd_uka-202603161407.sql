-- MySQL dump 10.13  Distrib 8.4.7, for Win64 (x86_64)
--
-- Host: localhost    Database: bdd_uka
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
-- Table structure for table `affecter`
--

DROP TABLE IF EXISTS `affecter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `affecter` (
  `IdAffectation` int NOT NULL AUTO_INCREMENT,
  `Fonction` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `DateAffectation` date DEFAULT NULL,
  `Statut` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Matricule_Agent` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `IdService` int DEFAULT NULL,
  `Id_filiere` int DEFAULT NULL,
  PRIMARY KEY (`IdAffectation`),
  KEY `fk_service` (`IdService`),
  KEY `Id_filiere` (`Id_filiere`),
  KEY `Matricule_Agent` (`Matricule_Agent`),
  CONSTRAINT `affecter_ibfk_1` FOREIGN KEY (`Id_filiere`) REFERENCES `filiere` (`IdFiliere`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `affecter_ibfk_2` FOREIGN KEY (`Matricule_Agent`) REFERENCES `agent` (`Mat_agent`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_service` FOREIGN KEY (`IdService`) REFERENCES `service` (`IdService`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `agent`
--

DROP TABLE IF EXISTS `agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agent` (
  `Mat_agent` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Nom_agent` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Post_agent` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Prenom` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Sexe` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Lieu` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `DateNaissance` date DEFAULT NULL,
  `Grade` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `EtatCivil` varchar(20) NOT NULL,
  `IdCategorie` int NOT NULL DEFAULT '1',
  `AdressePhysique` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Mail` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Tel` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Date_Engagement` date DEFAULT NULL,
  `Niveau_Etude` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Annee_Obt` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Institution` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Domaine` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `type_agent` int DEFAULT NULL,
  `Institution_attachee` varchar(255) DEFAULT NULL,
  `Domaine_etude` varchar(255) DEFAULT NULL,
  `Id_filiere` int DEFAULT NULL,
  PRIMARY KEY (`Mat_agent`),
  KEY `fk_categorie` (`IdCategorie`),
  KEY `Id_filiere` (`Id_filiere`),
  CONSTRAINT `agent_ibfk_1` FOREIGN KEY (`Id_filiere`) REFERENCES `filiere` (`IdFiliere`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_categorie` FOREIGN KEY (`IdCategorie`) REFERENCES `categorie` (`IdCategorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `annee_academique`
--

DROP TABLE IF EXISTS `annee_academique`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `annee_academique` (
  `idAnnee_Acad` int NOT NULL AUTO_INCREMENT,
  `Annee_debut` int DEFAULT NULL,
  `Annee_fin` int DEFAULT NULL,
  PRIMARY KEY (`idAnnee_Acad`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `autorisation_depense`
--

DROP TABLE IF EXISTS `autorisation_depense`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autorisation_depense` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Num_pce` varchar(50) NOT NULL,
  `Motif` varchar(1000) NOT NULL,
  `Beneficiaire` varchar(100) NOT NULL,
  `Montant` float NOT NULL,
  `Imputation` int NOT NULL,
  `Date_ajout` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Num_pce` (`Num_pce`),
  KEY `Imputation` (`Imputation`),
  CONSTRAINT `autorisation_depense_ibfk_1` FOREIGN KEY (`Imputation`) REFERENCES `t_imputation` (`Num_imputation`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `autorisation_depense_ibfk_2` FOREIGN KEY (`Num_pce`) REFERENCES `numero_autorisation` (`numero_pce`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `autoriser_depense`
--

DROP TABLE IF EXISTS `autoriser_depense`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autoriser_depense` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Num_pce` varchar(50) NOT NULL,
  `Agent_auriz1` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Date_autoriz1` datetime DEFAULT NULL,
  `Niveau_1` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Agent_auriz2` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Date_autoriz2` datetime DEFAULT NULL,
  `Niveau_2` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Num_pce` (`Num_pce`),
  KEY `Agent_auriz1` (`Agent_auriz1`),
  KEY `Agent_auriz2` (`Agent_auriz2`),
  CONSTRAINT `autoriser_depense_ibfk_1` FOREIGN KEY (`Agent_auriz1`) REFERENCES `agent` (`Mat_agent`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `autoriser_depense_ibfk_2` FOREIGN KEY (`Agent_auriz2`) REFERENCES `agent` (`Mat_agent`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `autoriser_depense_ibfk_3` FOREIGN KEY (`Num_pce`) REFERENCES `numero_autorisation` (`numero_pce`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `autreinfo_etudiant`
--

DROP TABLE IF EXISTS `autreinfo_etudiant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autreinfo_etudiant` (
  `idAutreInfo_etudiant` int NOT NULL AUTO_INCREMENT,
  `Matricule` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Religion` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Nationalite` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `EtatCiv` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `NomPere` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `ProfPere` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `NomMere` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `ProfMere` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `AdresseActuelle` varchar(45) DEFAULT NULL,
  `Paroisse` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Diocese` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `TelVoda` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `TelOrange` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `TelAirtel` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Annscol` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `NomEtablis` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `PourceCertificat` int DEFAULT NULL,
  `PourceDiplome` int DEFAULT NULL,
  `NumDiplom` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `SetionEtude` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `OptionEtude` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Lieudelivrance` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Datedelivrance` date DEFAULT NULL,
  `Ecole` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Province` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `ProvinceOrigine` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Territoire` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `TelResponsable` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  PRIMARY KEY (`idAutreInfo_etudiant`),
  KEY `fk_AutreInfo_etudiant_Etudiant1_idx` (`Matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=12020 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorie` (
  `IdCategorie` int NOT NULL AUTO_INCREMENT,
  `Libelle` varchar(50) NOT NULL,
  PRIMARY KEY (`IdCategorie`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compte_agent`
--

DROP TABLE IF EXISTS `compte_agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compte_agent` (
  `Id_Compte_agent` int NOT NULL AUTO_INCREMENT,
  `Mat_agent` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `id_annee_academique` int DEFAULT NULL,
  `Code_promotion` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Id_filiere` int DEFAULT NULL,
  `Login` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Mot_passe` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Etat` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Categorie` varchar(45) DEFAULT NULL,
  `Nom_image` varchar(255) DEFAULT NULL,
  `Type_image` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Photo_profil` longblob,
  `Fonction` varchar(50) DEFAULT NULL,
  `Date_creation` datetime DEFAULT NULL,
  `derniere_connexion` datetime DEFAULT NULL COMMENT 'Dernière connexion réussie',
  `session_active` tinyint(1) DEFAULT '0' COMMENT 'Indicateur de session active',
  `session_id_actuelle` varchar(255) DEFAULT NULL COMMENT 'ID de la session active',
  PRIMARY KEY (`Id_Compte_agent`),
  KEY `fk_Compte_agent_Agent1_idx` (`Mat_agent`),
  KEY `Id_filiere` (`Id_filiere`),
  KEY `Code_promotion` (`Code_promotion`),
  KEY `id_annee_academique` (`id_annee_academique`),
  KEY `idx_session_active` (`session_active`),
  KEY `idx_compte_agent_session` (`session_id_actuelle`),
  CONSTRAINT `compte_agent_ibfk_1` FOREIGN KEY (`Id_filiere`) REFERENCES `filiere` (`IdFiliere`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compte_agent_ibfk_2` FOREIGN KEY (`Code_promotion`) REFERENCES `promotion` (`Code_Promotion`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `compte_agent_ibfk_3` FOREIGN KEY (`id_annee_academique`) REFERENCES `annee_academique` (`idAnnee_Acad`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_Compte_agent_Agent1` FOREIGN KEY (`Mat_agent`) REFERENCES `agent` (`Mat_agent`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compte_etudiant`
--

DROP TABLE IF EXISTS `compte_etudiant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compte_etudiant` (
  `id_Compte_etudiant` int NOT NULL AUTO_INCREMENT,
  `Mail_etudiant` varchar(255) NOT NULL,
  `Mot_de_passe` text NOT NULL,
  `Matricule` varchar(20) NOT NULL,
  PRIMARY KEY (`id_Compte_etudiant`),
  KEY `fk_Compte_etudiant_Etudiant1_idx` (`Matricule`),
  CONSTRAINT `fk_Compte_etudiant_Etudiant1` FOREIGN KEY (`Matricule`) REFERENCES `etudiant` (`Matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `decaissement_caisse`
--

DROP TABLE IF EXISTS `decaissement_caisse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `decaissement_caisse` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Beneficiaire` varchar(100) NOT NULL,
  `Motif` varchar(200) NOT NULL,
  `Montant` decimal(15,2) NOT NULL,
  `Date_Oper` datetime NOT NULL,
  `Imputation` int NOT NULL,
  `Num_piece` varchar(100) NOT NULL,
  `Statut` varchar(10) NOT NULL,
  `Id_Anne_Acad` int NOT NULL,
  `Num_Autoriz` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Imputation` (`Imputation`),
  KEY `Num_piece` (`Num_piece`),
  KEY `Num_Autoriz` (`Num_Autoriz`),
  KEY `Id_Anne_Acad` (`Id_Anne_Acad`),
  CONSTRAINT `decaissement_caisse_ibfk_1` FOREIGN KEY (`Imputation`) REFERENCES `t_imputation` (`Num_imputation`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `decaissement_caisse_ibfk_2` FOREIGN KEY (`Num_piece`) REFERENCES `numero_piece` (`numero_pce`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `decaissement_caisse_ibfk_3` FOREIGN KEY (`Num_Autoriz`) REFERENCES `numero_autorisation` (`numero_pce`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `decaissement_caisse_ibfk_4` FOREIGN KEY (`Id_Anne_Acad`) REFERENCES `annee_academique` (`idAnnee_Acad`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `domaine`
--

DROP TABLE IF EXISTS `domaine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `domaine` (
  `idDomaine` int NOT NULL AUTO_INCREMENT,
  `Libelle_domaine` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idDomaine`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `element_constitutifs`
--

DROP TABLE IF EXISTS `element_constitutifs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element_constitutifs` (
  `id_ec` int NOT NULL AUTO_INCREMENT,
  `Code_ue` varchar(10) NOT NULL,
  `Intutile_ec` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Hr_TD` int NOT NULL,
  `Hr_TP` int NOT NULL,
  `Credit` int NOT NULL,
  `TPE` int NOT NULL,
  `VHT` int NOT NULL,
  `CMI` int NOT NULL,
  PRIMARY KEY (`id_ec`),
  KEY `fk_Element_constutifs_Unite_Enseignant1_idx` (`Code_ue`),
  CONSTRAINT `fk_Element_constutifs_Unite_Enseignant1` FOREIGN KEY (`Code_ue`) REFERENCES `unite_enseignement` (`Code_ue`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `element_constitutifs_aligne`
--

DROP TABLE IF EXISTS `element_constitutifs_aligne`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element_constitutifs_aligne` (
  `id_ec_aligne` int NOT NULL AUTO_INCREMENT,
  `idAnnee_Acad` int NOT NULL,
  `id_ec` int NOT NULL,
  `Id_Semestre` int NOT NULL,
  `Code_promotion` varchar(10) NOT NULL,
  `Mat_agent` varchar(20) NOT NULL,
  `Mat_assistant` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_ec_aligne`),
  KEY `fk_Element_constitufs_aligne_Annee_academique1_idx` (`idAnnee_Acad`),
  KEY `fk_Element_constitufs_aligne_Element_constutifs1_idx` (`id_ec`),
  KEY `Id_Semestre` (`Id_Semestre`),
  KEY `Mat_agent` (`Mat_agent`),
  KEY `Code_promotion` (`Code_promotion`),
  KEY `Mat_assistant` (`Mat_assistant`),
  CONSTRAINT `element_constitutifs_aligne_ibfk_1` FOREIGN KEY (`idAnnee_Acad`) REFERENCES `annee_academique` (`idAnnee_Acad`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `element_constitutifs_aligne_ibfk_2` FOREIGN KEY (`Id_Semestre`) REFERENCES `semestre` (`Id_Semestre`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `element_constitutifs_aligne_ibfk_3` FOREIGN KEY (`Mat_agent`) REFERENCES `agent` (`Mat_agent`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `element_constitutifs_aligne_ibfk_4` FOREIGN KEY (`Code_promotion`) REFERENCES `promotion` (`Code_Promotion`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `element_constitutifs_aligne_ibfk_5` FOREIGN KEY (`Mat_assistant`) REFERENCES `agent` (`Mat_agent`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_Element_constitufs_aligne_Element_constutifs1` FOREIGN KEY (`id_ec`) REFERENCES `element_constitutifs` (`id_ec`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `encaissement_caisse`
--

DROP TABLE IF EXISTS `encaissement_caisse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `encaissement_caisse` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Motif` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Id_Service` int DEFAULT NULL,
  `Id_filiere` int DEFAULT NULL,
  `Montant` decimal(15,2) NOT NULL,
  `Numero_pce` varchar(100) NOT NULL,
  `Date_Oper` datetime NOT NULL,
  `Statut` varchar(50) NOT NULL,
  `Deposant` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Imputation` int NOT NULL,
  `IdAnnee` int NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Numero_pce` (`Numero_pce`),
  KEY `Imputation` (`Imputation`),
  KEY `IdAnnee` (`IdAnnee`),
  CONSTRAINT `encaissement_caisse_ibfk_1` FOREIGN KEY (`Numero_pce`) REFERENCES `numero_piece` (`numero_pce`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `encaissement_caisse_ibfk_2` FOREIGN KEY (`Imputation`) REFERENCES `t_imputation` (`Num_imputation`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `encaissement_caisse_ibfk_3` FOREIGN KEY (`IdAnnee`) REFERENCES `annee_academique` (`idAnnee_Acad`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=356 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `enfant`
--

DROP TABLE IF EXISTS `enfant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enfant` (
  `IdEnfant` int NOT NULL AUTO_INCREMENT,
  `Noms` varchar(80) NOT NULL,
  `Lieu_Naissance` varchar(30) NOT NULL,
  `DateNaissance` date NOT NULL,
  `Mat_agent` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`IdEnfant`)
) ENGINE=InnoDB AUTO_INCREMENT=573 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `enseignant`
--

DROP TABLE IF EXISTS `enseignant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enseignant` (
  `Matr_enseign` varchar(20) NOT NULL,
  `Nom_enseign` varchar(30) DEFAULT NULL,
  `Postnom_enseign` varchar(30) DEFAULT NULL,
  `Prenom_enseign` varchar(30) DEFAULT NULL,
  `Sexe` char(1) DEFAULT NULL,
  `Titre_academique` varchar(50) DEFAULT NULL,
  `Domaine` varchar(50) DEFAULT NULL,
  `Instutition_attache` varchar(50) DEFAULT NULL,
  `Photo_profil` longblob,
  PRIMARY KEY (`Matr_enseign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `est_pris_en_charger`
--

DROP TABLE IF EXISTS `est_pris_en_charger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `est_pris_en_charger` (
  `Mat_agent` varchar(20) NOT NULL,
  `Matricule` varchar(20) NOT NULL,
  `_idAnnee_Acad` int NOT NULL,
  `Mois` varchar(45) DEFAULT NULL,
  `Montant` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`Mat_agent`,`Matricule`,`_idAnnee_Acad`),
  KEY `fk_Agent_has_Etudiant_Etudiant1_idx` (`Matricule`),
  KEY `fk_Agent_has_Etudiant_Agent1_idx` (`Mat_agent`),
  KEY `fk_Est_pris_en_charger_Annee_academique1_idx` (`_idAnnee_Acad`),
  CONSTRAINT `fk_Agent_has_Etudiant_Agent1` FOREIGN KEY (`Mat_agent`) REFERENCES `agent` (`Mat_agent`),
  CONSTRAINT `fk_Agent_has_Etudiant_Etudiant1` FOREIGN KEY (`Matricule`) REFERENCES `etudiant` (`Matricule`),
  CONSTRAINT `fk_Est_pris_en_charger_Annee_academique1` FOREIGN KEY (`_idAnnee_Acad`) REFERENCES `annee_academique` (`idAnnee_Acad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etudiant`
--

DROP TABLE IF EXISTS `etudiant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `etudiant` (
  `Matricule` varchar(20) NOT NULL,
  `Nom` varchar(30) DEFAULT NULL,
  `Postnom` varchar(30) DEFAULT NULL,
  `Prenom` varchar(30) DEFAULT NULL,
  `Sexe` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `LieuNaissance` varchar(50) DEFAULT NULL,
  `DateNaissance` date DEFAULT NULL,
  PRIMARY KEY (`Matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `evaluer`
--

DROP TABLE IF EXISTS `evaluer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `evaluer` (
  `Matricule` varchar(20) NOT NULL,
  `id_ec_aligne` int NOT NULL,
  `Cote` float DEFAULT NULL,
  `Cote_rattrapage` float DEFAULT NULL,
  `cote_compensee` float DEFAULT NULL COMMENT 'C''est une colonne qui doit contenir la cote si et seulement la ligne donc la cote est entre (8 et 9)',
  `cote_reste_apres_cedee` float DEFAULT NULL COMMENT 'La cote qui est restée après compensation ',
  `Ligne_touchee_Matricule_id_ec_aligne` varchar(50) DEFAULT NULL COMMENT 'C''est une colonne qui doit avoir la référence de la cote qui a été touchée',
  PRIMARY KEY (`Matricule`,`id_ec_aligne`),
  KEY `fk_Etudiant_has_Element_constitufs_aligne_Element_constituf_idx` (`id_ec_aligne`),
  KEY `fk_Etudiant_has_Element_constitufs_aligne_Etudiant1_idx` (`Matricule`),
  CONSTRAINT `fk_Etudiant_has_Element_constitufs_aligne_Element_constitufs_1` FOREIGN KEY (`id_ec_aligne`) REFERENCES `element_constitutifs_aligne` (`id_ec_aligne`),
  CONSTRAINT `fk_Etudiant_has_Element_constitufs_aligne_Etudiant1` FOREIGN KEY (`Matricule`) REFERENCES `etudiant` (`Matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `start` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  `details` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `filiere`
--

DROP TABLE IF EXISTS `filiere`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `filiere` (
  `IdFiliere` int NOT NULL AUTO_INCREMENT,
  `idDomaine` int NOT NULL,
  `Libelle_Filiere` varchar(52) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  PRIMARY KEY (`IdFiliere`),
  KEY `fk_Filiere_Domaine1_idx` (`idDomaine`),
  CONSTRAINT `fk_Filiere_Domaine1` FOREIGN KEY (`idDomaine`) REFERENCES `domaine` (`idDomaine`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fonction_user`
--

DROP TABLE IF EXISTS `fonction_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fonction_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Designation` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `frais`
--

DROP TABLE IF EXISTS `frais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `frais` (
  `idFrais` int NOT NULL AUTO_INCREMENT,
  `idAnnee_Acad` int NOT NULL,
  `Code_Promotion` varchar(10) NOT NULL,
  `Libelle_Frais` varchar(45) NOT NULL,
  `Montant` float NOT NULL,
  `Tranche` float NOT NULL,
  `Devise` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  PRIMARY KEY (`idFrais`),
  KEY `fk_Frais_Annee_academique1_idx` (`idAnnee_Acad`),
  KEY `fk_Frais_Promotion1_idx` (`Code_Promotion`),
  CONSTRAINT `fk_Frais_Annee_academique1` FOREIGN KEY (`idAnnee_Acad`) REFERENCES `annee_academique` (`idAnnee_Acad`),
  CONSTRAINT `fk_Frais_Promotion1` FOREIGN KEY (`Code_Promotion`) REFERENCES `promotion` (`Code_Promotion`)
) ENGINE=InnoDB AUTO_INCREMENT=1017 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lieu_paiement`
--

DROP TABLE IF EXISTS `lieu_paiement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lieu_paiement` (
  `idLieu_paiement` int NOT NULL AUTO_INCREMENT,
  `Libelle_lieu` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idLieu_paiement`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mecanisation`
--

DROP TABLE IF EXISTS `mecanisation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mecanisation` (
  `MatriculeAgent` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Libelle` varchar(20) NOT NULL,
  `Observation` int NOT NULL,
  PRIMARY KEY (`MatriculeAgent`,`Libelle`),
  CONSTRAINT `mecanisation_ibfk_1` FOREIGN KEY (`MatriculeAgent`) REFERENCES `agent` (`Mat_agent`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mentions`
--

DROP TABLE IF EXISTS `mentions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mentions` (
  `idMentions` int NOT NULL AUTO_INCREMENT,
  `IdFiliere` int NOT NULL,
  `Libelle_mention` varchar(50) DEFAULT NULL,
  `Cycle_mention` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idMentions`),
  KEY `fk_Mentions_Filiere1_idx` (`IdFiliere`),
  CONSTRAINT `fk_Mentions_Filiere1` FOREIGN KEY (`IdFiliere`) REFERENCES `filiere` (`IdFiliere`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `numero_autorisation`
--

DROP TABLE IF EXISTS `numero_autorisation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `numero_autorisation` (
  `numero_pce` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`numero_pce`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `numero_piece`
--

DROP TABLE IF EXISTS `numero_piece`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `numero_piece` (
  `numero_pce` varchar(100) NOT NULL,
  PRIMARY KEY (`numero_pce`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paie_locale`
--

DROP TABLE IF EXISTS `paie_locale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paie_locale` (
  `Idpaie` int NOT NULL AUTO_INCREMENT,
  `MatriculeAgent` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Libelle` varchar(20) NOT NULL,
  `Observation` varchar(20) NOT NULL,
  PRIMARY KEY (`Idpaie`),
  KEY `Mat_agent` (`MatriculeAgent`),
  CONSTRAINT `paie_locale_ibfk_1` FOREIGN KEY (`MatriculeAgent`) REFERENCES `agent` (`Mat_agent`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parent`
--

DROP TABLE IF EXISTS `parent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parent` (
  `id` int NOT NULL AUTO_INCREMENT,
  `MatriculeAgent` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Noms` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Statut` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `annee_dec` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=290 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `passer_par`
--

DROP TABLE IF EXISTS `passer_par`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `passer_par` (
  `Etudiant_Matricule` varchar(20) NOT NULL,
  `Code_Promotion` varchar(10) NOT NULL,
  `idAnnee_academique` int NOT NULL,
  `Decision_jury` varchar(50) DEFAULT NULL,
  `Session1` float DEFAULT NULL,
  `Mention1` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Session2` float DEFAULT NULL,
  `Mention2` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Active` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`Etudiant_Matricule`,`Code_Promotion`,`idAnnee_academique`),
  KEY `fk_Etudiant_has_Promotion_Promotion1_idx` (`Code_Promotion`),
  KEY `fk_Etudiant_has_Promotion_Etudiant1_idx` (`Etudiant_Matricule`),
  KEY `fk_Etudiant_has_Promotion_Annee_academique1_idx` (`idAnnee_academique`),
  CONSTRAINT `fk_Etudiant_has_Promotion_Annee_academique1` FOREIGN KEY (`idAnnee_academique`) REFERENCES `annee_academique` (`idAnnee_Acad`),
  CONSTRAINT `fk_Etudiant_has_Promotion_Etudiant1` FOREIGN KEY (`Etudiant_Matricule`) REFERENCES `etudiant` (`Matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_Etudiant_has_Promotion_Promotion1` FOREIGN KEY (`Code_Promotion`) REFERENCES `promotion` (`Code_Promotion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payer_frais`
--

DROP TABLE IF EXISTS `payer_frais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payer_frais` (
  `Id_payer_frais` int NOT NULL AUTO_INCREMENT,
  `Matricule` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `idFrais` int NOT NULL,
  `Date_paie` datetime NOT NULL,
  `idLieu_paiement` int NOT NULL,
  `Mat_agent` varchar(20) NOT NULL,
  `Montant_paie` float DEFAULT NULL,
  `Motif_paie` varchar(45) DEFAULT NULL,
  `Numero_bordereau` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Ensemble` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'Cette colonne nous permet de rassamblés les réçus d''une personne ( FA et F_Enrol)',
  `Fc` float DEFAULT NULL COMMENT 'Cette colonne nous permet de faire l''impression de rapport en franc congolais',
  `Motif_suppression` varchar(255) DEFAULT NULL COMMENT 'Motif obligatoire avant suppression',
  `numero_recu` varchar(25) NOT NULL COMMENT 'C''est une colonne qui stock le numéro du reçu journalièrement .',
  PRIMARY KEY (`Id_payer_frais`),
  KEY `fk_Etudiant_has_Frais_Frais1_idx` (`idFrais`),
  KEY `fk_Etudiant_has_Frais_Etudiant1_idx` (`Matricule`),
  KEY `fk_Paie_Etudiant_Lieu_paiement1_idx` (`idLieu_paiement`),
  KEY `fk_Payer_Frais_Agent1_idx` (`Mat_agent`),
  CONSTRAINT `fk_Etudiant_has_Frais_Etudiant1` FOREIGN KEY (`Matricule`) REFERENCES `etudiant` (`Matricule`),
  CONSTRAINT `fk_Paie_Etudiant_Lieu_paiement1` FOREIGN KEY (`idLieu_paiement`) REFERENCES `lieu_paiement` (`idLieu_paiement`),
  CONSTRAINT `fk_Payer_Frais_Agent1` FOREIGN KEY (`Mat_agent`) REFERENCES `agent` (`Mat_agent`),
  CONSTRAINT `payer_frais_ibfk_1` FOREIGN KEY (`idFrais`) REFERENCES `frais` (`idFrais`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=491716 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`blaise`@`%`*/ /*!50003 TRIGGER `Log_Modification` BEFORE UPDATE ON `payer_frais` FOR EACH ROW BEGIN
  DECLARE ancienne_valeur TEXT;
  DECLARE nouvelle_valeur TEXT;
  DECLARE colonne_modifiée TEXT;

  
  IF SESSION_USER() LIKE 'uka@%' THEN

    
    IF OLD.Montant_paie <> NEW.Montant_paie THEN
      SET colonne_modifiée = 'Montant_paie';
      SET ancienne_valeur = CONCAT('Montant:', IFNULL(OLD.Montant_paie, ''));
      SET nouvelle_valeur = CONCAT('Montant:', IFNULL(NEW.Montant_paie, ''));

      CALL bdd_uka_audit.Modification_Log(
        SESSION_USER(),
        'payer_frais',
        colonne_modifiée,
        OLD.Id_payer_frais,
        ancienne_valeur,
        nouvelle_valeur,
        CONCAT_WS(', ',
          CONCAT('Matricule:', IFNULL(OLD.Matricule, '')),
          CONCAT('Mat_agent:', IFNULL(OLD.Mat_agent, '')),
          CONCAT('Ensemble:', IFNULL(OLD.Ensemble, '')),
          CONCAT('Motif_paie:', IFNULL(OLD.Motif_paie, ''))
        )
      );
    END IF;

    
    IF OLD.Date_paie <> NEW.Date_paie THEN
      SET colonne_modifiée = 'Date_paie';
      SET ancienne_valeur = CONCAT('Date:', IFNULL(OLD.Date_paie, ''));
      SET nouvelle_valeur = CONCAT('Date:', IFNULL(NEW.Date_paie, ''));

      CALL bdd_uka_audit.Modification_Log(
        SESSION_USER(),
        'payer_frais',
        colonne_modifiée,
        OLD.Id_payer_frais,
        ancienne_valeur,
        nouvelle_valeur,
        CONCAT_WS(', ',
          CONCAT('Matricule:', IFNULL(OLD.Matricule, '')),
          CONCAT('Mat_agent:', IFNULL(OLD.Mat_agent, '')),
          CONCAT('Ensemble:', IFNULL(OLD.Ensemble, '')),
          CONCAT('Motif_paie:', IFNULL(OLD.Motif_paie, ''))
        )
      );
    END IF;
    
     
    IF OLD.Mat_agent  <> NEW.Mat_agent  THEN
      SET colonne_modifiée = 'Mat_agent ';
      SET ancienne_valeur = CONCAT('Mat_agent :', IFNULL(OLD.Date_paie, ''));
      SET nouvelle_valeur = CONCAT('Mat_agent :', IFNULL(NEW.Date_paie, ''));

      CALL bdd_uka_audit.Modification_Log(
        SESSION_USER(),
        'payer_frais',
        colonne_modifiée,
        OLD.Id_payer_frais,
        ancienne_valeur,
        nouvelle_valeur,
        CONCAT_WS(', ',
          CONCAT('Matricule:', IFNULL(OLD.Matricule, '')),
          CONCAT('Mat_agent:', IFNULL(OLD.Mat_agent, '')),
          CONCAT('Ensemble:', IFNULL(OLD.Ensemble, '')),
          CONCAT('Motif_paie:', IFNULL(OLD.Motif_paie, ''))
        )
      );
    END IF;

  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`blaise`@`%`*/ /*!50003 TRIGGER `Log_Suppression` BEFORE DELETE ON `payer_frais` FOR EACH ROW BEGIN
  
  IF OLD.Motif_suppression IS NULL OR TRIM(OLD.Motif_suppression) = '' THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Motif de suppression obligatoire dans Motif_suppression';
  END IF;

  
  IF SESSION_USER() LIKE 'uka@%' THEN
    CALL bdd_uka_audit.Suppression_Log(
      SESSION_USER(),
      'payer_frais',
      'Montant_paie, Date_paie',
      OLD.Id_payer_frais,
      CONCAT('Montant:', IFNULL(OLD.Montant_paie, ''), ', Date:', IFNULL(OLD.Date_paie, '')),
      CONCAT_WS(', ',
        CONCAT('Matricule:', IFNULL(OLD.Matricule, '')),
        CONCAT('Mat_agent:', IFNULL(OLD.Mat_agent, '')),
        CONCAT('Ensemble:', IFNULL(OLD.Ensemble, '')),
        CONCAT('Motif_paie:', IFNULL(OLD.Motif_paie, '')),
        CONCAT('Motif_suppression:', IFNULL(OLD.Motif_suppression, ''))
      )
    );
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `photo`
--

DROP TABLE IF EXISTS `photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `photo` (
  `IdImage` int NOT NULL AUTO_INCREMENT,
  `Matricule` varchar(20) NOT NULL,
  `Photo` longblob,
  `Nom_image` varchar(255) DEFAULT NULL,
  `Type_image` varchar(50) DEFAULT NULL,
  `Avatar` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`IdImage`),
  KEY `fk_Image_Etudiant_idx` (`Matricule`),
  CONSTRAINT `fk_Image_Etudiant` FOREIGN KEY (`Matricule`) REFERENCES `etudiant` (`Matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `promotion`
--

DROP TABLE IF EXISTS `promotion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotion` (
  `Code_Promotion` varchar(10) NOT NULL,
  `idMentions` int NOT NULL,
  `Libelle_promotion` varchar(50) NOT NULL,
  `Abréviation` varchar(10) NOT NULL,
  PRIMARY KEY (`Code_Promotion`),
  KEY `fk_Promotion_Mentions1_idx` (`idMentions`),
  CONSTRAINT `fk_Promotion_Mentions1` FOREIGN KEY (`idMentions`) REFERENCES `mentions` (`idMentions`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recette_generale`
--

DROP TABLE IF EXISTS `recette_generale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recette_generale` (
  `Id_rubrique` int NOT NULL,
  `Ref_budget` int NOT NULL,
  `Montant` int NOT NULL,
  PRIMARY KEY (`Id_rubrique`,`Ref_budget`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `repartition`
--

DROP TABLE IF EXISTS `repartition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `repartition` (
  `Id_repartition` int NOT NULL AUTO_INCREMENT,
  `Id_rubrique` int NOT NULL,
  `Code_promotion` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `idAnnee_Acad` int NOT NULL,
  `Montant` float NOT NULL,
  PRIMARY KEY (`Id_repartition`),
  KEY `idAnnee_Acad` (`idAnnee_Acad`),
  KEY `Code_promotion` (`Code_promotion`),
  KEY `Id_rubrique` (`Id_rubrique`)
) ENGINE=InnoDB AUTO_INCREMENT=945 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rubrique`
--

DROP TABLE IF EXISTS `rubrique`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rubrique` (
  `Id_rubrique` int NOT NULL,
  `Libelle` varchar(100) NOT NULL,
  `Categorie` varchar(50) NOT NULL,
  PRIMARY KEY (`Id_rubrique`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `semestre`
--

DROP TABLE IF EXISTS `semestre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `semestre` (
  `Id_Semestre` int NOT NULL AUTO_INCREMENT,
  `libelle_semestre` varchar(45) DEFAULT NULL,
  `Niveau_semestre` int DEFAULT NULL,
  PRIMARY KEY (`Id_Semestre`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service` (
  `IdService` int NOT NULL AUTO_INCREMENT,
  `Libelle` varchar(100) NOT NULL,
  PRIMARY KEY (`IdService`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `session_actives`
--

DROP TABLE IF EXISTS `session_actives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_actives` (
  `id_session` int NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ID unique de la session PHP',
  `user_login` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Login de l''utilisateur',
  `user_matricule` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT 'Matricule de l''agent',
  `type_compte` enum('agent','jury') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'agent' COMMENT 'Type de compte connecté',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Adresse IP de connexion',
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Navigateur et système utilisé',
  `date_connexion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date et heure de connexion',
  `derniere_activite` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Dernière activité détectée',
  `statut` enum('active','inactive','expiration_en_cours') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'État de la session',
  `token_validation` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Token unique pour validation',
  PRIMARY KEY (`id_session`),
  UNIQUE KEY `unique_session_id` (`session_id`),
  KEY `idx_user_login` (`user_login`),
  KEY `idx_matricule` (`user_matricule`),
  KEY `idx_statut` (`statut`),
  KEY `idx_derniere_activite` (`derniere_activite`),
  CONSTRAINT `session_actives_ibfk_1` FOREIGN KEY (`user_matricule`) REFERENCES `agent` (`Mat_agent`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Gestion des sessions actives avec un seul utilisateur connecté à la fois';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `session_activites`
--

DROP TABLE IF EXISTS `session_activites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_activites` (
  `id_activite` int NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ID de la session PHP',
  `user_login` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Login de l''utilisateur',
  `page_visitee` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL ou page visitée',
  `action` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Type d''action (clic, navigation, etc.)',
  `date_activite` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp de l''activité',
  `donnees_supplementaires` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Données JSON supplémentaires',
  PRIMARY KEY (`id_activite`),
  KEY `idx_session_id` (`session_id`),
  KEY `idx_date_activite` (`date_activite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log détaillé des activités par session (optionnel pour analytics)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `session_demandes_connexion`
--

DROP TABLE IF EXISTS `session_demandes_connexion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_demandes_connexion` (
  `id_demande` int NOT NULL AUTO_INCREMENT,
  `user_login` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Login de l''utilisateur demandant la connexion',
  `user_matricule` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT 'Matricule de l''agent',
  `type_compte` enum('agent','jury') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'agent' COMMENT 'Type de compte à connecter',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP de la nouvelle demande',
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Navigateur de la nouvelle demande',
  `date_demande` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de la tentative de connexion',
  `statut_demande` enum('en_attente','acceptee','refusee','expiree') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente' COMMENT 'État de la demande',
  `reponse_user_actuel` enum('en_attente','accepte','refuse') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente' COMMENT 'Réponse de l''utilisateur actuellement connecté',
  `date_reponse_actuel` datetime DEFAULT NULL COMMENT 'Date de réponse de l''utilisateur actuel',
  `reponse_user_demandeur` enum('en_attente','accepte','refuse') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente' COMMENT 'Confirmation du demandeur',
  `date_reponse_demandeur` datetime DEFAULT NULL COMMENT 'Date de confirmation du demandeur',
  `session_id_actuelle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID de la session à remplacer',
  `token_demande` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token unique pour cette demande',
  `expiration` datetime DEFAULT NULL COMMENT 'Date d''expiration de la demande',
  PRIMARY KEY (`id_demande`),
  UNIQUE KEY `unique_token_demande` (`token_demande`),
  KEY `idx_user_login` (`user_login`),
  KEY `idx_statut_demande` (`statut_demande`),
  KEY `idx_expiration` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='File d''attente des demandes de connexion avec validation bidirectionnelle';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `session_historique`
--

DROP TABLE IF EXISTS `session_historique`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_historique` (
  `id_historique` int NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ID de la session PHP',
  `user_login` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Login de l''utilisateur',
  `user_matricule` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT 'Matricule de l''agent',
  `type_compte` enum('agent','jury') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'agent' COMMENT 'Type de compte',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Adresse IP',
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Navigateur utilisé',
  `date_connexion` datetime NOT NULL COMMENT 'Date de connexion',
  `date_deconnexion` datetime DEFAULT NULL COMMENT 'Date de déconnexion',
  `duree_session` int DEFAULT NULL COMMENT 'Durée en secondes',
  `raison_deconnexion` enum('manuelle','inactivite','force','concurrent','expiration') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Raison de la déconnexion',
  `nombre_activites` int DEFAULT '0' COMMENT 'Nombre d''activités durant la session',
  PRIMARY KEY (`id_historique`),
  KEY `idx_user_login` (`user_login`),
  KEY `idx_matricule` (`user_matricule`),
  KEY `idx_date_connexion` (`date_connexion`),
  KEY `idx_raison` (`raison_deconnexion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historique complet de toutes les sessions pour audit et statistiques';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `solde`
--

DROP TABLE IF EXISTS `solde`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solde` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `date_solde` datetime NOT NULL,
  `devise` varchar(10) NOT NULL,
  `montant` decimal(15,2) NOT NULL,
  `Observation` varchar(1000) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_budget`
--

DROP TABLE IF EXISTS `t_budget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_budget` (
  `Ref_budget` int NOT NULL AUTO_INCREMENT,
  `Libelle` varchar(50) NOT NULL,
  `Description` text NOT NULL,
  `Periodicite` varchar(25) NOT NULL,
  `Annee_debut` int NOT NULL,
  `Annee_fin` int NOT NULL,
  `Idservice` int DEFAULT NULL,
  `id_filiere` int DEFAULT NULL,
  PRIMARY KEY (`Ref_budget`),
  KEY `Idservice` (`Idservice`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_depense_generale`
--

DROP TABLE IF EXISTS `t_depense_generale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_depense_generale` (
  `Ref_budget` int NOT NULL,
  `Num_imputation` int NOT NULL,
  `Montant` int NOT NULL,
  PRIMARY KEY (`Ref_budget`,`Num_imputation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_depense_prevues`
--

DROP TABLE IF EXISTS `t_depense_prevues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_depense_prevues` (
  `Id_depense` int NOT NULL AUTO_INCREMENT,
  `Ref_budget` int NOT NULL,
  `Num_imputation` int DEFAULT NULL,
  `Montant` float NOT NULL,
  PRIMARY KEY (`Id_depense`),
  KEY `Num_compte` (`Num_imputation`),
  KEY `Ref_budget` (`Ref_budget`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_imputation`
--

DROP TABLE IF EXISTS `t_imputation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_imputation` (
  `Num_imputation` int NOT NULL,
  `Intitul_compte` varchar(50) NOT NULL,
  `Pourcent_budget` int NOT NULL,
  PRIMARY KEY (`Num_imputation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_jury_deliberation`
--

DROP TABLE IF EXISTS `t_jury_deliberation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_jury_deliberation` (
  `ID_jury` int NOT NULL AUTO_INCREMENT,
  `Date_délibération` date NOT NULL,
  `idAnnee_Acad` int NOT NULL,
  `Code_Promotion` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Libelle_jury` text,
  PRIMARY KEY (`ID_jury`),
  KEY `idAnnee_Acad` (`idAnnee_Acad`),
  KEY `Code_Promotion` (`Code_Promotion`),
  CONSTRAINT `t_jury_deliberation_ibfk_1` FOREIGN KEY (`idAnnee_Acad`) REFERENCES `annee_academique` (`idAnnee_Acad`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `t_jury_deliberation_ibfk_2` FOREIGN KEY (`Code_Promotion`) REFERENCES `promotion` (`Code_Promotion`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_membre_jury`
--

DROP TABLE IF EXISTS `t_membre_jury`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_membre_jury` (
  `ID_jury_membre` int NOT NULL AUTO_INCREMENT,
  `ID_jury` int NOT NULL,
  `Mat_agent` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `role` enum('Président','Secrétaire','Membre') DEFAULT 'Membre',
  `Login` varchar(50) DEFAULT NULL,
  `Mot_passe` varchar(255) DEFAULT NULL,
  `Statut` enum('Actif','Inactif') DEFAULT 'Actif',
  `date_ajout` datetime DEFAULT CURRENT_TIMESTAMP,
  `derniere_connexion` datetime DEFAULT NULL COMMENT 'Dernière connexion réussie',
  `session_active` tinyint(1) DEFAULT '0' COMMENT 'Indicateur de session active',
  `session_id_actuelle` varchar(255) DEFAULT NULL COMMENT 'ID de la session active',
  PRIMARY KEY (`ID_jury_membre`),
  UNIQUE KEY `unique_login` (`Login`),
  KEY `ID_jury` (`ID_jury`),
  KEY `Mat_agent` (`Mat_agent`),
  KEY `idx_session_active` (`session_active`),
  KEY `idx_membre_jury_session` (`session_id_actuelle`),
  CONSTRAINT `t_membre_jury_ibfk_1` FOREIGN KEY (`ID_jury`) REFERENCES `t_jury_deliberation` (`ID_jury`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `t_membre_jury_ibfk_2` FOREIGN KEY (`Mat_agent`) REFERENCES `agent` (`Mat_agent`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_recettes_prevues`
--

DROP TABLE IF EXISTS `t_recettes_prevues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_recettes_prevues` (
  `Id_recette` int NOT NULL AUTO_INCREMENT,
  `Designation` varchar(50) NOT NULL,
  `IdPromotion` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Id_type_Recette` int NOT NULL,
  `Ref_budget` int NOT NULL,
  PRIMARY KEY (`Id_recette`),
  KEY `Ref_budget` (`Ref_budget`),
  KEY `Id_type_recette` (`Id_type_Recette`),
  KEY `IdPromotion` (`IdPromotion`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_type_recette`
--

DROP TABLE IF EXISTS `t_type_recette`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_type_recette` (
  `Id_type_Recette` int NOT NULL AUTO_INCREMENT,
  `Libelle_type` varchar(80) NOT NULL,
  PRIMARY KEY (`Id_type_Recette`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taux_du_jours`
--

DROP TABLE IF EXISTS `taux_du_jours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `taux_du_jours` (
  `Id_Taux_du_jours` int NOT NULL AUTO_INCREMENT,
  `Montant_du_jour` float NOT NULL,
  `Date_modification` date NOT NULL,
  PRIMARY KEY (`Id_Taux_du_jours`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `type_conge`
--

DROP TABLE IF EXISTS `type_conge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `type_conge` (
  `IdTypeConge` int NOT NULL AUTO_INCREMENT,
  `Libelle` varchar(50) NOT NULL,
  PRIMARY KEY (`IdTypeConge`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unite_enseignement`
--

DROP TABLE IF EXISTS `unite_enseignement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unite_enseignement` (
  `Code_ue` varchar(10) NOT NULL,
  `IdFiliere` int NOT NULL,
  `Intitule_ue` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Catégorie` varchar(200) NOT NULL,
  PRIMARY KEY (`Code_ue`),
  KEY `fk_Unite_Enseignant_Semestre1_idx` (`IdFiliere`),
  CONSTRAINT `unite_enseignement_ibfk_1` FOREIGN KEY (`IdFiliere`) REFERENCES `filiere` (`IdFiliere`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'bdd_uka'
--
/*!50003 DROP PROCEDURE IF EXISTS `Ajouter_Membre_Jury` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Ajouter_Membre_Jury`(
    IN p_id_jury INT,
    IN p_mat_agent VARCHAR(50),
    IN p_role ENUM('Président','Secrétaire','Membre'),
    IN p_login VARCHAR(50),
    IN p_mot_passe VARCHAR(255),
    IN p_statut ENUM('Actif', 'Inactif'),
    OUT p_success BOOLEAN,
    OUT p_message VARCHAR(255),
    OUT p_id_membre INT
)
BEGIN
    DECLARE v_count INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET p_success = FALSE;
        SET p_message = 'Erreur lors de l''ajout du membre au jury.';
        SET p_id_membre = NULL;
        ROLLBACK;
END;

START TRANSACTION;
    SET p_success = FALSE;
    SET p_message = '';
    SET p_id_membre = NULL;
    IF p_id_jury IS NULL OR p_mat_agent IS NULL OR p_role IS NULL THEN
        SET p_message = 'Données manquantes : id_jury, mat_agent et role sont requis.';
        ROLLBACK;
    ELSE
        SELECT COUNT(*) INTO v_count 
        FROM t_membre_jury 
        WHERE ID_jury = p_id_jury AND Mat_agent = p_mat_agent;
        IF v_count > 0 THEN
            SET p_message = CONCAT('L''agent avec le matricule ', p_mat_agent, ' est déjà membre de ce jury (ID_jury=', p_id_jury, '). Un agent ne peut avoir qu''un seul compte par jury.');
            ROLLBACK;
        ELSE
            IF (p_role = 'Président' OR p_role = 'Secrétaire') AND (p_login IS NULL OR p_login = '' OR p_mot_passe IS NULL OR p_mot_passe = '') THEN
                SET p_message = 'Le login et le mot de passe sont obligatoires pour les Présidents et Secrétaires.';
                ROLLBACK;
            ELSE
                IF p_login IS NOT NULL AND p_login != '' THEN
                  SELECT COUNT(*) INTO v_count FROM t_membre_jury WHERE Login = p_login;
                    IF v_count > 0 THEN
                        SET p_message = 'Ce login est déjà utilisé par un autre membre.';
                        ROLLBACK;
                    ELSE
                        INSERT INTO t_membre_jury (ID_jury, Mat_agent, role, Login, Mot_passe, Statut, date_ajout) 
                        VALUES (p_id_jury, p_mat_agent, p_role, p_login, p_mot_passe, p_statut, NOW());
                        SET p_id_membre = LAST_INSERT_ID();
                        SET p_success = TRUE;
                        SET p_message = 'Membre ajouté avec succès au jury.';
                        COMMIT;
                    END IF;
                ELSE
                    INSERT INTO t_membre_jury 
                    (ID_jury, Mat_agent, role, date_ajout) 
                    VALUES (p_id_jury, p_mat_agent, p_role, NOW());
                    SET p_id_membre = LAST_INSERT_ID();
                    SET p_success = TRUE;
                    SET p_message = 'Membre ajouté avec succès au jury.';
                    COMMIT;
                END IF;
            END IF;
        END IF;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Ajout_Nouvel_Jury` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Ajout_Nouvel_Jury`(
    IN `p_Libelle_jury` TEXT, 
    IN `p_Date_deliberation` DATE, 
    IN `p_Code_Promotion` VARCHAR(10), 
    IN `p_idAnnee_Acad` INT
)
BEGIN
    INSERT INTO t_jury_deliberation (
        Libelle_jury,
        Date_délibération,
        Code_Promotion,
        idAnnee_Acad
    ) VALUES (
        p_Libelle_jury,
        p_Date_deliberation,
        p_Code_Promotion,
        p_idAnnee_Acad
    );
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `clean_inactive_sessions` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `clean_inactive_sessions`(IN inactivity_minutes INT)
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE v_session_id VARCHAR(255);
    DECLARE v_user_login VARCHAR(45);
    DECLARE v_date_connexion DATETIME;
    DECLARE v_derniere_activite DATETIME;
    DECLARE cur_sessions CURSOR FOR 
        SELECT session_id, user_login, date_connexion, derniere_activite
        FROM sessions_actives 
        WHERE statut = 'active' 
        AND derniere_activite < DATE_SUB(NOW(), INTERVAL inactivity_minutes MINUTE);
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    OPEN cur_sessions;
    read_loop: LOOP
        FETCH cur_sessions INTO v_session_id, v_user_login, v_date_connexion, v_derniere_activite;
        IF done THEN
            LEAVE read_loop;
        END IF;
        INSERT INTO historique_sessions 
            (session_id, user_login, user_matricule, type_compte, ip_address, user_agent, 
             date_connexion, date_deconnexion, duree_session, raison_deconnexion)
        SELECT 
            session_id, user_login, user_matricule, type_compte, ip_address, user_agent,
            date_connexion, NOW(), 
            TIMESTAMPDIFF(SECOND, date_connexion, NOW()),
            'inactivite'
        FROM sessions_actives 
        WHERE session_id = v_session_id;
        DELETE FROM sessions_actives WHERE session_id = v_session_id;
        UPDATE compte_agent 
        SET session_active = 0, session_id_actuelle = NULL 
        WHERE session_id_actuelle = v_session_id;
        UPDATE t_membre_jury 
        SET session_active = 0, session_id_actuelle = NULL 
        WHERE session_id_actuelle = v_session_id;
    END LOOP;
    CLOSE cur_sessions;
    UPDATE demandes_connexion 
    SET statut_demande = 'expiree' 
    WHERE statut_demande = 'en_attente' 
    AND expiration < NOW();
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Liste_Agent_Aligner` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Liste_Agent_Aligner`(IN `p_idfiliere` INT)
BEGIN
    SELECT 
        agent.Mat_agent AS mat_agent,
        CONCAT(agent.Nom_agent, ' ', agent.Post_agent, ' ', agent.Prenom) AS enseignant,
        agent.Sexe AS sexe,
        agent.Niveau_Etude AS niveau_etude,
        categorie.Libelle AS categorie,
        agent.Tel AS phone,
        agent.Mail AS email,
        agent.AdressePhysique AS adresse,
        agent.Grade AS titre_academique,
        agent.Domaine_etude AS domaine,
        agent.Institution_attachee AS institut_attache,
        filiere.Libelle_Filiere AS filiere
    FROM 
        agent
    JOIN 
        filiere ON agent.Id_filiere = filiere.IdFiliere
    JOIN 
    	categorie ON agent.IdCategorie=categorie.IdCategorie
    ORDER BY 
        CASE WHEN filiere.IdFiliere = p_idfiliere THEN 1 ELSE 2 END, 
        agent.Nom_agent, 
        agent.Post_agent, 
        agent.Prenom;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Liste_Assistants_Disponibles` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Liste_Assistants_Disponibles`(IN `p_Mat_agent` VARCHAR(20), IN `p_id_ec_aligne` INT, IN `p_idAnnee_Acad` INT, IN `p_Id_Semestre` INT, IN `p_Code_Promotion` VARCHAR(10), IN `p_id_filiere` INT)
BEGIN
    SELECT 
        ag.Mat_agent AS mat_assistant,         -- Matricule de l'assistant
        ag.Nom_agent,                         -- Nom de l'assistant
        ag.Post_agent,                        -- Post-nom de l'assistant
        ag.Prenom,                            -- Prénom de l'assistant
        CONCAT(ag.Nom_agent, ' ', ag.Post_agent, ' ', ag.Prenom) AS nom_complet, -- Nom complet formaté
        ag.Grade AS titre_academique,         -- Grade (ASS1/ASS2)
        ag.Domaine,                           -- Domaine de compétence
        ag.Mail,                              -- Email de l'assistant
        ag.Tel,                               -- Téléphone de l'assistant
        ag.Id_filiere,                        -- Filière de l'assistant (sert au tri prioritaire)
        CASE 
            WHEN p_id_ec_aligne IS NOT NULL AND eca_specific.Mat_assistant = ag.Mat_agent THEN TRUE
            ELSE FALSE
        END AS est_attache_a_cet_ec,
        CASE 
            WHEN eca_enseignant.Mat_assistant IS NOT NULL THEN TRUE
            ELSE FALSE
        END AS est_assigne_a_cet_enseignant,
        CASE 
            WHEN eca_global.Mat_assistant IS NOT NULL THEN TRUE
            ELSE FALSE
        END AS est_assigne_globalement,
        CASE 
            WHEN eca_annee.Mat_assistant IS NOT NULL THEN TRUE
            ELSE FALSE
        END AS est_assigne_dans_annee,
        COALESCE(COUNT(DISTINCT eca_enseignant.id_ec_aligne), 0) AS nombre_ecs_avec_enseignant,
        (SELECT COUNT(DISTINCT id_ec_aligne) 
         FROM element_constitutifs_aligne 
         WHERE Mat_assistant = ag.Mat_agent
         AND idAnnee_Acad = p_idAnnee_Acad
         AND Code_Promotion = p_Code_Promotion) AS nombre_ecs_total,
        CASE 
            WHEN ag.Id_filiere IS NULL THEN 7
            WHEN ag.Id_filiere = p_id_filiere 
                 AND (eca_enseignant.Mat_assistant = ag.Mat_agent 
                      OR (p_id_ec_aligne IS NOT NULL AND eca_specific.Mat_assistant = ag.Mat_agent)) THEN 1
            WHEN ag.Id_filiere = p_id_filiere 
                 AND eca_global.Mat_assistant IS NULL THEN 2
            WHEN ag.Id_filiere = p_id_filiere 
                 AND eca_global.Mat_assistant IS NOT NULL THEN 3
            WHEN eca_enseignant.Mat_assistant = ag.Mat_agent 
                 OR (p_id_ec_aligne IS NOT NULL AND eca_specific.Mat_assistant = ag.Mat_agent) THEN 4
            WHEN eca_global.Mat_assistant IS NULL THEN 5
            ELSE 6
        END AS ordre_tri
    FROM 
        agent ag   -- Table principale des agents (assistants)
    LEFT JOIN 
        element_constitutifs_aligne eca_specific
        ON ag.Mat_agent = eca_specific.Mat_assistant
        AND eca_specific.id_ec_aligne = p_id_ec_aligne
    LEFT JOIN 
        element_constitutifs_aligne eca_enseignant
        ON ag.Mat_agent = eca_enseignant.Mat_assistant
        AND eca_enseignant.Mat_agent = p_Mat_agent
        AND eca_enseignant.idAnnee_Acad = p_idAnnee_Acad
        AND eca_enseignant.Code_Promotion = p_Code_Promotion
    LEFT JOIN 
        element_constitutifs_aligne eca_global
        ON ag.Mat_agent = eca_global.Mat_assistant
        AND eca_global.idAnnee_Acad = p_idAnnee_Acad
        AND eca_global.Id_Semestre = p_Id_Semestre
    LEFT JOIN 
        element_constitutifs_aligne eca_annee
        ON ag.Mat_agent = eca_annee.Mat_assistant
        AND eca_annee.idAnnee_Acad = p_idAnnee_Acad
    LEFT JOIN 
        element_constitutifs_aligne eca_promotion ON ec.id_ec = eca_promotion.id_ec 
        AND eca_promotion.Code_Promotion = p_Code_Promotion
        AND eca_promotion.idAnnee_Acad = p_idAnnee_Acad
    LEFT JOIN 
        agent a_prom ON eca_promotion.Mat_agent = a_prom.Mat_agent
    WHERE 
        ag.Grade IN ('ASS1', 'ASS2')
    GROUP BY 
        ag.Mat_agent, ag.Nom_agent, ag.Post_agent, ag.Prenom, 
        ag.Grade, ag.Domaine, ag.Mail, ag.Tel, ag.Id_filiere,
        est_attache_a_cet_ec,
        est_assigne_a_cet_enseignant,
        est_assigne_globalement,
        est_assigne_dans_annee,
        ordre_tri
    ORDER BY 
        ordre_tri ASC,        -- D'abord par priorité filière + statut
        ag.Grade ASC,         -- Puis ASS1 avant ASS2
        ag.Nom_agent ASC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Liste_cote` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Liste_cote`(IN `pCode_Promotion` VARCHAR(10), IN `pId_Semestre` INT)
BEGIN
    SELECT 
        ev.Matricule,
        ev.id_ec_aligne,
        ev.Cote,
        ev.Cote_rattrapage, 
        ev.cote_compensee, 
        ev.cote_reste_apres_cedee,
        ev.Ligne_touchee_Matricule_id_ec_aligne
    FROM evaluer ev
    JOIN element_constitutifs_aligne ec ON ev.id_ec_aligne = ec.id_ec_aligne
    WHERE ec.Code_Promotion = pCode_Promotion
    AND ec.Id_Semestre = pId_Semestre;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Liste_cote_etudiant` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Liste_cote_etudiant`(
    IN pCode_Promotion VARCHAR(10),
    IN pId_Semestre INT,
    IN pMatricule VARCHAR(50)
)
BEGIN
    SELECT 
        ev.Matricule,
        ev.id_ec_aligne,
        ev.Cote,
        ev.Cote_rattrapage, 
        ev.cote_compensee, 
        ev.cote_reste_apres_cedee,
        ev.Ligne_touchee_Matricule_id_ec_aligne
    FROM evaluer ev
    JOIN element_constitutifs_aligne ec ON ev.id_ec_aligne = ec.id_ec_aligne
    WHERE ec.Code_Promotion = pCode_Promotion
      AND ec.Id_Semestre = pId_Semestre
      AND ev.Matricule = pMatricule;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Liste_EC_Aligne` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Liste_EC_Aligne`(
    IN `p_id_filiere` INT, 
    IN `p_Mat_agent` VARCHAR(20), 
    IN `p_idAnnee_Acad` INT, 
    IN `p_Id_Semestre` INT, 
    IN `p_Code_Promotion` VARCHAR(10)
)
BEGIN
    SELECT 
        ec.id_ec,
        ec.Intutile_ec,
        ec.Credit,
        ec.CMI,
        ec.Hr_TD,
        ec.Hr_TP,
        ec.TPE,
        ec.VHT,
        ue.Intitule_ue,
        f.Libelle_Filiere,
        eca_promotion.Id_Semestre,
        eca_promotion.Mat_agent,
        CONCAT(a_prom.Nom_agent, ' ', a_prom.Post_agent, ' ', a_prom.Prenom) AS Nom_Complet_Agent,
        a_prom.Grade AS Titre_Academique_Agent,
        CASE 
            WHEN eca_global.id_ec IS NOT NULL THEN TRUE
            ELSE FALSE
        END AS etat_ec_pris_sm_envoyer,
        CASE 
            WHEN eca.Mat_agent IS NOT NULL THEN TRUE
            ELSE FALSE
        END AS etat_ec_pris_enseignant,
        CASE 
            WHEN eca_annee.id_ec IS NOT NULL THEN TRUE
            ELSE FALSE
        END AS etat_ec_pris_dans_annee,
        CASE 
            WHEN eca_promotion.id_ec IS NOT NULL THEN TRUE
            ELSE FALSE
        END AS etat_ec_pris_dans_promotion,
        CASE 
            WHEN eca.Mat_agent = p_Mat_agent THEN 1
            WHEN eca_global.id_ec IS NOT NULL THEN 2
            ELSE 3
        END AS ordre_tri
    FROM 
        element_constitutifs ec
    JOIN 
        unite_enseignement ue ON ec.Code_ue = ue.Code_ue
    JOIN 
        filiere f ON ue.IdFiliere = f.IdFiliere
    LEFT JOIN 
        element_constitutifs_aligne eca ON ec.id_ec = eca.id_ec 
        AND eca.Mat_agent = p_Mat_agent
        AND eca.idAnnee_Acad = p_idAnnee_Acad
        AND eca.Id_Semestre = p_Id_Semestre
        AND eca.Code_Promotion = p_Code_Promotion
    LEFT JOIN 
        element_constitutifs_aligne eca_global ON ec.id_ec = eca_global.id_ec 
        AND eca_global.idAnnee_Acad = p_idAnnee_Acad
        AND eca_global.Id_Semestre = p_Id_Semestre
    LEFT JOIN 
        element_constitutifs_aligne eca_annee ON ec.id_ec = eca_annee.id_ec 
        AND eca_annee.idAnnee_Acad = p_idAnnee_Acad
    LEFT JOIN 
        element_constitutifs_aligne eca_promotion ON ec.id_ec = eca_promotion.id_ec 
        AND eca_promotion.Code_Promotion = p_Code_Promotion
        AND eca_promotion.idAnnee_Acad = p_idAnnee_Acad
    LEFT JOIN 
        agent a_prom ON eca_promotion.Mat_agent = a_prom.Mat_agent
    WHERE 
        f.IdFiliere = p_id_filiere
    ORDER BY 
        ordre_tri, ec.id_ec;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Liste_EC_aligner_delibee` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Liste_EC_aligner_delibee`(IN `promo_code` VARCHAR(10), IN `annee_acad` INT, IN `semestre_id` INT)
BEGIN
    SELECT 
        ue.Code_ue as cd_ue,
        ue.Intitule_ue,
        ec.id_ec,
        eca.id_ec_aligne,
        ec.Intutile_ec,
        ec.Credit,
        subquery.total_credits,
        subquery.nombre_ec
    FROM 
        element_constitutifs_aligne eca
    JOIN 
        element_constitutifs ec ON eca.id_ec = ec.id_ec
    JOIN 
        unite_enseignement ue ON ec.Code_ue = ue.Code_ue
    JOIN 
        (
            SELECT 
                ue.Code_ue,
                SUM(ec.Credit) AS total_credits,
                COUNT(ec.id_ec) AS nombre_ec
            FROM 
                element_constitutifs_aligne eca
            JOIN 
                element_constitutifs ec ON eca.id_ec = ec.id_ec
            JOIN 
                unite_enseignement ue ON ec.Code_ue = ue.Code_ue
            WHERE 
                eca.Code_Promotion = promo_code
                AND eca.idAnnee_Acad = annee_acad
                AND eca.Id_Semestre = semestre_id
            GROUP BY 
                ue.Code_ue
        ) AS subquery ON ue.Code_ue = subquery.Code_ue
    WHERE 
        eca.Code_Promotion = promo_code
        AND eca.idAnnee_Acad = annee_acad
        AND eca.Id_Semestre = semestre_id
    ORDER BY 
        subquery.total_credits DESC, 
        ue.Code_ue DESC,
        ec.Credit DESC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Liste_EC_UE_donnee` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Liste_EC_UE_donnee`(IN `p_Code_ue` VARCHAR(10))
BEGIN
     SELECT 
        id_ec AS id_ec, 
        Intutile_ec AS nom_ec, 
        CMI AS cmi, 
        Hr_TD AS hr_td, 
        Hr_TP AS hr_tp, 
        TPE AS tpe, 
        VHT AS vht, 
        Credit AS credit
    FROM element_constitutifs
    WHERE Code_ue = p_Code_ue;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Liste_Enseignants_Aligner` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Liste_Enseignants_Aligner`(IN `p_idfiliere` INT)
BEGIN
    SELECT
        agent.Mat_agent AS mat_agent,
        CONCAT(agent.Nom_agent, ' ', agent.Post_agent, ' ', agent.Prenom) AS enseignant,
        agent.Sexe AS sexe,
        agent.Niveau_Etude AS niveau_etude,
        categorie.Libelle AS categorie,
        agent.Tel AS phone,
        agent.Mail AS email,
        agent.AdressePhysique AS adresse,
        agent.Grade AS titre_academique,
        agent.Domaine_etude AS domaine,
        agent.Institution_attachee AS institut_attache,
        filiere.IdFiliere AS id_filiere,
        filiere.Libelle_Filiere AS filiere
    FROM
        agent
    JOIN
        filiere ON agent.Id_filiere = filiere.IdFiliere
    JOIN
        categorie ON agent.IdCategorie=categorie.IdCategorie
    ORDER BY
        CASE WHEN filiere.IdFiliere = p_idfiliere THEN 1 ELSE 2 END,
        agent.Nom_agent,
        agent.Post_agent,
        agent.Prenom;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Liste_etudiant_deliberation` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Liste_etudiant_deliberation`(IN `promo_code` VARCHAR(10), IN `annee_acad` INT)
BEGIN
    SELECT 
        e.Matricule,
        CONCAT(
            IFNULL(e.Nom, ''), 
            ' ', 
            IFNULL(e.Postnom, ''), 
            ' ', 
            IFNULL(e.Prenom, '')
        ) AS ident_etudiant
    FROM etudiant e
    INNER JOIN passer_par pp ON e.Matricule = pp.Etudiant_Matricule
    INNER JOIN promotion p ON pp.Code_Promotion = p.Code_Promotion
    INNER JOIN mentions m ON p.idMentions = m.idMentions
    INNER JOIN filiere f ON m.IdFiliere = f.IdFiliere
    INNER JOIN annee_academique aa ON pp.idAnnee_academique = aa.idAnnee_Acad
    WHERE pp.Code_Promotion = promo_code
      AND aa.idAnnee_Acad = annee_acad
    ORDER BY e.Nom ASC, e.Postnom ASC, e.Prenom ASC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Liste_Memebre_jury` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Liste_Memebre_jury`(IN `mat_agent` VARCHAR(20))
BEGIN
	DECLARE v_id_annee_academique INT;
    SELECT idAnnee_Acad INTO v_id_annee_academique
    FROM annee_academique
    ORDER BY Annee_debut DESC
    LIMIT 1;
    SELECT compte_agent.Login, 
			compte_agent.Mot_passe,
            compte_agent.Etat,
            compte_agent.Categorie,
            concat(promotion.Abréviation,' ',mentions.Libelle_mention) as prom
    FROM compte_agent JOIN promotion ON compte_agent.Code_promotion=promotion.Code_Promotion
    JOIN mentions ON promotion.idMentions=mentions.idMentions
    WHERE 
    	compte_agent.Mat_agent=mat_agent
        AND compte_agent.id_annee_academique=v_id_annee_academique;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Liste_UE_Filiere` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Liste_UE_Filiere`(IN `id_filiere` INT)
BEGIN
    SELECT 
        unite_enseignement.Code_ue AS Code_ue,
        unite_enseignement.Intitule_ue AS nom_ue,
        unite_enseignement.Catégorie AS categorie_ue
    FROM 
        unite_enseignement
    WHERE 
        unite_enseignement.IdFiliere = id_filiere;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Modifier_cote` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Modifier_cote`(
    IN pMatricule VARCHAR(20),
    IN pid_ec_aligne INT,
    IN pCote FLOAT,
    IN pType VARCHAR(20)
)
BEGIN
    IF LOWER(pType) = 'rattrapage' THEN
        UPDATE evaluer
        SET Cote_rattrapage = pCote
        WHERE Matricule = pMatricule AND id_ec_aligne = pid_ec_aligne;
    ELSE
        UPDATE evaluer
        SET Cote = pCote
        WHERE Matricule = pMatricule AND id_ec_aligne = pid_ec_aligne;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Nouve` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Nouve`(IN `p_idAnnee_Acad` INT, IN `p_id_ec` INT, IN `p_Id_Semestre` INT, IN `p_Code_Promotion` VARCHAR(10), IN `p_Mat_agent` VARCHAR(20))
BEGIN
    INSERT INTO element_constitutifs_aligne (
        idAnnee_Acad,
        id_ec,
        Id_Semestre,
        Code_Promotion,
        Mat_agent
    ) VALUES (
        p_idAnnee_Acad,
        p_id_ec,
        p_Id_Semestre,
        p_Code_Promotion,
        p_Mat_agent
    );
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Nouvelle_Cote` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Nouvelle_Cote`(
    IN pMatricule VARCHAR(20),
    IN pid_ec_aligne INT,
    IN pCote FLOAT,
    IN pType VARCHAR(20)
)
BEGIN
    IF LOWER(pType) = 'rattrapage' THEN
        INSERT INTO evaluer (Matricule, id_ec_aligne, Cote_rattrapage)
        VALUES (pMatricule, pid_ec_aligne, pCote);
    ELSE
        INSERT INTO evaluer (Matricule, id_ec_aligne, Cote)
        VALUES (pMatricule, pid_ec_aligne, pCote);
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Nouvelle_UE` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Nouvelle_UE`(IN `p_Code_ue` VARCHAR(10), IN `p_IdFiliere` INT, IN `p_Intitule_ue` VARCHAR(200), IN `p_Categorie` VARCHAR(200))
BEGIN
    INSERT INTO unite_enseignement (Code_ue, IdFiliere, Intitule_ue, Catégorie)
    VALUES (p_Code_ue, p_IdFiliere, p_Intitule_ue, p_Categorie);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Nouvel_EC` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Nouvel_EC`(IN `p_Code_ue` VARCHAR(10), IN `p_Intutile_ec` VARCHAR(200), IN `p_Credit` INT, IN `p_CMI` INT, IN `p_Hr_TD` INT, IN `p_Hr_TP` INT, IN `p_TPE` INT, IN `p_VHT` INT)
BEGIN
    INSERT INTO element_constitutifs (
        Code_ue,
        Intutile_ec,
        Credit,
        CMI,
        Hr_TD,
        Hr_TP,
        TPE,
        VHT
    ) VALUES (
        p_Code_ue,
        p_Intutile_ec,
        p_Credit,
        p_CMI,
        p_Hr_TD,
        p_Hr_TP,
        p_TPE,
        p_VHT
    );
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Nouvel_EC_Aligne` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Nouvel_EC_Aligne`(IN `p_idAnnee_Acad` INT, IN `p_id_ec` INT, IN `p_Id_Semestre` INT, IN `p_Code_Promotion` VARCHAR(10), IN `p_Mat_agent` VARCHAR(20), IN `p_Mat_assistant` VARCHAR(20))
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM element_constitutifs_aligne
        WHERE idAnnee_Acad = p_idAnnee_Acad
          AND id_ec        = p_id_ec
          AND Id_Semestre  = p_Id_Semestre
          AND Code_Promotion = p_Code_Promotion
          AND Mat_agent    = p_Mat_agent
          AND ( (Mat_assistant IS NULL AND p_Mat_assistant IS NULL)
                OR Mat_assistant = p_Mat_assistant )
    ) THEN
        INSERT INTO element_constitutifs_aligne (
            idAnnee_Acad,
            id_ec,
            Id_Semestre,
            Code_Promotion,
            Mat_agent,
            Mat_assistant
        ) VALUES (
            p_idAnnee_Acad,
            p_id_ec,
            p_Id_Semestre,
            p_Code_Promotion,
            p_Mat_agent,
            p_Mat_assistant
        );
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Nouvel_membre_jury` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Nouvel_membre_jury`(IN `p_Mat_agent` VARCHAR(20), IN `p_Code_promotion` VARCHAR(10), IN `p_Login` VARCHAR(20), IN `p_Password` VARCHAR(255), IN `p_categorie` VARCHAR(50), IN `p_Etat` VARCHAR(15), IN `p_id_filiere` INT)
BEGIN
	DECLARE v_id_annee_academique INT;  
    DECLARE v_count INT;
    SELECT idAnnee_Acad INTO v_id_annee_academique
    FROM annee_academique
    ORDER BY Annee_debut DESC
    LIMIT 1;
    SELECT COUNT(*) INTO v_count
    FROM compte_agent
    WHERE Mat_agent = p_Mat_agent
      AND Code_promotion = p_Code_promotion
      AND Login=p_Login
      AND Mot_passe=p_Password
      AND id_annee_academique = v_id_annee_academique;
 	IF v_count > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Le membre du jury existe déjà pour cette année et cette promotion.';
    ELSE
    INSERT INTO compte_agent (
        Mat_agent,
        Id_filiere,
        Code_promotion,
        id_annee_academique,
        Login,
        Mot_passe,
        Etat,
        Categorie,
        Fonction
    ) VALUES (
        p_Mat_agent,
        p_id_filiere,
        p_Code_promotion,
        v_id_annee_academique,
        p_Login,
        p_Password,
        p_Etat,
        p_categorie,
        'Jury_deliberation'
    );
     END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Supprimer_cote` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Supprimer_cote`(
    IN p_mat_etudiant VARCHAR(20),
    IN p_id_ec INT,
    IN pType VARCHAR(20)
)
BEGIN
    IF LOWER(pType) = 'rattrapage' THEN
        UPDATE evaluer
        SET Cote_rattrapage = NULL
        WHERE Matricule = p_mat_etudiant AND id_ec_aligne = p_id_ec;
    ELSE
        UPDATE evaluer
        SET Cote = NULL
        WHERE Matricule = p_mat_etudiant AND id_ec_aligne = p_id_ec;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Supprimer_EC` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Supprimer_EC`(IN `p_id_ec` INT)
BEGIN
    DELETE FROM element_constitutifs
    WHERE id_ec = p_id_ec;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Supprimer_EC_Aligne` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Supprimer_EC_Aligne`(IN `p_idAnnee_Acad` INT, IN `p_id_ec` INT, IN `p_Id_Semestre` INT, IN `p_Code_Promotion` VARCHAR(10), IN `p_Mat_agent` VARCHAR(20), IN `p_Mat_assistant` VARCHAR(20))
BEGIN
    DELETE FROM element_constitutifs_aligne
    WHERE idAnnee_Acad = p_idAnnee_Acad
      AND id_ec        = p_id_ec
      AND Id_Semestre  = p_Id_Semestre
      AND Code_Promotion = p_Code_Promotion
      AND Mat_agent    = p_Mat_agent
      AND ( (Mat_assistant IS NULL AND p_Mat_assistant IS NULL)
            OR Mat_assistant = p_Mat_assistant );
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Supprimer_UE` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`blaise`@`%` PROCEDURE `Supprimer_UE`(IN `p_Code_ue` VARCHAR(10))
BEGIN
    DELETE FROM unite_enseignement
    WHERE Code_ue = p_Code_ue;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-16 14:07:42
