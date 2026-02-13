<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'postnom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'sexe' => 'required|in:M,F',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'etat_civil' => 'required|string|max:255',
            'nom_conjoint' => 'nullable|string|max:255',
            'zone' => 'required|string|max:255',
            'type_piece_identite' => 'required|string|max:255',
            'lieu_delivrance_piece' => 'required|string|max:255',
            'date_delivrance_piece' => 'required|date',
            'numero_piece_identite' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Partie 6 : Activité économique
            'secteur_activite' => 'nullable|string|max:255',
            'type_activite' => 'nullable|string|max:255',
            'nom_entreprise' => 'nullable|string|max:255',
            'adresse_entreprise' => 'nullable|string|max:255',
            'telephone_entreprise' => 'nullable|string|max:255',
            'statut_entreprise' => 'nullable|string|max:255',
            'nombre_annees_experience' => 'nullable|integer|min:0',
            'revenu_mensuel' => 'nullable|numeric|min:0',
            'autres_details_activite' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('clients', 'public');
            $validated['photo'] = $photoPath;
        }

        \App\Models\Client::create($validated);
        return redirect()->route('clients.create')->with('success', 'Client ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
