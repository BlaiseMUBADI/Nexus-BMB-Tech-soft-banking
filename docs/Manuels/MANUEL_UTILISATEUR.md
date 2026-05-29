# Manuel Utilisateur — Coopec EBEN

**Logiciel** : Nexus BMB Tech Soft Banking — Système de gestion bancaire et financier
**Édition** : Coopérative d'Épargne et de Crédit EBEN (Coopec EBEN)
**Site institutionnel** : https://coopaeben.info/
**Version** : 3.0.0
**Date** : Mai 2026
**Éditeur** : Nexus BMB Tech
**Public visé** : Caissiers, Agents commerciaux, Chargés de crédit, Analystes, Validateurs, Chargés des opérations, Trésoriers, Comptables, Gestionnaires RH, Administrateurs système.

---

## Sommaire

1. [Introduction](#1-introduction)
2. [Prérequis & accès](#2-prérequis--accès)
3. [Premiers pas — Connexion & déconnexion](#3-premiers-pas--connexion--déconnexion)
4. [Présentation de l'interface](#4-présentation-de-linterface)
5. [Tableau de bord](#5-tableau-de-bord)
6. [Module Comptes Clients](#6-module-comptes-clients)
7. [Module Caisse & Guichet](#7-module-caisse--guichet)
8. [Module Trésorerie & Coffre central](#8-module-trésorerie--coffre-central)
9. [Module Crédit](#9-module-crédit)
10. [Module Comptabilité OHADA](#10-module-comptabilité-ohada)
11. [Module Ressources Humaines](#11-module-ressources-humaines)
12. [Module Administration & RBAC](#12-module-administration--rbac)
13. [Centre de notifications](#13-centre-de-notifications)
14. [Mon profil](#14-mon-profil)
15. [Procédures types par rôle](#15-procédures-types-par-rôle)
16. [Bonnes pratiques & sécurité](#16-bonnes-pratiques--sécurité)
17. [Dépannage (FAQ)](#17-dépannage-faq)
18. [Glossaire métier](#18-glossaire-métier)
19. [Annexes](#19-annexes)

---

## 1. Introduction

### 1.1 Objet du logiciel

**Nexus BMB Tech Soft Banking** est une plateforme web complète de gestion bancaire conçue pour les institutions de microfinance et les coopératives d'épargne et de crédit (COOPEC). Déployée pour la **Coopec EBEN**, elle couvre l'ensemble des opérations métier d'une agence :

- Gestion centralisée des **membres** (clients) et de leurs **comptes** (Courant, DAT, Épargne, Garantie, Remboursement).
- Opérations de **guichet** multi-devises : dépôts, retraits, change, paiements, remboursements, avec calcul automatique des **commissions**.
- Gestion du **coffre central**, des **alimentations de guichets** et des **clôtures de caisse** validées par la trésorerie.
- Cycle complet du **crédit** : demande → analyse → validation à 4 niveaux → déblocage → remboursement, avec traçabilité totale.
- **Comptabilité OHADA** intégrée : chaque opération produit ses écritures (journal, grand-livre, plan comptable).
- **Ressources humaines** : agents, services, postes, affectations zones / guichets / portefeuilles historisées.
- **Administration & RBAC** dynamique : utilisateurs, rôles, permissions atomiques (≈ 100 permissions), zones, devises, taux de change.
- **Centre de notifications** persistantes pour suivre les flux métiers en temps réel.

### 1.2 Architecture résumée

| Composant | Détail |
|---|---|
| Cadriciel | Laravel 13.x |
| PHP | 8.3+ |
| Base de données | MySQL 8 (`coopa2747247` / `bdd_nexus_bmb_tech_soft_baking`) — tables préfixées `tb_*` |
| Interface | AdminLTE 3 + Tailwind utilitaires |
| Authentification | Laravel Breeze (sessions + vérification e-mail) |
| Notifications | Laravel database notifications (`SystemDatabaseNotification`) |
| Comptabilité | OHADA — plan comptable normalisé |
| PDF | Dompdf + Barryvdh PDF |

### 1.3 Principes fondateurs

- **Séparation stricte des rôles** : aucun utilisateur ne peut accéder à un module sans la permission correspondante.
- **Traçabilité exhaustive** : chaque opération est horodatée et attribuée à un agent.
- **Multi-devises** : toutes les opérations sont rattachées à une devise et à un taux de change historisé.
- **Multi-guichets** : guichets FIXE (bureau), MOBILE (terrain) et CENTRAL (coffre-fort).
- **Scoping géographique** : les agents commerciaux ne voient que les membres et comptes de **leurs zones d'affectation**.
- **Immuabilité comptable** : les écritures et les commissions sont versionnées et **non modifiables** après validation.

> **Note** : ce manuel décrit l'usage **fonctionnel** de l'application. Pour la mise en place technique (installation, déploiement, migrations, sauvegardes), reportez-vous au fichier `README.md` du projet.

---

## 2. Prérequis & accès

### 2.1 Matériel recommandé

- **Poste caissier / guichet** : PC ou tablette 14" minimum, clavier physique, souris ou écran tactile, imprimante ticket optionnelle (format A4 pour bordereaux PDF).
- **Poste chargé de crédit / analyste** : PC avec écran 22" recommandé.
- **Poste trésorier / administrateur** : PC avec écran 24", double écran recommandé pour le suivi des flux.
- **Imprimante** : laser A4 noir & blanc pour les bordereaux, fiches client, échéanciers et relevés.
- **Réseau** : connexion internet stable (fibre / 4G) pour l'accès distant à `https://coopaeben.info/`.

### 2.2 Navigateur

| Navigateur | Version minimale | Statut |
|---|---|---|
| Microsoft Edge | 110+ | Recommandé |
| Google Chrome | 110+ | Recommandé |
| Mozilla Firefox | 110+ | Supporté |
| Safari | 16+ | Supporté |
| Internet Explorer | toutes | **Non supporté** |

JavaScript, cookies et fenêtres modales doivent être autorisés. La résolution minimale conseillée est **1366 × 768**.

### 2.3 URL d'accès

L'application est accessible à l'adresse suivante :

```
https://coopaeben.info/login
```

En environnement local de formation ou de test :

```
http://localhost/Nexus-BMB-Tech-soft-banking/public/login
```

> **Conseil** : créez un raccourci sur le bureau pointant directement vers `/login` et épinglez l'onglet dans votre navigateur.

### 2.4 Comptes et rôles

Chaque utilisateur dispose d'**un compte personnel** (e-mail + mot de passe) rattaché à un membre du personnel (**agent**) via son **matricule**. Le compte se voit attribuer un ou plusieurs **rôles**, chacun regroupant un ensemble de **permissions atomiques** (codifiées `EBEN-PER01` à `EBEN-PER108`).

Les rôles typiques sont :

- **Super Admin** — accès intégral, gestion RBAC.
- **Administrateur système** — utilisateurs, zones, devises, paramétrage.
- **Caissier / Guichetier** — opérations courantes (dépôt, retrait, change).
- **Chef de caisse / Trésorier** — approbations clôtures, alimentations guichets, gestion coffre.
- **Comptable** — consultation journal, grand-livre, plan comptable.
- **Chargé de crédit** — création des dossiers, suivi.
- **Analyste crédit** — analyse des dossiers.
- **Validateur crédit L1 / L2 / L3 / L4** — validation hiérarchique.
- **Chargé des opérations crédit** — affectation, déblocage.
- **Chargé clientèle / Agent commercial** — gestion des membres et comptes.
- **Agent mobile** — opérations sur le terrain, accès restreint à ses zones.
- **Gestionnaire RH** — agents, services, postes, affectations.

---

## 3. Premiers pas — Connexion & déconnexion

### 3.1 Se connecter

1. Ouvrez votre navigateur et saisissez l'URL d'accès.
2. La page **Connexion** s'affiche avec le logo Coopec EBEN.
3. Saisissez votre **adresse e-mail** professionnelle.
4. Saisissez votre **mot de passe**.
5. Cochez éventuellement **Se souvenir de moi** (déconseillé sur un poste partagé).
6. Cliquez sur **Se connecter**.

**En cas d'échec** : un message rouge s'affiche. Vérifiez :

- la casse du mot de passe (verrouillage majuscule désactivé) ;
- que votre compte est **actif** (un compte désactivé refuse la connexion) ;
- que votre adresse e-mail a bien été **vérifiée** (lien dans le premier e-mail reçu).

Après 6 tentatives infructueuses en moins d'une minute, le compte est temporairement bloqué. Patientez ou contactez l'administrateur.

### 3.2 Vérification de l'e-mail

À la première connexion (ou si l'administrateur l'a exigé), le système vous demande de **vérifier votre adresse e-mail** :

1. Cliquez sur **Renvoyer le lien de vérification**.
2. Ouvrez votre boîte mail et cliquez sur le lien reçu.
3. Reconnectez-vous : vous accédez au tableau de bord.

### 3.3 Se déconnecter

- Cliquez sur votre **avatar** en haut à droite → **Déconnexion**.
- Une déconnexion automatique se produit après **10 minutes d'inactivité** (paramètre `session.inactivity_timeout`, modifiable par l'administrateur).
- Un **heartbeat** AJAX prolonge automatiquement la session tant que vous interagissez avec l'application.

### 3.4 Réinitialiser le mot de passe

- Page de connexion → **Mot de passe oublié ?**
- Saisissez votre e-mail → un lien signé vous est envoyé (validité 60 minutes).
- Cliquez sur le lien, choisissez un nouveau mot de passe (8 caractères minimum, mixte recommandé).
- L'administrateur peut également réinitialiser le mot de passe depuis **Administration → Utilisateurs**.

---

## 4. Présentation de l'interface

L'interface est structurée en quatre zones standard AdminLTE :

```
+---------------------------------------------------------+
|  Top Navbar  : logo . recherche . cloche notifs . avatar|
+----------+----------------------------------------------+
|          |                                              |
| Sidebar  |           Zone de contenu (modules)          |
| (menu)   |                                              |
|          |                                              |
+----------+----------------------------------------------+
|                  Footer · © Coopec EBEN                 |
+---------------------------------------------------------+
```

### 4.1 La barre supérieure (navbar)

- **Logo Coopec EBEN** à gauche : plie / déplie la sidebar.
- **Cloche de notifications** : affiche en rouge le compteur de notifications non lues, et en orange un second badge pour les notifications **nécessitant une action** (`action_required`). Un clic ouvre un menu déroulant des 5 dernières notifications.
- **Avatar utilisateur** : menu *Mon profil · Mes notifications · Déconnexion*.

### 4.2 La sidebar (menu latéral)

Le menu liste **uniquement les modules autorisés** par vos permissions. Les entrées peuvent contenir des sous-menus (cliquer pour développer). L'élément actif est surligné. Les sections principales :

- Tableau de bord
- Comptes Clients (Membres · Comptes · Mobilité)
- Caisse (Ouverture · Opérations · Mes demandes · Journal)
- Trésorerie (Coffre · Alimentations · Demandes · Clôtures · Commissions)
- Crédit (Dossiers · Analyse · Validation · Déblocage · Remboursement · Supervision)
- Comptabilité (Dashboard · Plan OHADA · Journal · Grand-livre)
- Ressources Humaines (Agents · Services · Postes · Affectations)
- Administration (Utilisateurs · Rôles & Permissions · Zones · Guichets · Devises)
- Notifications

### 4.3 Les fenêtres modales

Toutes les opérations de création ou de modification (créer un membre, enregistrer une opération, créer un dossier de crédit, etc.) s'ouvrent dans une **fenêtre modale** par-dessus la page courante. Cliquez sur la croix ou en dehors de la modale pour fermer **sans enregistrer**.

### 4.4 Les messages système

- **Vert** : opération réussie (« Enregistré avec succès »).
- **Bleu** : information neutre (« Demande transmise au trésorier »).
- **Orange** : avertissement (« Solde du guichet faible »).
- **Rouge** : erreur (« Solde insuffisant », « Champ obligatoire manquant »).

Les messages disparaissent automatiquement après 5 secondes. Cliquez dessus pour les masquer immédiatement.

### 4.5 Tableaux et recherches

Tous les tableaux disposent de :

- une **barre de recherche** en haut à droite ;
- des **filtres** par colonne (zone, statut, devise, période) ;
- la **pagination** en bas (10, 25, 50 ou 100 lignes par page) ;
- l'**export** PDF lorsque la permission est accordée.

---

## 5. Tableau de bord

**Route** : `/dashboard`
**Accès** : tous les utilisateurs authentifiés et vérifiés.

C'est la page d'accueil affichée après connexion. Elle présente :

- Un **bandeau de bienvenue** personnalisé (prénom, nom, postnom).
- La liste de vos **rôles** et **permissions** en cours.
- Des **vignettes d'accès rapide** vers les modules autorisés.
- Des **indicateurs clés** (KPI) selon votre rôle :
  - Caissier : statut de votre guichet, solde par devise, nombre d'opérations du jour.
  - Trésorier : solde du coffre central, demandes en attente, clôtures à valider.
  - Chargé de crédit : dossiers en cours, à analyser, à valider.
  - Administrateur : nombre d'utilisateurs actifs, alertes système.
- Un bloc **Dernières notifications** (5 plus récentes non lues).

> **Astuce** : vos vignettes d'accès sont calculées dynamiquement selon vos permissions. Si une vignette est absente, c'est qu'elle n'est pas autorisée par votre rôle — contactez votre administrateur.

---

## 6. Module Comptes Clients

Ce module gère les **membres** (clients) de la coopérative et leurs **comptes bancaires**.

### 6.1 Sous-module Membres (Clients)

**Route** : `/comptes-clients/clients`
**Permission de base** : `EBEN-PER15` (Voir clients).

#### 6.1.1 Lister les membres

À l'ouverture, le tableau affiche tous les membres visibles selon votre **scoping de zone** :

- Un **agent commercial mobile** ne voit que les membres affectés à ses zones (via `tb_affectations_zones`).
- Un **administrateur** ou un **caissier de bureau** voit l'ensemble des membres.

| Colonne | Description |
|---|---|
| Matricule | Identifiant unique (`CL-EBENKGA-YY-XXXXX`) auto-généré |
| Photo | Miniature de la photo d'identité |
| Nom · Postnom · Prénom | État civil |
| Téléphone | Numéro principal |
| Zone | Code zone d'affectation |
| Date inscription | Date de création |
| Actions | Voir · Éditer · Imprimer fiche · Supprimer |

#### 6.1.2 Créer un membre

**Permission** : `EBEN-PER15` (accès formulaire) + `EBEN-PER16` (enregistrement).

1. Cliquez sur **+ Nouveau membre**.
2. Renseignez l'**état civil** : nom, postnom, prénom, sexe, date et lieu de naissance, état civil, nom du conjoint le cas échéant.
3. Renseignez les **coordonnées** : téléphone, e-mail, adresse complète.
4. Renseignez la **pièce d'identité** : type (CNI, Passeport, Permis…), numéro, date d'émission.
5. Renseignez le **profil socio-économique** : secteur d'activité, revenu mensuel estimé.
6. Choisissez la **zone** d'affectation (obligatoire).
7. Téléversez la **photo d'identité** (formats JPG / PNG, ≤ 2 Mo).
8. Cliquez **Enregistrer**.

Le système :

- Génère automatiquement le **matricule** au format `CL-EBENKGA-YY-XXXXX`.
- Vérifie l'**unicité** du matricule, du numéro de pièce et de l'e-mail.
- Enregistre la photo dans `images_projet/clients/`.

> **Important** : le matricule est la **clé primaire** logique du membre. Il ne peut être ni modifié ni réutilisé. En cas de doublon détecté à l'enregistrement, le système rejette l'opération avec un message clair.

#### 6.1.3 Consulter ou modifier un membre

- **Voir** (PER15) : icône œil → fiche complète avec onglets *État civil · Comptes · Crédits · Historique*.
- **Éditer** (PER17) : icône crayon → formulaire de modification.
- **Supprimer** (PER107) : action restreinte, soumise à confirmation et tracée dans le journal.

#### 6.1.4 Impressions

| Impression | Route | Permission |
|---|---|---|
| Fiche membre individuelle (PDF) | `/comptes-clients/clients/{matricule}/fiche-pdf` | PER15 |
| Liste complète des membres (PDF) | `/comptes-clients/clients-liste-pdf` | PER15 |
| Liste des agents de terrain | `/comptes-clients/clients/agents-terrain-pdf` | PER76 |

> **Restriction mobile** : les utilisateurs en mode **mobile guichet** ne peuvent **pas imprimer** les fiches membres ni les listes (protection des données en mobilité). La protection est appliquée à la fois côté interface et côté serveur (403).

### 6.2 Sous-module Comptes

**Route** : `/comptes-clients/comptes`
**Permission de base** : `EBEN-PER18` (Voir comptes).

#### 6.2.1 Types de comptes

| Code | Libellé | Usage |
|---|---|---|
| `CC` | Compte Courant | Compte principal de dépôt / retrait |
| `RMB` | Remboursement | Compte de remboursement de crédit |
| `GTC` | Garantie / Caution | Caution bloquée pour un crédit |
| `DAT` | Dépôt à Terme | Épargne à terme rémunérée |
| `EAV` | Épargne & Avantages | Épargne libre |

#### 6.2.2 Créer un compte

**Permission** : `EBEN-PER18` (accès formulaire) + `EBEN-PER19` (enregistrement).

1. Cliquez sur **+ Nouveau compte**.
2. Recherchez le **membre titulaire** par matricule, nom ou téléphone.
3. Choisissez le **type de compte** (`CC`, `RMB`, `GTC`, `DAT`, `EAV`).
4. Choisissez la **devise** (CDF, USD, EUR…).
5. Choisissez le **portefeuille** d'affectation.
6. Saisissez le **dépôt initial** (peut être 0).
7. Cliquez **Enregistrer**.

Le système :

- Génère automatiquement le **code compte** (clé primaire).
- Initialise les soldes : solde réel + solde bloqué.
- Crée la première écriture comptable si un dépôt initial est saisi.

> **Information technique** : la clé primaire d'un compte est `code_compte` (chaîne) et **non un identifiant numérique**. Tous les modules (crédit, caisse, etc.) référencent les comptes par ce code.

#### 6.2.3 Documents d'un compte

| Document | Route | Description |
|---|---|---|
| **RIB / IBAN** | `/comptes-clients/comptes/{code}/rib` | Document officiel d'identification bancaire |
| **Relevé de compte** | `/comptes-clients/comptes/{code}/releve-pdf` | Historique des mouvements sur période |
| **Historique transactions** | `/comptes-clients/comptes/{code}/historique` | Vue en ligne des opérations |
| **Liste de tous les comptes** | `/comptes-clients/comptes-liste-pdf` | Export PDF global |

#### 6.2.4 Solde et blocages

- **Solde réel** : montant disponible.
- **Solde bloqué** : montant immobilisé (caution crédit, opération en cours).
- **Solde disponible** = Solde réel − Solde bloqué.

Le solde disponible est vérifié à chaque retrait ou paiement. En cas d'insuffisance, l'opération est refusée.

---

## 7. Module Caisse & Guichet

Ce module gère le quotidien d'un **caissier** : ouverture du guichet, opérations courantes, demandes d'approvisionnement et clôture de caisse.

### 7.1 Concepts clés

| Concept | Description |
|---|---|
| **Guichet** | Point physique de service. Trois types : `FIXE` (bureau), `MOBILE` (terrain), `CENTRAL` (coffre-fort). |
| **Affectation** | Lien entre un agent et un guichet, historisé dans `tb_affectations`. |
| **Solde guichet** | Solde par devise (multi-devises possibles sur un même guichet). |
| **Statut guichet** | `FERMÉ`, `OUVERT`, `SUSPENDU`, `CLÔTURÉ`. |
| **Opération** | Transaction : `DEPOT`, `RETRAIT`, `CHANGE`, `PAIEMENT`, `REMBOURSEMENT`. |
| **Commission** | Frais bancaires calculés selon des règles versionnées (`tb_commission_rules`) et figés par opération (`tb_transaction_commissions`). |

### 7.2 Ouvrir un guichet

**Route** : `/caisses/ouverture` (PER10) puis `/caisses/changer-statut/{id}` (PER11).

1. Allez dans **Caisse → Ouverture**.
2. Le système affiche votre **guichet affecté** (selon `tb_affectations` actives), avec :
   - le **type** (FIXE, MOBILE, CENTRAL) ;
   - le **statut courant** ;
   - les **soldes par devise**.
3. Cliquez sur **Ouvrir le guichet**.
4. Saisissez une **observation** si requis.
5. Validez.

Le guichet passe au statut **OUVERT**. Vous pouvez désormais enregistrer des opérations.

> **Avertissement** : un guichet `FERMÉ` ou `SUSPENDU` ne permet **aucune opération**. Les routes serveur refusent l'enregistrement même en cas de contournement de l'interface.

### 7.3 Effectuer une opération de guichet

**Route** : `/caisses/operations` (PER10 pour voir, PER11 pour créer).

#### 7.3.1 Étapes générales

1. Ouvrez **Caisse → Opérations**.
2. Cliquez **+ Nouvelle opération**.
3. Choisissez le **type** :
   - `DEPOT` — dépôt d'espèces sur un compte.
   - `RETRAIT` — retrait d'espèces.
   - `CHANGE` — change manuel devise A → devise B.
   - `PAIEMENT` — paiement d'une facture / frais.
   - `REMBOURSEMENT` — remboursement d'échéance de crédit.
4. Selon le type, sélectionnez le **compte client** (recherche AJAX par matricule / nom / numéro de compte).
5. Saisissez le **montant** et la **devise**.
6. Le système affiche en temps réel :
   - la **commission** calculée (`CommissionEngine`) ;
   - le **total débit / crédit** ;
   - le **solde projeté** du compte client ;
   - le **solde projeté** du guichet.
7. Cliquez **Valider l'opération**.

Le système :

- Crée l'opération (`tb_caisses_transactions`).
- Met à jour le **solde du compte** et le **solde du guichet**.
- **Snapshot** la règle de commission appliquée (immuable).
- Génère les **écritures comptables OHADA** automatiquement.
- Émet une **notification** au trésorier en cas de seuil critique.
- Vous propose d'**imprimer le bordereau** PDF.

#### 7.3.2 Cas particuliers

**Change devise A → devise B** : saisir la devise source, le montant source, la devise cible. Le **taux du jour** (`tb_taux_change`) est appliqué automatiquement.

**Remboursement de crédit** : sélectionner l'**échéance** à régler. Le système ventile automatiquement entre **capital** et **intérêts**.

#### 7.3.3 Imprimer un bordereau

À tout moment, depuis le journal des opérations : icône **imprimante** sur la ligne → bordereau PDF officiel téléchargé.

### 7.4 Annuler ou modifier une opération

**Annulation directe** (`PER25`) : un caissier peut annuler une opération **du jour** dans les minutes suivant la saisie. L'annulation est tracée et notifie le trésorier (`PER44`).

**Demande de modification** (workflow contrôlé) :

1. Caisse → Mes opérations → ligne concernée → **Demander modification / suppression**.
2. Saisissez le **motif obligatoire**.
3. La demande est transmise au **responsable** (`PER44`).
4. Vous suivez le statut dans **Caisse → Mes demandes**.

**Côté responsable** (`PER44`) :

- Page **Caisse → Demandes de modification**.
- Liste des demandes en attente avec compteur dans le navbar.
- Boutons **Approuver** / **Rejeter** (motif requis).
- Notifications automatiques au demandeur.

### 7.5 Demander une dotation (approvisionnement)

**Route** : `/caisses/demande-appro` (PER10).

1. Caisse → **Demander une dotation**.
2. Saisissez la **devise**, le **montant souhaité** et un **motif**.
3. Validez.

La demande est transmise au **trésorier** (`PER46`). Vous suivez son traitement dans **Caisse → Mes demandes**. Une notification vous prévient en cas d'approbation ou de rejet.

### 7.6 Guichet mobile

Pour les **guichets MOBILES** (collecte sur le terrain) :

1. **Départ** (PER11) : `/caisses/mobile/depart` — vous recevez une **dotation initiale** du coffre (espèces + carnet de reçus).
2. **Opérations** : identiques au guichet fixe, mais limitées à votre **scoping de zone** (vous ne voyez que les membres de vos zones).
3. **Retour** (PER11) : `/caisses/mobile/retour` — vous **reversez** les espèces collectées au coffre et clôturez votre tournée.

> **Sécurité** : les agents mobiles n'ont **aucun droit d'impression** des documents membres ; ils n'utilisent que l'application sur tablette ou téléphone.

### 7.7 Clôturer le guichet

**Routes** :

- Initier : `/caisses/fermeture/initier/{id}` (PER11)
- Confirmer : `/caisses/fermeture/confirmer/{id}` (PER11)
- Page d'attente : `/caisses/fermeture/pendante` (PER11)

#### 7.7.1 Procédure

1. À la fin de votre journée, allez dans **Caisse → Clôture**.
2. Cliquez **Initier la clôture**.
3. Pour **chaque devise** présente dans le guichet :
   - le système affiche le **solde comptable** (calculé) ;
   - saisissez le **billetage** physique : nombre de billets / pièces par coupure ;
   - le **solde physique** est calculé automatiquement ;
   - l'**écart de caisse** s'affiche : `Écart = Physique − Comptable`.
4. Si l'écart dépasse la **tolérance** définie par règle, un **motif obligatoire** est requis.
5. Cliquez **Confirmer la clôture**.

#### 7.7.2 Validation par la trésorerie

La clôture passe en statut **EN ATTENTE**. Le trésorier (`PER46`) la vérifie ligne par ligne (devise par devise) :

- **Approuver** : la clôture passe en `VALIDE`, le guichet en `CLÔTURÉ`.
- **Rejeter** : motif obligatoire, le guichet reste en attente, vous êtes notifié.

> **Important** : un écart **positif** (excédent) doit faire l'objet d'une enquête immédiate. Un écart **négatif** (déficit) est imputable au caissier sauf justification validée par le gérant.

### 7.8 Journal et rapports

| Vue | Route | Permission |
|---|---|---|
| Journal de la journée | `/caisses/operations/journal` | PER10 |
| Rapport fin de journée | `/caisses/operations/rapport` | PER10 |
| Bordereau d'opération | `/caisses/operations/{id}/bordereau` | PER11 |

---

## 8. Module Trésorerie & Coffre central

Ce module est le **cœur financier** de l'institution. Il gère le **coffre central** (`COFFRE_01`, type `CENTRAL`) et toutes les circulations de fonds entre la banque externe, le coffre et les guichets.

**Permissions clés** :

- `EBEN-PER44` — Voir trésorerie (consultation).
- `EBEN-PER45` — Approvisionner le coffre.
- `EBEN-PER46` — **Approuver les décisions trésor** (demandes, clôtures).
- `EBEN-PER47` — Alimenter un guichet.
- `EBEN-PER77` — Gérer les commissions (création).
- `EBEN-PER78` — Modifier / activer-désactiver les commissions.

### 8.1 État du coffre central

**Route** : `/tresorerie/etat-coffre`.

Vue d'ensemble du coffre-fort :

- **Soldes par devise** (CDF, USD, EUR…).
- Onglets : *Mouvements · Alimentations · Balances · Statistiques*.
- Compteurs de demandes et de clôtures en attente.
- Graphiques d'évolution des soldes.

### 8.2 Approvisionner le coffre (banque externe → coffre)

**Route** : `/tresorerie/coffre/approvisionner` (PER45 ou PER77).

Lorsque la coopérative reçoit du **cash** d'une banque externe ou d'une opération de retrait :

1. Trésorerie → **Approvisionner le coffre**.
2. Saisissez **devise**, **montant**, **source** (banque externe, retrait BCEAO, transfert interne…).
3. Renseignez le **bordereau / référence bancaire** et un **commentaire**.
4. Validez.

Le système :

- Crée un mouvement de coffre type `ENTREE`.
- Incrémente le solde `COFFRE_01` dans la devise concernée.
- Notifie les utilisateurs avec `PER44`.

### 8.3 Alimenter un guichet (coffre → guichet)

**Route** : `/tresorerie/coffre/alimenter` (PER47 ou PER77).

1. Trésorerie → **Alimenter un guichet**.
2. Sélectionnez le **guichet** destinataire.
3. Saisissez **devise**, **montant**, **commentaire**.
4. Validez.

Le système :

- Débite `COFFRE_01` et crédite le **solde du guichet** dans la devise concernée.
- Crée un mouvement `MouvementInterCaisse`.
- Notifie l'**agent du guichet**.

### 8.4 Traiter les demandes d'approvisionnement

Lorsque les caissiers demandent une dotation, vous (rôle `PER46`) les voyez dans :

**Route** : `/tresorerie/coffre/demandes`.

1. Liste des demandes en attente, avec :
   - identité du guichet et de l'agent demandeur ;
   - devise et montant demandé ;
   - motif et date de la demande.
2. Pour chaque demande, choisissez :
   - **Approuver** → déclenche automatiquement l'alimentation et notifie l'agent.
   - **Rejeter** → motif obligatoire, notifie l'agent.

> **Note** : les permissions sont strictes. Seul un utilisateur avec `EBEN-PER46` peut approuver ou rejeter ; les anciennes permissions héritées (`EBEN-PER78` notamment) ont été retirées de ce flux.

### 8.5 Valider les clôtures de guichets

**Route** : `/tresorerie/coffre/clotures` (PER44) et endpoints d'approbation (PER46).

Les clôtures soumises par les caissiers s'accumulent ici. La validation se fait **ligne par ligne** (une ligne par devise) :

1. Sélectionnez la clôture.
2. Vérifiez pour chaque devise :
   - solde comptable ;
   - solde physique déclaré ;
   - écart constaté ;
   - motif éventuel.
3. **Approuver** la ligne ou **Rejeter** (motif).
4. Lorsque toutes les lignes sont approuvées, la clôture globale est validée.

### 8.6 Gérer les règles de commissions

**Route** : `/tresorerie/commissions` (PER44 voir, PER77 créer, PER78 modifier).

La table des règles permet de définir **comment** sont facturées les opérations.

#### 8.6.1 Structure d'une règle

| Champ | Description |
|---|---|
| Libellé | Nom métier de la règle |
| Code opération | `DEPOT`, `RETRAIT`, `CHANGE`, `PAIEMENT`, `REMBOURSEMENT` |
| Type guichet | `FIXE`, `MOBILE`, `CENTRAL` (vide = tous) |
| Type compte | `CC`, `RMB`, `GTC`, `DAT`, `EAV` (vide = tous) |
| Devise | Code ISO (vide = toutes) |
| Code zone | Optionnel |
| Portefeuille | Optionnel |
| Montant | Fixe (montant) ou pourcentage |
| Période | Date début / date fin |
| Active | Oui / Non |
| Priorité | 1 = plus haute |

#### 8.6.2 Création / modification

1. Trésorerie → Commissions → **+ Nouvelle règle**.
2. Remplissez les champs (les champs vides signifient *applicable à tout*).
3. Validez.

#### 8.6.3 Application des règles

À chaque opération, le moteur `CommissionEngine` :

1. Sélectionne **toutes les règles actives** qui matchent le contexte (opération, guichet, compte, devise, zone, portefeuille, période).
2. Applique la règle de **priorité la plus haute**.
3. **Snapshot** la règle dans `tb_transaction_commissions` (montant, paramètres figés).
4. L'historique reste **immuable** : modifier ou désactiver une règle n'affecte **pas** les opérations passées.

> **Bonne pratique** : ne jamais supprimer une règle ; préférer la désactiver (`Active = Non`) pour préserver l'historique.

---

## 9. Module Crédit

Le module Crédit gère le **cycle de vie complet** d'un dossier, de la demande au remboursement final. Le workflow est strictement séquentiel et chaque transition est contrôlée par une permission dédiée.

**Permissions principales** :

- `EBEN-PER53` — Voir liste crédits.
- `EBEN-PER54` — Créer une demande.
- `EBEN-PER56` — Soumettre la demande.
- `EBEN-PER57` — Voir le détail d'un dossier.
- `EBEN-PER58` — Analyser.
- `EBEN-PER60..63` — Valider niveau L1 / L2 / L3 / L4.
- `EBEN-PER61` — Chargé des opérations (affectation analyse).
- `EBEN-PER64` — Débloquer (décaissement).
- `EBEN-PER65` — Enregistrer un remboursement.
- `EBEN-PER66..69` — Annuler / Suspendre / Lever / Signaler suspect.
- `EBEN-PER70` — Rapport frais de déblocage.
- `EBEN-PER71` — Imprimer fiches / échéanciers.

### 9.1 Vue d'ensemble du workflow

```
Brouillon → Soumis → En analyse → Validation L1 → L2 → L3 → L4
        → Prêt à débloquer → Déblocage effectué → Remboursement en cours → Soldé
```

Actions transverses possibles à tout moment :

- **Annuler** (motif obligatoire).
- **Suspendre** / **Lever suspension**.
- **Signaler suspect** (fraude présumée) / **Lever la suspicion**.

### 9.2 Créer une demande de crédit

**Route** : `/credits/nouveau` (PER54) puis `POST /credits` (PER54).

1. Crédit → **+ Nouvelle demande**.
2. Recherchez le **membre** par matricule ou nom.
3. Sélectionnez le **compte de décaissement** (généralement `CC`).
4. Renseignez :
   - **Type de crédit** (commercial, agricole, scolaire, social…).
   - **Objet du crédit** (texte libre).
   - **Montant demandé** et **devise**.
   - **Durée** en mois.
   - **Taux d'intérêt mensuel** (selon barème).
   - **Garantie** : description, caution, hypothèque…
5. Joignez les **pièces** (CNI, attestation revenus, devis…).
6. Cliquez **Enregistrer comme brouillon**.

Le dossier est créé avec un **numéro unique** et le statut **Brouillon**.

### 9.3 Soumettre la demande

**Route** : `POST /credits/{dossier}/soumettre` (PER56 ou PER53).

Une fois les pièces complètes :

1. Ouvrez le dossier brouillon.
2. Cliquez **Soumettre**.
3. Le dossier passe en statut **Soumis**.
4. Une **notification** est envoyée au **chargé des opérations** (`PER61`).

### 9.4 Affecter le dossier à un analyste

**Route** : `POST /credits/{dossier}/affecter-analyse` (PER61).

Côté chargé des opérations :

1. Crédit → **Dashboard** ou **Supervision**.
2. Sélectionnez le dossier soumis.
3. Cliquez **Affecter à l'analyse** → choisissez l'**agent analyste**.
4. Validez.

L'analyste et le demandeur reçoivent chacun une notification.

### 9.5 Analyser le dossier

**Route** : `/credits/{dossier}/analyse` (PER58).

L'analyste produit son **rapport d'analyse** :

1. Crédit → mon dossier → **Onglet Analyse**.
2. Remplissez :
   - capacité de remboursement ;
   - exposition risque ;
   - solidité de la garantie ;
   - avis et recommandation (favorable / défavorable / sous conditions).
3. Soumettez.

Le dossier passe à **Validation L1**. Le validateur L1 (`PER60`) est notifié.

### 9.6 Valider le dossier (4 niveaux)

**Route** : `/credits/{dossier}/validation` (PER60 / PER61 / PER62 / PER63).

Chaque niveau peut **approuver** ou **rejeter** :

| Niveau | Permission | Rôle typique |
|---|---|---|
| L1 | PER60 | Validateur opérationnel |
| L2 | PER61 | Chef d'agence |
| L3 | PER62 | Comité de crédit |
| L4 | PER63 | Direction |

À l'approbation L4, le dossier passe à **Prêt à débloquer** et le **chargé du déblocage** (`PER64`) est notifié. En cas de rejet, le dossier est marqué rejeté et le demandeur est notifié.

### 9.7 Débloquer (décaisser) le crédit

**Route** : `/credits/{dossier}/deblocage` (PER64).

1. Crédit → **Déblocage** → dossier prêt.
2. Vérifiez le **montant approuvé** et la **date prévue**.
3. Saisissez les **frais de déblocage** (selon barème).
4. Confirmez le **mode de décaissement** : virement compte membre, espèces guichet.
5. Validez le **déblocage**.

Le système :

- Crédite le **compte du membre** du montant net (montant approuvé − frais).
- Génère automatiquement l'**échéancier** complet (`CreditEcheancier` + `CreditEcheance`).
- Crée les **écritures comptables OHADA** correspondantes.
- Émet le **bordereau de déblocage** PDF.
- Notifie le demandeur.

**Rapport des frais de déblocage** : `/credits/rapport-frais` (PER70).

### 9.8 Suivre les remboursements

**Route** : `/credits/{dossier}/remboursement` (PER65).

Le système maintient un **échéancier** ligne par ligne : date prévue, capital, intérêts, solde restant.

#### 9.8.1 Enregistrer un paiement

1. Crédit → dossier → **Remboursement**.
2. Sélectionnez l'**échéance** à payer (ou plusieurs).
3. Saisissez le **montant payé** et le **mode** (espèces guichet, virement, mobile money).
4. Validez.

Le système :

- Met à jour l'échéance (totalement / partiellement payée).
- Crédite la **comptabilisation des intérêts** (compte 7001).
- Met à jour le **solde restant dû** du dossier.
- Lorsque tout est soldé, passe le dossier au statut **Soldé**.

#### 9.8.2 Cas de retard

Une échéance impayée à la date prévue est marquée **En retard**. Des **pénalités** peuvent être appliquées selon la règle de commission `RETARD_CREDIT`.

### 9.9 Actions de contrôle

- **Annuler le dossier** (`POST /credits/{dossier}/annuler`, PER66) : motif obligatoire. Tracé.
- **Suspendre** (`POST /credits/{dossier}/suspendre`, PER67) : suspension temporaire (litige, vérification). Levable via PER69.
- **Signaler suspect** (`POST /credits/{dossier}/signaler-suspect`, PER68) : fraude présumée. Levable via PER69.

### 9.10 Supervision & rapports

- **Dashboard crédits** : `/credits/dashboard` (PER61..PER65) — vue agrégée.
- **Supervision** : `/credits/supervision` — tous les dossiers en cours par statut.
- **Échéancier PDF** : `/credits/{dossier}/pdf/echeancier` (PER71).
- **Fiche dossier PDF** : `/credits/{dossier}/pdf/fiche` (PER71).

### 9.11 Référence : matrice de notifications

Toutes les étapes émettent des **notifications** ciblées par rôle ou par agent. La matrice complète figure dans `docs/MATRICE_NOTIFICATIONS_CREDIT_2026-05-03.md`.

---

## 10. Module Comptabilité OHADA

Ce module fournit la **vision comptable** normalisée selon le plan OHADA. Toutes les écritures sont **générées automatiquement** par les modules opérationnels (Caisse, Crédit, Trésorerie) ; **aucune saisie manuelle** n'est possible depuis l'interface (immuabilité comptable).

**Permissions** :

- `EBEN-PER49` — Voir dashboard comptabilité.
- `EBEN-PER50` — Voir le journal.
- `EBEN-PER51` — Voir le plan comptable OHADA.
- `EBEN-PER52` — Voir le grand-livre.

### 10.1 Dashboard

**Route** : `/comptabilite` (PER49).

Vue d'ensemble :

- nombre d'écritures du jour / du mois ;
- soldes des principaux comptes (caisse, dépôts, intérêts) ;
- alertes (équilibre débit/crédit, écritures suspectes).

### 10.2 Plan comptable

**Route** : `/comptabilite/plan-ohada` (PER51).

Liste hiérarchique des comptes :

- **Classe 1** — Comptes de ressources durables (capital).
- **Classe 2** — Comptes d'actif immobilisé.
- **Classe 3** — Comptes de stocks.
- **Classe 4** — Comptes de tiers (clients, fournisseurs).
- **Classe 5** — Comptes financiers (caisse, banque).
  - `5701` Caisse CDF, `5702` Caisse USD…
- **Classe 6** — Charges.
- **Classe 7** — Produits.
  - `7001` Intérêts sur crédits, `7061` Commissions perçues.

### 10.3 Journal comptable

**Route** : `/comptabilite/journal` (PER50).

Liste chronologique de toutes les écritures :

- date et numéro d'écriture ;
- libellé ;
- compte débité / crédité ;
- montant ;
- référence à l'opération source (transaction caisse, déblocage crédit, etc.).

Filtres : période, compte, agent, devise.

### 10.4 Grand-livre

**Route** : `/comptabilite/grand-livre` (PER52).

Pour chaque compte :

- solde initial de la période ;
- liste des mouvements (débit / crédit) ;
- solde final.

### 10.5 Règles fondamentales

- **Double entrée** : Σ Débits = Σ Crédits pour chaque écriture.
- **Immuabilité** : aucune écriture ne peut être modifiée ou supprimée a posteriori. Une correction se fait par **contre-passation**.
- **Comptabilisation des commissions** : débit du compte de dépôt client, crédit du compte produit `7061`.
- **Comptabilisation des dépôts / retraits** : impacte le compte client et le compte caisse correspondant, **sans incrémenter automatiquement** le coffre central.

---

## 11. Module Ressources Humaines

Ce module gère le **personnel** de la coopérative : agents, services, postes et affectations.

**Permissions** :

- `EBEN-PER6` — Voir RH (agents, services, postes).
- `EBEN-PER7` — Créer un agent.
- `EBEN-PER8` — Créer service / poste.
- `EBEN-PER9` — Voir / créer affectations.
- `EBEN-PER103` — Modifier agent / service / poste.
- `EBEN-PER104` — Supprimer agent / service / poste.
- `EBEN-PER105` — Changer l'état d'une affectation.
- `EBEN-PER106` — Supprimer une affectation.

### 11.1 Agents

**Route** : `/rh/agents` (PER6).

#### Données d'un agent

| Champ | Description |
|---|---|
| Matricule | Identifiant unique auto-généré (`AG-EBENKGA-YY-XXXXX`), **clé primaire** |
| Nom · Postnom · Prénom | État civil |
| Sexe, date de naissance | Informations personnelles |
| Téléphone, e-mail, adresse | Coordonnées |
| Photo | Stockée dans `images_projet/agents/` |
| Date d'embauche | Date d'entrée |
| Statut | `ACTIF`, `SUSPENDU`, `RETRAITÉ` |

#### Créer un agent

1. RH → Agents → **+ Nouvel agent**.
2. Renseignez les informations.
3. Téléversez la photo.
4. Validez. Le matricule est généré automatiquement.

> **Lien avec les utilisateurs** : un agent **peut** (ou non) être lié à un **compte utilisateur** (login). L'association se fait depuis Administration → Utilisateurs en saisissant le matricule.

### 11.2 Services et postes

**Routes** :

- Services : `/rh/services` (PER6, PER8 créer, PER103 éditer).
- Postes : `/rh/services/{service}/postes` (PER6, PER8 créer).

Un **service** est un département (Caisse, Crédit, Comptabilité, Direction…) qui regroupe des **postes** (fonctions : Caissier, Chargé de crédit, Analyste, Comptable…).

Création :

1. RH → Services → **+ Nouveau service**.
2. Cliquez sur le service → **+ Nouveau poste** (modale AJAX).
3. Nom, description.
4. Validez.

### 11.3 Affectations

**Route** : `/rh/affectations` (PER9).

Une **affectation** lie un **agent** à une **ressource** (guichet, zone, portefeuille).

#### Caractéristiques

- **Historisée** : `tb_affectations`, `tb_affectations_zones`, `tb_affectations_portefeuilles`.
- Une seule affectation **ACTIVE** par couple (agent, ressource) à un instant donné.
- Trois états : `ACTIF`, `SUSPENDU`, `CLÔTURÉ` (`date_fin` renseignée).
- Toute modification crée une **nouvelle ligne** d'historique.

#### Créer une affectation

1. RH → Affectations → **+ Nouvelle affectation**.
2. Sélectionnez l'**agent**.
3. Choisissez la **ressource** (guichet OU zone OU portefeuille).
4. Indiquez la **date de début**.
5. Validez. L'affectation passe en `ACTIF`.

#### Changer l'état (PER105)

Bouton **Suspendre** ou **Clôturer** sur la ligne. Renseignez la date de fin et le motif.

> **Important** : le **scoping** des données pour les agents mobiles repose sur les affectations zones actives. La clôture d'une affectation retire **immédiatement** l'accès aux membres de cette zone.

---

## 12. Module Administration & RBAC

Module réservé aux **administrateurs système**. Il regroupe la gestion des **utilisateurs**, des **rôles**, des **permissions**, des **zones**, des **portefeuilles**, des **guichets**, des **devises** et des **taux de change**.

### 12.1 Gestion des utilisateurs

**Route** : `/administration/utilisateurs` (PER1).

#### Créer un utilisateur

1. Administration → Utilisateurs → **+ Nouvel utilisateur**.
2. Saisissez l'**e-mail** et le **mot de passe initial**.
3. Saisissez le **matricule agent** : le système récupère automatiquement (AJAX) les informations de l'agent.
4. Attribuez un ou plusieurs **rôles**.
5. Validez.

L'utilisateur reçoit un e-mail de vérification et doit changer son mot de passe à la première connexion.

#### Actions

- **Modifier** : ajuster e-mail, rôles, statut actif.
- **Réinitialiser mot de passe** : envoie un nouveau mot de passe temporaire.
- **Désactiver** : bloque la connexion sans supprimer l'historique.
- **Supprimer** : irréversible, refusée si l'utilisateur a des écritures comptables.

### 12.2 Gestion des rôles et permissions (RBAC)

**Route** : `/administration/roles-permissions` (PER2 voir, PER3 gérer, PER5 attacher permissions).

#### 12.2.1 Onglet Rôles

- Liste des rôles avec le nombre de permissions et le nombre d'utilisateurs rattachés.
- **+ Nouveau rôle** : code, nom, description.
- Pour chaque rôle, bouton **Permissions** ouvre une modale avec les ≈ 100 permissions atomiques regroupées par module : cocher / décocher pour attacher / détacher.

#### 12.2.2 Onglet Permissions (lecture seule)

Liste des permissions atomiques avec leur **code** (`EBEN-PER01` à `EBEN-PER108`) et leur description. Les permissions sont **statiques** (définies par le développeur via migration) ; seul leur rattachement à des rôles est paramétrable depuis l'interface.

#### 12.2.3 Onglet Utilisateurs

- Liste des comptes avec leurs rôles.
- Bouton **Rôles** sur chaque utilisateur : modale d'attachement / détachement multiple.

> **Principe RBAC** : un utilisateur hérite des permissions de **tous ses rôles** (union). La vérification `Auth::user()->hasPermissionTo('EBEN-PER10')` est appliquée à chaque route protégée. Aucun fallback générique n'existe — la permission **précise** est exigée.

### 12.3 Référentiel des permissions (extrait)

| Groupe | Codes | Domaine |
|---|---|---|
| Administration | PER01–05 | Utilisateurs, rôles, permissions, zones, guichets |
| RH | PER06–09, PER103–106 | Agents, services, postes, affectations |
| Caisse | PER10–14, PER25, PER44 | Guichet, opérations, demandes modification |
| Clients | PER15–17, PER107 | Membres |
| Comptes | PER18–19, PER108 | Comptes clients |
| Devises | PER20–21 | Devises et taux de change |
| Trésorerie | PER44–48, PER76–78 | Coffre, alimentations, commissions |
| Comptabilité | PER49–52 | Plan, journal, grand-livre |
| Crédit | PER53–72 | Cycle complet du crédit |

> La liste complète et le détail métier figurent dans `docs/permissions_referentiel.md` et `docs/structure_roles_permissions.md`.

### 12.4 Gestion des zones et portefeuilles

**Route** : `/administration/zones-portfeuille` (PER1, onglets *Zones* / *Portefeuilles*).

#### Zones

- Code zone unique (ex : `Z-KIN-01`).
- Nom, description, périmètre géographique.
- Création / modification / suppression.
- **Affectation à un agent** : crée une ligne `tb_affectations_zones` active (historisée).

#### Portefeuilles

- Création possible **sans agent** ; affectation ultérieure.
- Historisation dans `tb_affectations_portefeuilles` et `tb_portefeuille_historiques`.
- Réaffectation possible — toutes les anciennes assignations sont conservées pour l'audit.

### 12.5 Gestion des guichets

**Route** : `/administration/guichets` (PER1).

CRUD complet :

- Code guichet (`GUI_01`, `MOB_KIN_03`, `COFFRE_01`…).
- Libellé.
- Type (`FIXE`, `MOBILE`, `CENTRAL`).
- Devises supportées (ajout via bouton dédié).
- Statut.

Pages annexes :

- **Historique des alimentations** : `/administration/guichets/alimentations`.
- **Balances du coffre** : `/administration/guichets/coffre-balances`.

### 12.6 Gestion des devises et taux de change

**Route** : `/administration/devises-taux` (PER20 voir, PER21 gérer).

#### Onglet Devises

CRUD : **code ISO** (CDF, USD, EUR, XAF…), libellé, symbole, devise par défaut.

#### Onglet Taux de change

- Saisie du taux du jour : devise source → devise cible, taux, date d'effet.
- Le **taux le plus récent** est appliqué automatiquement aux nouvelles opérations.
- Historique intégral conservé (jamais supprimé).

> **Important** : modifier un taux n'affecte **pas** les opérations passées. Les commissions et conversions sont **snapshotées** au moment de la transaction.

---

## 13. Centre de notifications

**Route principale** : `/notifications` (auth requis).

Le centre de notifications est une **boîte de réception métier** : il regroupe tous les événements qui vous concernent personnellement ou qui requièrent une action de votre rôle.

### 13.1 Accès rapide

- **Cloche** dans la navbar :
  - badge **rouge** = nombre total non lu ;
  - badge **orange** = sous-ensemble nécessitant une **action** (`action_required`).
- Clic → menu déroulant des 5 dernières notifications + lien **Voir tout**.

### 13.2 Page complète

- Liste paginée avec filtres (Toutes · Non lues · Action requise).
- Chaque notification affiche : icône typée, titre, contexte, date, agent / module source.
- Cliquer sur une notification : la marque comme **lue** et redirige vers la page d'action correspondante.
- Bouton **Marquer tout comme lu** disponible en haut de la liste.

### 13.3 Types de notifications

| Type | Couleur | Exemple |
|---|---|---|
| `info` | Bleu | « Coffre approvisionné de 5 000 USD » |
| `warning` | Orange | « Demande de modification d'opération » |
| `action_required` | Rouge | « Dossier crédit en attente de validation L2 » |
| `danger` | Rouge foncé | « Suspicion de fraude signalée » |

### 13.4 Origines des notifications

Les notifications sont émises automatiquement par les services métier. Les principales sources :

- **Crédit** (`CreditController`) : soumission, affectation, analyse, validation, déblocage, annulation, suspension, suspicion.
- **Caisse** (`CaisseController`, `OperationCaisseController`) : changement de statut guichet, clôture, demande d'approvisionnement, demande de modification, annulation, mobile départ/retour.
- **Trésorerie** (`TresorerieController`) : approvisionnement coffre, alimentation guichet, approbation/rejet de demande, approbation/rejet de clôture (globale ou ligne).
- **Administration** (`RolesPermissionsController`) : attachement / détachement de permissions ou de rôles.

### 13.5 Ciblage des destinataires

Le `NotificationService` permet de cibler :

- un **utilisateur** précis ;
- tous les utilisateurs ayant une **permission** donnée (par exemple tous les `PER46` pour les décisions trésor) ;
- un ou plusieurs **agents** par leurs matricules (utile pour notifier l'agent demandeur).

### 13.6 Référence

Les matrices complètes des flux et destinataires figurent dans :

- `docs/MATRICE_NOTIFICATIONS_SYSTEME_COMPLET_2026-04-28.md`
- `docs/MATRICE_NOTIFICATIONS_CREDIT_2026-05-03.md`
- `docs/MATRICE_NOTIFICATIONS_TRESORERIE_CAISSE_2026-05-03.md`

---

## 14. Mon profil

**Route** : `/profile` (auth).

Chaque utilisateur peut gérer son propre profil :

- **Informations personnelles** : nom affiché, e-mail (sous réserve d'une nouvelle vérification).
- **Mot de passe** : changement avec confirmation du mot de passe actuel.
- **Préférences** : thème clair / sombre (mémorisé).
- **Suppression de compte** : action irréversible, requiert confirmation par mot de passe ; refusée si l'utilisateur a des écritures comptables liées.

> **Bonne pratique** : changez votre mot de passe initial dès la première connexion et tous les 90 jours minimum.

---

## 15. Procédures types par rôle

### 15.1 Caissier — Journée type

1. **Connexion** au matin.
2. **Ouvrir** le guichet (Caisse → Ouverture).
3. Si solde insuffisant : **Demander une dotation** (Caisse → Demander appro).
4. Enregistrer les **opérations** au fil de la journée (dépôts, retraits, change, paiements, remboursements).
5. **Imprimer les bordereaux** à chaque opération sur demande du client.
6. En cas d'erreur : **annuler** ou **demander modification**.
7. En fin de journée : **Clôturer** le guichet, billetage par devise.
8. Attendre la **validation** par le trésorier.
9. **Déconnexion**.

### 15.2 Trésorier — Journée type

1. **Connexion**.
2. Consulter l'**état du coffre** et les compteurs (demandes, clôtures).
3. **Approvisionner le coffre** si arrivée de cash externe.
4. **Traiter les demandes d'approvisionnement** des guichets.
5. **Valider les clôtures** soumises par les caissiers.
6. Suivre l'évolution des **soldes par devise** et des **commissions**.
7. Ajuster les **règles de commission** si nécessaire.

### 15.3 Chargé de crédit — Journée type

1. **Connexion**.
2. Recevoir un membre : **créer une demande de crédit**.
3. Vérifier les pièces, **soumettre** la demande.
4. Suivre l'avancement via le **dashboard crédits**.
5. Recevoir les notifications de validation / rejet.
6. Lors du déblocage : remettre le **bordereau** au membre.
7. Suivre les **remboursements** et relancer les retards.

### 15.4 Administrateur — Tâches récurrentes

- Créer / désactiver des **utilisateurs** au gré des arrivées et départs.
- Mettre à jour les **rôles** et leurs permissions.
- Saisir les **taux de change** quotidiens.
- Créer les **zones**, **portefeuilles** et **guichets** au fil de l'expansion.
- Suivre le **centre de notifications** pour les alertes système.

### 15.5 Matrice synthétique des rôles

| Rôle | Modules principaux | Permissions clés |
|---|---|---|
| Super Admin | Tous | Toutes |
| Administrateur | Administration, RH | PER01–09, PER103–106 |
| Caissier | Caisse | PER10–14 |
| Trésorier | Trésorerie | PER44–48, PER76–78 |
| Chargé clientèle | Comptes Clients | PER15–19 |
| Chargé de crédit | Crédit | PER53–57, PER65 |
| Analyste crédit | Crédit | PER57, PER58 |
| Validateur L1/L2/L3/L4 | Crédit | PER60 / PER61 / PER62 / PER63 |
| Chargé déblocage | Crédit | PER64, PER70 |
| Comptable | Comptabilité | PER49–52 |
| Gestionnaire RH | RH | PER06–09, PER103–106 |
| Agent mobile | Comptes Clients, Caisse | PER10–11, PER15 (scopé zone) |

---

## 16. Bonnes pratiques & sécurité

### 16.1 Sécurité des accès

- **Mot de passe fort** : 8 caractères minimum, mixte (lettres, chiffres, symboles).
- **Ne jamais partager** son compte. Chaque opération est tracée nominativement.
- **Se déconnecter** systématiquement en quittant son poste.
- **Vérifier l'URL** : `https://coopaeben.info/` exclusivement. Ne jamais saisir ses identifiants sur une URL différente.

### 16.2 Sécurité opérationnelle

- **Vérifier deux fois** chaque montant avant validation : les opérations sont difficiles à annuler après transmission à la comptabilité.
- **Garder un trace papier** des bordereaux jusqu'à la clôture de la journée.
- **Compter physiquement** la caisse à l'ouverture et à la clôture.
- **Signaler immédiatement** tout écart inexpliqué, toute opération suspecte ou tout dysfonctionnement.

### 16.3 Protection des données membres

- Les informations personnelles des membres sont **confidentielles**.
- **Ne jamais imprimer** un document membre sans nécessité opérationnelle.
- **Détruire** les bordereaux annulés à l'aide d'un destructeur.
- Les agents **mobiles** ne disposent **pas** des fonctions d'impression : c'est volontaire.

### 16.4 Discipline comptable

- Les écritures sont **immuables**. Une erreur se corrige par **contre-passation**, jamais par modification.
- Les règles de commission ne doivent **jamais être supprimées** : préférer la désactivation.
- Les **taux de change** doivent être saisis chaque jour ouvré, idéalement avant l'ouverture des guichets.

---

## 17. Dépannage (FAQ)

### 17.1 Je n'arrive pas à me connecter

- Vérifiez la **casse** du mot de passe.
- Vérifiez que votre compte est **actif** (sinon contactez l'administrateur).
- Vérifiez que votre e-mail est **vérifié** (lien envoyé à la création).
- Après 6 tentatives échouées, attendez quelques minutes.

### 17.2 Le menu ne montre pas le module attendu

- Votre rôle ne dispose pas de la **permission** requise. Demandez à l'administrateur d'ajuster votre rôle.

### 17.3 « Aucun guichet ouvert » lors d'une opération

- Vous n'avez pas **ouvert** votre guichet, ou il a été **suspendu / clôturé**.
- Allez dans **Caisse → Ouverture** et ouvrez-le.

### 17.4 « Solde insuffisant »

- Pour un **retrait** : le solde disponible du compte (solde réel − bloqué) est inférieur au montant demandé.
- Pour un **paiement guichet** : le solde du guichet dans la devise est insuffisant ; demandez une dotation au trésorier.

### 17.5 Une commission n'apparaît pas

- Vérifiez la règle dans **Trésorerie → Commissions** : statut Active, période valide, scope correspondant à l'opération.
- L'aperçu temps réel sur l'écran d'opération doit afficher la commission **avant** validation.

### 17.6 Mon dossier de crédit est bloqué

- Vérifiez son **statut** dans le détail (analyse en cours, attente validation Lx, suspendu, signalé suspect…).
- Les notifications indiquent le **prochain acteur** attendu. Contactez-le si besoin.

### 17.7 Je vois moins de membres que mon collègue

- Vous êtes en **mobile guichet** et votre périmètre est **scopé** par vos zones d'affectation actives.
- Demandez à votre gestionnaire RH de mettre à jour vos **affectations zones**.

### 17.8 La session se ferme tout le temps

- La session expire après **10 minutes d'inactivité** (paramètre `session.inactivity_timeout`). Tant que vous interagissez, le **heartbeat** maintient la session active.
- Demandez à l'administrateur d'augmenter le paramètre si nécessaire.

### 17.9 Un PDF ne se génère pas

- Cause fréquente : permission `dompdf` insuffisante sur `storage/`.
- Voir `PHP_CONFIG_DOMPDF.conf` et le script `VERIFY_PDF_FIX.sh`.
- Contactez l'administrateur.

---

## 18. Glossaire métier

- **Coopec** — Coopérative d'Épargne et de Crédit.
- **Membre** — Adhérent de la coopérative ; équivalent du client.
- **Matricule** — Identifiant unique attribué à chaque membre ou agent.
- **Compte** — Compte bancaire d'un membre, identifié par `code_compte`.
- **CC / DAT / EAV / RMB / GTC** — Types de comptes (Courant, Dépôt À Terme, Épargne & Avantages, Remboursement, Garantie).
- **Guichet** — Point de service (FIXE, MOBILE, CENTRAL).
- **Coffre central** — Guichet de type `CENTRAL` qui centralise les fonds (`COFFRE_01`).
- **Dotation / Approvisionnement** — Transfert de fonds du coffre vers un guichet.
- **Clôture de caisse** — Vérification et arrêt des comptes en fin de journée pour un guichet, devise par devise.
- **Écart de caisse** — Différence entre solde physique compté et solde comptable.
- **Commission** — Frais bancaires facturés sur une opération.
- **OHADA** — Organisation pour l'Harmonisation en Afrique du Droit des Affaires ; définit le plan comptable utilisé.
- **Plan comptable** — Liste structurée des comptes de la comptabilité.
- **Écriture comptable** — Enregistrement d'un mouvement comptable (débit / crédit).
- **Grand-livre** — Récapitulatif des écritures par compte.
- **Dossier de crédit** — Demande de prêt avec tout son cycle de vie.
- **Échéancier** — Plan de remboursement d'un crédit, ligne par échéance.
- **Validation L1 / L2 / L3 / L4** — Quatre niveaux hiérarchiques d'approbation d'un dossier de crédit.
- **Déblocage / Décaissement** — Mise à disposition du montant approuvé sur le compte du membre.
- **Zone** — Découpage géographique pour la gestion commerciale.
- **Portefeuille** — Groupe de membres/comptes gérés par un agent.
- **Affectation** — Lien historisé entre un agent et une ressource (zone, guichet, portefeuille).
- **RBAC** — *Role-Based Access Control*, contrôle d'accès basé sur les rôles.
- **Permission** — Droit atomique d'effectuer une action (`EBEN-PERxx`).
- **Rôle** — Groupe nommé de permissions, attribué à un utilisateur.
- **Snapshot** — Photo immuable d'un paramètre (commission, taux) au moment d'une opération.

---

## 19. Annexes

### 19.1 Index des routes principales

| Module | Préfixe |
|---|---|
| Authentification | `/login`, `/register`, `/forgot-password` |
| Tableau de bord | `/dashboard` |
| Comptes Clients | `/comptes-clients/clients`, `/comptes-clients/comptes` |
| Caisse | `/caisses/*` |
| Trésorerie | `/tresorerie/*` |
| Crédit | `/credits/*` |
| Comptabilité | `/comptabilite/*` |
| RH | `/rh/*` |
| Administration | `/administration/*` |
| Notifications | `/notifications` |
| Profil | `/profile` |

### 19.2 Index des permissions clés

| Code | Action |
|---|---|
| EBEN-PER01 | Administration des utilisateurs |
| EBEN-PER02..05 | RBAC : voir, gérer rôles & permissions |
| EBEN-PER06..09 | RH : agents, services, postes, affectations |
| EBEN-PER10 | Voir caisse |
| EBEN-PER11 | Opérer la caisse |
| EBEN-PER15..17 | Membres : voir, créer, modifier |
| EBEN-PER18..19 | Comptes clients : voir, gérer |
| EBEN-PER20..21 | Devises et taux |
| EBEN-PER25 | Annulation transactions |
| EBEN-PER44 | Voir trésorerie / approuver demandes modif |
| EBEN-PER45 | Approvisionner coffre |
| EBEN-PER46 | Approuver décisions trésor (clôtures, demandes) |
| EBEN-PER47 | Alimenter un guichet |
| EBEN-PER49..52 | Comptabilité OHADA |
| EBEN-PER53..72 | Crédit (cycle complet) |
| EBEN-PER76 | Voir agents de terrain |
| EBEN-PER77 | Créer règles de commission |
| EBEN-PER78 | Modifier règles de commission |
| EBEN-PER103..108 | Modifier / supprimer (RH, clients, comptes) |

### 19.3 Documentation interne complémentaire

- `README.md` — Installation et déploiement.
- `SCHEDULER_SETUP.md` — Configuration des tâches planifiées.
- `docs/permissions_referentiel.md` — Référentiel complet des permissions.
- `docs/structure_roles_permissions.md` — Structure et matrice rôles/permissions.
- `docs/rbac_dynamique_exemple.md` — Exemples d'utilisation du RBAC.
- `docs/DEPLOIEMENT_ZONES_PORTEFEUILLES_2026-03-21.md` — Déploiement des zones et portefeuilles.
- `docs/MATRICE_NOTIFICATIONS_SYSTEME_COMPLET_2026-04-28.md` — Matrice générale des notifications.
- `docs/MATRICE_NOTIFICATIONS_CREDIT_2026-05-03.md` — Notifications du module Crédit.
- `docs/MATRICE_NOTIFICATIONS_TRESORERIE_CAISSE_2026-05-03.md` — Notifications Trésorerie / Caisse.

### 19.4 Contact & support

- **Site institutionnel** : https://coopaeben.info/
- **Éditeur** : Nexus BMB Tech
- **Support** : selon le canal défini avec l'institution (e-mail support, hotline interne).

---

*Fin du manuel.*

*Manuel Utilisateur — Coopec EBEN · v3.0.0 · © 2026 Nexus BMB Tech — Tous droits réservés.*
