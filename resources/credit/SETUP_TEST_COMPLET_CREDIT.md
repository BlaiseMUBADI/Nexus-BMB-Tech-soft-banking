# Setup test complet module Credit

Ce setup a ete corrige pour correspondre au processus metier clarifie en utilisant les roles metier reels `EBEN-ROL*`.

1. Demandeur: cree et soumet la demande.
2. Charge des operations: recoit le dossier, l'oriente, puis effectue aussi le deblocage.
3. Agent de credit: fait l'analyse terrain et la premiere validation technique.
4. Controleur: fait la contre-verification.
5. Gerant: prend la decision finale.

Il n'y a plus de profils artificiels `valid1..4`, `deblocage` ou `audit` dans ce setup.
Les anciens roles techniques `EBEN-RC*` sont supprimes par le runtime.

Important:

1. Le systeme conserve 4 niveaux de validation internes: `AGENT_CREDIT`, `CHARGE_OPERATIONS`, `CONTROLEUR`, `GERANT`.
2. Ces 4 niveaux representent 4 etapes de controle, pas 4 metiers supplementaires.
3. Le charge des operations reste une seule personne, meme s'il intervient avant la decision et au deblocage.

Mot de passe commun:

- `CreditTest@2026`

Hash bcrypt utilise dans le script:

- `$2y$10$.RfKv/dFNAdp6GHXw4vejeCL9PldgR2MUTg08WbtPcmN6o4z/iQ12`

---

## Utilisateurs de test cibles

1. `credit.demandeur@test.local` -> `EBEN-ROL9` `AGENT COMMERCIAL`
2. `credit.charge.operations@test.local` -> `EBEN-ROL11` `Charge des opérations`
3. `credit.agent.credit@test.local` -> `EBEN-ROL6` `Chargé de crédit`
4. `credit.controleur@test.local` -> role metier controleur cree si absent, localement `EBEN-ROL14`
5. `credit.gerant@test.local` -> `EBEN-ROL12` `GÉRANT`

---

## Nettoyage de l'ancien setup surdimensionne

Le runtime met d'abord en propre les anciens artefacts de test:

1. suppression des affectations de roles de `credit.analyste@test.local`, `credit.valid1@test.local`, `credit.valid2@test.local`, `credit.valid3@test.local`, `credit.valid4@test.local`, `credit.deblocage@test.local`, `credit.audit@test.local`
2. suppression des anciens users et agents correspondants
3. suppression de tous les anciens roles techniques `EBEN-RC*`

Le demandeur historique `credit.demandeur@test.local` est conserve et reutilise.
Le runtime reutilise les roles metier reels et cree seulement le role controleur s'il manque dans la base.

---

## Roles metier utilises

1. `EBEN-ROL9` -> Demandeur
2. `EBEN-ROL11` -> Charge des operations
3. `EBEN-ROL6` -> Agent de credit
4. `EBEN-ROL14` local -> Controleur interne credit
5. `EBEN-ROL12` -> Gerant

Note:

1. si un role controleur existe deja dans l'environnement cible, le runtime le reutilise
2. sinon il cree le prochain `EBEN-ROLx` libre avec le nom `Contrôleur interne crédit`

---

## Principe de recalage des permissions credit

Le runtime ne touche qu'au sous-ensemble de permissions de workflow credit `EBEN-PER53` a `EBEN-PER64`.
Les autres permissions non liees a ce flux restent intactes.

Repartition cible du workflow:

1. Demandeur -> `EBEN-PER53`, `EBEN-PER54`, `EBEN-PER56`, `EBEN-PER57`
2. Charge des operations -> `EBEN-PER53`, `EBEN-PER57`, `EBEN-PER61`, `EBEN-PER64`
3. Agent de credit -> `EBEN-PER53`, `EBEN-PER54`, `EBEN-PER57`, `EBEN-PER58`, `EBEN-PER59`, `EBEN-PER60`
4. Controleur -> `EBEN-PER53`, `EBEN-PER57`, `EBEN-PER62`
5. Gerant -> `EBEN-PER53`, `EBEN-PER57`, `EBEN-PER63`

---

## Script SQL de reference

Ce SQL ne recopie plus ligne par ligne tout le runtime. Le fichier PHP fait trois choses:

1. supprime les anciens roles techniques `EBEN-RC*`
2. attache les 5 users de test aux roles metier reels
3. recale les permissions credit de workflow sur les roles metier cibles

