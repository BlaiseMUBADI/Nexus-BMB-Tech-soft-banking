# DÉPLOIEMENT — Nexus BMB Tech Soft Banking
**Date :** 10 Mars 2026  
**Branche :** master  
**Développeur :** GitHub Copilot

---

## 1. FICHIERS À UPLOADER VIA FILEZILLA

> Uploader dans l'ordre suivant (du bas vers le haut si dépendances).

### 🔵 Fichiers MODIFIÉS (remplacer sur le serveur)

| Fichier local | Chemin serveur |
|---|---|
| `app/Http/Requests/Auth/LoginRequest.php` | `/app/Http/Requests/Auth/LoginRequest.php` |
| `app/Http/Controllers/ComptesClients/CompteController.php` | `/app/Http/Controllers/ComptesClients/CompteController.php` |
| `app/Http/Controllers/OperationCaisseController.php` | `/app/Http/Controllers/OperationCaisseController.php` |
| `app/Http/Controllers/Tresorerie/TresorerieController.php` | `/app/Http/Controllers/Tresorerie/TresorerieController.php` |
| `resources/views/Caisse_Guichet/operations.blade.php` | `/resources/views/Caisse_Guichet/operations.blade.php` |
| `resources/views/comptes_clients/liste.blade.php` | `/resources/views/comptes_clients/liste.blade.php` |
| `resources/views/tresorerie/coffre.blade.php` | `/resources/views/tresorerie/coffre.blade.php` |
| `routes/caisse.php` | `/routes/caisse.php` |
| `routes/comptes_clients.php` | `/routes/comptes_clients.php` |
| `routes/tresorerie.php` | `/routes/tresorerie.php` |

### 🟢 Fichiers NOUVEAUX (créer sur le serveur)

| Fichier local | Chemin serveur |
|---|---|
| `app/Models/DemandeModification.php` | `/app/Models/DemandeModification.php` |
| `database/migrations/2026_03_10_200000_create_tb_demandes_modification.php` | `/database/migrations/2026_03_10_200000_create_tb_demandes_modification.php` |
| `resources/views/Caisse_Guichet/demandes_modification.blade.php` | `/resources/views/Caisse_Guichet/demandes_modification.blade.php` |
| `resources/views/comptes_clients/historique.blade.php` | `/resources/views/comptes_clients/historique.blade.php` |
| `resources/views/impressions/caisse/bordereau.blade.php` | `/resources/views/impressions/caisse/bordereau.blade.php` *(créer le dossier `caisse/`)* |
| `resources/views/impressions/tresorerie/agents_mobiles.blade.php` | `/resources/views/impressions/tresorerie/agents_mobiles.blade.php` *(créer le dossier `tresorerie/`)* |
| `resources/views/tresorerie/agents_mobiles.blade.php` | `/resources/views/tresorerie/agents_mobiles.blade.php` |

> ⚠️ **Note FileZilla :** Pour les nouveaux dossiers `impressions/caisse/` et `impressions/tresorerie/`, faire un clic droit → *Créer un répertoire* avant d'uploader les fichiers.

---

## 2. COMMANDES À EXÉCUTER SUR LE SERVEUR (SSH ou terminal hébergeur)

Si vous avez accès SSH à votre hébergeur LWS, exécutez :

```bash
cd /chemin/vers/votre/projet
php artisan migrate
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear
```

Si vous n'avez **pas** d'accès SSH → voir section 3 ci-dessous (phpMyAdmin).

---

## 3. SQL À EXÉCUTER SUR PHPMYADMIN (LWS en ligne)

> Connectez-vous à phpMyAdmin LWS → sélectionnez votre base de données → onglet **SQL** → collez et exécutez.

### ➤ Étape 1 — Créer la table `tb_demandes_modification`

