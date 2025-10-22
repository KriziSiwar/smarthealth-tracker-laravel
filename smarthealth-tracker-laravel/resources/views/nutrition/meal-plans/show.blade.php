@extends('layouts.app')

@section('content')
<div class="container-fluid mt-5 pt-4"> {{-- Ajout de pt-4 pour plus d'espace en haut --}}
    <div class="row">
        <!-- Sidebar avec résumé -->
        <div class="col-md-4">
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Résumé Nutritionnel</h5>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-6">
                            <div class="metric-circle mb-3">
                                <canvas id="caloriesChart" width="80" height="80"></canvas>
                                <div class="metric-value">{{ $mealPlan->calories }}</div>
                                <div class="metric-label">kcal</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="metric-circle mb-3">
                                <canvas id="proteinChart" width="80" height="80"></canvas>
                                <div class="metric-value">{{ number_format($mealPlan->protein, 1) }}</div>
                                <div class="metric-label">g Prot.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="metric-circle">
                                <canvas id="carbsChart" width="80" height="80"></canvas>
                                <div class="metric-value">{{ number_format($mealPlan->carbohydrates, 1) }}</div>
                                <div class="metric-label">g Gluc.</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="metric-circle">
                                <canvas id="fatChart" width="80" height="80"></canvas>
                                <div class="metric-value">{{ number_format($mealPlan->fat, 1) }}</div>
                                <div class="metric-label">g Lip.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Actions Rapides</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addToLogModal">
                            <i class="fas fa-plus me-2"></i>Ajouter au Journal
                        </button>
                        <a href="{{ route('nutrition.meal-plans.edit', $mealPlan) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Modifier le Plan
                        </button>
                        <button type="button" class="btn btn-info" onclick="shareMealPlan()">
                            <i class="fas fa-share me-2"></i>Partager
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Zone principale -->
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-gradient-info text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0"><i class="fas fa-utensils me-2"></i>{{ $mealPlan->name }}</h4>
                        <p class="mb-0 opacity-75">Créé par {{ $mealPlan->user->name }} le {{ $mealPlan->created_at->format('d/m/Y') }}</p>
                    </div>
                    <form action="{{ route('nutrition.meal-plans.toggle-public', $mealPlan) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn {{ $mealPlan->is_public ? 'btn-success' : 'btn-outline-light' }} btn-sm">
                            <i class="fas {{ $mealPlan->is_public ? 'fa-eye' : 'fa-eye-slash' }} me-1"></i>
                            {{ $mealPlan->is_public ? 'Public' : 'Privé' }}
                        </button>
                    </form>
                </div>

                <div class="card-body">
                    @if($mealPlan->description)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>{{ $mealPlan->description }}
                        </div>
                    @endif

                    <!-- Graphique des repas -->
                    <div class="mb-4">
                        <h5 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Répartition par Repas</h5>
                        <canvas id="mealDistributionChart" height="300"></canvas>
                    </div>

                    <!-- Liste des repas -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3"><i class="fas fa-list-ul me-2"></i>Détail des Repas</h5>
                            <div class="accordion" id="mealsAccordion">
                                @foreach(['breakfast' => 'Petit-déjeuner', 'lunch' => 'Déjeuner', 'dinner' => 'Dîner', 'snack' => 'Collation'] as $mealType => $mealLabel)
                                    @if($meals[$mealType]->isNotEmpty())
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading{{ ucfirst($mealType) }}">
                                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ ucfirst($mealType) }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ ucfirst($mealType) }}">
                                                    <div class="d-flex justify-content-between w-100 me-3">
                                                        <span><i class="fas fa-coffee me-2"></i>{{ $mealLabel }}</span>
                                                        <span class="badge bg-primary">{{ $meals[$mealType]->count() }} aliments</span>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse{{ ucfirst($mealType) }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ ucfirst($mealType) }}" data-bs-parent="#mealsAccordion">
                                                <div class="accordion-body p-0">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Aliment</th>
                                                                    <th class="text-end">Portion</th>
                                                                    <th class="text-end">Calories</th>
                                                                    <th class="text-end">Protéines</th>
                                                                    <th class="text-end">Glucides</th>
                                                                    <th class="text-end">Lipides</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $mealCalories = 0;
                                                                    $mealProtein = 0;
                                                                    $mealCarbs = 0;
                                                                    $mealFat = 0;
                                                                @endphp
                                                                @foreach($meals[$mealType] as $food)
                                                                    @php
                                                                        $ratio = $food->pivot_serving_size / $food->serving_size;
                                                                        $calories = $food->calories * $ratio;
                                                                        $protein = $food->protein * $ratio;
                                                                        $carbs = $food->carbohydrates * $ratio;
                                                                        $fat = $food->fat * $ratio;
                                                                        
                                                                        $mealCalories += $calories;
                                                                        $mealProtein += $protein;
                                                                        $mealCarbs += $carbs;
                                                                        $mealFat += $fat;
                                                                    @endphp
                                                                    <tr class="food-row" data-bs-toggle="tooltip" title="{{ $food->name }} - {{ $food->description ?? 'Aucune description' }}">
                                                                        <td>
                                                                            <div class="d-flex align-items-center">
                                                                                <img src="{{ asset('img/food/' . ($food->image ?? 'default-food.png')) }}" alt="{{ $food->name }}" class="food-image me-2">
                                                                                <div>
                                                                                    <div class="fw-bold">{{ $food->name }}</div>
                                                                                    <small class="text-muted">{{ $food->pivot_serving_size }} {{ $food->pivot_serving_unit }}</small>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-end align-middle">{{ number_format($calories, 0) }}</td>
                                                                        <td class="text-end align-middle">{{ number_format($protein, 1) }}g</td>
                                                                        <td class="text-end align-middle">{{ number_format($carbs, 1) }}g</td>
                                                                        <td class="text-end align-middle">{{ number_format($fat, 1) }}g</td>
                                                                    </tr>
                                                                @endforeach
                                                                <tr class="table-active fw-bold">
                                                                    <td>Total {{ $mealLabel }}</td>
                                                                    <td class="text-end">{{ number_format($mealCalories, 0) }}</td>
                                                                    <td class="text-end">{{ number_format($mealProtein, 1) }}g</td>
                                                                    <td class="text-end">{{ number_format($mealCarbs, 1) }}g</td>
                                                                    <td class="text-end">{{ number_format($mealFat, 1) }}g</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter au journal -->
