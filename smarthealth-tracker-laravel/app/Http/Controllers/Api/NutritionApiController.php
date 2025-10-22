<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\MealPlan;
use App\Models\MealPlanItem;
use App\Models\NutritionGoal;
use App\Models\NutritionLog;
use App\Models\User;
use App\Models\WaterIntake;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NutritionApiController extends Controller
{
    /**
     * Get nutrition dashboard data
     */
    public function dashboard(Request $request): JsonResponse
    {
        $date = $request->date ? Carbon::parse($request->date) : now();
        $user = Auth::user();
        
        // Get nutrition logs for the selected date
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

        // Get user's nutrition goals
        $nutritionGoals = $user->nutritionGoals ?? (new NutritionGoal())->getFillableDefaults();
        
        // Calculate progress percentages
        $progress = [];
        foreach ($dailyTotals as $key => $value) {
            $goal = $nutritionGoals[$key] ?? 1;
            $progress[$key] = $goal > 0 ? min(100, round(($value / $goal) * 100)) : 0;
        }

        // Get water intake
        $waterIntake = WaterIntake::where('user_id', $user->id)
            ->whereDate('date', $date->format('Y-m-d'))
            ->sum('amount_ml');

        // Get recent foods
        $recentFoods = NutritionLog::with('food')
            ->select('food_id', DB::raw('MAX(consumed_at) as last_consumed'))
            ->where('user_id', $user->id)
            ->groupBy('food_id')
            ->orderBy('last_consumed', 'desc')
            ->limit(10)
            ->get()
            ->pluck('food');

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date->format('Y-m-d'),
                'daily_totals' => $dailyTotals,
                'nutrition_goals' => $nutritionGoals,
                'progress' => $progress,
                'water_intake' => $waterIntake,
                'water_goal' => $nutritionGoals['water_goal_ml'] ?? 2500,
                'recent_foods' => $recentFoods,
                'meal_plans' => $this->getMealPlans($date),
                'weekly_summary' => $this->getWeeklySummary($date),
            ]
        ]);
    }

    /**
     * Log a food item
     */
    public function logFood(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'food_id' => 'required|exists:foods,id',
            'serving_size' => 'required|numeric|min:0.1',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'consumed_at' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $food = Food::findOrFail($request->food_id);
        $servingSize = $request->serving_size;
        $multiplier = $servingSize / $food->serving_size;

        $log = NutritionLog::create([
            'user_id' => Auth::id(),
            'food_id' => $food->id,
            'serving_size' => $servingSize,
            'calories' => $food->calories * $multiplier,
            'protein' => $food->protein * $multiplier,
            'carbohydrates' => $food->carbohydrates * $multiplier,
            'fat' => $food->fat * $multiplier,
            'fiber' => $food->fiber * $multiplier,
            'sugar' => $food->sugar * $multiplier,
            'sodium' => $food->sodium * $multiplier,
            'cholesterol' => $food->cholesterol * $multiplier,
            'meal_type' => $request->meal_type,
            'consumed_at' => $request->consumed_at ?? now(),
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Food logged successfully',
            'data' => $log->load('food')
        ], 201);
    }

    /**
     * Log water intake
     */
    public function logWater(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount_ml' => 'required|integer|min:1|max:5000',
            'date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $waterLog = WaterIntake::create([
            'user_id' => Auth::id(),
            'amount_ml' => $request->amount_ml,
            'date' => $request->date ?? now()->format('Y-m-d'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Water intake logged successfully',
            'data' => $waterLog
        ], 201);
    }

    /**
     * Get food search results
     */
    public function searchFood(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2|max:100',
            'category_id' => 'nullable|exists:food_categories,id',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $query = Food::query()
            ->where('name', 'like', '%' . $request->query . '%')
            ->orWhere('brand', 'like', '%' . $request->query . '%');

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $perPage = $request->per_page ?? 15;
        $foods = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $foods
        ]);
    }

    /**
     * Get food categories
     */
    public function getCategories(): JsonResponse
    {
        $categories = FoodCategory::orderBy('name')->get();
        
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get nutrition goals for the authenticated user
     */
    public function getNutritionGoals(): JsonResponse
    {
        $goals = Auth::user()->nutritionGoals;
        
        return response()->json([
            'success' => true,
            'data' => $goals
        ]);
    }

    /**
     * Update nutrition goals
     */
    public function updateNutritionGoals(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'calories' => 'nullable|integer|min:500|max:10000',
            'protein' => 'nullable|integer|min:0|max:500',
            'carbohydrates' => 'nullable|integer|min:0|max:1500',
            'fat' => 'nullable|integer|min:0|max:500',
            'fiber' => 'nullable|integer|min:0|max:200',
            'sugar' => 'nullable|integer|min:0|max:500',
            'sodium' => 'nullable|integer|min:0|max:10000',
            'cholesterol' => 'nullable|integer|min:0|max:2000',
            'water_goal_ml' => 'nullable|integer|min:500|max:10000',
            'activity_level' => 'nullable|in:sedentary,lightly_active,moderately_active,very_active,extra_active',
            'goal_type' => 'nullable|in:maintain,lose_weight,gain_weight,build_muscle',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $goals = Auth::user()->nutritionGoals()->updateOrCreate(
            ['user_id' => Auth::id()],
            $request->all()
        );

        return response()->json([
            'success' => true,
            'message' => 'Nutrition goals updated successfully',
            'data' => $goals
        ]);
    }

    /**
     * Get meal plans for a specific date
     */
    public function getMealPlansForDate(Request $request, $date): JsonResponse
    {
        $date = Carbon::parse($date);
        $mealPlans = MealPlan::with(['items.food'])
            ->where('user_id', Auth::id())
            ->whereDate('date', $date->format('Y-m-d'))
            ->orderBy('meal_type')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $mealPlans
        ]);
    }

    /**
     * Create a new meal plan
     */
    public function createMealPlan(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'items' => 'required|array|min:1',
            'items.*.food_id' => 'required|exists:foods,id',
            'items.*.serving_size' => 'required|numeric|min:0.1',
            'items.*.notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $mealPlan = MealPlan::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'description' => $request->description,
                'date' => $request->date,
                'meal_type' => $request->meal_type,
            ]);

            foreach ($request->items as $item) {
                $food = Food::find($item['food_id']);
                $multiplier = $item['serving_size'] / $food->serving_size;

                $mealPlan->items()->create([
                    'food_id' => $food->id,
                    'serving_size' => $item['serving_size'],
                    'calories' => $food->calories * $multiplier,
                    'protein' => $food->protein * $multiplier,
                    'carbohydrates' => $food->carbohydrates * $multiplier,
                    'fat' => $food->fat * $multiplier,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Meal plan created successfully',
                'data' => $mealPlan->load('items.food')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create meal plan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create meal plan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log all items from a meal plan
     */
    public function logMealPlan(Request $request, $mealPlanId): JsonResponse
    {
        $mealPlan = MealPlan::with('items.food')
            ->where('user_id', Auth::id())
            ->findOrFail($mealPlanId);

        DB::beginTransaction();

        try {
            $loggedItems = [];
            
            foreach ($mealPlan->items as $item) {
                $log = NutritionLog::create([
                    'user_id' => Auth::id(),
                    'food_id' => $item->food_id,
                    'serving_size' => $item->serving_size,
                    'calories' => $item->calories,
                    'protein' => $item->protein,
                    'carbohydrates' => $item->carbohydrates,
                    'fat' => $item->fat,
                    'fiber' => $item->fiber,
                    'sugar' => $item->sugar,
                    'sodium' => $item->sodium,
                    'cholesterol' => $item->cholesterol,
                    'meal_type' => $mealPlan->meal_type,
                    'consumed_at' => now(),
                    'notes' => $item->notes,
                ]);

                $loggedItems[] = $log->load('food');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Meal plan logged successfully',
                'data' => $loggedItems
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to log meal plan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to log meal plan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get nutrition summary for a date range
     */
    public function getNutritionSummary(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $summary = NutritionLog::select(
                DB::raw('DATE(consumed_at) as date'),
                DB::raw('SUM(calories) as calories'),
                DB::raw('SUM(protein) as protein'),
                DB::raw('SUM(carbohydrates) as carbohydrates'),
                DB::raw('SUM(fat) as fat'),
                DB::raw('SUM(fiber) as fiber'),
                DB::raw('SUM(sugar) as sugar'),
                DB::raw('SUM(sodium) as sodium'),
                DB::raw('SUM(cholesterol) as cholesterol')
            )
            ->where('user_id', Auth::id())
            ->whereBetween('consumed_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $waterIntake = WaterIntake::select(
                'date',
                DB::raw('SUM(amount_ml) as total_ml')
            )
            ->where('user_id', Auth::id())
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(function($item) {
                return $item->total_ml;
            });

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => $summary,
                'water_intake' => $waterIntake,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]
        ]);
    }

    /**
     * Get weekly nutrition summary
     */
    private function getWeeklySummary(Carbon $date): array
    {
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        $data = [
            'labels' => [],
            'calories' => [],
            'protein' => [],
            'carbs' => [],
            'fat' => [],
        ];

        $currentDate = $startOfWeek->copy();
        while ($currentDate <= $endOfWeek) {
            $formattedDate = $currentDate->format('Y-m-d');
            
            $dailyTotals = NutritionLog::where('user_id', Auth::id())
                ->whereDate('consumed_at', $formattedDate)
                ->select(
                    DB::raw('SUM(calories) as calories'),
                    DB::raw('SUM(protein) as protein'),
                    DB::raw('SUM(carbohydrates) as carbohydrates'),
                    DB::raw('SUM(fat) as fat')
                )
                ->first();

            $data['labels'][] = $currentDate->format('D, M j');
            $data['calories'][] = (int)($dailyTotals->calories ?? 0);
            $data['protein'][] = (int)($dailyTotals->protein ?? 0);
            $data['carbs'][] = (int)($dailyTotals->carbohydrates ?? 0);
            $data['fat'][] = (int)($dailyTotals->fat ?? 0);

            $currentDate->addDay();
        }

        return $data;
    }

    /**
     * Get meal distribution for a specific date
     */
    private function getMealDistribution(Carbon $date): array
    {
        $mealTypes = ['breakfast', 'lunch', 'dinner', 'snack'];
        $distribution = [];

        foreach ($mealTypes as $mealType) {
            $total = NutritionLog::where('user_id', Auth::id())
                ->where('meal_type', $mealType)
                ->whereDate('consumed_at', $date->format('Y-m-d'))
                ->sum('calories');

            $distribution[$mealType] = (int)$total;
        }

        return $distribution;
    }

    /**
     * Get meal plans for a specific date
     */
    private function getMealPlans(Carbon $date)
    {
        return MealPlan::with(['items.food'])
            ->where('user_id', Auth::id())
            ->whereDate('date', $date->format('Y-m-d'))
            ->orderBy('meal_type')
            ->get();
    }
}
