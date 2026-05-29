# Matrice Notifications Tresorerie et Caisse (2026-05-03)

Cette matrice detaille les notifications du workflow tresorerie, guichet, operations caisse et mobile.

## Vue d'ensemble

| Domaine | Evenement | Destinataires | Type | Action |
|---|---|---|---|---|
| Caisse | Changement statut guichet | Utilisateurs avec `EBEN-PER44` | info | Voir l'etat du guichet |
| Caisse | Cloture soumise | Utilisateurs avec `EBEN-PER46` | action_required | Verifier et traiter la cloture |
| Caisse | Demande d'approvisionnement | Utilisateurs avec `EBEN-PER46` | action_required | Approuver ou rejeter la demande |
| Tresorerie | Coffre central approvisionne | Utilisateurs avec `EBEN-PER44` | info | Voir l'etat du coffre |
| Tresorerie | Guichet ravitaille | Agent du guichet cible | info | Voir la mise a disposition des fonds |
| Tresorerie | Demande de ravitaillement approuvee | Agent demandeur | info | Voir la demande traitee |
| Tresorerie | Demande de ravitaillement rejetee | Agent demandeur | warning | Voir le motif |
| Tresorerie | Cloture guichet approuvee | Agent cloturant | info | Voir la cloture confirmee |
| Tresorerie | Cloture guichet rejetee | Agent cloturant | warning | Voir le motif |
| Tresorerie | Ligne de cloture approuvee | Agent cloturant | info | Voir l'avancement devise par devise |
| Tresorerie | Ligne de cloture rejetee | Agent cloturant | warning | Voir le motif |
| Operations caisse | Operation annulee | Utilisateurs avec `EBEN-PER44` | warning | Consulter le journal |
| Operations caisse | Demande de modification/suppression soumise | Utilisateurs avec `EBEN-PER44` | warning | Traiter la demande |
| Operations caisse | Demande approuvee | Agent demandeur | info | Consulter le journal |
| Operations caisse | Demande rejetee | Agent demandeur | warning | Consulter le motif |
| Mobile | Demande de dotation mobile | Utilisateurs avec `EBEN-PER46` | action_required | Traiter la dotation |
| Mobile | Reversement mobile declare | Utilisateurs avec `EBEN-PER46` | action_required | Confirmer le reversement |

## 1. Caisse guichet

### Changement de statut

- Methode: `CaisseController::changerStatut`
- Notification: `Statut guichet modifie`
- Destinataires: utilisateurs avec `EBEN-PER44`
- Type: `info`
- Icone: `fas fa-store`
- Action: `caisses.ouverture`
- But metier: informer la supervision qu'un guichet a change d'etat operationnel.

### Cloture soumise

- Methode: `CaisseController::confirmerFermeture`
- Notification: `Cloture guichet en attente`
- Destinataires: utilisateurs avec `EBEN-PER46`
- Type: `action_required`
- Icone: `fas fa-clipboard-check`
- Action: `tresorerie.etat-coffre`
- But metier: declencher la verification superviseur/tresorerie.

### Demande d'approvisionnement

- Methode: `CaisseController::demanderApprovisionnement`
- Notification: `Nouvelle demande de ravitaillement`
- Destinataires: utilisateurs avec `EBEN-PER46`
- Type: `action_required`
- Icone: `fas fa-hand-holding-usd`
- Action: `tresorerie.etat-coffre`
- But metier: permettre au tresorier de traiter la demande de liquidite d'un guichet.

## 2. Tresorerie coffre

### Approvisionnement externe du coffre

- Methode: `TresorerieController::approvisionner`
- Notification: `Coffre central ravitaille`
- Destinataires: utilisateurs avec `EBEN-PER44`
- Type: `info`
- Icone: `fas fa-piggy-bank`
- Action: `tresorerie.etat-coffre`

### Alimentation d'un guichet

- Methode: `TresorerieController::alimenter`
- Notification: `Ravitaillement recu`
- Destinataires: agent actif du guichet cible via matricule
- Type: `info`
- Icone: `fas fa-donate`
- Action: `caisses.ouverture`

### Demandes de ravitaillement

- Methodes:
  - `TresorerieController::approuverDemande`
  - `TresorerieController::rejeterDemande`
- Notifications:
  - `Demande de ravitaillement approuvee`
  - `Demande de ravitaillement rejetee`
- Destinataires: agent initiateur de la demande
- Types: `info` / `warning`
- Action: `caisses.mes.demandes`

### Clotures guichet

- Methodes:
  - `TresorerieController::approuverCloture`
  - `TresorerieController::rejeterCloture`
  - `TresorerieController::approuverLigneCloture`
  - `TresorerieController::rejeterLigneCloture`
- Destinataires: agent cloturant
- Notifications:
  - `Cloture guichet approuvee`
  - `Cloture guichet rejetee`
  - `Cloture devise traitee`
  - `Cloture devise rejetee`
- Action: `caisses.ouverture`
- But metier: donner le retour immediat au guichetier sur le traitement de sa cloture.

## 3. Operations caisse

### Annulation d'operation

- Methode: `OperationCaisseController::annuler`
- Notification: `Operation annulee`
- Destinataires: utilisateurs avec `EBEN-PER44`
- Type: `warning`
- Icone: `fas fa-ban`
- Action: `caisses.journal.page`

### Demande de modification ou suppression

- Methode: `OperationCaisseController::demanderModification`
- Notification: `Nouvelle demande de modification`
- Destinataires: utilisateurs avec `EBEN-PER44`
- Type: `warning`
- Icone: `fas fa-edit`
- Action: `caisses.demandes.modification.page`

### Decision superviseur sur demande

- Methodes:
  - `OperationCaisseController::approuverModification`
  - `OperationCaisseController::rejeterModification`
- Destinataires: agent demandeur
- Notifications:
  - `Demande approuvee`
  - `Demande rejetee`
- Types: `info` / `warning`
- Action: `caisses.journal.page`

## 4. Workflow mobile

### Demande de dotation mobile

- Methode: `OperationCaisseController::mobileDepart`
- Notification: `Demande de dotation mobile`
- Destinataires: utilisateurs avec `EBEN-PER46`
- Type: `action_required`
- Icone: `fas fa-shipping-fast`
- Action: `tresorerie.etat-coffre`

### Declaration de reversement mobile

- Methode: `OperationCaisseController::mobileRetour`
- Notification: `Reversement mobile declare`
- Destinataires: utilisateurs avec `EBEN-PER46`
- Type: `action_required`
- Icone: `fas fa-undo-alt`
- Action: `tresorerie.etat-coffre`

## 5. Gouvernance permissions

| Permission | Usage notification | Observation |
|---|---|---|
| `EBEN-PER44` | Supervision et visibilite | Reserve aux notifications informationnelles et warnings de supervision |
| `EBEN-PER46` | Decision tresorerie | Recoit les notifications action_required sur demandes, clotures et mobile |
| `EBEN-PER45` | Approvisionner coffre | Permission d'action, pas de diffusion de notification ciblee |
| `EBEN-PER47` | Alimenter guichets | Permission d'action, le retour de notification va a l'agent du guichet |

## Couverture actuelle

- Couverture tresorerie/caisse: elevee
- Couverture mobile: elevee
- Point optionnel futur: notifications proactives sur ecarts de cloture, fonds critiques coffre, ou demandes non traitees au-dela d'un delai seuil
