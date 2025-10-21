@extends('admin.layouts.app')

@section('title', 'Modifier le plan de repas : ' . $mealPlan->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Modifier le plan : {{ $mealPlan->name }}</h1>
        <div>
            <a href="{{ route('admin.nutrition.meal-plans.show', $mealPlan) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour au détail
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.nutrition.meal-plans.update', $mealPlan) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du plan</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $mealPlan->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $mealPlan->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_public" name="is_public" 
                                   value="1" {{ old('is_public', $mealPlan->is_public) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_public">Rendre ce plan public</label>
                            <small class="d-block text-muted">Les plans publics sont visibles par tous les utilisateurs.</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="calories" class="form-label">Calories (kcal)</label>
                                    <input type="number" step="0.1" class="form-control @error('calories') is-invalid @enderror" 
                                           id="calories" name="calories" value="{{ old('calories', $mealPlan->calories) }}">
                                    @error('calories')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="protein" class="form-label">Protéines (g)</label>
                                    <input type="number" step="0.1" class="form-control @error('protein') is-invalid @enderror" 
                                           id="protein" name="protein" value="{{ old('protein', $mealPlan->protein) }}">
                                    @error('protein')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="carbohydrates" class="form-label">Glucides (g)</label>
                                    <input type="number" step="0.1" class="form-control @error('carbohydrates') is-invalid @enderror" 
                                           id="carbohydrates" name="carbohydrates" value="{{ old('carbohydrates', $mealPlan->carbohydrates) }}">
                                    @error('carbohydrates')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fat" class="form-label">Lipides (g)</label>
                                    <input type="number" step="0.1" class="form-control @error('fat') is-invalid @enderror" 
                                           id="fat" name="fat" value="{{ old('fat', $mealPlan->fat) }}">
                                    @error('fat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Enregistrer les modifications
                            </button>
                            
                            <a href="#" class="btn btn-danger" 
                               onclick="event.preventDefault(); if(confirm('Êtes-vous sûr de vouloir supprimer ce plan de repas ?')) { document.getElementById('delete-form').submit(); }">
                                <i class="fas fa-trash me-1"></i> Supprimer
                            </a>
                        </div>
                    </form>
                    
                    <!-- Formulaire de suppression -->
                    <form id="delete-form" action="{{ route('admin.nutrition.meal-plans.destroy', $mealPlan) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Aliments du plan</h6>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addFoodModal">
                        <i class="fas fa-plus me-1"></i> Ajouter un aliment
                    </button>
                </div>
                <div class="card-body">
                    @if($mealPlan->foods->isEmpty())
                        <div class="alert alert-info mb-0">
                            Aucun aliment n'a encore été ajouté à ce plan.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Portion</th>
                                        <th>Repas</th>
                                        <th>Calories</th>
                                        <th>Protéines</th>
                                        <th>Glucides</th>
                                        <th>Lipides</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mealPlan->foods as $food)
                                        @php
                                            $pivot = $food->pivot;
                                            $servingRatio = $pivot->serving_size / 100;
                                        @endphp
                                        <tr>
                                            <td>{{ $food->name }}</td>
                                            <td>{{ $pivot->serving_size }} {{ $pivot->serving_unit }}</td>
                                            <td>
                                                @php
                                                    $mealTypes = [
                                                        'breakfast' => 'Petit-déjeuner',
                                                        'lunch' => 'Déjeuner',
                                                        'dinner' => 'Dîner',
                                                        'snack' => 'Collation'
                                                    ];
                                                @endphp
                                                {{ $mealTypes[$pivot->meal_type] ?? $pivot->meal_type }}
                                            </td>
                                            <td>{{ number_format($food->calories * $servingRatio, 0) }} kcal</td>
                                            <td>{{ number_format($food->protein * $servingRatio, 1) }}g</td>
                                            <td>{{ number_format($food->carbohydrates * $servingRatio, 1) }}g</td>
                                            <td>{{ number_format($food->fat * $servingRatio, 1) }}g</td>
                                            <td>
                                                <form action="{{ route('admin.nutrition.meal-plans.remove-food', [$mealPlan, $food]) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir retirer cet aliment du plan ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Retirer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
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
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Résumé nutritionnel</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="nutritionChart"></canvas>
                        </div>
                        <div class="mt-4 small">
                            <span class="me-3">
                                <i class="fas fa-circle text-primary"></i> Protéines
                            </span>
                            <span class="me-3">
                                <i class="fas fa-circle text-success"></i> Glucides
                            </span>
                            <span>
                                <i class="fas fa-circle text-warning"></i> Lipides
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Calories totales</span>
                            <strong>{{ number_format($mealPlan->calories, 0) }} kcal</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" role="progressbar" 
                                 style="width: {{ min(100, ($mealPlan->calories / 3000) * 100) }}%" 
                                 aria-valuenow="{{ $mealPlan->calories }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="3000">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Protéines</span>
                            <strong>{{ number_format($mealPlan->protein, 1) }}g</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ min(100, ($mealPlan->protein / 200) * 100) }}%" 
                                 aria-valuenow="{{ $mealPlan->protein }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="200">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Glucides</span>
                            <strong>{{ number_format($mealPlan->carbohydrates, 1) }}g</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ min(100, ($mealPlan->carbohydrates / 400) * 100) }}%" 
                                 aria-valuenow="{{ $mealPlan->carbohydrates }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="400">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Lipides</span>
                            <strong>{{ number_format($mealPlan->fat, 1) }}g</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" 
                                 style="width: {{ min(100, ($mealPlan->fat / 100) * 100) }}%" 
                                 aria-valuenow="{{ $mealPlan->fat }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions rapides</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.nutrition.meal-plans.show', $mealPlan) }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-eye me-1"></i> Voir le détail
                    </a>
                    
                    <button class="btn btn-success btn-block mb-2" data-bs-toggle="modal" data-bs-target="#duplicateModal">
                        <i class="fas fa-copy me-1"></i> Dupliquer ce plan
                    </button>
                    
                    <form action="{{ route('admin.nutrition.meal-plans.destroy', $mealPlan) }}" method="POST" class="d-inline w-100">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block" 
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce plan de repas ? Cette action est irréversible.')">
                            <i class="fas fa-trash me-1"></i> Supprimer ce plan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter un aliment -->
