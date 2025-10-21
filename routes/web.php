<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\UserChallengeController;
use App\Http\Controllers\Admin\ChallengeController as AdminChallengeController;
use App\Http\Controllers\Admin\UserChallengeController as AdminUserChallengeController;
use App\Http\Controllers\NutritionController;
use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\Admin\FoodController as AdminFoodController;

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
    Route::get('/challenges', [ChallengeController::class, 'index'])->name('challenges.index');
    Route::get('/challenges/create', [ChallengeController::class, 'create'])->name('challenges.create');
    Route::post('/challenges', [ChallengeController::class, 'store'])->name('challenges.store');
    Route::get('/challenges/{challenge}', [ChallengeController::class, 'show'])->name('challenges.show');
    Route::post('/challenges/{challenge}/join', [ChallengeController::class, 'join'])->name('challenges.join');
    Route::post('/challenges/{challenge}/leave', [ChallengeController::class, 'leave'])->name('challenges.leave');

    // Participation aux challenges (my/challenges/…)
    Route::prefix('my')->name('user.challenges.')->group(function () {
        Route::get('/challenges', [UserChallengeController::class, 'index'])->name('index');
        Route::post('/challenges/{challenge}/join', [UserChallengeController::class, 'join'])->name('join');
        Route::get('/challenges/{userChallenge}', [UserChallengeController::class, 'show'])->name('show');
        Route::put('/challenges/{userChallenge}/progress', [UserChallengeController::class, 'updateProgress'])->name('update-progress');
        Route::post('/challenges/{userChallenge}/complete', [UserChallengeController::class, 'complete'])->name('complete');
    });

    // Nutrition Module - User Routes
    Route::prefix('nutrition')->name('nutrition.')->group(function () {
        // Dashboard
        Route::get('/', [NutritionController::class, 'dashboard'])->name('dashboard');
        
        // Food Log
        Route::match(['get', 'post'], '/food-log', function() {
            if (request()->isMethod('get')) {
                Log::info('GET request to /nutrition/food-log', [
                    'url' => request()->fullUrl(),
                    'referer' => request()->header('referer'),
                    'user_agent' => request()->userAgent(),
                ]);
                return redirect()->back();
            }
            return app(NutritionController::class)->logFood();
        })->name('food.log');
        
        Route::delete('/food-log/{foodLog}', [NutritionController::class, 'deleteFoodLog'])->name('food.log.delete');
        
        // Meal Plans
        Route::get('/meal-plans', [MealPlanController::class, 'index'])->name('meal-plans.index');
        Route::get('/meal-plans/create', [MealPlanController::class, 'create'])->name('meal-plans.create');
        Route::post('/meal-plans', [MealPlanController::class, 'store'])->name('meal-plans.store');
        Route::get('/meal-plans/{mealPlan}', [MealPlanController::class, 'show'])->name('meal-plans.show');
        Route::get('/meal-plans/{mealPlan}/edit', [MealPlanController::class, 'edit'])->name('meal-plans.edit');
        Route::put('/meal-plans/{mealPlan}', [MealPlanController::class, 'update'])->name('meal-plans.update');
        Route::delete('/meal-plans/{mealPlan}', [MealPlanController::class, 'destroy'])->name('meal-plans.destroy');
        Route::post('/meal-plans/{mealPlan}/add-to-log', [MealPlanController::class, 'addToLog'])->name('meal-plans.add-to-log');
        
        // Water Intake
        Route::get('/water-intake', [NutritionController::class, 'waterIntake'])->name('water-intake');
        Route::post('/water-intake', [NutritionController::class, 'logWaterIntake'])->name('water-intake.log');
    });
});

