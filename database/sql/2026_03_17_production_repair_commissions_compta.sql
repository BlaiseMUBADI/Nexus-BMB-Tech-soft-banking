-- =====================================================================
-- PRODUCTION REPAIR SCRIPT - COMMISSIONS / COMPTABILITE / REFERENCES
-- Purpose:
--   1. Repair tb_devises and tb_plan_comptable on legacy production DBs
--   2. Seed missing OHADA chart accounts used by caisse operations
--   3. Add missing indexes before foreign keys to avoid MySQL #1822
--   4. Align commissions and accounting tables with local structure
--
-- Safe usage:
--   - Run on the target production database after a backup.
--   - Prefer running in phpMyAdmin SQL tab or mysql client.
-- =====================================================================

SET NAMES utf8mb4;
SET @db := DATABASE();
SET FOREIGN_KEY_CHECKS = 0;

SELECT CONCAT('Running repair script on DB: ', @db) AS info;

-- =====================================================================
-- A. tb_devises
-- =====================================================================

CREATE TABLE IF NOT EXISTS tb_devises (
  code_iso VARCHAR(3) NOT NULL,
  nom VARCHAR(50) NOT NULL,
  symbole VARCHAR(5) NOT NULL,
  est_reference TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (code_iso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE tb_devises ENGINE=InnoDB;
ALTER TABLE tb_devises MODIFY code_iso VARCHAR(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

SET @dup_devises := (
  SELECT COUNT(*) FROM (
    SELECT code_iso
    FROM tb_devises
    WHERE code_iso IS NOT NULL AND code_iso <> ''
    GROUP BY code_iso
    HAVING COUNT(*) > 1
  ) x
);

SET @pk_devises := (
  SELECT COUNT(*)
  FROM information_schema.table_constraints tc
  JOIN information_schema.key_column_usage kcu
    ON tc.constraint_schema = kcu.constraint_schema
   AND tc.table_name = kcu.table_name
   AND tc.constraint_name = kcu.constraint_name
  WHERE tc.constraint_schema = @db
    AND tc.table_name = 'tb_devises'
    AND tc.constraint_type = 'PRIMARY KEY'
    AND kcu.column_name = 'code_iso'
);

SET @uq_devises := (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = @db
    AND table_name = 'tb_devises'
    AND column_name = 'code_iso'
    AND seq_in_index = 1
    AND non_unique = 0
);

SET @sql := IF(
  @dup_devises = 0 AND @pk_devises = 0 AND @uq_devises = 0,
  'ALTER TABLE tb_devises ADD UNIQUE INDEX uq_tb_devises_code_iso (code_iso)',
  'SELECT "tb_devises.code_iso already protected or duplicate values detected" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_devises_any := (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = @db
    AND table_name = 'tb_devises'
    AND column_name = 'code_iso'
    AND seq_in_index = 1
);

SET @sql := IF(
  @idx_devises_any = 0,
  'ALTER TABLE tb_devises ADD INDEX idx_tb_devises_code_iso_fk (code_iso)',
  'SELECT "tb_devises.code_iso has at least one usable index" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

INSERT INTO tb_devises (code_iso, nom, symbole, est_reference, created_at, updated_at)
VALUES
('CDF', 'Franc Congolais', 'Fc', 1, NOW(), NULL),
('USD', 'Dollar Americain', '$', 0, NOW(), NULL),
('EUR', 'Euro', 'EUR', 0, NOW(), NULL)
ON DUPLICATE KEY UPDATE
  nom = VALUES(nom),
  symbole = VALUES(symbole),
  est_reference = VALUES(est_reference);

-- =====================================================================
-- A2. PRE-REQUIS FK (tables referencees)
-- Ensure referenced columns are indexed to avoid MySQL #1822.
-- =====================================================================

SET @sql := (
  SELECT IF(
    EXISTS (
      SELECT 1 FROM information_schema.tables
      WHERE table_schema = @db AND table_name = 'tb_agents' AND ENGINE <> 'InnoDB'
    ),
    'ALTER TABLE tb_agents ENGINE=InnoDB',
    'SELECT "Engine tb_agents OK (InnoDB ou table absente)" AS info'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    EXISTS (
      SELECT 1 FROM information_schema.columns
      WHERE table_schema = @db AND table_name = 'tb_agents' AND column_name = 'matricule'
    ),
    'ALTER TABLE tb_agents MODIFY matricule VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL',
    'SELECT "Colonne tb_agents.matricule absente" AS info'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    EXISTS (
      SELECT 1 FROM information_schema.columns
      WHERE table_schema = @db AND table_name = 'tb_agents' AND column_name = 'matricule'
    )
    AND NOT EXISTS (
      SELECT 1 FROM information_schema.statistics
      WHERE table_schema = @db AND table_name = 'tb_agents' AND column_name = 'matricule' AND seq_in_index = 1
        AND (sub_part IS NULL OR sub_part = 0)
    ),
    'ALTER TABLE tb_agents ADD INDEX idx_tb_agents_matricule_fk_full (matricule)',
    'SELECT "Index sur tb_agents.matricule deja present (ou table/colonne absente)" AS info'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    EXISTS (
      SELECT 1 FROM information_schema.tables
      WHERE table_schema = @db AND table_name = 'tb_zones' AND ENGINE <> 'InnoDB'
    ),
    'ALTER TABLE tb_zones ENGINE=InnoDB',
    'SELECT "Engine tb_zones OK (InnoDB ou table absente)" AS info'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    EXISTS (
      SELECT 1 FROM information_schema.columns
      WHERE table_schema = @db AND table_name = 'tb_zones' AND column_name = 'code_zone'
    )
    AND NOT EXISTS (
      SELECT 1 FROM information_schema.statistics
      WHERE table_schema = @db AND table_name = 'tb_zones' AND column_name = 'code_zone' AND seq_in_index = 1
    ),
    'ALTER TABLE tb_zones ADD INDEX idx_tb_zones_code_zone (code_zone)',
    'SELECT "Index sur tb_zones.code_zone deja present (ou table/colonne absente)" AS info'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    EXISTS (
      SELECT 1 FROM information_schema.tables
      WHERE table_schema = @db AND table_name = 'tb_portefeuilles_agents' AND ENGINE <> 'InnoDB'
    ),
    'ALTER TABLE tb_portefeuilles_agents ENGINE=InnoDB',
    'SELECT "Engine tb_portefeuilles_agents OK (InnoDB ou table absente)" AS info'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    EXISTS (
      SELECT 1 FROM information_schema.columns
      WHERE table_schema = @db AND table_name = 'tb_portefeuilles_agents' AND column_name = 'id'
    )
    AND NOT EXISTS (
      SELECT 1 FROM information_schema.statistics
      WHERE table_schema = @db AND table_name = 'tb_portefeuilles_agents' AND column_name = 'id' AND seq_in_index = 1
    ),
    'ALTER TABLE tb_portefeuilles_agents ADD INDEX idx_tb_portefeuilles_agents_id (id)',
    'SELECT "Index sur tb_portefeuilles_agents.id deja present (ou table/colonne absente)" AS info'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    EXISTS (
      SELECT 1 FROM information_schema.tables
      WHERE table_schema = @db AND table_name = 'tb_caisses_guichets' AND ENGINE <> 'InnoDB'
    ),
    'ALTER TABLE tb_caisses_guichets ENGINE=InnoDB',
    'SELECT "Engine tb_caisses_guichets OK (InnoDB ou table absente)" AS info'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    EXISTS (
      SELECT 1 FROM information_schema.columns
      WHERE table_schema = @db AND table_name = 'tb_caisses_guichets' AND column_name = 'id'
    )
    AND NOT EXISTS (
      SELECT 1 FROM information_schema.statistics
      WHERE table_schema = @db AND table_name = 'tb_caisses_guichets' AND column_name = 'id' AND seq_in_index = 1
    ),
    'ALTER TABLE tb_caisses_guichets ADD INDEX idx_tb_caisses_guichets_id (id)',
    'SELECT "Index sur tb_caisses_guichets.id deja present (ou table/colonne absente)" AS info'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    EXISTS (
      SELECT 1 FROM information_schema.tables
      WHERE table_schema = @db AND table_name = 'tb_transactions' AND ENGINE <> 'InnoDB'
    ),
    'ALTER TABLE tb_transactions ENGINE=InnoDB',
    'SELECT "Engine tb_transactions OK (InnoDB ou table absente)" AS info'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    EXISTS (
      SELECT 1 FROM information_schema.columns
      WHERE table_schema = @db AND table_name = 'tb_transactions' AND column_name = 'id'
    )
    AND NOT EXISTS (
      SELECT 1 FROM information_schema.statistics
      WHERE table_schema = @db AND table_name = 'tb_transactions' AND column_name = 'id' AND seq_in_index = 1
    ),
    'ALTER TABLE tb_transactions ADD INDEX idx_tb_transactions_id (id)',
    'SELECT "Index sur tb_transactions.id deja present (ou table/colonne absente)" AS info'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================================
-- B. tb_plan_comptable
-- =====================================================================

CREATE TABLE IF NOT EXISTS tb_plan_comptable (
  numero_compte VARCHAR(20) NOT NULL,
  classe_ohada CHAR(1) NULL,
  libelle VARCHAR(191) NOT NULL,
  parent_compte VARCHAR(20) NULL,
  niveau TINYINT UNSIGNED NOT NULL DEFAULT 1,
  type_compte ENUM('ACTIF','PASSIF','CHARGE','PRODUIT','MIXTE','HORS_BILAN') NOT NULL,
  est_mouvementable TINYINT(1) NOT NULL DEFAULT 1,
  est_actif TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (numero_compte)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE tb_plan_comptable ENGINE=InnoDB;
ALTER TABLE tb_plan_comptable MODIFY numero_compte VARCHAR(20) NOT NULL;
ALTER TABLE tb_plan_comptable MODIFY libelle VARCHAR(191) NOT NULL;
ALTER TABLE tb_plan_comptable MODIFY type_compte ENUM('ACTIF','PASSIF','CHARGE','PRODUIT','MIXTE','HORS_BILAN') NOT NULL;

ALTER TABLE tb_plan_comptable
  ADD COLUMN IF NOT EXISTS classe_ohada CHAR(1) NULL AFTER numero_compte,
  ADD COLUMN IF NOT EXISTS parent_compte VARCHAR(20) NULL AFTER libelle,
  ADD COLUMN IF NOT EXISTS niveau TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER parent_compte,
  ADD COLUMN IF NOT EXISTS est_mouvementable TINYINT(1) NOT NULL DEFAULT 1 AFTER type_compte,
  ADD COLUMN IF NOT EXISTS est_actif TINYINT(1) NOT NULL DEFAULT 1 AFTER est_mouvementable;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'CREATE INDEX idx_plan_ohada_classe_num ON tb_plan_comptable (classe_ohada, numero_compte)',
    'SELECT "Index idx_plan_ohada_classe_num already exists"')
  FROM information_schema.statistics
  WHERE table_schema=@db AND table_name='tb_plan_comptable' AND index_name='idx_plan_ohada_classe_num'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'CREATE INDEX idx_plan_ohada_parent ON tb_plan_comptable (parent_compte)',
    'SELECT "Index idx_plan_ohada_parent already exists"')
  FROM information_schema.statistics
  WHERE table_schema=@db AND table_name='tb_plan_comptable' AND index_name='idx_plan_ohada_parent'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'CREATE INDEX idx_plan_ohada_actif ON tb_plan_comptable (est_actif)',
    'SELECT "Index idx_plan_ohada_actif already exists"')
  FROM information_schema.statistics
  WHERE table_schema=@db AND table_name='tb_plan_comptable' AND index_name='idx_plan_ohada_actif'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

DROP TEMPORARY TABLE IF EXISTS tmp_ohada_accounts;
CREATE TEMPORARY TABLE tmp_ohada_accounts (
  numero VARCHAR(20) PRIMARY KEY,
  libelle VARCHAR(255) NOT NULL,
  parent_compte VARCHAR(20) NULL,
  est_mouvementable TINYINT(1) NOT NULL,
  type_override VARCHAR(20) NULL
) ENGINE=InnoDB;

INSERT INTO tmp_ohada_accounts (numero, libelle, parent_compte, est_mouvementable, type_override) VALUES
('1','Comptes de capitaux',NULL,0,NULL),('10','Capital','1',0,NULL),('101','Capital social','10',0,NULL),('1011','Capital souscrit appele verse','101',1,NULL),('102','Capital souscrit non appele','10',1,NULL),('103','Capital souscrit appele non verse','10',1,NULL),('104','Primes liees au capital','10',1,NULL),('105','Ecarts de reevaluation','10',1,NULL),('106','Reserves','10',1,NULL),('107','Report a nouveau','10',1,NULL),('108','Resultat net en instance d affectation','10',1,NULL),('109','Actionnaires capital souscrit non appele','10',1,NULL),('11','Emprunts et dettes assimilees','1',0,NULL),('12','Dettes de location acquisition','1',0,NULL),('13','Provisions pour risques et charges','1',0,NULL),('14','Dettes financieres diverses','1',0,NULL),('15','Dettes rattachees a des participations','1',0,NULL),('16','Fonds affectes et subventions d investissement','1',0,NULL),('17','Autres fonds propres','1',0,NULL),('18','Comptes de liaison des etablissements','1',0,NULL),('19','Provisions financieres pour risques et charges','1',0,NULL),
('2','Comptes d immobilisations',NULL,0,NULL),('20','Charges immobilisees','2',0,NULL),('21','Immobilisations incorporelles','2',0,NULL),('22','Terrains','2',0,NULL),('23','Batiments installations techniques et agencements','2',0,NULL),('24','Materiel mobilier et actifs biologiques','2',0,NULL),('25','Avances et acomptes verses sur immobilisations','2',0,NULL),('251','Avances et acomptes sur immobilisations corporelles','25',0,NULL),('2511','Depots a vue clients','251',1,'PASSIF'),('2512','Depots a terme clients','251',1,'PASSIF'),('26','Titres de participation et autres immobilisations financieres','2',0,NULL),('27','Ecarts de conversion actif','2',0,NULL),('28','Amortissements','2',0,NULL),('29','Depreciations des immobilisations','2',0,NULL),
('3','Comptes de stocks',NULL,0,NULL),('31','Marchandises','3',0,NULL),('32','Matieres premieres et fournitures liees','3',0,NULL),('33','Autres approvisionnements','3',0,NULL),('34','Produits en cours','3',0,NULL),('35','Services en cours','3',0,NULL),('36','Produits finis','3',0,NULL),('37','Produits intermediaires et residuels','3',0,NULL),('38','Stocks en cours de route et en consignation','3',0,NULL),('39','Depreciations des stocks','3',0,NULL),
('4','Comptes de tiers',NULL,0,NULL),('40','Fournisseurs et comptes rattaches','4',0,NULL),('41','Clients et comptes rattaches','4',0,NULL),('411','Clients ordinaires','41',0,NULL),('4111','Comptes courants clients','411',1,'PASSIF'),('4112','Comptes epargne clients','411',1,'PASSIF'),('412','Clients effets a recevoir','41',1,NULL),('42','Personnel','4',0,NULL),('43','Organismes sociaux','4',0,NULL),('44','Etat et collectivites publiques','4',0,NULL),('45','Organismes internationaux','4',0,NULL),('46','Associes et groupe','4',0,NULL),('47','Debiteurs et crediteurs divers','4',0,NULL),('471','Comptes d attente','47',0,NULL),('4711','Compte transitoire operations de change','471',1,'PASSIF'),('48','Comptes de regularisation','4',0,NULL),('49','Depreciations et provisions des comptes de tiers','4',0,NULL),
('5','Comptes de tresorerie',NULL,0,NULL),('50','Titres de placement','5',0,NULL),('51','Valeurs a encaisser','5',0,NULL),('52','Banques etablissements financiers et assimiles','5',0,NULL),('521','Banques locales','52',0,NULL),('5211','Banque locale CDF','521',1,NULL),('5212','Banque locale USD','521',1,NULL),('53','Etablissements financiers et instruments monetaires','5',0,NULL),('54','Instruments de tresorerie','5',0,NULL),('55','Monnaie electronique','5',0,NULL),('56','Banques crediteurs','5',0,NULL),('57','Caisse','5',0,NULL),('570','Caisse principale','57',0,NULL),('5701','Caisse CDF','570',1,NULL),('5702','Caisse USD','570',1,NULL),('5703','Caisse EUR','570',1,NULL),('58','Virements internes','5',0,NULL),('581','Virements internes en cours','58',0,NULL),('5811','Virements internes en cours CDF','581',1,NULL),('59','Depreciations des comptes financiers','5',0,NULL),
('6','Comptes de charges des activites ordinaires',NULL,0,NULL),('60','Achats et variation de stocks','6',0,NULL),('600','Achats','60',0,NULL),('6001','Frais bancaires','600',1,NULL),('61','Transports','6',0,NULL),('62','Services exterieurs A','6',0,NULL),('63','Services exterieurs B','6',0,NULL),('64','Impots et taxes','6',0,NULL),('65','Autres charges','6',0,NULL),('66','Charges de personnel','6',0,NULL),('67','Frais financiers et charges assimilees','6',0,NULL),('68','Dotations aux amortissements provisions et depreciations','6',0,NULL),('69','Impots sur resultats','6',0,NULL),
('7','Comptes de produits des activites ordinaires',NULL,0,NULL),('70','Ventes','7',0,NULL),('700','Produits financiers courants','70',0,NULL),('7001','Interets et produits assimiles','700',1,NULL),('701','Ventes de produits finis','70',0,NULL),('702','Ventes de produits intermediaires','70',0,NULL),('703','Ventes de produits residuels','70',0,NULL),('704','Travaux factures','70',0,NULL),('705','Etudes facturees','70',0,NULL),('706','Services vendus','70',0,NULL),('7061','Commissions sur services bancaires','706',1,NULL),('707','Produits accessoires','70',0,NULL),('7071','Produits services guichet','707',1,NULL),('708','Produits divers','70',0,NULL),('71','Subventions d exploitation','7',0,NULL),('72','Production immobilisee','7',0,NULL),('73','Variations des stocks de biens et services produits','7',0,NULL),('74','Produits divers','7',0,NULL),('75','Transferts de charges','7',0,NULL),('76','Revenus financiers et produits assimiles','7',0,NULL),('77','Produits exceptionnels','7',0,NULL),('78','Reprises de provisions et amortissements','7',0,NULL),('79','Transferts de produits','7',0,NULL),
('8','Comptes des autres charges et autres produits',NULL,0,NULL),('81','Valeurs comptables des cessions d immobilisations','8',0,NULL),('82','Produits des cessions d immobilisations','8',0,NULL),('83','Charges hors activites ordinaires','8',0,NULL),('84','Produits hors activites ordinaires','8',0,NULL),('85','Dotations hors activites ordinaires','8',0,NULL),('86','Reprises hors activites ordinaires','8',0,NULL),('87','Participations des travailleurs','8',0,NULL),('88','Subventions d equilibre','8',0,NULL),('89','Bilan ouverture et cloture','8',0,NULL);

INSERT INTO tb_plan_comptable (numero_compte, classe_ohada, libelle, parent_compte, niveau, type_compte, est_mouvementable, est_actif)
SELECT
  t.numero,
  LEFT(t.numero, 1),
  t.libelle,
  t.parent_compte,
  CHAR_LENGTH(t.numero),
  COALESCE(
    t.type_override,
    CASE LEFT(t.numero, 1)
      WHEN '1' THEN 'PASSIF'
      WHEN '2' THEN 'ACTIF'
      WHEN '3' THEN 'ACTIF'
      WHEN '4' THEN 'MIXTE'
      WHEN '5' THEN 'ACTIF'
      WHEN '6' THEN 'CHARGE'
      WHEN '7' THEN 'PRODUIT'
      WHEN '8' THEN 'HORS_BILAN'
      ELSE 'MIXTE'
    END
  ),
  t.est_mouvementable,
  1
FROM tmp_ohada_accounts t
ON DUPLICATE KEY UPDATE
  classe_ohada = VALUES(classe_ohada),
  libelle = VALUES(libelle),
  parent_compte = VALUES(parent_compte),
  niveau = VALUES(niveau),
  type_compte = VALUES(type_compte),
  est_mouvementable = VALUES(est_mouvementable),
  est_actif = VALUES(est_actif);

DROP TEMPORARY TABLE IF EXISTS tmp_ohada_accounts;

UPDATE tb_plan_comptable
SET classe_ohada = LEFT(numero_compte, 1)
WHERE classe_ohada IS NULL OR classe_ohada = '';

UPDATE tb_plan_comptable
SET niveau = CHAR_LENGTH(numero_compte)
WHERE niveau IS NULL OR niveau = 0;

-- =====================================================================
-- C. tb_commission_rules
-- =====================================================================

CREATE TABLE IF NOT EXISTS tb_commission_rules (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  libelle VARCHAR(150) NOT NULL,
  code_operation VARCHAR(50) NOT NULL DEFAULT 'TOUS',
  type_compte VARCHAR(20) NOT NULL DEFAULT 'TOUS',
  type_guichet VARCHAR(20) NOT NULL DEFAULT 'TOUS',
  devise_code CHAR(3) NULL,
  code_zone VARCHAR(50) NULL,
  portefeuille_id BIGINT UNSIGNED NULL,
  montant_min DECIMAL(18,2) NULL,
  montant_max DECIMAL(18,2) NULL,
  mode_calcul ENUM('FIXE','POURCENTAGE') NOT NULL,
  valeur DECIMAL(18,4) NOT NULL,
  priorite INT UNSIGNED NOT NULL DEFAULT 100,
  date_debut DATE NOT NULL,
  date_fin DATE NULL,
  est_actif TINYINT(1) NOT NULL DEFAULT 1,
  observations TEXT NULL,
  created_by_agent VARCHAR(50) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  INDEX idx_comm_rules_active_dates (est_actif, date_debut, date_fin),
  INDEX idx_comm_rules_scope (code_operation, type_compte, type_guichet),
  INDEX idx_comm_rules_context (devise_code, code_zone, portefeuille_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE tb_commission_rules ENGINE=InnoDB;
ALTER TABLE tb_commission_rules
  MODIFY created_by_agent VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  MODIFY code_zone VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  MODIFY devise_code VARCHAR(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'CREATE INDEX tb_comm_rules_zone_fk ON tb_commission_rules (code_zone)',
    'SELECT "Index tb_comm_rules_zone_fk already exists"')
  FROM information_schema.statistics
  WHERE table_schema=@db AND table_name='tb_commission_rules' AND index_name='tb_comm_rules_zone_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'CREATE INDEX tb_comm_rules_portefeuille_fk ON tb_commission_rules (portefeuille_id)',
    'SELECT "Index tb_comm_rules_portefeuille_fk already exists"')
  FROM information_schema.statistics
  WHERE table_schema=@db AND table_name='tb_commission_rules' AND index_name='tb_comm_rules_portefeuille_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'CREATE INDEX tb_agent ON tb_commission_rules (created_by_agent)',
    'SELECT "Index tb_agent already exists"')
  FROM information_schema.statistics
  WHERE table_schema=@db AND table_name='tb_commission_rules' AND index_name='tb_agent'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================================
-- D. tb_transaction_commissions
-- =====================================================================

CREATE TABLE IF NOT EXISTS tb_transaction_commissions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  transaction_id BIGINT UNSIGNED NOT NULL,
  commission_rule_id BIGINT UNSIGNED NULL,
  libelle VARCHAR(150) NOT NULL,
  code_operation VARCHAR(50) NOT NULL,
  type_compte VARCHAR(20) NULL,
  type_guichet VARCHAR(20) NULL,
  devise_code CHAR(3) NULL,
  code_zone VARCHAR(50) NULL,
  portefeuille_id BIGINT UNSIGNED NULL,
  mode_calcul ENUM('FIXE','POURCENTAGE') NOT NULL,
  valeur_snapshot DECIMAL(18,4) NOT NULL,
  base_calcul DECIMAL(18,2) NOT NULL DEFAULT 0,
  montant_commission DECIMAL(18,2) NOT NULL DEFAULT 0,
  date_application TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  agent_matricule VARCHAR(50) NULL,
  guichet_id BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  INDEX idx_trans_comm_tx_date (transaction_id, date_application),
  INDEX idx_trans_comm_scope (code_operation, type_compte, type_guichet)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'CREATE INDEX tb_trans_comm_rule_fk ON tb_transaction_commissions (commission_rule_id)',
    'SELECT "Index tb_trans_comm_rule_fk already exists"')
  FROM information_schema.statistics
  WHERE table_schema=@db AND table_name='tb_transaction_commissions' AND index_name='tb_trans_comm_rule_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'CREATE INDEX tb_trans_comm_zone_fk ON tb_transaction_commissions (code_zone)',
    'SELECT "Index tb_trans_comm_zone_fk already exists"')
  FROM information_schema.statistics
  WHERE table_schema=@db AND table_name='tb_transaction_commissions' AND index_name='tb_trans_comm_zone_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'CREATE INDEX tb_trans_comm_portefeuille_fk ON tb_transaction_commissions (portefeuille_id)',
    'SELECT "Index tb_trans_comm_portefeuille_fk already exists"')
  FROM information_schema.statistics
  WHERE table_schema=@db AND table_name='tb_transaction_commissions' AND index_name='tb_trans_comm_portefeuille_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'CREATE INDEX tb_trans_comm_guichet_fk ON tb_transaction_commissions (guichet_id)',
    'SELECT "Index tb_trans_comm_guichet_fk already exists"')
  FROM information_schema.statistics
  WHERE table_schema=@db AND table_name='tb_transaction_commissions' AND index_name='tb_trans_comm_guichet_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'CREATE INDEX tb_trans_comm_agent_fk ON tb_transaction_commissions (agent_matricule)',
    'SELECT "Index tb_trans_comm_agent_fk already exists"')
  FROM information_schema.statistics
  WHERE table_schema=@db AND table_name='tb_transaction_commissions' AND index_name='tb_trans_comm_agent_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================================
