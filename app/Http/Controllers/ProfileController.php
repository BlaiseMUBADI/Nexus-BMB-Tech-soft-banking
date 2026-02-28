<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $agent = $user->agent;
        // Affectations for agent
        $affectations = $agent ? \App\Models\Affectation::where('agent_matricule', $agent->matricule)
            ->with(['poste.service'])
            ->orderByDesc('date_debut')
            ->get() : collect();
        // Poste and Service (current)
        $poste = $agent && $agent->poste ? $agent->poste : null;
        $service = $poste && $poste->service ? $poste->service : null;
        // Permissions/roles
        $userRoles = \DB::table('tb_role_user')->where('user_id', $user->id)->pluck('role_code');
        $userPermissions = \DB::table('tb_role_permission')
            ->whereIn('role_code', $userRoles)
            ->pluck('permission_code');
        $roles = \App\Models\Role::orderBy('nom')->get();
        $permissions = \App\Models\Permission::orderBy('nom')->get();
        return view('profile.edit', [
            'user' => $user,
            'agent' => $agent,
            'affectations' => $affectations,
            'poste' => $poste,
            'service' => $service,
            'roles' => $roles,
            'permissions' => $permissions,
            'userRoles' => $userRoles,
            'userPermissions' => $userPermissions,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Affiche le profil utilisateur.
     */
    public function index(Request $request): View
    {
        return view('profile.index');
    }
}
