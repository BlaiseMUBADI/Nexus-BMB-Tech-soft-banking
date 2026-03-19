<?php
namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UtilisateurController extends Controller
{
    // Affiche la liste des utilisateurs
    public function liste(Request $request)
    {
        $users = \App\Models\User::with([
            'agent',
            'agent.affectations' => function ($q) {
                $q->with('poste.service')
                    ->orderByRaw("CASE WHEN UPPER(Etat) = 'ACTIF' THEN 0 ELSE 1 END")
                    ->orderByDesc('date_debut');
            },
        ])->get();
        if ($request->ajax()) {
            return response()->json([
                'users' => $users
            ]);
        }
        return view('administration.utilisateurs.liste', compact('users'));
    }

    // Affiche le formulaire de création d'un utilisateur
    public function nouveau()
    {
        $agents = \App\Models\RH\Agent::orderByDesc('created_at')->get();
        return view('administration.utilisateurs.create', compact('agents'));
    }

    // Handle user creation
    public function store(Request $request)
    {
        $validated = $request->validate([
            'login' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'etat' => 'required|in:actif,inactif',
            'agent_matricule' => 'required|exists:tb_agents,matricule',
        ]);

        $user = new \App\Models\User();
        $user->name = $validated['login'];
        $user->email = $validated['email'] ?? null;
        $user->password = bcrypt($validated['password']);
        $user->etat = $validated['etat'];
        $user->agent_matricule = $validated['agent_matricule'];
        $user->save();

        return response()->json(['success' => true, 'message' => 'Utilisateur créé avec succès.']);
    }

    // Retourne les infos d'un agent en JSON pour AJAX
    public function agentInfo($matricule)
    {
        $agent = \App\Models\RH\Agent::with(['poste.service'])->where('matricule', $matricule)->first();
        if (!$agent) {
            return response()->json(['error' => 'Agent non trouvé'], 404);
        }
        return response()->json($agent);
    }

    // Supprimer un utilisateur
    public function destroy($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();
        return response()->json(['success' => true, 'message' => 'Utilisateur supprimé avec succès.']);
    }

    // Modifier un utilisateur (AJAX)
    public function update(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);

        $validated = $request->validate([
            'login' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $id,
            'etat' => 'required|in:actif,inactif',
            'password' => 'nullable|confirmed|min:6', // Mot de passe optionnel
        ]);

        $becomesInactive = strtolower((string) $validated['etat']) === 'inactif';

        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $validated, $becomesInactive) {
            $user->name = $validated['login'];
            $user->email = $validated['email'] ?? null;
            $user->etat = $validated['etat'];

            // Mettre à jour le mot de passe si fourni
            if (! empty($validated['password'])) {
                $user->password = bcrypt($validated['password']);
            }

            $user->save();

            // Si le compte passe inactif, désactiver aussi les affectations actives.
            if ($becomesInactive && $user->agent_matricule) {
                \App\Models\RH\Affectation::where('agent_matricule', $user->agent_matricule)
                    ->whereRaw('UPPER(Etat) = ?', ['ACTIF'])
                    ->update([
                        'Etat' => 'INACTIF',
                        'date_fin' => now(),
                        'updated_at' => now(),
                    ]);
            }

            // Déconnecter immédiatement toutes les sessions actives de cet utilisateur.
            if ($becomesInactive) {
                \Illuminate\Support\Facades\DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->delete();
            }
        });
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Utilisateur modifié avec succès.']);
        }
        
        return redirect()->route('administration.utilisateurs.liste')
                        ->with('success', 'Utilisateur modifié avec succès.');
    }

        // Affiche un utilisateur
    public function show($id)
    {
        $user = \App\Models\User::with('agent')->findOrFail($id);
        return view('administration.utilisateurs.show', compact('user'));
    }

    // Formulaire d'édition d'un utilisateur
    public function edit($id)
    {
        $user = \App\Models\User::with('agent')->findOrFail($id);
        $agents = \App\Models\RH\Agent::orderByDesc('created_at')->get();
        return view('administration.utilisateurs.edit', compact('user', 'agents'));
    }
}
