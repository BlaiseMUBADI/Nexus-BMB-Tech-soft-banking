# DEPLOIEMENT MODULE CREDIT (COOPEC EBEN)

Date generation : 2026-03-16
Projet : Nexus-BMB-Tech-soft-banking

Ce document liste tous les dossiers/fichiers touches pour le module credit,
avec un guide de migration serveur (online) et la structure de la base de donnees.

---

## 1) Structure BDD ajoutee

### Migration 1 : tables metier credit
Fichier : `database/migrations/2026_03_16_000011_credit_module_tables.php`

Tables creees :
1. `tb_credit_demandes`
2. `tb_credit_analyses`
3. `tb_credit_validations`
4. `tb_credit_pieces`
5. `tb_credit_deblocages`
6. `tb_credit_echeanciers`
7. `tb_credit_echeances`
8. `tb_credit_remboursements`
9. `tb_credit_audits`

Relations principales :
- `tb_credit_demandes.client_matricule` -> `tb_clients.matricule`
- `tb_credit_demandes.compte_id` -> `tb_comptes.code_compte`
- `tb_credit_demandes.portefeuille_id` -> `tb_portefeuilles_agents.id`
- `tb_credit_demandes.code_zone` -> `tb_zones.code_zone`
- Tables enfants via `credit_demande_id` vers `tb_credit_demandes.id`
- `tb_credit_echeances.echeancier_id` -> `tb_credit_echeanciers.id`
- `tb_credit_remboursements.echeance_id` -> `tb_credit_echeances.id`

### Migration 2 : permissions credit
Fichier : `database/migrations/2026_03_16_000012_credit_permissions.php`

Permissions ajoutees :
- `EBEN-PER53` Acceder module credit
- `EBEN-PER54` Creer demande credit
- `EBEN-PER55` Modifier demande en brouillon
- `EBEN-PER56` Soumettre demande pour analyse
- `EBEN-PER57` Voir analyses credit
- `EBEN-PER58` Analyser dossier credit
- `EBEN-PER59` Completer analyse et transmettre validation
- `EBEN-PER60` Valider niveau Agent credit
- `EBEN-PER61` Valider niveau Charge operations
- `EBEN-PER62` Valider niveau Controleur
- `EBEN-PER63` Valider niveau Gerant
- `EBEN-PER64` Debloquer credit
- `EBEN-PER65` Enregistrer remboursement
- `EBEN-PER66` Reamenager echeancier
- `EBEN-PER67` Annuler dossier credit
- `EBEN-PER68` Exporter liste credits
- `EBEN-PER69` Imprimer fiche/echeancier credit
- `EBEN-PER70` Supervision credit
- `EBEN-PER71` Signaler transaction suspecte credit
- `EBEN-PER72` Voir audit complet credit

---

## 2) Fichiers applicatifs ajoutes/modifies

### A. Migrations
1. `database/migrations/2026_03_16_000011_credit_module_tables.php` (NOUVEAU)
2. `database/migrations/2026_03_16_000012_credit_permissions.php` (NOUVEAU)

### B. Models
3. `app/Models/Credit/CreditDemande.php` (NOUVEAU)
4. `app/Models/Credit/CreditAnalyse.php` (NOUVEAU)
5. `app/Models/Credit/CreditValidation.php` (NOUVEAU)
6. `app/Models/Credit/CreditPiece.php` (NOUVEAU)
7. `app/Models/Credit/CreditDeblocage.php` (NOUVEAU)
8. `app/Models/Credit/CreditEcheancier.php` (NOUVEAU)
9. `app/Models/Credit/CreditEcheance.php` (NOUVEAU)
10. `app/Models/Credit/CreditRemboursement.php` (NOUVEAU)
11. `app/Models/Credit/CreditAudit.php` (NOUVEAU)

### C. Service
12. `app/Services/Credit/AmortissementService.php` (NOUVEAU)

### D. Controller
13. `app/Http/Controllers/Credit/CreditController.php` (NOUVEAU)

### E. Routes
14. `routes/credit.php` (NOUVEAU)
15. `routes/web.php` (MODIFIE : ajout require `credit.php`)

### F. Navigation
16. `resources/views/layouts/sidebar.blade.php` (MODIFIE : menu Credits)

### G. Vues module credit
17. `resources/views/credit/dashboard.blade.php` (NOUVEAU)
18. `resources/views/credit/liste.blade.php` (NOUVEAU)
19. `resources/views/credit/creation.blade.php` (NOUVEAU)
20. `resources/views/credit/show.blade.php` (NOUVEAU)
21. `resources/views/credit/analyse.blade.php` (NOUVEAU)
22. `resources/views/credit/validation.blade.php` (NOUVEAU)
23. `resources/views/credit/deblocage.blade.php` (NOUVEAU)
24. `resources/views/credit/supervision.blade.php` (NOUVEAU)
25. `resources/views/credit/remboursement.blade.php` (NOUVEAU)

### H. Impressions / PDF
26. `resources/views/impressions/credit/echeancier.blade.php` (NOUVEAU)
27. `resources/views/impressions/credit/fiche_credit.blade.php` (NOUVEAU)

### I. Documentation deployment
28. `DEPLOIEMENT_CREDIT.md` (NOUVEAU)

---

## 3) Commandes de migration serveur (production/online)

Depuis la racine projet :

