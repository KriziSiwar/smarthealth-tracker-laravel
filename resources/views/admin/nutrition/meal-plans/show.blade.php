@extends('admin.layouts.app')

@section('title', 'Détails du plan de repas : ' . $mealPlan->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Détails du plan : {{ $mealPlan->name }}</h1>
        <div>
            <a href="{{ route('admin.nutrition.meal-plans.edit', $mealPlan) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Modifier
            </a>
            <a href="{{ route('admin.nutrition.meal-plans.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
                    <span class="badge bg-{{ $mealPlan->is_public ? 'success' : 'secondary' }}">
                        {{ $mealPlan->is_public ? 'Public' : 'Privé' }}
                    </span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $mealPlan->name }}</h5>
                    <p class="card-text">{{ $mealPlan->description ?? 'Aucune description fournie.' }}</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">Créé par</h6>
                                    <p class="card-text">{{ $mealPlan->user->name }}</p>
                                    
                                    <h6 class="card-subtitle mb-2 text-muted mt-3">Date de création</h6>
                                    <p class="card-text">{{ $mealPlan->created_at->format('d/m/Y à H:i') }}</p>
                                    
                                    <h6 class="card-subtitle mb-2 text-muted mt-3">Dernière mise à jour</h6>
                                    <p class="card-text">{{ $mealPlan->updated_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-3 text-muted">Valeurs nutritionnelles</h6>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Calories :</span>
                                        <strong>{{ number_format($mealPlan->calories, 0) }} kcal</strong>
                                    </div>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-danger" role="progressbar" 
                                             style="width: {{ min(100, ($mealPlan->calories / 3000) * 100) }}%" 
                                             aria-valuenow="{{ $mealPlan->calories }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="3000">
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Protéines :</span>
                                        <strong>{{ number_format($mealPlan->protein, 1) }}g</strong>
                                    </div>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-primary" role="progressbar" 
                                             style="width: {{ min(100, ($mealPlan->protein / 200) * 100) }}%" 
                                             aria-valuenow="{{ $mealPlan->protein }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="200">
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Glucides :</span>
                                        <strong>{{ number_format($mealPlan->carbohydrates, 1) }}g</strong>
                                    </div>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ min(100, ($mealPlan->carbohydrates / 400) * 100) }}%" 
                                             aria-valuenow="{{ $mealPlan->carbohydrates }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="400">
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Lipides :</span>
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
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Repas du plan</h6>
                </div>
                <div class="card-body">
                    @if($mealPlan->foods->isEmpty())
                        <div class="alert alert-info">
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
                                            <td>{{ number_format($food->calories * ($pivot->serving_size / 100), 0) }} kcal</td>
                                            <td>{{ number_format($food->protein * ($pivot->serving_size / 100), 1) }}g</td>
                                            <td>{{ number_format($food->carbohydrates * ($pivot->serving_size / 100), 1) }}g</td>
                                            <td>{{ number_format($food->fat * ($pivot->serving_size / 100), 1) }}g</td>
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
                    
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addFoodModal">
                        <i class="fas fa-plus me-1"></i> Ajouter un aliment
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions rapides</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.nutrition.meal-plans.edit', $mealPlan) }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-edit me-1"></i> Modifier ce plan
                    </a>
                    
                    <button class="btn btn-success btn-block mb-2" data-bs-toggle="modal" data-bs-target="#duplicateModal">
                        <i class="fas fa-copy me-1"></i> Dupliquer ce plan
                    </button>
                    
                    <form action="{{ route('admin.nutrition.meal-plans.destroy', $mealPlan) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block" 
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce plan de repas ? Cette action est irréversible.')">
                            <i class="fas fa-trash me-1"></i> Supprimer ce plan
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
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
                            <select class="form-select" id="food_id" name="food_id" required>
                                <option value="">Sélectionner un aliment</option>
                                @foreach($foods as $food)
                                    <option value="{{ $food->id }}">
                                        {{ $food->name }} ({{ $food->calories }} kcal/100g)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="serving_size" class="form-label">Portion (g)</label>
                            <input type="number" class="form-control" id="serving_size" name="serving_size" 
                                   value="100" min="1" step="1" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="meal_type" class="form-label">Repas</label>
                            <select class="form-select" id="meal_type" name="meal_type" required>
                                <option value="breakfast">Petit-déjeuner</option>
                                <option value="lunch">Déjeuner</option>
                                <option value="dinner">Dîner</option>
                                <option value="snack">Collation</option>
                            </select>
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
                        <input type="text" class="form-control" id="new_plan_name" name="name" 
                               value="Copie de {{ $mealPlan->name }}" required>
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
        height: 15rem;
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
</script>
@endpush
@endsection
