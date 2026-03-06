# Référentiel des Permissions — Nexus BMB Tech (Coopec EBEN)

> **Règle d'or** : Les permissions sont **statiques** (définies dans ce document et dans le code des routes).  
> Seul le développeur peut en ajouter via une migration.  
> L'admin peut uniquement **attribuer / retirer** des permissions aux rôles.

---

## Synthèse par module

| Module | PER | Nombre |
|---|---|---|
| Administration système | PER01 – PER05 | 5 |
| Ressources Humaines | PER06 – PER09 | 4 |
| Caisse / Guichet | PER10 – PER14 | 5 |
| Clients / Membres | PER15 – PER17 | 3 |
| Comptes clients | PER18 – PER19 | 2 |
| Devises & Taux | PER20 – PER21 | 2 |
| Transactions bancaires | PER22 – PER26 | 5 |
| Épargne | PER27 – PER29 | 3 |
| Crédits / Prêts | PER30 – PER35 | 6 |
| Rapports & Statistiques | PER36 – PER38 | 3 |
| Comptabilité | PER39 – PER41 | 3 |
| Audit & Sécurité | PER42 – PER43 | 2 |
| **TOTAL** | | **43** |

---

## MODULE 1 — Administration système

| Code | Nom | Description | Route(s) protégée(s) |
|---|---|---|---|
| `EBEN-PER1` | Gérer administration | Utilisateurs, zones, portefeuilles, guichets | `administration/*` |
| `EBEN-PER2` | Voir rôles | Consulter la liste des rôles et leurs permissions | `administration/roles-permissions` (GET) |
| `EBEN-PER3` | Gérer rôles | Créer/supprimer rôles, attacher/détacher permissions et rôles aux users | `administration/roles-permissions` (POST/DELETE) |
| `EBEN-PER4` | Voir permissions | Consulter la liste des permissions système | `administration/permissions-table` |
| `EBEN-PER5` | ~~Créer permissions~~ | **DÉSACTIVÉ** — statique, réservé développeur | — |

---

## MODULE 2 — Ressources Humaines

| Code | Nom | Description | Route(s) protégée(s) |
|---|---|---|---|
| `EBEN-PER6` | Voir agents | Consulter la liste et les fiches agents | `rh/agents` (GET) |
| `EBEN-PER7` | Créer agents | Ajouter un nouvel agent | `rh/agents/create`, `rh/agents` (POST) |
| `EBEN-PER8` | Modifier/supprimer agents | Éditer ou supprimer un agent, gérer services & postes | `rh/agents/{id}/edit`, DELETE |
| `EBEN-PER9` | Gérer affectations | Créer, modifier, clôturer les affectations | `rh/affectations/*` |

---

## MODULE 3 — Caisse / Guichet

| Code | Nom | Description | Route(s) protégée(s) |
|---|---|---|---|
| `EBEN-PER10` | Voir caisse | Accéder à l'écran caisse/guichet | `caisses/ouverture` (GET) |
| `EBEN-PER11` | Ouvrir / Fermer caisse | Changer le statut OUVERT/FERMÉ du guichet | `caisses/changer-statut` |
| `EBEN-PER12` | Fermer caisse | Fermeture formelle de session caisse | — *(à implémenter)* |
| `EBEN-PER13` | Mouvements de caisse | Saisir les mouvements (versements, retraits guichet) | — *(à implémenter)* |
| `EBEN-PER14` | Clôture de caisse | Clôture journalière et génération du rapport de caisse | — *(à implémenter)* |

---

## MODULE 4 — Clients / Membres

| Code | Nom | Description | Route(s) protégée(s) |
|---|---|---|---|
| `EBEN-PER15` | Voir clients | Consulter la liste et les fiches membres | `comptes-clients/clients` (GET) |
| `EBEN-PER16` | Créer clients | Enregistrer un nouveau membre/client | `comptes-clients/clients` (POST) |
| `EBEN-PER17` | Modifier/supprimer clients | Éditer ou supprimer un client | `comptes-clients/clients/{id}/edit`, DELETE |

