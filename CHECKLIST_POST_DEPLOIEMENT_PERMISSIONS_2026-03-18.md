# Checklist post-deploiement - Nettoyage des doublons permissions

Date: 2026-03-18

## 1) Ordre de deploiement

1. Backup base de donnees en ligne.
2. Deploy code applicatif (routes + vues + migration).
3. Executer SQL de `DEPLOIEMENT_SQL_PERMISSIONS_2026-03-18_FINAL.sql`.
4. Vider les caches Laravel:

```bash
php artisan optimize:clear
```

5. Reconnexion utilisateur (logout/login) pour recharger les permissions en session.

## 2) Verification SQL minimale

Executer:

```sql
SELECT code, nom
FROM tb_permissions
WHERE code IN (
  'EBEN-PER73','EBEN-PER74','EBEN-PER75',
  'EBEN-PER76','EBEN-PER77','EBEN-PER78','EBEN-PER79',
  'EBEN-PER80','EBEN-PER81','EBEN-PER82','EBEN-PER83','EBEN-PER84','EBEN-PER85',
  'EBEN-PER86','EBEN-PER87','EBEN-PER88','EBEN-PER89','EBEN-PER90','EBEN-PER91','EBEN-PER92','EBEN-PER93','EBEN-PER94',
  'EBEN-PER95','EBEN-PER96','EBEN-PER97','EBEN-PER100','EBEN-PER101','EBEN-PER102',
  'EBEN-PER103','EBEN-PER104','EBEN-PER105','EBEN-PER106','EBEN-PER107','EBEN-PER108','EBEN-PER109'
)
ORDER BY code;

SELECT role_code, permission_code
FROM tb_role_permission
WHERE permission_code IN (
  'EBEN-PER76','EBEN-PER77','EBEN-PER78','EBEN-PER79',
  'EBEN-PER80','EBEN-PER81','EBEN-PER82','EBEN-PER83','EBEN-PER84','EBEN-PER85',
  'EBEN-PER86','EBEN-PER87','EBEN-PER88','EBEN-PER89','EBEN-PER90','EBEN-PER91','EBEN-PER92','EBEN-PER93','EBEN-PER94',
  'EBEN-PER95','EBEN-PER96','EBEN-PER97','EBEN-PER100','EBEN-PER101','EBEN-PER102'
)
ORDER BY role_code, permission_code;

SELECT migration, batch
FROM migrations
WHERE migration IN (
  '2026_03_18_000015_remove_global_crud_permissions',
  '2026_03_18_000016_remove_duplicate_module_permissions',
  '2026_03_18_000017_normalize_permission_action_aliases',
  '2026_03_18_000018_refine_permission_action_wording_by_context',
  '2026_03_18_000019_split_modify_delete_permissions_by_module'
);
```

Resultat attendu:
- Les permissions dupliquees sont absentes (`73..75`, `80..97`, `100..102`).
- Les permissions metier historiques restent actives (ex: `16`,`17`,`19`,`7`,`8`,`9`,`54`,`56`, etc.).
- Les nouvelles permissions separees (`103..109`) sont presentes.
- Les migrations 000015 a 000019 sont executees.

## 3) Smoke tests fonctionnels (UI)

## 3.1 Admin (EBEN-ROL1)
- Ouvrir menu Clients/Membres: entree Agents Terrain visible.
- Ouvrir page Agents Terrain et export PDF: OK.
- Tester mutations metier: RH, Caisse, Credit, Tresorerie: OK.

## 3.2 Role sans permission cible
- Verifier qu un role non mappe sur la permission d action recoit 403 sur les endpoints de mutation.

## 3.3 Tresorerie
- Approvisionner / Alimenter: requiert `EBEN-PER45|EBEN-PER77` et `EBEN-PER47|EBEN-PER77`.
- Approbation/Rejet demandes et clotures: requiert `EBEN-PER46`.
- Commissions:
  - create: `EBEN-PER77`
  - update: `EBEN-PER78`
  - toggle: `EBEN-PER78`

## 4) Verification routes critiques

Verifier qu il n y a plus de dependance active vers les anciennes routes Agents Terrain sous tresorerie:

```bash
rg "tresorerie\.agents\.mobiles|clients\.agents-terrain" routes resources/views
```

Resultat attendu:
- `tresorerie.agents.mobiles*` absent des vues/routes actives.
- `clients.agents-terrain*` present dans routes + sidebar + vue rapport.

## 5) Retour arriere (si incident)

1. Restaurer backup DB.
2. Redeployer la revision precedente du code.
3. Vider cache Laravel.

## 6) Notes

- Le modele actif est base sur les permissions metier historiques deja en place.
- Les permissions globales CRUD (`EBEN-PER73/74/75`) sont retirees.
- Les permissions module-specifiques ajoutees en doublon (`EBEN-PER80..97` et `EBEN-PER100..102`) sont retirees.
- Regle de nommage retenue: un seul verbe par permission selon le contexte metier (Creer ou Ajouter, Supprimer ou Annuler).
- Separation stricte appliquee pour les actions sensibles via `EBEN-PER103..EBEN-PER109`.
