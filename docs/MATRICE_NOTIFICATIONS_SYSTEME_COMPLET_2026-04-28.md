# Matrice Notifications Systeme Complet (2026-04-28)

Cette matrice liste les notifications metier actuellement branchees dans l'application.

## 1) Administration RBAC

| Evenement | Emetteur (code) | Destinataires | Type | Action UI |
|---|---|---|---|---|
| Permission ajoutee a un role | RolesPermissionsController::attachPermission | Tous les utilisateurs du role cible | info | administration.roles_permissions |
| Permission retiree d'un role | RolesPermissionsController::detachPermission | Tous les utilisateurs du role cible | warning | administration.roles_permissions |

Source: app/Http/Controllers/Administration/RolesPermissionsController.php

## 2) Credit

| Evenement | Emetteur (code) | Destinataires | Type | Action UI |
|---|---|---|---|---|
| Etape de validation terminee -> etape suivante | CreditController::storeValidation | Utilisateurs ayant la permission de l'etape suivante | action_required | credit.show |
| Dossier rejete | CreditController::storeValidation | Agent createur + agent analyse du dossier | danger | credit.show |
| Dossier pret au deblocage | CreditController::storeValidation | Utilisateurs avec EBEN-PER64 | warning | credit.deblocage |
| Validation terminee (info dossier) | CreditController::storeValidation | Agent createur + agent analyse du dossier | info | credit.show |

Source: app/Http/Controllers/Credit/CreditController.php

## 3) Caisse (ouverture/fermeture/appro)

| Evenement | Emetteur (code) | Destinataires | Type | Action UI |
|---|---|---|---|---|
| Changement statut guichet (OUVERT/SUSPENDU) | CaisseController::changerStatut | Utilisateurs avec EBEN-PER44 | info | caisses.ouverture |
| Cloture soumise en verification | CaisseController::confirmerFermeture | Utilisateurs avec EBEN-PER46 | action_required | tresorerie.etat-coffre |
| Demande d'approvisionnement guichet soumise | CaisseController::demanderApprovisionnement | Utilisateurs avec EBEN-PER46 | action_required | tresorerie.etat-coffre |

Source: app/Http/Controllers/CaisseController.php

## 4) Tresorerie (coffre/ravitaillement/cloture)

| Evenement | Emetteur (code) | Destinataires | Type | Action UI |
|---|---|---|---|---|
| Coffre central approvisionne | TresorerieController::approvisionner | Utilisateurs avec EBEN-PER44 | info | tresorerie.etat-coffre |
| Guichet ravitaille depuis coffre | TresorerieController::alimenter | Agent actif du guichet concerne (matricule) | info | caisses.ouverture |
| Demande de ravitaillement approuvee | TresorerieController::approuverDemande | Agent initiateur de la demande | info | caisses.mes.demandes |
| Demande de ravitaillement rejetee | TresorerieController::rejeterDemande | Agent initiateur de la demande | warning | caisses.mes.demandes |
| Cloture guichet approuvee (globale) | TresorerieController::approuverCloture | Agent cloturant | info | caisses.ouverture |
| Cloture guichet rejetee (globale) | TresorerieController::rejeterCloture | Agent cloturant | warning | caisses.ouverture |
| Cloture ligne devise traitee | TresorerieController::approuverLigneCloture | Agent cloturant | info | caisses.ouverture |
| Cloture ligne devise rejetee | TresorerieController::rejeterLigneCloture | Agent cloturant | warning | caisses.ouverture |

Source: app/Http/Controllers/Tresorerie/TresorerieController.php

## 5) Operations Caisse (annulation/modification/suppression/mobile)

| Evenement | Emetteur (code) | Destinataires | Type | Action UI |
|---|---|---|---|---|
| Operation annulee | OperationCaisseController::annuler | Utilisateurs avec EBEN-PER44 | warning | caisses.journal.page |
| Demande modification/suppression soumise | OperationCaisseController::demanderModification | Utilisateurs avec EBEN-PER44 | action_required | caisses.demandes.modification.page |
| Demande modification/suppression approuvee | OperationCaisseController::approuverModification | Agent demandeur (matricule) | info | caisses.journal.page |
| Demande modification/suppression rejetee | OperationCaisseController::rejeterModification | Agent demandeur (matricule) | warning | caisses.journal.page |
| Demande de dotation mobile soumise | OperationCaisseController::mobileDepart | Utilisateurs avec EBEN-PER46 | action_required | tresorerie.etat-coffre |
| Reversement mobile declare | OperationCaisseController::mobileRetour | Utilisateurs avec EBEN-PER46 | action_required | tresorerie.etat-coffre |

Source: app/Http/Controllers/OperationCaisseController.php

## Harmonisation appliquee (metier)

- Option appliquee (stricte):
  - Toutes les validations/rejets tresorerie sont protegees par EBEN-PER46 (sans fallback EBEN-PER78) dans les routes.
  - Les notifications diffusees via EBEN-PER44 restent de type information/warning.

Fichiers modifies pour cette harmonisation:
- routes/tresorerie.php
- app/Http/Controllers/OperationCaisseController.php

## 6) Tableau validation metier: permissions notification vs roles

Source de verite: bdd_nexus_bmb_tech_soft_baking.sql (tb_roles + tb_role_permission).

| Permission | Description | Roles porteurs |
|---|---|---|
| EBEN-PER44 | Voir tresorerie | EBEN-ROL1 Administrateur, EBEN-ROL3 Directeur, EBEN-ROL5 Superviseur, EBEN-ROL8 Tresorier |
| EBEN-PER46 | Valider mouvements tresorerie | EBEN-ROL1 Administrateur, EBEN-ROL8 Tresorier |
| EBEN-PER64 | Debloquer credit | EBEN-ROL1 Administrateur, EBEN-ROL11 Charge des operations, EBEN-ROL14 Controleur interne credit |
| EBEN-PER78 | Modifier operation tresorerie (legacy technique) | EBEN-ROL1 Administrateur, EBEN-ROL8 Tresorier |

### Notes de gouvernance

- Les endpoints de decision tresorerie sont desormais sur EBEN-PER46 uniquement.
- EBEN-PER78 reste utilise pour les ecrans/actions de modification de regles/operations, pas pour les validations/rejets de demandes et clotures.