---

## MODULE 5 — Comptes clients

| Code | Nom | Description | Route(s) protégée(s) |
|---|---|---|---|
| `EBEN-PER18` | Voir comptes | Consulter les comptes clients | `comptes-clients/comptes` (GET) |
| `EBEN-PER19` | Gérer comptes | Ouvrir et clôturer des comptes | `comptes-clients/comptes` (POST/DELETE) |

---

## MODULE 6 — Devises & Taux de change

| Code | Nom | Description | Route(s) protégée(s) |
|---|---|---|---|
| `EBEN-PER20` | Voir devises/taux | Consulter les devises et les taux | `administration/devises-taux` (GET) |
| `EBEN-PER21` | Gérer devises/taux | Créer devises, saisir nouveaux taux | `administration/devises-taux/*` (POST/DELETE) |

---

## MODULE 7 — Transactions bancaires *(à implémenter)*

| Code | Nom | Description |
|---|---|---|
| `EBEN-PER22` | Effectuer dépôts | Saisir un dépôt sur un compte client |
| `EBEN-PER23` | Effectuer retraits | Saisir un retrait sur un compte client |
| `EBEN-PER24` | Effectuer virements | Initier un virement entre comptes |
| `EBEN-PER25` | Annuler transactions | Annuler / reverser une opération |
| `EBEN-PER26` | Valider transactions | Approuver les opérations en attente (double validation) |

---

## MODULE 8 — Épargne *(à implémenter)*

| Code | Nom | Description |
|---|---|---|
| `EBEN-PER27` | Voir produits épargne | Consulter les produits d'épargne disponibles |
| `EBEN-PER28` | Gérer produits épargne | Créer/modifier les produits d'épargne |
| `EBEN-PER29` | Gérer comptes épargne | Ouvrir, alimenter, clôturer des comptes épargne |

---

## MODULE 9 — Crédits / Prêts *(à implémenter)*

| Code | Nom | Description |
|---|---|---|
| `EBEN-PER30` | Voir crédits | Consulter les dossiers de crédit |
| `EBEN-PER31` | Soumettre demande crédit | Créer une demande de prêt pour un client |
| `EBEN-PER32` | Instruire dossier crédit | Analyser et compléter un dossier |
| `EBEN-PER33` | Approuver crédit | Accorder ou rejeter un crédit (comité) |
| `EBEN-PER34` | Gérer remboursements | Saisir les échéances et paiements |
| `EBEN-PER35` | Clôturer crédit | Marquage crédit soldé / en contentieux |

---

## MODULE 10 — Rapports & Statistiques *(à implémenter)*

| Code | Nom | Description |
|---|---|---|
| `EBEN-PER36` | Voir rapports opérationnels | Rapports journaliers caisse, transactions |
| `EBEN-PER37` | Voir rapports financiers | Bilan, compte de résultat, situation financière |
| `EBEN-PER38` | Exporter / imprimer | Export PDF/Excel de tous les rapports |

---

## MODULE 11 — Comptabilité *(à implémenter)*

| Code | Nom | Description |
|---|---|---|
| `EBEN-PER39` | Voir journal comptable | Consulter les écritures du plan comptable |
| `EBEN-PER40` | Saisir écritures | Créer des écritures comptables manuelles |
| `EBEN-PER41` | Valider écritures | Approuver / lettrer les écritures |

---

## MODULE 12 — Audit & Sécurité *(à implémenter)*

| Code | Nom | Description |
|---|---|---|
| `EBEN-PER42` | Voir journal d'activité | Logs : qui a fait quoi et quand |
| `EBEN-PER43` | Gérer paramètres sécurité | Politique mdp, tentatives login, blocages |

---

## Matrice Rôles × Permissions recommandée

