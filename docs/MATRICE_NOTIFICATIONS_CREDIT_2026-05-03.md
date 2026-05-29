# Matrice Notifications Credit (2026-05-03)

Cette matrice detaille le workflow credit avec les notifications declenchees, les destinataires, le type de notification et le point de declenchement dans le code.

## Vue d'ensemble

| Etape | Evenement | Destinataires | Type | Action |
|---|---|---|---|---|
| 1 | Creation du dossier | Aucun envoi automatique | - | - |
| 2 | Soumission du dossier | Utilisateurs avec EBEN-PER61 | action_required | Ouvrir le dossier et affecter un agent credit |
| 3 | Affectation analyse | Agent credit affecte | action_required | Ouvrir le dossier pour analyse |
| 4 | Affectation analyse | Demandeur du dossier | info | Voir que le dossier est pris en charge |
| 5 | Demarrage analyse | Demandeur du dossier | info | Suivi d'avancement |
| 6 | Analyse complete | Utilisateurs avec EBEN-PER60 | action_required | Demarrer la validation bloc Agent credit |
| 7 | Analyse complete | Demandeur + agent analyse | info | Voir le passage en validation |
| 8 | Validation intermediaire | Permission du validateur suivant | action_required | Continuer la chaine de validation |
| 9 | Validation rejetee | Demandeur + agent analyse | danger | Voir le dossier rejete |
| 10 | Validation finale terminee | Utilisateurs avec EBEN-PER64 | warning | Ouvrir le deblocage |
| 11 | Validation finale terminee | Demandeur + agent analyse | info | Voir que le dossier est pret au deblocage |
| 12 | Deblocage effectue | Demandeur + agent analyse | info | Voir le dossier debloque |
| 13 | Deblocage effectue | Utilisateurs avec EBEN-PER65 | info | Ouvrir le suivi remboursement |
| 14 | Remboursement enregistre | Demandeur + agent analyse | info | Voir le remboursement |
| 15 | Credit totalement solde | Demandeur + agent analyse | info | Voir le dossier solde |
| 16 | Dossier annule | Demandeur + agent analyse | warning | Voir le motif |
| 17 | Dossier suspendu | Demandeur + agent analyse | warning | Voir le motif |
| 18 | Suspension levee | Demandeur + agent analyse | info | Retour en validation |
| 19 | Dossier signale suspect | Demandeur + agent analyse | danger | Voir le motif |
| 20 | Suspicion levee | Demandeur + agent analyse | info | Retour en validation |

## Detail par methode

### 1. Soumission

- Methode: `CreditController::soumettre`
- Fichier: `app/Http/Controllers/Credit/CreditController.php`
- Notification:
  - Titre: `Nouveau dossier soumis`
  - Destinataires: `EBEN-PER61`
  - Type: `action_required`
  - Icone: `fas fa-file-upload`
  - Action: `credit.show`
- But metier: informer le charge des operations qu'un dossier attend l'affectation d'un agent credit.

### 2. Affectation d'analyse

- Methode: `CreditController::affecterAnalyse`
- Notifications:
  - `Nouveau dossier credit affecte`
    - Destinataires: agent affecte via matricule
    - Type: `action_required`
    - Icone: `fas fa-user-check`
  - `Dossier pris en charge`
    - Destinataires: demandeur via `agent_createur_matricule`
    - Type: `info`
    - Icone: `fas fa-user-tie`
- But metier: notifier l'analyste cible et rassurer le demandeur que le dossier est pris en charge.

### 3. Analyse

- Methode: `CreditController::storeAnalyse`
- Notifications:
  - `Analyse credit demarree`
    - Destinataires: demandeur
    - Type: `info`
    - Icone: `fas fa-search-dollar`
  - `Dossier pret pour validation`
    - Destinataires: `EBEN-PER60`
    - Type: `action_required`
    - Icone: `fas fa-file-signature`
  - `Analyse credit completee`
    - Destinataires: demandeur + agent analyse
    - Type: `info`
    - Icone: `fas fa-check-circle`
- But metier: suivre le lancement reel de l'analyse puis declencher la premiere validation.

### 4. Validation

- Methode: `CreditController::storeValidation`
- Notifications deja en place:
  - `Validation credit en cours`
    - Destinataires: permission du validateur suivant (`EBEN-PER61`, `EBEN-PER62`, `EBEN-PER63` selon etape)
    - Type: `action_required`
  - `Dossier credit rejete`
    - Destinataires: demandeur + agent analyse
    - Type: `danger`
  - `Credit pret a debloquer`
    - Destinataires: `EBEN-PER64`
    - Type: `warning`
  - `Validation credit terminee`
    - Destinataires: demandeur + agent analyse
    - Type: `info`
- But metier: porter le dossier d'un validateur au suivant jusqu'au deblocage.

### 5. Deblocage

- Methode: `CreditController::storeDeblocage`
- Notifications:
  - `Credit debloque`
    - Destinataires: demandeur + agent analyse
    - Type: `info`
    - Icone: `fas fa-money-check-alt`
  - `Credit en remboursement`
    - Destinataires: `EBEN-PER65`
    - Type: `info`
    - Icone: `fas fa-calendar-check`
- But metier: informer les acteurs dossier et preparer les equipes remboursement.

### 6. Remboursement

- Methode: `CreditController::storeRemboursement`
- Notifications:
  - `Remboursement enregistre`
    - Destinataires: demandeur + agent analyse
    - Type: `info`
    - Icone: `fas fa-coins`
  - `Credit solde`
    - Destinataires: demandeur + agent analyse
    - Type: `info`
    - Icone: `fas fa-check-double`
- But metier: donner la visibilite sur les paiements et la cloture financiere du dossier.

### 7. Actions transverses

- Methodes: `annuler`, `suspendre`, `leverSuspension`, `signalerSuspect`, `leverSuspicion`
- Notifications:
  - `Dossier credit annule`
  - `Dossier credit suspendu`
  - `Suspension levee`
  - `Dossier signale suspect`
  - `Suspicion levee`
- Destinataires: demandeur + agent analyse
- But metier: eviter toute perte d'information sur les blocages et reactivations du dossier.

## Mapping permissions cle workflow credit

| Permission | Role fonctionnel attendu | Usage dans les notifications |
|---|---|---|
| `EBEN-PER60` | Agent credit validateur niveau 1 | Recoit le dossier apres analyse complete |
| `EBEN-PER61` | Charge des operations | Recoit le dossier a la soumission et peut affecter l'analyse |
| `EBEN-PER62` | Controleur | Recoit la notification quand la chaine de validation atteint son niveau |
| `EBEN-PER63` | Gerant | Recoit la notification pour la validation finale metier |
| `EBEN-PER64` | Deblocage | Recoit la notification `pret a debloquer` |
| `EBEN-PER65` | Remboursement | Recoit la notification apres deblocage pour suivi des echeances |

## Couverture actuelle

- Couverture workflow credit: elevee
- Point restant hors notification automatique: creation initiale du brouillon
- Point optionnel futur: relances echeances impayees / echeances en retard
