<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityType;
use Illuminate\Http\Request;

class ActivityTypeController extends Controller
{
    /**
     * Liste des types d’activités (admin) avec recherche + tri + pagination.
     */
    public function index(Request $request)
    {
        $search    = $request->query('search');
        $sort      = $request->query('sort', 'name');
        $direction = $request->query('direction', 'asc');
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $sortable = ['name', 'description'];
        if (! in_array($sort, $sortable, true)) {
            $sort = 'name';
        }

        $query = ActivityType::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $query->orderBy($sort, $direction);

        $types = $query->paginate(5)->appends($request->query());

        return view('admin.activity_types.index', compact('types', 'search', 'sort', 'direction'));
    }

    public function create()
    {
        return view('admin.activity_types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ActivityType::create($data);

        return redirect()
            ->route('admin.activity-types.index')
            ->with('success', 'Type créé avec succès.');
    }

    public function edit(ActivityType $activityType)
    {
        return view('admin.activity_types.edit', compact('activityType'));
    }

    public function update(Request $request, ActivityType $activityType)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $activityType->update($data);

        return redirect()
            ->route('admin.activity-types.index')
            ->with('success', 'Type mis à jour avec succès.');
    }

    public function destroy(ActivityType $activityType)
    {
        $activityType->delete();

        return redirect()
            ->route('admin.activity-types.index')
            ->with('success', 'Type supprimé avec succès.');
    }
}
