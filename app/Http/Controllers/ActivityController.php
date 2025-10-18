<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ActivityController extends Controller
{
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

    public function create()
    {
        $types = ActivityType::all();
        return view('activities.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_type_id' => 'nullable|exists:activity_types,id',
            'duration'         => 'required|integer|min:1',
            'intensity'        => 'required|string|max:50',
            'activity_date'    => 'required|date',
            'notes'            => 'nullable|string|max:255',
        ]);

        try {
            $response = Http::timeout(5)->post('http://127.0.0.1:5000/predict', [
                'duration'  => $request->duration,
                'intensity' => $request->intensity,
            ]);

            if ($response->successful()) {
                $prediction = $response->json('prediction');
                $validated['calories_burned'] = round($prediction);
            } else {
                $validated['calories_burned'] = 0;
            }
        } catch (\Exception $e) {
            $validated['calories_burned'] = 0;
        }

        $validated['user_id'] = Auth::id();
        Activity::create($validated);

        return redirect()
            ->route('activities.index')
            ->with('success', '✅ Activité ajoutée avec succès. Calories estimées : ' . $validated['calories_burned'] . ' kcal.');
    }

    public function edit(Activity $activity)
    {
        if ($activity->user_id !== Auth::id()) {
            abort(403, 'Non autorisé.');
        }

        $types = ActivityType::all();
        return view('activities.edit', compact('activity', 'types'));
    }

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
