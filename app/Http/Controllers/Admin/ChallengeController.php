<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    /**
     * Afficher la liste des challenges (admin)
     */
    public function index()
    {
        // Récupère tous les challenges avec leur créateur
        $challenges = Challenge::with('creator')->latest()->get();

        return view('admin.challenges.index', compact('challenges'));
    }

    /**
     * Afficher le formulaire de création d’un challenge
     */
    public function create()
    {
        return view('admin.challenges.create');
    }

    /**
     * Enregistrer un nouveau challenge
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'target_value' => 'required|numeric|min:1',
            'unit' => 'required|string|max:50',
            'reward_points' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
            'category' => 'required|string|max:100',
            'status' => 'required|in:active,inactive,draft',
        ]);

        $challenge = Challenge::create($validated);

        return redirect()->route('admin.challenges.index')
                         ->with('success', 'Challenge créé avec succès.');
    }

    /**
     * Afficher un challenge spécifique
     */
    public function show(Challenge $challenge)
    {
        return view('admin.challenges.show', compact('challenge'));
    }

    /**
     * Afficher le formulaire d’édition d’un challenge
     */
    public function edit(Challenge $challenge)
    {
        return view('admin.challenges.edit', compact('challenge'));
    }

    /**
     * Mettre à jour un challenge existant
     */
    public function update(Request $request, Challenge $challenge)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'target_value' => 'required|numeric|min:1',
            'unit' => 'required|string|max:50',
            'reward_points' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
            'category' => 'required|string|max:100',
            'status' => 'required|in:active,inactive,draft',
        ]);

        $challenge->update($validated);

        return redirect()->route('admin.challenges.index')
                         ->with('success', 'Challenge mis à jour avec succès.');
    }

    /**
     * Supprimer un challenge
     */
    public function destroy(Challenge $challenge)
    {
        $challenge->delete();

        return redirect()->route('admin.challenges.index')
                         ->with('success', 'Challenge supprimé avec succès.');
    }
}
