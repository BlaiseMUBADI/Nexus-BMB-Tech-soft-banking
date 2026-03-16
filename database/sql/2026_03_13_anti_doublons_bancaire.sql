-- =============================================================
-- SCRIPT LWS - ANTI DOUBLONS BANCAIRES (SAFE)
-- Auteur: Copilot
-- Date: 2026-03-13
-- Objectif: Empêcher les doublons critiques en production
-- =============================================================

SET NAMES utf8mb4;
SET @db := DATABASE();

-- -------------------------------------------------------------
-- 0) AUDIT PREALABLE (A LANCER ET VERIFIER)
-- -------------------------------------------------------------
-- A) Doublons piece identite clients
SELECT type_piece_identite, numero_piece_identite, COUNT(*) AS total
FROM tb_clients
WHERE numero_piece_identite IS NOT NULL
  AND numero_piece_identite <> ''
GROUP BY type_piece_identite, numero_piece_identite
HAVING COUNT(*) > 1;

-- B) Doublons email clients
SELECT email, COUNT(*) AS total
FROM tb_clients
WHERE email IS NOT NULL
  AND email <> ''
GROUP BY email
HAVING COUNT(*) > 1;

-- C) Doublons matricule client
SELECT matricule, COUNT(*) AS total
FROM tb_clients
WHERE matricule IS NOT NULL
  AND matricule <> ''
GROUP BY matricule
HAVING COUNT(*) > 1;

-- IMPORTANT:
-- Si une des 3 requêtes retourne des lignes, corriger les données d'abord
-- puis seulement exécuter la section 1.

-- -------------------------------------------------------------
-- 1) NORMALISATION LEGERE (NON DESTRUCTIVE)
-- -------------------------------------------------------------
UPDATE tb_clients
SET numero_piece_identite = TRIM(numero_piece_identite)
WHERE numero_piece_identite IS NOT NULL;

UPDATE tb_clients
SET email = LOWER(TRIM(email))
WHERE email IS NOT NULL AND email <> '';

-- -------------------------------------------------------------
-- 2) CONTRAINTES UNIQUES (IDEMPOTENT)
-- -------------------------------------------------------------
-- A) Unique piece identite par type
SET @sql := (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE tb_clients ADD UNIQUE KEY uq_tb_clients_piece_type_num (type_piece_identite, numero_piece_identite)',
        'SELECT "Index uq_tb_clients_piece_type_num deja present" AS info'
    )
    FROM information_schema.statistics
    WHERE table_schema = @db
      AND table_name = 'tb_clients'
      AND index_name = 'uq_tb_clients_piece_type_num'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- B) Unique email client
SET @sql := (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE tb_clients ADD UNIQUE KEY uq_tb_clients_email (email)',
        'SELECT "Index uq_tb_clients_email deja present" AS info'
    )
    FROM information_schema.statistics
    WHERE table_schema = @db
      AND table_name = 'tb_clients'
      AND index_name = 'uq_tb_clients_email'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- C) Unique numero matricule client
SET @sql := (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE tb_clients ADD UNIQUE KEY uq_tb_clients_matricule (matricule)',
        'SELECT "Contrainte d unicite sur tb_clients.matricule deja presente" AS info'
    )
    FROM information_schema.statistics
    WHERE table_schema = @db
      AND table_name = 'tb_clients'
      AND column_name = 'matricule'
      AND non_unique = 0
      AND seq_in_index = 1
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- -------------------------------------------------------------
-- 3) VERIFICATION FINALE
-- -------------------------------------------------------------
SHOW INDEX FROM tb_clients;

-- Fin script
