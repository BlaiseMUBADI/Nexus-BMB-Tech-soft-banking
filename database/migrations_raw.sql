-- =====================================================================
-- NEXUS BMB - SCHEMA + SEED COMPLET (converti depuis les migrations Laravel)
-- Genere pour import direct dans phpMyAdmin (MySQL / MariaDB)
-- Encodage : utf8mb4 | Ordre : par prefixe date du nom de fichier
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET @now = NOW();

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================== 2026_03_05_000001_laravel_core =====================
CREATE TABLE IF NOT EXISTS `cache` (
  `key` VARCHAR(191) NOT NULL,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` VARCHAR(191) NOT NULL,
  `owner` VARCHAR(191) NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(191) NOT NULL,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` VARCHAR(191) NOT NULL,
  `name` VARCHAR(191) NOT NULL,
  `total_jobs` INT NOT NULL,
  `pending_jobs` INT NOT NULL,
  `failed_jobs` INT NOT NULL,
  `failed_job_ids` LONGTEXT NOT NULL,
  `options` MEDIUMTEXT NULL,
  `cancelled_at` INT NULL,
  `created_at` INT NOT NULL,
  `finished_at` INT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` VARCHAR(191) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL,
  `reserved_at` INT UNSIGNED NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` VARCHAR(191) NOT NULL,
  `token` VARCHAR(191) NOT NULL,
  `created_at` TIMESTAMP NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` VARCHAR(191) NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_roles` (
  `code` VARCHAR(20) NOT NULL,
  `nom` VARCHAR(191) NOT NULL,
  `description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `tb_roles_nom_unique` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_permissions` (
  `code` VARCHAR(20) NOT NULL,
  `nom` VARCHAR(191) NOT NULL,
  `description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `tb_permissions_nom_unique` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_role_permission` (
  `role_code` VARCHAR(20) NOT NULL,
  `permission_code` VARCHAR(20) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`role_code`,`permission_code`),
  KEY `fk_rp_permission` (`permission_code`),
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_code`) REFERENCES `tb_roles`(`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_code`) REFERENCES `tb_permissions`(`code`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================== 2026_03_05_000002_rh_banque_core =====================
CREATE TABLE IF NOT EXISTS `tb_services` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(191) NOT NULL,
  `description` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_postes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_id` BIGINT UNSIGNED NOT NULL,
  `nom` VARCHAR(191) NOT NULL,
  `description` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `tb_postes_service_id_foreign` (`service_id`),
  CONSTRAINT `tb_postes_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `tb_services`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_agents` (
  `matricule` VARCHAR(50) NOT NULL,
  `nom` VARCHAR(191) NOT NULL,
  `postnom` VARCHAR(191) NULL,
  `prenom` VARCHAR(191) NULL,
  `sexe` ENUM('M','F') NULL,
  `date_naissance` DATE NULL,
  `telephone` VARCHAR(50) NULL,
  `email` VARCHAR(191) NULL,
  `adresse` VARCHAR(191) NULL,
  `photo` VARCHAR(255) NULL,
  `date_embauche` DATE NULL,
  `statut` ENUM('actif','inactif') NOT NULL DEFAULT 'actif',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `agent_matricule` VARCHAR(50) NULL,
  `name` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `email_verified_at` TIMESTAMP NULL,
  `password` VARCHAR(191) NOT NULL,
  `remember_token` VARCHAR(100) NULL,
  `etat` VARCHAR(20) NOT NULL DEFAULT 'actif',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `fk_agent_matricule` (`agent_matricule`),
  CONSTRAINT `fk_agent_matricule` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents`(`matricule`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_role_user` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `role_code` VARCHAR(20) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `contrainte_user` (`user_id`),
  KEY `contrainte_role` (`role_code`),
  CONSTRAINT `contrainte_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `contrainte_role` FOREIGN KEY (`role_code`) REFERENCES `tb_roles`(`code`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_zones` (
  `code_zone` VARCHAR(50) NOT NULL,
  `nom` VARCHAR(100) NOT NULL,
  `agent_commercial_matricule` VARCHAR(50) NOT NULL,
  `commune` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`code_zone`),
  KEY `tb_zones_ibfk_1` (`agent_commercial_matricule`),
  CONSTRAINT `tb_zones_ibfk_1` FOREIGN KEY (`agent_commercial_matricule`) REFERENCES `tb_agents`(`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_affectations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `agent_matricule` VARCHAR(50) NOT NULL,
  `poste_id` BIGINT UNSIGNED NOT NULL,
  `date_debut` DATE NOT NULL,
  `date_fin` DATE NULL,
  `Etat` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `tb_affectations_agent_matricule_foreign` (`agent_matricule`),
  KEY `tb_affectations_poste_id_foreign` (`poste_id`),
  CONSTRAINT `tb_affectations_agent_matricule_foreign` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents`(`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `tb_affectations_poste_id_foreign` FOREIGN KEY (`poste_id`) REFERENCES `tb_postes`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_portefeuilles_agents` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `agent_matricule` VARCHAR(50) NOT NULL,
  `nom_portefeuille` VARCHAR(100) NOT NULL,
  `taux_commission_agent` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_port_agent` (`agent_matricule`),
  CONSTRAINT `fk_port_agent` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents`(`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_devises` (
  `code_iso` VARCHAR(3) NOT NULL,
  `nom` VARCHAR(50) NOT NULL,
  `symbole` VARCHAR(5) NOT NULL,
  `est_reference` TINYINT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`code_iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_taux_echanges` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `devise_source` VARCHAR(3) NOT NULL,
  `devise_destination` VARCHAR(3) NOT NULL,
  `taux` DECIMAL(18,4) NOT NULL,
  `date_application` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `fk_devise_src` (`devise_source`),
  KEY `fk_devise_dest` (`devise_destination`),
  CONSTRAINT `fk_devise_src` FOREIGN KEY (`devise_source`) REFERENCES `tb_devises`(`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_devise_dest` FOREIGN KEY (`devise_destination`) REFERENCES `tb_devises`(`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_clients` (
  `matricule` VARCHAR(191) NOT NULL,
  `code_zone` VARCHAR(50) NOT NULL,
  `nom` VARCHAR(191) NOT NULL,
  `postnom` VARCHAR(191) NOT NULL,
  `prenom` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) NULL,
  `telephone` VARCHAR(191) NULL,
  `sexe` ENUM('M','F') NOT NULL,
  `date_naissance` DATE NOT NULL,
  `lieu_naissance` VARCHAR(191) NOT NULL,
  `adresse` VARCHAR(191) NOT NULL,
  `etat_civil` VARCHAR(191) NOT NULL,
  `nom_conjoint` VARCHAR(191) NULL,
  `type_piece_identite` VARCHAR(191) NOT NULL,
  `lieu_delivrance_piece` VARCHAR(191) NOT NULL,
  `date_delivrance_piece` DATE NOT NULL,
  `numero_piece_identite` VARCHAR(191) NOT NULL,
  `photo` VARCHAR(191) NULL,
  `secteur_activite` VARCHAR(191) NULL,
  `type_activite` VARCHAR(191) NULL,
  `nom_entreprise` VARCHAR(191) NULL,
  `adresse_entreprise` VARCHAR(191) NULL,
  `telephone_entreprise` VARCHAR(191) NULL,
  `statut_entreprise` VARCHAR(191) NULL,
  `nombre_annees_experience` VARCHAR(191) NULL,
  `revenu_mensuel` DECIMAL(15,2) NULL,
  `revenu_mensuel_devise` VARCHAR(10) NULL,
  `autres_details_activite` VARCHAR(191) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `tb_clients_matricule_unique` (`matricule`),
  KEY `tb_zones_ibfk_11` (`code_zone`),
  CONSTRAINT `tb_zones_ibfk_11` FOREIGN KEY (`code_zone`) REFERENCES `tb_zones`(`code_zone`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_comptes` (
  `code_compte` VARCHAR(64) NOT NULL,
  `client_matricule` VARCHAR(191) NOT NULL,
  `devise` VARCHAR(3) NOT NULL,
  `type` ENUM('COURANT','EPARGNE_LIBRE','EPARGNE_BLOQUEE','CAUTION_CREDIT') NOT NULL,
  `portefeuille_id` BIGINT UNSIGNED NULL,
  `solde_reel` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `solde_bloque` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`code_compte`),
  KEY `fk_compte_devise` (`devise`),
  KEY `fk_compte_portefeuille` (`portefeuille_id`),
  KEY `tb_comptes_ibfk_112` (`client_matricule`),
  CONSTRAINT `fk_compte_devise` FOREIGN KEY (`devise`) REFERENCES `tb_devises`(`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_compte_portefeuille` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tb_comptes_ibfk_112` FOREIGN KEY (`client_matricule`) REFERENCES `tb_clients`(`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_transactions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `compte_code` VARCHAR(64) NOT NULL,
  `agent_matricule` VARCHAR(50) NOT NULL,
  `type` ENUM('DEPOT','RETRAIT','VIREMENT','REMBOURSEMENT') NOT NULL,
  `montant` DECIMAL(18,2) NOT NULL,
  `reference` VARCHAR(50) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tb_transactions_reference_unique` (`reference`),
  KEY `tb_transactions_ibfk_1` (`compte_code`),
  KEY `tb_transactions_ibfk_2` (`agent_matricule`),
  CONSTRAINT `tb_transactions_ibfk_1` FOREIGN KEY (`compte_code`) REFERENCES `tb_comptes`(`code_compte`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tb_transactions_ibfk_2` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents`(`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `tb_services` (`id`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 (1,'Direction Générale','Administration centrale du système',@now,@now),
 (2,'Caisse','Gestion des guichets et opérations caisse',@now,@now),
 (3,'Ressources Humaines','Gestion du personnel et des affectations',@now,@now);

INSERT IGNORE INTO `tb_postes` (`id`,`service_id`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 (1,1,'Administrateur Système','Poste réservé au compte administrateur',@now,@now),
 (2,2,'Caissier Principal','Gestion du guichet principal',@now,@now),
 (3,3,'Responsable RH','Gestion des agents et des affectations',@now,@now);

INSERT IGNORE INTO `tb_agents` (`matricule`,`nom`,`postnom`,`prenom`,`sexe`,`date_naissance`,`telephone`,`email`,`adresse`,`photo`,`date_embauche`,`statut`,`created_at`,`updated_at`) VALUES
 ('AG-EBENKGA-26-00001','BMB','ADMIN','Système','M',NULL,NULL,'bmb@bmb.cd',NULL,NULL,CURDATE(),'actif',@now,@now),
 ('AG-EBENKGA-26-00002','MULUMBA',NULL,'Jean','M','1990-06-15','+243810000002','jean.caissier@bmb.cd','Kinshasa',NULL,CURDATE(),'actif',@now,@now),
 ('AG-EBENKGA-26-00003','KASONGO',NULL,'Marie','F','1992-03-20','+243810000003','marie.rh@bmb.cd','Kinshasa',NULL,CURDATE(),'actif',@now,@now);

INSERT IGNORE INTO `tb_devises` (`code_iso`,`nom`,`symbole`,`est_reference`,`created_at`,`updated_at`) VALUES
 ('CDF','Franc Congolais','Fc',1,@now,NULL),
 ('USD','Dollar Américain','$',0,@now,NULL),
 ('EUR','Euro','€',0,@now,NULL);

INSERT IGNORE INTO `users` (`agent_matricule`,`name`,`email`,`email_verified_at`,`password`,`remember_token`,`etat`,`created_at`,`updated_at`) VALUES
 ('AG-EBENKGA-26-00001','bmb','bmb@bmb.cd',@now,'$2y$12$h2eVRVSTyES1nDSAzc91q.PGyeA8TvOdjWlEz5WXEo8OGop39HuTW',NULL,'actif',@now,@now),
 ('AG-EBENKGA-26-00002','jean_caissier','jean.caissier@bmb.cd',@now,'$2y$12$o/m3X.G8ImNrB8WgzrankOb5R8trQnBbSwK4vVzYXa7WHjy6AIIfG',NULL,'actif',@now,@now),
 ('AG-EBENKGA-26-00003','marie_rh','marie.rh@bmb.cd',@now,'$2y$12$j8T6Zy8w4q.zzZ1eNt/eeuak5l4H/PdUWwz7rmUNqNKJL7UQ7PR4m',NULL,'actif',@now,@now);

INSERT IGNORE INTO `tb_affectations` (`agent_matricule`,`poste_id`,`date_debut`,`date_fin`,`Etat`,`created_at`,`updated_at`) VALUES
 ('AG-EBENKGA-26-00001',1,CURDATE(),NULL,'ACTIF',@now,@now),
 ('AG-EBENKGA-26-00002',2,CURDATE(),NULL,'ACTIF',@now,@now),
 ('AG-EBENKGA-26-00003',3,CURDATE(),NULL,'ACTIF',@now,@now);

INSERT IGNORE INTO `tb_role_user` (`user_id`,`role_code`,`created_at`,`updated_at`)
SELECT `id`, CASE `email` WHEN 'bmb@bmb.cd' THEN 'EBEN-ROL1' WHEN 'jean.caissier@bmb.cd' THEN 'EBEN-ROL2' WHEN 'marie.rh@bmb.cd' THEN 'EBEN-ROL4' END, @now, @now
FROM `users` WHERE `email` IN ('bmb@bmb.cd','jean.caissier@bmb.cd','marie.rh@bmb.cd');

-- ===================== 2026_03_05_000003_caisse_guichet =====================
CREATE TABLE IF NOT EXISTS `tb_plan_comptable` (
  `numero_compte` VARCHAR(20) NOT NULL,
  `libelle` VARCHAR(191) NOT NULL,
  `type_compte` ENUM('ACTIF','PASSIF','CHARGE','PRODUIT') NOT NULL,
  PRIMARY KEY (`numero_compte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_caisses_guichets` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code_guichet` VARCHAR(20) NOT NULL,
  `intitule` VARCHAR(100) NOT NULL,
  `statut_operationnel` ENUM('OUVERT','FERME','SUSPENDU') NOT NULL DEFAULT 'FERME',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tb_caisses_guichets_code_guichet_unique` (`code_guichet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_caisses_guichets_soldes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `guichet_id` BIGINT UNSIGNED NOT NULL,
  `devise_code` VARCHAR(3) NOT NULL,
  `solde_en_caisse` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_guichet_devise` (`guichet_id`,`devise_code`),
  KEY `fk_solde_devise` (`devise_code`),
  CONSTRAINT `fk_solde_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_solde_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises`(`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_mouvements_inter_caisses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `guichet_source_id` BIGINT UNSIGNED NULL,
  `guichet_dest_id` BIGINT UNSIGNED NULL,
  `agent_initiateur` VARCHAR(50) NOT NULL,
  `type_flux` ENUM('ALIMENTATION','DEGAGEMENT','TRANSFERT') NOT NULL,
  `montant` DECIMAL(18,2) NOT NULL,
  `devise_code` VARCHAR(3) NOT NULL,
  `reference_bordereau` VARCHAR(50) NULL,
  `date_mouvement` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_reference_bordereau` (`reference_bordereau`),
  KEY `fk_mouv_guichet_src` (`guichet_source_id`),
  KEY `fk_mouv_guichet_dest` (`guichet_dest_id`),
  KEY `fk_mouv_agent` (`agent_initiateur`),
  KEY `fk_mouv_devise` (`devise_code`),
  CONSTRAINT `fk_mouv_guichet_src` FOREIGN KEY (`guichet_source_id`) REFERENCES `tb_caisses_guichets`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_mouv_guichet_dest` FOREIGN KEY (`guichet_dest_id`) REFERENCES `tb_caisses_guichets`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_mouv_agent` FOREIGN KEY (`agent_initiateur`) REFERENCES `tb_agents`(`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_mouv_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises`(`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_cloture_caisse` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `guichet_id` BIGINT UNSIGNED NOT NULL,
  `devise_code` VARCHAR(3) NOT NULL,
  `solde_comptable` DECIMAL(18,2) NOT NULL,
  `solde_physique` DECIMAL(18,2) NOT NULL,
  `ecart_caisse` DECIMAL(18,2) NOT NULL,
  `detail_billetage` JSON NULL,
  `agent_cloturant` VARCHAR(50) NOT NULL,
  `date_cloture` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_cloture_guichet` (`guichet_id`),
  KEY `fk_cloture_devise` (`devise_code`),
  KEY `fk_cloture_agent` (`agent_cloturant`),
  CONSTRAINT `fk_cloture_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cloture_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises`(`code_iso`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cloture_agent` FOREIGN KEY (`agent_cloturant`) REFERENCES `tb_agents`(`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `tb_plan_comptable` (`numero_compte`,`libelle`,`type_compte`) VALUES
 ('5701','Caisse CDF','ACTIF'),('5702','Caisse USD','ACTIF'),('5703','Caisse EUR','ACTIF'),
 ('2511','Dépôts à vue clients','PASSIF'),('2512','Dépôts à terme clients','PASSIF'),
 ('7001','Intérêts et produits assimilés','PRODUIT'),('6001','Frais bancaires','CHARGE'),
 ('1011','Capital social','PASSIF');

INSERT IGNORE INTO `tb_caisses_guichets` (`code_guichet`,`intitule`,`statut_operationnel`,`created_at`) VALUES
 ('G01','Guichet Principal CDF/USD','FERME',@now),
 ('G02','Guichet Secondaire CDF','FERME',@now);

-- ===================== 2026_03_05_000004_add_guichet_to_affectations =====================
ALTER TABLE `tb_affectations`
  ADD COLUMN `guichet_id` BIGINT UNSIGNED NULL COMMENT 'Guichet de caisse affecté (optionnel). NULL = agent hors caisse.' AFTER `poste_id`,
  ADD CONSTRAINT `fk_affectation_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- ===================== 2026_03_05_000005_seed_test_users =====================
-- (Liens role/permission appliques apres la migration 6 qui cree les permissions.)
INSERT IGNORE INTO `tb_agents` (`matricule`,`nom`,`postnom`,`prenom`,`sexe`,`date_naissance`,`telephone`,`email`,`adresse`,`photo`,`date_embauche`,`statut`,`created_at`,`updated_at`) VALUES
 ('AG-EBENKGA-26-00002','MULUMBA',NULL,'Jean','M','1990-06-15','+243810000002','jean.caissier@bmb.cd','Kinshasa',NULL,CURDATE(),'actif',@now,@now),
 ('AG-EBENKGA-26-00003','KASONGO',NULL,'Marie','F','1992-03-20','+243810000003','marie.rh@bmb.cd','Kinshasa',NULL,CURDATE(),'actif',@now,@now);

INSERT IGNORE INTO `users` (`agent_matricule`,`name`,`email`,`email_verified_at`,`password`,`remember_token`,`etat`,`created_at`,`updated_at`) VALUES
 ('AG-EBENKGA-26-00002','jean_caissier','jean.caissier@bmb.cd',@now,'$2y$12$o/m3X.G8ImNrB8WgzrankOb5R8trQnBbSwK4vVzYXa7WHjy6AIIfG',NULL,'actif',@now,@now),
 ('AG-EBENKGA-26-00003','marie_rh','marie.rh@bmb.cd',@now,'$2y$12$j8T6Zy8w4q.zzZ1eNt/eeuak5l4H/PdUWwz7rmUNqNKJL7UQ7PR4m',NULL,'actif',@now,@now);

INSERT IGNORE INTO `tb_affectations` (`agent_matricule`,`poste_id`,`guichet_id`,`date_debut`,`date_fin`,`Etat`,`created_at`,`updated_at`) VALUES
 ('AG-EBENKGA-26-00002',2,NULL,CURDATE(),NULL,'ACTIF',@now,@now),
 ('AG-EBENKGA-26-00003',3,NULL,CURDATE(),NULL,'ACTIF',@now,@now);

INSERT IGNORE INTO `tb_role_user` (`user_id`,`role_code`,`created_at`,`updated_at`)
SELECT `id`, CASE `email` WHEN 'jean.caissier@bmb.cd' THEN 'EBEN-ROL2' WHEN 'marie.rh@bmb.cd' THEN 'EBEN-ROL4' END, @now, @now
FROM `users` WHERE `email` IN ('jean.caissier@bmb.cd','marie.rh@bmb.cd');

-- ===================== 2026_03_06_000006_permissions_banque_complete =====================
INSERT IGNORE INTO `tb_roles` (`code`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL1','Administrateur','Accès total au système',@now,@now),
 ('EBEN-ROL2','Caissier','Gestion caisse, guichet et transactions',@now,@now),
 ('EBEN-ROL3','Directeur','Supervision générale',@now,@now),
 ('EBEN-ROL4','Agent RH','Gestion des ressources humaines',@now,@now),
 ('EBEN-ROL5','Superviseur','Supervision opérationnelle',@now,@now),
 ('EBEN-ROL6','Chargé de crédit','Gestion complète des dossiers crédit, épargne et comptes clients',@now,@now),
 ('EBEN-ROL7','Comptable','Comptabilité, rapports financiers, validation des écritures',@now,@now);

INSERT IGNORE INTO `tb_permissions` (`code`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 ('EBEN-PER1','Accès Administration','Accès au panneau d''administration',@now,@now),
 ('EBEN-PER2','Voir les rôles','Consultation des rôles',@now,@now),
 ('EBEN-PER3','Gérer les rôles','Création et modification des rôles',@now,@now),
 ('EBEN-PER4','Voir les permissions','Consultation des permissions',@now,@now),
 ('EBEN-PER5','Gérer les permissions','Gestion des permissions',@now,@now),
 ('EBEN-PER6','Voir RH','Accès au module RH',@now,@now),
 ('EBEN-PER7','Créer agent','Création d''un nouvel agent',@now,@now),
 ('EBEN-PER8','Modifier agent','Modification d''un agent',@now,@now),
 ('EBEN-PER9','Affectations','Gestion des affectations',@now,@now),
 ('EBEN-PER10','Voir caisse','Consultation des caisses',@now,@now),
 ('EBEN-PER11','Ouvrir caisse','Ouverture d''une caisse/guichet',@now,@now),
 ('EBEN-PER12','Fermer caisse','Fermeture d''une caisse/guichet',@now,@now),
 ('EBEN-PER13','Mouvements caisse','Enregistrement des mouvements',@now,@now),
 ('EBEN-PER14','Clôture caisse','Clôture journalière caisse',@now,@now),
 ('EBEN-PER15','Voir clients','Consultation des clients',@now,@now),
 ('EBEN-PER16','Créer client','Enregistrement d''un client',@now,@now),
 ('EBEN-PER17','Modifier client','Modification d''un client',@now,@now),
 ('EBEN-PER18','Voir comptes','Consultation des comptes',@now,@now),
 ('EBEN-PER19','Créer compte','Ouverture d''un compte',@now,@now),
 ('EBEN-PER20','Voir devises','Consultation des devises',@now,@now),
 ('EBEN-PER21','Gérer devises','Gestion des devises et taux',@now,@now),
 ('EBEN-PER22','Effectuer dépôts','Saisir un dépôt sur un compte client',@now,@now),
 ('EBEN-PER23','Effectuer retraits','Saisir un retrait sur un compte client',@now,@now),
 ('EBEN-PER24','Effectuer virements','Initier un virement entre comptes',@now,@now),
 ('EBEN-PER25','Annuler transactions','Annuler ou reverser une opération bancaire',@now,@now),
 ('EBEN-PER26','Valider transactions','Approuver les opérations en attente (double validation)',@now,@now),
 ('EBEN-PER27','Voir produits épargne','Consulter les produits d''épargne disponibles',@now,@now),
 ('EBEN-PER28','Gérer produits épargne','Créer et modifier les produits d''épargne',@now,@now),
 ('EBEN-PER29','Gérer comptes épargne','Ouvrir, alimenter et clôturer des comptes épargne',@now,@now),
 ('EBEN-PER35','Clôturer crédit','Marquer un crédit comme soldé ou en contentieux',@now,@now),
 ('EBEN-PER36','Voir rapports opérationnels','Rapports journaliers caisse et transactions',@now,@now),
 ('EBEN-PER37','Voir rapports financiers','Bilan, compte de résultat, situation financière',@now,@now),
 ('EBEN-PER38','Exporter rapports','Exporter ou imprimer tous les rapports en PDF/Excel',@now,@now),
 ('EBEN-PER39','Voir journal comptable','Consulter les écritures du journal comptable',@now,@now),
 ('EBEN-PER40','Saisir écritures','Créer des écritures comptables manuelles',@now,@now),
 ('EBEN-PER41','Valider écritures','Approuver et lettrer les écritures comptables',@now,@now),
 ('EBEN-PER42','Voir journal d''activité','Logs d''audit : qui a fait quoi et quand',@now,@now),
 ('EBEN-PER43','Gérer paramètres sécurité','Politique mots de passe, tentatives login, blocages',@now,@now);

INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`)
SELECT 'EBEN-ROL1', `code`, @now, @now FROM `tb_permissions`
WHERE `code` IN ('EBEN-PER1','EBEN-PER2','EBEN-PER3','EBEN-PER4','EBEN-PER5','EBEN-PER6','EBEN-PER7','EBEN-PER8','EBEN-PER9','EBEN-PER10',
 'EBEN-PER11','EBEN-PER12','EBEN-PER13','EBEN-PER14','EBEN-PER15','EBEN-PER16','EBEN-PER17','EBEN-PER18','EBEN-PER19','EBEN-PER20',
 'EBEN-PER21','EBEN-PER22','EBEN-PER23','EBEN-PER24','EBEN-PER25','EBEN-PER26','EBEN-PER27','EBEN-PER28','EBEN-PER29','EBEN-PER35',
 'EBEN-PER36','EBEN-PER37','EBEN-PER38','EBEN-PER39','EBEN-PER40','EBEN-PER41','EBEN-PER42','EBEN-PER43');

INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL2','EBEN-PER10',@now,@now),('EBEN-ROL2','EBEN-PER11',@now,@now),('EBEN-ROL2','EBEN-PER12',@now,@now),('EBEN-ROL2','EBEN-PER13',@now,@now),
 ('EBEN-ROL2','EBEN-PER14',@now,@now),('EBEN-ROL2','EBEN-PER15',@now,@now),('EBEN-ROL2','EBEN-PER18',@now,@now),('EBEN-ROL2','EBEN-PER22',@now,@now),
 ('EBEN-ROL2','EBEN-PER23',@now,@now),('EBEN-ROL2','EBEN-PER24',@now,@now),('EBEN-ROL2','EBEN-PER29',@now,@now),
 ('EBEN-ROL3','EBEN-PER1',@now,@now),('EBEN-ROL3','EBEN-PER2',@now,@now),('EBEN-ROL3','EBEN-PER4',@now,@now),('EBEN-ROL3','EBEN-PER6',@now,@now),
 ('EBEN-ROL3','EBEN-PER10',@now,@now),('EBEN-ROL3','EBEN-PER15',@now,@now),('EBEN-ROL3','EBEN-PER18',@now,@now),('EBEN-ROL3','EBEN-PER20',@now,@now),
 ('EBEN-ROL3','EBEN-PER26',@now,@now),('EBEN-ROL3','EBEN-PER27',@now,@now),('EBEN-ROL3','EBEN-PER36',@now,@now),('EBEN-ROL3','EBEN-PER37',@now,@now),
 ('EBEN-ROL3','EBEN-PER38',@now,@now),('EBEN-ROL3','EBEN-PER39',@now,@now),('EBEN-ROL3','EBEN-PER42',@now,@now),
 ('EBEN-ROL4','EBEN-PER6',@now,@now),('EBEN-ROL4','EBEN-PER7',@now,@now),('EBEN-ROL4','EBEN-PER8',@now,@now),('EBEN-ROL4','EBEN-PER9',@now,@now),
 ('EBEN-ROL5','EBEN-PER2',@now,@now),('EBEN-ROL5','EBEN-PER6',@now,@now),('EBEN-ROL5','EBEN-PER10',@now,@now),('EBEN-ROL5','EBEN-PER15',@now,@now),
 ('EBEN-ROL5','EBEN-PER18',@now,@now),('EBEN-ROL5','EBEN-PER20',@now,@now),('EBEN-ROL5','EBEN-PER26',@now,@now),('EBEN-ROL5','EBEN-PER27',@now,@now),
 ('EBEN-ROL5','EBEN-PER36',@now,@now),('EBEN-ROL5','EBEN-PER37',@now,@now),('EBEN-ROL5','EBEN-PER38',@now,@now),('EBEN-ROL5','EBEN-PER42',@now,@now),
 ('EBEN-ROL6','EBEN-PER15',@now,@now),('EBEN-ROL6','EBEN-PER16',@now,@now),('EBEN-ROL6','EBEN-PER18',@now,@now),('EBEN-ROL6','EBEN-PER19',@now,@now),
 ('EBEN-ROL6','EBEN-PER27',@now,@now),('EBEN-ROL6','EBEN-PER28',@now,@now),('EBEN-ROL6','EBEN-PER29',@now,@now),('EBEN-ROL6','EBEN-PER35',@now,@now),
 ('EBEN-ROL7','EBEN-PER18',@now,@now),('EBEN-ROL7','EBEN-PER37',@now,@now),('EBEN-ROL7','EBEN-PER38',@now,@now),('EBEN-ROL7','EBEN-PER39',@now,@now),
 ('EBEN-ROL7','EBEN-PER40',@now,@now),('EBEN-ROL7','EBEN-PER41',@now,@now);

-- Liens role/permission differes de la migration 5 (000005)
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL2','EBEN-PER10',@now,@now),('EBEN-ROL2','EBEN-PER11',@now,@now),('EBEN-ROL2','EBEN-PER12',@now,@now),('EBEN-ROL2','EBEN-PER13',@now,@now),
 ('EBEN-ROL2','EBEN-PER14',@now,@now),('EBEN-ROL2','EBEN-PER15',@now,@now),('EBEN-ROL2','EBEN-PER18',@now,@now),
 ('EBEN-ROL3','EBEN-PER1',@now,@now),('EBEN-ROL3','EBEN-PER2',@now,@now),('EBEN-ROL3','EBEN-PER4',@now,@now),('EBEN-ROL3','EBEN-PER6',@now,@now),
 ('EBEN-ROL3','EBEN-PER10',@now,@now),('EBEN-ROL3','EBEN-PER15',@now,@now),('EBEN-ROL3','EBEN-PER18',@now,@now),('EBEN-ROL3','EBEN-PER20',@now,@now),
 ('EBEN-ROL4','EBEN-PER6',@now,@now),('EBEN-ROL4','EBEN-PER7',@now,@now),('EBEN-ROL4','EBEN-PER8',@now,@now),('EBEN-ROL4','EBEN-PER9',@now,@now),
 ('EBEN-ROL5','EBEN-PER2',@now,@now),('EBEN-ROL5','EBEN-PER6',@now,@now),('EBEN-ROL5','EBEN-PER10',@now,@now),
 ('EBEN-ROL5','EBEN-PER15',@now,@now),('EBEN-ROL5','EBEN-PER18',@now,@now),('EBEN-ROL5','EBEN-PER20',@now,@now);

-- ===================== 2026_03_07_103903_add_statut_observations_to_mouvements_inter_caisses =====================
ALTER TABLE `tb_mouvements_inter_caisses`
  ADD COLUMN `statut` ENUM('EN_ATTENTE','VALIDE','CONFIRME','ANNULE') NOT NULL DEFAULT 'CONFIRME' AFTER `date_mouvement`,
  ADD COLUMN `validateur_matricule` VARCHAR(50) NULL AFTER `statut`,
  ADD COLUMN `observations` VARCHAR(255) NULL AFTER `validateur_matricule`,
  ADD CONSTRAINT `tb_mouvements_inter_caisses_ibfk_1` FOREIGN KEY (`validateur_matricule`) REFERENCES `tb_agents`(`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- ===================== 2026_03_07_105258_add_type_guichet_and_coffre_central =====================
ALTER TABLE `tb_caisses_guichets`
  ADD COLUMN `type_guichet` ENUM('FIXE','MOBILE','CENTRAL') NOT NULL DEFAULT 'FIXE' AFTER `code_guichet`;

INSERT IGNORE INTO `tb_caisses_guichets` (`code_guichet`,`type_guichet`,`intitule`,`statut_operationnel`,`created_at`) VALUES
 ('COFFRE_01','CENTRAL','Coffre-Fort Central EBEN','OUVERT',@now);

INSERT IGNORE INTO `tb_caisses_guichets_soldes` (`guichet_id`,`devise_code`,`solde_en_caisse`,`updated_at`)
SELECT g.`id`, d.`code_iso`, 0.00, @now
FROM `tb_caisses_guichets` g CROSS JOIN `tb_devises` d
WHERE g.`code_guichet` = 'COFFRE_01';

-- ===================== 2026_03_07_145751_add_timestamps_to_tb_caisses_guichets =====================
ALTER TABLE `tb_caisses_guichets`
  ADD COLUMN `updated_at` TIMESTAMP NULL AFTER `created_at`;
UPDATE `tb_caisses_guichets` SET `updated_at` = `created_at` WHERE `updated_at` IS NULL;

-- ===================== 2026_03_07_210000_tresorerie_permissions_and_user =====================
INSERT IGNORE INTO `tb_permissions` (`code`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 ('EBEN-PER44','Voir trésorerie','Accès au module trésorerie/coffre-fort (vue d''ensemble et soldes)',@now,@now),
 ('EBEN-PER45','Approvisionner coffre','Enregistrer un approvisionnement externe (banque → coffre)',@now,@now),
 ('EBEN-PER46','Valider mouvements trésorerie','Approuver / rejeter les opérations coffre-fort en attente',@now,@now),
 ('EBEN-PER47','Alimenter guichets','Transférer des fonds entre le coffre central et les guichets',@now,@now),
 ('EBEN-PER48','Journal trésorerie','Consulter le journal complet de la caisse centrale (historique)',@now,@now);

INSERT IGNORE INTO `tb_roles` (`code`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL8','Trésorier','Gestion complète du coffre-fort central, approvisionnements et transferts',@now,@now);

INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL1','EBEN-PER44',@now,@now),('EBEN-ROL1','EBEN-PER45',@now,@now),('EBEN-ROL1','EBEN-PER46',@now,@now),('EBEN-ROL1','EBEN-PER47',@now,@now),('EBEN-ROL1','EBEN-PER48',@now,@now),
 ('EBEN-ROL3','EBEN-PER44',@now,@now),('EBEN-ROL3','EBEN-PER48',@now,@now),
 ('EBEN-ROL5','EBEN-PER44',@now,@now),('EBEN-ROL5','EBEN-PER48',@now,@now),
 ('EBEN-ROL8','EBEN-PER44',@now,@now),('EBEN-ROL8','EBEN-PER45',@now,@now),('EBEN-ROL8','EBEN-PER46',@now,@now),('EBEN-ROL8','EBEN-PER47',@now,@now),('EBEN-ROL8','EBEN-PER48',@now,@now),
 ('EBEN-ROL8','EBEN-PER10',@now,@now),('EBEN-ROL8','EBEN-PER20',@now,@now),('EBEN-ROL8','EBEN-PER36',@now,@now),('EBEN-ROL8','EBEN-PER37',@now,@now),('EBEN-ROL8','EBEN-PER38',@now,@now);

INSERT IGNORE INTO `tb_agents` (`matricule`,`nom`,`postnom`,`prenom`,`sexe`,`date_naissance`,`telephone`,`email`,`adresse`,`photo`,`date_embauche`,`statut`,`created_at`,`updated_at`) VALUES
 ('AG-EBENKGA-26-00004','ILUNGA',NULL,'Prosper','M','1985-09-10','+243810000004','tresorier@bmb.cd','Kinshasa',NULL,CURDATE(),'actif',@now,@now);

INSERT IGNORE INTO `tb_services` (`id`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 (3,'Trésorerie & Finance','Gestion du coffre-fort central, flux monétaires et reporting financier',@now,@now);

INSERT IGNORE INTO `tb_postes` (`id`,`service_id`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 (4,3,'Chef Trésorier','Responsable du coffre-fort central et des flux inter-caisses',@now,@now);

-- Mot de passe bcrypt genere pour 'Tresorier@2026'
INSERT IGNORE INTO `users` (`agent_matricule`,`name`,`email`,`email_verified_at`,`password`,`remember_token`,`etat`,`created_at`,`updated_at`) VALUES
 ('AG-EBENKGA-26-00004','tresorier','tresorier@bmb.cd',@now,'$2y$10$3NJ/Q3z//Yp9FWbtyoR70.r/hZlHV3H5oF3mZH746KHrqujnKHCK6',NULL,'actif',@now,@now);

INSERT IGNORE INTO `tb_role_user` (`user_id`,`role_code`,`created_at`,`updated_at`)
SELECT `id`,'EBEN-ROL8',@now,@now FROM `users` WHERE `email`='tresorier@bmb.cd';

INSERT IGNORE INTO `tb_affectations` (`agent_matricule`,`poste_id`,`guichet_id`,`date_debut`,`date_fin`,`Etat`,`created_at`,`updated_at`) VALUES
 ('AG-EBENKGA-26-00004',4,NULL,CURDATE(),NULL,'ACTIF',@now,@now);

-- ===================== 2026_03_07_220001_add_demande_appro_to_type_flux =====================
ALTER TABLE `tb_mouvements_inter_caisses`
  MODIFY COLUMN `type_flux` ENUM('ALIMENTATION','DEGAGEMENT','TRANSFERT','DEMANDE_APPRO') NOT NULL;

-- ===================== 2026_03_07_235000_add_motif_statut_to_cloture_caisse =====================
ALTER TABLE `tb_cloture_caisse`
  ADD COLUMN `motif_ecart` TEXT NULL COMMENT 'Justification requise si écart différent de 0' AFTER `detail_billetage`,
  ADD COLUMN `statut_ecart` ENUM('EQUILIBRE','EXCEDENT','DEFICIT') NOT NULL DEFAULT 'EQUILIBRE' COMMENT 'Résultat de la confrontation physique / système' AFTER `motif_ecart`;

-- ===================== 2026_03_08_000001_add_en_verification_and_cloture_validation =====================
ALTER TABLE `tb_caisses_guichets`
  MODIFY COLUMN `statut_operationnel` ENUM('OUVERT','FERME','SUSPENDU','EN_VERIFICATION') NOT NULL DEFAULT 'FERME';

ALTER TABLE `tb_cloture_caisse`
  ADD COLUMN `statut_validation` ENUM('EN_ATTENTE','VALIDE','REJETE') NOT NULL DEFAULT 'EN_ATTENTE' COMMENT 'Statut de validation par le superviseur' AFTER `statut_ecart`,
  ADD COLUMN `validateur_matricule` VARCHAR(20) NULL COMMENT 'Matricule du superviseur ayant validé' AFTER `statut_validation`,
  ADD COLUMN `date_validation` TIMESTAMP NULL COMMENT 'Date/heure de validation par le superviseur' AFTER `validateur_matricule`,
  ADD COLUMN `observations_superviseur` TEXT NULL COMMENT 'Commentaire du superviseur lors de la validation' AFTER `date_validation`,
  ADD CONSTRAINT `tb_cloture_caisse_ibfk_1` FOREIGN KEY (`validateur_matricule`) REFERENCES `tb_agents`(`matricule`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- ===================== 2026_03_08_110000_extend_tb_transactions_for_guichet =====================
ALTER TABLE `tb_transactions` MODIFY COLUMN `compte_code` VARCHAR(64) NULL;

ALTER TABLE `tb_transactions`
  ADD COLUMN `guichet_id` BIGINT UNSIGNED NULL COMMENT 'Guichet ayant effectué l''opération' AFTER `agent_matricule`,
  ADD COLUMN `devise_code` CHAR(3) NULL COMMENT 'Devise de la transaction (CDF, USD, EUR)' AFTER `guichet_id`,
  ADD COLUMN `client_nom` VARCHAR(150) NULL AFTER `devise_code`,
  ADD COLUMN `client_ref` VARCHAR(50) NULL COMMENT 'Réf. externe, passeport, ID' AFTER `client_nom`,
  ADD COLUMN `devise_dest` CHAR(3) NULL AFTER `client_ref`,
  ADD COLUMN `montant_dest` DECIMAL(18,2) NULL AFTER `devise_dest`,
  ADD COLUMN `taux_change` DECIMAL(14,6) NULL AFTER `montant_dest`,
  ADD COLUMN `observations` TEXT NULL AFTER `taux_change`,
  ADD COLUMN `statut` ENUM('CONFIRME','ANNULE') NOT NULL DEFAULT 'CONFIRME' AFTER `observations`,
  ADD COLUMN `date_operation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `statut`,
  ADD COLUMN `created_at` TIMESTAMP NULL,
  ADD COLUMN `updated_at` TIMESTAMP NULL,
  ADD CONSTRAINT `tb_transactions_guichet_fk` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets`(`id`) ON DELETE SET NULL,
  ADD KEY `idx_trans_guichet_date` (`guichet_id`,`date_operation`),
  ADD KEY `idx_trans_statut` (`statut`);

ALTER TABLE `tb_transactions`
  MODIFY COLUMN `type` ENUM('DEPOT','RETRAIT','VIREMENT','REMBOURSEMENT','CHANGE','PAIEMENT') NOT NULL;

ALTER TABLE `tb_mouvements_inter_caisses`
  MODIFY COLUMN `type_flux` ENUM('ALIMENTATION','DEGAGEMENT','TRANSFERT','DEMANDE_APPRO','DOTATION_MOBILE','REVERSEMENT_MOBILE') NOT NULL;

-- ===================== 2026_03_08_120000_drop_client_columns_from_tb_transactions =====================
ALTER TABLE `tb_transactions`
  DROP COLUMN `client_nom`,
  DROP COLUMN `client_ref`;

-- ===================== 2026_03_10_000001_update_type_enum_tb_comptes =====================
ALTER TABLE `tb_comptes` MODIFY COLUMN `type`
  ENUM('COURANT','EPARGNE_LIBRE','EPARGNE_BLOQUEE','CAUTION_CREDIT','CC','RMB','GTC','DAT','EAV')
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
  COMMENT 'CC=Compte Courant, RMB=Remboursement, GTC=Caution, DAT=Dépôt à Terme, EAV=Épargne & Vie';

UPDATE `tb_comptes` SET `type`='CC'  WHERE `type`='COURANT';
UPDATE `tb_comptes` SET `type`='EAV' WHERE `type`='EPARGNE_LIBRE';
UPDATE `tb_comptes` SET `type`='DAT' WHERE `type`='EPARGNE_BLOQUEE';
UPDATE `tb_comptes` SET `type`='GTC' WHERE `type`='CAUTION_CREDIT';

ALTER TABLE `tb_comptes` MODIFY COLUMN `type`
  ENUM('CC','RMB','GTC','DAT','EAV')
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
  COMMENT 'CC=Compte Courant, RMB=Remboursement, GTC=Caution, DAT=Dépôt à Terme, EAV=Épargne & Vie';

-- ===================== 2026_03_10_200000_create_tb_demandes_modification =====================
CREATE TABLE IF NOT EXISTS `tb_demandes_modification` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_id` BIGINT UNSIGNED NOT NULL,
  `reference_operation` VARCHAR(60) NULL,
  `guichet_id` BIGINT UNSIGNED NULL,
  `compte_code` VARCHAR(60) NULL,
  `client_nom` VARCHAR(200) NULL,
  `type_operation` VARCHAR(30) NULL,
  `devise_code` VARCHAR(10) NULL,
  `ancien_montant` DECIMAL(15,2) NULL,
  `anciennes_observations` TEXT NULL,
  `type_demande` ENUM('MODIFICATION','SUPPRESSION') NOT NULL,
  `agent_matricule` VARCHAR(60) NULL,
  `motif` TEXT NOT NULL,
  `nouveau_montant` DECIMAL(15,2) NULL,
  `nouvelles_observations` TEXT NULL,
  `statut` ENUM('EN_ATTENTE','APPROUVEE','REJETEE') NOT NULL DEFAULT 'EN_ATTENTE',
  `superviseur_matricule` VARCHAR(60) NULL,
  `commentaire_superviseur` TEXT NULL,
  `traitee_le` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `tb_demandes_modification_statut_created_at_index` (`statut`,`created_at`),
  KEY `tb_demandes_modification_agent_matricule_index` (`agent_matricule`),
  KEY `tb_demandes_modification_superviseur_matricule_index` (`superviseur_matricule`),
  KEY `tb_demandes_modification_guichet_id_foreign` (`guichet_id`),
  CONSTRAINT `tb_demandes_modification_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `tb_transactions`(`id`) ON DELETE CASCADE,
  CONSTRAINT `tb_demandes_modification_guichet_id_foreign` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================== 2026_03_13_210000_create_tb_commission_rules_and_transaction_commissions =====================
CREATE TABLE IF NOT EXISTS `tb_commission_rules` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(150) NOT NULL,
  `code_operation` VARCHAR(50) NOT NULL DEFAULT 'TOUS',
  `type_compte` VARCHAR(20) NOT NULL DEFAULT 'TOUS',
  `type_guichet` VARCHAR(20) NOT NULL DEFAULT 'TOUS',
  `devise_code` CHAR(3) NULL,
  `code_zone` VARCHAR(50) NULL,
  `portefeuille_id` BIGINT UNSIGNED NULL,
  `montant_min` DECIMAL(18,2) NULL,
  `montant_max` DECIMAL(18,2) NULL,
  `mode_calcul` ENUM('FIXE','POURCENTAGE') NOT NULL,
  `valeur` DECIMAL(18,4) NOT NULL,
  `priorite` INT UNSIGNED NOT NULL DEFAULT 100,
  `date_debut` DATE NOT NULL,
  `date_fin` DATE NULL,
  `est_actif` TINYINT(1) NOT NULL DEFAULT 1,
  `observations` TEXT NULL,
  `created_by_agent` VARCHAR(50) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `idx_comm_rules_active_dates` (`est_actif`,`date_debut`,`date_fin`),
  KEY `idx_comm_rules_scope` (`code_operation`,`type_compte`,`type_guichet`),
  KEY `idx_comm_rules_context` (`devise_code`,`code_zone`,`portefeuille_id`),
  CONSTRAINT `tb_comm_rules_devise_fk` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises`(`code_iso`) ON DELETE SET NULL,
  CONSTRAINT `tb_comm_rules_zone_fk` FOREIGN KEY (`code_zone`) REFERENCES `tb_zones`(`code_zone`) ON DELETE SET NULL,
  CONSTRAINT `tb_comm_rules_portefeuille_fk` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `tb_transactions`
  ADD COLUMN `montant_commission_total` DECIMAL(18,2) NOT NULL DEFAULT 0 AFTER `montant`;

CREATE TABLE IF NOT EXISTS `tb_transaction_commissions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_id` BIGINT UNSIGNED NOT NULL,
  `commission_rule_id` BIGINT UNSIGNED NULL,
  `libelle` VARCHAR(150) NOT NULL,
  `code_operation` VARCHAR(50) NOT NULL,
  `type_compte` VARCHAR(20) NULL,
  `type_guichet` VARCHAR(20) NULL,
  `devise_code` CHAR(3) NULL,
  `code_zone` VARCHAR(50) NULL,
  `portefeuille_id` BIGINT UNSIGNED NULL,
  `mode_calcul` ENUM('FIXE','POURCENTAGE') NOT NULL,
  `valeur_snapshot` DECIMAL(18,4) NOT NULL,
  `base_calcul` DECIMAL(18,2) NOT NULL DEFAULT 0,
  `montant_commission` DECIMAL(18,2) NOT NULL DEFAULT 0,
  `date_application` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `agent_matricule` VARCHAR(50) NULL,
  `guichet_id` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `idx_trans_comm_tx_date` (`transaction_id`,`date_application`),
  KEY `idx_trans_comm_scope` (`code_operation`,`type_compte`,`type_guichet`),
  KEY `tb_trans_comm_rule_fk` (`commission_rule_id`),
  KEY `tb_trans_comm_zone_fk` (`code_zone`),
  KEY `tb_trans_comm_portefeuille_fk` (`portefeuille_id`),
  KEY `tb_trans_comm_guichet_fk` (`guichet_id`),
  KEY `tb_trans_comm_agent_fk` (`agent_matricule`),
  CONSTRAINT `tb_trans_comm_tx_fk` FOREIGN KEY (`transaction_id`) REFERENCES `tb_transactions`(`id`) ON DELETE CASCADE,
  CONSTRAINT `tb_trans_comm_rule_fk` FOREIGN KEY (`commission_rule_id`) REFERENCES `tb_commission_rules`(`id`) ON DELETE SET NULL,
  CONSTRAINT `tb_trans_comm_zone_fk` FOREIGN KEY (`code_zone`) REFERENCES `tb_zones`(`code_zone`) ON DELETE SET NULL,
  CONSTRAINT `tb_trans_comm_portefeuille_fk` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents`(`id`) ON DELETE SET NULL,
  CONSTRAINT `tb_trans_comm_guichet_fk` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets`(`id`) ON DELETE SET NULL,
  CONSTRAINT `tb_trans_comm_agent_fk` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents`(`matricule`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================== 2026_03_13_233000_add_accounting_snapshots_to_tb_transactions =====================
ALTER TABLE `tb_transactions`
  ADD COLUMN `solde_compte_avant` DECIMAL(18,2) NULL AFTER `montant_commission_total`,
  ADD COLUMN `solde_compte_apres` DECIMAL(18,2) NULL AFTER `solde_compte_avant`,
  ADD COLUMN `montant_total_client` DECIMAL(18,2) NULL AFTER `solde_compte_apres`,
  ADD COLUMN `montant_net_client` DECIMAL(18,2) NULL AFTER `montant_total_client`;

-- ===================== 2026_03_13_234000_create_tb_compta_journaux_and_ecritures =====================
CREATE TABLE IF NOT EXISTS `tb_compta_journaux` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code_journal` VARCHAR(20) NOT NULL DEFAULT 'CAI',
  `reference_piece` VARCHAR(80) NOT NULL,
  `transaction_id` BIGINT UNSIGNED NULL,
  `type_piece` ENUM('OPERATION','ANNULATION','REGULARISATION') NOT NULL DEFAULT 'OPERATION',
  `devise_code` VARCHAR(3) NULL,
  `libelle` VARCHAR(191) NOT NULL,
  `statut` ENUM('COMPTABILISE','ANNULE') NOT NULL DEFAULT 'COMPTABILISE',
  `agent_matricule` VARCHAR(50) NULL,
  `date_ecriture` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `metadata` JSON NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tb_compta_journaux_reference_piece_unique` (`reference_piece`),
  KEY `idx_compta_journal_trans_type` (`transaction_id`,`type_piece`),
  KEY `idx_compta_journal_date_devise` (`date_ecriture`,`devise_code`),
  KEY `fk_compta_journal_agent` (`agent_matricule`),
  KEY `fk_compta_journal_devise` (`devise_code`),
  CONSTRAINT `fk_compta_journal_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `tb_transactions`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_compta_journal_agent` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents`(`matricule`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_compta_journal_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises`(`code_iso`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_compta_ecritures` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `journal_id` BIGINT UNSIGNED NOT NULL,
  `numero_compte` VARCHAR(20) NOT NULL,
  `devise_code` VARCHAR(3) NULL,
  `libelle_ligne` VARCHAR(191) NULL,
  `debit` DECIMAL(18,2) NOT NULL DEFAULT 0,
  `credit` DECIMAL(18,2) NOT NULL DEFAULT 0,
  `ordre` INT UNSIGNED NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `idx_compta_ecriture_compte_devise` (`numero_compte`,`devise_code`),
  KEY `idx_compta_ecriture_journal` (`journal_id`),
  KEY `fk_compta_ecriture_devise` (`devise_code`),
  CONSTRAINT `fk_compta_ecriture_journal` FOREIGN KEY (`journal_id`) REFERENCES `tb_compta_journaux`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_compta_ecriture_compte` FOREIGN KEY (`numero_compte`) REFERENCES `tb_plan_comptable`(`numero_compte`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_compta_ecriture_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises`(`code_iso`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================== 2026_03_13_235000_seed_ohada_accounts_and_comptabilite_permissions =====================
INSERT IGNORE INTO `tb_plan_comptable` (`numero_compte`,`libelle`,`type_compte`) VALUES
 ('7061','Commissions sur services bancaires','PRODUIT'),
 ('7071','Produits services guichet','PRODUIT'),
 ('4711','Compte transitoire operations de change','PASSIF');

INSERT IGNORE INTO `tb_permissions` (`code`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 ('EBEN-PER49','Voir comptabilite','Acces au module Comptabilite OHADA',@now,@now),
 ('EBEN-PER50','Journal comptable','Consulter le journal des ecritures comptables',@now,@now),
 ('EBEN-PER51','Plan comptable','Consulter le plan comptable OHADA',@now,@now),
 ('EBEN-PER52','Grand livre','Consulter le grand livre comptable',@now,@now);

INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL1','EBEN-PER49',@now,@now),('EBEN-ROL1','EBEN-PER50',@now,@now),('EBEN-ROL1','EBEN-PER51',@now,@now),('EBEN-ROL1','EBEN-PER52',@now,@now),
 ('EBEN-ROL8','EBEN-PER49',@now,@now),('EBEN-ROL8','EBEN-PER50',@now,@now),('EBEN-ROL8','EBEN-PER51',@now,@now),('EBEN-ROL8','EBEN-PER52',@now,@now),
 ('EBEN-ROL3','EBEN-PER49',@now,@now),('EBEN-ROL3','EBEN-PER50',@now,@now),('EBEN-ROL3','EBEN-PER52',@now,@now),
 ('EBEN-ROL5','EBEN-PER49',@now,@now),('EBEN-ROL5','EBEN-PER50',@now,@now),('EBEN-ROL5','EBEN-PER52',@now,@now);

-- ===================== 2026_03_13_238000_extend_tb_plan_comptable_for_ohada_structure =====================
ALTER TABLE `tb_plan_comptable`
  MODIFY COLUMN `type_compte` ENUM('ACTIF','PASSIF','CHARGE','PRODUIT','MIXTE','HORS_BILAN') NOT NULL;

ALTER TABLE `tb_plan_comptable`
  ADD COLUMN `classe_ohada` CHAR(1) NULL AFTER `numero_compte`,
  ADD COLUMN `parent_compte` VARCHAR(20) NULL AFTER `libelle`,
  ADD COLUMN `niveau` TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER `parent_compte`,
  ADD COLUMN `est_mouvementable` TINYINT(1) NOT NULL DEFAULT 1 AFTER `type_compte`,
  ADD COLUMN `est_actif` TINYINT(1) NOT NULL DEFAULT 1 AFTER `est_mouvementable`;

UPDATE `tb_plan_comptable` SET `classe_ohada` = LEFT(`numero_compte`,1) WHERE `classe_ohada` IS NULL OR `classe_ohada`='';
UPDATE `tb_plan_comptable` SET `niveau` = CHAR_LENGTH(`numero_compte`) WHERE `niveau` IS NULL OR `niveau`=0;
UPDATE `tb_plan_comptable` SET `est_mouvementable` = 0 WHERE CHAR_LENGTH(`numero_compte`) <= 2;

ALTER TABLE `tb_plan_comptable`
  ADD KEY `idx_plan_ohada_classe_num` (`classe_ohada`,`numero_compte`),
  ADD KEY `idx_plan_ohada_parent` (`parent_compte`),
  ADD KEY `idx_plan_ohada_actif` (`est_actif`);

-- ===================== 2026_03_13_239000_seed_full_ohada_chart_accounts =====================
INSERT INTO `tb_plan_comptable` (`numero_compte`,`classe_ohada`,`libelle`,`parent_compte`,`niveau`,`type_compte`,`est_mouvementable`,`est_actif`) VALUES
 ('1','1','Comptes de capitaux',NULL,1,'PASSIF',0,1),
 ('10','1','Capital','1',2,'PASSIF',0,1),
 ('101','1','Capital social','10',3,'PASSIF',0,1),
 ('1011','1','Capital souscrit appele verse','101',4,'PASSIF',1,1),
 ('102','1','Capital souscrit non appele','10',3,'PASSIF',1,1),
 ('103','1','Capital souscrit appele non verse','10',3,'PASSIF',1,1),
 ('104','1','Primes liees au capital','10',3,'PASSIF',1,1),
 ('105','1','Ecarts de reevaluation','10',3,'PASSIF',1,1),
 ('106','1','Reserves','10',3,'PASSIF',1,1),
 ('107','1','Report a nouveau','10',3,'PASSIF',1,1),
 ('108','1','Resultat net en instance d affectation','10',3,'PASSIF',1,1),
 ('109','1','Actionnaires capital souscrit non appele','10',3,'PASSIF',1,1),
 ('11','1','Emprunts et dettes assimilees','1',2,'PASSIF',0,1),
 ('12','1','Dettes de location acquisition','1',2,'PASSIF',0,1),
 ('13','1','Provisions pour risques et charges','1',2,'PASSIF',0,1),
 ('14','1','Dettes financieres diverses','1',2,'PASSIF',0,1),
 ('15','1','Dettes rattachees a des participations','1',2,'PASSIF',0,1),
 ('16','1','Fonds affectes et subventions d investissement','1',2,'PASSIF',0,1),
 ('17','1','Autres fonds propres','1',2,'PASSIF',0,1),
 ('18','1','Comptes de liaison des etablissements','1',2,'PASSIF',0,1),
 ('19','1','Provisions financieres pour risques et charges','1',2,'PASSIF',0,1),
 ('2','2','Comptes d immobilisations',NULL,1,'ACTIF',0,1),
 ('20','2','Charges immobilisees','2',2,'ACTIF',0,1),
 ('21','2','Immobilisations incorporelles','2',2,'ACTIF',0,1),
 ('22','2','Terrains','2',2,'ACTIF',0,1),
 ('23','2','Batiments installations techniques et agencements','2',2,'ACTIF',0,1),
 ('24','2','Materiel mobilier et actifs biologiques','2',2,'ACTIF',0,1),
 ('25','2','Avances et acomptes verses sur immobilisations','2',2,'ACTIF',0,1),
 ('251','2','Avances et acomptes sur immobilisations corporelles','25',3,'ACTIF',0,1),
 ('2511','2','Depots a vue clients','251',4,'PASSIF',1,1),
 ('2512','2','Depots a terme clients','251',4,'PASSIF',1,1),
 ('26','2','Titres de participation et autres immobilisations financieres','2',2,'ACTIF',0,1),
 ('27','2','Ecarts de conversion actif','2',2,'ACTIF',0,1),
 ('28','2','Amortissements','2',2,'ACTIF',0,1),
 ('29','2','Depreciations des immobilisations','2',2,'ACTIF',0,1),
 ('3','3','Comptes de stocks',NULL,1,'ACTIF',0,1),
 ('31','3','Marchandises','3',2,'ACTIF',0,1),
 ('32','3','Matieres premieres et fournitures liees','3',2,'ACTIF',0,1),
 ('33','3','Autres approvisionnements','3',2,'ACTIF',0,1),
 ('34','3','Produits en cours','3',2,'ACTIF',0,1),
 ('35','3','Services en cours','3',2,'ACTIF',0,1),
 ('36','3','Produits finis','3',2,'ACTIF',0,1),
 ('37','3','Produits intermediaires et residuels','3',2,'ACTIF',0,1),
 ('38','3','Stocks en cours de route et en consignation','3',2,'ACTIF',0,1),
 ('39','3','Depreciations des stocks','3',2,'ACTIF',0,1),
 ('4','4','Comptes de tiers',NULL,1,'MIXTE',0,1),
 ('40','4','Fournisseurs et comptes rattaches','4',2,'MIXTE',0,1),
 ('41','4','Clients et comptes rattaches','4',2,'MIXTE',0,1),
 ('411','4','Clients ordinaires','41',3,'MIXTE',0,1),
 ('4111','4','Comptes courants clients','411',4,'PASSIF',1,1),
 ('4112','4','Comptes epargne clients','411',4,'PASSIF',1,1),
 ('412','4','Clients effets a recevoir','41',3,'MIXTE',1,1),
 ('42','4','Personnel','4',2,'MIXTE',0,1),
 ('43','4','Organismes sociaux','4',2,'MIXTE',0,1),
 ('44','4','Etat et collectivites publiques','4',2,'MIXTE',0,1),
 ('45','4','Organismes internationaux','4',2,'MIXTE',0,1),
 ('46','4','Associes et groupe','4',2,'MIXTE',0,1),
 ('47','4','Debiteurs et crediteurs divers','4',2,'MIXTE',0,1),
 ('471','4','Comptes d attente','47',3,'MIXTE',0,1),
 ('4711','4','Compte transitoire operations de change','471',4,'PASSIF',1,1),
 ('48','4','Comptes de regularisation','4',2,'MIXTE',0,1),
 ('49','4','Depreciations et provisions des comptes de tiers','4',2,'MIXTE',0,1),
 ('5','5','Comptes de tresorerie',NULL,1,'ACTIF',0,1),
 ('50','5','Titres de placement','5',2,'ACTIF',0,1),
 ('51','5','Valeurs a encaisser','5',2,'ACTIF',0,1),
 ('52','5','Banques etablissements financiers et assimiles','5',2,'ACTIF',0,1),
 ('521','5','Banques locales','52',3,'ACTIF',0,1),
 ('5211','5','Banque locale CDF','521',4,'ACTIF',1,1),
 ('5212','5','Banque locale USD','521',4,'ACTIF',1,1),
 ('53','5','Etablissements financiers et instruments monetaires','5',2,'ACTIF',0,1),
 ('54','5','Instruments de tresorerie','5',2,'ACTIF',0,1),
 ('55','5','Monnaie electronique','5',2,'ACTIF',0,1),
 ('56','5','Banques crediteurs','5',2,'ACTIF',0,1),
 ('57','5','Caisse','5',2,'ACTIF',0,1),
 ('570','5','Caisse principale','57',3,'ACTIF',0,1),
 ('5701','5','Caisse CDF','570',4,'ACTIF',1,1),
 ('5702','5','Caisse USD','570',4,'ACTIF',1,1),
 ('5703','5','Caisse EUR','570',4,'ACTIF',1,1),
 ('58','5','Virements internes','5',2,'ACTIF',0,1),
 ('581','5','Virements internes en cours','58',3,'ACTIF',0,1),
 ('5811','5','Virements internes en cours CDF','581',4,'ACTIF',1,1),
 ('59','5','Depreciations des comptes financiers','5',2,'ACTIF',0,1),
 ('6','6','Comptes de charges des activites ordinaires',NULL,1,'CHARGE',0,1),
 ('60','6','Achats et variation de stocks','6',2,'CHARGE',0,1),
 ('600','6','Achats','60',3,'CHARGE',0,1),
 ('6001','6','Frais bancaires','600',4,'CHARGE',1,1),
 ('61','6','Transports','6',2,'CHARGE',0,1),
 ('62','6','Services exterieurs A','6',2,'CHARGE',0,1),
 ('63','6','Services exterieurs B','6',2,'CHARGE',0,1),
 ('64','6','Impots et taxes','6',2,'CHARGE',0,1),
 ('65','6','Autres charges','6',2,'CHARGE',0,1),
 ('66','6','Charges de personnel','6',2,'CHARGE',0,1),
 ('67','6','Frais financiers et charges assimilees','6',2,'CHARGE',0,1),
 ('68','6','Dotations aux amortissements provisions et depreciations','6',2,'CHARGE',0,1),
 ('69','6','Impots sur resultats','6',2,'CHARGE',0,1),
 ('7','7','Comptes de produits des activites ordinaires',NULL,1,'PRODUIT',0,1),
 ('70','7','Ventes','7',2,'PRODUIT',0,1),
 ('700','7','Produits financiers courants','70',3,'PRODUIT',0,1),
 ('7001','7','Interets et produits assimiles','700',4,'PRODUIT',1,1),
 ('701','7','Ventes de produits finis','70',3,'PRODUIT',0,1),
 ('702','7','Ventes de produits intermediaires','70',3,'PRODUIT',0,1),
 ('703','7','Ventes de produits residuels','70',3,'PRODUIT',0,1),
 ('704','7','Travaux factures','70',3,'PRODUIT',0,1),
 ('705','7','Etudes facturees','70',3,'PRODUIT',0,1),
 ('706','7','Services vendus','70',3,'PRODUIT',0,1),
 ('7061','7','Commissions sur services bancaires','706',4,'PRODUIT',1,1),
 ('707','7','Produits accessoires','70',3,'PRODUIT',0,1),
 ('7071','7','Produits services guichet','707',4,'PRODUIT',1,1),
 ('708','7','Produits divers','70',3,'PRODUIT',0,1),
 ('71','7','Subventions d exploitation','7',2,'PRODUIT',0,1),
 ('72','7','Production immobilisee','7',2,'PRODUIT',0,1),
 ('73','7','Variations des stocks de biens et services produits','7',2,'PRODUIT',0,1),
 ('74','7','Produits divers','7',2,'PRODUIT',0,1),
 ('75','7','Transferts de charges','7',2,'PRODUIT',0,1),
 ('76','7','Revenus financiers et produits assimiles','7',2,'PRODUIT',0,1),
 ('77','7','Produits exceptionnels','7',2,'PRODUIT',0,1),
 ('78','7','Reprises de provisions et amortissements','7',2,'PRODUIT',0,1),
 ('79','7','Transferts de produits','7',2,'PRODUIT',0,1),
 ('8','8','Comptes des autres charges et autres produits',NULL,1,'HORS_BILAN',0,1),
 ('81','8','Valeurs comptables des cessions d immobilisations','8',2,'HORS_BILAN',0,1),
 ('82','8','Produits des cessions d immobilisations','8',2,'HORS_BILAN',0,1),
 ('83','8','Charges hors activites ordinaires','8',2,'HORS_BILAN',0,1),
 ('84','8','Produits hors activites ordinaires','8',2,'HORS_BILAN',0,1),
 ('85','8','Dotations hors activites ordinaires','8',2,'HORS_BILAN',0,1),
 ('86','8','Reprises hors activites ordinaires','8',2,'HORS_BILAN',0,1),
 ('87','8','Participations des travailleurs','8',2,'HORS_BILAN',0,1),
 ('88','8','Subventions d equilibre','8',2,'HORS_BILAN',0,1),
 ('89','8','Bilan ouverture et cloture','8',2,'HORS_BILAN',0,1)
ON DUPLICATE KEY UPDATE
 `libelle`=VALUES(`libelle`), `classe_ohada`=VALUES(`classe_ohada`), `parent_compte`=VALUES(`parent_compte`),
 `niveau`=VALUES(`niveau`), `type_compte`=VALUES(`type_compte`), `est_mouvementable`=VALUES(`est_mouvementable`), `est_actif`=VALUES(`est_actif`);

-- ===================== 2026_03_13_246000_add_unique_constraints_for_banking_integrity =====================
ALTER TABLE `tb_clients`
  ADD UNIQUE KEY `uq_tb_clients_piece_type_num` (`type_piece_identite`,`numero_piece_identite`),
  ADD UNIQUE KEY `uq_tb_clients_email` (`email`);
ALTER TABLE `tb_comptes`
  ADD UNIQUE KEY `uq_tb_comptes_client_type_devise` (`client_matricule`,`type`,`devise`);

-- ===================== 2026_03_16_000011_credit_module_tables =====================
CREATE TABLE IF NOT EXISTS `tb_credit_demandes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_dossier` VARCHAR(30) NOT NULL,
  `client_matricule` VARCHAR(30) NOT NULL,
  `compte_id` VARCHAR(64) NOT NULL,
  `portefeuille_id` BIGINT UNSIGNED NOT NULL,
  `code_zone` VARCHAR(20) NOT NULL,
  `agent_createur_matricule` VARCHAR(30) NOT NULL,
  `montant_demande` DECIMAL(15,2) NOT NULL,
  `devise` VARCHAR(5) NOT NULL DEFAULT 'CDF',
  `duree_mois` TINYINT UNSIGNED NOT NULL,
  `taux_interet_mensuel` DECIMAL(6,4) NOT NULL,
  `type_credit` ENUM('INDIVIDUEL','SOLIDAIRE','PME') NOT NULL DEFAULT 'INDIVIDUEL',
  `objet_credit` VARCHAR(500) NOT NULL,
  `garantie_description` TEXT NULL,
  `montant_approuve` DECIMAL(15,2) NULL,
  `montant_total_echeances` DECIMAL(15,2) NULL,
  `total_interets` DECIMAL(15,2) NULL,
  `statut_global` ENUM('BROUILLON','SOUMIS','EN_ANALYSE','EN_VALIDATION','PRET_A_DEBLOQUER','DEBLOQUE','EN_REMBOURSEMENT','EN_RETARD','SOLDE','ANNULE','SUSPENDU','SUSPECT') NOT NULL DEFAULT 'BROUILLON',
  `est_annule` TINYINT(1) NOT NULL DEFAULT 0,
  `motif_annulation` TEXT NULL,
  `annule_par_matricule` VARCHAR(30) NULL,
  `annule_le` TIMESTAMP NULL,
  `est_suspendu` TINYINT(1) NOT NULL DEFAULT 0,
  `motif_suspension` TEXT NULL,
  `suspendu_par_matricule` VARCHAR(30) NULL,
  `suspendu_le` TIMESTAMP NULL,
  `est_suspect` TINYINT(1) NOT NULL DEFAULT 0,
  `motif_suspicion` TEXT NULL,
  `signale_par_matricule` VARCHAR(30) NULL,
  `signale_le` TIMESTAMP NULL,
  `soumis_le` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tb_credit_demandes_numero_dossier_unique` (`numero_dossier`),
  KEY `idx_crd_client` (`client_matricule`),
  KEY `idx_crd_zone` (`code_zone`),
  KEY `idx_crd_statut` (`statut_global`),
  KEY `idx_crd_portef` (`portefeuille_id`),
  KEY `idx_crd_agent` (`agent_createur_matricule`),
  KEY `tb_credit_demandes_compte_id_foreign` (`compte_id`),
  CONSTRAINT `tb_credit_demandes_client_matricule_foreign` FOREIGN KEY (`client_matricule`) REFERENCES `tb_clients`(`matricule`) ON DELETE RESTRICT,
  CONSTRAINT `tb_credit_demandes_compte_id_foreign` FOREIGN KEY (`compte_id`) REFERENCES `tb_comptes`(`code_compte`) ON DELETE RESTRICT,
  CONSTRAINT `tb_credit_demandes_portefeuille_id_foreign` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents`(`id`) ON DELETE RESTRICT,
  CONSTRAINT `tb_credit_demandes_code_zone_foreign` FOREIGN KEY (`code_zone`) REFERENCES `tb_zones`(`code_zone`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_credit_analyses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `credit_demande_id` BIGINT UNSIGNED NOT NULL,
  `analyseur_matricule` VARCHAR(30) NOT NULL,
  `revenu_mensuel_verifie` DECIMAL(15,2) NULL,
  `capacite_remboursement` DECIMAL(15,2) NULL,
  `ratio_endettement` DECIMAL(6,2) NULL,
  `score_risque` ENUM('FAIBLE','MOYEN','ELEVE','TRES_ELEVE') NULL,
  `historique_credit` TEXT NULL,
  `garanties_evaluees` TEXT NULL,
  `observations` TEXT NULL,
  `recommandation` ENUM('FAVORABLE','FAVORABLE_AVEC_RESERVE','DEFAVORABLE') NOT NULL,
  `montant_recommande` DECIMAL(15,2) NULL,
  `statut` ENUM('EN_COURS','COMPLETE','ANNULE') NOT NULL DEFAULT 'EN_COURS',
  `complete_le` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_analyse_demande` (`credit_demande_id`),
  KEY `idx_analyse_agt` (`analyseur_matricule`),
  CONSTRAINT `tb_credit_analyses_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_credit_validations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `credit_demande_id` BIGINT UNSIGNED NOT NULL,
  `type_validateur` ENUM('AGENT_CREDIT','CHARGE_OPERATIONS','CONTROLEUR','GERANT') NOT NULL,
  `validateur_matricule` VARCHAR(30) NOT NULL,
  `decision` ENUM('EN_ATTENTE','APPROUVE','APPROUVE_AVEC_RESERVE','REJETE') NOT NULL DEFAULT 'EN_ATTENTE',
  `montant_valide` DECIMAL(15,2) NULL,
  `observations` TEXT NULL,
  `conditions` TEXT NULL,
  `ordre_etape` TINYINT UNSIGNED NOT NULL,
  `etape_precedente_ok` TINYINT(1) NOT NULL DEFAULT 0,
  `valide_le` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_validation_type` (`credit_demande_id`,`type_validateur`),
  KEY `idx_valid_agt` (`validateur_matricule`),
  KEY `idx_valid_dec` (`decision`),
  CONSTRAINT `tb_credit_validations_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_credit_pieces` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `credit_demande_id` BIGINT UNSIGNED NOT NULL,
  `libelle` VARCHAR(200) NOT NULL,
  `type_piece` ENUM('IDENTITE','DOMICILE','REVENU','GARANTIE','AUTRE') NOT NULL,
  `nom_fichier` VARCHAR(255) NULL,
  `est_recu` TINYINT(1) NOT NULL DEFAULT 0,
  `est_conforme` TINYINT(1) NULL,
  `observations` VARCHAR(500) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pieces_dem` (`credit_demande_id`),
  CONSTRAINT `tb_credit_pieces_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_credit_deblocages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `credit_demande_id` BIGINT UNSIGNED NOT NULL,
  `agent_matricule` VARCHAR(30) NOT NULL,
  `compte_debit_id` VARCHAR(64) NOT NULL,
  `compte_credit_id` VARCHAR(64) NOT NULL,
  `montant_debloque` DECIMAL(15,2) NOT NULL,
  `devise` VARCHAR(5) NOT NULL DEFAULT 'CDF',
  `frais_dossier` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `montant_net_verse` DECIMAL(15,2) NOT NULL,
  `reference_transaction` VARCHAR(50) NULL,
  `numero_ordre` VARCHAR(30) NULL,
  `observations` TEXT NULL,
  `debloque_le` TIMESTAMP NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tb_credit_deblocages_credit_demande_id_unique` (`credit_demande_id`),
  KEY `tb_credit_deblocages_compte_debit_id_foreign` (`compte_debit_id`),
  KEY `tb_credit_deblocages_compte_credit_id_foreign` (`compte_credit_id`),
  CONSTRAINT `tb_credit_deblocages_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes`(`id`) ON DELETE RESTRICT,
  CONSTRAINT `tb_credit_deblocages_compte_debit_id_foreign` FOREIGN KEY (`compte_debit_id`) REFERENCES `tb_comptes`(`code_compte`) ON DELETE RESTRICT,
  CONSTRAINT `tb_credit_deblocages_compte_credit_id_foreign` FOREIGN KEY (`compte_credit_id`) REFERENCES `tb_comptes`(`code_compte`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_credit_echeanciers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `credit_demande_id` BIGINT UNSIGNED NOT NULL,
  `montant_capital` DECIMAL(15,2) NOT NULL,
  `taux_mensuel` DECIMAL(6,4) NOT NULL,
  `duree_mois` TINYINT UNSIGNED NOT NULL,
  `date_premier_remboursement` DATE NOT NULL,
  `type_amortissement` ENUM('DEGRESSIF','LINEAIRE') NOT NULL DEFAULT 'DEGRESSIF',
  `total_capital` DECIMAL(15,2) NOT NULL,
  `total_interets` DECIMAL(15,2) NOT NULL,
  `total_general` DECIMAL(15,2) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tb_credit_echeanciers_credit_demande_id_unique` (`credit_demande_id`),
  CONSTRAINT `tb_credit_echeanciers_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_credit_echeances` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `echeancier_id` BIGINT UNSIGNED NOT NULL,
  `numero_echeance` TINYINT UNSIGNED NOT NULL,
  `date_echeance` DATE NOT NULL,
  `capital_restant_debut` DECIMAL(15,2) NOT NULL,
  `capital_echeance` DECIMAL(15,2) NOT NULL,
  `interet_echeance` DECIMAL(15,2) NOT NULL,
  `total_echeance` DECIMAL(15,2) NOT NULL,
  `capital_restant_fin` DECIMAL(15,2) NOT NULL,
  `statut` ENUM('EN_ATTENTE','PAYE','PARTIELLEMENT_PAYE','EN_RETARD','REPORTE') NOT NULL DEFAULT 'EN_ATTENTE',
  `montant_paye` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `date_paiement_effectif` DATE NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ech_echeancier` (`echeancier_id`),
  KEY `idx_ech_date` (`date_echeance`),
  KEY `idx_ech_statut` (`statut`),
  CONSTRAINT `tb_credit_echeances_echeancier_id_foreign` FOREIGN KEY (`echeancier_id`) REFERENCES `tb_credit_echeanciers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_credit_remboursements` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `credit_demande_id` BIGINT UNSIGNED NOT NULL,
  `echeance_id` BIGINT UNSIGNED NULL,
  `agent_matricule` VARCHAR(30) NOT NULL,
  `compte_id` VARCHAR(64) NOT NULL,
  `montant_recu` DECIMAL(15,2) NOT NULL,
  `dont_capital` DECIMAL(15,2) NOT NULL,
  `dont_interet` DECIMAL(15,2) NOT NULL,
  `dont_penalite` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `devise` VARCHAR(5) NOT NULL DEFAULT 'CDF',
  `type_remboursement` ENUM('ECHEANCE','PARTIEL','ANTICIPE','PENALITE') NOT NULL DEFAULT 'ECHEANCE',
  `reference_caisse` VARCHAR(50) NULL,
  `observations` TEXT NULL,
  `recu_le` TIMESTAMP NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rembours_dem` (`credit_demande_id`),
  KEY `idx_rembours_ech` (`echeance_id`),
  KEY `tb_credit_remboursements_compte_id_foreign` (`compte_id`),
  CONSTRAINT `tb_credit_remboursements_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes`(`id`) ON DELETE RESTRICT,
  CONSTRAINT `tb_credit_remboursements_echeance_id_foreign` FOREIGN KEY (`echeance_id`) REFERENCES `tb_credit_echeances`(`id`) ON DELETE SET NULL,
  CONSTRAINT `tb_credit_remboursements_compte_id_foreign` FOREIGN KEY (`compte_id`) REFERENCES `tb_comptes`(`code_compte`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_credit_audits` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `credit_demande_id` BIGINT UNSIGNED NOT NULL,
  `acteur_matricule` VARCHAR(30) NULL,
  `type_action` ENUM('CREATION','SOUMISSION','ANALYSE_DEMARREE','ANALYSE_COMPLETE','VALIDATION_PARTIELLE','VALIDATION_COMPLETE','REJET','DEBLOCAGE','REMBOURSEMENT','ANNULATION','SUSPENSION','LEVER_SUSPENSION','SIGNALEMENT_SUSPECT','LEVER_SUSPICION','MODIFICATION') NOT NULL,
  `ancien_statut` VARCHAR(30) NULL,
  `nouveau_statut` VARCHAR(30) NULL,
  `details` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `idx_audit_dem` (`credit_demande_id`),
  KEY `idx_audit_action` (`type_action`),
  CONSTRAINT `tb_credit_audits_credit_demande_id_foreign` FOREIGN KEY (`credit_demande_id`) REFERENCES `tb_credit_demandes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================== 2026_03_16_000012_credit_permissions =====================
INSERT IGNORE INTO `tb_permissions` (`code`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 ('EBEN-PER53','Voir liste crédits','Accéder à la liste des dossiers crédit',@now,@now),
 ('EBEN-PER54','Créer demande crédit','Saisir une nouvelle demande de crédit',@now,@now),
 ('EBEN-PER55','Modifier demande brouillon','Modifier un dossier en statut BROUILLON',@now,@now),
 ('EBEN-PER56','Soumettre demande crédit','Soumettre un dossier pour analyse (BROUILLON vers SOUMIS)',@now,@now),
 ('EBEN-PER57','Voir détail dossier crédit','Consulter le détail complet d''un dossier crédit',@now,@now),
 ('EBEN-PER58','Saisir analyse crédit','Démarrer et saisir l''analyse d''un dossier crédit',@now,@now),
 ('EBEN-PER59','Compléter analyse crédit','Marquer l''analyse comme complète',@now,@now),
 ('EBEN-PER60','Valider bloc Agent crédit','Validation niveau 1 - Chargé de crédit',@now,@now),
 ('EBEN-PER61','Valider bloc Chargé opérations','Validation niveau 2 - Chargé des opérations',@now,@now),
 ('EBEN-PER62','Valider bloc Contrôleur','Validation niveau 3 - Contrôleur interne',@now,@now),
 ('EBEN-PER63','Valider bloc Gérant','Validation niveau 4 - Gérant / Directeur (validation finale)',@now,@now),
 ('EBEN-PER64','Débloquer crédit','Effectuer le déblocage des fonds (PRET_A_DEBLOQUER vers DEBLOQUE)',@now,@now),
 ('EBEN-PER65','Enregistrer remboursement','Enregistrer un paiement d''échéance ou remboursement',@now,@now),
 ('EBEN-PER66','Annuler dossier crédit','Annuler définitivement un dossier crédit',@now,@now),
 ('EBEN-PER67','Suspendre dossier crédit','Mettre un dossier en suspension temporaire',@now,@now),
 ('EBEN-PER68','Signaler dossier suspect','Signaler un dossier comme suspect (fraude, irrégularité)',@now,@now),
 ('EBEN-PER69','Lever suspension/suspicion','Lever une suspension ou un signalement de suspicion',@now,@now),
 ('EBEN-PER70','Tableau de bord crédit','Accéder au tableau de bord de supervision du crédit',@now,@now),
 ('EBEN-PER71','Imprimer échéancier PDF','Générer et imprimer l''échéancier de remboursement PDF',@now,@now),
 ('EBEN-PER72','Historique audit dossier','Consulter le journal d''audit complet d''un dossier crédit',@now,@now);

INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`)
SELECT 'EBEN-ROL1', `code`, @now, @now FROM `tb_permissions` WHERE `code` BETWEEN 'EBEN-PER53' AND 'EBEN-PER72'
 AND `code` IN ('EBEN-PER53','EBEN-PER54','EBEN-PER55','EBEN-PER56','EBEN-PER57','EBEN-PER58','EBEN-PER59','EBEN-PER60','EBEN-PER61','EBEN-PER62','EBEN-PER63','EBEN-PER64','EBEN-PER65','EBEN-PER66','EBEN-PER67','EBEN-PER68','EBEN-PER69','EBEN-PER70','EBEN-PER71','EBEN-PER72');

INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL2','EBEN-PER65',@now,@now),
 ('EBEN-ROL3','EBEN-PER53',@now,@now),('EBEN-ROL3','EBEN-PER57',@now,@now),('EBEN-ROL3','EBEN-PER63',@now,@now),('EBEN-ROL3','EBEN-PER66',@now,@now),('EBEN-ROL3','EBEN-PER67',@now,@now),('EBEN-ROL3','EBEN-PER68',@now,@now),('EBEN-ROL3','EBEN-PER69',@now,@now),('EBEN-ROL3','EBEN-PER70',@now,@now),('EBEN-ROL3','EBEN-PER71',@now,@now),('EBEN-ROL3','EBEN-PER72',@now,@now),
 ('EBEN-ROL5','EBEN-PER53',@now,@now),('EBEN-ROL5','EBEN-PER57',@now,@now),('EBEN-ROL5','EBEN-PER62',@now,@now),('EBEN-ROL5','EBEN-PER67',@now,@now),('EBEN-ROL5','EBEN-PER68',@now,@now),('EBEN-ROL5','EBEN-PER70',@now,@now),('EBEN-ROL5','EBEN-PER71',@now,@now),('EBEN-ROL5','EBEN-PER72',@now,@now),
 ('EBEN-ROL6','EBEN-PER53',@now,@now),('EBEN-ROL6','EBEN-PER54',@now,@now),('EBEN-ROL6','EBEN-PER55',@now,@now),('EBEN-ROL6','EBEN-PER56',@now,@now),('EBEN-ROL6','EBEN-PER57',@now,@now),('EBEN-ROL6','EBEN-PER58',@now,@now),('EBEN-ROL6','EBEN-PER59',@now,@now),('EBEN-ROL6','EBEN-PER60',@now,@now),('EBEN-ROL6','EBEN-PER61',@now,@now),('EBEN-ROL6','EBEN-PER64',@now,@now),('EBEN-ROL6','EBEN-PER65',@now,@now),('EBEN-ROL6','EBEN-PER70',@now,@now),('EBEN-ROL6','EBEN-PER71',@now,@now),
 ('EBEN-ROL8','EBEN-PER53',@now,@now),('EBEN-ROL8','EBEN-PER54',@now,@now),('EBEN-ROL8','EBEN-PER55',@now,@now),('EBEN-ROL8','EBEN-PER56',@now,@now),('EBEN-ROL8','EBEN-PER57',@now,@now),('EBEN-ROL8','EBEN-PER71',@now,@now);

-- ===================== 2026_03_18_000013_extend_permissions_crud_tresorerie_agents_terrain =====================
INSERT IGNORE INTO `tb_permissions` (`code`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 ('EBEN-PER76','Voir rapport agents terrain','Acces au rapport Agents Terrain depuis le menu Clients/Membres',@now,@now),
 ('EBEN-PER77','Ajouter en tresorerie','Creation/ajout d operations dans le module tresorerie',@now,@now),
 ('EBEN-PER78','Modifier en tresorerie','Modification d operations dans le module tresorerie',@now,@now),
 ('EBEN-PER79','Supprimer en tresorerie','Suppression d operations dans le module tresorerie',@now,@now);
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL1','EBEN-PER76',@now,@now),('EBEN-ROL1','EBEN-PER77',@now,@now),('EBEN-ROL1','EBEN-PER78',@now,@now),('EBEN-ROL1','EBEN-PER79',@now,@now),
 ('EBEN-ROL8','EBEN-PER76',@now,@now),('EBEN-ROL8','EBEN-PER77',@now,@now),('EBEN-ROL8','EBEN-PER78',@now,@now),('EBEN-ROL8','EBEN-PER79',@now,@now),
 ('EBEN-ROL3','EBEN-PER76',@now,@now),('EBEN-ROL5','EBEN-PER76',@now,@now),('EBEN-ROL9','EBEN-PER76',@now,@now);

-- ===================== 2026_03_18_000014_add_module_specific_crud_permissions =====================
INSERT IGNORE INTO `tb_permissions` (`code`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 ('EBEN-PER80','Ajouter client (module Clients)','Permission d ajout dans le module Clients',@now,@now),
 ('EBEN-PER81','Modifier client (module Clients)','Permission de modification dans le module Clients',@now,@now),
 ('EBEN-PER82','Supprimer client (module Clients)','Permission de suppression dans le module Clients',@now,@now),
 ('EBEN-PER83','Ajouter compte (module Comptes)','Permission d ajout dans le module Comptes',@now,@now),
 ('EBEN-PER84','Modifier compte (module Comptes)','Permission de modification dans le module Comptes',@now,@now),
 ('EBEN-PER85','Supprimer compte (module Comptes)','Permission de suppression dans le module Comptes',@now,@now),
 ('EBEN-PER86','Ajouter agent RH','Permission d ajout des agents RH',@now,@now),
 ('EBEN-PER87','Modifier agent RH','Permission de modification des agents RH',@now,@now),
 ('EBEN-PER88','Supprimer agent RH','Permission de suppression des agents RH',@now,@now),
 ('EBEN-PER89','Ajouter service/poste RH','Permission d ajout des services et postes RH',@now,@now),
 ('EBEN-PER90','Modifier service RH','Permission de modification des services RH',@now,@now),
 ('EBEN-PER91','Supprimer service/poste RH','Permission de suppression des services et postes RH',@now,@now),
 ('EBEN-PER92','Ajouter affectation RH','Permission d ajout des affectations RH',@now,@now),
 ('EBEN-PER93','Modifier affectation RH','Permission de modification des affectations RH',@now,@now),
 ('EBEN-PER94','Supprimer affectation RH','Permission de suppression des affectations RH',@now,@now),
 ('EBEN-PER95','Ajouter operation caisse','Permission d ajout dans le module Caisse',@now,@now),
 ('EBEN-PER96','Modifier operation caisse','Permission de modification dans le module Caisse',@now,@now),
 ('EBEN-PER97','Supprimer operation caisse','Permission de suppression/annulation dans le module Caisse',@now,@now),
 ('EBEN-PER100','Ajouter operation credit','Permission d ajout dans le module Credit',@now,@now),
 ('EBEN-PER101','Modifier workflow credit','Permission de modification du workflow Credit',@now,@now),
 ('EBEN-PER102','Supprimer/annuler dossier credit','Permission de suppression/annulation dans le module Credit',@now,@now);
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL1','EBEN-PER80',@now,@now),('EBEN-ROL1','EBEN-PER81',@now,@now),('EBEN-ROL1','EBEN-PER82',@now,@now),('EBEN-ROL1','EBEN-PER83',@now,@now),('EBEN-ROL1','EBEN-PER84',@now,@now),('EBEN-ROL1','EBEN-PER85',@now,@now),('EBEN-ROL1','EBEN-PER86',@now,@now),('EBEN-ROL1','EBEN-PER87',@now,@now),('EBEN-ROL1','EBEN-PER88',@now,@now),('EBEN-ROL1','EBEN-PER89',@now,@now),('EBEN-ROL1','EBEN-PER90',@now,@now),('EBEN-ROL1','EBEN-PER91',@now,@now),('EBEN-ROL1','EBEN-PER92',@now,@now),('EBEN-ROL1','EBEN-PER93',@now,@now),('EBEN-ROL1','EBEN-PER94',@now,@now),('EBEN-ROL1','EBEN-PER95',@now,@now),('EBEN-ROL1','EBEN-PER96',@now,@now),('EBEN-ROL1','EBEN-PER97',@now,@now),('EBEN-ROL1','EBEN-PER100',@now,@now),('EBEN-ROL1','EBEN-PER101',@now,@now),('EBEN-ROL1','EBEN-PER102',@now,@now),
 ('EBEN-ROL4','EBEN-PER86',@now,@now),('EBEN-ROL4','EBEN-PER87',@now,@now),('EBEN-ROL4','EBEN-PER88',@now,@now),('EBEN-ROL4','EBEN-PER89',@now,@now),('EBEN-ROL4','EBEN-PER90',@now,@now),('EBEN-ROL4','EBEN-PER91',@now,@now),('EBEN-ROL4','EBEN-PER92',@now,@now),('EBEN-ROL4','EBEN-PER93',@now,@now),('EBEN-ROL4','EBEN-PER94',@now,@now),
 ('EBEN-ROL2','EBEN-PER95',@now,@now),('EBEN-ROL2','EBEN-PER96',@now,@now),('EBEN-ROL2','EBEN-PER97',@now,@now),
 ('EBEN-ROL6','EBEN-PER80',@now,@now),('EBEN-ROL6','EBEN-PER81',@now,@now),('EBEN-ROL6','EBEN-PER82',@now,@now),('EBEN-ROL6','EBEN-PER83',@now,@now),('EBEN-ROL6','EBEN-PER84',@now,@now),('EBEN-ROL6','EBEN-PER85',@now,@now),('EBEN-ROL6','EBEN-PER100',@now,@now),('EBEN-ROL6','EBEN-PER101',@now,@now),('EBEN-ROL6','EBEN-PER102',@now,@now),
 ('EBEN-ROL9','EBEN-PER80',@now,@now),('EBEN-ROL9','EBEN-PER81',@now,@now),('EBEN-ROL9','EBEN-PER82',@now,@now);

-- ===================== 2026_03_18_000015_remove_global_crud_permissions =====================
DELETE FROM `tb_role_permission` WHERE `permission_code` IN ('EBEN-PER73','EBEN-PER74','EBEN-PER75');
DELETE FROM `tb_permissions` WHERE `code` IN ('EBEN-PER73','EBEN-PER74','EBEN-PER75');

-- ===================== 2026_03_18_000016_remove_duplicate_module_permissions =====================
DELETE FROM `tb_role_permission` WHERE `permission_code` IN ('EBEN-PER80','EBEN-PER81','EBEN-PER82','EBEN-PER83','EBEN-PER84','EBEN-PER85','EBEN-PER86','EBEN-PER87','EBEN-PER88','EBEN-PER89','EBEN-PER90','EBEN-PER91','EBEN-PER92','EBEN-PER93','EBEN-PER94','EBEN-PER95','EBEN-PER96','EBEN-PER97','EBEN-PER100','EBEN-PER101','EBEN-PER102');
DELETE FROM `tb_permissions` WHERE `code` IN ('EBEN-PER80','EBEN-PER81','EBEN-PER82','EBEN-PER83','EBEN-PER84','EBEN-PER85','EBEN-PER86','EBEN-PER87','EBEN-PER88','EBEN-PER89','EBEN-PER90','EBEN-PER91','EBEN-PER92','EBEN-PER93','EBEN-PER94','EBEN-PER95','EBEN-PER96','EBEN-PER97','EBEN-PER100','EBEN-PER101','EBEN-PER102');

-- ===================== 2026_03_18_000017_normalize_permission_action_aliases =====================
UPDATE `tb_permissions` SET `nom`='Creer/Ajouter agent',`description`='Creer (ajouter) un nouvel agent',`updated_at`=@now WHERE `code`='EBEN-PER7';
UPDATE `tb_permissions` SET `nom`='Modifier/Supprimer agent-service-poste',`description`='Modifier ou supprimer agent, service et poste RH',`updated_at`=@now WHERE `code`='EBEN-PER8';
UPDATE `tb_permissions` SET `nom`='Affectations (Creer/Modifier/Supprimer)',`description`='Gerer les affectations: creer, modifier, supprimer',`updated_at`=@now WHERE `code`='EBEN-PER9';
UPDATE `tb_permissions` SET `nom`='Voir caisse + Demandes',`description`='Consulter la caisse et initier des demandes selon routes autorisees',`updated_at`=@now WHERE `code`='EBEN-PER10';
UPDATE `tb_permissions` SET `nom`='Gerer operations caisse',`description`='Creer, modifier, confirmer et annuler des operations caisse',`updated_at`=@now WHERE `code`='EBEN-PER11';
UPDATE `tb_permissions` SET `nom`='Creer/Ajouter client',`description`='Creer (ajouter) un client',`updated_at`=@now WHERE `code`='EBEN-PER16';
UPDATE `tb_permissions` SET `nom`='Modifier/Supprimer client',`description`='Modifier ou supprimer un client',`updated_at`=@now WHERE `code`='EBEN-PER17';
UPDATE `tb_permissions` SET `nom`='Creer/Ajouter/Supprimer compte',`description`='Creer (ajouter) ou supprimer un compte',`updated_at`=@now WHERE `code`='EBEN-PER19';
UPDATE `tb_permissions` SET `nom`='Creer/Ajouter demande credit',`description`='Creer (ajouter) une nouvelle demande de credit',`updated_at`=@now WHERE `code`='EBEN-PER54';
UPDATE `tb_permissions` SET `nom`='Annuler/Supprimer dossier credit',`description`='Annuler (supprimer) definitivement un dossier credit',`updated_at`=@now WHERE `code`='EBEN-PER66';
UPDATE `tb_permissions` SET `nom`='Creer/Ajouter en tresorerie',`description`='Creation/ajout d operations dans le module tresorerie',`updated_at`=@now WHERE `code`='EBEN-PER77';
UPDATE `tb_permissions` SET `nom`='Modifier/Mettre a jour en tresorerie',`description`='Modification/mise a jour d operations dans le module tresorerie',`updated_at`=@now WHERE `code`='EBEN-PER78';
UPDATE `tb_permissions` SET `nom`='Supprimer/Annuler en tresorerie',`description`='Suppression/annulation d operations dans le module tresorerie',`updated_at`=@now WHERE `code`='EBEN-PER79';

-- ===================== 2026_03_18_000018_refine_permission_action_wording_by_context =====================
UPDATE `tb_permissions` SET `nom`='Creer agent',`description`='Creer un nouvel agent',`updated_at`=@now WHERE `code`='EBEN-PER7';
UPDATE `tb_permissions` SET `nom`='Modifier agent/service/poste',`description`='Modifier ou supprimer agent, service et poste RH',`updated_at`=@now WHERE `code`='EBEN-PER8';
UPDATE `tb_permissions` SET `nom`='Gerer affectations',`description`='Gerer les affectations RH',`updated_at`=@now WHERE `code`='EBEN-PER9';
UPDATE `tb_permissions` SET `nom`='Voir caisse et demandes',`description`='Consulter la caisse et initier des demandes',`updated_at`=@now WHERE `code`='EBEN-PER10';
UPDATE `tb_permissions` SET `nom`='Gerer operations caisse',`description`='Gerer les operations caisse, y compris annulation',`updated_at`=@now WHERE `code`='EBEN-PER11';
UPDATE `tb_permissions` SET `nom`='Creer client',`description`='Enregistrer un client',`updated_at`=@now WHERE `code`='EBEN-PER16';
UPDATE `tb_permissions` SET `nom`='Modifier client',`description`='Modifier ou supprimer un client',`updated_at`=@now WHERE `code`='EBEN-PER17';
UPDATE `tb_permissions` SET `nom`='Gerer compte client',`description`='Ouvrir et fermer un compte client',`updated_at`=@now WHERE `code`='EBEN-PER19';
UPDATE `tb_permissions` SET `nom`='Creer demande credit',`description`='Creer une nouvelle demande de credit',`updated_at`=@now WHERE `code`='EBEN-PER54';
UPDATE `tb_permissions` SET `nom`='Annuler dossier credit',`description`='Annuler definitivement un dossier credit',`updated_at`=@now WHERE `code`='EBEN-PER66';
UPDATE `tb_permissions` SET `nom`='Ajouter operation tresorerie',`description`='Ajouter une operation dans le module tresorerie',`updated_at`=@now WHERE `code`='EBEN-PER77';
UPDATE `tb_permissions` SET `nom`='Modifier operation tresorerie',`description`='Modifier une operation dans le module tresorerie',`updated_at`=@now WHERE `code`='EBEN-PER78';
UPDATE `tb_permissions` SET `nom`='Annuler operation tresorerie',`description`='Annuler une operation dans le module tresorerie',`updated_at`=@now WHERE `code`='EBEN-PER79';

-- ===================== 2026_03_18_000019_split_modify_delete_permissions_by_module =====================
INSERT IGNORE INTO `tb_permissions` (`code`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 ('EBEN-PER103','Modifier agent et service/poste RH','Modifier les agents, services et postes RH',@now,@now),
 ('EBEN-PER104','Supprimer agent/service/poste RH','Supprimer les agents, services et postes RH',@now,@now),
 ('EBEN-PER105','Modifier affectation RH','Modifier une affectation RH',@now,@now),
 ('EBEN-PER106','Supprimer affectation RH','Supprimer une affectation RH',@now,@now),
 ('EBEN-PER107','Supprimer client','Supprimer un client',@now,@now),
 ('EBEN-PER108','Supprimer compte client','Fermer/supprimer un compte client',@now,@now);
-- copyRoleMappings (PER8->103,104 ; PER9->105,106 ; PER17->107 ; PER19->108)
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`)
SELECT `role_code`,'EBEN-PER103',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER8';
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`)
SELECT `role_code`,'EBEN-PER104',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER8';
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`)
SELECT `role_code`,'EBEN-PER105',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER9';
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`)
SELECT `role_code`,'EBEN-PER106',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER9';
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`)
SELECT `role_code`,'EBEN-PER107',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER17';
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`)
SELECT `role_code`,'EBEN-PER108',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER19';
UPDATE `tb_permissions` SET `nom`='Ajouter service/poste RH',`description`='Ajouter des services et postes RH',`updated_at`=@now WHERE `code`='EBEN-PER8';
UPDATE `tb_permissions` SET `nom`='Voir et creer affectations RH',`description`='Consulter et creer les affectations RH',`updated_at`=@now WHERE `code`='EBEN-PER9';
UPDATE `tb_permissions` SET `nom`='Gerer operations caisse',`description`='Creer et confirmer les operations caisse',`updated_at`=@now WHERE `code`='EBEN-PER11';
UPDATE `tb_permissions` SET `nom`='Modifier client',`description`='Modifier un client',`updated_at`=@now WHERE `code`='EBEN-PER17';
UPDATE `tb_permissions` SET `nom`='Creer compte client',`description`='Ouvrir un compte client',`updated_at`=@now WHERE `code`='EBEN-PER19';

-- ===================== 2026_03_18_000020_add_caisse_journal_report_view_permission =====================
INSERT IGNORE INTO `tb_permissions` (`code`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 ('EBEN-PER110','Voir rapport journalier caisse/guichet','Consulter le journal des operations et le rapport journalier caisse/guichet',@now,@now);
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`)
SELECT `role_code`,'EBEN-PER110',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER10';

-- ===================== 2026_03_19_000013_credit_legacy_permissions_cleanup =====================
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) SELECT `role_code`,'EBEN-PER53',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER30';
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) SELECT `role_code`,'EBEN-PER56',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER31';
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) SELECT `role_code`,'EBEN-PER58',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER32';
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) SELECT `role_code`,'EBEN-PER63',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER33';
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) SELECT `role_code`,'EBEN-PER65',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER34';
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) SELECT `role_code`,'EBEN-PER72',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER35';
DELETE FROM `tb_role_permission` WHERE `permission_code` IN ('EBEN-PER30','EBEN-PER31','EBEN-PER32','EBEN-PER33','EBEN-PER34','EBEN-PER35');
UPDATE `tb_permissions` SET `description`=CONCAT('[LEGACY] ', IFNULL(`description`,'Permission historique non utilisée')),`updated_at`=@now WHERE `code` IN ('EBEN-PER30','EBEN-PER31','EBEN-PER32','EBEN-PER33','EBEN-PER34','EBEN-PER35');

-- ===================== 2026_03_19_000014_make_credit_demande_account_fields_nullable =====================
ALTER TABLE `tb_credit_demandes` DROP FOREIGN KEY `tb_credit_demandes_compte_id_foreign`;
ALTER TABLE `tb_credit_demandes` DROP FOREIGN KEY `tb_credit_demandes_portefeuille_id_foreign`;
ALTER TABLE `tb_credit_demandes`
  MODIFY COLUMN `compte_id` VARCHAR(64) NULL,
  MODIFY COLUMN `portefeuille_id` BIGINT UNSIGNED NULL;
ALTER TABLE `tb_credit_demandes`
  ADD CONSTRAINT `tb_credit_demandes_compte_id_foreign` FOREIGN KEY (`compte_id`) REFERENCES `tb_comptes`(`code_compte`) ON DELETE RESTRICT,
  ADD CONSTRAINT `tb_credit_demandes_portefeuille_id_foreign` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents`(`id`) ON DELETE RESTRICT;

-- ===================== 2026_03_21_000022_create_tb_affectations_zones =====================
CREATE TABLE IF NOT EXISTS `tb_affectations_zones` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code_zone` VARCHAR(50) NOT NULL,
  `agent_matricule` VARCHAR(50) NOT NULL,
  `date_debut` DATE NOT NULL,
  `date_fin` DATE NULL,
  `Etat` VARCHAR(50) NOT NULL DEFAULT 'ACTIF',
  `motif` VARCHAR(255) NULL,
  `effectue_par_user_id` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `idx_aff_zone_active` (`code_zone`,`Etat`,`date_fin`),
  KEY `idx_aff_zone_agent_active` (`agent_matricule`,`Etat`,`date_fin`),
  KEY `idx_aff_zone_period` (`code_zone`,`date_debut`),
  CONSTRAINT `fk_aff_zone_agent` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents`(`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tb_affectations_zones` (`code_zone`,`agent_matricule`,`date_debut`,`date_fin`,`Etat`,`motif`,`effectue_par_user_id`,`created_at`,`updated_at`)
SELECT `code_zone`,`agent_commercial_matricule`,CURDATE(),NULL,'ACTIF','Initialisation depuis tb_zones',NULL,@now,@now
FROM `tb_zones` WHERE `agent_commercial_matricule` IS NOT NULL;

-- ===================== 2026_03_21_000023_create_tb_affectations_portefeuilles =====================
CREATE TABLE IF NOT EXISTS `tb_affectations_portefeuilles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `portefeuille_id` BIGINT UNSIGNED NOT NULL,
  `agent_matricule` VARCHAR(50) NULL,
  `date_debut` DATE NOT NULL,
  `date_fin` DATE NULL,
  `Etat` VARCHAR(50) NOT NULL DEFAULT 'ACTIF',
  `motif` VARCHAR(255) NULL,
  `effectue_par_user_id` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `idx_aff_pf_active` (`portefeuille_id`,`Etat`,`date_fin`),
  KEY `idx_aff_pf_agent_active` (`agent_matricule`,`Etat`,`date_fin`),
  KEY `idx_aff_pf_period` (`portefeuille_id`,`date_debut`),
  CONSTRAINT `fk_aff_pf_portefeuille` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_aff_pf_agent` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents`(`matricule`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tb_affectations_portefeuilles` (`portefeuille_id`,`agent_matricule`,`date_debut`,`date_fin`,`Etat`,`motif`,`effectue_par_user_id`,`created_at`,`updated_at`)
SELECT `id`,`agent_matricule`,CURDATE(),NULL,'ACTIF','Initialisation depuis tb_portefeuilles_agents',NULL,@now,@now
FROM `tb_portefeuilles_agents` WHERE `agent_matricule` IS NOT NULL;

-- ===================== 2026_03_21_000025_make_zone_and_portefeuille_agents_nullable =====================
ALTER TABLE `tb_zones` MODIFY `agent_commercial_matricule` VARCHAR(50) NULL;
ALTER TABLE `tb_portefeuilles_agents` MODIFY `agent_matricule` VARCHAR(50) NULL;

-- ===================== 2026_03_21_000026_drop_zone_portefeuille_historiques =====================
DROP TABLE IF EXISTS `tb_zone_historiques`;
DROP TABLE IF EXISTS `tb_portefeuille_historiques`;

-- ===================== 2026_03_24_180000_add_agent_analyse_to_credit_demandes =====================
ALTER TABLE `tb_credit_demandes`
  ADD COLUMN `agent_analyse_matricule` VARCHAR(50) NULL AFTER `agent_createur_matricule`,
  ADD KEY `idx_credit_demande_agent_analyse` (`agent_analyse_matricule`);

-- ===================== 2026_04_28_000001_add_signature_columns_to_credit_validations =====================
ALTER TABLE `tb_credit_validations`
  ADD COLUMN `signature_agent` VARCHAR(50) NULL COMMENT 'Matricule de l''agent signataire (auto)' AFTER `valide_le`,
  ADD COLUMN `nom_signataire` VARCHAR(150) NULL COMMENT 'Nom complet du signataire au moment de la validation' AFTER `signature_agent`,
  ADD COLUMN `ip_validation` VARCHAR(45) NULL COMMENT 'Adresse IP depuis laquelle la validation a été soumise' AFTER `nom_signataire`;

-- ===================== 2026_04_28_000001_create_notifications_table =====================
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` CHAR(36) NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `notifiable_type` VARCHAR(255) NOT NULL,
  `notifiable_id` BIGINT UNSIGNED NOT NULL,
  `data` TEXT NOT NULL,
  `read_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================== 2026_04_28_000002_add_service_provenance_to_credit_demandes =====================
ALTER TABLE `tb_credit_demandes`
  ADD COLUMN `service_provenance` VARCHAR(100) NULL COMMENT 'Service ou département ayant référé le client' AFTER `garantie_description`,
  ADD COLUMN `referent_nom` VARCHAR(120) NULL COMMENT 'Nom du référent dans le service' AFTER `service_provenance`;

-- ===================== 2026_04_28_000003_reorder_credit_validation_workflow =====================
UPDATE `tb_credit_validations` SET `ordre_etape` = CASE
  WHEN `type_validateur`='AGENT_CREDIT' THEN 1
  WHEN `type_validateur`='CONTROLEUR' THEN 2
  WHEN `type_validateur`='CHARGE_OPERATIONS' THEN 3
  WHEN `type_validateur`='GERANT' THEN 4
  ELSE `ordre_etape` END;

UPDATE `tb_credit_validations` v
  JOIN `tb_credit_demandes` d ON d.`id` = v.`credit_demande_id`
  LEFT JOIN `tb_credit_validations` va ON va.`credit_demande_id` = v.`credit_demande_id` AND va.`type_validateur`='AGENT_CREDIT'
  LEFT JOIN `tb_credit_validations` vc ON vc.`credit_demande_id` = v.`credit_demande_id` AND vc.`type_validateur`='CONTROLEUR'
  LEFT JOIN `tb_credit_validations` vo ON vo.`credit_demande_id` = v.`credit_demande_id` AND vo.`type_validateur`='CHARGE_OPERATIONS'
  SET v.`etape_precedente_ok` = CASE
    WHEN v.`type_validateur`='AGENT_CREDIT' THEN 1
    WHEN v.`type_validateur`='CONTROLEUR' THEN IF(va.`decision` <> 'EN_ATTENTE', 1, 0)
    WHEN v.`type_validateur`='CHARGE_OPERATIONS' THEN IF(vc.`decision` <> 'EN_ATTENTE', 1, 0)
    WHEN v.`type_validateur`='GERANT' THEN IF(vo.`decision` <> 'EN_ATTENTE', 1, 0)
    ELSE v.`etape_precedente_ok` END
  WHERE d.`statut_global`='EN_VALIDATION';

-- ===================== 2026_04_28_000004_persist_credit_actor_and_deblocage_rules =====================
UPDATE `tb_permissions` SET `nom`='Valider bloc Charge operations',`description`='Validation niveau 3 - Charge des operations',`updated_at`=@now WHERE `code`='EBEN-PER61';
UPDATE `tb_permissions` SET `nom`='Valider bloc Controleur',`description`='Validation niveau 2 - Controleur interne',`updated_at`=@now WHERE `code`='EBEN-PER62';
DELETE FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER64' AND `role_code` NOT IN ('EBEN-ROL1','EBEN-ROL3','EBEN-ROL12');
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL3','EBEN-PER64',@now,@now),('EBEN-ROL12','EBEN-PER64',@now,@now),('EBEN-ROL6','EBEN-PER58',@now,@now);
-- Acteur test agent credit (mot de passe : CreditTest@2026)
INSERT INTO `tb_agents` (`matricule`,`nom`,`postnom`,`prenom`,`sexe`,`email`,`date_embauche`,`statut`,`created_at`,`updated_at`) VALUES
 ('AG-CRD-TST-0006','TEST','CREDIT','AGENT_CREDIT_1','M','credit.agent.credit1@test.local',CURDATE(),'actif',@now,@now)
ON DUPLICATE KEY UPDATE `nom`=VALUES(`nom`),`postnom`=VALUES(`postnom`),`prenom`=VALUES(`prenom`),`sexe`=VALUES(`sexe`),`email`=VALUES(`email`),`date_embauche`=VALUES(`date_embauche`),`statut`=VALUES(`statut`),`updated_at`=VALUES(`updated_at`);
INSERT INTO `users` (`agent_matricule`,`name`,`email`,`email_verified_at`,`password`,`etat`,`created_at`,`updated_at`) VALUES
 ('AG-CRD-TST-0006','credit_agent_credit1','credit.agent.credit1@test.local',@now,'$2y$10$hLX7FuQsGS1.aO8hAE6SmOZ.QoKJgwmlyY4UWMU6suoZYpOWzT7UW','actif',@now,@now)
ON DUPLICATE KEY UPDATE `agent_matricule`=VALUES(`agent_matricule`),`name`=VALUES(`name`),`email_verified_at`=VALUES(`email_verified_at`),`password`=VALUES(`password`),`etat`=VALUES(`etat`),`updated_at`=VALUES(`updated_at`);
DELETE FROM `tb_role_user` WHERE `user_id` = (SELECT `id` FROM `users` WHERE `email`='credit.agent.credit1@test.local');
INSERT IGNORE INTO `tb_role_user` (`user_id`,`role_code`,`created_at`,`updated_at`)
SELECT `id`,'EBEN-ROL6',@now,@now FROM `users` WHERE `email`='credit.agent.credit1@test.local';

-- ===================== 2026_04_28_000005_persist_full_credit_matrix_and_test_actors =====================
UPDATE `tb_permissions` SET `nom`='Valider bloc Charge operations',`description`='Validation niveau 3 - Charge des operations',`updated_at`=@now WHERE `code`='EBEN-PER61';
UPDATE `tb_permissions` SET `nom`='Valider bloc Controleur',`description`='Validation niveau 2 - Controleur interne',`updated_at`=@now WHERE `code`='EBEN-PER62';
SET @creditPerms = "EBEN-PER53,EBEN-PER54,EBEN-PER55,EBEN-PER56,EBEN-PER57,EBEN-PER58,EBEN-PER59,EBEN-PER60,EBEN-PER61,EBEN-PER62,EBEN-PER63,EBEN-PER64,EBEN-PER65,EBEN-PER66,EBEN-PER67,EBEN-PER68,EBEN-PER69,EBEN-PER70,EBEN-PER71,EBEN-PER72";
DELETE FROM `tb_role_permission` WHERE FIND_IN_SET(`permission_code`, @creditPerms) AND `role_code` NOT IN ('EBEN-ROL9','EBEN-ROL6','EBEN-ROL11','EBEN-ROL14','EBEN-ROL12','EBEN-ROL3','EBEN-ROL8','EBEN-ROL1');
DELETE FROM `tb_role_permission` WHERE `role_code`='EBEN-ROL1' AND FIND_IN_SET(`permission_code`, @creditPerms);
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`)
SELECT 'EBEN-ROL1', `code`, @now, @now FROM `tb_permissions` WHERE FIND_IN_SET(`code`, @creditPerms);
-- Matrice exacte par role
DELETE FROM `tb_role_permission` WHERE `role_code`='EBEN-ROL9'  AND FIND_IN_SET(`permission_code`, @creditPerms);
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL9','EBEN-PER53',@now,@now),('EBEN-ROL9','EBEN-PER54',@now,@now),('EBEN-ROL9','EBEN-PER55',@now,@now),('EBEN-ROL9','EBEN-PER56',@now,@now),('EBEN-ROL9','EBEN-PER57',@now,@now);
DELETE FROM `tb_role_permission` WHERE `role_code`='EBEN-ROL6'  AND FIND_IN_SET(`permission_code`, @creditPerms);
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL6','EBEN-PER53',@now,@now),('EBEN-ROL6','EBEN-PER54',@now,@now),('EBEN-ROL6','EBEN-PER55',@now,@now),('EBEN-ROL6','EBEN-PER56',@now,@now),('EBEN-ROL6','EBEN-PER57',@now,@now),('EBEN-ROL6','EBEN-PER58',@now,@now),('EBEN-ROL6','EBEN-PER59',@now,@now),('EBEN-ROL6','EBEN-PER60',@now,@now),('EBEN-ROL6','EBEN-PER70',@now,@now),('EBEN-ROL6','EBEN-PER71',@now,@now),('EBEN-ROL6','EBEN-PER72',@now,@now);
DELETE FROM `tb_role_permission` WHERE `role_code`='EBEN-ROL11' AND FIND_IN_SET(`permission_code`, @creditPerms);
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL11','EBEN-PER53',@now,@now),('EBEN-ROL11','EBEN-PER57',@now,@now),('EBEN-ROL11','EBEN-PER61',@now,@now),('EBEN-ROL11','EBEN-PER70',@now,@now),('EBEN-ROL11','EBEN-PER72',@now,@now);
DELETE FROM `tb_role_permission` WHERE `role_code`='EBEN-ROL14' AND FIND_IN_SET(`permission_code`, @creditPerms);
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL14','EBEN-PER53',@now,@now),('EBEN-ROL14','EBEN-PER57',@now,@now),('EBEN-ROL14','EBEN-PER62',@now,@now),('EBEN-ROL14','EBEN-PER70',@now,@now),('EBEN-ROL14','EBEN-PER72',@now,@now);
DELETE FROM `tb_role_permission` WHERE `role_code`='EBEN-ROL12' AND FIND_IN_SET(`permission_code`, @creditPerms);
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL12','EBEN-PER53',@now,@now),('EBEN-ROL12','EBEN-PER57',@now,@now),('EBEN-ROL12','EBEN-PER63',@now,@now),('EBEN-ROL12','EBEN-PER64',@now,@now),('EBEN-ROL12','EBEN-PER70',@now,@now),('EBEN-ROL12','EBEN-PER71',@now,@now),('EBEN-ROL12','EBEN-PER72',@now,@now);
DELETE FROM `tb_role_permission` WHERE `role_code`='EBEN-ROL3'  AND FIND_IN_SET(`permission_code`, @creditPerms);
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL3','EBEN-PER53',@now,@now),('EBEN-ROL3','EBEN-PER57',@now,@now),('EBEN-ROL3','EBEN-PER63',@now,@now),('EBEN-ROL3','EBEN-PER64',@now,@now),('EBEN-ROL3','EBEN-PER70',@now,@now),('EBEN-ROL3','EBEN-PER71',@now,@now),('EBEN-ROL3','EBEN-PER72',@now,@now);
DELETE FROM `tb_role_permission` WHERE `role_code`='EBEN-ROL8'  AND FIND_IN_SET(`permission_code`, @creditPerms);
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL8','EBEN-PER53',@now,@now),('EBEN-ROL8','EBEN-PER54',@now,@now),('EBEN-ROL8','EBEN-PER55',@now,@now),('EBEN-ROL8','EBEN-PER56',@now,@now),('EBEN-ROL8','EBEN-PER57',@now,@now);
DELETE FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER64' AND `role_code` NOT IN ('EBEN-ROL1','EBEN-ROL3','EBEN-ROL12');
-- Acteurs test (mot de passe : CreditTest@2026)
INSERT INTO `tb_agents` (`matricule`,`nom`,`postnom`,`prenom`,`sexe`,`email`,`date_embauche`,`statut`,`created_at`,`updated_at`) VALUES
 ('AG-CRD-TST-0101','TEST','CREDIT','DEMANDEUR_1','M','credit.demandeur1@test.local',CURDATE(),'actif',@now,@now),
 ('AG-CRD-TST-0102','TEST','CREDIT','CHARGE_OPERATIONS_1','M','credit.charge.operations1@test.local',CURDATE(),'actif',@now,@now),
 ('AG-CRD-TST-0103','TEST','CREDIT','AGENT_CREDIT_1','M','credit.agent.credit1@test.local',CURDATE(),'actif',@now,@now),
 ('AG-CRD-TST-0104','TEST','CREDIT','CONTROLEUR_1','M','credit.controleur1@test.local',CURDATE(),'actif',@now,@now),
 ('AG-CRD-TST-0105','TEST','CREDIT','GERANT_1','M','credit.gerant1@test.local',CURDATE(),'actif',@now,@now),
 ('AG-CRD-TST-0106','TEST','CREDIT','DIRECTEUR_NATIONAL_1','M','credit.directeur.national1@test.local',CURDATE(),'actif',@now,@now)
ON DUPLICATE KEY UPDATE `nom`=VALUES(`nom`),`postnom`=VALUES(`postnom`),`prenom`=VALUES(`prenom`),`sexe`=VALUES(`sexe`),`email`=VALUES(`email`),`date_embauche`=VALUES(`date_embauche`),`statut`=VALUES(`statut`),`updated_at`=VALUES(`updated_at`);
INSERT INTO `users` (`agent_matricule`,`name`,`email`,`email_verified_at`,`password`,`etat`,`created_at`,`updated_at`) VALUES
 ('AG-CRD-TST-0101','credit_demandeur1','credit.demandeur1@test.local',@now,'$2y$10$hLX7FuQsGS1.aO8hAE6SmOZ.QoKJgwmlyY4UWMU6suoZYpOWzT7UW','actif',@now,@now),
 ('AG-CRD-TST-0102','credit_charge_operations1','credit.charge.operations1@test.local',@now,'$2y$10$hLX7FuQsGS1.aO8hAE6SmOZ.QoKJgwmlyY4UWMU6suoZYpOWzT7UW','actif',@now,@now),
 ('AG-CRD-TST-0103','credit_agent_credit1','credit.agent.credit1@test.local',@now,'$2y$10$hLX7FuQsGS1.aO8hAE6SmOZ.QoKJgwmlyY4UWMU6suoZYpOWzT7UW','actif',@now,@now),
 ('AG-CRD-TST-0104','credit_controleur1','credit.controleur1@test.local',@now,'$2y$10$hLX7FuQsGS1.aO8hAE6SmOZ.QoKJgwmlyY4UWMU6suoZYpOWzT7UW','actif',@now,@now),
 ('AG-CRD-TST-0105','credit_gerant1','credit.gerant1@test.local',@now,'$2y$10$hLX7FuQsGS1.aO8hAE6SmOZ.QoKJgwmlyY4UWMU6suoZYpOWzT7UW','actif',@now,@now),
 ('AG-CRD-TST-0106','credit_directeur_national1','credit.directeur.national1@test.local',@now,'$2y$10$hLX7FuQsGS1.aO8hAE6SmOZ.QoKJgwmlyY4UWMU6suoZYpOWzT7UW','actif',@now,@now)
ON DUPLICATE KEY UPDATE `agent_matricule`=VALUES(`agent_matricule`),`name`=VALUES(`name`),`email_verified_at`=VALUES(`email_verified_at`),`password`=VALUES(`password`),`etat`=VALUES(`etat`),`updated_at`=VALUES(`updated_at`);
DELETE FROM `tb_role_user` WHERE `user_id` IN (SELECT `id` FROM `users` WHERE `email` IN ('credit.demandeur1@test.local','credit.charge.operations1@test.local','credit.agent.credit1@test.local','credit.controleur1@test.local','credit.gerant1@test.local','credit.directeur.national1@test.local'));
INSERT IGNORE INTO `tb_role_user` (`user_id`,`role_code`,`created_at`,`updated_at`) SELECT `id`,'EBEN-ROL9',@now,@now FROM `users` WHERE `email`='credit.demandeur1@test.local';
INSERT IGNORE INTO `tb_role_user` (`user_id`,`role_code`,`created_at`,`updated_at`) SELECT `id`,'EBEN-ROL11',@now,@now FROM `users` WHERE `email`='credit.charge.operations1@test.local';
INSERT IGNORE INTO `tb_role_user` (`user_id`,`role_code`,`created_at`,`updated_at`) SELECT `id`,'EBEN-ROL6',@now,@now FROM `users` WHERE `email`='credit.agent.credit1@test.local';
INSERT IGNORE INTO `tb_role_user` (`user_id`,`role_code`,`created_at`,`updated_at`) SELECT `id`,'EBEN-ROL14',@now,@now FROM `users` WHERE `email`='credit.controleur1@test.local';
INSERT IGNORE INTO `tb_role_user` (`user_id`,`role_code`,`created_at`,`updated_at`) SELECT `id`,'EBEN-ROL12',@now,@now FROM `users` WHERE `email`='credit.gerant1@test.local';
INSERT IGNORE INTO `tb_role_user` (`user_id`,`role_code`,`created_at`,`updated_at`) SELECT `id`,'EBEN-ROL3',@now,@now FROM `users` WHERE `email`='credit.directeur.national1@test.local';

-- ===================== 2026_04_28_000006_add_duree_validee_to_credit_validations =====================
ALTER TABLE `tb_credit_validations`
  ADD COLUMN `duree_mois_validee` TINYINT UNSIGNED NULL AFTER `montant_valide`;
UPDATE `tb_credit_validations` v
  JOIN `tb_credit_demandes` d ON d.`id` = v.`credit_demande_id`
  SET v.`duree_mois_validee` = d.`duree_mois`
  WHERE v.`decision` IN ('APPROUVE','APPROUVE_AVEC_RESERVE') AND v.`duree_mois_validee` IS NULL;

-- ===================== 2026_04_29_000001_add_caution_fields_to_credit_deblocages =====================
ALTER TABLE `tb_credit_deblocages`
  ADD COLUMN `montant_valide_gerant` DECIMAL(15,2) NULL AFTER `montant_debloque`,
  ADD COLUMN `montant_caution` DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER `montant_valide_gerant`,
  ADD COLUMN `frais_etude` DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER `montant_caution`;

-- ===================== 2026_04_29_140000_fix_compte_debit_fk_in_credit_deblocages =====================
ALTER TABLE `tb_credit_deblocages` DROP FOREIGN KEY `tb_credit_deblocages_compte_debit_id_foreign`;
ALTER TABLE `tb_credit_deblocages`
  ADD COLUMN `guichet_solde_id` BIGINT UNSIGNED NULL AFTER `compte_debit_id`,
  ADD CONSTRAINT `tb_credit_deblocages_guichet_solde_id_foreign` FOREIGN KEY (`guichet_solde_id`) REFERENCES `tb_caisses_guichets_soldes`(`id`) ON DELETE SET NULL;

-- ===================== 2026_05_03_000001_round_and_sync_credit_amounts =====================
UPDATE `tb_credit_validations` SET `montant_valide` = ROUND(`montant_valide`,2) WHERE `montant_valide` IS NOT NULL;
UPDATE `tb_credit_demandes` d
 JOIN (
   SELECT v.`credit_demande_id`, ROUND(v.`montant_valide`,2) AS montant_retenu
   FROM `tb_credit_validations` v
   JOIN (
     SELECT `credit_demande_id`, MAX(`ordre_etape`) AS max_ordre
     FROM `tb_credit_validations`
     WHERE `decision` IN ('APPROUVE','APPROUVE_AVEC_RESERVE') AND `montant_valide` IS NOT NULL
     GROUP BY `credit_demande_id`
   ) x ON x.`credit_demande_id` = v.`credit_demande_id` AND x.max_ordre = v.`ordre_etape`
   WHERE v.`decision` IN ('APPROUVE','APPROUVE_AVEC_RESERVE') AND v.`montant_valide` IS NOT NULL
 ) s ON s.`credit_demande_id` = d.`id`
 SET d.`montant_approuve` = s.montant_retenu;

-- ===================== 2026_05_03_141500_expand_numero_ordre_in_credit_deblocages =====================
ALTER TABLE `tb_credit_deblocages` MODIFY COLUMN `numero_ordre` VARCHAR(80) NULL;

-- ===================== 2026_05_03_142500_grant_bordereau_permission_to_gerant_and_directeur =====================
INSERT INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`) VALUES
 ('EBEN-ROL12','EBEN-PER11',@now,@now),('EBEN-ROL3','EBEN-PER11',@now,@now)
ON DUPLICATE KEY UPDATE `created_at`=VALUES(`created_at`),`updated_at`=VALUES(`updated_at`);

-- ===================== 2026_05_04_104108_enforce_portefeuille_id_not_null_on_credit_demandes =====================
ALTER TABLE `tb_credit_demandes` DROP FOREIGN KEY `tb_credit_demandes_portefeuille_id_foreign`;
ALTER TABLE `tb_credit_demandes` MODIFY COLUMN `portefeuille_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `tb_credit_demandes`
  ADD CONSTRAINT `tb_credit_demandes_portefeuille_id_foreign` FOREIGN KEY (`portefeuille_id`) REFERENCES `tb_portefeuilles_agents`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- ===================== 2026_06_02_000000_add_caisse_remboursements_permission =====================
INSERT IGNORE INTO `tb_permissions` (`code`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 ('EBEN-PER109','Remboursements Caisse/Guichet','Consulter la liste des dossiers crédit en cours de remboursement depuis le module Caisse',@now,@now);
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`)
SELECT `role_code`,'EBEN-PER109',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER65';

-- ===================== 2026_06_02_000021_add_caisse_remboursements_permission =====================
INSERT IGNORE INTO `tb_permissions` (`code`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 ('EBEN-PER111','Remboursements crédit (Caisse)','Consulter la liste des dossiers en cours de remboursement depuis le module Caisse/Guichet',@now,@now);
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`)
SELECT `role_code`,'EBEN-PER111',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER65';

-- ===================== 2026_06_02_000022_remove_credit_remboursement_permission =====================
DELETE FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER65';
DELETE FROM `tb_permissions` WHERE `code`='EBEN-PER65';

-- ===================== 2026_06_02_000023_add_caisse_releve_credit_permission =====================
INSERT IGNORE INTO `tb_permissions` (`code`,`nom`,`description`,`created_at`,`updated_at`) VALUES
 ('EBEN-PER112','Imprimer relevé crédit','Imprimer le relevé de compte crédit (PDF) depuis le module Caisse/Guichet',@now,@now);
INSERT IGNORE INTO `tb_role_permission` (`role_code`,`permission_code`,`created_at`,`updated_at`)
SELECT `role_code`,'EBEN-PER112',@now,@now FROM `tb_role_permission` WHERE `permission_code`='EBEN-PER111';

-- ===================== 2026_06_07_142625_add_transaction_id_to_credit_remboursements_table =====================
ALTER TABLE `tb_credit_remboursements`
  ADD COLUMN `transaction_id` BIGINT UNSIGNED NULL AFTER `recu_le`,
  ADD CONSTRAINT `tb_credit_remboursements_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `tb_transactions`(`id`) ON DELETE SET NULL;

-- ===================== 2026_06_08_145711_add_prelevement_auto_to_credit_demandes =====================
ALTER TABLE `tb_credit_demandes`
  ADD COLUMN `prelevement_auto_autorise` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Autorise le prélèvement automatique même avant la date d''échéance' AFTER `statut_global`;

-- =====================================================================
-- JOURNAL DES MIGRATIONS (table `migrations`)
-- =====================================================================
INSERT IGNORE INTO `migrations` (`migration`,`batch`) VALUES
 ('2026_03_05_000001_laravel_core',1),
 ('2026_03_05_000002_rh_banque_core',1),
 ('2026_03_05_000003_caisse_guichet',1),
 ('2026_03_05_000004_add_guichet_to_affectations',1),
 ('2026_03_05_000005_seed_test_users',1),
 ('2026_03_06_000006_permissions_banque_complete',1),
 ('2026_03_07_103903_add_statut_observations_to_mouvements_inter_caisses',1),
 ('2026_03_07_105258_add_type_guichet_and_coffre_central',1),
 ('2026_03_07_145751_add_timestamps_to_tb_caisses_guichets',1),
 ('2026_03_07_210000_tresorerie_permissions_and_user',1),
 ('2026_03_07_220001_add_demande_appro_to_type_flux',1),
 ('2026_03_07_235000_add_motif_statut_to_cloture_caisse',1),
 ('2026_03_08_000001_add_en_verification_and_cloture_validation',1),
 ('2026_03_08_110000_extend_tb_transactions_for_guichet',1),
 ('2026_03_08_120000_drop_client_columns_from_tb_transactions',1),
 ('2026_03_10_000001_update_type_enum_tb_comptes',1),
 ('2026_03_10_200000_create_tb_demandes_modification',1),
 ('2026_03_13_210000_create_tb_commission_rules_and_transaction_commissions',1),
 ('2026_03_13_233000_add_accounting_snapshots_to_tb_transactions',1),
 ('2026_03_13_234000_create_tb_compta_journaux_and_ecritures',1),
 ('2026_03_13_235000_seed_ohada_accounts_and_comptabilite_permissions',1),
 ('2026_03_13_238000_extend_tb_plan_comptable_for_ohada_structure',1),
 ('2026_03_13_239000_seed_full_ohada_chart_accounts',1),
 ('2026_03_13_246000_add_unique_constraints_for_banking_integrity',1),
 ('2026_03_16_000011_credit_module_tables',1),
 ('2026_03_16_000012_credit_permissions',1),
 ('2026_03_18_000013_extend_permissions_crud_tresorerie_agents_terrain',1),
 ('2026_03_18_000014_add_module_specific_crud_permissions',1),
 ('2026_03_18_000015_remove_global_crud_permissions',1),
 ('2026_03_18_000016_remove_duplicate_module_permissions',1),
 ('2026_03_18_000017_normalize_permission_action_aliases',1),
 ('2026_03_18_000018_refine_permission_action_wording_by_context',1),
 ('2026_03_18_000019_split_modify_delete_permissions_by_module',1),
 ('2026_03_18_000020_add_caisse_journal_report_view_permission',1),
 ('2026_03_19_000013_credit_legacy_permissions_cleanup',1),
 ('2026_03_19_000014_make_credit_demande_account_fields_nullable',1),
 ('2026_03_21_000022_create_tb_affectations_zones',1),
 ('2026_03_21_000023_create_tb_affectations_portefeuilles',1),
 ('2026_03_21_000025_make_zone_and_portefeuille_agents_nullable',1),
 ('2026_03_21_000026_drop_zone_portefeuille_historiques',1),
 ('2026_03_24_180000_add_agent_analyse_to_credit_demandes',1),
 ('2026_04_28_000001_add_signature_columns_to_credit_validations',1),
 ('2026_04_28_000001_create_notifications_table',1),
 ('2026_04_28_000002_add_service_provenance_to_credit_demandes',1),
 ('2026_04_28_000003_reorder_credit_validation_workflow',1),
 ('2026_04_28_000004_persist_credit_actor_and_deblocage_rules',1),
 ('2026_04_28_000005_persist_full_credit_matrix_and_test_actors',1),
 ('2026_04_28_000006_add_duree_validee_to_credit_validations',1),
 ('2026_04_29_000001_add_caution_fields_to_credit_deblocages',1),
 ('2026_04_29_140000_fix_compte_debit_fk_in_credit_deblocages',1),
 ('2026_05_03_000001_round_and_sync_credit_amounts',1),
 ('2026_05_03_141500_expand_numero_ordre_in_credit_deblocages',1),
 ('2026_05_03_142500_grant_bordereau_permission_to_gerant_and_directeur',1),
 ('2026_05_04_104108_enforce_portefeuille_id_not_null_on_credit_demandes',1),
 ('2026_06_02_000000_add_caisse_remboursements_permission',1),
 ('2026_06_02_000021_add_caisse_remboursements_permission',1),
 ('2026_06_02_000022_remove_credit_remboursement_permission',1),
 ('2026_06_02_000023_add_caisse_releve_credit_permission',1),
 ('2026_06_07_142625_add_transaction_id_to_credit_remboursements_table',1),
 ('2026_06_08_145711_add_prelevement_auto_to_credit_demandes',1);

SET FOREIGN_KEY_CHECKS = 1;
-- FIN DU SCRIPT











