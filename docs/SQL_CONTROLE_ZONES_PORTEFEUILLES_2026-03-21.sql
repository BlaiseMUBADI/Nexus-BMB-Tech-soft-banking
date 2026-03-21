-- ======================================================
-- CONTROLE POST-DEPLOIEMENT ZONES / PORTEFEUILLES
-- Date: 2026-03-21
-- Compatible MySQL / MariaDB
-- A lancer après les migrations en local et en production
-- ======================================================

-- ======================================================
-- 1. Vérifier l'existence des nouvelles tables
-- ======================================================
SELECT TABLE_NAME
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN (
    'tb_affectations_zones',
    'tb_affectations_portefeuilles'
)
ORDER BY TABLE_NAME;

-- ======================================================
-- 2. Vérifier que les colonnes legacy sont bien nullable
-- ======================================================
SELECT TABLE_NAME, COLUMN_NAME, IS_NULLABLE, COLUMN_TYPE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND (
    (TABLE_NAME = 'tb_zones' AND COLUMN_NAME = 'agent_commercial_matricule')
    OR
    (TABLE_NAME = 'tb_portefeuilles_agents' AND COLUMN_NAME = 'agent_matricule')
  )
ORDER BY TABLE_NAME, COLUMN_NAME;

-- ======================================================
-- 3. Vérifier l'initialisation des affectations zones
-- ======================================================
SELECT COUNT(*) AS total_zones FROM tb_zones;

SELECT COUNT(*) AS total_affectations_zones FROM tb_affectations_zones;

SELECT z.code_zone, z.nom, z.agent_commercial_matricule, az.agent_matricule, az.Etat, az.date_debut, az.date_fin
FROM tb_zones z
LEFT JOIN tb_affectations_zones az
  ON az.code_zone = z.code_zone
 AND UPPER(az.Etat) = 'ACTIF'
 AND az.date_debut <= CURDATE()
 AND (az.date_fin IS NULL OR az.date_fin > CURDATE())
ORDER BY z.nom;

-- ======================================================
-- 4. Vérifier l'initialisation des affectations portefeuilles
-- ======================================================
SELECT COUNT(*) AS total_portefeuilles FROM tb_portefeuilles_agents;

SELECT COUNT(*) AS total_affectations_portefeuilles FROM tb_affectations_portefeuilles;

SELECT p.id, p.nom_portefeuille, p.agent_matricule, ap.agent_matricule AS agent_actif, ap.Etat, ap.date_debut, ap.date_fin
FROM tb_portefeuilles_agents p
LEFT JOIN tb_affectations_portefeuilles ap
  ON ap.portefeuille_id = p.id
 AND UPPER(ap.Etat) = 'ACTIF'
 AND ap.date_debut <= CURDATE()
 AND (ap.date_fin IS NULL OR ap.date_fin > CURDATE())
ORDER BY p.nom_portefeuille;

-- ======================================================
-- 5. Contrôle critique: aucune zone ne doit avoir plus d'un agent actif
-- ======================================================
SELECT code_zone, COUNT(*) AS nb_affectations_actives
FROM tb_affectations_zones
WHERE UPPER(Etat) = 'ACTIF'
  AND date_debut <= CURDATE()
  AND (date_fin IS NULL OR date_fin > CURDATE())
GROUP BY code_zone
HAVING COUNT(*) > 1;

-- Résultat attendu: 0 ligne

-- ======================================================
-- 6. Contrôle critique: aucun portefeuille ne doit avoir plus d'un agent actif
-- ======================================================
SELECT portefeuille_id, COUNT(*) AS nb_affectations_actives
FROM tb_affectations_portefeuilles
WHERE UPPER(Etat) = 'ACTIF'
  AND date_debut <= CURDATE()
  AND (date_fin IS NULL OR date_fin > CURDATE())
GROUP BY portefeuille_id
HAVING COUNT(*) > 1;

-- Résultat attendu: 0 ligne

-- ======================================================
-- 7. Zones sans affectation active
-- ======================================================
SELECT z.code_zone, z.nom, z.commune
FROM tb_zones z
LEFT JOIN tb_affectations_zones az
  ON az.code_zone = z.code_zone
 AND UPPER(az.Etat) = 'ACTIF'
 AND az.date_debut <= CURDATE()
 AND (az.date_fin IS NULL OR az.date_fin > CURDATE())
WHERE az.id IS NULL
ORDER BY z.nom;

-- Résultat acceptable: des lignes peuvent exister, car une zone peut être créée sans agent

-- ======================================================
-- 8. Portefeuilles sans affectation active
-- ======================================================
SELECT p.id, p.nom_portefeuille, p.taux_commission_agent
FROM tb_portefeuilles_agents p
LEFT JOIN tb_affectations_portefeuilles ap
  ON ap.portefeuille_id = p.id
 AND UPPER(ap.Etat) = 'ACTIF'
 AND ap.date_debut <= CURDATE()
 AND (ap.date_fin IS NULL OR ap.date_fin > CURDATE())
