-- ======================================================
-- DEPLOIEMENT MANUEL SQL ZONES / PORTEFEUILLES
-- Date: 2026-03-21
-- Usage:
--   1. Sauvegarder la base
--   2. Exécuter ce script UNE SEULE FOIS
--   3. Exécuter ensuite le script de contrôle
-- Important:
--   Préférer normalement php artisan migrate / migrate --force
--   Ce script est une alternative manuelle
-- ======================================================

-- ======================================================
-- A. TABLES D'AFFECTATIONS
-- ======================================================
CREATE TABLE IF NOT EXISTS tb_affectations_zones (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    code_zone VARCHAR(50) NOT NULL,
    agent_matricule VARCHAR(50) NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NULL,
    Etat VARCHAR(50) NOT NULL DEFAULT 'ACTIF',
    motif VARCHAR(255) NULL,
    effectue_par_user_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_aff_zone_active (code_zone, Etat, date_fin),
    INDEX idx_aff_zone_agent_active (agent_matricule, Etat, date_fin),
    INDEX idx_aff_zone_period (code_zone, date_debut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_affectations_portefeuilles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    portefeuille_id BIGINT UNSIGNED NOT NULL,
    agent_matricule VARCHAR(50) NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NULL,
    Etat VARCHAR(50) NOT NULL DEFAULT 'ACTIF',
    motif VARCHAR(255) NULL,
    effectue_par_user_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_aff_pf_active (portefeuille_id, Etat, date_fin),
    INDEX idx_aff_pf_agent_active (agent_matricule, Etat, date_fin),
    INDEX idx_aff_pf_period (portefeuille_id, date_debut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- B. CONTRAINTES FK SI NON EXISTANTES
-- Selon les environnements, si elles existent déjà, ignorer l'erreur
-- ======================================================
ALTER TABLE tb_affectations_zones
    ADD CONSTRAINT fk_aff_zone_agent
    FOREIGN KEY (agent_matricule) REFERENCES tb_agents(matricule)
    ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE tb_affectations_portefeuilles
    ADD CONSTRAINT fk_aff_pf_portefeuille
    FOREIGN KEY (portefeuille_id) REFERENCES tb_portefeuilles_agents(id)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE tb_affectations_portefeuilles
    ADD CONSTRAINT fk_aff_pf_agent
    FOREIGN KEY (agent_matricule) REFERENCES tb_agents(matricule)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- ======================================================
-- C. RENDRE LEGACY NULLABLE
-- ======================================================
ALTER TABLE tb_zones MODIFY agent_commercial_matricule VARCHAR(50) NULL;
ALTER TABLE tb_portefeuilles_agents MODIFY agent_matricule VARCHAR(50) NULL;

DROP TABLE IF EXISTS tb_zone_historiques;
DROP TABLE IF EXISTS tb_portefeuille_historiques;

-- ======================================================
-- D. BACKFILL INITIAL ZONES -> AFFECTATIONS_ZONES
-- Insère seulement les affectations actives manquantes
-- ======================================================
INSERT INTO tb_affectations_zones (
    code_zone,
    agent_matricule,
    date_debut,
    date_fin,
    Etat,
    motif,
    effectue_par_user_id,
    created_at,
    updated_at
)
SELECT
    z.code_zone,
    z.agent_commercial_matricule,
    CURDATE(),
    NULL,
    'ACTIF',
    'Initialisation depuis tb_zones',
    NULL,
    NOW(),
    NOW()
FROM tb_zones z
LEFT JOIN tb_affectations_zones az
    ON az.code_zone = z.code_zone
   AND UPPER(az.Etat) = 'ACTIF'
   AND az.date_fin IS NULL
WHERE z.agent_commercial_matricule IS NOT NULL
  AND az.id IS NULL;

-- ======================================================
-- E. BACKFILL INITIAL PORTEFEUILLES -> AFFECTATIONS_PORTEFEUILLES
-- Insère seulement les affectations actives manquantes
-- ======================================================
INSERT INTO tb_affectations_portefeuilles (
    portefeuille_id,
    agent_matricule,
    date_debut,
    date_fin,
    Etat,
    motif,
    effectue_par_user_id,
    created_at,
    updated_at
)
SELECT
    p.id,
    p.agent_matricule,
    CURDATE(),
    NULL,
    'ACTIF',
    'Initialisation depuis tb_portefeuilles_agents',
    NULL,
    NOW(),
    NOW()
FROM tb_portefeuilles_agents p
LEFT JOIN tb_affectations_portefeuilles ap
    ON ap.portefeuille_id = p.id
   AND UPPER(ap.Etat) = 'ACTIF'
   AND ap.date_fin IS NULL
WHERE ap.id IS NULL;

-- ======================================================
-- F. CONTROLE RAPIDE IMMEDIAT
-- ======================================================
SELECT code_zone, COUNT(*) AS nb_actives
FROM tb_affectations_zones
WHERE UPPER(Etat) = 'ACTIF' AND date_debut <= CURDATE() AND (date_fin IS NULL OR date_fin > CURDATE())
GROUP BY code_zone
HAVING COUNT(*) > 1;

SELECT portefeuille_id, COUNT(*) AS nb_actives
FROM tb_affectations_portefeuilles
WHERE UPPER(Etat) = 'ACTIF' AND date_debut <= CURDATE() AND (date_fin IS NULL OR date_fin > CURDATE())
GROUP BY portefeuille_id
HAVING COUNT(*) > 1;

-- Résultat attendu pour les 2 requêtes ci-dessus: 0 ligne