```sql
START TRANSACTION;

DELETE ru
FROM tb_role_user ru
JOIN users u ON u.id = ru.user_id
WHERE u.email IN (
  'credit.analyste@test.local',
  'credit.valid1@test.local',
  'credit.valid2@test.local',
  'credit.valid3@test.local',
  'credit.valid4@test.local',
  'credit.deblocage@test.local',
  'credit.audit@test.local'
);

DELETE FROM users
WHERE email IN (
  'credit.analyste@test.local',
  'credit.valid1@test.local',
  'credit.valid2@test.local',
  'credit.valid3@test.local',
  'credit.valid4@test.local',
  'credit.deblocage@test.local',
  'credit.audit@test.local'
);

DELETE FROM tb_agents
WHERE matricule IN (
  'AG-CRD-TST-0002',
  'AG-CRD-TST-0003',
  'AG-CRD-TST-0004',
  'AG-CRD-TST-0005',
  'AG-CRD-TST-0006',
  'AG-CRD-TST-0007',
  'AG-CRD-TST-0008'
);

DELETE FROM tb_role_permission
WHERE role_code IN (
  'EBEN-RCANA','EBEN-RCV1','EBEN-RCV2','EBEN-RCV3','EBEN-RCV4','EBEN-RCDEB','EBEN-RCAUD'
);

DELETE FROM tb_roles
WHERE code IN (
  'EBEN-RCANA','EBEN-RCV1','EBEN-RCV2','EBEN-RCV3','EBEN-RCV4','EBEN-RCDEB','EBEN-RCAUD'
);

INSERT INTO tb_agents
(matricule, nom, postnom, prenom, sexe, email, date_embauche, statut, created_at, updated_at)
VALUES
('AG-CRD-TST-0001', 'TEST', 'CREDIT', 'DEMANDEUR', 'M', 'credit.demandeur@test.local', CURDATE(), 'actif', NOW(), NOW()),
('AG-CRD-TST-0002', 'TEST', 'CREDIT', 'CHARGE_OPERATIONS', 'M', 'credit.charge.operations@test.local', CURDATE(), 'actif', NOW(), NOW()),
('AG-CRD-TST-0003', 'TEST', 'CREDIT', 'AGENT_CREDIT', 'M', 'credit.agent.credit@test.local', CURDATE(), 'actif', NOW(), NOW()),
('AG-CRD-TST-0004', 'TEST', 'CREDIT', 'CONTROLEUR', 'M', 'credit.controleur@test.local', CURDATE(), 'actif', NOW(), NOW()),
('AG-CRD-TST-0005', 'TEST', 'CREDIT', 'GERANT', 'M', 'credit.gerant@test.local', CURDATE(), 'actif', NOW(), NOW())
ON DUPLICATE KEY UPDATE
prenom = VALUES(prenom),
email = VALUES(email),
statut = VALUES(statut),
updated_at = NOW();

INSERT INTO users
(agent_matricule, name, email, email_verified_at, password, remember_token, etat, created_at, updated_at)
VALUES
('AG-CRD-TST-0001', 'credit_demandeur', 'credit.demandeur@test.local', NOW(), '$2y$10$.RfKv/dFNAdp6GHXw4vejeCL9PldgR2MUTg08WbtPcmN6o4z/iQ12', NULL, 'actif', NOW(), NOW()),
('AG-CRD-TST-0002', 'credit_charge_operations', 'credit.charge.operations@test.local', NOW(), '$2y$10$.RfKv/dFNAdp6GHXw4vejeCL9PldgR2MUTg08WbtPcmN6o4z/iQ12', NULL, 'actif', NOW(), NOW()),
('AG-CRD-TST-0003', 'credit_agent_credit', 'credit.agent.credit@test.local', NOW(), '$2y$10$.RfKv/dFNAdp6GHXw4vejeCL9PldgR2MUTg08WbtPcmN6o4z/iQ12', NULL, 'actif', NOW(), NOW()),
('AG-CRD-TST-0004', 'credit_controleur', 'credit.controleur@test.local', NOW(), '$2y$10$.RfKv/dFNAdp6GHXw4vejeCL9PldgR2MUTg08WbtPcmN6o4z/iQ12', NULL, 'actif', NOW(), NOW()),
('AG-CRD-TST-0005', 'credit_gerant', 'credit.gerant@test.local', NOW(), '$2y$10$.RfKv/dFNAdp6GHXw4vejeCL9PldgR2MUTg08WbtPcmN6o4z/iQ12', NULL, 'actif', NOW(), NOW())
ON DUPLICATE KEY UPDATE
name = VALUES(name),
agent_matricule = VALUES(agent_matricule),
etat = VALUES(etat),
updated_at = NOW();

INSERT INTO tb_roles (code, nom, description, created_at, updated_at)
VALUES
('EBEN-RCDEM', 'TEST Credit Demandeur', 'Creation, detail, soumission', NOW(), NOW()),
('EBEN-RCOPS', 'TEST Credit Charge Operations', 'Reception, validation charge operations et deblocage', NOW(), NOW()),
('EBEN-RCAGC', 'TEST Credit Agent Credit', 'Analyse terrain et premiere validation', NOW(), NOW()),
('EBEN-RCCTR', 'TEST Credit Controleur', 'Contre-verification du dossier', NOW(), NOW()),
('EBEN-RCGER', 'TEST Credit Gerant', 'Decision finale du dossier', NOW(), NOW())
ON DUPLICATE KEY UPDATE
nom = VALUES(nom),
description = VALUES(description),
updated_at = NOW();

INSERT IGNORE INTO tb_role_permission (role_code, permission_code, created_at, updated_at)
VALUES
('EBEN-RCDEM', 'EBEN-PER53', NOW(), NOW()),
('EBEN-RCDEM', 'EBEN-PER54', NOW(), NOW()),
('EBEN-RCDEM', 'EBEN-PER56', NOW(), NOW()),
('EBEN-RCDEM', 'EBEN-PER57', NOW(), NOW()),

('EBEN-RCOPS', 'EBEN-PER53', NOW(), NOW()),
('EBEN-RCOPS', 'EBEN-PER57', NOW(), NOW()),
('EBEN-RCOPS', 'EBEN-PER61', NOW(), NOW()),
('EBEN-RCOPS', 'EBEN-PER64', NOW(), NOW()),

('EBEN-RCAGC', 'EBEN-PER53', NOW(), NOW()),
('EBEN-RCAGC', 'EBEN-PER57', NOW(), NOW()),
('EBEN-RCAGC', 'EBEN-PER58', NOW(), NOW()),
('EBEN-RCAGC', 'EBEN-PER59', NOW(), NOW()),
('EBEN-RCAGC', 'EBEN-PER60', NOW(), NOW()),

('EBEN-RCCTR', 'EBEN-PER53', NOW(), NOW()),
('EBEN-RCCTR', 'EBEN-PER57', NOW(), NOW()),
('EBEN-RCCTR', 'EBEN-PER62', NOW(), NOW()),

('EBEN-RCGER', 'EBEN-PER53', NOW(), NOW()),
('EBEN-RCGER', 'EBEN-PER57', NOW(), NOW()),
('EBEN-RCGER', 'EBEN-PER63', NOW(), NOW());

COMMIT;
```

