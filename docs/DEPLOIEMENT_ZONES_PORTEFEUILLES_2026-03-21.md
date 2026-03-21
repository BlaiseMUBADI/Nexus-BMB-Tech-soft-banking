# Déploiement Zone / Portefeuille avec affectations historisées

Date: 2026-03-21

## Objectif

Mettre en place en local puis en production:
- création d'une zone sans agent puis affectation plus tard
- création d'un portefeuille sans agent puis affectation plus tard
- une seule affectation active par zone
- une seule affectation active par portefeuille
- conservation des traces directement dans les tables d'affectation

## Fichiers de migration concernés

- database/migrations/2026_03_21_000022_create_tb_affectations_zones.php
- database/migrations/2026_03_21_000023_create_tb_affectations_portefeuilles.php
- database/migrations/2026_03_21_000025_make_zone_and_portefeuille_agents_nullable.php
- database/migrations/2026_03_21_000026_drop_zone_portefeuille_historiques.php

## Commandes local

```powershell
php artisan migrate
php artisan optimize:clear
```

## Commandes production

```powershell
php artisan migrate --force
php artisan optimize:clear
```

## Sauvegarde recommandée avant production

### Sauvegarde base de données

```powershell
mysqldump -u root -p nexus_bmb_tech_soft_banking > backup_nexus_bmb_tech_soft_banking_2026-03-21.sql
```

Adapte `root` et `nexus_bmb_tech_soft_banking` à tes paramètres réels.

## Requêtes SQL de vérification après migration

Exécuter le fichier SQL suivant:
- docs/SQL_CONTROLE_ZONES_PORTEFEUILLES_2026-03-21.sql

## Contrôle métier attendu après déploiement

### Zones
- une zone peut exister sans agent actif
- si un agent est affecté, une seule ligne active doit exister dans tb_affectations_zones
- l'historique des affectations doit apparaître dans tb_affectations_zones avec date_debut, date_fin et Etat

### Portefeuilles
- un portefeuille peut exister sans agent actif
- si un agent est affecté, une seule ligne active doit exister dans tb_affectations_portefeuilles
- l'historique des affectations doit apparaître dans tb_affectations_portefeuilles avec date_debut, date_fin et Etat

## Séquence recommandée

### Local
1. Sauvegarder la base locale si nécessaire
2. Lancer `php artisan migrate`
3. Vérifier l'écran Administration > Zones & Portefeuilles
4. Tester:
   - création zone sans agent
   - affectation zone après création
   - réaffectation zone
   - création portefeuille sans agent
   - affectation portefeuille après création
   - réaffectation portefeuille
5. Exécuter le script SQL de contrôle

### Production
1. Sauvegarder la base production
2. Mettre le code à jour
3. Lancer `php artisan migrate --force`
4. Lancer `php artisan optimize:clear`
5. Exécuter le script SQL de contrôle
6. Tester un cas simple sur l'interface

## Point important

Les colonnes legacy suivantes sont conservées pour compatibilité applicative transitoire:
- tb_zones.agent_commercial_matricule
- tb_portefeuilles_agents.agent_matricule

La vérité métier doit désormais être lue via:
- tb_affectations_zones
- tb_affectations_portefeuilles

Ces colonnes legacy ont été rendues nullable pour permettre la création sans affectation immédiate.

La colonne Etat pilote désormais les cas suivants:
- ACTIF: affectation courante
- INACTIF: affectation désactivée manuellement
- TERMINE: affectation clôturée lors d'une réaffectation ou suppression
- EXPIRE: affectation désactivée automatiquement car la date_fin est atteinte
