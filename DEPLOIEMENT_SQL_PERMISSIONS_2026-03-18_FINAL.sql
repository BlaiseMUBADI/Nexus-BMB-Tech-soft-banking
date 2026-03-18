-- =============================================================
-- DEPLOIEMENT SQL FINAL - PERMISSIONS RBAC (2026-03-18)
-- Couvre les migrations:
--   000015 remove_global_crud_permissions
--   000016 remove_duplicate_module_permissions
--   000017 normalize_permission_action_aliases
--   000018 refine_permission_action_wording_by_context
--   000019 split_modify_delete_permissions_by_module
-- =============================================================

START TRANSACTION;

SET @now = NOW();

-- 1) Nettoyage des permissions globales et doublons modules
DELETE FROM tb_role_permission
WHERE permission_code IN (
	'EBEN-PER73','EBEN-PER74','EBEN-PER75',
	'EBEN-PER80','EBEN-PER81','EBEN-PER82','EBEN-PER83','EBEN-PER84','EBEN-PER85',
	'EBEN-PER86','EBEN-PER87','EBEN-PER88','EBEN-PER89','EBEN-PER90','EBEN-PER91','EBEN-PER92','EBEN-PER93','EBEN-PER94',
	'EBEN-PER95','EBEN-PER96','EBEN-PER97','EBEN-PER100','EBEN-PER101','EBEN-PER102'
);

DELETE FROM tb_permissions
WHERE code IN (
	'EBEN-PER73','EBEN-PER74','EBEN-PER75',
	'EBEN-PER80','EBEN-PER81','EBEN-PER82','EBEN-PER83','EBEN-PER84','EBEN-PER85',
	'EBEN-PER86','EBEN-PER87','EBEN-PER88','EBEN-PER89','EBEN-PER90','EBEN-PER91','EBEN-PER92','EBEN-PER93','EBEN-PER94',
	'EBEN-PER95','EBEN-PER96','EBEN-PER97','EBEN-PER100','EBEN-PER101','EBEN-PER102'
);

-- 2) Libelles finaux des permissions historiques (et creation si manquante)
INSERT INTO tb_permissions (code, nom, description, created_at, updated_at) VALUES
	('EBEN-PER7',  'Creer agent',                         'Creer un nouvel agent', @now, @now),
	('EBEN-PER8',  'Ajouter service/poste RH',            'Ajouter des services et postes RH', @now, @now),
	('EBEN-PER9',  'Voir et creer affectations RH',       'Consulter et creer les affectations RH', @now, @now),
	('EBEN-PER10', 'Voir caisse et demandes',             'Consulter la caisse et initier des demandes', @now, @now),
	('EBEN-PER11', 'Gerer operations caisse',             'Creer et confirmer les operations caisse', @now, @now),
	('EBEN-PER16', 'Creer client',                        'Enregistrer un client', @now, @now),
	('EBEN-PER17', 'Modifier client',                     'Modifier un client', @now, @now),
	('EBEN-PER19', 'Creer compte client',                 'Ouvrir un compte client', @now, @now),
	('EBEN-PER54', 'Creer demande credit',                'Creer une nouvelle demande de credit', @now, @now),
	('EBEN-PER66', 'Annuler dossier credit',              'Annuler definitivement un dossier credit', @now, @now),
	('EBEN-PER77', 'Ajouter operation tresorerie',        'Ajouter une operation dans le module tresorerie', @now, @now),
	('EBEN-PER78', 'Modifier operation tresorerie',       'Modifier une operation dans le module tresorerie', @now, @now),
	('EBEN-PER79', 'Annuler operation tresorerie',        'Annuler une operation dans le module tresorerie', @now, @now)
ON DUPLICATE KEY UPDATE
	nom = VALUES(nom),
	description = VALUES(description),
	updated_at = VALUES(updated_at);

-- 3) Nouvelles permissions (separation stricte modifier/supprimer)
INSERT INTO tb_permissions (code, nom, description, created_at, updated_at) VALUES
	('EBEN-PER103', 'Modifier agent et service/poste RH', 'Modifier les agents, services et postes RH', @now, @now),
	('EBEN-PER104', 'Supprimer agent/service/poste RH',   'Supprimer les agents, services et postes RH', @now, @now),
	('EBEN-PER105', 'Modifier affectation RH',            'Modifier une affectation RH', @now, @now),
	('EBEN-PER106', 'Supprimer affectation RH',           'Supprimer une affectation RH', @now, @now),
	('EBEN-PER107', 'Supprimer client',                   'Supprimer un client', @now, @now),
	('EBEN-PER108', 'Supprimer compte client',            'Fermer/supprimer un compte client', @now, @now),
	('EBEN-PER109', 'Annuler operation caisse',           'Annuler une operation de caisse', @now, @now)
