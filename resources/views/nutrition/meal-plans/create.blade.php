@extends('layouts.app')

@section('content')
<div style="padding-top: 110px;"> {{-- Wrapper pour éviter le masquage par la navbar fixe --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Nouveau Plan de Repas</h4>
                    <a href="{{ route('nutrition.meal-plans.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>

                <div class="card-body">
                    <form id="mealPlanForm" action="{{ route('nutrition.meal-plans.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">Nom du Plan *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label fw-bold">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_public">
                                        Rendre ce plan public
                                    </label>
                                    <small class="d-block text-muted">
                                        Les plans publics peuvent être partagés avec d'autres utilisateurs.
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card bg-light border-0">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Prévisualisation Nutritionnelle</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <canvas id="previewChart" height="150"></canvas>
                                        <div class="mt-3">
                                            <div class="row text-center">
                                                <div class="col-6">
                                                    <div class="h5 mb-0" id="previewCalories">0</div>
                                                    <small class="text-muted">Calories</small>
                                                </div>
                                                <div class="col-6">
                                                    <div class="h5 mb-0" id="previewProtein">0g</div>
                                                    <small class="text-muted">Protéines</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5><i class="fas fa-utensils me-2"></i>Composition du Plan</h5>
                                <button type="button" class="btn btn-primary btn-sm" id="addMealBtn">
                                    <i class="fas fa-plus me-1"></i>Ajouter un Repas
                                </button>
                            </div>
                            
                            <div id="mealsContainer">
                                <!-- Les repas seront ajoutés ici -->
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i>Enregistrer le Plan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

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
                    @foreach($foods as $food)
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
    let mealIndex = 0;
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
    
    // Graphique de prévisualisation
    const ctx = document.getElementById('previewChart');
    if (ctx) {
        console.log('Canvas trouvé'); // Debug
        const previewChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Protéines', 'Glucides', 'Lipides'],
                datasets: [{
                    data: [0, 0, 0],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12 }
                    }
                }
            }
        });
    } else {
        console.error('Canvas previewChart non trouvé');
    }

    // Fonction pour mettre à jour le graphique et les totaux
    window.updatePreview = function() {
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
        
        // Mettre à jour les éléments
        document.getElementById('previewCalories').textContent = totalCalories.toLocaleString();
        document.getElementById('previewProtein').textContent = totalProtein.toFixed(1) + 'g';
        
        // Mettre à jour le graphique
        previewChart.data.datasets[0].data = [totalProtein, totalCarbs, totalFat];
        previewChart.update();
    };

    // Appeler updatePreview après chaque changement (déjà dans updateTotals)
    // Modifier updateTotals pour appeler updatePreview
    const originalUpdateTotals = updateTotals;
    updateTotals = function() {
        originalUpdateTotals();
        updatePreview();
    };
    
    // Événements
    addMealBtn.addEventListener('click', addMeal);
    
    // Ajouter un repas par défaut au chargement
    addMeal();
    
    // Valider le formulaire avant soumission et formater les données
    document.getElementById('mealPlanForm').addEventListener('submit', function(e) {
        console.log('Formulaire soumis'); // Debug
        const mealItems = document.querySelectorAll('.meal-item');
        console.log('Nombre de repas:', mealItems.length); // Debug
        if (mealItems.length === 0) {
            e.preventDefault();
            alert('Veuillez ajouter au moins un repas.');
            return;
        }
        
        let hasFoods = false;
        const foods = [];
        
        mealItems.forEach((meal, mealIndex) => {
            const mealType = meal.querySelector('.meal-type').value;
            const foodItems = meal.querySelectorAll('.food-item');
            console.log('Repas', mealIndex, ':', foodItems.length, 'aliments'); // Debug
            
            foodItems.forEach((foodItem, foodIndex) => {
                const foodId = foodItem.querySelector('.food-select').value;
                const servingSize = foodItem.querySelector('.serving-size').value;
                console.log('Aliment', foodIndex, ': ID=', foodId, 'Portion=', servingSize); // Debug
                
                if (foodId) {
                    hasFoods = true;
                    foods.push({
                        id: foodId,
                        serving_size: servingSize,
                        meal_type: mealType
                    });
                }
            });
        });
        
        console.log('Aliments valides:', hasFoods, 'Total foods:', foods.length); // Debug
        if (!hasFoods) {
            e.preventDefault();
            alert('Veuillez ajouter au moins un aliment à l\'un des repas.');
            return;
        }
        
        // Ajouter un champ caché avec les données formatées
        const foodsInput = document.createElement('input');
        foodsInput.type = 'hidden';
        foodsInput.name = 'foods';
        foodsInput.value = JSON.stringify(foods);
        this.appendChild(foodsInput);
        console.log('Champ foods ajouté:', foodsInput.value); // Debug
        
        // Permettre la soumission
        return true;
    });
});
</script>
@endpush