-- E. tb_compta_journaux and tb_compta_ecritures
-- =====================================================================

CREATE TABLE IF NOT EXISTS tb_compta_journaux (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  code_journal VARCHAR(20) NOT NULL DEFAULT 'CAI',
  reference_piece VARCHAR(80) NOT NULL,
  transaction_id BIGINT UNSIGNED NULL,
  type_piece ENUM('OPERATION','ANNULATION','REGULARISATION') NOT NULL DEFAULT 'OPERATION',
  devise_code VARCHAR(3) NULL,
  libelle VARCHAR(191) NOT NULL,
  statut ENUM('COMPTABILISE','ANNULE') NOT NULL DEFAULT 'COMPTABILISE',
  agent_matricule VARCHAR(50) NULL,
  date_ecriture TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  metadata JSON NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_tb_compta_journaux_reference_piece (reference_piece),
  INDEX idx_compta_journal_trans_type (transaction_id, type_piece),
  INDEX idx_compta_journal_date_devise (date_ecriture, devise_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'CREATE INDEX fk_compta_journal_agent ON tb_compta_journaux (agent_matricule)',
    'SELECT "Index fk_compta_journal_agent already exists"')
  FROM information_schema.statistics
  WHERE table_schema=@db AND table_name='tb_compta_journaux' AND index_name='fk_compta_journal_agent'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'CREATE INDEX fk_compta_journal_devise ON tb_compta_journaux (devise_code)',
    'SELECT "Index fk_compta_journal_devise already exists"')
  FROM information_schema.statistics
  WHERE table_schema=@db AND table_name='tb_compta_journaux' AND index_name='fk_compta_journal_devise'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS tb_compta_ecritures (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  journal_id BIGINT UNSIGNED NOT NULL,
  numero_compte VARCHAR(20) NOT NULL,
  devise_code VARCHAR(3) NULL,
  libelle_ligne VARCHAR(191) NULL,
  debit DECIMAL(18,2) NOT NULL DEFAULT 0,
  credit DECIMAL(18,2) NOT NULL DEFAULT 0,
  ordre INT UNSIGNED NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  INDEX idx_compta_ecriture_compte_devise (numero_compte, devise_code),
  INDEX idx_compta_ecriture_journal (journal_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'CREATE INDEX fk_compta_ecriture_devise ON tb_compta_ecritures (devise_code)',
    'SELECT "Index fk_compta_ecriture_devise already exists"')
  FROM information_schema.statistics
  WHERE table_schema=@db AND table_name='tb_compta_ecritures' AND index_name='fk_compta_ecriture_devise'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================================
-- F. Foreign keys
-- =====================================================================

ALTER TABLE tb_commission_rules ENGINE=InnoDB;
ALTER TABLE tb_commission_rules
  MODIFY created_by_agent VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  MODIFY code_zone VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  MODIFY devise_code VARCHAR(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE tb_transaction_commissions
  MODIFY agent_matricule VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  MODIFY code_zone VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE tb_compta_journaux
  MODIFY agent_matricule VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  MODIFY devise_code VARCHAR(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE tb_compta_ecritures
  MODIFY devise_code VARCHAR(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

UPDATE tb_commission_rules c
LEFT JOIN tb_agents a ON a.matricule = c.created_by_agent
SET c.created_by_agent = NULL
WHERE c.created_by_agent IS NOT NULL
  AND a.matricule IS NULL;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_commission_rules ADD CONSTRAINT fk_comm_rules_agent FOREIGN KEY (created_by_agent) REFERENCES tb_agents(matricule) ON DELETE RESTRICT ON UPDATE RESTRICT',
    'SELECT "FK tb_agent/fk_comm_rules_agent already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_commission_rules' AND constraint_name IN ('tb_agent', 'fk_comm_rules_agent')
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_commission_rules ADD CONSTRAINT tb_comm_rules_devise_fk FOREIGN KEY (devise_code) REFERENCES tb_devises(code_iso) ON DELETE SET NULL',
    'SELECT "FK tb_comm_rules_devise_fk already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_commission_rules' AND constraint_name='tb_comm_rules_devise_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_commission_rules ADD CONSTRAINT tb_comm_rules_zone_fk FOREIGN KEY (code_zone) REFERENCES tb_zones(code_zone) ON DELETE SET NULL',
    'SELECT "FK tb_comm_rules_zone_fk already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_commission_rules' AND constraint_name='tb_comm_rules_zone_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_commission_rules ADD CONSTRAINT tb_comm_rules_portefeuille_fk FOREIGN KEY (portefeuille_id) REFERENCES tb_portefeuilles_agents(id) ON DELETE SET NULL',
    'SELECT "FK tb_comm_rules_portefeuille_fk already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_commission_rules' AND constraint_name='tb_comm_rules_portefeuille_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_transaction_commissions ADD CONSTRAINT tb_trans_comm_tx_fk FOREIGN KEY (transaction_id) REFERENCES tb_transactions(id) ON DELETE CASCADE',
    'SELECT "FK tb_trans_comm_tx_fk already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_transaction_commissions' AND constraint_name='tb_trans_comm_tx_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_transaction_commissions ADD CONSTRAINT tb_trans_comm_rule_fk FOREIGN KEY (commission_rule_id) REFERENCES tb_commission_rules(id) ON DELETE SET NULL',
    'SELECT "FK tb_trans_comm_rule_fk already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_transaction_commissions' AND constraint_name='tb_trans_comm_rule_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_transaction_commissions ADD CONSTRAINT tb_trans_comm_zone_fk FOREIGN KEY (code_zone) REFERENCES tb_zones(code_zone) ON DELETE SET NULL',
    'SELECT "FK tb_trans_comm_zone_fk already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_transaction_commissions' AND constraint_name='tb_trans_comm_zone_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_transaction_commissions ADD CONSTRAINT tb_trans_comm_portefeuille_fk FOREIGN KEY (portefeuille_id) REFERENCES tb_portefeuilles_agents(id) ON DELETE SET NULL',
    'SELECT "FK tb_trans_comm_portefeuille_fk already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_transaction_commissions' AND constraint_name='tb_trans_comm_portefeuille_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_transaction_commissions ADD CONSTRAINT tb_trans_comm_guichet_fk FOREIGN KEY (guichet_id) REFERENCES tb_caisses_guichets(id) ON DELETE SET NULL',
    'SELECT "FK tb_trans_comm_guichet_fk already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_transaction_commissions' AND constraint_name='tb_trans_comm_guichet_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_transaction_commissions ADD CONSTRAINT tb_trans_comm_agent_fk FOREIGN KEY (agent_matricule) REFERENCES tb_agents(matricule) ON DELETE SET NULL',
    'SELECT "FK tb_trans_comm_agent_fk already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_transaction_commissions' AND constraint_name='tb_trans_comm_agent_fk'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_compta_journaux ADD CONSTRAINT fk_compta_journal_transaction FOREIGN KEY (transaction_id) REFERENCES tb_transactions(id) ON DELETE SET NULL ON UPDATE CASCADE',
    'SELECT "FK fk_compta_journal_transaction already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_compta_journaux' AND constraint_name='fk_compta_journal_transaction'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_compta_journaux ADD CONSTRAINT fk_compta_journal_agent FOREIGN KEY (agent_matricule) REFERENCES tb_agents(matricule) ON DELETE SET NULL ON UPDATE CASCADE',
    'SELECT "FK fk_compta_journal_agent already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_compta_journaux' AND constraint_name='fk_compta_journal_agent'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_compta_journaux ADD CONSTRAINT fk_compta_journal_devise FOREIGN KEY (devise_code) REFERENCES tb_devises(code_iso) ON DELETE SET NULL ON UPDATE CASCADE',
    'SELECT "FK fk_compta_journal_devise already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_compta_journaux' AND constraint_name='fk_compta_journal_devise'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_compta_ecritures ADD CONSTRAINT fk_compta_ecriture_journal FOREIGN KEY (journal_id) REFERENCES tb_compta_journaux(id) ON DELETE CASCADE ON UPDATE CASCADE',
    'SELECT "FK fk_compta_ecriture_journal already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_compta_ecritures' AND constraint_name='fk_compta_ecriture_journal'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_compta_ecritures ADD CONSTRAINT fk_compta_ecriture_compte FOREIGN KEY (numero_compte) REFERENCES tb_plan_comptable(numero_compte) ON DELETE RESTRICT ON UPDATE CASCADE',
    'SELECT "FK fk_compta_ecriture_compte already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_compta_ecritures' AND constraint_name='fk_compta_ecriture_compte'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE tb_compta_ecritures ADD CONSTRAINT fk_compta_ecriture_devise FOREIGN KEY (devise_code) REFERENCES tb_devises(code_iso) ON DELETE SET NULL ON UPDATE CASCADE',
    'SELECT "FK fk_compta_ecriture_devise already exists"')
  FROM information_schema.table_constraints
  WHERE table_schema=@db AND table_name='tb_compta_ecritures' AND constraint_name='fk_compta_ecriture_devise'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Production repair script completed' AS status;