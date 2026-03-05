
# Dossier de Spécification – Gestion Professionnelle de la Caisse Bancaire

## 1. Introduction Générale
Ce document détaille l’architecture, les concepts, les processus et les scripts nécessaires pour transformer ton application en un véritable système bancaire professionnel, conforme aux standards du secteur.

---

## 2. État Initial et Fondations

### 2.1 Tables existantes essentielles
- **tb_agents** : Identité des employés/guichetiers
- **tb_clients** : Identité des clients
- **tb_comptes** : Comptes clients (solde, devise, type)
- **tb_transactions** : Historique des opérations clients (dépôt, retrait, virement)
- **tb_devises** : Référentiel des monnaies (USD, CDF, etc.)
- **tb_taux_echanges** : Taux de change entre devises

### 2.2 Limites actuelles
- Mélange des flux physiques (caisse) et virtuels (compte)
- Manque de traçabilité sur les mouvements internes et les frais
- Absence de contrôle physique (pointage, billetage)

---

## 3. Objectifs de la refonte
- Séparer clairement la gestion des caisses physiques et des comptes clients
- Sécuriser chaque opération par des contrôles métiers et des relations SQL
- Permettre l’audit, la traçabilité et la conformité réglementaire

---

## 4. Menu Complet « Gestion de Caisse » (à intégrer dans l’application)

### 4.1 Structure du menu
- **Ouverture de caisse** (par agent/guichet)
- **Dépôt client**
- **Retrait client**
- **Virement interne** (compte à compte)
- **Opérations de change** (multi-devises)
- **Approvisionnement/Dégagement** (mouvements entre caisses)
- **Clôture de caisse** (pointage physique, billetage)
- **Journal de caisse** (brouillard chronologique)
- **Rapports et tableaux de bord**

### 4.2 Exigences fonctionnelles
- Un agent ne peut opérer que sur sa caisse ouverte
- Toute opération doit être enregistrée dans la bonne devise
- Les mouvements internes ne doivent pas impacter les comptes clients
- Les frais doivent être paramétrables et traçables

---

## 5. Nouvelles Tables SQL à créer (avec explications)

### 5.1 tb_plan_comptable
> Référentiel des comptes comptables (actif, passif, produit, charge)
```sql
CREATE TABLE `tb_plan_comptable` (
   `numero_compte` varchar(20) NOT NULL,
   `libelle` varchar(191) NOT NULL,
   `type_compte` enum('ACTIF', 'PASSIF', 'CHARGE', 'PRODUIT') NOT NULL,
   PRIMARY KEY (`numero_compte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.2 tb_caisses_guichets
