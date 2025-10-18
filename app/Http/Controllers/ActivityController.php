<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with(['user','activityType'])->latest()->paginate(10);
        return view('activities.index', compact('activities'));
    }

    public function create()
    {
        $types = ActivityType::orderBy('name')->pluck('name','id');
        $users = User::orderBy('email')->pluck('email','id'); // peut être vide, user_id est nullable
        return view('activities.create', [
            'types' => $types,
            'users' => $users,
            'activity' => new Activity(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'          => ['nullable','exists:users,id'],
            'activity_type_id' => ['required','exists:activity_types,id'],
            'duration_minutes' => ['required','integer','min:1'],
            'activity_date'    => ['required','date'],
        ]);
        Activity::create($data);
        return redirect()->route('activities.index')->with('success','Activité créée.');
    }

    public function show(Activity $activity)
    {
        $activity->load(['user','activityType']);
        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        $types = ActivityType::orderBy('name')->pluck('name','id');
        $users = User::orderBy('email')->pluck('email','id');
        return view('activities.edit', compact('activity','types','users'));
    }

    public function update(Request $request, Activity $activity)
    {
        $data = $request->validate([
            'user_id'          => ['nullable','exists:users,id'],
            'activity_type_id' => ['required','exists:activity_types,id'],
            'duration_minutes' => ['required','integer','min:1'],
            'activity_date'    => ['required','date'],
        ]);
        $activity->update($data);
        return redirect()->route('activities.index')->with('success','Activité mise à jour.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('activities.index')->with('success','Activité supprimée.');
    }
}
