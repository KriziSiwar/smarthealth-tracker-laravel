<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserChallenge;
use App\Models\User;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserChallengeController extends Controller
{
    /**
     * Constructor
     */
  

    /**
     * Check if user is admin
     */
    private function checkAdmin()
    {
        if (auth()->id() !== 1) {
            abort(403, 'Accès administrateur requis.');
        }
    }

    /**
     * Display a listing of all user challenges with filters
     */
    public function index(Request $request)
    {
        $this->checkAdmin();
        
        // Query de base avec les relations
        $query = UserChallenge::with(['user', 'challenge'])
            ->latest();

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par completion
        if ($request->filled('completed')) {
            $query->where('completed', $request->completed === 'true');
        }

        // Filtre par utilisateur
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtre par challenge
        if ($request->filled('challenge_id')) {
            $query->where('challenge_id', $request->challenge_id);
        }

        // Filtre par date de début
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        // Filtre par date de fin
        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        // Pagination avec 20 éléments par page
        $userChallenges = $query->paginate(20)
            ->appends($request->except('page'));

        // Données pour les filtres
        $users = User::select('id', 'name', 'email')->get();
        $challenges = Challenge::select('id', 'title')->get();

        // Statistiques rapides
        $stats = [
            'total' => UserChallenge::count(),
            'completed' => UserChallenge::where('completed', true)->count(),
            'in_progress' => UserChallenge::where('status', 'in_progress')->count(),
            'abandoned' => UserChallenge::where('status', 'abandoned')->count(),
        ];

        return view('admin.user-challenges.index', compact(
            'userChallenges', 
            'users', 
            'challenges', 
            'stats'
        ));
    }

    /**
     * Display the specified user challenge.
     */
    public function show(UserChallenge $userChallenge)
    {
        $this->checkAdmin();
        
        $userChallenge->load(['user', 'challenge']);

        return view('admin.user-challenges.show', compact('userChallenge'));
    }

    /**
     * Show the form for editing the specified user challenge.
     */
    public function edit(UserChallenge $userChallenge)
    {
        $this->checkAdmin();
        
        $userChallenge->load(['user', 'challenge']);
        return view('admin.user-challenges.edit', compact('userChallenge'));
    }

    /**
     * Update the specified user challenge in storage.
     */
    public function update(Request $request, UserChallenge $userChallenge)
    {
        $this->checkAdmin();
        
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:' . $userChallenge->challenge->target_value,
            'completed' => 'required|boolean',
            'status' => 'required|in:in_progress,completed,abandoned',
            'score' => 'nullable|integer|min:0',
        ]);

        // Si marqué comme complété, forcer la progression au maximum
        if ($validated['completed']) {
            $validated['progress'] = $userChallenge->challenge->target_value;
            $validated['status'] = 'completed';
        }

        $userChallenge->update($validated);

        return redirect()->route('admin.user-challenges.show', $userChallenge)
            ->with('success', 'Participation mise à jour avec succès!');
    }

    /**
     * Remove the specified user challenge from storage.
     */
    public function destroy(UserChallenge $userChallenge)
    {
        $this->checkAdmin();
        
        $userChallenge->delete();

        return redirect()->route('admin.user-challenges.index')
            ->with('success', 'Participation supprimée avec succès!');
    }

    /**
     * Force complete a user challenge
     */
    public function forceComplete(UserChallenge $userChallenge)
    {
        $this->checkAdmin();
        
        $userChallenge->update([
            'progress' => $userChallenge->challenge->target_value,
            'completed' => true,
            'status' => 'completed',
            'end_date' => now(),
        ]);

        return back()->with('success', 'Challenge marqué comme complété!');
    }

    /**
     * Reset user challenge progress
     */
    public function resetProgress(UserChallenge $userChallenge)
    {
        $this->checkAdmin();
        
        $userChallenge->update([
            'progress' => 0,
            'completed' => false,
            'status' => 'in_progress',
            'score' => 0,
        ]);

        return back()->with('success', 'Progression réinitialisée!');
    }

    /**
     * Show user challenges statistics
     */
   
    public function stats()
{
    $activeUsers = \App\Models\UserChallenge::distinct('user_id')->count();
    $completedChallenges = \App\Models\UserChallenge::where('status', 'completed')->count();
    $averageScore = \App\Models\UserChallenge::avg('score');

    // Moyenne des scores par utilisateur
    $userStats = \App\Models\UserChallenge::selectRaw('user_id, AVG(score) as avg_score')
        ->groupBy('user_id')
        ->with('user')
        ->get();

    $userNames = $userStats->pluck('user.name');
    $userScores = $userStats->pluck('avg_score');

    // Progression moyenne par challenge
    $challengeStats = \App\Models\UserChallenge::selectRaw('challenge_id, AVG(progress) as avg_progress')
        ->groupBy('challenge_id')
        ->with('challenge')
        ->get();

    $challengeNames = $challengeStats->pluck('challenge.title');
    $challengeProgress = $challengeStats->pluck('avg_progress');

    return view('admin.user-challenges.stats', compact(
        'activeUsers',
        'completedChallenges',
        'averageScore',
        'userNames',
        'userScores',
        'challengeNames',
        'challengeProgress'
    ));
}

}