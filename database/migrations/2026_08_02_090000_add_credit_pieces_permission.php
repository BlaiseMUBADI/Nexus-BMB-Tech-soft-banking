<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Nouvelle permission EBEN-PER73 : "Gérer pièces justificatives".
 *
 * Jusqu'ici l'onglet "Pièces & docs" d'un dossier crédit n'était que
 * de la LECTURE (toujours "Manquant", jamais modifiable) — aucun contrôleur,
 * route ou vue ne permettait de marquer une pièce comme fournie, d'ajouter
 * une référence ou un commentaire. Cette migration ajoute la permission qui
 * gère désormais cet accès (voir CreditController::updatePiece()).
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            [
                'code' => 'EBEN-PER73',
                'nom' => 'Gérer pièces justificatives',
                'description' => "Marquer les pièces d'un dossier crédit comme fournies et ajouter référence/commentaire",
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // ROL1 = Administrateur, ROL6 = Chargé de crédit, ROL8 = Agent commercial
        // (les mêmes rôles qui peuvent créer/modifier une demande — PER54/PER55)
        foreach (['EBEN-ROL1', 'EBEN-ROL6', 'EBEN-ROL8'] as $roleCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => $roleCode,
                'permission_code' => 'EBEN-PER73',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tb_role_permission')->where('permission_code', 'EBEN-PER73')->delete();
        DB::table('tb_permissions')->where('code', 'EBEN-PER73')->delete();
    }
};
