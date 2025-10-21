<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // ✅ pour appeler l'API Flask

class ActivityController extends Controller
{
    /**
     * Affiche la liste des activités de l’utilisateur connecté
     * avec recherche, tri et pagination (5 par page)
     */
    public function index(Request $request)
    {
        $search    = $request->query('search');
        $sort      = $request->query('sort', 'activity_date');
        $direction = $request->query('direction', 'desc');

        $query = Activity::with('type')
            ->where('user_id', Auth::id());

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('intensity', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('duration', 'like', "%{$search}%")
                  ->orWhere('calories_burned', 'like', "%{$search}%")
                  ->orWhere('activity_date', 'like', "%{$search}%");
            });
        }

        $activities = $query->orderBy($sort, $direction)
            ->paginate(5)
            ->appends($request->query());

        return view('activities.index', compact('activities', 'search', 'sort', 'direction'));
    }

    /**
     * Affiche le formulaire d’ajout
     */
    public function create()
    {
        $types = ActivityType::all();
        return view('activities.create', compact('types'));
    }

    /**
     * Enregistre une nouvelle activité + prédiction IA des calories
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_type_id' => 'nullable|exists:activity_types,id',
            'duration'         => 'required|integer|min:1',
            'calories_burned'  => 'nullable|integer|min:0', // ← on le garde facultatif
            'intensity'        => 'required|string|max:50',
            'activity_date'    => 'required|date',
            'notes'            => 'nullable|string|max:255',
        ]);

        // 🧠 🔥 Appel API Flask pour prédire les calories
        try {
            $response = Http::timeout(5)->post('http://127.0.0.1:5000/predict', [
                'duration'  => $request->duration,
                'intensity' => $request->intensity,
            ]);

            if ($response->successful()) {
                $prediction = $response->json('prediction');
                $validated['calories_burned'] = round($prediction);
            } else {
                $prediction = null;
            }
        } catch (\Exception $e) {
            // si Flask ne répond pas
            $prediction = null;
        }

        $validated['user_id'] = Auth::id();
        Activity::create($validated);

        $message = $prediction
            ? "✅ Activité ajoutée avec succès. Estimation IA : environ {$prediction} kcal brûlées."
            : "✅ Activité ajoutée avec succès (aucune estimation IA disponible).";

        return redirect()
            ->route('activities.index')
            ->with('success', $message);
    }

    /**
     * Affiche le formulaire d’édition
     */
    public function edit(Activity $activity)
    {
        if ($activity->user_id !== Auth::id()) {
            abort(403, 'Non autorisé.');
        }

        $types = ActivityType::all();
        return view('activities.edit', compact('activity', 'types'));
    }

    /**
     * Met à jour une activité existante
     */
    public function update(Request $request, Activity $activity)
    {
        if ($activity->user_id !== Auth::id()) {
            abort(403, 'Non autorisé.');
        }

        $validated = $request->validate([
            'activity_type_id' => 'nullable|exists:activity_types,id',
            'duration'         => 'required|integer|min:1',
            'calories_burned'  => 'required|integer|min:0',
            'intensity'        => 'required|string|max:50',
            'activity_date'    => 'required|date',
            'notes'            => 'nullable|string|max:255',
        ]);

        $activity->update($validated);

        return redirect()
            ->route('activities.index')
            ->with('success', '✏️ Activité mise à jour avec succès.');
    }

    /**
     * Supprime une activité
     */
    public function destroy(Activity $activity)
    {
        if ($activity->user_id !== Auth::id()) {
            abort(403, 'Non autorisé.');
        }

        $activity->delete();

        return redirect()
            ->route('activities.index')
            ->with('success', '🗑️ Activité supprimée avec succès.');
    }
}
