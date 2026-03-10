<?php

namespace App\Http\Controllers\Profil;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Affiche le profil (redirige vers edit).
     */
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('profile.edit');
    }

    /**
     * Affiche le formulaire de profil complet.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $agent = $user->agent;

        // Affectations de l'agent avec poste + service
        $affectations = $agent
            ? \App\Models\RH\Affectation::where('agent_matricule', $agent->matricule)
                ->with(['poste.service'])
                ->orderByDesc('date_debut')
                ->get()
            : collect();

        // Poste et service courants (via affectation active)
        $activeAff = $affectations->firstWhere('Etat', 'Actif');
        $poste   = $activeAff?->poste   ?? null;
        $service = $poste?->service     ?? null;

        // Rôles et permissions
        $userRoles = DB::table('tb_role_user')
            ->where('user_id', $user->id)
            ->pluck('role_code');

        $userPermissions = DB::table('tb_role_permission')
            ->whereIn('role_code', $userRoles)
            ->pluck('permission_code');

        $roles       = \App\Models\RH\Role::orderBy('nom')->get();
        $permissions = \App\Models\RH\Permission::orderBy('nom')->get();

        return view('profile.edit', compact(
            'user', 'agent', 'affectations',
            'poste', 'service',
            'roles', 'permissions',
            'userRoles', 'userPermissions'
        ));
    }

    /**
     * Met à jour les informations du compte (nom + email) ou le mot de passe.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // ── Changement de mot de passe ─────────────────────────
        if ($request->filled('_change_password')) {
            $request->validate([
                'current_password'      => ['required', 'current_password'],
                'password'              => ['required', 'string', 'min:8', 'confirmed'],
            ], [
                'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
                'password.min'                      => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
                'password.confirmed'                => 'La confirmation ne correspond pas.',
            ]);

            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->route('profile.edit')->with('status', 'password-updated');
        }

        // ── Mise à jour nom + email ────────────────────────────
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255',
                        \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->fill($request->only('name', 'email'));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Supprime le compte utilisateur.
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

        return redirect('/');
    }
}
