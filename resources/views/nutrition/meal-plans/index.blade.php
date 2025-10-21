@extends('layouts.app')

@section('content')
<div style="padding-top: 110px;"> {{-- Wrapper avec plus d'espace (augmenté à 110px) pour éviter définitivement le masquage par la navbar fixe --}}
<div class="container-fluid mt-4">
    <!-- En-tête avec métriques globales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-primary text-white shadow-lg">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <i class="fas fa-utensils fa-2x me-3"></i>
                        <div>
                            <h3 class="mb-0">{{ $mealPlans->count() }}</h3>
                            <small>Plans de Repas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-success text-white shadow-lg">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <i class="fas fa-fire fa-2x me-3"></i>
                        <div>
                            <h3 class="mb-0">{{ $mealPlans->sum('calories') }}</h3>
                            <small>Total Calories</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-info text-white shadow-lg">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <i class="fas fa-dumbbell fa-2x me-3"></i>
                        <div>
                            <h3 class="mb-0">{{ round($mealPlans->avg('protein'), 1) }}g</h3>
                            <small>Protéines Moy.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-warning text-white shadow-lg">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <i class="fas fa-plus-circle fa-2x me-3"></i>
                        <div>
                            <a href="{{ route('nutrition.meal-plans.create') }}" class="text-white text-decoration-none">
                                <h5 class="mb-0">Nouveau Plan</h5>
                                <small>Créer maintenant</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" id="searchInput" class="form-control" placeholder="Rechercher par nom..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select id="calorieFilter" class="form-select">
                                <option value="">Toutes les calories</option>
                                <option value="low">Moins de 500 kcal</option>
                                <option value="medium">500-1000 kcal</option>
                                <option value="high">Plus de 1000 kcal</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="mealTypeFilter" class="form-select">
                                <option value="">Tous les types</option>
                                <option value="public">Plans publics</option>
                                <option value="private">Mes plans</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button id="applyFilters" class="btn btn-primary w-100">Filtrer</button>
                        </div>
                        <div class="col-md-2">
                            <button id="resetFilters" class="btn btn-outline-secondary w-100">Réinitialiser</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique récapitulatif -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Répartition des Calories</h6>
                </div>
                <div class="card-body">
                    <canvas id="calorieDistributionChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des plans de repas -->
    <div class="row" id="mealPlansContainer">
        @if($mealPlans->isEmpty())
            <div class="col-12">
                <div class="card shadow-sm text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-utensils fa-4x text-muted mb-4"></i>
                        <h5 class="text-muted">Aucun plan de repas trouvé</h5>
                        <p class="text-muted mb-4">Créez votre premier plan pour commencer votre suivi nutritionnel.</p>
                        <a href="{{ route('nutrition.meal-plans.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Créer un Plan de Repas
                        </a>
                    </div>
                </div>
            </div>
        @else
            @foreach($mealPlans as $mealPlan)
                <div class="col-md-6 col-lg-4 mb-4 meal-plan-card" data-calories="{{ $mealPlan->calories }}" data-public="{{ $mealPlan->is_public ? 'public' : 'private' }}">
                    <div class="card h-100 shadow-sm hover-card">
                        <div class="card-header bg-gradient-{{ $mealPlan->calories > 1000 ? 'danger' : ($mealPlan->calories > 500 ? 'warning' : 'success') }} text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-utensils me-2"></i>{{ $mealPlan->name }}</h6>
                            @if($mealPlan->is_public)
                                <span class="badge bg-light text-dark"><i class="fas fa-globe me-1"></i>Public</span>
                            @else
                                <span class="badge bg-dark"><i class="fas fa-lock me-1"></i>Privé</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">{{ Str::limit($mealPlan->description, 100) }}</p>
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="metric-small">
                                        <i class="fas fa-fire text-danger"></i>
                                        <div class="fw-bold">{{ $mealPlan->calories }}</div>
                                        <small>kcal</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="metric-small">
                                        <i class="fas fa-dumbbell text-primary"></i>
                                        <div class="fw-bold">{{ number_format($mealPlan->protein, 1) }}g</div>
                                        <small>Prot.</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="metric-small">
                                        <i class="fas fa-bread-slice text-success"></i>
                                        <div class="fw-bold">{{ $mealPlan->foods->count() }}</div>
                                        <small>Aliments</small>
                                    </div>
                                </div>
                            </div>
                            <div class="progress mb-3" style="height: 8px;">
                                <div class="progress-bar bg-{{ $mealPlan->calories > 1000 ? 'danger' : ($mealPlan->calories > 500 ? 'warning' : 'success') }}" style="width: {{ min(($mealPlan->calories / 1500) * 100, 100) }}%;"></div>
                            </div>
                        </div>
                        <div class="card-footer bg-light d-flex justify-content-between">
                            <a href="{{ route('nutrition.meal-plans.show', $mealPlan) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Voir
                            </a>
                            <div class="btn-group">
                                <a href="{{ route('nutrition.meal-plans.edit', $mealPlan) }}" class="btn btn-outline-secondary btn-sm" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('nutrition.meal-plans.destroy', $mealPlan) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Pagination -->
    @if($mealPlans->hasPages())
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $mealPlans->links() }}
            </div>
        </div>
    @endif
