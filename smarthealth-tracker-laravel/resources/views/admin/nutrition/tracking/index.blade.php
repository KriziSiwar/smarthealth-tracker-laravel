@extends('admin.layouts.app')

@section('title', 'Tableau de bord nutritionnel')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css" rel="stylesheet">
<style>
    .card-counter {
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        margin: 5px;
        padding: 20px 10px;
        border-radius: 5px;
        transition: .3s linear all;
    }
    .card-counter.primary {
        background: linear-gradient(45deg, #4e73df, #224abe);
        color: #FFF;
    }
    .card-counter.success {
        background: linear-gradient(45deg, #1cc88a, #13855c);
        color: #FFF;
    }
    .card-counter.warning {
        background: linear-gradient(45deg, #f6c23e, #dda20a);
        color: #FFF;
    }
    .card-counter.danger {
        background: linear-gradient(45deg, #e74a3b, #be2617);
        color: #FFF;
    }
    .card-counter i {
        font-size: 2.5rem;
        opacity: 0.3;
    }
    .card-counter .count-numbers {
        font-size: 2rem;
        font-weight: 700;
    }
    .card-counter .count-name {
        font-size: 1rem;
        opacity: 0.8;
        text-transform: capitalize;
    }
    .progress {
        height: 10px;
        border-radius: 5px;
    }
    .progress-bar {
        border-radius: 5px;
    }
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    .badge-meal {
        font-size: 0.7rem;
        padding: 0.35em 0.65em;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tableau de bord nutritionnel</h1>
    
    <!-- Cartes de statistiques -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-counter primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="count-numbers">{{ number_format($stats['total_users']) }}</span>
                        <span class="count-name">Utilisateurs</span>
                    </div>
                    <i class="fas fa-users"></i>
                </div>
                <div class="mt-2">
                    <small>{{ $stats['active_users'] }} actifs ce mois-ci</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-counter success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="count-numbers">{{ number_format($stats['meals_logged']) }}</span>
                        <span class="count-name">Repas enregistrés</span>
                    </div>
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="mt-2">
                    <small>30 derniers jours</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-counter warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="count-numbers">{{ $stats['active_plans'] }}</span>
                        <span class="count-name">Plans actifs</span>
                    </div>
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="mt-2">
                    <small>{{ round($stats['avg_calories']) }} kcal/repas en moyenne</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-counter danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="count-numbers">{{ round(($stats['user_goals']['calories_goal']['achieved'] / $stats['user_goals']['calories_goal']['met']) * 100) }}%</span>
                        <span class="count-name">Objectifs calories</span>
                    </div>
                    <i class="fas fa-bullseye"></i>
                </div>
                <div class="mt-2">
                    <small>{{ round($stats['user_goals']['calories_goal']['achieved']) }}/{{ round($stats['user_goals']['calories_goal']['met']) }} kcal</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques principaux -->
    <div class="row">
        <!-- Graphique des calories -->
        <div class="col-xl-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Tendance des calories (30 derniers jours)</h6>
                    <div class="dropdown no-arrow
                    ">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" href="#">Exporter</a></li>
                            <li><a class="dropdown-item" href="#">Détails</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="caloriesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition des macronutriments -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition des macronutriments</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="macronutrientsChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="me-3">
                            <i class="fas fa-circle text-primary"></i> Protéines ({{ $chartData['macros']['protein'] }}%)
                        </span>
                        <span class="me-3">
                            <i class="fas fa-circle text-success"></i> Glucides ({{ $chartData['macros']['carbs'] }}%)
                        </span>
                        <span>
                            <i class="fas fa-circle text-info"></i> Lipides ({{ $chartData['macros']['fat'] }}%)
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Derniers utilisateurs actifs -->
    <div class="row
    ">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Derniers utilisateurs actifs</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Dernière activité</th>
                                    <th>Apport calorique</th>
                                    <th>Macros</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}" 
                                                 class="user-avatar me-2" 
                                                 alt="{{ $user->name }}">
                                            <div>
                                                <div class="fw-bold">{{ $user->name }}</div>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Jamais' }}</td>
                                    <td>
                                        @php
                                            $calories = $user->foodLogs->first() ? $user->foodLogs->first()->total_calories : 0;
                                            $goal = 2000; // Valeur par défaut
                                            $percentage = min(100, ($calories / $goal) * 100);
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $percentage > 90 ? 'danger' : ($percentage > 70 ? 'warning' : 'success') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $percentage }}%" 
                                                 aria-valuenow="{{ $calories }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="{{ $goal }}">
                                                {{ round($calories) }}
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ round($percentage) }}% de l'objectif</small>
                                    </td>
                                    <td>
                                        @if($user->foodLogs->first())
                                            <span class="badge bg-primary">P: {{ round($user->foodLogs->first()->avg_protein) }}g</span>
                                            <span class="badge bg-success">G: {{ round($user->foodLogs->first()->avg_carbs) }}g</span>
                                            <span class="badge bg-info">L: {{ round($user->foodLogs->first()->avg_fat) }}g</span>
                                        @else
                                            <span class="text-muted">Aucune donnée</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Aucun utilisateur récent</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aliments les plus consommés -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Aliments les plus consommés</h6>
                    <span class="badge bg-primary">30 derniers jours</span>
                </div>
                <div class="card-body">
                    @if(count($stats['top_foods']) > 0)
                        @foreach($stats['top_foods'] as $food)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $food->food_name }}</span>
                                <span class="text-muted">{{ $food->consumption_count }} {{ Str::plural('fois', $food->consumption_count) }}</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                @php
                                    $max = $stats['top_foods']->first()->consumption_count;
                                    $width = ($food->consumption_count / $max) * 100;
                                @endphp
                                <div class="progress-bar bg-{{ ['primary', 'success', 'info', 'warning', 'danger'][$loop->index % 5] }}" 
                                     role="progressbar" 
                                     style="width: {{ $width }}%" 
                                     aria-valuenow="{{ $food->consumption_count }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="{{ $max }}">
                                </div>
                            </div>
                            <small class="text-muted">Moyenne: {{ round($food->avg_calories) }} kcal</small>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun aliment enregistré récemment</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Répartition des repas -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition des repas</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="mealTimeChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($chartData['meal_times'] as $meal => $count)
                            <span class="me-3">
                                <i class="fas fa-circle" style="color: {{ ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b'][$loop->index] }}"></i> 
                                {{ $meal }} ({{ $count }})
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
    // Graphique des calories
    const caloriesCtx = document.getElementById('caloriesChart').getContext('2d');
    const caloriesChart = new Chart(caloriesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($chartData['calories'], 'date')) !!},
            datasets: [{
                label: 'Calories consommées',
                data: {!! json_encode(array_column($chartData['calories'], 'calories')) !!},
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointRadius: 3,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: 'rgba(78, 115, 223, 1)',
                pointHoverRadius: 3,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: { size: 14 },
                    bodyFont: { size: 14 },
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return `${context.parsed.y.toLocaleString()} kcal`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7,
                        maxRotation: 0,
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' kcal';
                        },
                        maxTicksLimit: 5
                    }
                }
            }
        }
    });

    // Graphique des macronutriments
    const macrosCtx = document.getElementById('macronutrientsChart').getContext('2d');
    const macrosChart = new Chart(macrosCtx, {
        type: 'doughnut',
        data: {
            labels: ['Protéines', 'Glucides', 'Lipides'],
            datasets: [{
                data: [
                    {{ $chartData['macros']['protein'] }}, 
                    {{ $chartData['macros']['carbs'] }}, 
                    {{ $chartData['macros']['fat'] }}
                ],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                hoverBorderColor: 'rgba(234, 236, 244, 1)',
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    bodyFont: { size: 14 },
                    borderWidth: 1,
                    borderColor: '#dddfeb',
                    padding: 15,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.raw}%`;
                        }
                    }
                }
            },
            cutout: '70%',
        },
    });

    // Graphique des repas par moment de la journée
    const mealTimeCtx = document.getElementById('mealTimeChart').getContext('2d');
    const mealTimeChart = new Chart(mealTimeCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($chartData['meal_times'])) !!},
            datasets: [{
                data: {!! json_encode(array_values($chartData['meal_times'])) !!},
                backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#dda20a', '#be2617'],
                hoverBorderColor: 'rgba(234, 236, 244, 1)',
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    bodyFont: { size: 14 },
                    borderWidth: 1,
                    borderColor: '#dddfeb',
                    padding: 15,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} repas (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '70%',
        },
    });

    // Redimensionner les graphiques lors du redimensionnement de la fenêtre
    window.addEventListener('resize', function() {
        caloriesChart.resize();
        macrosChart.resize();
        mealTimeChart.resize();
    });
</script>
    // Scripts pour les graphiques et le suivi
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page de suivi nutritionnel chargée');
    });
</script>
@endpush
