<?php

namespace App\Http\Controllers;

use App\Models\ActivityType;
use Illuminate\Http\Request;

class ActivityTypeController extends Controller
{
    public function index()
    {
        $types = ActivityType::latest()->paginate(10);
        return view('activity_types.index', compact('types'));
    }

    public function create()
    {
        return view('activity_types.create', ['activityType' => new ActivityType()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','max:255','unique:activity_types,name'],
            'calories_per_minute' => ['required','numeric','min:0'],
        ]);
        ActivityType::create($data);
        return redirect()->route('activity-types.index')->with('success','Type créé.');
    }

    public function show(ActivityType $activityType)
    {
        return view('activity_types.show', compact('activityType'));
    }

    public function edit(ActivityType $activityType)
    {
        return view('activity_types.edit', compact('activityType'));
    }

    public function update(Request $request, ActivityType $activityType)
    {
        $data = $request->validate([
            'name' => ['required','max:255','unique:activity_types,name,'.$activityType->id],
            'calories_per_minute' => ['required','numeric','min:0'],
        ]);
        $activityType->update($data);
        return redirect()->route('activity-types.index')->with('success','Type mis à jour.');
    }

    public function destroy(ActivityType $activityType)
    {
        $activityType->delete();
        return redirect()->route('activity-types.index')->with('success','Type supprimé.');
    }
}
