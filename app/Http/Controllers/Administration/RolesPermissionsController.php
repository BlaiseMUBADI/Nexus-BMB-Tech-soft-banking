<?php
namespace App\Http\Controllers\Administration;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
// Ajoutez le modèle Permission si besoin
// use App\Models\Permission;

class RolesPermissionsController extends Controller
{
    public function showPermission($code)
    {
        $permission = \App\Models\Permission::where('code', $code)->firstOrFail();
        return view('administration.permission_show', compact('permission'));
    }

    public function storePermission(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|unique:tb_permissions,nom',
                'description' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            throw $e;
        }
        $permission = \App\Models\Permission::create($request->only('nom', 'description'));
        if ($request->ajax()) {
            return response()->json(['success' => true, 'permission' => $permission]);
        }
        return redirect()->route('administration.roles_permissions')->with('success', 'Permission ajoutée avec succès.');
    }

    public function index()
    {
        $roles       = Role::withCount('permissions')->orderBy('created_at', 'desc')->get();
        $permissions = \App\Models\Permission::orderBy('code')->get();
        $users       = \App\Models\User::with('agent')->orderBy('name')->get();
        $moduleMap   = self::moduleMap();
        $permissionsGrouped = $permissions->groupBy(fn($p) => self::moduleNum($p->code));

        $stats = [
            'total_roles'       => $roles->count(),
            'total_permissions' => $permissions->count(),
            'total_liaisons'    => DB::table('tb_role_permission')->count(),
            'users_avec_role'   => DB::table('tb_role_user')->distinct('user_id')->count('user_id'),
        ];

        return view('administration.roles_permissions', compact('roles', 'permissions', 'permissionsGrouped', 'moduleMap', 'users', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|unique:tb_roles,nom',
            'description' => 'nullable|string',
        ]);
        $role = Role::create($request->only('nom', 'description'));
        if ($request->ajax()) {
            return response()->json(['success' => true, 'role' => $role]);
        }
        return redirect()->route('administration.roles_permissions')->with('success', 'Rôle ajouté avec succès.');
    }

    public function rolesTable()
    {
        $roles = \App\Models\Role::orderBy('created_at', 'desc')->get();
        return view('administration.partials.roles_table', compact('roles'))->render();
    }

    public function permissionsTable()
    {
        $permissions = \App\Models\Permission::orderBy('created_at', 'desc')->get();
        return view('administration.partials.permissions_table', compact('permissions'))->render();
    }

    /** Carte des modules : numéro → label/icon/couleur */
    private static function moduleMap(): array
    {
        return [
            1  => ['label' => 'Administration',    'icon' => 'fa-cog',              'color' => 'danger'],
            2  => ['label' => 'RH',                'icon' => 'fa-users',            'color' => 'info'],
            3  => ['label' => 'Caisse',            'icon' => 'fa-cash-register',    'color' => 'success'],
            4  => ['label' => 'Clients',           'icon' => 'fa-user-friends',     'color' => 'primary'],
            5  => ['label' => 'Comptes',           'icon' => 'fa-wallet',           'color' => 'warning'],
            6  => ['label' => 'Devises',           'icon' => 'fa-coins',            'color' => 'secondary'],
            7  => ['label' => 'Transactions',      'icon' => 'fa-exchange-alt',     'color' => 'info'],
            8  => ['label' => 'Épargne',           'icon' => 'fa-piggy-bank',       'color' => 'success'],
            9  => ['label' => 'Crédits',           'icon' => 'fa-hand-holding-usd', 'color' => 'warning'],
            10 => ['label' => 'Rapports',          'icon' => 'fa-chart-bar',        'color' => 'primary'],
            11 => ['label' => 'Comptabilité',      'icon' => 'fa-book',             'color' => 'info'],
            12 => ['label' => 'Audit & Sécurité',  'icon' => 'fa-shield-alt',       'color' => 'danger'],
        ];
    }

    /** Retourne le numéro de module d'après le code EBEN-PERxx */
    private static function moduleNum(string $code): int
    {
        preg_match('/EBEN-PER(\d+)/', $code, $m);
        $n = (int)($m[1] ?? 0);
        if ($n <= 5)  return 1;
        if ($n <= 9)  return 2;
        if ($n <= 14) return 3;
        if ($n <= 17) return 4;
        if ($n <= 19) return 5;
        if ($n <= 21) return 6;
        if ($n <= 26) return 7;
        if ($n <= 29) return 8;
        if ($n <= 35) return 9;
        if ($n <= 38) return 10;
        if ($n <= 41) return 11;
        return 12;
    }

    public function rolePermissionsList($role_code)
    {
        $role           = \App\Models\Role::where('code', $role_code)->firstOrFail();
        $allPermissions = \App\Models\Permission::orderBy('code')->get();
        $attached       = DB::table('tb_role_permission')->where('role_code', $role_code)->pluck('permission_code')->toArray();
        $moduleMap      = self::moduleMap();
        $grouped        = $allPermissions->groupBy(fn($p) => self::moduleNum($p->code));
        return view('administration.partials.role_permissions_list', compact('role', 'allPermissions', 'attached', 'grouped', 'moduleMap'))->render();
    }

    public function attachPermission(Request $request)
    {
        $request->validate([
            'role_code' => 'required|string|exists:tb_roles,code',
            'permission_code' => 'required|string|exists:tb_permissions,code',
        ]);
        DB::table('tb_role_permission')->updateOrInsert(
            ['role_code' => $request->role_code, 'permission_code' => $request->permission_code],
            ['created_at' => now(), 'updated_at' => now()]
        );
        return response()->json(['success' => true]);
    }

    public function detachPermission(Request $request)
    {
        $request->validate([
            'role_code' => 'required|string|exists:tb_roles,code',
            'permission_code' => 'required|string|exists:tb_permissions,code',
        ]);
        DB::table('tb_role_permission')
            ->where('role_code', $request->role_code)
            ->where('permission_code', $request->permission_code)
            ->delete();
        return response()->json(['success' => true]);
    }

      // AJAX : liste des rôles et permissions d'un utilisateur
    public function userRolesPermissionsList($user_id)
    {
        $user = \App\Models\User::with('agent')->findOrFail($user_id);
        $roles = \App\Models\Role::orderBy('nom')->get();
        $permissions = \App\Models\Permission::orderBy('nom')->get();
        // Rôles attribués à l'utilisateur
        $userRoles = DB::table('tb_role_user')->where('user_id', $user_id)->pluck('role_code');
        // Permissions héritées via les rôles
        $userPermissions = DB::table('tb_role_permission')
            ->whereIn('role_code', $userRoles)
            ->pluck('permission_code');
        return view('administration.partials.user_roles_permissions_list', compact('user', 'roles', 'permissions', 'userRoles', 'userPermissions'))->render();
    }

    // AJAX : attacher un rôle à un utilisateur
    public function attachUserRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role_code' => 'required|string|exists:tb_roles,code',
        ]);
        DB::table('tb_role_user')->updateOrInsert([
            'user_id' => $request->user_id,
            'role_code' => $request->role_code
        ], []);
        return response()->json(['success' => true]);
    }

    // AJAX : détacher un rôle d'un utilisateur
    public function detachUserRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role_code' => 'required|string|exists:tb_roles,code',
        ]);
        DB::table('tb_role_user')
            ->where('user_id', $request->user_id)
            ->where('role_code', $request->role_code)
            ->delete();
        return response()->json(['success' => true]);
    }
}
