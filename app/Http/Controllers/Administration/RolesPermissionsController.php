<?php
namespace App\Http\Controllers\Administration;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RH\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// Ajoutez le modèle Permission si besoin
// use App\Models\Permission;

class RolesPermissionsController extends Controller
{
    public function showPermission($code)
    {
        $permission = \App\Models\RH\Permission::where('code', $code)->first();
        if (!$permission) {
            Log::warning('[Admin] Permission introuvable', ['code' => $code, 'ip' => request()->ip()]);
            abort(404, 'Permission introuvable : ' . $code);
        }
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
        $permission = \App\Models\RH\Permission::create($request->only('nom', 'description'));
        if ($request->ajax()) {
            return response()->json(['success' => true, 'permission' => $permission]);
        }
        return redirect()->route('administration.roles_permissions')->with('success', 'Permission ajoutée avec succès.');
    }

    public function index()
    {
        $roles       = Role::withCount('permissions')->orderBy('created_at', 'desc')->get();
        $permissions = \App\Models\RH\Permission::orderBy('code')->get();
        $users       = \App\Models\User::with('agent')->orderBy('name')->get();
        $moduleMap   = self::moduleMap();
        $legacyPermissionMap = self::legacyCreditPermissionMap();
        $legacyPermissionCodes = array_keys($legacyPermissionMap);
        $permissionsGrouped = $permissions->groupBy(fn($p) => self::moduleNum($p->code));

        $stats = [
            'total_roles'       => $roles->count(),
            'total_permissions' => $permissions->count(),
            'total_liaisons'    => DB::table('tb_role_permission')->count(),
            'users_avec_role'   => DB::table('tb_role_user')->distinct('user_id')->count('user_id'),
        ];

        return view('administration.roles_permissions', compact(
            'roles',
            'permissions',
            'permissionsGrouped',
            'moduleMap',
            'users',
            'stats',
            'legacyPermissionMap',
            'legacyPermissionCodes'
        ));
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
        $roles = \App\Models\RH\Role::orderBy('created_at', 'desc')->get();
        return view('administration.partials.roles_table', compact('roles'))->render();
    }

    public function permissionsTable()
    {
        $permissions = \App\Models\RH\Permission::orderBy('created_at', 'desc')->get();
        return view('administration.partials.permissions_table', compact('permissions'))->render();
    }

    /** Carte des modules : numéro → label/icon/couleur */
    private static function moduleMap(): array
    {
        return [
            1  => ['label' => 'Administration',    'icon' => 'fa-cog',              'color' => 'danger'],
            2  => ['label' => 'RH',                'icon' => 'fa-users',            'color' => 'info'],
            3  => ['label' => 'Caisse / Guichet',  'icon' => 'fa-cash-register',    'color' => 'success'],
            4  => ['label' => 'Clients',           'icon' => 'fa-user-friends',     'color' => 'primary'],
            5  => ['label' => 'Comptes',           'icon' => 'fa-wallet',           'color' => 'warning'],
            6  => ['label' => 'Devises',           'icon' => 'fa-coins',            'color' => 'secondary'],
            7  => ['label' => 'Transactions',      'icon' => 'fa-exchange-alt',     'color' => 'info'],
            8  => ['label' => 'Épargne',           'icon' => 'fa-piggy-bank',       'color' => 'success'],
            9  => ['label' => 'Crédits',           'icon' => 'fa-hand-holding-usd', 'color' => 'warning'],
            10 => ['label' => 'Rapports',          'icon' => 'fa-chart-bar',        'color' => 'primary'],
            11 => ['label' => 'Comptabilité',      'icon' => 'fa-book',             'color' => 'info'],
            12 => ['label' => 'Trésorerie',        'icon' => 'fa-university',       'color' => 'success'],
            13 => ['label' => 'Audit & Sécurité',  'icon' => 'fa-shield-alt',       'color' => 'danger'],
            14 => ['label' => 'CRUD Global',       'icon' => 'fa-layer-group',      'color' => 'secondary'],
        ];
    }

    /** Retourne le numéro de module d'après le code EBEN-PERxx */
    private static function moduleNum(string $code): int
    {
        preg_match('/EBEN-PER(\d+)/', $code, $m);
        $n = (int)($m[1] ?? 0);

        if ($n >= 1 && $n <= 5) {
            return 1;
        }
        if ($n >= 6 && $n <= 9) {
            return 2;
        }
        if ($n >= 103 && $n <= 106) {
            return 2;
        }
        if (($n >= 10 && $n <= 14) || $n === 110) {
            return 3;
        }
        if ($n === 97) {
            return 7;
        }
        if (($n >= 15 && $n <= 17) || $n === 76) {
            return 4;
        }
        if ($n === 107) {
            return 4;
        }
        if ($n >= 18 && $n <= 19) {
            return 5;
        }
        if ($n === 108) {
            return 5;
        }
        if ($n >= 20 && $n <= 21) {
            return 6;
        }
        if ($n >= 22 && $n <= 26) {
            return 7;
        }
        if ($n >= 27 && $n <= 29) {
            return 8;
        }
        if (($n >= 30 && $n <= 35) || ($n >= 53 && $n <= 72)) {
            return 9;
        }
        if ($n >= 36 && $n <= 38) {
            return 10;
        }
        if (($n >= 39 && $n <= 41) || ($n >= 49 && $n <= 52)) {
            return 11;
        }
        if (($n >= 44 && $n <= 48) || ($n >= 77 && $n <= 79)) {
            return 12;
        }
        if ($n === 42 || $n === 43) {
            return 13;
        }
        if ($n >= 73 && $n <= 75) {
            return 14;
        }

        return 13;
    }