> Gestion des caisses physiques par guichet/agent
```sql
CREATE TABLE `tb_caisses_guichets` (
   `id` bigint unsigned NOT NULL AUTO_INCREMENT,
   `code_guichet` varchar(20) NOT NULL UNIQUE,
   `intitule` varchar(100) NOT NULL,
   `agent_matricule` varchar(50) NOT NULL,
   `devise_code` varchar(3) NOT NULL,
   `solde_en_caisse` decimal(18,2) DEFAULT '0.00',
   `statut_operationnel` enum('OUVERT', 'FERME', 'SUSPENDU') DEFAULT 'FERME',
   `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   CONSTRAINT `fk_guichet_agent` FOREIGN KEY (`agent_matricule`) REFERENCES `tb_agents` (`matricule`),
   CONSTRAINT `fk_guichet_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.3 tb_mouvements_inter_caisses
> Traçabilité des flux physiques entre caisses/guichets
```sql
CREATE TABLE `tb_mouvements_inter_caisses` (
   `id` bigint unsigned NOT NULL AUTO_INCREMENT,
   `guichet_source_id` bigint unsigned DEFAULT NULL,
   `guichet_dest_id` bigint unsigned DEFAULT NULL,
   `agent_initiateur` varchar(50) NOT NULL,
   `type_flux` enum('ALIMENTATION', 'DEGAGEMENT', 'TRANSFERT') NOT NULL,
   `montant` decimal(18,2) NOT NULL,
   `devise_code` varchar(3) NOT NULL,
   `reference_bordereau` varchar(50) UNIQUE,
   `date_mouvement` timestamp DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   CONSTRAINT `fk_mouv_guichet_src` FOREIGN KEY (`guichet_source_id`) REFERENCES `tb_caisses_guichets` (`id`),
   CONSTRAINT `fk_mouv_guichet_dest` FOREIGN KEY (`guichet_dest_id`) REFERENCES `tb_caisses_guichets` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.4 tb_frais_parametrages
> Paramétrage dynamique des frais bancaires
```sql
CREATE TABLE `tb_frais_parametrages` (
   `id` bigint unsigned NOT NULL AUTO_INCREMENT,
   `libelle` varchar(100) NOT NULL,
   `type_operation` enum('DEPOT', 'RETRAIT', 'VIREMENT', 'TENUE_COMPTE') NOT NULL,
   `montant_fixe` decimal(18,2) DEFAULT '0.00',
   `pourcentage` decimal(5,2) DEFAULT '0.00',
   `devise_code` varchar(3) NOT NULL,
   `compte_produit_associe` varchar(20) NOT NULL,
   PRIMARY KEY (`id`),
   CONSTRAINT `fk_frais_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`),
   CONSTRAINT `fk_frais_plan` FOREIGN KEY (`compte_produit_associe`) REFERENCES `tb_plan_comptable` (`numero_compte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.5 tb_ecritures_comptables
> Journal à partie double (traçabilité totale)
```sql
CREATE TABLE `tb_ecritures_comptables` (
   `id` bigint unsigned NOT NULL AUTO_INCREMENT,
   `transaction_id` bigint unsigned DEFAULT NULL,
   `numero_compte_comptable` varchar(20) NOT NULL,
   `libelle` varchar(255) NOT NULL,
   `debit` decimal(18,2) DEFAULT '0.00',
   `credit` decimal(18,2) DEFAULT '0.00',
   `devise_code` varchar(3) NOT NULL,
   `date_ecriture` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   CONSTRAINT `fk_ecr_plan` FOREIGN KEY (`numero_compte_comptable`) REFERENCES `tb_plan_comptable` (`numero_compte`),
   CONSTRAINT `fk_ecr_devise` FOREIGN KEY (`devise_code`) REFERENCES `tb_devises` (`code_iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.6 tb_arretes_caisses
> Arrêté de caisse (pointage physique, billetage)
```sql
CREATE TABLE `tb_arretes_caisses` (
   `id` bigint unsigned NOT NULL AUTO_INCREMENT,
   `guichet_id` bigint unsigned NOT NULL,
   `solde_comptable` decimal(18,2) NOT NULL,
   `solde_physique` decimal(18,2) NOT NULL,
   `ecart_caisse` decimal(18,2) NOT NULL,
   `detail_billetage` json DEFAULT NULL,
   `date_arrete` timestamp DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   CONSTRAINT `fk_arrete_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `tb_caisses_guichets` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.7 Mises à jour des tables existantes
- Ajouter la colonne `devise_code` et `frais_montant` à `tb_transactions` avec clé étrangère

---

## 6. Relations et Sécurité

- Toutes les opérations sont liées à la table `tb_devises` (clé étrangère)
- Les écritures comptables référencent le plan comptable (`tb_plan_comptable`)
- Les mouvements de caisse relient les guichets entre eux
- Les frais sont toujours affectés à un compte de produit
- Les contrôles métiers empêchent toute opération incohérente (devise, solde, statut)

---

## 7. Processus Métier Détaillés

### 7.1 Ouverture de caisse
- L’agent sélectionne sa caisse et la devise
- Le statut passe à OUVERT
- Uniquement alors, il peut effectuer des opérations

### 7.2 Dépôt client
- Vérification : caisse ouverte, devise cohérente
- Crédit du compte client
- Crédit de la caisse physique
- Génération automatique des écritures comptables (débit caisse, crédit client)

### 7.3 Retrait client
- Vérification : solde suffisant, caisse ouverte
- Débit du compte client
- Débit de la caisse physique
- Calcul et prélèvement des frais (via tb_frais_parametrages)
- Génération des écritures comptables (débit client, crédit caisse, crédit produit)

### 7.4 Virement interne
- Mouvement entre deux comptes clients
- Deux écritures : débit et crédit

### 7.5 Opérations de change
- Utilisation de tb_taux_echanges
- Contrôle des devises et calcul automatique

### 7.6 Approvisionnement/Dégagement
- Mouvement entre caisses (ex : coffre vers guichet)
- Enregistrement dans tb_mouvements_inter_caisses
- Mise à jour des soldes physiques

### 7.7 Clôture de caisse (pointage)
- Calcul du solde théorique (logiciel)
- Comptage physique (billetage)
- Saisie du détail des billets
- Calcul de l’écart
- Enregistrement dans tb_arretes_caisses

### 7.8 Journal de caisse (brouillard)
- Liste chronologique de toutes les opérations du jour
- Filtrage par agent, guichet, date

---

## 8. Tableaux de bord et Rapports

### 8.1 Indicateurs clés
- Total dépôts clients (somme des soldes)
- Disponibilité physique (somme des soldes caisses ouvertes)
- Volume de change (total des opérations de change)
- Bénéfice sur frais (SUM(frais_montant) sur tb_transactions)
- Performance par agent (classement des agents par volume/frais générés)

### 8.2 Exemples de requêtes SQL
```sql
-- Bénéfice mensuel
SELECT SUM(frais_montant) as total_benefice
FROM tb_transactions
WHERE MONTH(created_at) = 3 AND YEAR(created_at) = 2026;

-- Performance par agent
SELECT agent_matricule, SUM(frais_montant) as total_frais
FROM tb_transactions
GROUP BY agent_matricule
ORDER BY total_frais DESC;
```

---

## 9. Lexique et Concepts Bancaires

- **Caisse guichet** : Point de service physique d’un agent
- **Alimentation** : Approvisionnement d’un guichet
- **Dégagement** : Retour d’excédent vers la caisse centrale
- **Arrêté de caisse** : Contrôle fin de journée
- **Solde comptable** : Ce que dit le logiciel
- **Solde physique** : Ce que compte l’agent
- **Billetage** : Détail du comptage des billets
- **Produit** : Compte de revenu pour la banque
- **Partie double** : Principe comptable (chaque opération = 1 débit + 1 crédit)

---

## 10. Étapes de Mise en Œuvre (Roadmap)

1. Créer toutes les nouvelles tables (voir scripts ci-dessus)
2. Mettre à jour les tables existantes (ajout de colonnes, clés étrangères)
3. Développer les contrôleurs Laravel :
    - Vérification ouverture de caisse
    - Vérification cohérence devise
    - Génération automatique des écritures comptables
    - Gestion dynamique des frais
4. Créer les vues et interfaces :
    - Ouverture/Fermeture de caisse
    - Dépôt/Retrait client
    - Pointage physique (billetage)
    - Brouillard de caisse (journal)
    - Rapports de performance et tableaux de bord
5. Tester chaque processus avec des cas réels (scénarios d’usage)
6. Former les utilisateurs (agents, superviseurs)

---

## 11. Conseils et Bonnes Pratiques

- Toujours lier chaque opération à la bonne devise
- Ne jamais permettre d’opération sur une caisse fermée
- Utiliser le plan comptable pour toute écriture
- Séparer clairement les flux physiques (caisse) et virtuels (compte)
- Automatiser la génération des écritures pour éviter les erreurs humaines
- Prévoir des audits réguliers (rapports d’écart, arrêtés)

---

**Ce document est la feuille de route complète pour professionnaliser la gestion de caisse dans ton application bancaire Nexus.**

Pour toute extension, adaptation ou script SQL complet, se référer à ce dossier ou demander la version intégrale adaptée à ton contexte.