<div class="modal fade" id="addToLogModal" tabindex="-1" aria-labelledby="addToLogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addToLogModalLabel"><i class="fas fa-plus me-2"></i>Ajouter à mon Journal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="{{ route('nutrition.meal-plans.add-to-log', $mealPlan) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="meal_type" class="form-label">Type de repas</label>
                        <select class="form-select" id="meal_type" name="meal_type" required>
                            <option value="breakfast">Petit-déjeuner</option>
                            <option value="lunch">Déjeuner</option>
                            <option value="dinner">Dîner</option>
                            <option value="snack">Collation</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-gradient-success { background: linear-gradient(45deg, #28a745, #20c997); }
    .bg-gradient-info { background: linear-gradient(45deg, #17a2b8, #007bff); }
    .metric-circle {
        position: relative;
        margin: 0 auto 1rem;
    }
    .metric-circle canvas {
        position: absolute;
        top: 0;
        left: 0;
    }
    .metric-value {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.2rem;
        font-weight: bold;
        color: #495057;
    }
    .metric-label {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .food-image {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #dee2e6;
    }
    .food-row {
        transition: background-color 0.2s;
    }
    .food-row:hover {
        background-color: #f8f9fa;
    }
    .accordion-button:not(.collapsed) {
        background-color: rgba(23, 162, 184, 0.1);
        color: #17a2b8;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphiques circulaires pour les métriques
    ['calories', 'protein', 'carbs', 'fat'].forEach(metric => {
        const ctx = document.getElementById(metric + 'Chart').getContext('2d');
        const value = metric === 'calories' ? {{ $mealPlan->calories }} :
                     metric === 'protein' ? {{ $mealPlan->protein }} :
                     metric === 'carbs' ? {{ $mealPlan->carbohydrates }} :
                     {{ $mealPlan->fat }};
        const max = metric === 'calories' ? 2500 : 200; // Valeurs max approximatives
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [value, max - value],
                    backgroundColor: ['#28a745', '#e9ecef'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    });

    // Graphique de répartition par repas
    const mealCtx = document.getElementById('mealDistributionChart').getContext('2d');
    const mealData = [
        @foreach(['breakfast', 'lunch', 'dinner', 'snack'] as $mealType)
            @if($meals[$mealType]->isNotEmpty())
                { label: '{{ ucfirst($mealType) }}', value: {{ $meals[$mealType]->sum(function($food) { return $food->calories * ($food->pivot_serving_size / $food->serving_size); }) }} },
            @endif
        @endforeach
    ];
    new Chart(mealCtx, {
        type: 'bar',
        data: {
            labels: mealData.map(d => d.label),
            datasets: [{
                label: 'Calories',
                data: mealData.map(d => d.value),
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545'],
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Tooltips pour les lignes d'aliments
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Fonction de partage (exemple)
    window.shareMealPlan = function() {
        if (navigator.share) {
            navigator.share({
                title: '{{ $mealPlan->name }}',
                text: 'Découvrez ce plan de repas !',
                url: window.location.href
            });
        } else {
            // Fallback : copie du lien
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Lien copié dans le presse-papiers !');
            });
        }
    };
});
</script>
@endpush
