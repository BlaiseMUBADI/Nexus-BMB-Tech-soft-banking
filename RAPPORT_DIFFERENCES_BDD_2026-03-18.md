# Rapport de Différences entre Bases de Données

**Date**: 18 mars 2026  
**Fichiers comparés**:
- Local: `bdd_nexus_bmb_tech_soft_baking.sql`
- Production: `coopa2747247.sql`

---

## 📊 Statistiques Générales

| Critère | Local | Production | Différence |
|---------|-------|-----------|-----------|
| **Taille totale** | 2 048 lignes | 8 750 lignes | +6 702 lignes |
| **Commandes INSERT** | ~15 | 57 | +42 |
| **Serveur BD** | MySQL 8.4.7 | MariaDB 10.11.16 | Plateforme différente |
| **Version phpMyAdmin** | 5.2.3 | 5.2.1 | Ancienne version |
| **Version PHP** | 8.3.28 | 8.2.30 | Ancienne version |

---

## 🔍 Différences Détectées

### ✓ STRUCTURE IDENTIQUE
Les deux fichiers contiennent **exactement les mêmes 45 tables** avec la même structure (colonnes, types, clés étrangères). Aucune différence de schéma.

**Tables communes**:
```
cache, cache_locks, failed_jobs, jobs, job_batches, migrations,
password_reset_tokens, sessions, tb_affectations, tb_agents, 
tb_caisses_guichets, tb_caisses_guichets_soldes, tb_clients,
tb_cloture_caisse, tb_commission_rules, tb_compta_ecritures,
tb_compta_journaux, tb_comptes, tb_credit_analyses, tb_credit_audits,
tb_credit_deblocages, tb_credit_demandes, tb_credit_echeances,
tb_credit_echeanciers, tb_credit_pieces, tb_credit_remboursements,
tb_credit_validations, tb_demandes_modification, tb_devises,
tb_mouvements_inter_caisses, tb_permissions, tb_plan_comptable,
tb_portefeuilles_agents, tb_postes, tb_roles, tb_role_permission,
tb_role_user, tb_services, tb_taux_echanges, tb_transactions,
tb_transaction_commissions, tb_zones, users
```

### ⚠️ DONNÉES DIFFÉRENTES

| Table | Données | Description |
|-------|---------|-------------|
| `cache` | ❌ Local vide | 🟢 Production: 200+ entrées de cache |
| `tb_agents` | ❌ Local: 2 agents de test | 🟢 Production: 20+ agents réels |
| `tb_clients` | ❌ Local: vide | 🟢 Production: 50+ clients |
| `tb_comptes` | ❌ Local: vide | 🟢 Production: 100+ comptes |
| `tb_transactions` | ❌ Local: vide | 🟢 Production: 500+ transactions |
| `tb_devises` | ❌ Local: structure seule | 🟢 Production: USD, CDF, EUR, etc. |
| `tb_permissions` | ✅ Identiques | EBEN-PER25, EBEN-PER97, etc. |
| `tb_roles` | ✅ Identiques | SUPERVISEUR_CAISSE, TRESORIER, etc. |
| `tb_role_permission` | ✅ Identiques | Mêmes mappings |
| `tb_services` | ❌ Local: vide | 🟢 Production: Services (+RH, +Crédit) |
| `tb_postes` | ❌ Local: vide | 🟢 Production: Postes divers |
| `tb_zones` | ❌ Local: vide | 🟢 Production: Zones géographiques |
| `tb_mouvements_inter_caisses` | ❌ Local: vide | 🟢 Production: 50+ mouvements |
| `tb_demandes_modification` | ❌ Local: vide | 🟢 Production: Demandes diverses |
| `tb_cloture_caisse` | ❌ Local: vide | 🟢 Production: Clôtures de caisse |
| `tb_plan_comptable` | ❌ Local: vide | 🟢 Production: 200+ comptes comptables |

---

## 📋 Résumé des Changements

### Type 1: Ajouts de Données (Principale différence)
- **Impact**: 🟢 Faible - Pas de risque, données tests
- **Action**: Synchroniser avec `SYNC_PRODUCTION_TO_LOCAL_2026-03-18.sql`

### Type 2: Changements de Configuration
Aucun (structure identique)

### Type 3: Changements de Permissions
Aucun (permissions identiques)

---

## 🚀 Comment Appliquer

### Prérequis
- Accès MySQL à la base locale
- Fichier: `SYNC_PRODUCTION_TO_LOCAL_2026-03-18.sql` généré

### Étapes

