{{-- resources/views/health-metrics/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Tableau de Bord - Mon App Santé')

@section('content')
<!-- ======= Dernières Métriques Section ======= -->
<section id="latest-metrics" class="latest-metrics section">
    <div class="container" data-aos="fade-up">

        <div class="section-header">
            <h2>Dernières Métriques</h2>
            <p>Votre suivi santé des 7 derniers jours</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- ======= Scrolling Metrics Card ======= -->
                <div class="php-email-form">
                    <div class="metrics-header d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">Historique Hebdomadaire</h3>
                        <a href="{{ route('health-metrics.create') }}" class="btn-getstarted">
                            <i class="bi bi-plus-circle me-2"></i>Nouvelle Métrique
                        </a>
                    </div>

                    <div class="scrolling-card-container">
                        @forelse($weeklyMetrics as $metric)
                        <div class="metric-scroll-item">
                            <div class="metric-scroll-content">
                                <div class="metric-scroll-date">
                                    <i class="bi bi-calendar3 me-2"></i>
                                    <strong>{{ $metric->measured_at->format('d/m/Y') }}</strong>
                                </div>
                                <div class="metric-scroll-details">
                                    <div class="metric-scroll-info">
                                        <div class="metric-scroll-icon">
                                            <i class="bi bi-speedometer2"></i>
                                        </div>
                                        <div class="metric-scroll-text">
                                            <small>Poids</small>
                                            <div class="metric-value">{{ $metric->weight_kg }} kg</div>
                                        </div>
                                    </div>
                                    <div class="metric-scroll-info">
                                        <div class="metric-scroll-icon">
                                            <i class="bi bi-clock"></i>
                                        </div>
                                        <div class="metric-scroll-text">
                                            <small>Mesure</small>
                                            <div class="metric-value">{{ $metric->measurement }}</div>
                                        </div>
                                    </div>
                                    <div class="metric-scroll-info">
                                        <div class="metric-scroll-icon">
                                            <i class="bi bi-droplet"></i>
                                        </div>
                                        <div class="metric-scroll-text">
                                            <small>Eau bue</small>
                                            <div class="metric-value">{{ $metric->waterIntakes->sum('amount_ml') }} ml</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="metric-scroll-actions">
                                    <a href="{{ route('health-metrics.edit', $metric->id) }}" class="btn-action btn-edit" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('health-metrics.destroy', $metric->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete" title="Supprimer" onclick="return confirm('Supprimer cette métrique ?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('waterintakes.create') }}" class="btn-action btn-water" title="Ajouter de l'eau">
                                        <i class="bi bi-plus-lg"></i>
                                    </a>
                                    <a href="{{ route('waterintakes.index') }}" class="btn-action btn-view" title="Voir l'hydratation">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state text-center py-5">
                            <i class="bi bi-clipboard2-x display-1 text-muted mb-3"></i>
                            <h4 class="text-muted">Aucune métrique enregistrée</h4>
                            <p class="text-muted mb-4">Commencez votre suivi santé dès maintenant</p>
                            <a href="{{ route('health-metrics.create') }}" class="btn-getstarted">
                                <i class="bi bi-plus-circle me-2"></i>Créer ma Première Métrique
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div><!-- End Scrolling Metrics Card -->

            </div>
        </div>

    </div>
</section><!-- End Latest Metrics Section -->
@endsection

@section('styles')
<style>
.latest-metrics.section {
    padding: 60px 0;
    background: #f8f9fa;
}

.php-email-form {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
}

.metrics-header {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 20px;
}

.metrics-header h3 {
    color: #2c4964;
    font-weight: 700;
}

/* Scrolling Card Container */
.scrolling-card-container {
    max-height: 500px;
    overflow-y: auto;
    padding-right: 10px;
}

/* Custom Scrollbar */
.scrolling-card-container::-webkit-scrollbar {
    width: 6px;
}

.scrolling-card-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.scrolling-card-container::-webkit-scrollbar-thumb {
    background: #1977cc;
    border-radius: 10px;
}

.scrolling-card-container::-webkit-scrollbar-thumb:hover {
    background: #1c84e3;
}

/* Metric Scroll Item */
.metric-scroll-item {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.metric-scroll-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    border-color: #1977cc;
}

.metric-scroll-item:last-child {
    margin-bottom: 0;
}

.metric-scroll-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.metric-scroll-date {
    flex: 0 0 120px;
    color: #2c4964;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.metric-scroll-details {
    flex: 1;
    display: flex;
    gap: 30px;
}

.metric-scroll-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.metric-scroll-icon {
    width: 40px;
    height: 40px;
    background: #e7f1fd;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1977cc;
}

.metric-scroll-text small {
    color: #6c757d;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.metric-value {
    font-weight: 600;
    color: #2c4964;
    font-size: 14px;
}

.metric-scroll-actions {
    flex: 0 0 auto;
    display: flex;
    gap: 8px;
}

.btn-action {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-edit {
    background: #e7f1fd;
    color: #1977cc;
}

.btn-edit:hover {
    background: #1977cc;
    color: #fff;
    transform: scale(1.1);
}

.btn-delete {
    background: #fde7e7;
    color: #dc3545;
}

.btn-delete:hover {
    background: #dc3545;
    color: #fff;
    transform: scale(1.1);
}

.btn-water {
    background: #e7f6fd;
    color: #17a2b8;
}

.btn-water:hover {
    background: #17a2b8;
    color: #fff;
    transform: scale(1.1);
}

.btn-view {
    background: #e7f1fd;
    color: #6c757d;
}

.btn-view:hover {
    background: #6c757d;
    color: #fff;
    transform: scale(1.1);
}

.empty-state {
    padding: 40px 20px;
}

.btn-getstarted {
    background: #1977cc;
    color: #fff;
    border-radius: 5px;
    padding: 10px 25px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: 0.3s;
    display: inline-flex;
    align-items: center;
}

.btn-getstarted:hover {
    background: #1c84e3;
    color: #fff;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .metric-scroll-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .metric-scroll-details {
        flex-direction: column;
        gap: 15px;
        width: 100%;
    }
    
    .metric-scroll-info {
        justify-content: flex-start;
    }
    
    .metric-scroll-actions {
        align-self: flex-end;
        flex-wrap: wrap;
    }
    
    .scrolling-card-container {
        max-height: 400px;
    }
}
</style>
@endsection

@section('scripts')
<script>
// Confirmation de suppression
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette métrique ?')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection