-- ==============================================================
-- NEXUS BMB TECH SOFT BANKING
-- FICHIER 3/3 : MODULE CAISSE GUICHET (MULTI-DEVISES)
-- ==============================================================
-- Dépendances : db_01_laravel_core.sql ET db_02_rh_banque_core.sql
--               doivent être exécutés avant ce fichier.
--
-- Contenu :
--   [COMPTA]  tb_plan_comptable
--   [CAISSE]  tb_caisses_guichets        (guichets, table mère)
--   [CAISSE]  tb_caisses_guichets_soldes  (soldes par devise — multi-devises)
--   [CAISSE]  tb_mouvements_inter_caisses (alimentations / dégagements)
--   [CAISSE]  tb_cloture_caisse           (arrêté de caisse par devise)
--
-- Principe multi-devises :
--   Un guichet unique peut détenir simultanément des CDF et des USD.
--   Les soldes sont portés par tb_caisses_guichets_soldes (une ligne
--   par couple guichet+devise). La clôture se fait devise par devise.
--
--   Dans Laravel, mise à jour de solde :
--     DB::table('tb_caisses_guichets_soldes')
--       ->where('guichet_id', $id)->where('devise_code', $devise)
--       ->increment('solde_en_caisse', $montant);
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
DROP TABLE IF EXISTS `tb_cloture_caisse`;
DROP TABLE IF EXISTS `tb_mouvements_inter_caisses`;
DROP TABLE IF EXISTS `tb_caisses_guichets_soldes`;
DROP TABLE IF EXISTS `tb_caisses_guichets`;
DROP TABLE IF EXISTS `tb_plan_comptable`;

-- ==============================================================
-- SECTION A — PLAN COMPTABLE
-- ==============================================================

-- A1. Plan comptable simplifié (référentiel des numéros de compte)
-- ex : 5701 → Caisse CDF | 5702 → Caisse USD | 2511 → Dépôts clients
CREATE TABLE `tb_plan_comptable` (
  `numero_compte` varchar(20)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle`       varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_compte`   enum('ACTIF','PASSIF','CHARGE','PRODUIT')
                  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`numero_compte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- SECTION B — GUICHETS (table mère)
-- ==============================================================

-- B1. Caisses / Guichets (points de service financier)
-- L'agent titulaire est désigné par tb_affectations (fichier 2).
-- Les soldes sont dans tb_caisses_guichets_soldes (table ci-dessous).
-- Pas de colonne devise ni solde ici → architecture multi-devises.
CREATE TABLE `tb_caisses_guichets` (
  `id`                   bigint unsigned NOT NULL AUTO_INCREMENT,
  `code_guichet`         varchar(20)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
                         -- ex: G01, G02
  `intitule`             varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut_operationnel`  enum('OUVERT','FERME','SUSPENDU')
                         CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'FERME',
  `created_at`           timestamp    NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- SECTION C — SOLDES MULTI-DEVISES
-- ==============================================================

-- C1. Positions de caisse par devise
-- Une ligne = un solde pour UN guichet dans UNE devise.
-- Contrainte UNIQUE (guichet_id, devise_code) : impossible d'avoir
-- deux lignes USD pour le même guichet.
-- Dépend de : tb_caisses_guichets, tb_devises (fichier 2)
CREATE TABLE `tb_caisses_guichets_soldes` (
  `id`              bigint unsigned NOT NULL AUTO_INCREMENT,
  `guichet_id`      bigint unsigned NOT NULL,
  `devise_code`     varchar(3)   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `solde_en_caisse` decimal(18,2) NOT NULL DEFAULT '0.00',
  `updated_at`      timestamp     NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_guichet_devise` (`guichet_id`, `devise_code`),
  CONSTRAINT `fk_solde_guichet`
    FOREIGN KEY (`guichet_id`)  REFERENCES `tb_caisses_guichets` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_solde_devise`
    FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`)
    ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- SECTION D — MOUVEMENTS DE FONDS
-- ==============================================================

-- D1. Mouvements inter-caisses (alimentations, dégagements, transferts)
-- Dépend de : tb_caisses_guichets, tb_agents (fichier 2), tb_devises (fichier 2)
CREATE TABLE `tb_mouvements_inter_caisses` (
  `id`                  bigint unsigned NOT NULL AUTO_INCREMENT,
  `guichet_source_id`   bigint unsigned DEFAULT NULL,         -- NULL = alimentation depuis vault
  `guichet_dest_id`     bigint unsigned DEFAULT NULL,         -- NULL = dégagement vers vault
  `agent_initiateur`    varchar(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_flux`           enum('ALIMENTATION','DEGAGEMENT','TRANSFERT')
                        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant`             decimal(18,2) NOT NULL,
  `devise_code`         varchar(3)   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_bordereau` varchar(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_mouvement`      timestamp    DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_reference_bordereau` (`reference_bordereau`),
  CONSTRAINT `fk_mouv_guichet_src`
    FOREIGN KEY (`guichet_source_id`) REFERENCES `tb_caisses_guichets` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_mouv_guichet_dest`
    FOREIGN KEY (`guichet_dest_id`)   REFERENCES `tb_caisses_guichets` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_mouv_agent`
    FOREIGN KEY (`agent_initiateur`)  REFERENCES `tb_agents` (`matricule`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_mouv_devise`
    FOREIGN KEY (`devise_code`)       REFERENCES `tb_devises` (`code_iso`)
    ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- SECTION E — CLÔTURE DE CAISSE
-- ==============================================================

-- E1. Arrêté de caisse (une ligne par guichet + devise + journée)
-- L'agent effectue un billetage séparé pour chaque devise détenue.
-- solde_comptable est lu depuis tb_caisses_guichets_soldes au moment de la clôture.
-- Dépend de : tb_caisses_guichets, tb_devises (fichier 2), tb_agents (fichier 2)
CREATE TABLE `tb_cloture_caisse` (
  `id`               bigint unsigned NOT NULL AUTO_INCREMENT,
  `guichet_id`       bigint unsigned NOT NULL,
  `devise_code`      varchar(3)   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `solde_comptable`  decimal(18,2) NOT NULL, -- Solde système (tb_caisses_guichets_soldes)
  `solde_physique`   decimal(18,2) NOT NULL, -- Solde compté physiquement par l'agent
  `ecart_caisse`     decimal(18,2) NOT NULL, -- = solde_physique - solde_comptable
  `detail_billetage` json          DEFAULT NULL, -- Détail coupures : {"50000":3, "20000":5, ...}
  `agent_cloturant`  varchar(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_cloture`     timestamp    DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_cloture_guichet`
    FOREIGN KEY (`guichet_id`)     REFERENCES `tb_caisses_guichets` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cloture_devise`
    FOREIGN KEY (`devise_code`)    REFERENCES `tb_devises` (`code_iso`)
    ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cloture_agent`
    FOREIGN KEY (`agent_cloturant`) REFERENCES `tb_agents` (`matricule`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- CONTRAINTE CROISÉE : tb_affectations.guichet_id → tb_caisses_guichets
-- Cette FK ne peut être ajoutée qu'ici car tb_caisses_guichets
-- est défini dans ce fichier (db_03) APRÈS tb_affectations (db_02).
-- ==============================================================
ALTER TABLE `tb_affectations`
  ADD CONSTRAINT `fk_affectation_guichet`
    FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- ==============================================================
-- FIN DU FICHIER 3/3
-- Base de données Nexus BMB complète.
--
-- Ordre d'exécution :
--   1. db_01_laravel_core.sql
--   2. db_02_rh_banque_core.sql
--   3. db_03_caisse_guichet.sql
-- ==============================================================

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