<div class="modal fade" id="addFoodModal" tabindex="-1" aria-labelledby="addFoodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFoodModalLabel">Ajouter un aliment au plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="{{ route('admin.nutrition.meal-plans.add-food', $mealPlan) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="food_id" class="form-label">Aliment</label>
                            <select class="form-select @error('food_id') is-invalid @enderror" id="food_id" name="food_id" required>
                                <option value="">Sélectionner un aliment</option>
                                @foreach($foods as $food)
                                    <option value="{{ $food->id }}" {{ old('food_id') == $food->id ? 'selected' : '' }}>
                                        {{ $food->name }} ({{ $food->calories }} kcal/100g)
                                    </option>
                                @endforeach
                            </select>
                            @error('food_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="serving_size" class="form-label">Portion (g)</label>
                            <input type="number" class="form-control @error('serving_size') is-invalid @enderror" 
                                   id="serving_size" name="serving_size" value="{{ old('serving_size', 100) }}" 
                                   min="1" step="1" required>
                            @error('serving_size')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="meal_type" class="form-label">Repas</label>
                            <select class="form-select @error('meal_type') is-invalid @enderror" id="meal_type" name="meal_type" required>
                                <option value="breakfast" {{ old('meal_type') == 'breakfast' ? 'selected' : '' }}>Petit-déjeuner</option>
                                <option value="lunch" {{ old('meal_type') == 'lunch' ? 'selected' : '' }}>Déjeuner</option>
                                <option value="dinner" {{ old('meal_type') == 'dinner' ? 'selected' : '' }}>Dîner</option>
                                <option value="snack" {{ old('meal_type') == 'snack' ? 'selected' : '' }}>Collation</option>
                            </select>
                            @error('meal_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> 
                        Les valeurs nutritionnelles seront automatiquement calculées en fonction de la portion spécifiée.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter l'aliment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Dupliquer le plan -->
<div class="modal fade" id="duplicateModal" tabindex="-1" aria-labelledby="duplicateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="duplicateModalLabel">Dupliquer le plan de repas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="{{ route('admin.nutrition.meal-plans.duplicate', $mealPlan) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_plan_name" class="form-label">Nouveau nom du plan</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="new_plan_name" name="name" 
                               value="{{ old('name', 'Copie de ' . $mealPlan->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="include_foods" name="include_foods" checked>
                        <label class="form-check-label" for="include_foods">
                            Inclure les aliments du plan
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Dupliquer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .chart-pie {
        position: relative;
        height: 10rem;
        width: 100%;
    }
    
    @media (min-width: 768px) {
        .chart-pie {
            height: 10rem;
        }
    }
    
    .progress {
        height: 0.5rem;
    }
    
    .card {
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart.js pour le graphique de répartition des macronutriments
    document.addEventListener('DOMContentLoaded', function() {
        // Récupérer les données des macronutriments
        const protein = {{ $mealPlan->protein * 4 }}; // 4 kcal/g
        const carbs = {{ $mealPlan->carbohydrates * 4 }}; // 4 kcal/g
        const fat = {{ $mealPlan->fat * 9 }}; // 9 kcal/g
        const total = protein + carbs + fat;
        
        // Calculer les pourcentages
        const proteinPercent = total > 0 ? Math.round((protein / total) * 100) : 0;
        const carbsPercent = total > 0 ? Math.round((carbs / total) * 100) : 0;
        const fatPercent = total > 0 ? Math.round((fat / total) * 100) : 0;
        
        // Créer le graphique
        const ctx = document.getElementById('nutritionChart').getContext('2d');
        const myPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Protéines', 'Glucides', 'Lipides'],
                datasets: [{
                    data: [proteinPercent, carbsPercent, fatPercent],
                    backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#dda20a'],
                    hoverBorderColor: 'rgba(234, 236, 244, 1)',
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw}%`;
                            }
                        }
                    }
                },
                cutout: '80%',
            },
        });
    });
    
    // Gestion des erreurs de formulaire
    @if($errors->has('food_id') || $errors->has('serving_size') || $errors->has('meal_type'))
        document.addEventListener('DOMContentLoaded', function() {
            var addFoodModal = new bootstrap.Modal(document.getElementById('addFoodModal'));
            addFoodModal.show();
        });
    @endif
    
    @if($errors->has('name') && !$errors->has('food_id') && !$errors->has('serving_size') && !$errors->has('meal_type'))
        document.addEventListener('DOMContentLoaded', function() {
            var duplicateModal = new bootstrap.Modal(document.getElementById('duplicateModal'));
            duplicateModal.show();
        });
    @endif
</script>
@endpush
@endsection
