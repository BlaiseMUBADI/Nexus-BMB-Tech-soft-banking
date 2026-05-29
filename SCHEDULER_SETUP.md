# Configuration du Scheduler Laravel — À faire UNE SEULE FOIS

---

## EN LOCAL (WAMP Windows)

### Option A — Lancement manuel (le plus simple, pas de config)
Ouvrir un terminal dans le dossier du projet et taper :
```
php artisan notifications:proactive
```
À relancer quand vous voulez envoyer les alertes proactives.

---

### Option B — Automatique via le Planificateur de tâches Windows (une seule fois)
Ouvrir PowerShell en administrateur et coller ces 3 lignes :

```powershell
$action  = New-ScheduledTaskAction -Execute "php" -Argument "c:\wamp64\www\Nexus-BMB-Tech-soft-banking\artisan schedule:run" -WorkingDirectory "c:\wamp64\www\Nexus-BMB-Tech-soft-banking"
$trigger = New-ScheduledTaskTrigger -RepetitionInterval (New-TimeSpan -Minutes 1) -Once -At (Get-Date)
Register-ScheduledTask -TaskName "Laravel Scheduler - Nexus BMB" -Action $action -Trigger $trigger -RunLevel Highest
```

C'est tout. Windows lancera automatiquement le scheduler toutes les minutes.
Pour vérifier : ouvrir "Planificateur de tâches" dans le menu Démarrer → la tâche "Laravel Scheduler - Nexus BMB" doit apparaître.

Pour supprimer la tâche si besoin :
```powershell
Unregister-ScheduledTask -TaskName "Laravel Scheduler - Nexus BMB" -Confirm:$false
```

---

## EN PRODUCTION (LWS)

### Étape 1 — Se connecter au panneau LWS
Aller sur : https://panel.lws.fr → Hébergement → votre domaine

### Étape 2 — Aller dans "Cron" ou "Tâches planifiées"
Dans le panneau LWS, chercher la section : **Cron** ou **Tâches Cron** ou **Tâches planifiées**

### Étape 3 — Créer la tâche CRON (une seule fois)
Remplir le formulaire avec :

| Champ         | Valeur                                              |
|---------------|-----------------------------------------------------|
| Fréquence     | Toutes les minutes  (ou choisir * * * * *)          |
| Commande      | php /home/votre-compte/www/artisan schedule:run     |

> Remplacer `/home/votre-compte/www/` par le vrai chemin de votre projet sur LWS.
> Pour trouver le chemin exact : se connecter en FTP ou SSH et taper `pwd` dans le dossier du projet.

Si LWS propose un champ "Expression cron" :
```
* * * * * php /home/votre-compte/www/artisan schedule:run >> /dev/null 2>&1
```

### Étape 4 — Sauvegarder
Cliquer sur Enregistrer / Valider. C'est terminé, la tâche tourne en permanence.

---

## RÉSUMÉ — Combien de fois faut-il faire ça ?

| Environnement | Configuration  | Fréquence |
|---------------|---------------|-----------|
| WAMP local    | Option A (manuel) | Chaque fois que vous voulez lancer les alertes |
| WAMP local    | Option B (automatique) | UNE SEULE FOIS |
| LWS production | Cron panel LWS | UNE SEULE FOIS |

---

## TESTER QUE ÇA FONCTIONNE

Tester la commande en simulation (aucun envoi réel) :
```
php artisan notifications:proactive --dry-run
```

Tester l'envoi réel :
```
php artisan notifications:proactive
```

Vérifier que le scheduler reconnaît bien la commande :
```
php artisan schedule:list
```