</div>
</div>
@endsection

@push('styles')
<style>
    .bg-gradient-primary { background: linear-gradient(45deg, #007bff, #0056b3); }
    .bg-gradient-success { background: linear-gradient(45deg, #28a745, #20c997); }
    .bg-gradient-info { background: linear-gradient(45deg, #17a2b8, #007bff); }
    .bg-gradient-warning { background: linear-gradient(45deg, #ffc107, #fd7e14); }
    .bg-gradient-danger { background: linear-gradient(45deg, #dc3545, #c82333); }
    .hover-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .metric-small {
        font-size: 0.9rem;
    }
    .metric-small i {
        margin-bottom: 0.5rem;
        opacity: 0.7;
    }
    .card-header {
        border-bottom: none;
    }
    .card-footer {
        border-top: 1px solid rgba(0,0,0,0.125);
    }
    #searchInput:focus, .form-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique de répartition des calories
    const ctx = document.getElementById('calorieDistributionChart').getContext('2d');
    const calorieData = {
        low: {{ $mealPlans->where('calories', '<', 500)->count() }},
        medium: {{ $mealPlans->whereBetween('calories', [500, 1000])->count() }},
        high: {{ $mealPlans->where('calories', '>', 1000)->count() }}
    };
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Faible (<500)', 'Moyen (500-1000)', 'Élevé (>1000)'],
            datasets: [{
                data: [calorieData.low, calorieData.medium, calorieData.high],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Filtres dynamiques
    const searchInput = document.getElementById('searchInput');
    const calorieFilter = document.getElementById('calorieFilter');
    const mealTypeFilter = document.getElementById('mealTypeFilter');
    const applyFilters = document.getElementById('applyFilters');
    const resetFilters = document.getElementById('resetFilters');
    const mealPlansContainer = document.getElementById('mealPlansContainer');

    function filterMealPlans() {
        const searchTerm = searchInput.value.toLowerCase();
        const calorieRange = calorieFilter.value;
        const mealType = mealTypeFilter.value;
        const cards = mealPlansContainer.querySelectorAll('.meal-plan-card');

        cards.forEach(card => {
            const name = card.querySelector('.card-header h6').textContent.toLowerCase();
            const calories = parseInt(card.dataset.calories);
            const isPublic = card.dataset.public;
            let show = true;

            if (searchTerm && !name.includes(searchTerm)) show = false;
            if (calorieRange === 'low' && calories >= 500) show = false;
            if (calorieRange === 'medium' && (calories < 500 || calories > 1000)) show = false;
            if (calorieRange === 'high' && calories <= 1000) show = false;
            if (mealType === 'public' && isPublic !== 'public') show = false;
            if (mealType === 'private' && isPublic !== 'private') show = false;

            card.style.display = show ? 'block' : 'none';
        });
    }

    applyFilters.addEventListener('click', filterMealPlans);
    resetFilters.addEventListener('click', () => {
        searchInput.value = '';
        calorieFilter.value = '';
        mealTypeFilter.value = '';
        filterMealPlans();
    });

    // Recherche en temps réel
    searchInput.addEventListener('input', filterMealPlans);

    // Animation d'apparition des cartes
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.meal-plan-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s, transform 0.5s';
        observer.observe(card);
    });
});
</script>
@endpush