    /**
     * Permissions Crédit legacy conservées pour compatibilité historique.
     * Elles ne doivent plus être attribuées pour les nouveaux rôles.
     */
    private static function legacyCreditPermissionMap(): array
    {
        return [
            'EBEN-PER30' => 'EBEN-PER53', // Voir crédits -> Voir liste crédits
            'EBEN-PER31' => 'EBEN-PER56', // Soumettre -> Soumettre demande crédit
            'EBEN-PER32' => 'EBEN-PER58', // Instruire -> Saisir analyse crédit
            'EBEN-PER33' => 'EBEN-PER63', // Approuver -> Validation finale gérant
            'EBEN-PER34' => 'EBEN-PER65', // Gérer remboursements -> Enregistrer remboursement
            'EBEN-PER35' => 'EBEN-PER72', // Clôturer crédit -> Historique/audit de clôture
        ];
    }

    public function rolePermissionsList($role_code)
    {
        $role           = \App\Models\RH\Role::where('code', $role_code)->first();
        if (!$role) {
            Log::warning('[Admin] Rôle introuvable', ['role_code' => $role_code, 'ip' => request()->ip()]);
            abort(404, 'Rôle introuvable : ' . $role_code);
        }
        $allPermissions = \App\Models\RH\Permission::orderBy('code')->get();
        $attached       = DB::table('tb_role_permission')->where('role_code', $role_code)->pluck('permission_code')->toArray();
        $moduleMap      = self::moduleMap();
        $legacyPermissionMap = self::legacyCreditPermissionMap();
        $legacyPermissionCodes = array_keys($legacyPermissionMap);
        $grouped        = $allPermissions->groupBy(fn($p) => self::moduleNum($p->code));
        return view('administration.partials.role_permissions_list', compact(
            'role',
            'allPermissions',
            'attached',
            'grouped',
            'moduleMap',
            'legacyPermissionMap',
            'legacyPermissionCodes'
        ))->render();
    }

    public function attachPermission(Request $request)
    {
        try {
            $request->validate([
                'role_code'       => 'required|string|exists:tb_roles,code',
                'permission_code' => 'required|string|exists:tb_permissions,code',
            ]);

            if (array_key_exists($request->permission_code, self::legacyCreditPermissionMap())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission legacy non autorisée pour une nouvelle attribution. Utilisez la permission crédit canonique.',
                ], 422);
            }

            DB::table('tb_role_permission')->updateOrInsert(
                ['role_code' => $request->role_code, 'permission_code' => $request->permission_code],
                ['created_at' => now(), 'updated_at' => now()]
            );
            return response()->json(['success' => true, 'message' => 'Permission attribuée.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $first = array_key_first($e->errors());
            return response()->json(['success' => false, 'message' => implode(' ', $e->errors()[$first])], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('attachPermission: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

    public function detachPermission(Request $request)
    {
        try {
            $request->validate([
                'role_code'       => 'required|string|exists:tb_roles,code',
                'permission_code' => 'required|string|exists:tb_permissions,code',
            ]);
            DB::table('tb_role_permission')
                ->where('role_code', $request->role_code)
                ->where('permission_code', $request->permission_code)
                ->delete();
            return response()->json(['success' => true, 'message' => 'Permission retirée.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $first = array_key_first($e->errors());
            return response()->json(['success' => false, 'message' => implode(' ', $e->errors()[$first])], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('detachPermission: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

      // AJAX : liste des rôles et permissions d'un utilisateur
    public function userRolesPermissionsList($user_id)
    {
        $user = \App\Models\User::with('agent')->findOrFail($user_id);
        $roles = \App\Models\RH\Role::orderBy('nom')->get();
        $permissions = \App\Models\RH\Permission::orderBy('nom')->get();
        $legacyPermissionMap = self::legacyCreditPermissionMap();
        $legacyPermissionCodes = array_keys($legacyPermissionMap);
        // Rôles attribués à l'utilisateur
        $userRoles = DB::table('tb_role_user')->where('user_id', $user_id)->pluck('role_code');
        // Permissions héritées via les rôles
        $userPermissions = DB::table('tb_role_permission')
            ->whereIn('role_code', $userRoles)
            ->pluck('permission_code')
            ->unique()
            ->values();

        $userLegacyPermissions = $userPermissions
            ->filter(fn($code) => in_array($code, $legacyPermissionCodes, true))
            ->values();

        $userPermissions = $userPermissions
            ->reject(fn($code) => in_array($code, $legacyPermissionCodes, true))
            ->values();

        return view('administration.partials.user_roles_permissions_list', compact(
            'user',
            'roles',
            'permissions',
            'userRoles',
            'userPermissions',
            'userLegacyPermissions',
            'legacyPermissionMap'
        ))->render();
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
