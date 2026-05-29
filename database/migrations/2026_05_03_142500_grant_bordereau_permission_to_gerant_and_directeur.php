<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $targets = [
            ['role_code' => 'EBEN-ROL12', 'permission_code' => 'EBEN-PER11'], // GERANT
            ['role_code' => 'EBEN-ROL3',  'permission_code' => 'EBEN-PER11'], // Directeur
        ];

        foreach ($targets as $row) {
            DB::table('tb_role_permission')->updateOrInsert(
                [
                    'role_code' => $row['role_code'],
                    'permission_code' => $row['permission_code'],
                ],
                [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('tb_role_permission')
            ->where('permission_code', 'EBEN-PER11')
            ->whereIn('role_code', ['EBEN-ROL12', 'EBEN-ROL3'])
            ->delete();
    }
};
