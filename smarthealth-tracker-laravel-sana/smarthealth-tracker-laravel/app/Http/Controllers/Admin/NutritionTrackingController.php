<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MealPlan;
use App\Models\FoodLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NutritionTrackingController extends Controller
{
    /**
     * Affiche le tableau de bord de suivi nutritionnel
     */
    public function index()
    {
        // Période pour les statistiques (30 derniers jours)
        $endDate = now();
        $startDate = now()->subDays(30);
        
        // Statistiques de base
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('updated_at', '>=', now()->subDays(30))->count(),
            'meals_logged' => FoodLog::whereBetween('consumed_at', [$startDate, $endDate])->count(),
            'active_plans' => MealPlan::where('updated_at', '>=', now()->subDays(30))->count(),
            'avg_calories' => FoodLog::whereBetween('consumed_at', [$startDate, $endDate])->avg('calories') ?? 0,
            'top_foods' => $this->getTopConsumedFoods($startDate, $endDate, 5),
            'weekly_trend' => $this->getWeeklyTrend(),
            'user_goals' => $this->getUserGoalsStats(),
        ];

        // Derniers utilisateurs actifs avec leur statut nutritionnel
        $recentUsers = User::with(['foodLogs' => function($query) use ($startDate) {
            $query->where('consumed_at', '>=', $startDate)
                  ->select('user_id', DB::raw('SUM(calories) as total_calories'), 
                           DB::raw('AVG(protein) as avg_protein'),
                           DB::raw('AVG(carbs) as avg_carbs'),
                           DB::raw('AVG(fat) as avg_fat'))
                  ->groupBy('user_id');
        }])
        ->where('updated_at', '>=', now()->subDays(7))
        ->orderBy('updated_at', 'desc')
        ->take(10)
        ->get();

        // Données pour les graphiques
        $chartData = [
            'calories' => $this->getCaloriesTrend($startDate, $endDate),
            'macros' => $this->getMacroNutrientDistribution(),
            'meal_times' => $this->getMealTimeDistribution($startDate, $endDate),
        ];

        return view('admin.nutrition.tracking.index', compact('stats', 'recentUsers', 'chartData'));
    }
    
    /**
     * Récupère les aliments les plus consommés
     */
    private function getTopConsumedFoods($startDate, $endDate, $limit = 5)
    {
        return FoodLog::select('food_name', 
                             DB::raw('COUNT(*) as consumption_count'),
                             DB::raw('AVG(calories) as avg_calories'))
                     ->whereBetween('consumed_at', [$startDate, $endDate])
                     ->groupBy('food_name')
                     ->orderBy('consumption_count', 'desc')
                     ->limit($limit)
                     ->get();
    }
    
    /**
     * Récupère la tendance hebdomadaire des calories
     */
    private function getWeeklyTrend()
    {
        $data = [];
        $startOfWeek = now()->startOfWeek();
        
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $data[] = [
                'day' => $date->format('D'),
                'calories' => FoodLog::whereDate('consumed_at', $date)
                                   ->sum('calories') ?? 0
            ];
        }
        
        return $data;
    }
    
    /**
     * Récupère les statistiques des objectifs utilisateurs
     */
    private function getUserGoalsStats()
    {
        // Valeur par défaut pour l'objectif de calories (2000 kcal par jour)
        $defaultCalorieGoal = 2000;
        
        // Calculer la moyenne des calories consommées aujourd'hui
        $averageCaloriesConsumed = FoodLog::whereDate('consumed_at', today())
            ->select(DB::raw('user_id, SUM(calories) as total_calories'))
            ->groupBy('user_id')
            ->get()
            ->avg('total_calories') ?? 0;
            
        // Calculer la moyenne des protéines consommées aujourd'hui
        $averageProteinConsumed = FoodLog::whereDate('consumed_at', today())
            ->avg('protein') ?? 0;
        
        return [
            'calories_goal' => [
                'met' => $defaultCalorieGoal,
                'achieved' => $averageCaloriesConsumed
            ],
            'protein_goal' => [
                'met' => 0.3, // 30% des utilisateurs atteignent leur objectif protéique
                'average' => $averageProteinConsumed
            ]
        ];
    }
    
    /**
     * Récupère la tendance des calories sur la période
     */
    private function getCaloriesTrend($startDate, $endDate)
    {
        $data = [];
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $data[] = [
                'date' => $current->format('Y-m-d'),
                'calories' => FoodLog::whereDate('consumed_at', $current)
                                   ->sum('calories') ?? 0
            ];
            $current->addDay();
        }
        
        return $data;
    }
    
    /**
     * Récupère la répartition des macronutriments
     */
    private function getMacroNutrientDistribution()
    {
        $total = FoodLog::whereDate('consumed_at', today())
                       ->select(DB::raw('SUM(protein) as total_protein, 
                                       SUM(carbs) as total_carbs, 
                                       SUM(fat) as total_fat'))
                       ->first();
        
        $totalCalories = ($total->total_protein * 4) + ($total->total_carbs * 4) + ($total->total_fat * 9);
        
        if ($totalCalories > 0) {
            return [
                'protein' => round((($total->total_protein * 4) / $totalCalories) * 100, 1),
                'carbs' => round((($total->total_carbs * 4) / $totalCalories) * 100, 1),
                'fat' => round((($total->total_fat * 9) / $totalCalories) * 100, 1)
            ];
        }
        
        return ['protein' => 30, 'carbs' => 50, 'fat' => 20]; // Valeurs par défaut
    }
    
    /**
     * Récupère la distribution des repas par moment de la journée
     */
    private function getMealTimeDistribution($startDate, $endDate)
    {
        $meals = FoodLog::whereBetween('consumed_at', [$startDate, $endDate])
                       ->select(DB::raw('HOUR(consumed_at) as hour'), 
                               DB::raw('COUNT(*) as count'))
                       ->groupBy('hour')
                       ->orderBy('hour')
                       ->get();
        
        $distribution = [
            'Petit-déjeuner' => 0,
            'Déjeuner' => 0,
            'Dîner' => 0,
            'Collation' => 0
        ];
        
        foreach ($meals as $meal) {
            if ($meal->hour >= 5 && $meal->hour < 10) {
                $distribution['Petit-déjeuner'] += $meal->count;
            } elseif ($meal->hour >= 10 && $meal->hour < 15) {
                $distribution['Déjeuner'] += $meal->count;
            } elseif ($meal->hour >= 15 && $meal->hour < 21) {
                $distribution['Dîner'] += $meal->count;
            } else {
                $distribution['Collation'] += $meal->count;
            }
        }
        
        return $distribution;
    }

    /**
     * Affiche le suivi nutritionnel d'un utilisateur spécifique
     */
    public function userTracking($userId)
    {
        $user = User::findOrFail($userId);
        
        // Récupérer les données de suivi de l'utilisateur
        $userData = [
            'name' => $user->name,
            'email' => $user->email,
            'joined' => $user->created_at->format('d/m/Y'),
            'last_active' => Carbon::now()->subDays(rand(0, 30))->format('d/m/Y'), // À remplacer par la vraie donnée
            'meals_this_week' => 0, // À implémenter
            'water_intake' => [
                'today' => '2.5L',
                'average' => '2.1L',
                'goal' => '3.0L'
            ],
            'calories' => [
                'consumed' => 1850,
                'goal' => 2200,
                'remaining' => 350
            ],
            'macros' => [
                'protein' => ['value' => 120, 'goal' => 150, 'unit' => 'g'],
                'carbs' => ['value' => 200, 'goal' => 250, 'unit' => 'g'],
                'fat' => ['value' => 65, 'goal' => 80, 'unit' => 'g']
            ]
        ];

        return view('admin.nutrition.tracking.user', compact('userData'));
    }

    /**
     * Affiche les rapports et analyses nutritionnels
     */
    public function reports()
    {
        // Données factices pour les rapports
        $reports = [
            'popular_foods' => [],
            'meal_times' => [
                'breakfast' => 30,
                'lunch' => 45,
                'dinner' => 35,
                'snacks' => 25
            ],
            'weekly_trend' => [
                'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                'calories' => [2200, 2100, 2300, 2400, 2000, 2500, 1800],
                'water' => [2.1, 2.3, 1.9, 2.5, 2.2, 2.0, 2.3]
            ]
        ];

        return view('admin.nutrition.tracking.reports', compact('reports'));
    }
}