---

## Correspondance metier -> validation systeme

1. `AGENT_CREDIT` -> Agent de credit
2. `CHARGE_OPERATIONS` -> Charge des operations
3. `CONTROLEUR` -> Controleur
4. `GERANT` -> Gerant

Le demandeur ne fait pas partie des validations. Il cree et soumet seulement le dossier.

---

## Comptes clients de test

Le runtime conserve aussi la creation de 8 comptes `CAU-TST-*` repartis 4/4 sur deux portefeuilles de test.

Note schema:

1. dans cette base, le type `CAUTION_CREDIT` n'existe pas
2. les comptes de caution de test sont donc crees avec type `RMB`
3. le prefixe technique reste `CAU-TST-`

---

## Resultat attendu apres execution du runtime

1. `users_test_count = 5`
2. `technical_roles_remaining = 0`
3. `caution_test_count = 8`
4. aucune trace des anciens users `valid1..4`, `deblocage`, `audit`
5. `credit.controleur@test.local` est rattache a un vrai role metier controleur

---

## Ordre exact de test du workflow

1. Connecte-toi avec `credit.demandeur@test.local`.
2. Cree une demande de credit depuis la fiche client ou depuis le module credit.
3. Soumets le dossier.

4. Connecte-toi avec `credit.charge.operations@test.local`.
5. Ouvre le dossier soumis.
6. Clique sur `Affecter agent crédit` et sélectionne l'agent de crédit.
7. Confirme l'affectation. Le dossier reste `SOUMIS` mais devient analysable uniquement par l'agent affecté.
8. Utilise aussi ce meme compte plus tard pour la validation `CHARGE_OPERATIONS` puis le deblocage final des fonds.

9. Connecte-toi avec `credit.agent.credit@test.local`.
10. Ouvre le dossier qui t'a ete affecté.
11. Demarre l'analyse du dossier.
12. Renseigne l'analyse terrain.
13. Marque l'analyse comme complete.
14. Fait la validation niveau `AGENT_CREDIT`.

15. Connecte-toi avec `credit.controleur@test.local`.
16. Ouvre le dossier apres l'analyse.
17. Fait la contre-verification.
18. Valide le niveau `CONTROLEUR`.

19. Reconnecte-toi avec `credit.charge.operations@test.local`.
20. Fait la revue operationnelle finale.
21. Valide le niveau `CHARGE_OPERATIONS`.

22. Connecte-toi avec `credit.gerant@test.local`.
23. Prends la decision finale.
24. Valide le niveau `GERANT` ou rejette le dossier.

25. Reconnecte-toi avec `credit.charge.operations@test.local`.
26. Si le dossier est pret a debloquer, effectue le deblocage.
27. Verifie la generation de l'echeancier et la bascule du statut du dossier.

---

## Etat actuel apres cette refonte

1. les roles techniques `EBEN-RC*` ne sont plus necessaires
2. les 5 users de test utilisent des roles metier reels
3. le seul ajout structurel est le role controleur si votre base ne l'avait pas encore
4. la separation des validations est preservee sans garder un jeu de roles de test artificiels
