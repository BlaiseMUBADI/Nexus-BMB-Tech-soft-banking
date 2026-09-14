<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * AUDIT COMPLET DES AUTORISATIONS (EBEN-PER1 à EBEN-PER127) — nettoyage
 * professionnel demandé : doublons morts, permissions mal nommées, mauvaise
 * classification de module.
 *
 * ── 1. BUG CRITIQUE : EBEN-PER90 ──────────────────────────────────────
 * Catalogué "Modifier service RH" (jamais vérifié nulle part dans le code
 * pour cet usage — le vrai contrôle RH passe par EBEN-PER103), mais
 * `routes/web.php` l'utilise réellement pour protéger TOUT le module
 * Recouvrement Automatique (finance). Conséquence concrète et vérifiée :
 * le rôle "Agent RH" avait accès au recouvrement automatique (prélèvements
 * RMB), sans aucun rapport avec ses attributions RH.
 * Correction : on renomme EBEN-PER90 pour refléter son VRAI usage (plutôt
 * que de créer un nouveau code et migrer 3 fichiers de code), on le retire
 * du rôle Agent RH, et on l'assigne aux rôles de supervision pertinents.
 *
 * ── 2. PERMISSION MANQUANTE : EBEN-PER56 ──────────────────────────────
 * "Soumettre demande crédit" est référencée à 6+ endroits du code
 * (routes/credit.php, credit/show.blade.php, credit/_table.blade.php)
 * mais n'existe plus du tout dans tb_permissions. Le workflow ne casse
 * pas au premier abord (repli sur EBEN-PER53 via un OR dans les routes),
 * mais aucun administrateur ne peut attribuer cette permission de façon
 * granulaire tant qu'elle n'existe pas. Recréée avec les mêmes rôles que
 * EBEN-PER54 (Créer demande crédit), son équivalent amont dans le workflow.
 *
 * ── 3. DOUBLONS MORTS (34 permissions) ────────────────────────────────
 * Vérifié par recherche exhaustive dans app/, routes/, resources/views/ :
 * AUCUNE de ces permissions n'est vérifiée par le moindre `permission:`,
 * `hasPermission()` ou `in_array(...,$userPermCodes)`. Ce sont des
 * tentatives de granularité CRUD (Ajouter/Modifier/Supprimer par module)
 * abandonnées au profit du système actuel (EBEN-PER103/104/105/106/107/
 * 108/121 etc.), ou des permissions "épargne/rapports/écritures/sécurité"
 * jamais branchées à une fonctionnalité réelle. Elles restent pourtant
 * cochées sur 1 à 9 rôles chacune — donnant une fausse impression de
 * contrôle à qui configure les rôles (cocher "Effectuer dépôts" ne limite
 * RIEN, car aucune route ne vérifie ce code).
 *
 * ── 4. LIBELLÉ TROMPEUR : EBEN-PER52 ───────────────────────────────────
 * Catalogué "Grand livre" mais protège en réalité 4 états financiers
 * distincts (Grand livre + Balance + Compte de résultat + Bilan), cf.
 * routes/comptabilite.php.
 */
