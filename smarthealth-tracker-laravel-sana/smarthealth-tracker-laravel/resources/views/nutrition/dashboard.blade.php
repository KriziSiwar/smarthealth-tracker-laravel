@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Nutrition Dashboard</h4>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#logFoodModal">
                            <i class="fas fa-plus"></i> Log Food
                        </button>
                        <a href="{{ route('nutrition.meal-plans.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-utensils"></i> Meal Plans
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Daily Summary -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Daily Summary - {{ now()->format('F j, Y') }}</h5>
                            <div class="progress mb-3" style="height: 30px;">
                                @php
                                    $caloriePercent = min(100, ($dailyTotals['calories'] / $dailyGoals['calories']) * 100);
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $caloriePercent }}%" 
                                     aria-valuenow="{{ $caloriePercent }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ $dailyTotals['calories'] }} / {{ $dailyGoals['calories'] }} kcal
                                </div>
                            </div>
                            
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title text-muted">Protein</h6>
                                            <h4 class="mb-0">{{ $dailyTotals['protein'] }}g</h4>
                                            <small class="text-muted">{{ round(($dailyTotals['protein'] / $dailyGoals['protein']) * 100) }}% of goal</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title text-muted">Carbs</h6>
                                            <h4 class="mb-0">{{ $dailyTotals['carbohydrates'] }}g</h4>
                                            <small class="text-muted">{{ round(($dailyTotals['carbohydrates'] / $dailyGoals['carbohydrates']) * 100) }}% of goal</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title text-muted">Fat</h6>
                                            <h4 class="mb-0">{{ $dailyTotals['fat'] }}g</h4>
                                            <small class="text-muted">{{ round(($dailyTotals['fat'] / $dailyGoals['fat']) * 100) }}% of goal</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title text-muted">Fiber</h6>
                                            <h4 class="mb-0">{{ $dailyTotals['fiber'] }}g</h4>
                                            <small class="text-muted">{{ round(($dailyTotals['fiber'] / $dailyGoals['fiber']) * 100) }}% of goal</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Food Log -->
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Today's Food Log</h5>
                                </div>
                                <div class="card-body">
                                    @if($logs->isEmpty())
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-utensils fa-3x mb-3"></i>
                                            <p>No food logged today. Click the button above to add food.</p>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Food</th>
                                                        <th class="text-end">Calories</th>
                                                        <th class="text-end">Protein</th>
                                                        <th class="text-end">Carbs</th>
                                                        <th class="text-end">Fat</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="foodLogTable">
                                                    @foreach($logs as $log)
                                                        <tr data-log-id="{{ $log->id }}">
                                                            <td>
                                                                <strong>{{ $log->food->name }}</strong><br>
                                                                <small class="text-muted">
                                                                    {{ ucfirst($log->meal_type) }} • 
                                                                    {{ $log->servings }} {{ $log->servings == 1 ? 'serving' : 'servings' }}
                                                                    @if($log->notes)
                                                                        • {{ $log->notes }}
                                                                    @endif
                                                                </small>
                                                            </td>
                                                            <td class="text-end">{{ $log->nutritional_values['calories'] }}</td>
                                                            <td class="text-end">{{ $log->nutritional_values['protein'] }}g</td>
                                                            <td class="text-end">{{ $log->nutritional_values['carbs'] }}g</td>
                                                            <td class="text-end">{{ $log->nutritional_values['fat'] }}g</td>
                                                            <td class="text-end">
                                                                <button class="btn btn-sm btn-outline-danger delete-log" data-id="{{ $log->id }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Meal Plan -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Today's Meal Plan</h5>
                                    <a href="{{ route('nutrition.meal-plans.create') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-plus"></i> Add
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($mealPlans->isEmpty())
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-calendar-plus fa-3x mb-3"></i>
                                            <p>No meal plans for today. Create one to get started!</p>
                                        </div>
                                    @else
                                        <div id="mealPlansAccordion">
                                            @foreach($mealPlans as $mealPlan)
                                                <div class="card mb-2">
                                                    <div class="card-header p-0" id="heading{{ $mealPlan->id }}">
                                                        <button class="btn btn-link w-100 text-start px-3 py-2" 
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#collapse{{ $mealPlan->id }}" 
                                                                aria-expanded="true" 
                                                                aria-controls="collapse{{ $mealPlan->id }}">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span>
                                                                    <strong>{{ ucfirst($mealPlan->meal_type) }}: {{ $mealPlan->name }}</strong>
                                                                    @if($mealPlan->description)
                                                                        <small class="d-block text-muted">{{ Str::limit($mealPlan->description, 30) }}</small>
                                                                    @endif
                                                                </span>
                                                                <i class="fas fa-chevron-down"></i>
                                                            </div>
                                                        </button>
                                                    </div>

                                                    <div id="collapse{{ $mealPlan->id }}" 
                                                         class="collapse" 
                                                         aria-labelledby="heading{{ $mealPlan->id }}" 
                                                         data-bs-parent="#mealPlansAccordion">
                                                        <div class="card-body">
                                                            @if($mealPlan->description)
                                                                <p class="small">{{ $mealPlan->description }}</p>
                                                                <hr>
                                                            @endif
                                                            
                                                            <ul class="list-group list-group-flush">
                                                                @foreach($mealPlan->foods as $food)
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                        {{ $food->name }}
                                                                        <span class="badge bg-primary rounded-pill">
                                                                            {{ $food->pivot->servings }} {{ $food->pivot->servings == 1 ? 'serving' : 'servings' }}
                                                                        </span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                            
                                                            @php
                                                                $nutrition = $mealPlan->calculateNutrition();
                                                            @endphp
                                                            
                                                            <div class="mt-3 p-2 bg-light rounded">
                                                                <div class="d-flex justify-content-between small">
                                                                    <span>Calories: <strong>{{ $nutrition['calories'] }}</strong></span>
                                                                    <span>Protein: <strong>{{ $nutrition['protein'] }}g</strong></span>
                                                                    <span>Carbs: <strong>{{ $nutrition['carbs'] }}g</strong></span>
                                                                    <span>Fat: <strong>{{ $nutrition['fat'] }}g</strong></span>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="mt-2 d-flex justify-content-end">
                                                                <button class="btn btn-sm btn-outline-primary me-2 add-to-log" 
                                                                        data-meal-plan-id="{{ $mealPlan->id }}">
                                                                    <i class="fas fa-plus"></i> Add to Today's Log
                                                                </button>
                                                                <a href="{{ route('meal-plans.edit', $mealPlan->id) }}" class="btn btn-sm btn-outline-secondary">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Food Modal -->
