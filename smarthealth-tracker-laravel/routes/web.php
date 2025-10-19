<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\UserChallengeController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin\ChallengeController as AdminChallengeController;
use App\Http\Controllers\Admin\UserChallengeController as AdminUserChallengeController;
use App\Http\Controllers\Admin\ActivityTypeController as AdminActivityTypeController;
use App\Http\Controllers\Admin\ActivityController as AdminActivityController;

// === PAGE D’ACCUEIL ===
Route::get('/', function () {
    return view('welcome');
});

// === DASHBOARD UTILISATEUR ===
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// === PARTIE UTILISATEUR ===
Route::middleware('auth')->group(function () {

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Challenges
    Route::resource('challenges', ChallengeController::class);

    // ✅ Met la route des stats AVANT la resource
    Route::get('/activities/stats', [ActivityController::class, 'stats'])->name('activities.stats');

    // ✅ Activités utilisateur (CRUD complet)
    Route::resource('activities', ActivityController::class);

    // Challenges de l'utilisateur
    Route::prefix('my')->name('user.challenges.')->group(function () {
        Route::get('/challenges', [UserChallengeController::class, 'index'])->name('index');
        Route::post('/challenges/{challenge}/join', [UserChallengeController::class, 'join'])->name('join');
        Route::get('/challenges/{userChallenge}', [UserChallengeController::class, 'show'])->name('show');
        Route::put('/challenges/{userChallenge}/progress', [UserChallengeController::class, 'updateProgress'])->name('update-progress');
        Route::post('/challenges/{userChallenge}/complete', [UserChallengeController::class, 'complete'])->name('complete');
    });
});

// === PARTIE ADMIN ===
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {

        Route::get('/', fn() => view('admin.dashboard'))->name('dashboard');

        Route::resource('challenges', AdminChallengeController::class);
        Route::resource('activity-types', AdminActivityTypeController::class);
        Route::resource('activities', AdminActivityController::class);

        Route::get('/user-challenges', [AdminUserChallengeController::class, 'index'])->name('user-challenges.index');
        Route::get('/user-challenges/{userChallenge}', [AdminUserChallengeController::class, 'show'])->name('user-challenges.show');
        Route::get('/user-challenges/{userChallenge}/edit', [AdminUserChallengeController::class, 'edit'])->name('user-challenges.edit');
        Route::put('/user-challenges/{userChallenge}', [AdminUserChallengeController::class, 'update'])->name('user-challenges.update');
        Route::delete('/user-challenges/{userChallenge}', [AdminUserChallengeController::class, 'destroy'])->name('user-challenges.destroy');
    });

require __DIR__ . '/auth.php';