```bash
php artisan migrate --path=database/migrations/2026_03_16_000011_credit_module_tables.php
php artisan migrate --path=database/migrations/2026_03_16_000012_credit_permissions.php
php artisan optimize:clear
composer dump-autoload -o
```

Si besoin cache routes/config/views :

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

Puis (optionnel) re-cache en prod :

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 4) Checklist post-deploiement

1. Verifier que les 9 tables `tb_credit_*` existent en base.
2. Verifier que permissions `EBEN-PER53` a `EBEN-PER72` sont en base.
3. Verifier menu Credits visible selon role dans sidebar.
4. Creer un dossier test, soumettre, analyser, valider (4 niveaux), debloquer.
5. Verifier generation echeancier automatique apres deblocage.
6. Enregistrer un remboursement et verifier MAJ statut echeance.
7. Imprimer PDF echeancier et PDF fiche credit.
8. Verifier journal d'audit sur le dossier.

---

## 5) Notes techniques

- Numerotation auto dossier : `CRD-EBEN-YYYY-00001`
- Validation sequentielle : Agent credit -> Charge operations -> Controleur -> Gerant
- Deblocage autorise uniquement si toutes validations approuvees
- Methode remboursement : amortissement degressif (capital constant)
- Scope securite zone applique cote controller (`resolveZoneScope`)

---

## 6) Rollback (si incident)

Rollback des migrations credit uniquement (ordre inverse) :

```bash
php artisan migrate:rollback --path=database/migrations/2026_03_16_000012_credit_permissions.php
php artisan migrate:rollback --path=database/migrations/2026_03_16_000011_credit_module_tables.php
```

Attention : le rollback supprime les structures et donnees credit liees.

---

## 7) Addendum - Correctifs Point 1 et 2 (UX + Routes)

Passe realisee le 2026-03-16 apres integration initiale, avec deux objectifs :
- Uniformisation UX des ecrans d'action du module credit
- Verification et correction du flux de routes/permissions

### Fichiers modifies lors de cette passe

1. `app/Http/Middleware/CheckPermission.php`
	- Ajout du support multi-permissions en OR via separateur `|` ou `,`
	- Exemple possible : `permission:EBEN-PER60|EBEN-PER61|EBEN-PER62|EBEN-PER63`

2. `routes/credit.php`
	- Route de validation ouverte aux 4 profils validateurs (PER60 a PER63)

3. `resources/views/credit/analyse.blade.php`
	- Barre d'actions harmonisee (Voir dossier / Liste credits)
	- Alignement champs formulaire avec contrat controller

4. `resources/views/credit/validation.blade.php`
	- Barre d'actions harmonisee
	- Alignement noms de champs submit avec controller

5. `resources/views/credit/deblocage.blade.php`
	- Barre d'actions harmonisee

6. `resources/views/credit/remboursement.blade.php`
	- Barre d'actions harmonisee
	- Alignement formulaire remboursement avec controller

### Verification routes effectuee

- Commande executee : `php artisan route:list --name=credit`
- Resultat : 24 routes credit detectees (index, create, show, analyse, validation, deblocage, remboursement, supervision, PDF, actions transverses)

### Point de controle cle corrige

- Avant : validation accessible uniquement avec `EBEN-PER60`
- Apres : validation accessible avec `EBEN-PER60|EBEN-PER61|EBEN-PER62|EBEN-PER63`

---

## 8) Addendum - Correctif cle compte `tb_comptes.code_compte`

Passe realisee le 2026-03-16 pour aligner le module credit avec le schema coeur banque :
- `tb_comptes` utilise `code_compte` (string) comme cle primaire, pas `id`
- Toutes les FK/metiers credit ont ete alignees sur `code_compte`

### Fichiers techniques ajustes

1. `database/migrations/2026_03_16_000011_credit_module_tables.php`
2. `app/Http/Controllers/Credit/CreditController.php`
3. `app/Models/Credit/CreditDemande.php`
4. `app/Models/Credit/CreditDeblocage.php`
5. `app/Models/Credit/CreditRemboursement.php`
6. `resources/views/credit/creation.blade.php`
7. `resources/views/credit/deblocage.blade.php`
8. `resources/views/credit/remboursement.blade.php`

### Recuperation appliquee sur environnement local

En cas d'etat partiel de migration (table creee mais migration en pending), sequence utilisee :

```bash
php artisan tinker --execute="\Illuminate\Support\Facades\Schema::dropIfExists('tb_credit_audits'); \Illuminate\Support\Facades\Schema::dropIfExists('tb_credit_remboursements'); \Illuminate\Support\Facades\Schema::dropIfExists('tb_credit_echeances'); \Illuminate\Support\Facades\Schema::dropIfExists('tb_credit_echeanciers'); \Illuminate\Support\Facades\Schema::dropIfExists('tb_credit_deblocages'); \Illuminate\Support\Facades\Schema::dropIfExists('tb_credit_pieces'); \Illuminate\Support\Facades\Schema::dropIfExists('tb_credit_validations'); \Illuminate\Support\Facades\Schema::dropIfExists('tb_credit_analyses'); \Illuminate\Support\Facades\Schema::dropIfExists('tb_credit_demandes');"
php artisan migrate --path=database/migrations/2026_03_16_000011_credit_module_tables.php
php artisan optimize:clear
```
