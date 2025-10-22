@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Modifier le plan de repas : {{ $mealPlan->name }}</h4>
                    <a href="{{ route('nutrition.meal-plans.show', $mealPlan) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Annuler
                    </a>
                </div>

                <div class="card-body">
                    <form id="mealPlanForm" action="{{ route('nutrition.meal-plans.update', $mealPlan) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom du plan de repas *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $mealPlan->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="2">{{ old('description', $mealPlan->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1" 
                                           {{ old('is_public', $mealPlan->is_public) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">
                                        Rendre ce plan de repas public
                                    </label>
                                    <small class="d-block text-muted">
                                        Les plans de repas publics peuvent être vus par d'autres utilisateurs.
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6 class="mb-0">Résumé nutritionnel</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <div class="h2 mb-0" id="totalCalories">{{ $mealPlan->calories }}</div>
                                            <small class="text-muted">Calories totales</small>
                                        </div>
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="h5 mb-0" id="totalProtein">{{ $mealPlan->protein }}g</div>
                                                <small class="text-muted">Protéines</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="h5 mb-0" id="totalCarbs">{{ $mealPlan->carbohydrates }}g</div>
                                                <small class="text-muted">Glucides</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="h5 mb-0" id="totalFat">{{ $mealPlan->fat }}g</div>
                                                <small class="text-muted">Lipides</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Repas</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addMealBtn">
                                    <i class="fas fa-plus"></i> Ajouter un repas
                                </button>
                            </div>
                            
                            <div id="mealsContainer">
                                @php $mealIndex = 0; @endphp
                                @foreach($mealPlan->foods->groupBy('pivot.meal_type') as $mealType => $foods)
                                    <div class="card mb-3 meal-item">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <div class="form-group mb-0 flex-grow-1 me-3">
                                                <select class="form-select meal-type" name="meals[{{ $mealIndex }}][type]" required>
                                                    <option value="breakfast" {{ $mealType === 'breakfast' ? 'selected' : '' }}>Petit-déjeuner</option>
                                                    <option value="lunch" {{ $mealType === 'lunch' ? 'selected' : '' }}>Déjeuner</option>
                                                    <option value="dinner" {{ $mealType === 'dinner' ? 'selected' : '' }}>Dîner</option>
                                                    <option value="snack" {{ $mealType === 'snack' ? 'selected' : '' }}>Collation</option>
                                                </select>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-meal">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="foods-container">
                                                @php $foodIndex = 0; @endphp
                                                @foreach($foods as $food)
                                                    <div class="food-item border-bottom py-2">
                                                        <div class="row g-2">
                                                            <div class="col-md-5">
                                                                <select class="form-select food-select" name="meals[{{ $mealIndex }}][foods][{{ $foodIndex }}][id]" required>
                                                                    <option value="">Sélectionner un aliment</option>
                                                                    @foreach($allFoods as $foodOption)
                                                                        <option value="{{ $foodOption->id }}" 
                                                                                data-serving-size="{{ $foodOption->serving_size }}" 
                                                                                data-serving-unit="{{ $foodOption->serving_unit }}"
                                                                                data-calories="{{ $foodOption->calories }}"
                                                                                data-protein="{{ $foodOption->protein }}"
                                                                                data-carbs="{{ $foodOption->carbohydrates }}"
                                                                                data-fat="{{ $foodOption->fat }}"
                                                                                {{ $foodOption->id == $food->id ? 'selected' : '' }}>
                                                                            {{ $foodOption->name }} ({{ $foodOption->serving_size }} {{ $foodOption->serving_unit }})
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="input-group">
                                                                    <input type="number" step="0.1" min="0.1" class="form-control serving-size" 
                                                                           name="meals[{{ $mealIndex }}][foods][{{ $foodIndex }}][serving_size]" 
                                                                           value="{{ $food->pivot->serving_size }}" required>
                                                                    <span class="input-group-text serving-unit">{{ $food->pivot->serving_unit }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="nutrition-info">
                                                                    @php
                                                                        $ratio = $food->pivot->serving_size / $food->serving_size;
                                                                        $calories = round($food->calories * $ratio);
                                                                        $protein = $food->protein * $ratio;
                                                                        $carbs = $food->carbohydrates * $ratio;
                                                                        $fat = $food->fat * $ratio;
                                                                    @endphp
                                                                    <small class="text-muted">
                                                                        <span class="food-calories">{{ $calories }}</span> kcal | 
                                                                        P: <span class="food-protein">{{ number_format($protein, 1) }}</span>g 
                                                                        C: <span class="food-carbs">{{ number_format($carbs, 1) }}</span>g 
                                                                        F: <span class="food-fat">{{ number_format($fat, 1) }}</span>g
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1 text-end">
                                                                <button type="button" class="btn btn-sm btn-outline-danger remove-food">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @php $foodIndex++; @endphp
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2 add-food-btn">
                                                <i class="fas fa-plus"></i> Ajouter un aliment
                                            </button>
                                        </div>
                                    </div>
                                    @php $mealIndex++; @endphp
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Mettre à jour le plan de repas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template pour un repas -->
<template id="mealTemplate">
    <div class="card mb-3 meal-item">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="form-group mb-0 flex-grow-1 me-3">
                <select class="form-select meal-type" name="meals[INDEX][type]" required>
                    <option value="breakfast">Petit-déjeuner</option>
                    <option value="lunch">Déjeuner</option>
                    <option value="dinner">Dîner</option>
                    <option value="snack">Collation</option>
                </select>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger remove-meal">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="foods-container">
                <!-- Les aliments seront ajoutés ici -->
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary mt-2 add-food-btn">
                <i class="fas fa-plus"></i> Ajouter un aliment
            </button>
        </div>
    </div>
</template>

<!-- Template pour un aliment -->
<template id="foodTemplate">
    <div class="food-item border-bottom py-2">
        <div class="row g-2">
            <div class="col-md-5">
                <select class="form-select food-select" name="meals[MEAL_INDEX][foods][FOOD_INDEX][id]" required>
                    <option value="">Sélectionner un aliment</option>
                    @foreach($allFoods as $food)
                        <option value="{{ $food->id }}" 
                                data-serving-size="{{ $food->serving_size }}" 
                                data-serving-unit="{{ $food->serving_unit }}"
                                data-calories="{{ $food->calories }}"
                                data-protein="{{ $food->protein }}"
                                data-carbs="{{ $food->carbohydrates }}"
                                data-fat="{{ $food->fat }}">
                            {{ $food->name }} ({{ $food->serving_size }} {{ $food->serving_unit }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <input type="number" step="0.1" min="0.1" class="form-control serving-size" 
                           name="meals[MEAL_INDEX][foods][FOOD_INDEX][serving_size]" value="1" required>
                    <span class="input-group-text serving-unit">g</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="nutrition-info">
                    <small class="text-muted">
                        <span class="food-calories">0</span> kcal | 
                        P: <span class="food-protein">0</span>g 
                        C: <span class="food-carbs">0</span>g 
                        F: <span class="food-fat">0</span>g
                    </small>
                </div>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger remove-food">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let mealIndex = {{ $mealPlan->foods->groupBy('pivot.meal_type')->count() }};
    const mealsContainer = document.getElementById('mealsContainer');
    const mealTemplate = document.getElementById('mealTemplate');
    const foodTemplate = document.getElementById('foodTemplate');
    const addMealBtn = document.getElementById('addMealBtn');
    
    // Ajouter un repas
    function addMeal() {
        const mealElement = document.createElement('div');
        mealElement.innerHTML = mealTemplate.innerHTML.replace(/INDEX/g, mealIndex);
        mealsContainer.appendChild(mealElement);
        
        // Ajouter un aliment par défaut
        const addFoodBtn = mealElement.querySelector('.add-food-btn');
        addFoodBtn.addEventListener('click', () => addFood(mealElement, mealIndex));
        
        // Supprimer le repas
        const removeMealBtn = mealElement.querySelector('.remove-meal');
        removeMealBtn.addEventListener('click', () => {
            if (confirm('Voulez-vous vraiment supprimer ce repas ?')) {
                mealElement.remove();
                updateTotals();
            }
        });
        
        // Ajouter un aliment initial
        addFood(mealElement, mealIndex);
        
        mealIndex++;
    }
    
    // Ajouter un aliment à un repas
    function addFood(mealElement, mealIdx) {
        const foodsContainer = mealElement.querySelector('.foods-container');
        const foodElement = document.createElement('div');
        const foodId = foodsContainer.children.length;
        
        foodElement.innerHTML = foodTemplate.innerHTML
            .replace(/MEAL_INDEX/g, mealIdx)
            .replace(/FOOD_INDEX/g, foodId);
            
        foodsContainer.appendChild(foodElement);
        
        // Gérer la sélection d'un aliment
        const foodSelect = foodElement.querySelector('.food-select');
        const servingSize = foodElement.querySelector('.serving-size');
        const servingUnit = foodElement.querySelector('.serving-unit');
        const removeBtn = foodElement.querySelector('.remove-food');
        
        foodSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const servingSizeValue = selectedOption.dataset.servingSize;
                const servingUnitValue = selectedOption.dataset.servingUnit;
                
                servingSize.value = servingSizeValue;
                servingUnit.textContent = servingUnitValue;
                
                updateFoodNutrition(foodElement, selectedOption, parseFloat(servingSizeValue));
                updateTotals();
            }
        });
        
        // Mettre à jour les valeurs nutritionnelles lors du changement de portion
        servingSize.addEventListener('input', function() {
            const selectedOption = foodSelect.options[foodSelect.selectedIndex];
            if (selectedOption.value) {
                updateFoodNutrition(foodElement, selectedOption, parseFloat(this.value) || 0);
                updateTotals();
            }
        });
        
        // Supprimer l'aliment
        removeBtn.addEventListener('click', function() {
            foodElement.remove();
            updateTotals();
        });
        
        // Initialiser les valeurs pour les aliments existants
        if (foodSelect.value) {
            const selectedOption = foodSelect.options[foodSelect.selectedIndex];
            if (selectedOption.value) {
                const servingSizeValue = parseFloat(servingSize.value) || 1;
                updateFoodNutrition(foodElement, selectedOption, servingSizeValue);
            }
        }
        
        return foodElement;
    }
    
    // Mettre à jour les valeurs nutritionnelles d'un aliment
    function updateFoodNutrition(foodElement, foodOption, servingSize) {
        const baseServingSize = parseFloat(foodOption.dataset.servingSize);
        const ratio = servingSize / baseServingSize;
        
        const calories = Math.round(parseFloat(foodOption.dataset.calories) * ratio);
        const protein = (parseFloat(foodOption.dataset.protein) * ratio).toFixed(1);
        const carbs = (parseFloat(foodOption.dataset.carbs) * ratio).toFixed(1);
        const fat = (parseFloat(foodOption.dataset.fat) * ratio).toFixed(1);
        
        foodElement.querySelector('.food-calories').textContent = calories;
        foodElement.querySelector('.food-protein').textContent = protein;
        foodElement.querySelector('.food-carbs').textContent = carbs;
        foodElement.querySelector('.food-fat').textContent = fat;
        
        return { calories, protein, carbs, fat };
    }
    
    // Mettre à jour les totaux
    function updateTotals() {
        let totalCalories = 0;
        let totalProtein = 0;
        let totalCarbs = 0;
        let totalFat = 0;
        
        document.querySelectorAll('.food-item').forEach(foodItem => {
            const calories = parseInt(foodItem.querySelector('.food-calories').textContent) || 0;
            const protein = parseFloat(foodItem.querySelector('.food-protein').textContent) || 0;
            const carbs = parseFloat(foodItem.querySelector('.food-carbs').textContent) || 0;
            const fat = parseFloat(foodItem.querySelector('.food-fat').textContent) || 0;
            
            totalCalories += calories;
            totalProtein += protein;
            totalCarbs += carbs;
            totalFat += fat;
        });
        
        document.getElementById('totalCalories').textContent = totalCalories.toLocaleString();
        document.getElementById('totalProtein').textContent = totalProtein.toFixed(1) + 'g';
        document.getElementById('totalCarbs').textContent = totalCarbs.toFixed(1) + 'g';
        document.getElementById('totalFat').textContent = totalFat.toFixed(1) + 'g';
    }
    
    // Initialiser les événements pour les repas existants
    function initExistingMeals() {
        document.querySelectorAll('.meal-item').forEach((mealElement, mealIndex) => {
            // Ajouter un aliment
            const addFoodBtn = mealElement.querySelector('.add-food-btn');
            addFoodBtn.addEventListener('click', () => addFood(mealElement, mealIndex));
            
            // Supprimer le repas
            const removeMealBtn = mealElement.querySelector('.remove-meal');
            removeMealBtn.addEventListener('click', () => {
                if (confirm('Voulez-vous vraiment supprimer ce repas ?')) {
                    mealElement.remove();
                    updateTotals();
                }
            });
            
            // Initialiser les aliments existants
            const foodsContainer = mealElement.querySelector('.foods-container');
            const foodItems = foodsContainer.querySelectorAll('.food-item');
            
            foodItems.forEach((foodItem, foodIndex) => {
                const foodSelect = foodItem.querySelector('.food-select');
                const servingSize = foodItem.querySelector('.serving-size');
                const servingUnit = foodItem.querySelector('.serving-unit');
                const removeBtn = foodItem.querySelector('.remove-food');
                
                foodSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.value) {
                        const servingSizeValue = selectedOption.dataset.servingSize;
                        const servingUnitValue = selectedOption.dataset.servingUnit;
                        
                        servingSize.value = servingSizeValue;
                        servingUnit.textContent = servingUnitValue;
                        
                        updateFoodNutrition(foodItem, selectedOption, parseFloat(servingSizeValue));
                        updateTotals();
                    }
                });
                
                servingSize.addEventListener('input', function() {
                    const selectedOption = foodSelect.options[foodSelect.selectedIndex];
                    if (selectedOption.value) {
                        updateFoodNutrition(foodItem, selectedOption, parseFloat(this.value) || 0);
                        updateTotals();
                    }
                });
                
                removeBtn.addEventListener('click', function() {
                    foodItem.remove();
                    updateTotals();
                });
                
                // Initialiser les valeurs pour les aliments existants
                if (foodSelect.value) {
                    const selectedOption = foodSelect.options[foodSelect.selectedIndex];
                    if (selectedOption.value) {
                        const servingSizeValue = parseFloat(servingSize.value) || 1;
                        updateFoodNutrition(foodItem, selectedOption, servingSizeValue);
                    }
                }
            });
        });
    }
    
    // Événements
    addMealBtn.addEventListener('click', addMeal);
    
    // Initialiser les repas existants
    initExistingMeals();
    
    // Mettre à jour les totaux initiaux
    updateTotals();
    
    // Valider le formulaire avant soumission
    document.getElementById('mealPlanForm').addEventListener('submit', function(e) {
        const mealItems = document.querySelectorAll('.meal-item');
        if (mealItems.length === 0) {
            e.preventDefault();
            alert('Veuillez ajouter au moins un repas.');
            return false;
        }
        
        let hasFoods = false;
        mealItems.forEach(meal => {
            if (meal.querySelectorAll('.food-item').length > 0) {
                hasFoods = true;
            }
        });
        
        if (!hasFoods) {
            e.preventDefault();
            alert('Veuillez ajouter au moins un aliment à l\'un des repas.');
            return false;
        }
    });
});
</script>
@endpush