// === PARTIE ADMIN ===
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'web', \App\Http\Middleware\AdminMiddleware::class])
    ->group(function () {

        // Dashboard admin
        Route::get('/dashboard', function () {
            return view('admin.dashboard'); // Crée resources/views/admin/dashboard.blade.php
        })->name('dashboard');

        // Gestion des challenges
        Route::resource('challenges', \App\Http\Controllers\Admin\ChallengeController::class);

        // Gestion des plans de repas
        Route::resource('nutrition/meal-plans', \App\Http\Controllers\Admin\MealPlanController::class)
            ->names('nutrition.meal-plans')
            ->except(['edit', 'update']);
            
        // Ajoutez ici d'autres routes d'administration si nécessaire
        Route::post('nutrition/meal-plans/{mealPlan}/add-food', [\App\Http\Controllers\Admin\MealPlanController::class, 'addFood'])
            ->name('nutrition.meal-plans.add-food');
            
        Route::delete('nutrition/meal-plans/{mealPlan}/remove-food/{food}', [\App\Http\Controllers\Admin\MealPlanController::class, 'removeFood'])
            ->name('nutrition.meal-plans.remove-food');
            
        Route::post('nutrition/meal-plans/{mealPlan}/duplicate', [\App\Http\Controllers\Admin\MealPlanController::class, 'duplicate'])
            ->name('nutrition.meal-plans.duplicate');

        // Gestion des participations
        Route::get('/user-challenges', [AdminUserChallengeController::class, 'index'])->name('user-challenges.index');
        Route::get('/user-challenges/{userChallenge}', [AdminUserChallengeController::class, 'show'])->name('user-challenges.show');
        Route::delete('/user-challenges/{userChallenge}', [AdminUserChallengeController::class, 'destroy'])->name('user-challenges.destroy');
        Route::get('/user-challenges/{userChallenge}/edit', [AdminUserChallengeController::class, 'edit'])->name('user-challenges.edit');
        Route::put('/user-challenges/{userChallenge}', [AdminUserChallengeController::class, 'update'])->name('user-challenges.update');

        // Gestion de la nutrition - Admin
        Route::prefix('nutrition')->name('nutrition.')->group(function () {
            // Gestion des aliments
            Route::prefix('foods')->name('foods.')->group(function () {
                Route::get('/', [AdminFoodController::class, 'index'])->name('index');
                Route::get('/create', [AdminFoodController::class, 'create'])->name('create');
                Route::post('/', [AdminFoodController::class, 'store'])->name('store');
                
                // Move export route before the {food} parameter
                Route::get('/export', [AdminFoodController::class, 'export'])->name('export');
                Route::get('/search', [AdminFoodController::class, 'search'])->name('search');
                Route::post('/bulk-actions', [AdminFoodController::class, 'bulkActions'])->name('bulk-actions');
                
                // Food-specific routes
                Route::get('/{food}', [AdminFoodController::class, 'show'])->name('show');
                Route::get('/{food}/edit', [AdminFoodController::class, 'edit'])->name('edit');
                Route::put('/{food}', [AdminFoodController::class, 'update'])->name('update');
                Route::delete('/{food}', [AdminFoodController::class, 'destroy'])->name('destroy');
                Route::post('/{food}/approve', [AdminFoodController::class, 'approve'])->name('approve');
                Route::post('/{food}/toggle-approval', [AdminFoodController::class, 'toggleApproval'])->name('toggle-approval');
            });

            // Gestion des plans de repas
            Route::prefix('meal-plans')->name('meal-plans.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\MealPlanController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\MealPlanController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\MealPlanController::class, 'store'])->name('store');
                Route::get('/{mealPlan}', [\App\Http\Controllers\Admin\MealPlanController::class, 'show'])->name('show');
                Route::get('/{mealPlan}/edit', [\App\Http\Controllers\Admin\MealPlanController::class, 'edit'])->name('edit');
                Route::put('/{mealPlan}', [\App\Http\Controllers\Admin\MealPlanController::class, 'update'])->name('update');
                Route::delete('/{mealPlan}', [\App\Http\Controllers\Admin\MealPlanController::class, 'destroy'])->name('destroy');
            });

            // Suivi nutritionnel
            Route::prefix('tracking')->name('tracking.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\NutritionTrackingController::class, 'index'])->name('index');
                Route::get('/user/{user}', [\App\Http\Controllers\Admin\NutritionTrackingController::class, 'userTracking'])->name('user');
                Route::get('/reports', [\App\Http\Controllers\Admin\NutritionTrackingController::class, 'reports'])->name('reports');
            });
        });
    });

require __DIR__ . '/auth.php';
