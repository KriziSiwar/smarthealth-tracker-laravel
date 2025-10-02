{{-- resources/views/waterintakes/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Suivi Hydratation - Mon App Santé')

@section('content')
<!-- ======= Suivi Hydratation Section ======= -->
<section id="water-tracking" class="water-tracking section">
    <div class="container" data-aos="fade-up">

        <div class="section-header">
            <h2>Suivi de consommation d'eau</h2>
            <p>🚰 Votre historique d'hydratation</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <!-- ======= Water Cards Grid ======= -->
                <div class="php-email-form">
                    <div class="water-header d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">Historique d'Hydratation</h3>
                        <a href="{{ route('waterintakes.create') }}" class="btn-getstarted">
                            <i class="bi bi-plus-circle me-2"></i>Nouvelle Prise d'Eau
                        </a>
                    </div>

                    <div class="row gy-4">
                        @forelse($waterIntakes as $intake)
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="water-card">
                                <div class="water-card-header">
                                    <div class="water-icon">
                                        <i class="bi bi-droplet-fill"></i>
                                    </div>
                                    <div class="water-amount">
                                        <h3>{{ $intake->amount_ml }} ml</h3>
                                    </div>
                                </div>
                                <div class="water-card-body">
                                    <div class="water-date">
                                        <i class="bi bi-calendar3 me-2"></i>
                                        {{ \Carbon\Carbon::parse($intake->intake_date)->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="water-card-actions">
                                    <a href="{{ route('waterintakes.edit', $intake->id) }}" class="btn-action btn-edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('waterintakes.destroy', $intake) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete" onclick="return confirm('Supprimer cette prise d&#39;eau ?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="empty-state text-center py-5">
                                <i class="bi bi-droplet display-1 text-muted mb-3"></i>
                                <h4 class="text-muted">Aucune prise d'eau enregistrée</h4>
                                <p class="text-muted mb-4">Commencez votre suivi d'hydratation</p>
                                <a href="{{ route('waterintakes.create') }}" class="btn-getstarted">
                                    <i class="bi bi-plus-circle me-2"></i>Ajouter une Prise d'Eau
                                </a>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div><!-- End Water Cards Grid -->

            </div>
        </div>

    </div>
</section><!-- End Water Tracking Section -->
@endsection

@section('styles')
<style>
.water-tracking.section {
    padding: 60px 0;
    background: #f8f9fa;
}

.php-email-form {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
}

.water-header {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 20px;
}

.water-header h3 {
    color: #2c4964;
    font-weight: 700;
}

/* Water Card */
.water-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 25px;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    height: 100%;
    position: relative;
}

.water-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    border-color: #1977cc;
}

.water-card-header {
    margin-bottom: 15px;
}

.water-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #1977cc, #1c84e3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
}

.water-icon i {
    font-size: 30px;
    color: #fff;
}

.water-amount h3 {
    color: #2c4964;
    font-weight: 700;
    font-size: 24px;
    margin: 0;
}

.water-card-body {
    margin-bottom: 20px;
}

.water-date {
    color: #6c757d;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.water-card-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
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

.empty-state {
    padding: 60px 20px;
    background: #fff;
    border-radius: 10px;
    border: 2px dashed #dee2e6;
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
    .water-card {
        padding: 20px;
    }
    
    .water-icon {
        width: 60px;
        height: 60px;
    }
    
    .water-icon i {
        font-size: 24px;
    }
    
    .water-amount h3 {
        font-size: 20px;
    }
}
</style>
@endsection