```sql
CREATE TABLE IF NOT EXISTS `tb_demandes_modification` (
  `id`                       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_id`           BIGINT UNSIGNED NOT NULL COMMENT 'FK vers tb_transactions',
  `reference_operation`      VARCHAR(60)     DEFAULT NULL COMMENT 'Référence de l\'opération initiale',
  `guichet_id`               BIGINT UNSIGNED DEFAULT NULL COMMENT 'FK vers tb_caisses_guichets',
  `compte_code`              VARCHAR(60)     DEFAULT NULL COMMENT 'Compte client concerné',
  `client_nom`               VARCHAR(200)    DEFAULT NULL COMMENT 'Nom du client (dénormalisation audit)',
  `type_operation`           VARCHAR(30)     DEFAULT NULL COMMENT 'Type original : DEPOT, RETRAIT...',
  `devise_code`              VARCHAR(10)     DEFAULT NULL,
  `ancien_montant`           DECIMAL(15,2)   DEFAULT NULL COMMENT 'Montant original',
  `anciennes_observations`   TEXT            DEFAULT NULL COMMENT 'Observations originales',
  `type_demande`             ENUM('MODIFICATION','SUPPRESSION') NOT NULL,
  `agent_matricule`          VARCHAR(60)     DEFAULT NULL COMMENT 'Guichetier demandeur',
  `motif`                    TEXT            NOT NULL COMMENT 'Motif obligatoire',
  `nouveau_montant`          DECIMAL(15,2)   DEFAULT NULL COMMENT 'Nouveau montant demandé',
  `nouvelles_observations`   TEXT            DEFAULT NULL,
  `statut`                   ENUM('EN_ATTENTE','APPROUVEE','REJETEE') NOT NULL DEFAULT 'EN_ATTENTE',
  `superviseur_matricule`    VARCHAR(60)     DEFAULT NULL COMMENT 'Superviseur ayant traité',
  `commentaire_superviseur`  TEXT            DEFAULT NULL,
  `traitee_le`               TIMESTAMP       DEFAULT NULL COMMENT 'Date de traitement',
  `created_at`               TIMESTAMP       DEFAULT NULL,
  `updated_at`               TIMESTAMP       DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_statut_created` (`statut`, `created_at`),
  KEY `idx_agent_matricule` (`agent_matricule`),
  KEY `idx_superviseur_matricule` (`superviseur_matricule`),
  CONSTRAINT `fk_demmod_transaction`
    FOREIGN KEY (`transaction_id`) REFERENCES `tb_transactions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_demmod_guichet`
    FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### ➤ Étape 2 — Enregistrer la migration dans Laravel (évite les erreurs `artisan migrate`)

```sql
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_03_10_200000_create_tb_demandes_modification', MAX(`batch`) + 1
FROM `migrations`
WHERE `migration` NOT LIKE '%2026_03_10_200000%';
```

---

## 4. RÉSUMÉ DES FONCTIONNALITÉS DÉVELOPPÉES

### ✅ Tâche 1 — Restriction connexion (comptes actifs uniquement)
- **Fichier :** `app/Http/Requests/Auth/LoginRequest.php`
- Lors du login, si le compte utilisateur a le statut `INACTIF`, la connexion est bloquée avec le message :  
  *"Votre compte est désactivé. Veuillez contacter l'administrateur système."*

---

### ✅ Tâche 2 — Historique des mouvements d'un compte client
- **Fichiers :** `CompteController.php`, `routes/comptes_clients.php`, `liste.blade.php`, `historique.blade.php`
- Dans la liste des comptes clients, clic droit → **"Historique des mouvements"**
- Page filtrée par : date début/fin, type d'opération, statut
- Tableau paginé (30/page) avec colonnes débit/crédit, badges type, badges statut

---

### ✅ Tâche 3 — Rapport contributions agents mobiles (Trésorerie)
- **Fichiers :** `TresorerieController.php`, `routes/tresorerie.php`, `agents_mobiles.blade.php`, `impressions/tresorerie/agents_mobiles.blade.php`, `coffre.blade.php`
- Bouton **"Rapport Agents Mobiles"** sur le tableau de bord trésorerie
- Filtres : période, agent, zone, type de compte, type d'opération, devise
- 4 cartes statistiques (total dépôts, retraits, opérations, agents actifs)
- Tableau récapitulatif par agent/devise + tableau détail des opérations
- **Export PDF** (DomPDF) avec en-tête banque professionnelle

---

### ✅ Tâche 4 — Workflow approbation superviseur (modification/suppression)
- **Fichiers :** `OperationCaisseController.php` (6 nouvelles méthodes), `routes/caisse.php` (5 nouvelles routes), `DemandeModification.php` (modèle), migration, `operations.blade.php`, `demandes_modification.blade.php`

**Côté guichetier :**
- Bouton jaune ✏️ sur chaque opération CONFIRMÉE → modal "Demander modification / suppression"
- Choix : Modification (avec nouveau montant) ou Suppression
- Motif obligatoire + observations optionnelles
- Envoi AJAX → demande créée en base en statut `EN_ATTENTE`

**Côté superviseur :**
- Page `/caisses/demandes-modification` — liste des demandes filtrables
- Bouton ✅ Approuver → modal avec commentaire optionnel → exécute réellement la modification/annulation en base + recalcule les soldes
- Bouton ❌ Rejeter → modal avec motif obligatoire → demande marquée REJETEE
- Historique complet conservé : qui a demandé, pourquoi, qui a traité, quand

---

### ✅ Tâche 5 — Bordereau de versement (preuve de transaction)
- **Fichiers :** `OperationCaisseController.php` (méthode `bordereau()`), `routes/caisse.php`, `impressions/caisse/bordereau.blade.php`
- **Ouverture automatique** dans un nouvel onglet après chaque opération enregistrée
- Bouton 🧾 bleu sur chaque ligne du tableau pour réimprimer à tout moment
- **Format A5 paysage** — 2 exemplaires sur une page (client + banque)
- Contenu professionnel :
  - Référence en grand format
  - Date et heure exactes
  - Montant en gros + devise
  - Infos change (montant destination + taux) si change de devises
  - Photo + nom complet + matricule + téléphone du client
  - Solde du compte après opération
  - Guichet + nom du caissier
  - Case signature client
  - Exemplaire banque condensé (récapitulatif sous ligne de découpe)
  - Badge "OPÉRATION ANNULÉE" si l'opération a été annulée

---

## 5. ROUTES AJOUTÉES

| Méthode | URL | Nom | Permission |
|---|---|---|---|
| `GET` | `/comptes/{code}/historique` | `comptes.historique` | EBEN-PER18 |
| `GET` | `/tresorerie/agents-mobiles` | `tresorerie.agents.mobiles` | EBEN-PER44 |
| `GET` | `/tresorerie/agents-mobiles-pdf` | `tresorerie.agents.mobiles.pdf` | EBEN-PER44 |
| `POST` | `/caisses/operations/{id}/demande` | `caisses.operations.demande.modification` | EBEN-PER11 |
| `GET` | `/caisses/operations/{id}/bordereau` | `caisses.operations.bordereau` | EBEN-PER11 |
| `GET` | `/caisses/demandes-modification` | `caisses.demandes.modification.page` | EBEN-PER44 |
| `GET` | `/caisses/demandes-modification/data` | `caisses.demandes.modification.data` | EBEN-PER44 |
| `GET` | `/caisses/demandes-modification/count` | `caisses.demandes.modification.count` | EBEN-PER44 |
| `POST` | `/caisses/demandes-modification/{id}/approuver` | `caisses.demandes.modification.approuver` | EBEN-PER44 |
| `POST` | `/caisses/demandes-modification/{id}/rejeter` | `caisses.demandes.modification.rejeter` | EBEN-PER44 |
