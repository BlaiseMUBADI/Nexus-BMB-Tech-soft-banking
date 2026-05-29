# Manuel Utilisateur — Station BOPEL

**Logiciel** : Nexus BMB Tech Petro — Gestion de station-service
**Version** : 1.0.0
**Date** : Mai 2026
**Éditeur** : Nexus BMB Tech
**Public visé** : Pompistes, Caissiers, Magasiniers, Techniciens, Gérants, Super Administrateurs

---

## Sommaire

1. [Introduction](#1-introduction)
2. [Prérequis & accès](#2-prérequis--accès)
3. [Premiers pas — Connexion & déconnexion](#3-premiers-pas--connexion--déconnexion)
4. [Présentation de l'interface](#4-présentation-de-linterface)
5. [Tableau de bord](#5-tableau-de-bord)
6. [Module Piste](#6-module-piste)
7. [Module Shifts (caisse pompiste)](#7-module-shifts-caisse-pompiste)
8. [Module POS (boutique)](#8-module-pos-boutique)
9. [Module Stocks](#9-module-stocks)
10. [Module Tiers](#10-module-tiers)
11. [Module Comptabilité](#11-module-comptabilité)
12. [Module Administration](#12-module-administration)
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

**Nexus BMB Tech Petro** est une application web complète de gestion pour les stations-service. Elle couvre l'ensemble du cycle d'exploitation :

- Suivi des **pompes**, des **cuves** et des **stocks de carburant** (FIFO par lot, COGS automatique).
- Gestion des **shifts** (postes) des pompistes avec calcul d'écart de caisse.
- Point de vente **(POS)** pour la boutique.
- **Stocks boutique** (produits, mouvements, ajustements).
- **Tiers** : clients, véhicules, bons carburant B2B, fournisseurs.
- **Comptabilité** : dépenses, trésorerie, marges, rapports.
- **Administration** : rôles & permissions (RBAC), prix, devises, personnel, maintenance, journal d'audit complet.

### 1.2 Architecture résumée

| Composant | Détail |
|---|---|
| Cadriciel | Laravel 13.x |
| PHP | 8.3.28 |
| Base de données | MySQL 8.4 (`bdd_nexus_bmb_tech_petro`) — toutes les tables préfixées `tb_*` |
| Interface | AdminLTE 3 + Tailwind utilitaires |
| Authentification | Laravel Breeze (sessions) |

> **Note** : ce manuel décrit l'usage fonctionnel. Pour la mise en place technique (installation, déploiement, migrations), reportez-vous au `README.md` du projet.

---

## 2. Prérequis & accès

### 2.1 Matériel recommandé

- **Poste pompiste / caissier** : tablette tactile 10" ou PC avec écran ≥ 15", clavier physique pour la saisie d'index.
- **Poste gérant** : PC avec écran 22", souris.
- **Imprimante ticket** (optionnel) : USB ou réseau, format 80 mm.
- **Réseau** : WiFi local stable ou Ethernet ; la station doit avoir accès au serveur WAMP/Apache.

### 2.2 Navigateur

| Navigateur | Version minimale | Statut |
|---|---|---|
| Microsoft Edge | 110+ | Recommandé |
| Google Chrome | 110+ | Recommandé |
| Mozilla Firefox | 110+ | Supporté |
| Internet Explorer | toutes | Non supporté |

JavaScript et les cookies doivent être activés.

### 2.3 URL d'accès

L'application est accessible à l'adresse suivante (à adapter selon votre installation) :

```
http://localhost/Nexus-BMB-Tech-Petro/public/login
```

> **Conseil** : créez un raccourci sur le bureau pointant directement vers `/login` pour les postes opérationnels.

### 2.4 Comptes et rôles

Chaque utilisateur possède **un compte** rattaché à un ou plusieurs **rôles** (Pompiste, Caissier, Magasinier, Technicien, Auditeur, Gérant, Super Admin). Les permissions sont strictement filtrées par rôle (voir matrice § 15.1).

---

## 3. Premiers pas — Connexion & déconnexion

### 3.1 Se connecter

1. Ouvrez votre navigateur et saisissez l'URL d'accès.
2. La page **Connexion** s'affiche.
3. Renseignez **soit votre nom d'utilisateur, soit votre adresse e-mail**.
4. Saisissez votre **mot de passe**.
5. Cochez éventuellement *Se souvenir de moi*.
6. Cliquez sur **Se connecter**.

> ![Capture : page de connexion](images/01-login.png)

**En cas d'échec** : un message rouge s'affiche. Vérifiez la casse du mot de passe, l'absence de verrouillage majuscule, et contactez votre gérant si trois tentatives consécutives échouent.

### 3.2 Se déconnecter

- Cliquez sur votre **avatar** en haut à droite → **Déconnexion**.
- Une déconnexion automatique se produit après **120 minutes d'inactivité** (configurable).

### 3.3 Réinitialiser le mot de passe

Seul le **Super Admin** peut réinitialiser un mot de passe utilisateur depuis le module *Administration → RBAC*. Contactez-le en cas d'oubli.

---

## 4. Présentation de l'interface

L'interface est structurée en quatre zones :

```
+---------------------------------------------------------+
|  Top Navbar  : recherche . cloche notifs . avatar       |
+----------+----------------------------------------------+
|          |                                              |
| Sidebar  |           Zone de contenu (modules)          |
| (menu)   |                                              |
|          |                                              |
+----------+----------------------------------------------+
|                  Footer . (c) Nexus BMB Tech            |
+---------------------------------------------------------+
```

### 4.1 La barre supérieure (navbar)

- **Logo BOPEL** à gauche, replie ou déplie la sidebar.
- **Recherche** globale (à venir).
- **Cloche de notifications** : affiche en rouge le nombre de messages non lus. Cliquez pour voir les 5 derniers.
- **Avatar utilisateur** : menu déroulant *Profil · Déconnexion*.

### 4.2 La sidebar (menu latéral)

Le menu liste **uniquement les modules autorisés** par votre rôle. Chaque entrée principale peut contenir des sous-entrées (cliquez pour développer). L'élément actif est surligné en couleur primaire.

### 4.3 Le thème clair / sombre

- L'application supporte le mode **clair** (par défaut) et le mode **sombre**.
- Le mode peut être basculé manuellement depuis votre **Profil**.
- L'application mémorise votre choix.

### 4.4 Les fenêtres modales

Toutes les opérations de création / modification (ajouter une pompe, créer un bon, etc.) s'ouvrent dans une **fenêtre modale** par-dessus la page courante. Cliquez en dehors ou sur la croix pour fermer sans enregistrer.

### 4.5 Les messages système

- **Vert** : opération réussie (« Enregistré avec succès »).
- **Orange** : avertissement (« Stock critique »).
- **Rouge** : erreur (« Champ obligatoire manquant »).
- Les messages disparaissent automatiquement après 5 secondes.

---

## 5. Tableau de bord

**Route** : `/dashboard`
**Accès** : tous les rôles connectés.

Le tableau de bord est la page d'accueil après connexion. Il affiche :

- **KPIs du jour** : ventes carburant, ventes boutique, encaissements, marge brute.
- **Indicateurs cuves** : niveau en pourcentage, alertes seuil bas.
- **Shifts actifs** : pompistes en poste.
- **Bons carburant** émis du jour.
- **Dernières activités** (extrait du journal d'audit).

> ![Capture : tableau de bord](images/02-dashboard.png)

> **Astuce** : tous les graphiques sont **interactifs** — survolez pour voir les détails, cliquez pour filtrer.

---

## 6. Module Piste

Le module Piste regroupe la gestion physique des **pompes** et des **jauges manuelles** sur cuves.

### 6.1 Sous-module Pompes

**Route** : `/piste/pompes`

#### 6.1.1 Lister les pompes

À l'ouverture, vous voyez un tableau listant toutes les pompes :

| Colonne | Description |
|---|---|
| N° pompe | Numéro physique (1, 2, 3…) |
| Cuve rattachée | Identifiant de la cuve alimentant la pompe |
| Type carburant | Gasoil, Essence, Pétrole lampant… |
| Statut | Active · En panne · Hors service |
| Dernier index | Dernier relevé connu |
| Actions | Modifier · Activer/Désactiver |

#### 6.1.2 Créer une pompe

1. Cliquez sur **+ Nouvelle pompe** en haut à droite.
2. Renseignez : numéro, cuve, type carburant, index initial.
3. Cliquez **Enregistrer**.

> **Important** : l'index initial doit correspondre à la valeur affichée physiquement sur le compteur de la pompe au moment de la mise en service.

#### 6.1.3 Modifier ou désactiver une pompe

- **Modifier** : icône crayon → ajustez les informations → Enregistrer.
- **Basculer le statut** : interrupteur dans la colonne *Statut*. Une pompe **En panne** ou **Hors service** ne peut plus être sélectionnée lors d'un shift.

### 6.2 Sous-module Jaugeage manuel

**Route** : `/piste/jaugeage`

Permet de saisir un relevé physique à la perche (contrôle hebdomadaire ou réception de carburant).

#### Procédure

1. Cliquez sur **+ Nouveau jaugeage**.
2. Sélectionnez la **cuve**.
3. Saisissez la **hauteur mesurée en centimètres**.
4. Renseignez la **température** (°C) et une **observation** (optionnel).
5. Cliquez **Enregistrer**.

Le système :

- Convertit automatiquement la hauteur en **litres** via la table de barème (`tb_bareme_cuves`).
- Calcule **l'écart** par rapport au stock théorique (ventes – réceptions).
- Stocke l'historique des 50 derniers jaugeages.

> **Note** : un écart > 1,5 % doit déclencher une enquête (fuite, erreur d'index, vol).

---

## 7. Module Shifts (caisse pompiste)

**Route principale** : `/shifts`

Un **shift** représente un poste de travail (matin / soir / nuit) d'un pompiste, avec ouverture, ventes et clôture.

### 7.1 Vue d'ensemble

| Vue | Route | Description |
|---|---|---|
| Liste historique | `/shifts` | Tous les shifts paginés |
| Shifts actifs | `/shifts/actifs` | Cartes des shifts en cours |
| Ouvrir un shift | `/shifts/create` | Formulaire d'ouverture |
| Détails | `/shifts/{id}` | KPIs + relevés + ventes |
| Clôturer | `/shifts/{id}/close` | Formulaire de clôture |

### 7.2 Ouvrir un shift

1. Allez dans **Shifts → Ouvrir un shift**.
2. Sélectionnez votre **nom dans la liste Personnel**.
3. Saisissez le **fond de caisse** (montant et devise).
4. Pour **chaque pompe**, saisissez **l'index physique au début de poste**.
5. Cliquez **Démarrer le shift**.

> **Avertissement** : un index incorrect fausse tout le calcul théorique. Vérifiez deux fois avant de valider.

Le shift passe au statut **Ouvert**. Vous pouvez désormais enregistrer des ventes.

### 7.3 Pendant le shift

- Toutes les **ventes carburant** (relevés) sont rattachées à votre shift ouvert.
- Toutes les **ventes boutique** au POS sont également rattachées.
- Vous pouvez consulter les détails à tout moment via *Shifts actifs*.

### 7.4 Clôturer un shift

1. À la fin de votre poste, allez dans **Shifts → Shifts actifs**.
2. Cliquez **Clôturer** sur votre carte.
3. Saisissez pour chaque pompe **l'index physique final**.
4. Saisissez les **encaissements réels** :
   - Espèces (par devise)
   - Mobile money
   - Carte bancaire
   - Bons carburant utilisés
5. Cliquez **Clôturer définitivement**.

Le système calcule automatiquement :

```
Volume vendu  = index_final - index_ouverture
Theorique     = Somme (volume * prix actuel)
Ecart caisse  = Rapporte - Theorique
```

> Si l'écart dépasse le seuil toléré (par défaut ± 2 %), une **notification est envoyée au gérant**.

### 7.5 Statuts possibles

| Statut | Signification |
|---|---|
| Ouvert | En cours d'exploitation |
| Cloture | Clôturé, intangible |
| Annule | Annulé par le gérant (motif requis, audit) |

---

## 8. Module POS (boutique)

**Route** : `/pos`
**Accès** : Pompiste, Caissier, Gérant, Super Admin (sous condition d'un shift ouvert).

### 8.1 Interface

L'écran POS est conçu pour **tablette tactile** :

- **À gauche** : grille de produits avec image, nom, prix. Filtre par recherche en haut.
- **À droite** : le **panier** (liste des lignes), le total, les modes de paiement, le bouton **Encaisser**.

> ![Capture : POS](images/03-pos.png)

### 8.2 Effectuer une vente

1. **Filtrez** un produit en tapant son nom dans la barre de recherche.
2. **Cliquez** sur le produit : il s'ajoute au panier (quantité = 1).
3. **Ajustez la quantité** dans le panier (boutons + / -, ou saisie directe).
4. Sélectionnez la **devise** et le **mode de paiement** :
   - Espèces
   - Mobile money
   - Carte bancaire
   - Bon carburant (sélectionnez le bon dans la liste)
5. Cliquez sur **Encaisser**.

Le système :

- Crée la vente et ses lignes.
- Décrémente le stock des produits.
- Crée un **mouvement de stock** type *Sortie*.
- Affiche un **ticket** imprimable.

### 8.3 Consulter mes ventes du jour

**Route** : `/pos/mes-ventes`

Liste les ventes effectuées par votre compte sur le shift en cours, avec total cumulé.

### 8.4 Cas d'erreur fréquents

| Message | Cause | Solution |
|---|---|---|
| « Aucun shift ouvert » | Vous n'avez pas ouvert de shift | Allez dans Shifts → Ouvrir |
| « Stock insuffisant » | Quantité demandée > stock | Faire une réception ou ajustement |
| « Bon déjà utilisé » | Le bon a déjà servi | Sélectionner un autre bon |

---

## 9. Module Stocks

### 9.1 Stocks Carburants

**Route** : `/stocks/carburants`

Affiche pour chaque cuve :

- Un **indicateur visuel** (jauge en %) du niveau actuel.
- Le **volume théorique** (litres).
- Les **lots FIFO restants** avec date de réception, coût unitaire, taux de change.
- Une alerte rouge si le niveau passe sous le **seuil minimal**.

> **Concept FIFO** : chaque réception crée un *lot* indépendant. Les ventes consomment les lots du plus ancien au plus récent, ce qui permet de calculer la **marge brute exacte** (COGS = coût d'achat réel).

### 9.2 Stocks Boutique

**Route** : `/stocks/boutique`

#### 9.2.1 Lister les produits

Tableau des produits avec : code, nom, catégorie, prix, stock actuel, seuil min, statut.

#### 9.2.2 Créer un produit

1. Cliquez **+ Nouveau produit**.
2. Renseignez : code, nom, catégorie, prix d'achat, prix de vente, seuil minimal, stock initial.
3. **Enregistrer**.

#### 9.2.3 Ajuster le stock

L'ajustement sert lors d'un inventaire physique ou de la constatation d'une perte.

1. Sur la ligne du produit, cliquez **Ajuster (+/-)**.
2. Saisissez la **quantité** (positive ou négative) et un **motif obligatoire**.
3. **Valider**.

Un mouvement de type *Ajust* est créé et tracé dans le journal d'audit.

### 9.3 Réception de carburant

**Route** : `/stocks/reception`

À chaque livraison de citerne :

1. Sélectionnez la **cuve** destinataire.
2. Sélectionnez le **fournisseur**.
3. Saisissez : **quantité reçue (litres)**, **coût unitaire**, **devise**, **taux de change** (rempli automatiquement avec le taux du jour, modifiable).
4. Numéro **bon de livraison fournisseur** + observations.
5. **Enregistrer**.

Le système :

- Crée un **lot de carburant** (`tb_lots_carburant`).
- Incrémente le stock théorique de la cuve.
- Crée un mouvement de stock type *Entrée*.

### 9.4 Journal des mouvements

**Route** : `/stocks/mouvements`

Liste paginée filtrable de tous les mouvements (Entrée, Sortie, Ajust). Colonnes : date, type, produit/cuve, quantité, motif, utilisateur.

---

## 10. Module Tiers

### 10.1 Clients

**Route** : `/tiers/clients`

| Type | Champs principaux |
|---|---|
| Particulier | Nom, prénom, téléphone, plafond crédit |
| Entreprise | Raison sociale, NIF, contact, plafond crédit |

**Solde calculé en temps réel** : somme des bons utilisés non payés. Si solde > plafond → blocage des nouveaux bons.

#### Créer un client

1. **+ Nouveau client**.
2. Choisir le **type**.
3. Compléter les champs.
4. Définir le **plafond crédit** (0 = paiement comptant uniquement).
5. **Enregistrer**.

### 10.2 Véhicules clients

**Route** : `/tiers/vehicules`

- Plaque d'immatriculation **unique** (vérifiée automatiquement).
- Rattaché à un **client**.
- Type de carburant compatible.
- Interrupteur **Actif / Inactif**.

### 10.3 Bons carburant

**Route** : `/tiers/bons`

Les bons permettent à un client B2B de payer à crédit ou à l'avance.

#### Émettre un bon

1. **+ Nouveau bon**.
2. Sélectionner **client** + **véhicule** (optionnel).
3. Choisir le **type** :
   - **Volume max** (litres)
   - **Montant max** (devise)
4. Date d'émission, date d'expiration.
5. **Enregistrer**.

Le système attribue un **numéro automatique** au format `BC-yymmdd-NNNN` (ex : `BC-260522-0007`).

#### Utiliser un bon

Le bon est sélectionnable au moment de la vente (carburant ou POS). Une fois consommé, le statut passe à **Utilisé**.

#### Annuler un bon

Bouton **Annuler** sur la ligne. **Motif obligatoire**. Tracé dans le journal d'audit.

#### Statuts

| Statut | Description |
|---|---|
| Émis | Disponible |
| Utilisé | Consommé totalement |
| Annulé | Invalide, motif requis |
| Expiré | Date d'expiration dépassée |

### 10.4 Fournisseurs

**Route** : `/tiers/fournisseurs`

CRUD standard : raison sociale, NIF, téléphone, e-mail, adresse, solde compte.

---

## 11. Module Comptabilité

### 11.1 Dépenses

**Route** : `/compta/depenses`

Saisie des dépenses d'exploitation (électricité, salaires, fournitures, carburant…).

#### Créer une dépense

1. **+ Nouvelle dépense**.
2. Sélectionner **catégorie** (Loyer, Salaires, Énergie, Maintenance, Autres…).
3. Renseigner **fournisseur** (optionnel), **montant**, **devise**, **mode de paiement**.
4. **Date** + **observation**.
5. **Enregistrer**.

Toutes les dépenses sont **horodatées** et **non modifiables** après 24h (verrouillage comptable).

### 11.2 Trésorerie

**Route** : `/compta/tresorerie`

Vue agrégée :

- **Recettes** (ventes encaissées) par jour / semaine / mois.
- **Dépenses** par catégorie.
- **Solde net** par devise.
- **Flux journalier** (graphique).

### 11.3 Marges

**Route** : `/compta/marges`

Tableau d'analyse 30 jours par carburant :

- Volume vendu.
- Chiffre d'affaires.
- Coût (COGS via FIFO).
- **Marge brute** (montant + %).
- Prix de vente moyen pondéré.

### 11.4 Rapports

**Route** : `/compta/rapports`

Tableau de bord d'accès aux différents rapports (à venir : export PDF / Excel).

---

## 12. Module Administration

> **Accès restreint** : ce module est **uniquement accessible au Super Admin**. Les autres rôles ne voient pas l'entrée dans la sidebar.

### 12.1 Personnel

**Route** : `/administration/personnel`

CRUD complet de la table Personnel :

- Nom, prénom, matricule, fonction (Pompiste, Caissier, Gérant…).
- Téléphone, adresse, date d'embauche.
- Statut **Actif / Inactif**.

Un membre du personnel peut (ou non) être lié à un **compte utilisateur** pour ouvrir une session.

### 12.2 Maintenance

**Route** : `/administration/maintenance`

Suivi des interventions techniques sur pompes / cuves :

- Type : Réparation · Entretien · Calibration.
- Date, technicien, durée, coût, observations.
- Statut : Planifiée · En cours · Terminée.

### 12.3 RBAC (Rôles & permissions)

**Route** : `/administration/rbac`

#### 12.3.1 Onglet Rôles

- Liste des rôles avec nombre de permissions et nombre d'utilisateurs.
- Bouton **+ Nouveau rôle**.
- Pour chaque rôle, bouton **Permissions** : ouvre une modal avec les checkboxes des 32 permissions disponibles regroupées par module.

#### 12.3.2 Onglet Permissions

Lecture seule. Liste des permissions atomiques (`dashboard.voir`, `pompes.gerer`, `shifts.cloturer`, etc.) avec leur clé interne.

#### 12.3.3 Onglet Utilisateurs

- Liste des comptes avec rôles attribués.
- **+ Nouvel utilisateur** : nom, e-mail, mot de passe initial, rôle(s).
- Actions : **Modifier · Réinitialiser mot de passe · Désactiver · Supprimer**.
- Bouton **Rôles** : modal pour attacher / détacher plusieurs rôles à un compte.

### 12.4 Prix & devises

**Route** : `/administration/prix-devises`

#### Onglet Devises

CRUD : code (XAF, USD, EUR…), libellé, symbole, devise par défaut.

#### Onglet Taux de change

- Saisie d'un taux du jour : devise source, devise cible, taux, date d'effet.
- Le **taux le plus récent** est appliqué automatiquement aux nouvelles opérations.
- Historique conservé.

#### Onglet Prix carburants

- Saisie du **prix de vente** par carburant et par devise, avec date d'effet.
- L'ancien prix est archivé dans `tb_historique_prix_carburant`.

> **Important** : modifier un prix n'affecte **pas** les shifts déjà ouverts. Le nouveau prix s'applique aux nouvelles ventes uniquement.

### 12.5 Journal d'audit

**Route** : `/administration/audit`

Lecture seule. Trace **automatique** de toutes les actions sensibles :

- Création / modification / suppression d'entités.
- Ouverture / clôture / annulation de shift.
- Émission / utilisation / annulation de bon.
- Modification de prix / taux / permissions.

#### Filtres disponibles

- Par **utilisateur**.
- Par **action** (create, update, delete, login, cloture_shift…).
- Par **table** cible.
- Par **plage de dates**.

---

## 13. Centre de notifications

### 13.1 La cloche dans la navbar

Affiche en rouge le **nombre de notifications non lues**. Un effet pulsé attire l'attention.

### 13.2 Aperçu rapide

Cliquez sur la cloche → liste déroulante des **5 dernières notifications** avec titre, contenu court et heure.

### 13.3 Page complète

**Route** : `/notifications`

Toutes vos notifications, paginées, filtrables. Bouton **Tout marquer comme lu**.

### 13.4 Types de notifications

| Type | Émetteur | Exemple |
|---|---|---|
| Système | Auto | « Cuve 2 sous le seuil critique (8 %) » |
| Shift | Auto | « Écart caisse de -3,2 % sur le shift #142 » |
| Stock | Auto | « Produit Coca 33cl en rupture » |
| Audit | Manuel | « Bon BC-260522-0007 annulé » |

---

## 14. Mon profil

**Route** : `/profile`

Page personnelle :

- Modifier **nom**, **e-mail**.
- Changer le **mot de passe** (saisie de l'ancien + 2× le nouveau).
- Choisir le **thème** (clair / sombre).
- **Supprimer mon compte** (zone de danger, mot de passe requis ; non disponible pour le Super Admin).

---

## 15. Procédures types par rôle

### 15.1 Matrice des permissions

| Rôle | Nb perms | Couverture résumée |
|---|---|---|
| Super Admin | 32 | Tout |
| Gérant | 31 | Tout sauf gérer le personnel |
| Pompiste | 7 | Dashboard, pompes (voir), jaugeage, ouvrir/clôturer shift, POS |
| Caissier | 5 | Dashboard, POS, voir shifts, voir clients, voir pompes |
| Magasinier | 5 | Stocks (voir, gérer, réceptionner, inventaire) |
| Technicien | 4 | Pompes (voir), maintenance (voir + saisir) |
| Auditeur | 13 | Lecture seule sur tous les modules |

Détail complet : voir `database/seeders/PermissionsSpecSeeder.php`.

### 15.2 Journée type d'un Pompiste

1. **Arrivée** : se connecter avec son compte.
2. **Ouvrir son shift** : *Shifts → Ouvrir* → saisir fond de caisse + index pompes.
3. **Vendre** : saisir un nouvel index après chaque vente carburant ; effectuer les ventes boutique au POS.
4. **Surveiller** : niveau des cuves (alertes orange / rouge), bons carburant.
5. **Fin de poste** : *Shifts actifs → Clôturer* → saisir index final + encaissements réels.
6. **Déposer** la caisse au gérant + signer le rapport de clôture imprimé.
7. **Se déconnecter**.

### 15.3 Journée type d'un Caissier (boutique)

1. Se connecter, ouvrir un shift.
2. Toute la journée : enregistrer les ventes au POS.
3. Consulter régulièrement *Mes ventes* pour le total intermédiaire.
4. Clôturer le shift en fin de poste.

### 15.4 Journée type d'un Magasinier

1. Réceptionner les livraisons (carburant + boutique) → *Stocks → Réception*.
2. Effectuer un inventaire tournant → *Stocks boutique → Ajuster*.
3. Surveiller les seuils d'alerte → préparer les commandes fournisseurs.

### 15.5 Routine quotidienne du Gérant

- Matin : consulter le **dashboard**, vérifier les **shifts clôturés de la veille**, valider les **écarts caisse**.
- Saisir les **dépenses** du jour.
- Vérifier le **journal d'audit** (10 minutes).
- Soir : suivre les **shifts actifs** et les **alertes**.

### 15.6 Routine hebdomadaire du Gérant

- **Jaugeage manuel** complet de toutes les cuves → comparer à la théorie.
- Revue des **marges carburant** sur 7 jours.
- Inventaire physique partiel de la boutique.
- Pointage des **bons carburant** non utilisés / non payés.

### 15.7 Routine mensuelle

- **Marges 30 jours** par carburant et par produit.
- **Trésorerie** mensuelle, rapprochement bancaire.
- Audit RBAC : vérifier qu'aucun compte inactif ne reste activé.
- Mise à jour des **prix carburant** si nécessaire.

---

## 16. Bonnes pratiques & sécurité

### 16.1 Mots de passe

- **Minimum 8 caractères** avec majuscule, minuscule, chiffre et caractère spécial.
- **Ne jamais** partager son mot de passe.
- Changement recommandé tous les **90 jours**.

### 16.2 Sessions

- **Toujours se déconnecter** en quittant son poste, même pour quelques minutes.
- Ne pas laisser une session ouverte sur un PC partagé.

### 16.3 Saisie

- **Vérifier les index** des pompes deux fois avant de valider ouverture et clôture.
- En cas de doute sur un montant : ne pas valider, appeler le gérant.

### 16.4 Sauvegardes

- La base de données doit être sauvegardée **quotidiennement** (responsabilité du gérant / DSI).
- Conserver les sauvegardes pendant au moins **6 mois** sur un support externe.

### 16.5 Confidentialité

- Les données clients (numéros de téléphone, NIF, soldes) sont **confidentielles**.
- Aucune capture d'écran de comptes clients ne doit être diffusée.

---

## 17. Dépannage (FAQ)

### Je ne peux pas me connecter

- Vérifiez la **casse** du mot de passe.
- Vérifiez l'**URL** (doit se terminer par `/login`).
- Si trois échecs : contactez le Super Admin.

### Je n'ai pas accès à un module

- Votre rôle n'a pas la permission requise. Demandez au Super Admin de revoir vos droits dans *RBAC → Utilisateurs*.

### « Aucun shift ouvert » lors d'une vente

- Ouvrez un shift avant : *Shifts → Ouvrir*.

### Le total ne correspond pas à mes calculs

- Vérifiez que la **devise** sélectionnée est correcte.
- Vérifiez le **taux de change** appliqué (peut être un ancien taux si la session est restée longtemps ouverte).
- Rechargez la page (F5).

### La cloche notifications reste rouge

- Allez sur `/notifications` et cliquez **Tout marquer comme lu**.

### Le thème ne change pas

- Allez dans *Mon profil*, sélectionnez le thème souhaité, **Enregistrer**, puis rafraîchissez la page (Ctrl+F5).

### Erreur 419 (Page Expired)

- Votre session a expiré. **Reconnectez-vous**.

### Erreur 500 / page blanche

- Notez l'**URL** et l'**heure** de l'erreur, puis contactez le support technique.

### La page de connexion bloque sur Internet Explorer

- IE n'est pas supporté. Utilisez **Edge** ou **Chrome**.

---

## 18. Glossaire métier

| Terme | Définition |
|---|---|
| **Bareme cuve** | Table de correspondance hauteur (cm) ↔ volume (L), propre à chaque cuve |
| **Bon carburant (BC)** | Titre de paiement émis pour un client B2B, consommable au POS / Piste |
| **COGS** | *Cost Of Goods Sold* — coût des marchandises vendues |
| **Cuve** | Réservoir enterré ou aérien stockant un carburant |
| **Écart caisse** | Différence entre l'encaissement réel et le théorique calculé |
| **FIFO** | *First In, First Out* — consommation des plus anciens lots en premier |
| **Index pompe** | Compteur cumulé en litres affiché sur la pompe |
| **Jaugeage** | Mesure physique du niveau de carburant dans une cuve |
| **Lot carburant** | Quantité reçue lors d'une livraison, avec son coût propre |
| **Marge brute** | Chiffre d'affaires − COGS |
| **Mouvement de stock** | Entrée, Sortie ou Ajustement journalisé |
| **POS** | *Point Of Sale* — caisse boutique |
| **RBAC** | *Role-Based Access Control* — contrôle d'accès par rôle |
| **Relevé pompe** | Snapshot d'index pris en début ou fin de shift |
| **Seuil minimal** | Stock en deçà duquel une alerte est déclenchée |
| **Shift** | Poste de travail d'un pompiste, borné par ouverture et clôture |
| **Taux de change** | Conversion d'une devise vers une autre à une date donnée |
| **Théorique** | Montant calculé attendu (par opposition au rapporté) |

---

## 19. Annexes

### 19.1 Comptes de démonstration

> Le formulaire de connexion accepte **soit le nom**, **soit l'e-mail**.

**Super Admin (production)** : `bmb` / `Bmb@2026`

Comptes démo (mot de passe : `Petro@2026`) :

| Login | Rôle |
|---|---|
| `gerant@nexus.local` | Gérant |
| `pompiste1@nexus.local` | Pompiste |
| `pompiste2@nexus.local` | Pompiste |
| `caissier@nexus.local` | Caissier |
| `magasinier@nexus.local` | Magasinier |
| `technicien@nexus.local` | Technicien |
| `auditeur@nexus.local` | Auditeur |
| `invite@nexus.local` | (sans rôle) |

### 19.2 Raccourcis clavier utiles

| Touche | Action |
|---|---|
| F5 | Rafraîchir la page |
| Ctrl + F5 | Rafraîchir en ignorant le cache |
| Esc | Fermer la fenêtre modale active |
| Tab | Champ suivant |
| Enter (dans recherche POS) | Ajouter le premier produit filtré |

### 19.3 Conventions techniques (référence)

- Tables MySQL préfixées `tb_*` (ex : `tb_pompes`, `tb_shifts`).
- Clés primaires nommées `id_<entité>` (ex : `id_pompe`).
- Champs en français, snake_case (ex : `montant_total_lc`).
- Suffixe `_lc` = montant en devise locale.
- Toute action sensible est tracée par `AuditLog::trace(...)`.

### 19.4 Structure de base de données (vue d'ensemble)

```
roles, permissions, role_permission, user_role, utilisateurs, personnel
devises, taux_change, carburants, historique_prix_carburant
cuves, pompes, bareme_cuves, jaugeages_cuves, lots_carburant
shifts, releves_pompes, caisses
ventes, vente_details_carburant, vente_details_boutique
produits, categories, mouvements_stock
clients, vehicules_client, bons_carburant
fournisseurs, comptabilite, categories_depenses, maintenance
audit_logs, notifications
```

### 19.5 Commandes utiles (administrateur système)

```powershell
# Préparer l'environnement
composer install
npm install
copy .env.example .env
php artisan key:generate

# Base de données
php artisan migrate
php artisan db:seed --class=DemoDataSeeder
php artisan db:seed --class=StationDataSeeder
php artisan db:seed --class=PermissionsSpecSeeder

# Maintenance / cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Lister toutes les routes
php artisan route:list
```

### 19.6 Support & contact

- **Éditeur** : Nexus BMB Tech
- **Support technique** : support@nexus-bmb-tech.local
- **Documentation technique** : voir `README.md`
- **Tickets** : utiliser le canal interne défini avec votre administrateur.

---

*Document : Manuel utilisateur — Station BOPEL · Version 1.0.0 · © 2026 Nexus BMB Tech.*
*Reproduction autorisée à des fins de formation interne uniquement.*
