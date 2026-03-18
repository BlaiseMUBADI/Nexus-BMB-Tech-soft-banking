# Plan de commit propre - Permissions 2026-03-18

Objectif: versionner uniquement les changements RBAC/routes/deploiement lies a la demande, sans inclure les gros dumps SQL.

## Fichiers a inclure

- database/migrations/2026_03_18_000013_extend_permissions_crud_tresorerie_agents_terrain.php
- database/migrations/2026_03_18_000014_add_module_specific_crud_permissions.php
- database/migrations/2026_03_18_000015_remove_global_crud_permissions.php
- database/migrations/2026_03_18_000016_remove_duplicate_module_permissions.php
- routes/comptes_clients.php
- routes/tresorerie.php
- routes/rh.php
- routes/caisse.php
- routes/credit.php
- resources/views/layouts/sidebar.blade.php
- resources/views/tresorerie/agents_mobiles.blade.php
- DEPLOIEMENT_PERMISSIONS_2026-03-18.md
- DEPLOIEMENT_PERMISSIONS_MODULES_2026-03-18.md
- CHECKLIST_POST_DEPLOIEMENT_PERMISSIONS_2026-03-18.md
- PLAN_COMMIT_PERMISSIONS_2026-03-18.md

## Fichiers a exclure du commit

- bdd_nexus_bmb_tech_soft_baking.sql
- coopa2747247.sql
- coopa2747247 (2).sql
- migration_sync_coopa2747247.sql
- dump-bdd_nexus_bmb_tech_soft_baking-202603151119.sql (supprime)
- dump-bdd_uka-202603161407.sql (supprime)

## Commandes git recommandees

```bash
git add database/migrations/2026_03_18_000013_extend_permissions_crud_tresorerie_agents_terrain.php
git add database/migrations/2026_03_18_000014_add_module_specific_crud_permissions.php
git add database/migrations/2026_03_18_000015_remove_global_crud_permissions.php
git add database/migrations/2026_03_18_000016_remove_duplicate_module_permissions.php
git add routes/comptes_clients.php routes/tresorerie.php routes/rh.php routes/caisse.php routes/credit.php
git add resources/views/layouts/sidebar.blade.php resources/views/tresorerie/agents_mobiles.blade.php
git add DEPLOIEMENT_PERMISSIONS_2026-03-18.md DEPLOIEMENT_PERMISSIONS_MODULES_2026-03-18.md CHECKLIST_POST_DEPLOIEMENT_PERMISSIONS_2026-03-18.md PLAN_COMMIT_PERMISSIONS_2026-03-18.md

git status

git commit -m "RBAC: retirer CRUD global (73-75) et consolider CRUD par module"
```

## Controle final avant push

```bash
git diff --name-only --cached
```

Resultat attendu: seulement les fichiers listes dans "Fichiers a inclure".
