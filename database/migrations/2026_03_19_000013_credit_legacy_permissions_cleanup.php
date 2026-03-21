<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migrate legacy credit permissions (PER30-PER35) to canonical permissions (PER53-PER72)
     * in role-permission assignments, then detach legacy assignments.
     */
    public function up(): void
    {
        $now = now();

        $mapping = [
            'EBEN-PER30' => 'EBEN-PER53',
            'EBEN-PER31' => 'EBEN-PER56',
            'EBEN-PER32' => 'EBEN-PER58',
            'EBEN-PER33' => 'EBEN-PER63',
            'EBEN-PER34' => 'EBEN-PER65',
            'EBEN-PER35' => 'EBEN-PER72',
        ];

        foreach ($mapping as $legacyCode => $canonicalCode) {
            $roleCodes = DB::table('tb_role_permission')
                ->where('permission_code', $legacyCode)
                ->pluck('role_code')
                ->unique()
                ->values();

            foreach ($roleCodes as $roleCode) {
                DB::table('tb_role_permission')->insertOrIgnore([
                    'role_code' => $roleCode,
                    'permission_code' => $canonicalCode,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        DB::table('tb_role_permission')
            ->whereIn('permission_code', array_keys($mapping))
            ->delete();

        // Mark legacy permissions as deprecated for admin UI readability.
        DB::table('tb_permissions')
            ->whereIn('code', array_keys($mapping))
            ->update([
                'description' => DB::raw("CONCAT('[LEGACY] ', IFNULL(description, 'Permission historique non utilisée'))"),
                'updated_at' => $now,
            ]);
    }

    /**
     * Restore legacy assignments from canonical permissions if needed.
     * Note: this is best-effort and may reintroduce overlap.
     */
    public function down(): void
    {
        $now = now();

        $mapping = [
            'EBEN-PER30' => 'EBEN-PER53',
            'EBEN-PER31' => 'EBEN-PER56',
            'EBEN-PER32' => 'EBEN-PER58',
            'EBEN-PER33' => 'EBEN-PER63',
            'EBEN-PER34' => 'EBEN-PER65',
            'EBEN-PER35' => 'EBEN-PER72',
        ];

        foreach ($mapping as $legacyCode => $canonicalCode) {
            $roleCodes = DB::table('tb_role_permission')
                ->where('permission_code', $canonicalCode)
                ->pluck('role_code')
                ->unique()
                ->values();

            foreach ($roleCodes as $roleCode) {
                DB::table('tb_role_permission')->insertOrIgnore([
                    'role_code' => $roleCode,
                    'permission_code' => $legacyCode,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
};
