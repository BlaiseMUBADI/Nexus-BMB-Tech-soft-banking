<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Supprimer les mappings role-permission
        DB::table('tb_role_permission')
            ->where('permission_code', 'EBEN-PER65')
            ->delete();

        // Supprimer la permission
        DB::table('tb_permissions')
            ->where('code', 'EBEN-PER65')
            ->delete();
    }

    public function down(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            [
                'code' => 'EBEN-PER65',
                'nom' => 'Enregistrer remboursement',
                'description' => "Enregistrer un paiement d'échéance ou remboursement",
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
};
