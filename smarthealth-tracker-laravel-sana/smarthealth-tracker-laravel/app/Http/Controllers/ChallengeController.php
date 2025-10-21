<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChallengeController extends Controller
{
    public function index()
    {
        $challenges = Challenge::with('creator')->latest()->get();
        return view('challenges.index', compact('challenges'));
    }

    public function create()
    {
        // Plus besoin de passer $users à la vue
        return view('challenges.create');
    }

    public function store(Request $request)
    {
        logger('=== TENTATIVE CRÉATION CHALLENGE ===');
        logger('Données reçues:', $request->all());

        // Validation SANS created_by (sera assigné automatiquement)
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
            // 'created_by' est retiré de la validation
        ]);

        try {
            // Assigner automatiquement l'utilisateur connecté
            $validated['created_by'] = Auth::id();
            
            logger('Données validées avec created_by automatique:', $validated);

            $challenge = Challenge::create($validated);
            
            logger('Challenge créé avec succès:', ['id' => $challenge->id]);

            return redirect()->route('challenges.show', $challenge)
                            ->with('success', 'Challenge créé avec succès!');

        } catch (\Exception $e) {
            logger('Erreur lors de la création:', ['message' => $e->getMessage()]);
            return redirect()->back()
                           ->with('error', 'Erreur lors de la création du challenge: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function show(Challenge $challenge)
    {
            //$challenge = Challenge::with('users')->findOrFail($id);

        return view('challenges.show', compact('challenge'));
    }

    public function edit(Challenge $challenge)
    {
        // Vérifier que l'utilisateur peut modifier ce challenge
        if ($challenge->created_by !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        // Plus besoin de passer $users
        return view('challenges.edit', compact('challenge'));
    }

    public function update(Request $request, Challenge $challenge)
    {
        // Vérifier que l'utilisateur peut modifier ce challenge
        if ($challenge->created_by !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        // Validation SANS created_by (ne change pas)
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

        return redirect()->route('challenges.show', $challenge)
                        ->with('success', 'Challenge mis à jour avec succès!');
    }

    public function destroy(Challenge $challenge)
    {
        // Vérifier que l'utilisateur peut supprimer ce challenge
        if ($challenge->created_by !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        $challenge->delete();

        return redirect()->route('challenges.index')
                        ->with('success', 'Challenge supprimé avec succès!');
    }
}