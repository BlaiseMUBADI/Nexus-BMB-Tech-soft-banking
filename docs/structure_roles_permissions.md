# Structure des rôles, postes, services et permissions pour Nexus BMB Tech

## 1. Hiérarchie et concepts

- **Service** : Département de l’entreprise (ex : Comptabilité, Crédit, Opérations)
- **Poste** : Fonction RH occupée par l’agent (ex : Archiviste, Guichetier)
- **Rôle** : Ensemble de permissions informatiques (ex : Auditeur_Donnees, Operateur_Caisse)
- **Permission** : Action précise autorisée (ex : consulter-pieces-jointes, effectuer-depot)

## 2. Exemple concret

### Service : Comptabilité & Finance
- **Poste** : Chef Comptable
  - Rôle : Comptable_Senior
  - Permissions : cloturer-exercice, valider-ecritures, generer-bilan, voir-toutes-transactions
- **Poste** : Archiviste Comptable
  - Rôle : Auditeur_Donnees
  - Permissions : consulter-pieces-jointes, exporter-journaux, rechercher-transaction-historique

### Service : Crédit & Engagement
- **Poste** : Analyste de Crédit
  - Rôle : Analyste_Dossier
  - Permissions : evaluer-solvabilite, creer-dossier-credit, modifier-garanties, editer-plan-remboursement
- **Poste** : Agent de Recouvrement
  - Rôle : Suivi_Paiement
  - Permissions : voir-retards-paiement, enregistrer-relance, bloquer-compte-defaillant

### Service : Opérations & Caisse
- **Poste** : Caissier Principal
  - Rôle : Superviseur_Caisse
  - Permissions : ouvrir-fermer-coffre, valider-gros-retrait, valider-transfert-agence, annuler-erreur-saisie
- **Poste** : Guichetier
  - Rôle : Operateur_Caisse
  - Permissions : effectuer-depot, effectuer-retrait-limite, consulter-solde-membre

## 3. Structure de la base de données

- **services** (id, nom, ...)
- **postes** (id, nom, service_id, ...)
- **roles** (id, name, description)
- **permissions** (id, name, description)
- **permission_role** (role_id, permission_id)
- **agent_role** (agent_id, role_id)
- **agents** (id, nom, poste_id, service_id, ...)

## 4. Pourquoi séparer Poste et Rôle ?

- Deux agents avec le même poste peuvent avoir des rôles différents (ex : un archiviste temporairement admin).
- Les permissions sont attribuées aux rôles, pas directement aux postes.

## 5. Interface de gestion

- Menu déroulant pour le Service
- Champ texte ou liste pour le Poste
- Cases à cocher pour les Rôles/Permissions

## 6. Contrôle d’accès Laravel

- Utiliser les policies/gates dans `AuthServiceProvider.php` pour restreindre les actions selon les permissions du rôle de l’agent.
- Exemple : Interdire à un archiviste de faire un retrait si son rôle ne le permet pas.

---

**Conseil** : Toujours privilégier la flexibilité en liant les rôles aux agents, et les permissions aux rôles.