WHERE ap.id IS NULL
ORDER BY p.nom_portefeuille;

-- Résultat acceptable: des lignes peuvent exister, car un portefeuille peut être créé sans agent

-- ======================================================
-- 9. Incohérences legacy vs affectation active - ZONES
-- ======================================================
SELECT z.code_zone,
       z.nom,
       z.agent_commercial_matricule AS legacy_agent,
       az.agent_matricule AS active_agent
FROM tb_zones z
LEFT JOIN tb_affectations_zones az
  ON az.code_zone = z.code_zone
 AND UPPER(az.Etat) = 'ACTIF'
 AND az.date_debut <= CURDATE()
 AND (az.date_fin IS NULL OR az.date_fin > CURDATE())
WHERE COALESCE(z.agent_commercial_matricule, '') <> COALESCE(az.agent_matricule, '')
ORDER BY z.nom;

-- Résultat attendu: 0 ligne idéalement
-- Si des lignes existent, vérifier si elles proviennent d'anciennes données ou d'une opération incomplète

-- ======================================================
-- 10. Incohérences legacy vs affectation active - PORTEFEUILLES
-- ======================================================
SELECT p.id,
       p.nom_portefeuille,
       p.agent_matricule AS legacy_agent,
       ap.agent_matricule AS active_agent
FROM tb_portefeuilles_agents p
LEFT JOIN tb_affectations_portefeuilles ap
  ON ap.portefeuille_id = p.id
 AND UPPER(ap.Etat) = 'ACTIF'
 AND ap.date_debut <= CURDATE()
 AND (ap.date_fin IS NULL OR ap.date_fin > CURDATE())
WHERE COALESCE(p.agent_matricule, '') <> COALESCE(ap.agent_matricule, '')
ORDER BY p.nom_portefeuille;

-- Résultat attendu: 0 ligne idéalement

-- ======================================================
-- 11. Historique d'affectation des zones
-- ======================================================
SELECT COUNT(*) AS total_affectations_zones FROM tb_affectations_zones;

SELECT id, code_zone, agent_matricule, date_debut, date_fin, Etat, motif, effectue_par_user_id, created_at
FROM tb_affectations_zones
ORDER BY date_debut DESC, id DESC
LIMIT 30;

-- ======================================================
-- 12. Historique d'affectation des portefeuilles
-- ======================================================
SELECT COUNT(*) AS total_affectations_portefeuilles FROM tb_affectations_portefeuilles;

SELECT id, portefeuille_id, agent_matricule, date_debut, date_fin, Etat, motif, effectue_par_user_id, created_at
FROM tb_affectations_portefeuilles
ORDER BY date_debut DESC, id DESC
LIMIT 30;

-- ======================================================
-- 13. Vérification d'intégrité référentielle basique
-- ======================================================
SELECT az.id, az.code_zone
FROM tb_affectations_zones az
LEFT JOIN tb_zones z ON z.code_zone = az.code_zone
WHERE z.code_zone IS NULL;

SELECT ap.id, ap.portefeuille_id
FROM tb_affectations_portefeuilles ap
LEFT JOIN tb_portefeuilles_agents p ON p.id = ap.portefeuille_id
WHERE p.id IS NULL;

-- Résultat attendu: 0 ligne dans les deux cas

-- ======================================================
-- 14. Résumé exécutif
-- ======================================================
SELECT 'ZONES_TOTAL' AS indicateur, COUNT(*) AS valeur FROM tb_zones
UNION ALL
SELECT 'ZONES_SANS_AGENT_ACTIF', COUNT(*)
FROM tb_zones z
LEFT JOIN tb_affectations_zones az
  ON az.code_zone = z.code_zone
 AND UPPER(az.Etat) = 'ACTIF'
 AND az.date_debut <= CURDATE()
 AND (az.date_fin IS NULL OR az.date_fin > CURDATE())
WHERE az.id IS NULL
UNION ALL
SELECT 'PORTEFEUILLES_TOTAL', COUNT(*) FROM tb_portefeuilles_agents
UNION ALL
SELECT 'PORTEFEUILLES_SANS_AGENT_ACTIF', COUNT(*)
FROM tb_portefeuilles_agents p
LEFT JOIN tb_affectations_portefeuilles ap
  ON ap.portefeuille_id = p.id
 AND UPPER(ap.Etat) = 'ACTIF'
 AND ap.date_debut <= CURDATE()
 AND (ap.date_fin IS NULL OR ap.date_fin > CURDATE())
WHERE ap.id IS NULL
UNION ALL
SELECT 'ZONE_AFFECTATIONS_TOTAL', COUNT(*) FROM tb_affectations_zones
UNION ALL
SELECT 'PORTEFEUILLE_AFFECTATIONS_TOTAL', COUNT(*) FROM tb_affectations_portefeuilles;
