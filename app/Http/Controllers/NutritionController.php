<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\MealPlan;
use App\Models\MealPlanItem;
use App\Models\NutritionLog;
use App\Models\WaterIntake;
use App\Models\NutritionGoal;
use App\Models\FoodCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NutritionController extends Controller
{
    protected $dailyMacroGoals = [
        'calories' => 2000,
        'protein' => 150,    // 30% of 2000 calories (4 cal/g)
        'carbohydrates' => 250, // 50% of 2000 calories (4 cal/g)
        'fat' => 67,        // 30% of 2000 calories (9 cal/g)
        'fiber' => 30,
        'sugar' => 50,
        'sodium' => 2300,
        'cholesterol' => 300,
    ];

    /**
     * Display the nutrition dashboard with advanced analytics
     */
    public function dashboard(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : now();
        $user = Auth::user();
        
        // Get user's nutrition goals or use defaults
        $nutritionGoals = $user->nutritionGoals ?? (object)$this->dailyMacroGoals;
        
        // Get daily nutrition logs with food details
        $logs = NutritionLog::with(['food', 'mealType'])
            ->where('user_id', $user->id)
            ->whereDate('consumed_at', $date->format('Y-m-d'))
            ->orderBy('consumed_at')
            ->get();

        // Calculate daily totals
        $dailyTotals = [
            'calories' => $logs->sum('calories'),
            'protein' => $logs->sum('protein'),
            'carbohydrates' => $logs->sum('carbohydrates'),
            'fat' => $logs->sum('fat'),
            'fiber' => $logs->sum('fiber'),
            'sugar' => $logs->sum('sugar'),
            'sodium' => $logs->sum('sodium'),
            'cholesterol' => $logs->sum('cholesterol'),
        ];

        // Calculate progress percentages
        $progress = [];
        foreach ($dailyTotals as $key => $value) {
            $goal = $nutritionGoals->{$key} ?? $this->dailyMacroGoals[$key] ?? 1;
            $progress[$key] = $goal > 0 ? min(100, round(($value / $goal) * 100)) : 0;
        }

        // Get weekly summary for charts
        $weeklyData = $this->getWeeklyNutritionData($date);
        $monthlyData = $this->getMonthlyNutritionData($date);
        
        // Get meal distribution for the day
        $mealDistribution = $this->getMealDistribution($date);
        
        // Get water intake for the day
        $waterIntake = WaterIntake::where('user_id', $user->id)
            ->whereDate('date', $date->format('Y-m-d'))
            ->sum('amount_ml');

        // Get recommended foods based on user's goals
        $recommendedFoods = $this->getRecommendedFoods($dailyTotals, $nutritionGoals);

        // Get recent food logs for quick add
        $recentFoods = NutritionLog::with('food')
            ->select('food_id', DB::raw('MAX(consumed_at) as last_consumed'))
            ->where('user_id', $user->id)
            ->groupBy('food_id')
            ->orderBy('last_consumed', 'desc')
            ->limit(10)
            ->get()
            ->pluck('food');

        // Get all food categories for the food log form
        $foodCategories = FoodCategory::orderBy('name')->get();

        // Get user's meal types
        $mealTypes = [
            ['id' => 'breakfast', 'name' => 'Petit-déjeuner'],
            ['id' => 'lunch', 'name' => 'Déjeuner'],
            ['id' => 'dinner', 'name' => 'Dîner'],
            ['id' => 'snack', 'name' => 'Collation'],
        ];

        return view('nutrition.dashboard', [
            'logs' => $logs,
            'dailyTotals' => $dailyTotals,
            'nutritionGoals' => $nutritionGoals,
            'progress' => (object)$progress,
            'selectedDate' => $date,
            'weeklyData' => $weeklyData,
            'monthlyData' => $monthlyData,
            'mealDistribution' => $mealDistribution,
            'waterIntake' => $waterIntake,
            'recommendedFoods' => $recommendedFoods,
            'recentFoods' => $recentFoods,
            'foodCategories' => $foodCategories,
            'mealTypes' => $mealTypes,
            'mealPlans' => $mealPlans,
            'weeklyCalories' => array_sum($chartData['calories']),
            'weeklyProtein' => array_sum($chartData['protein']),
            'weeklyCarbs' => array_sum($chartData['carbs']),
            'weeklyFat' => array_sum($chartData['fat']),
        ]);
    }

    /**
     * Log a food item.
     */
    public function logFood(Request $request)
    {
        $validated = $request->validate([
            'food_id' => 'required|exists:foods,id',
            'serving_size' => 'required|numeric|min:0.1',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'consumed_at' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $food = Food::findOrFail($validated['food_id']);
        $ratio = $validated['serving_size'] / $food->serving_size;

        $log = new NutritionLog([
            'user_id' => Auth::id(),
            'food_id' => $food->id,
            'serving_size' => $validated['serving_size'],
            'serving_unit' => $food->serving_unit,
            'meal_type' => $validated['meal_type'],
            'consumed_at' => $validated['consumed_at'],
            'notes' => $validated['notes'] ?? null,
            'calories' => $food->calories * $ratio,
            'protein' => $food->protein * $ratio,
            'carbohydrates' => $food->carbohydrates * $ratio,
            'fat' => $food->fat * $ratio,
        ]);

        $log->save();

        return back()->with('success', 'Food logged successfully!');
    }

    /**
     * Delete a food log entry.
     */
    public function deleteFoodLog(NutritionLog $foodLog)
    {
        $this->authorize('delete', $foodLog);
        
        $foodLog->delete();
        
        return redirect()->back()->with('success', 'Food log deleted successfully!');
    }

    /**
     * Display the water intake tracking page.
     */
    public function waterIntake()
    {
        $today = now()->format('Y-m-d');
        $waterIntake = WaterIntake::firstOrCreate(
            ['user_id' => auth()->id(), 'date' => $today],
            ['amount' => 0]
        );

        $dailyGoal = 2000; // Default daily water goal in ml (2L)
        $progress = min(100, ($waterIntake->amount / $dailyGoal) * 100);

        return view('nutrition.water-intake', [
            'waterIntake' => $waterIntake,
            'dailyGoal' => $dailyGoal,
            'progress' => $progress,
        ]);
    }

    /**
     * Log water intake.
     */
    public function logWaterIntake(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:1000',
        ]);

        $today = now()->format('Y-m-d');
        $waterIntake = WaterIntake::firstOrCreate(
            ['user_id' => auth()->id(), 'date' => $today],
            ['amount' => 0]
        );

        $waterIntake->increment('amount', $request->amount);

        return redirect()->route('nutrition.water-intake')
            ->with('success', 'Water intake logged successfully!');
    }

    /**
     * Search for foods.
     */
    public function searchFoods(Request $request)
    {
        $query = $request->get('query');
        
        $foods = Food::where('is_approved', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json($foods);
    }

    /**
     * Get nutrition summary for a date range.
     */
    public function getNutritionSummary(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $summary = NutritionLog::select(
            DB::raw('DATE(consumed_at) as date'),
            DB::raw('SUM(calories) as calories'),
            DB::raw('SUM(protein) as protein'),
            DB::raw('SUM(carbohydrates) as carbohydrates'),
            DB::raw('SUM(fat) as fat')
        )
        ->where('user_id', Auth::id())
        ->whereBetween('consumed_at', [
            $request->start_date,
            $request->end_date
        ])
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return response()->json($summary);
    }
}