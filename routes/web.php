<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\UserChallengeController;
use App\Http\Controllers\Admin\ChallengeController as AdminChallengeController;
use App\Http\Controllers\Admin\UserChallengeController as AdminUserChallengeController;

// === PAGE D’ACCUEIL ===
Route::get('/', function () {
    return view('welcome');
});

// === DASHBOARD UTILISATEUR (après login) ===
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// === PARTIE CLIENT ===
Route::middleware('auth')->group(function () {

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestion des challenges
    Route::resource('challenges', ChallengeController::class);

    // Participation aux challenges (my/challenges/…)
    Route::prefix('my')->name('user.challenges.')->group(function () {
        Route::get('/challenges', [UserChallengeController::class, 'index'])->name('index');
        Route::post('/challenges/{challenge}/join', [UserChallengeController::class, 'join'])->name('join');
        Route::get('/challenges/{userChallenge}', [UserChallengeController::class, 'show'])->name('show');
        Route::put('/challenges/{userChallenge}/progress', [UserChallengeController::class, 'updateProgress'])->name('update-progress');
        Route::post('/challenges/{userChallenge}/complete', [UserChallengeController::class, 'complete'])->name('complete');
    });
});

// === PARTIE ADMIN ===
// Assure-toi d’avoir créé le middleware 'admin' pour vérifier le rôle
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {

        // Dashboard admin
        Route::get('/', function () {
            return view('admin.dashboard'); // Crée resources/views/admin/dashboard.blade.php
        })->name('dashboard');

        // Gestion des challenges
        Route::resource('challenges', AdminChallengeController::class);

        // Gestion des participations
        Route::get('/user-challenges', [AdminUserChallengeController::class, 'index'])->name('user-challenges.index');
        Route::get('/user-challenges/{userChallenge}', [AdminUserChallengeController::class, 'show'])->name('user-challenges.show');
        Route::delete('/user-challenges/{userChallenge}', [AdminUserChallengeController::class, 'destroy'])->name('user-challenges.destroy');
        Route::get('/user-challenges/{userChallenge}/edit', [AdminUserChallengeController::class, 'edit'])->name('user-challenges.edit');
Route::put('/user-challenges/{userChallenge}', [AdminUserChallengeController::class, 'update'])->name('user-challenges.update');

    });

require __DIR__ . '/auth.php';
