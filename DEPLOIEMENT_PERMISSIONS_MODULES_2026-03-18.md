# Deploiement SQL - Nettoyage doublons permissions

Objectif:
- Eviter les doublons de permissions deja existantes (creer/modifier/ajouter)
- Retirer les permissions globales legacy `EBEN-PER73..75`
- Retirer les permissions dupliquees module-specifiques `EBEN-PER80..97` et `EBEN-PER100..102`
- Conserver les permissions metier historiques (ex: `EBEN-PER16`, `EBEN-PER17`, `EBEN-PER19`, `EBEN-PER7`, `EBEN-PER8`, etc.)

Migrations code:
- `2026_03_18_000015_remove_global_crud_permissions`
- `2026_03_18_000016_remove_duplicate_module_permissions`

## SQL a executer en ligne

```sql
START TRANSACTION;

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

INSERT INTO migrations (migration, batch)
SELECT '2026_03_18_000015_remove_global_crud_permissions', COALESCE(MAX(batch), 0) + 1
FROM migrations
WHERE NOT EXISTS (
  SELECT 1 FROM migrations WHERE migration = '2026_03_18_000015_remove_global_crud_permissions'
);

INSERT INTO migrations (migration, batch)
SELECT '2026_03_18_000016_remove_duplicate_module_permissions', COALESCE(MAX(batch), 0) + 1
FROM migrations
WHERE NOT EXISTS (
  SELECT 1 FROM migrations WHERE migration = '2026_03_18_000016_remove_duplicate_module_permissions'
);

COMMIT;
```

## Verifications

```sql
SELECT code
FROM tb_permissions
WHERE code IN (
  'EBEN-PER73','EBEN-PER74','EBEN-PER75',
  'EBEN-PER80','EBEN-PER81','EBEN-PER82','EBEN-PER83','EBEN-PER84','EBEN-PER85',
  'EBEN-PER86','EBEN-PER87','EBEN-PER88','EBEN-PER89','EBEN-PER90','EBEN-PER91','EBEN-PER92','EBEN-PER93','EBEN-PER94',
  'EBEN-PER95','EBEN-PER96','EBEN-PER97','EBEN-PER100','EBEN-PER101','EBEN-PER102'
);

SELECT migration, batch
FROM migrations
WHERE migration IN (
  '2026_03_18_000015_remove_global_crud_permissions',
  '2026_03_18_000016_remove_duplicate_module_permissions'
);
```

Resultat attendu:
- Aucune ligne retournee pour les codes supprimes.
- Les deux migrations de nettoyage marquees comme executees.
