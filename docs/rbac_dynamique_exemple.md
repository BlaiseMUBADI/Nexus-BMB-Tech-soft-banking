# RBAC Dynamique avec Spatie Laravel-Permission pour Nexus BMB Tech

## Introduction

Ce document explique comment mettre en place un contrôle d'accès dynamique (RBAC) dans une application Laravel pour la Coopec EBEN, en utilisant le package Spatie Laravel-Permission. L'objectif est de permettre à l'administrateur de gérer les rôles et permissions via une interface, sans modifier le code.

---

## 1. Structure des tables

- **permissions** : Liste des actions possibles (ex : `effectuer-retrait`, `voir-rapport`, `supprimer-client`).
- **roles** : Noms des fonctions (ex : `Caissier`, `Admin`, `Archiviste`).
- **role_has_permissions** : Table pivot liant les rôles aux permissions.
- **model_has_roles** : Table pivot liant les utilisateurs aux rôles.

> Ces tables sont créées automatiquement par le package Spatie.

---

## 2. Utilisation dans le code (Blade)

Au lieu de tester le poste, on utilise les permissions :

```blade
{{-- Le bouton ne s'affiche que si l'utilisateur a la permission --}}
@can('effectuer-retrait')
    <button class="btn btn-success">Valider le Retrait</button>
@endcan
```

Pour les menus dynamiques :

```blade
<ul class="nav nav-pinner">
    @can('voir-comptabilite')
        <li class="nav-item">... Menu Compta ...</li>
    @endcan
    @can('voir-credit')
        <li class="nav-item">... Menu Crédit ...</li>
    @endcan
</ul>
```

---

## 3. Interface d'administration (exemple de vue)

Voici un exemple de formulaire pour gérer dynamiquement les permissions d'un rôle :

```blade
<form method="POST" action="{{ route('roles.updatePermissions', $role->id) }}">
    @csrf
    @method('PUT')
    <h5>Permissions pour le rôle : {{ $role->name }}</h5>
    <div class="row">
        @foreach($permissions as $permission)
            <div class="col-md-4 mb-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                        id="perm_{{ $permission->id }}" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                        {{ $permission->name }}
                    </label>
                </div>
            </div>
        @endforeach
    </div>
    <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save mr-1"></i>Enregistrer</button>
</form>
```

---

## 4. Exemple d'actions déjà présentes dans le projet

- `effectuer-retrait`
- `voir-rapport`
- `supprimer-client`
- `ajouter-utilisateur`
- `modifier-service`
- `voir-credit`

Ajoutez vos propres permissions selon les besoins métier.

---

## 5. Recommandation

Utilisez le package [spatie/laravel-permission](https://spatie.be/docs/laravel-permission/v5/introduction) pour une gestion robuste, sécurisée et évolutive des rôles et permissions.

---

## 6. Avantages pour la Coopec EBEN

- **Évolutivité** : Ajout/suppression de rôles et permissions sans toucher au code.
- **Sécurité** : Gestion centralisée et instantanée des accès.
- **Simplicité** : Interface claire pour l’admin, maintenance facilitée.

---

*Document généré le 22/02/2026 pour Nexus BMB Tech - Coopec EBEN.*