| Permission | ROL1 Admin | ROL2 Caissier | ROL3 Directeur | ROL4 Agent RH | ROL5 Superviseur | ROL6 Crédit | ROL7 Comptable |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| PER1 Gérer admin | ✅ | | | | | | |
| PER2 Voir rôles | ✅ | | ✅ | | | | |
| PER3 Gérer rôles | ✅ | | | | | | |
| PER4 Voir permissions | ✅ | | ✅ | | | | |
| PER6 Voir agents | ✅ | | ✅ | ✅ | ✅ | | |
| PER7 Créer agents | ✅ | | | ✅ | | | |
| PER8 Modifier agents | ✅ | | | ✅ | | | |
| PER9 Affectations | ✅ | | | ✅ | | | |
| PER10 Voir caisse | ✅ | ✅ | ✅ | | ✅ | | |
| PER11 Ouvrir caisse | ✅ | ✅ | | | | | |
| PER12 Fermer caisse | ✅ | ✅ | | | | | |
| PER13 Mouvements | ✅ | ✅ | | | | | |
| PER14 Clôture caisse | ✅ | ✅ | | | | | |
| PER15 Voir clients | ✅ | ✅ | ✅ | | ✅ | ✅ | |
| PER16 Créer clients | ✅ | | | | | ✅ | |
| PER17 Modifier clients | ✅ | | | | | | |
| PER18 Voir comptes | ✅ | ✅ | ✅ | | ✅ | ✅ | ✅ |
| PER19 Gérer comptes | ✅ | | | | | ✅ | |
| PER20 Voir devises | ✅ | ✅ | ✅ | | ✅ | | |
| PER21 Gérer devises | ✅ | | | | | | |
| PER22 Dépôts | ✅ | ✅ | | | | | |
| PER23 Retraits | ✅ | ✅ | | | | | |
| PER24 Virements | ✅ | ✅ | | | | | |
| PER25 Annuler tx | ✅ | | | | | | |
| PER26 Valider tx | ✅ | | ✅ | | ✅ | | |
| PER27 Voir épargne | ✅ | | ✅ | | ✅ | ✅ | |
| PER28 Gérer épargne | ✅ | | | | | ✅ | |
| PER29 Comptes épargne | ✅ | ✅ | | | | ✅ | |
| PER30 Voir crédits | ✅ | | ✅ | | ✅ | ✅ | |
| PER31 Soumettre crédit | ✅ | | | | | ✅ | |
| PER32 Instruire crédit | ✅ | | | | | ✅ | |
| PER33 Approuver crédit | ✅ | | ✅ | | | | |
| PER34 Remboursements | ✅ | ✅ | | | | ✅ | |
| PER35 Clôturer crédit | ✅ | | | | | ✅ | |
| PER36 Rapports opéra. | ✅ | | ✅ | | ✅ | | |
| PER37 Rapports financ. | ✅ | | ✅ | | ✅ | | ✅ |
| PER38 Exporter | ✅ | | ✅ | | ✅ | | ✅ |
| PER39 Voir journal cpta | ✅ | | ✅ | | | | ✅ |
| PER40 Saisir écritures | ✅ | | | | | | ✅ |
| PER41 Valider écritures | ✅ | | | | | | ✅ |
| PER42 Voir logs audit | ✅ | | ✅ | | | | |
| PER43 Paramètres sécu | ✅ | | | | | | |

> **Note** : ROL1 (Admin) possède **toutes** les permissions via le bypass `isAdmin()` — indépendamment de la DB.

---

## Rôles recommandés

| Code | Nom | Usage |
|---|---|---|
| `EBEN-ROL1` | Administrateur | Accès total — directeur informatique / responsable système |
| `EBEN-ROL2` | Caissier | Opérations guichet uniquement |
| `EBEN-ROL3` | Directeur | Lecture seule sur tout, approbation crédits |
| `EBEN-ROL4` | Agent RH | Gestion du personnel et affectations |
| `EBEN-ROL5` | Superviseur | Contrôle transversal en lecture, validation light |
| `EBEN-ROL6` | Chargé de crédit | Dossiers crédit + comptes + épargne |
| `EBEN-ROL7` | Comptable | Comptabilité, rapports financiers, comptes |
