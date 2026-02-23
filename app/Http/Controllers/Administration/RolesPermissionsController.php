<?php
namespace App\Http\Controllers\Administration;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
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
        return view('administration.roles_permissions', compact('roles', 'permissions'));
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
}