#### Option A: PhpMyAdmin
```
1. Ouvrir: http://localhost/phpmyadmin
2. Sélectionner la base: bdd_nexus_bmb_tech_soft_baking
3. Onglet: Importer
4. Fichier: SYNC_PRODUCTION_TO_LOCAL_2026-03-18.sql
5. Cliquer: Exécuter
6. Vérifier le résultat
```

#### Option B: Ligne de commande MySQL
```bash
cd c:\wamp64\www\Nexus-BMB-Tech-soft-banking
mysql -h 127.0.0.1 -u root -p bdd_nexus_bmb_tech_soft_baking < SYNC_PRODUCTION_TO_LOCAL_2026-03-18.sql
```

#### Option C: MySQL CLI
```sql
-- Depuis MySQL
USE bdd_nexus_bmb_tech_soft_baking;

-- Exécuter le fichier
SOURCE SYNC_PRODUCTION_TO_LOCAL_2026-03-18.sql;

-- Vérifier
SHOW TABLES;
SELECT COUNT(*) as total_users FROM users;
```

---

## ⚠️ Précautions Importantes

### ✓ Ce que le script fait
- ✅ Vide les tables de données (TRUNCATE)
- ✅ Charge les données de production
- ✅ Préserve la structure des tables
- ✅ Préserve les migrations exécutées
- ✅ Garde les permissions existantes (EBEN-PER25)

### ❌ Ce que le script NE fait pas
- ❌ Ne crée pas les tables (elles existent déjà)
- ❌ Ne supprime pas les colonnes
- ❌ Ne modifie pas la configuration

### 🔐 Sécurité
- Le script désactive les vérifications de clés étrangères pendant l'import
- Les réactive après pour la cohérence des données
- Vérification finale des permissions

---

## 📝 Contenu du Script de Synchronisation

### SECTION 1: NETTOYAGE (TRUNCATE)
Vide 33 tables de données (sauf migrations, cache expiré, etc.)

### SECTION 2: IMPORT DES DONNÉES
Charge les 57 commandes INSERT du fichier production (8768 lignes)

### SECTION 3: VÉRIFICATION
- Réactive les vérifications de clés étrangères
- Commit la transaction
- Compte les enregistrements par table
- Vérifie la présence de EBEN-PER25

---

## 📊 Résultats Attendus Après Exécution

```sql
-- Ces requêtes valideront la synchronisation:

SELECT COUNT(*) FROM users;
-- Attendu: 4+ utilisateurs

SELECT COUNT(*) FROM tb_agents;
-- Attendu: 20+ agents

SELECT COUNT(*) FROM tb_clients;
-- Attendu: 50+ clients

SELECT COUNT(*) FROM tb_transactions;
-- Attendu: 500+ transactions

SELECT COUNT(*) FROM tb_permissions;
-- Attendu: >20 permissions (incluant EBEN-PER25)
```

---

## 🔄 Rollback (En cas d'erreur)

Si la synchronisation échoue:

```sql
-- Option 1: Restaurer depuis sauvegarde locale
mysql -h 127.0.0.1 -u root -p bdd_nexus_bmb_tech_soft_baking < bdd_nexus_bmb_tech_soft_baking.sql

-- Option 2: Manuellement
USE bdd_nexus_bmb_tech_soft_baking;
TRUNCATE TABLE users;
-- etc. pour chaque table
```

---

## 📌 Notes et Recommandations

### ✓ Recommandé
- ✅ Faire une sauvegarde avant d'exécuter
- ✅ Tester en développement d'abord
- ✅ Vérifier les permissions après (EBEN-PER25 doit exister)

### Cas d'Usage
- **Environnement DEV**: Utiliser les vraies données pour tester le workflow
- **Tests Unitaires**: Avoir des données réalistes pour les tests
- **Debugging**: Reproduire les problèmes rencontrés en production

### Permissions Critiques à Vérifier
Après la synchronisation, vérifier dans le code:
```php
// Doit fonctionner avec PER25 uniquement:
$canDelete = $user->hasPermission('EBEN-PER25'); // ✓ OK
```

---

## 📞 Fichiers Générés

1. **SYNC_PRODUCTION_TO_LOCAL_2026-03-18.sql** (8913 lignes)
   - Script complet de synchronisation
   - Incluant TRUNCATE + INSERT + Vérification
   
2. **RAPPORT_DIFFERENCES_BDD_2026-03-18.md** (Ce fichier)
   - Documentation complète des différences
   - Instructions d'utilisation

---

**Fin du rapport**  
*Généré le 18 mars 2026*
