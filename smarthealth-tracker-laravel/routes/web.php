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
use App\Http\Controllers\NutritionController;
use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\Admin\FoodController as AdminFoodController;

// === PAGE D’ACCUEIL ===
Route::get('/', function () {
    return view('welcome');
});

// === DASHBOARD UTILISATEUR ===
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboardV', function () {
    return view('dashboardV'); // Crée une vue resources/views/dashboard.blade.php
})->name('dashboardV');

// === PARTIE UTILISATEUR ===
Route::middleware('auth')->group(function () {

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Challenges
    Route::resource('challenges', ChallengeController::class);
Route::get('/chatbot', function () {
    return view('chatbot'); // Crée une vue resources/views/chatbot.blade.php
})->name('chatbot');


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
      //  Route::get('/water-intake', [NutritionController::class, 'waterIntake'])->name('water-intake');
      //  Route::post('/water-intake', [NutritionController::class, 'logWaterIntake'])->name('water-intake.log');
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
        Route::get('/user-challenges/stats', [AdminUserChallengeController::class, 'stats'])->name('user-challenges.stats');
        Route::get('/user-challenges/{userChallenge}', [AdminUserChallengeController::class, 'show'])->name('user-challenges.show');
        Route::get('/user-challenges/{userChallenge}/edit', [AdminUserChallengeController::class, 'edit'])->name('user-challenges.edit');
        Route::put('/user-challenges/{userChallenge}', [AdminUserChallengeController::class, 'update'])->name('user-challenges.update');
        Route::delete('/user-challenges/{userChallenge}', [AdminUserChallengeController::class, 'destroy'])->name('user-challenges.destroy');

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
