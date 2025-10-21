<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NutritionApiController;
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\FoodCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Nutrition API Routes
Route::prefix('nutrition')->middleware('auth:sanctum')->group(function () {
    // Dashboard
    Route::get('/dashboard', [NutritionApiController::class, 'dashboard']);
    
    // Food Logging
    Route::post('/log-food', [NutritionApiController::class, 'logFood']);
    Route::post('/log-water', [NutritionApiController::class, 'logWater']);
    
    // Food Search
    Route::get('/food/search', [NutritionApiController::class, 'searchFood']);
    Route::get('/food/categories', [NutritionApiController::class, 'getCategories']);
    
    // Nutrition Goals
    Route::get('/goals', [NutritionApiController::class, 'getNutritionGoals']);
    Route::put('/goals', [NutritionApiController::class, 'updateNutritionGoals']);
    
    // Meal Plans
    Route::get('/meal-plans/{date}', [NutritionApiController::class, 'getMealPlansForDate']);
    Route::post('/meal-plans', [NutritionApiController::class, 'createMealPlan']);
    Route::post('/meal-plans/{mealPlan}/log', [NutritionApiController::class, 'logMealPlan']);
    
    // Reports & Analytics
    Route::get('/summary', [NutritionApiController::class, 'getNutritionSummary']);
});

// Food API Routes
Route::prefix('food')->middleware('auth:sanctum')->group(function () {
    // Standard resource routes
    Route::get('/', [FoodController::class, 'index']);
    Route::post('/', [FoodController::class, 'store']);
    Route::get('/{id}', [FoodController::class, 'show']);
    Route::put('/{id}', [FoodController::class, 'update']);
    Route::delete('/{id}', [FoodController::class, 'destroy']);
    
    // Additional food-related routes
    Route::get('/search', [FoodController::class, 'search']);
    Route::get('/popular', [FoodController::class, 'popular']);
    Route::get('/recent', [FoodController::class, 'recent']);
});

// Food Category API Routes
Route::prefix('food-categories')->middleware('auth:sanctum')->group(function () {
    // Standard resource routes
    Route::get('/', [FoodCategoryController::class, 'index']);
    Route::post('/', [FoodCategoryController::class, 'store']);
    Route::get('/{id}', [FoodCategoryController::class, 'show']);
    Route::put('/{id}', [FoodCategoryController::class, 'update']);
    Route::delete('/{id}', [FoodCategoryController::class, 'destroy']);
    
    // Additional category-related routes
    Route::get('/active/with-count', [FoodCategoryController::class, 'activeWithCount']);
    Route::get('/popular/{limit?}', [FoodCategoryController::class, 'popular']);
    Route::post('/{id}/toggle-status', [FoodCategoryController::class, 'toggleStatus']);
});