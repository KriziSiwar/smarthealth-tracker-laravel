<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\MealPlan;
use App\Models\NutritionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MealPlanController extends Controller
{
    /**
     * Display a listing of the user's meal plans.
     */
    public function index()
    {
        $mealPlans = Auth::user()->mealPlans()
            ->with(['foods' => function($query) {
                $query->select('foods.*', 
                    'meal_plan_food.serving_size as pivot_serving_size',
                    'meal_plan_food.serving_unit as pivot_serving_unit',
                    'meal_plan_food.meal_type as pivot_meal_type'
                );
            }])
            ->latest()
            ->paginate(10);

        return view('nutrition.meal-plans.index', compact('mealPlans'));
    }

    /**
     * Show the form for creating a new meal plan.
     */
    public function create()
    {
        $foods = Food::where('is_approved', true)
            ->orderBy('name')
            ->get(['id', 'name', 'serving_size', 'serving_unit', 'calories']);
            
        return view('nutrition.meal-plans.create', compact('foods'));
    }

    /**
     * Store a newly created meal plan in storage.
     */
    public function store(Request $request)
    {
        // Decode JSON string if necessary for 'foods' field
        $foodsData = $request->input('foods');
        if (is_string($foodsData)) {
            $foodsData = json_decode($foodsData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['foods' => 'Invalid foods data format.'])->withInput();
            }
            $request->merge(['foods' => $foodsData]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'foods' => 'required|array|min:1',
            'foods.*.id' => 'required|exists:foods,id',
            'foods.*.serving_size' => 'required|numeric|min:0.1',
            'foods.*.meal_type' => 'required|in:breakfast,lunch,dinner,snack',
        ]);

        $mealPlan = Auth::user()->mealPlans()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_public' => $validated['is_public'] ?? false,
        ]);

        // Attach foods with pivot data
        $foodsToAttach = [];
        foreach ($validated['foods'] as $foodData) {
            $food = Food::find($foodData['id']);
            $foodsToAttach[$food->id] = [
                'serving_size' => $foodData['serving_size'],
                'serving_unit' => $food->serving_unit,
                'meal_type' => $foodData['meal_type'],
            ];
        }

        $mealPlan->foods()->attach($foodsToAttach);
        $mealPlan->calculateNutrition();
        $mealPlan->save();

        return redirect()->route('nutrition.meal-plans.show', $mealPlan)
                        ->with('success', 'Meal plan created successfully!');
    }

    /**
     * Display the specified meal plan.
     */
    public function show(MealPlan $mealPlan)
    {
        $this->authorize('view', $mealPlan);
        
        $mealPlan->load(['foods' => function($query) {
            $query->select('foods.*', 
                'meal_plan_food.serving_size as pivot_serving_size',
                'meal_plan_food.serving_unit as pivot_serving_unit',
                'meal_plan_food.meal_type as pivot_meal_type'
            );
        }]);

        // Group foods by meal type for display
        $meals = [
            'breakfast' => $mealPlan->foods->where('pivot_meal_type', 'breakfast'),
            'lunch' => $mealPlan->foods->where('pivot_meal_type', 'lunch'),
            'dinner' => $mealPlan->foods->where('pivot_meal_type', 'dinner'),
            'snack' => $mealPlan->foods->where('pivot_meal_type', 'snack'),
        ];

        return view('nutrition.meal-plans.show', [
            'mealPlan' => $mealPlan,
            'meals' => $meals,
        ]);
    }

    /**
     * Show the form for editing the specified meal plan.
     */
    public function edit(MealPlan $mealPlan)
    {
        $this->authorize('update', $mealPlan);
        
        $mealPlan->load(['foods' => function($query) {
            $query->select('foods.*', 
                'meal_plan_food.serving_size as pivot_serving_size',
                'meal_plan_food.meal_type as pivot_meal_type'
            );
        }]);
        
        $foods = Food::where('is_approved', true)
            ->orderBy('name')
            ->get(['id', 'name', 'serving_size', 'serving_unit', 'calories']);
            
        return view('nutrition.meal-plans.edit', compact('mealPlan', 'foods'));
    }

    /**
     * Update the specified meal plan in storage.
     */
    public function update(Request $request, MealPlan $mealPlan)
    {
        $this->authorize('update', $mealPlan);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'foods' => 'required|array|min:1',
            'foods.*.id' => 'required|exists:foods,id',
            'foods.*.serving_size' => 'required|numeric|min:0.1',
            'foods.*.meal_type' => 'required|in:breakfast,lunch,dinner,snack',
        ]);

        $mealPlan->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_public' => $validated['is_public'] ?? false,
        ]);

        // Sync foods with pivot data
        $foodsToSync = [];
        foreach ($validated['foods'] as $foodData) {
            $food = Food::find($foodData['id']);
            $foodsToSync[$food->id] = [
                'serving_size' => $foodData['serving_size'],
                'serving_unit' => $food->serving_unit,
                'meal_type' => $foodData['meal_type'],
            ];
        }

        $mealPlan->foods()->sync($foodsToSync);
        $mealPlan->calculateNutrition();
        $mealPlan->save();

        return redirect()->route('nutrition.meal-plans.show', $mealPlan)
                        ->with('success', 'Meal plan updated successfully!');
    }

    /**
     * Remove the specified meal plan from storage.
     */
    public function destroy(MealPlan $mealPlan)
    {
        $this->authorize('delete', $mealPlan);
        
        $mealPlan->delete();
        
        return redirect()->route('nutrition.meal-plans.index')
                        ->with('success', 'Meal plan deleted successfully!');
    }

    /**
     * Add meal plan items to the user's food log.
     */
    public function addToLog(Request $request, MealPlan $mealPlan)
    {
        $this->authorize('view', $mealPlan);
        
        $validated = $request->validate([
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
        ]);

        $logs = [];
        
        foreach ($mealPlan->foods as $food) {
            $logs[] = [
                'user_id' => Auth::id(),
                'food_id' => $food->id,
                'serving_size' => $food->pivot->serving_size,
                'serving_unit' => $food->pivot->serving_unit,
                'meal_type' => $validated['meal_type'],
                'consumed_at' => $validated['date'] . ' ' . now()->format('H:i:s'),
                'notes' => 'Added from meal plan: ' . $mealPlan->name,
                'calories' => $food->calories * ($food->pivot->serving_size / $food->serving_size),
                'protein' => $food->protein * ($food->pivot->serving_size / $food->serving_size),
                'carbohydrates' => $food->carbohydrates * ($food->pivot->serving_size / $food->serving_size),
                'fat' => $food->fat * ($food->pivot->serving_size / $food->serving_size),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        NutritionLog::insert($logs);

        return back()->with('success', 'Meal plan added to your food log!');
    }

    /**
     * Toggle the public/private status of a meal plan.
     */
    public function togglePublic(MealPlan $mealPlan)
    {
        $this->authorize('update', $mealPlan);
        
        $mealPlan->update(['is_public' => !$mealPlan->is_public]);
        
        $status = $mealPlan->is_public ? 'public' : 'private';
        return back()->with('success', "Meal plan is now {$status}.");
    }
}