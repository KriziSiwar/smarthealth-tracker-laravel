<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\UserChallenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserChallengeController extends Controller
{
    /**
     * Display a listing of the user's challenges.
     */
    public function index()
    {
        $userChallenges = UserChallenge::with('challenge')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('challenges.user.my-challenges', compact('userChallenges'));
    }

    /**
     * Join a challenge
     */
  public function join(Challenge $challenge)
{
    \Log::info('Join method called for challenge: ' . $challenge->id);
    
    // Vérifier si l'utilisateur a déjà rejoint
    $existing = UserChallenge::where('user_id', Auth::id())
        ->where('challenge_id', $challenge->id)
        ->first();

    if ($existing) {
        return redirect()->route('challenges.show', $challenge)
            ->with('error', 'Vous participez déjà à ce challenge!');
    }

    try {
        // Création simple sans le champ status problématique
        $userChallenge = UserChallenge::create([
            'user_id' => Auth::id(),
            'challenge_id' => $challenge->id,
            'progress' => 0,
            'completed' => false,
            'score' => 0,
            'start_date' => now(),
        ]);

        \Log::info('UserChallenge created with ID: ' . $userChallenge->id);

        // 🔥 CORRECTION : Passer l'ID de la userChallenge, pas l'objet
        return redirect()->route('user.challenges.show', $userChallenge->id)
            ->with('success', 'Vous avez rejoint le challenge!');

    } catch (\Exception $e) {
        \Log::error('Error joining challenge: ' . $e->getMessage());
        return redirect()->route('challenges.show', $challenge)
            ->with('error', 'Erreur: ' . $e->getMessage());
    }
}
    /**
     * Display the specified user challenge progress.
     */
    public function show(UserChallenge $userChallenge)
    {
        // Vérifier que l'utilisateur peut voir cette progression
        if ($userChallenge->user_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        $userChallenge->load('challenge');

        return view('challenges.user.progress', compact('userChallenge'));
    }

    /**
     * Update the user's progress in a challenge.
     */
    public function updateProgress(Request $request, UserChallenge $userChallenge)
    {
        // Vérifier les permissions
        if ($userChallenge->user_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        $request->validate([
            'progress' => 'required|integer|min:0|max:' . $userChallenge->challenge->target_value
        ]);

        $isCompleted = $request->progress >= $userChallenge->challenge->target_value;

        $userChallenge->update([
            'progress' => $request->progress,
            'completed' => $isCompleted,
            'status' => $isCompleted ? 'completed' : 'in_progress',
            'end_date' => $isCompleted ? now() : null
        ]);

        $message = $isCompleted 
            ? 'Félicitations! Vous avez complété le challenge!' 
            : 'Progression mise à jour!';

        return back()->with('success', $message);
    }

    /**
     * Mark a challenge as completed.
     */
    public function complete(UserChallenge $userChallenge)
    {
        // Vérifier les permissions
        if ($userChallenge->user_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        $userChallenge->update([
            'progress' => $userChallenge->challenge->target_value,
            'completed' => true,
            'status' => 'completed',
            'end_date' => now(),
            'score' => $userChallenge->challenge->reward_points
        ]);

        return back()->with('success', 'Challenge complété! Félicitations!');
    }

    /**
     * Leave a challenge.
     */
    public function leave(UserChallenge $userChallenge)
    {
        // Vérifier les permissions
        if ($userChallenge->user_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        $userChallenge->delete();

        return redirect()->route('user.challenges.index')
            ->with('success', 'Vous avez quitté le challenge.');
    }
}