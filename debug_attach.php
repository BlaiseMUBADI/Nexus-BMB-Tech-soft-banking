<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== USERS & ROLES ===\n";
$users = DB::table('users')->get();
foreach ($users as $u) {
    $roles = DB::table('tb_role_user')->where('user_id', $u->id)->pluck('role_code')->toArray();
    echo "  User #{$u->id} {$u->email} => [" . implode(', ', $roles) . "]\n";
}

echo "\n=== TEST updateOrInsert ===\n";
try {
    DB::table('tb_role_permission')->updateOrInsert(
        ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER1'],
        ['created_at' => now(), 'updated_at' => now()]
    );
    echo "  updateOrInsert ROL2/PER1 : OK\n";
    // Nettoyage test
    DB::table('tb_role_permission')
        ->where('role_code', 'EBEN-ROL2')
        ->where('permission_code', 'EBEN-PER1')
        ->delete();
    echo "  Cleanup : OK\n";
} catch (\Exception $e) {
    echo "  ERREUR: " . $e->getMessage() . "\n";
}

echo "\n=== PERMISSIONS COUNT ===\n";
echo "  tb_permissions: " . DB::table('tb_permissions')->count() . "\n";
echo "  tb_role_permission: " . DB::table('tb_role_permission')->count() . "\n";

echo "\n=== VALIDATION ROUTE (simulation) ===\n";
$validator = \Illuminate\Support\Facades\Validator::make(
    ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER1'],
    ['role_code' => 'required|string|exists:tb_roles,code', 'permission_code' => 'required|string|exists:tb_permissions,code']
);
echo "  Validation: " . ($validator->fails() ? 'FAIL: ' . json_encode($validator->errors()->toArray()) : 'OK') . "\n";

echo "\nDone.\n";