return new class extends Migration
{
    /** Permissions dupliquées/mortes confirmées par audit exhaustif du code. */
    private const PERMISSIONS_MORTES = [
        // Granularité CRUD abandonnée (remplacée par EBEN-PER103/104/105/106/107/108/121)
        'EBEN-PER80', 'EBEN-PER81', 'EBEN-PER82',   // Ajouter/Modifier/Supprimer client
        'EBEN-PER83', 'EBEN-PER84', 'EBEN-PER85',   // Ajouter/Modifier/Supprimer compte
        'EBEN-PER86', 'EBEN-PER87', 'EBEN-PER88',   // Ajouter/Modifier/Supprimer agent RH
        'EBEN-PER91', 'EBEN-PER92',                 // Supprimer service/poste RH, Ajouter affectation RH
        'EBEN-PER95', 'EBEN-PER96', 'EBEN-PER97',   // Ajouter/Modifier/Supprimer opération caisse
        'EBEN-PER100', 'EBEN-PER101', 'EBEN-PER102', // Ajouter/Modifier/Supprimer opération|workflow crédit
        // Caisse : jamais vérifiées (le vrai contrôle passe par EBEN-PER10/11)
        'EBEN-PER12', 'EBEN-PER13', 'EBEN-PER14',
        'EBEN-PER22', 'EBEN-PER23', 'EBEN-PER24', 'EBEN-PER26',
        // Épargne : module non implémenté dans le code actuel
        'EBEN-PER27', 'EBEN-PER28', 'EBEN-PER29',
        // Rapports génériques : jamais branchés
        'EBEN-PER36', 'EBEN-PER37', 'EBEN-PER38',
        // Comptabilité : écritures manuelles jamais implémentées (le vrai journal = EBEN-PER50)
        'EBEN-PER39', 'EBEN-PER40', 'EBEN-PER41',
        // Sécurité : jamais branchée à une fonctionnalité réelle
        'EBEN-PER43',
    ];

    public function up(): void
    {
        $now = now();

        // ── 1. Corriger EBEN-PER90 : renommer pour refléter son vrai usage ──
        DB::table('tb_permissions')->where('code', 'EBEN-PER90')->update([
            'nom' => 'Accéder au recouvrement automatique (RMB)',
            'description' => "Accès au module Recouvrement Automatique (prélèvement RMB sur échéances en retard). "
                . "Anciennement « Modifier service RH » par erreur (jamais vérifié pour cet usage — le module RH "
                . "utilise EBEN-PER103).",
            'updated_at' => $now,
        ]);

        // Retirer du rôle Agent RH (fuite de privilège corrigée : un agent RH
        // n'a aucune raison d'accéder au recouvrement financier).
        DB::table('tb_role_permission')
            ->where('permission_code', 'EBEN-PER90')
            ->where('role_code', 'EBEN-ROL4')
            ->delete();

        // Assigner aux rôles de supervision pertinents pour le recouvrement
        // (Gérant, Directeur) — Administrateur l'a déjà.
        foreach (['EBEN-ROL12', 'EBEN-ROL3'] as $roleCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => $roleCode,
                'permission_code' => 'EBEN-PER90',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ── 2. Recréer EBEN-PER56 (manquante, référencée par le code) ──
        DB::table('tb_permissions')->insertOrIgnore([
            'code' => 'EBEN-PER56',
            'nom' => 'Soumettre demande crédit',
            'description' => "Soumettre un dossier crédit en brouillon pour lancer le circuit de validation "
                . "(Agent crédit → Contrôleur → Chargé opérations → Gérant).",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Mêmes rôles que EBEN-PER54 (Créer demande crédit), son équivalent
        // amont dans le workflow de création de dossier.
        $rolesCreation = DB::table('tb_role_permission')
            ->where('permission_code', 'EBEN-PER54')
            ->pluck('role_code');
        foreach ($rolesCreation as $roleCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => $roleCode,
                'permission_code' => 'EBEN-PER56',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ── 3. Nettoyer les 34 permissions doublons/mortes confirmées ──
        DB::table('tb_role_permission')->whereIn('permission_code', self::PERMISSIONS_MORTES)->delete();
        DB::table('tb_permissions')->whereIn('code', self::PERMISSIONS_MORTES)->delete();

        // ── 4. Libellé EBEN-PER52 : refléter le périmètre réel ──
        DB::table('tb_permissions')->where('code', 'EBEN-PER52')->update([
            'nom' => 'États financiers (Grand livre, Balance, Résultat, Bilan)',
            'description' => "Consulter et imprimer le grand livre, la balance générale, le compte de résultat "
                . "et le bilan OHADA.",
            'updated_at' => $now,
        ]);

        // ── 5. Documenter le rôle transverse d'EBEN-PER1 ──
        // Ce code sert de "passe-partout" superviseur dans plusieurs modules
        // (Caisse, Crédit) au-delà du panneau d'administration — comportement
        // voulu, mais jamais documenté jusqu'ici.
        DB::table('tb_permissions')->where('code', 'EBEN-PER1')->update([
            'description' => "Accès au panneau Administration. Sert AUSSI de passe-partout superviseur dans "
                . "d'autres modules (caisse, crédit) — intentionnel, réservé aux profils de direction.",
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        // Volontairement minimal : restaure les libellés d'origine et retire
        // les nouvelles attributions, mais ne recrée pas les 34 permissions
        // mortes (leur suppression est le but recherché ; un rollback ne
        // doit pas réintroduire la confusion qu'elles causaient).
        DB::table('tb_permissions')->where('code', 'EBEN-PER90')->update([
            'nom' => 'Modifier service RH',
            'description' => null,
            'updated_at' => now(),
        ]);
        DB::table('tb_role_permission')
            ->where('permission_code', 'EBEN-PER90')
            ->whereIn('role_code', ['EBEN-ROL12', 'EBEN-ROL3'])
            ->delete();
        DB::table('tb_role_permission')->where('permission_code', 'EBEN-PER56')->delete();
        DB::table('tb_permissions')->where('code', 'EBEN-PER56')->delete();
        DB::table('tb_permissions')->where('code', 'EBEN-PER52')->update([
            'nom' => 'Grand livre',
            'description' => null,
            'updated_at' => now(),
        ]);
        DB::table('tb_permissions')->where('code', 'EBEN-PER1')->update([
            'description' => null,
            'updated_at' => now(),
        ]);
    }
};
