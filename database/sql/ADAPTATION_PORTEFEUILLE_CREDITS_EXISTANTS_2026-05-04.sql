-- ============================================================================
-- ADAPTATION DES DOSSIERS CREDIT EXISTANTS A LA CONTRAINTE PORTEFEUILLE
-- Date: 2026-05-04
-- Cible: MySQL / MariaDB
--
-- Objectif:
-- 1) Renseigner tb_credit_demandes.portefeuille_id quand il est NULL
-- 2) Utiliser une logique metier fiable:
--    a. portefeuille unique actif de l'agent_analyse_matricule
--    b. sinon portefeuille unique actif de l'agent_createur_matricule
--    c. sinon portefeuille du compte rattache (tb_comptes.portefeuille_id)
-- 3) Conserver les cas ambigus/non resolus pour traitement manuel
--
-- IMPORTANT:
-- - Ce script ne force PAS portefeuille_id NOT NULL.
-- - Il produit un rapport final des lignes encore non resolues.
-- - Une table de sauvegarde est creee avant modification.
-- ============================================================================

SET @today := CURDATE();

START TRANSACTION;

-- --------------------------------------------------------------------------
-- 0) Sauvegarde des lignes impactables (portefeuille_id NULL)
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS backup_tb_credit_demandes_portefeuille_20260504 AS
SELECT
    d.id,
    d.numero_dossier,
    d.portefeuille_id,
    d.agent_analyse_matricule,
    d.agent_createur_matricule,
    d.compte_id,
    d.statut_global,
    NOW() AS sauvegarde_le
FROM tb_credit_demandes d
WHERE d.portefeuille_id IS NULL;

-- --------------------------------------------------------------------------
-- 1) Table temporaire: agents avec portefeuille actif UNIQUE
-- --------------------------------------------------------------------------
DROP TEMPORARY TABLE IF EXISTS tmp_agent_portefeuille_unique;
CREATE TEMPORARY TABLE tmp_agent_portefeuille_unique AS
SELECT
    ap.agent_matricule,
    MIN(ap.portefeuille_id) AS portefeuille_id,
    COUNT(DISTINCT ap.portefeuille_id) AS nb_portefeuilles_actifs
FROM tb_affectations_portefeuilles ap
WHERE ap.agent_matricule IS NOT NULL
  AND UPPER(COALESCE(ap.Etat, '')) = 'ACTIF'
  AND ap.date_debut <= @today
  AND (ap.date_fin IS NULL OR ap.date_fin > @today)
GROUP BY ap.agent_matricule
HAVING COUNT(DISTINCT ap.portefeuille_id) = 1;

-- --------------------------------------------------------------------------
-- 2) Remplissage via agent analyse (prioritaire)
-- --------------------------------------------------------------------------
UPDATE tb_credit_demandes d
JOIN tmp_agent_portefeuille_unique u
  ON u.agent_matricule = d.agent_analyse_matricule
SET d.portefeuille_id = u.portefeuille_id
WHERE d.portefeuille_id IS NULL;

SET @updated_from_analyse := ROW_COUNT();

-- --------------------------------------------------------------------------
-- 3) Remplissage via agent createur (fallback)
-- --------------------------------------------------------------------------
UPDATE tb_credit_demandes d
JOIN tmp_agent_portefeuille_unique u
  ON u.agent_matricule = d.agent_createur_matricule
SET d.portefeuille_id = u.portefeuille_id
WHERE d.portefeuille_id IS NULL;

SET @updated_from_createur := ROW_COUNT();

-- --------------------------------------------------------------------------
-- 4) Remplissage via compte rattache (dernier fallback)
-- --------------------------------------------------------------------------
UPDATE tb_credit_demandes d
JOIN tb_comptes c
  ON c.code_compte = d.compte_id
SET d.portefeuille_id = c.portefeuille_id
WHERE d.portefeuille_id IS NULL
  AND c.portefeuille_id IS NOT NULL;

SET @updated_from_compte := ROW_COUNT();

-- --------------------------------------------------------------------------
-- 5) Rapport de resultat
-- --------------------------------------------------------------------------
SELECT
    @updated_from_analyse AS maj_depuis_agent_analyse,
    @updated_from_createur AS maj_depuis_agent_createur,
    @updated_from_compte AS maj_depuis_compte,
    (
        SELECT COUNT(*)
        FROM tb_credit_demandes d
        WHERE d.portefeuille_id IS NULL
    ) AS dossiers_sans_portefeuille_restants;

-- Cas restants a traiter manuellement
SELECT
    d.id,
    d.numero_dossier,
    d.statut_global,
    d.agent_analyse_matricule,
    d.agent_createur_matricule,
    d.compte_id,
    d.portefeuille_id
FROM tb_credit_demandes d
WHERE d.portefeuille_id IS NULL
ORDER BY d.created_at DESC, d.id DESC;

-- Controle coherence: dossier avec portefeuille mais sans reference valide
SELECT
    d.id,
    d.numero_dossier,
    d.portefeuille_id
FROM tb_credit_demandes d
LEFT JOIN tb_portefeuilles_agents p ON p.id = d.portefeuille_id
WHERE d.portefeuille_id IS NOT NULL
  AND p.id IS NULL;

-- Controle metier: portefeuille du dossier non actif pour l'agent analyse actuel
SELECT
    d.id,
    d.numero_dossier,
    d.agent_analyse_matricule,
    d.portefeuille_id,
    d.statut_global
FROM tb_credit_demandes d
WHERE d.portefeuille_id IS NOT NULL
  AND d.agent_analyse_matricule IS NOT NULL
  AND NOT EXISTS (
      SELECT 1
      FROM tb_affectations_portefeuilles ap
      WHERE ap.portefeuille_id = d.portefeuille_id
        AND ap.agent_matricule = d.agent_analyse_matricule
        AND UPPER(COALESCE(ap.Etat, '')) = 'ACTIF'
        AND ap.date_debut <= @today
        AND (ap.date_fin IS NULL OR ap.date_fin > @today)
  )
ORDER BY d.id DESC;

-- --------------------------------------------------------------------------
-- 6) Validation finale avant commit
-- --------------------------------------------------------------------------
-- Si le rapport te convient, garde COMMIT.
-- Sinon remplace COMMIT par ROLLBACK.
COMMIT;

-- ROLLBACK;
