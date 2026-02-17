<?php

namespace App\Http\Controllers\RH;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:191',
            'description' => 'nullable|string',
        ]);
        \App\Models\Service::create($validated);
        return redirect()->route('services.index')->with('success', 'Service ajouté avec succès.');
    }

    public function index()
    {
        $services = Service::orderByDesc('created_at')->get();
        return view('rh.services.liste', compact('services'));
    }
}