<div class="modal fade" id="logFoodModal" tabindex="-1" aria-labelledby="logFoodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logFoodModalLabel">Log Food</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="logFoodForm" method="POST" action="{{ route('nutrition.food.log') }}" onsubmit="event.preventDefault(); return false;">
                    @csrf
                    <div class="mb-3">
                        <label for="foodSearch" class="form-label">Search Food</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="foodSearch" placeholder="Search for a food..." autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button" id="addCustomFoodBtn">
                                <i class="fas fa-plus"></i> Add Custom Food
                            </button>
                        </div>
                        <div id="foodSearchResults" class="list-group mt-2" style="display: none;">
                            <!-- Search results will be populated here by JavaScript -->
                        </div>
                    </div>

                    <div id="foodDetails" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="servings" class="form-label">Servings</label>
                                <input type="number" class="form-control" id="servings" min="0.1" step="0.1" value="1">
                            </div>
                            <div class="col-md-6">
                                <label for="mealType" class="form-label">Meal Type</label>
                                <select class="form-select" id="mealType">
                                    <option value="breakfast">Breakfast</option>
                                    <option value="lunch">Lunch</option>
                                    <option value="dinner">Dinner</option>
                                    <option value="snack">Snack</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (optional)</label>
                            <input type="text" class="form-control" id="notes" placeholder="e.g. With milk, no sugar">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nutritional Information (per serving)</label>
                            <div class="row g-2">
                                <div class="col-6 col-md-3">
                                    <div class="p-2 bg-light rounded text-center">
                                        <small class="d-block text-muted">Calories</small>
                                        <span id="foodCalories">0</span> kcal
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-2 bg-light rounded text-center">
                                        <small class="d-block text-muted">Protein</small>
                                        <span id="foodProtein">0</span>g
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-2 bg-light rounded text-center">
                                        <small class="d-block text-muted">Carbs</small>
                                        <span id="foodCarbs">0</span>g
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-2 bg-light rounded text-center">
                                        <small class="d-block text-muted">Fat</small>
                                        <span id="foodFat">0</span>g
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="selectedFoodId">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveFoodLogBtn" disabled>Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Custom Food Modal -->
<div class="modal fade" id="addCustomFoodModal" tabindex="-1" aria-labelledby="addCustomFoodModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomFoodModalLabel">Add Custom Food</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="customFoodForm">
                    <div class="mb-3">
                        <label for="foodName" class="form-label">Food Name</label>
                        <input type="text" class="form-control" id="foodName" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="servingSize" class="form-label">Serving Size</label>
                            <input type="text" class="form-control" id="servingSize" placeholder="e.g. 1 cup, 100g" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="calories" class="form-label">Calories</label>
                            <input type="number" class="form-control" id="calories" min="0" step="1" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="protein" class="form-label">Protein (g)</label>
                            <input type="number" class="form-control" id="protein" min="0" step="0.1" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="carbs" class="form-label">Carbs (g)</label>
                            <input type="number" class="form-control" id="carbs" min="0" step="0.1" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="fat" class="form-label">Fat (g)</label>
                            <input type="number" class="form-control" id="fat" min="0" step="0.1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="fiber" class="form-label">Fiber (g)</label>
                        <input type="number" class="form-control" id="fiber" min="0" step="0.1" value="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCustomFoodBtn">Save Food</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this food log entry?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Food search functionality
        const foodSearch = document.getElementById('foodSearch');
        const foodSearchResults = document.getElementById('foodSearchResults');
        const foodDetails = document.getElementById('foodDetails');
        const saveFoodLogBtn = document.getElementById('saveFoodLogBtn');
        let selectedFoodId = null;
        let deleteLogId = null;

        // Debounce function to limit API calls
        const debounce = (func, delay) => {
            let timeoutId;
            return function(...args) {
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }
                timeoutId = setTimeout(() => {
                    func.apply(this, args);
                }, delay);
            };
        };

        // Search for foods
        foodSearch.addEventListener('input', debounce(function(e) {
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                foodSearchResults.style.display = 'none';
                return;
            }
            
            fetch(`/api/nutrition/foods/search?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        foodSearchResults.innerHTML = '';
                        data.forEach(food => {
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.className = 'list-group-item list-group-item-action';
                            item.innerHTML = `
                                <div class="d-flex justify-content-between">
                                    <span>${food.name}</span>
                                    <small class="text-muted">${food.calories} kcal per ${food.serving_size}</small>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>P: ${food.protein}g</span>
                                    <span>C: ${food.carbs}g</span>
                                    <span>F: ${food.fat}g</span>
                                </div>
                            `;
                            item.addEventListener('click', () => selectFood(food));
                            foodSearchResults.appendChild(item);
                        });
                        foodSearchResults.style.display = 'block';
                    } else {
                        foodSearchResults.innerHTML = '<div class="list-group-item">No results found</div>';
                        foodSearchResults.style.display = 'block';
                    }
                });
        }, 300));

        // Select a food from search results
        function selectFood(food) {
            selectedFoodId = food.id;
            document.getElementById('selectedFoodId').value = food.id;
            document.getElementById('foodCalories').textContent = food.calories;
            document.getElementById('foodProtein').textContent = food.protein;
            document.getElementById('foodCarbs').textContent = food.carbs;
            document.getElementById('foodFat').textContent = food.fat;
            
            foodDetails.style.display = 'block';
            saveFoodLogBtn.disabled = false;
            foodSearchResults.style.display = 'none';
        }

        // Save food log
        saveFoodLogBtn.addEventListener('click', function() {
            if (!selectedFoodId) {
                alert('Please select a food first');
                return;
            }
            
            const servings = parseFloat(document.getElementById('servings').value) || 1;
            const mealType = document.getElementById('mealType').value;
            const notes = document.getElementById('notes').value;
            
            // Create a FormData object to submit the form
            const formData = new FormData();
            formData.append('food_id', selectedFoodId);
            formData.append('serving_size', servings);
            formData.append('meal_type', mealType);
            formData.append('consumed_at', new Date().toISOString());
            if (notes) formData.append('notes', notes);
            
            // Get the form element
            const form = document.getElementById('logFoodForm');
            
            // Submit the form using fetch
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Close the modal and refresh the page
                    const modal = bootstrap.Modal.getInstance(document.getElementById('logFoodModal'));
                    modal.hide();
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Unknown error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving food log: ' + (error.message || 'An unknown error occurred'));
            });
        });

        // Delete food log
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-log')) {
                const logId = e.target.closest('.delete-log').getAttribute('data-id');
                deleteLogId = logId;
                const modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
                modal.show();
            }
        });

        // Confirm delete
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (!deleteLogId) return;
            
            fetch(`/api/nutrition/logs/${deleteLogId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`tr[data-log-id="${deleteLogId}"]`).remove();
                    window.location.reload();
                } else {
                    alert('Error deleting food log: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the food log.');
            })
            .finally(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmationModal'));
                modal.hide();
                deleteLogId = null;
            });
        });

        // Add custom food modal
        document.getElementById('addCustomFoodBtn').addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('addCustomFoodModal'));
            modal.show();
        });

        // Save custom food
        document.getElementById('saveCustomFoodBtn').addEventListener('click', function() {
            const foodData = {
                name: document.getElementById('foodName').value,
                calories: parseFloat(document.getElementById('calories').value),
                protein: parseFloat(document.getElementById('protein').value),
                carbs: parseFloat(document.getElementById('carbs').value),
                fat: parseFloat(document.getElementById('fat').value),
                fiber: parseFloat(document.getElementById('fiber').value) || 0,
                serving_size: document.getElementById('servingSize').value
            };

            // Basic validation
            if (!foodData.name || isNaN(foodData.calories) || isNaN(foodData.protein) || 
                isNaN(foodData.carbs) || isNaN(foodData.fat) || !foodData.serving_size) {
                alert('Please fill in all required fields with valid values.');
                return;
            }

            fetch('/api/nutrition/foods', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(foodData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close the modal and reset the form
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addCustomFoodModal'));
                    modal.hide();
                    document.getElementById('customFoodForm').reset();
                    
                    // Update the food search with the new food
                    foodSearch.value = foodData.name;
                    selectFood({
                        id: data.food.id,
                        name: foodData.name,
                        calories: foodData.calories,
                        protein: foodData.protein,
                        carbs: foodData.carbs,
                        fat: foodData.fat,
                        serving_size: foodData.serving_size
                    });
                } else {
                    alert('Error saving custom food: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving the custom food.');
            });
        });

        // Add meal plan to today's log
        document.addEventListener('click', function(e) {
            if (e.target.closest('.add-to-log')) {
                const mealPlanId = e.target.closest('.add-to-log').getAttribute('data-meal-plan-id');
                
                fetch(`/api/nutrition/meal-plans/${mealPlanId}/add-to-log`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error adding to log: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while adding the meal plan to your log.');
                });
            }
        });
    });
</script>
@endpush