ON DUPLICATE KEY UPDATE
	nom = VALUES(nom),
	description = VALUES(description),
	updated_at = VALUES(updated_at);

-- 4) Clonage des attributions de roles vers les nouveaux codes
INSERT INTO tb_role_permission (role_code, permission_code, created_at, updated_at)
SELECT rp.role_code, 'EBEN-PER103', @now, @now
FROM tb_role_permission rp
WHERE rp.permission_code = 'EBEN-PER8'
ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at);

INSERT INTO tb_role_permission (role_code, permission_code, created_at, updated_at)
SELECT rp.role_code, 'EBEN-PER104', @now, @now
FROM tb_role_permission rp
WHERE rp.permission_code = 'EBEN-PER8'
ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at);

INSERT INTO tb_role_permission (role_code, permission_code, created_at, updated_at)
SELECT rp.role_code, 'EBEN-PER105', @now, @now
FROM tb_role_permission rp
WHERE rp.permission_code = 'EBEN-PER9'
ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at);

INSERT INTO tb_role_permission (role_code, permission_code, created_at, updated_at)
SELECT rp.role_code, 'EBEN-PER106', @now, @now
FROM tb_role_permission rp
WHERE rp.permission_code = 'EBEN-PER9'
ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at);

INSERT INTO tb_role_permission (role_code, permission_code, created_at, updated_at)
SELECT rp.role_code, 'EBEN-PER107', @now, @now
FROM tb_role_permission rp
WHERE rp.permission_code = 'EBEN-PER17'
ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at);

INSERT INTO tb_role_permission (role_code, permission_code, created_at, updated_at)
SELECT rp.role_code, 'EBEN-PER108', @now, @now
FROM tb_role_permission rp
WHERE rp.permission_code = 'EBEN-PER19'
ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at);

INSERT INTO tb_role_permission (role_code, permission_code, created_at, updated_at)
SELECT rp.role_code, 'EBEN-PER109', @now, @now
FROM tb_role_permission rp
WHERE rp.permission_code = 'EBEN-PER11'
ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at);

-- 5) Marquage des migrations executees (meme batch)
SET @batch = (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations);

INSERT INTO migrations (migration, batch)
SELECT '2026_03_18_000015_remove_global_crud_permissions', @batch
FROM DUAL
WHERE NOT EXISTS (
	SELECT 1 FROM migrations WHERE migration = '2026_03_18_000015_remove_global_crud_permissions'
);

INSERT INTO migrations (migration, batch)
SELECT '2026_03_18_000016_remove_duplicate_module_permissions', @batch
FROM DUAL
WHERE NOT EXISTS (
	SELECT 1 FROM migrations WHERE migration = '2026_03_18_000016_remove_duplicate_module_permissions'
);

INSERT INTO migrations (migration, batch)
SELECT '2026_03_18_000017_normalize_permission_action_aliases', @batch
FROM DUAL
WHERE NOT EXISTS (
	SELECT 1 FROM migrations WHERE migration = '2026_03_18_000017_normalize_permission_action_aliases'
);

INSERT INTO migrations (migration, batch)
SELECT '2026_03_18_000018_refine_permission_action_wording_by_context', @batch
FROM DUAL
WHERE NOT EXISTS (
	SELECT 1 FROM migrations WHERE migration = '2026_03_18_000018_refine_permission_action_wording_by_context'
);

INSERT INTO migrations (migration, batch)
SELECT '2026_03_18_000019_split_modify_delete_permissions_by_module', @batch
FROM DUAL
WHERE NOT EXISTS (
	SELECT 1 FROM migrations WHERE migration = '2026_03_18_000019_split_modify_delete_permissions_by_module'
);

COMMIT;

-- =============================================================
-- VERIFICATIONS RAPIDES (optionnel)
-- =============================================================
-- SELECT code, nom FROM tb_permissions WHERE code IN
-- ('EBEN-PER7','EBEN-PER8','EBEN-PER9','EBEN-PER10','EBEN-PER11','EBEN-PER16','EBEN-PER17','EBEN-PER19','EBEN-PER54','EBEN-PER66','EBEN-PER77','EBEN-PER78','EBEN-PER79','EBEN-PER103','EBEN-PER104','EBEN-PER105','EBEN-PER106','EBEN-PER107','EBEN-PER108','EBEN-PER109')
-- ORDER BY code;
--
-- SELECT permission_code, COUNT(*) AS total
-- FROM tb_role_permission
-- WHERE permission_code IN ('EBEN-PER103','EBEN-PER104','EBEN-PER105','EBEN-PER106','EBEN-PER107','EBEN-PER108','EBEN-PER109')
-- GROUP BY permission_code
-- ORDER BY permission_code;
