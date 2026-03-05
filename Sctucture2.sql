-- ==========================================================
-- STRUCTURE BANCAIRE NEXUS – EXTENSION CAISSE GUICHET
-- ==========================================================
-- Extension de Structure.sql (base existante)
-- Prérequis : tb_agents, tb_devises, tb_affectations doivent exister.

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

-- Ordre de suppression : enfants avant parents
DROP TABLE IF EXISTS `tb_cloture_caisse`;
DROP TABLE IF EXISTS `tb_mouvements_inter_caisses`;
DROP TABLE IF EXISTS `tb_caisses_guichets_soldes`;
DROP TABLE IF EXISTS `tb_caisses_guichets`;
DROP TABLE IF EXISTS `tb_plan_comptable`;

-- 1. PLAN COMPTABLE (Le Référentiel des comptes généraux)
CREATE TABLE `tb_plan_comptable` (
  `numero_compte` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, -- ex: 5701 (Caisse), 2511 (Dépôts clients)
  `libelle` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_compte` ENUM('ACTIF', 'PASSIF', 'CHARGE', 'PRODUIT') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`numero_compte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. CAISSES GUICHETS (Les points de service financier — table mère, sans solde ni devise)
-- L'affectation de l'agent est gérée par la table RH (tb_affectations).
-- Les soldes par devise sont portés par tb_caisses_guichets_soldes.
CREATE TABLE `tb_caisses_guichets` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code_guichet` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE, -- ex: G01, G02
  `intitule` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut_operationnel` ENUM('OUVERT', 'FERME', 'SUSPENDU') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'FERME',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2b. SOLDES MULTI-DEVISES PAR GUICHET (Positions de caisse par devise)
-- Chaque ligne représente le solde d'UN guichet pour UNE devise donnée.
-- Laravel : UPDATE tb_caisses_guichets_soldes SET solde_en_caisse = ... WHERE guichet_id = X AND devise_code = Y
CREATE TABLE `tb_caisses_guichets_soldes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `guichet_id` BIGINT UNSIGNED NOT NULL,
  `devise_code` VARCHAR(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `solde_en_caisse` DECIMAL(18,2) NOT NULL DEFAULT '0.00',
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_guichet_devise` (`guichet_id`, `devise_code`), -- Un seul solde par couple (guichet, devise)
  CONSTRAINT `fk_solde_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_solde_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. MOUVEMENTS INTER-CAISSES (Alimentations et Dégagements)
CREATE TABLE `tb_mouvements_inter_caisses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `guichet_source_id` BIGINT UNSIGNED DEFAULT NULL,
  `guichet_dest_id` BIGINT UNSIGNED DEFAULT NULL,
  `agent_initiateur` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,  -- FK → tb_agents.matricule
  `type_flux` ENUM('ALIMENTATION', 'DEGAGEMENT', 'TRANSFERT') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant` DECIMAL(18,2) NOT NULL,
  `devise_code` VARCHAR(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,         -- FK → tb_devises.code_iso
  `reference_bordereau` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci UNIQUE,
  `date_mouvement` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mouv_guichet_src`  FOREIGN KEY (`guichet_source_id`) REFERENCES `tb_caisses_guichets` (`id`),
  CONSTRAINT `fk_mouv_guichet_dest` FOREIGN KEY (`guichet_dest_id`)   REFERENCES `tb_caisses_guichets` (`id`),
  CONSTRAINT `fk_mouv_agent`        FOREIGN KEY (`agent_initiateur`)   REFERENCES `tb_agents`           (`matricule`) ON UPDATE CASCADE,
  CONSTRAINT `fk_mouv_devise`        FOREIGN KEY (`devise_code`)        REFERENCES `tb_devises`           (`code_iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. CLOTURE DE CAISSE (Procédure de fin de journée — une ligne par devise et par guichet)
-- En multi-devises, l'agent effectue un billetage séparé pour chaque devise détenue.
CREATE TABLE `tb_cloture_caisse` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `guichet_id` BIGINT UNSIGNED NOT NULL,
  `devise_code` VARCHAR(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,       -- Clôture par devise
  `solde_comptable` DECIMAL(18,2) NOT NULL,                                                  -- Solde logiciel (issu de tb_caisses_guichets_soldes)
  `solde_physique` DECIMAL(18,2) NOT NULL,                                                   -- Solde compté physiquement
  `ecart_caisse` DECIMAL(18,2) NOT NULL,                                                     -- Différence (solde_physique - solde_comptable)
  `detail_billetage` JSON DEFAULT NULL,                                                      -- Détail des coupures par devise
  `agent_cloturant` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,   -- FK → tb_agents.matricule
  `date_cloture` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_cloture_guichet` FOREIGN KEY (`guichet_id`)      REFERENCES `tb_caisses_guichets` (`id`),
  CONSTRAINT `fk_cloture_devise`  FOREIGN KEY (`devise_code`)      REFERENCES `tb_devises`           (`code_iso`),
  CONSTRAINT `fk_cloture_agent`   FOREIGN KEY (`agent_cloturant`)  REFERENCES `tb_agents`            (`matricule`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
