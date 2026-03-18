<?php

/**
 * ============================================================
 * MODULE CRÉDIT – PERMISSIONS & AFFECTATIONS RÔLES
 * ============================================================
 * Permissions PER53 → PER72 créées.
 * Affectations par rôle :
 *   ROL1 = Administrateur      → tout
 *   ROL2 = Caissier            → PER65 (enregistrer remboursement)
 *   ROL3 = Directeur/Gérant    → PER53,57,63,66,67,68,69,70,71,72
 *   ROL5 = Superviseur         → PER53,57,62,67,68,70,71,72
 *   ROL6 = Chargé de crédit    → PER53,54,55,56,57,58,59,60,61,64,65,70,71
 *   ROL8 = Agent commercial    → PER53,54,55,56,57,71
 * ============================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // ====================================================
        // 1. CRÉER LES PERMISSIONS PER53 → PER72
        // ====================================================
        DB::table('tb_permissions')->insertOrIgnore([
            // --- Module Crédit : Accès & Navigation ---
            ['code' => 'EBEN-PER53', 'nom' => 'Voir liste crédits',            'description' => 'Accéder à la liste des dossiers crédit',                      'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER54', 'nom' => 'Créer demande crédit',          'description' => "Saisir une nouvelle demande de crédit",                        'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER55', 'nom' => 'Modifier demande brouillon',    'description' => "Modifier un dossier en statut BROUILLON",                      'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER56', 'nom' => 'Soumettre demande crédit',      'description' => "Soumettre un dossier pour analyse (BROUILLON → SOUMIS)",       'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER57', 'nom' => 'Voir détail dossier crédit',    'description' => "Consulter le détail complet d'un dossier crédit",              'created_at' => $now, 'updated_at' => $now],

            // --- Module Crédit : Analyse ---
            ['code' => 'EBEN-PER58', 'nom' => 'Saisir analyse crédit',         'description' => "Démarrer et saisir l'analyse d'un dossier crédit",             'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER59', 'nom' => 'Compléter analyse crédit',      'description' => "Marquer l'analyse comme complète",                             'created_at' => $now, 'updated_at' => $now],

            // --- Module Crédit : Validation (4 blocs) ---
            ['code' => 'EBEN-PER60', 'nom' => 'Valider bloc Agent crédit',     'description' => "Validation niveau 1 – Chargé de crédit",                      'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER61', 'nom' => 'Valider bloc Chargé opérations','description' => "Validation niveau 2 – Chargé des opérations",                 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER62', 'nom' => 'Valider bloc Contrôleur',       'description' => "Validation niveau 3 – Contrôleur interne",                    'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER63', 'nom' => 'Valider bloc Gérant',           'description' => "Validation niveau 4 – Gérant / Directeur (validation finale)", 'created_at' => $now, 'updated_at' => $now],

            // --- Module Crédit : Déblocage & Remboursement ---
            ['code' => 'EBEN-PER64', 'nom' => 'Débloquer crédit',              'description' => "Effectuer le déblocage des fonds (PRET_A_DEBLOQUER → DEBLOQUE)",'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER65', 'nom' => 'Enregistrer remboursement',     'description' => "Enregistrer un paiement d'échéance ou remboursement",          'created_at' => $now, 'updated_at' => $now],

            // --- Module Crédit : Actions transverses ---
            ['code' => 'EBEN-PER66', 'nom' => 'Annuler dossier crédit',        'description' => "Annuler définitivement un dossier crédit",                     'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER67', 'nom' => 'Suspendre dossier crédit',      'description' => "Mettre un dossier en suspension temporaire",                   'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER68', 'nom' => 'Signaler dossier suspect',      'description' => "Signaler un dossier comme suspect (fraude, irrégularité)",      'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER69', 'nom' => 'Lever suspension/suspicion',    'description' => "Lever une suspension ou un signalement de suspicion",           'created_at' => $now, 'updated_at' => $now],

            // --- Module Crédit : Supervision & Rapports ---
            ['code' => 'EBEN-PER70', 'nom' => 'Tableau de bord crédit',        'description' => "Accéder au tableau de bord de supervision du crédit",          'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER71', 'nom' => 'Imprimer échéancier PDF',       'description' => "Générer et imprimer l'échéancier de remboursement PDF",         'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER72', 'nom' => 'Historique audit dossier',      'description' => "Consulter le journal d'audit complet d'un dossier crédit",      'created_at' => $now, 'updated_at' => $now],
        ]);

        // ====================================================
        // 2. AFFECTATIONS PAR RÔLE
        // ====================================================

        // ROL1 = Administrateur → TOUTES les permissions crédit
        $allCreditPerms = [
            'EBEN-PER53','EBEN-PER54','EBEN-PER55','EBEN-PER56','EBEN-PER57',
            'EBEN-PER58','EBEN-PER59','EBEN-PER60','EBEN-PER61','EBEN-PER62',
            'EBEN-PER63','EBEN-PER64','EBEN-PER65','EBEN-PER66','EBEN-PER67',
            'EBEN-PER68','EBEN-PER69','EBEN-PER70','EBEN-PER71','EBEN-PER72',
        ];
        foreach ($allCreditPerms as $permCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => 'EBEN-ROL1',
                'permission_code' => $permCode,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ROL2 = Caissier → Enregistrer remboursement seulement
        DB::table('tb_role_permission')->insertOrIgnore([
            'role_code' => 'EBEN-ROL2',
            'permission_code' => 'EBEN-PER65',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ROL3 = Directeur/Gérant → Supervision + validation niveau 4 + annulation/suspension
        foreach (['EBEN-PER53','EBEN-PER57','EBEN-PER63','EBEN-PER66','EBEN-PER67','EBEN-PER68','EBEN-PER69','EBEN-PER70','EBEN-PER71','EBEN-PER72'] as $permCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => 'EBEN-ROL3',
                'permission_code' => $permCode,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ROL5 = Superviseur/Contrôleur → Voir + validation niveau 3 + suspension/suspicion
        foreach (['EBEN-PER53','EBEN-PER57','EBEN-PER62','EBEN-PER67','EBEN-PER68','EBEN-PER70','EBEN-PER71','EBEN-PER72'] as $permCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => 'EBEN-ROL5',
                'permission_code' => $permCode,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ROL6 = Chargé de crédit → Analyse, validations niv 1+2, déblocage, remboursement
        foreach (['EBEN-PER53','EBEN-PER54','EBEN-PER55','EBEN-PER56','EBEN-PER57','EBEN-PER58','EBEN-PER59','EBEN-PER60','EBEN-PER61','EBEN-PER64','EBEN-PER65','EBEN-PER70','EBEN-PER71'] as $permCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => 'EBEN-ROL6',
                'permission_code' => $permCode,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ROL8 = Agent commercial → Créer, modifier brouillon, soumettre, voir
        foreach (['EBEN-PER53','EBEN-PER54','EBEN-PER55','EBEN-PER56','EBEN-PER57','EBEN-PER71'] as $permCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => 'EBEN-ROL8',
                'permission_code' => $permCode,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $perms = [
            'EBEN-PER53','EBEN-PER54','EBEN-PER55','EBEN-PER56','EBEN-PER57',
            'EBEN-PER58','EBEN-PER59','EBEN-PER60','EBEN-PER61','EBEN-PER62',
            'EBEN-PER63','EBEN-PER64','EBEN-PER65','EBEN-PER66','EBEN-PER67',
            'EBEN-PER68','EBEN-PER69','EBEN-PER70','EBEN-PER71','EBEN-PER72',
        ];

        DB::table('tb_role_permission')->whereIn('permission_code', $perms)->delete();
        DB::table('tb_permissions')->whereIn('code', $perms)->delete();
    }
};
