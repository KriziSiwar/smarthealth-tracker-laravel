<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MealPlan;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MealPlanController extends Controller
{
    /**
     * Affiche la liste des plans de repas
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $mealPlans = MealPlan::with('user')
            ->where('user_id', auth()->id())
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.nutrition.meal-plans.index', compact('mealPlans', 'search'));
    }

    /**
     * Affiche le formulaire de création d'un plan de repas
     */
    public function create()
    {
        return view('admin.nutrition.meal-plans.create');
    }

    /**
     * Enregistre un nouveau plan de repas
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'calories' => 'nullable|numeric|min:0',
            'protein' => 'nullable|numeric|min:0',
            'carbohydrates' => 'nullable|numeric|min:0',
            'fat' => 'nullable|numeric|min:0',
        ]);

        $mealPlan = Auth::user()->mealPlans()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_public' => $validated['is_public'] ?? false,
            'calories' => $validated['calories'] ?? 0,
            'protein' => $validated['protein'] ?? 0,
            'carbohydrates' => $validated['carbohydrates'] ?? 0,
            'fat' => $validated['fat'] ?? 0,
        ]);

        return redirect()
            ->route('admin.nutrition.meal-plans.show', $mealPlan)
            ->with('success', 'Plan de repas créé avec succès');
    }

    /**
     * Affiche les détails d'un plan de repas
     */
    public function show(MealPlan $mealPlan)
    {
        $this->authorize('view', $mealPlan);
        
        $mealPlan->load(['foods', 'user']);
        $foods = Food::all();
        
        return view('admin.nutrition.meal-plans.show', compact('mealPlan', 'foods'));
    }

    /**
     * Affiche le formulaire d'édition d'un plan de repas
     */
    public function edit(MealPlan $mealPlan)
    {
        $this->authorize('update', $mealPlan);
        
        $foods = Food::all();
        $mealPlan->load('foods');
        
        return view('admin.nutrition.meal-plans.edit', compact('mealPlan', 'foods'));
    }

    /**
     * Met à jour un plan de repas existant
     */
    public function update(Request $request, MealPlan $mealPlan)
    {
        $this->authorize('update', $mealPlan);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'calories' => 'nullable|numeric|min:0',
            'protein' => 'nullable|numeric|min:0',
            'carbohydrates' => 'nullable|numeric|min:0',
            'fat' => 'nullable|numeric|min:0',
        ]);

        $mealPlan->update($validated);
        
        // Mise à jour des aliments associés si nécessaire
        if ($request->has('foods')) {
            $mealPlan->foods()->sync($request->foods);
            $mealPlan->calculateNutrition();
        }

        return redirect()
            ->route('admin.nutrition.meal-plans.index')
            ->with('success', 'Plan de repas mis à jour avec succès');
    }

    /**
     * Supprime un plan de repas
     */
    public function destroy(MealPlan $mealPlan)
    {
        $this->authorize('delete', $mealPlan);
        
        $mealPlan->delete();
        
        return redirect()
            ->route('admin.nutrition.meal-plans.index')
            ->with('success', 'Plan de repas supprimé avec succès');
    }
    
    /**
     * Ajoute un aliment au plan de repas
     */
    public function addFood(Request $request, MealPlan $mealPlan)
    {
        $this->authorize('update', $mealPlan);
        
        $validated = $request->validate([
            'food_id' => 'required|exists:foods,id',
            'serving_size' => 'required|numeric|min:1',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
        ]);
        
        // Vérifier si l'aliment est déjà dans le plan
        if ($mealPlan->foods()->where('food_id', $validated['food_id'])->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Cet aliment est déjà dans le plan.');
        }
        
        // Ajouter l'aliment au plan
        $food = Food::findOrFail($validated['food_id']);
        
        $mealPlan->foods()->attach($food->id, [
            'serving_size' => $validated['serving_size'],
            'serving_unit' => 'g', // Unité par défaut
            'meal_type' => $validated['meal_type'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Recharger les relations et sauvegarder
        $mealPlan->load('foods');
        $mealPlan->calculateNutrition();
        $mealPlan->save();
        
        return redirect()
            ->route('admin.nutrition.meal-plans.show', $mealPlan)
            ->with('success', 'Aliment ajouté au plan avec succès.');
    }
    
    /**
     * Supprime un aliment du plan de repas
     */
    public function removeFood(MealPlan $mealPlan, Food $food)
    {
        $this->authorize('update', $mealPlan);
        
        // Vérifier si l'aliment est bien dans le plan
        if (!$mealPlan->foods()->where('food_id', $food->id)->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Cet aliment n\'est pas dans le plan.');
        }
        
        // Retirer l'aliment du plan
        $mealPlan->foods()->detach($food->id);
        
        // Recalculer les valeurs nutritionnelles
        $mealPlan->load('foods');
        $mealPlan->calculateNutrition();
        $mealPlan->save();
        
        return redirect()
            ->route('admin.nutrition.meal-plans.show', $mealPlan)
            ->with('success', 'Aliment retiré du plan avec succès.');
    }
}
