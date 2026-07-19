<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            [
                'code' => 'EBEN-PER119',
                'nom' => 'Proposer un virement bancaire',
                'description' => 'Créer une demande de virement entre deux comptes clients (avec ou sans changement de devise), soumise à validation.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER120',
                'nom' => 'Valider / rejeter un virement bancaire',
                'description' => 'Approuver (exécution réelle du mouvement) ou rejeter une demande de virement bancaire proposée par le Comptable.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // PER119 (proposer) -> Comptable
        DB::table('tb_role_permission')->insertOrIgnore([
            'role_code' => 'EBEN-ROL7',
            'permission_code' => 'EBEN-PER119',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // PER120 (valider/rejeter) -> Gérant + Directeur
        foreach (['EBEN-ROL12', 'EBEN-ROL3'] as $roleCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => $roleCode,
                'permission_code' => 'EBEN-PER120',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tb_role_permission')->whereIn('permission_code', ['EBEN-PER119', 'EBEN-PER120'])->delete();
        DB::table('tb_permissions')->whereIn('code', ['EBEN-PER119', 'EBEN-PER120'])->delete();
    }
};
