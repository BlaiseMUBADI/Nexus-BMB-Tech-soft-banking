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
        $roles = Role::orderBy('created_at', 'desc')->get();
        $permissions = \App\Models\Permission::orderBy('created_at', 'desc')->get();
        $users = \App\Models\User::with('agent')->orderBy('name')->get();
        return view('administration.roles_permissions', compact('roles', 'permissions', 'users'));
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

    public function rolePermissionsList($role_code)
    {
        $role = \App\Models\Role::where('code', $role_code)->firstOrFail();
        $allPermissions = \App\Models\Permission::orderBy('nom')->get();
        $attached = DB::table('tb_role_permission')->where('role_code', $role_code)->pluck('permission_code')->toArray();
        return view('administration.partials.role_permissions_list', compact('role', 'allPermissions', 'attached'))->render();
    }

    public function attachPermission(Request $request)
    {
        $request->validate([
            'role_code' => 'required|string|exists:tb_roles,code',
            'permission_code' => 'required|string|exists:tb_permissions,code',
        ]);
        DB::table('tb_role_permission')->updateOrInsert([
            'role_code' => $request->role_code,
            'permission_code' => $request->permission_code
        ], []);
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
