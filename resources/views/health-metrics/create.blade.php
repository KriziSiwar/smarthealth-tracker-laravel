{{-- resources/views/health-metrics/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Ajouter une Métrique Santé')

@section('content')
<!-- ======= Add Metric Section ======= -->
<section id="add-metric" class="add-metric section">
    <div class="container" data-aos="fade-up">

        <div class="section-header">
            <h2>Ajouter une Métrique Santé</h2>
            <p>Enregistrez vos mesures de santé quotidiennes</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- ======= Metric Form ======= -->
                <form action="{{ route('health-metrics.store') }}" method="POST" class="php-email-form">
                    @csrf

                    <div class="row mb-4">
                        <div class="col-md-12 form-group">
                            <label for="weight_kg" class="form-label">Poids (kg)</label>
                            <input type="number" id="weight_kg" name="weight_kg" step="0.1" 
                                   class="form-control" required 
                                   placeholder="Ex: 70.5"
                                   min="30" max="300">
                            <div class="validate"></div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12 form-group">
                            <label for="measurement" class="form-label">Type de mesure</label>
                            <select id="measurement" name="measurement" class="form-control" required>
                                <option value="">Sélectionnez le moment de la mesure</option>
                                <option value="matin">Matin</option>
                                <option value="soir">Soir</option>
                                <option value="avant_entrainement">Avant entraînement</option>
                                <option value="apres_entrainement">Après entraînement</option>
                            </select>
                            <div class="validate"></div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12 form-group">
                            <label for="measured_at" class="form-label">Date de mesure</label>
                            <input type="date" id="measured_at" name="measured_at" 
                                   value="{{ date('Y-m-d') }}" 
                                   class="form-control" required>
                            <div class="validate"></div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle me-2"></i>
                            Enregistrer la métrique
                        </button>
                        <a href="{{ route('health.dashboard') }}" class="btn btn-outline-secondary btn-lg ms-2">
                            <i class="bi bi-arrow-left me-2"></i>
                            Retour à la liste
                        </a>
                    </div>

                </form>
                <!-- End Metric Form -->

            </div>
        </div>

    </div>
</section><!-- End Add Metric Section -->
@endsection

@section('styles')
<style>
.add-metric.section {
    padding: 60px 0;
    background: #f8f9fa;
    min-height: calc(100vh - 160px);
}

.php-email-form {
    background: #fff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
}

.php-email-form .form-group {
    margin-bottom: 25px;
}

.php-email-form .form-control {
    height: 50px;
    border-radius: 5px;
    border: 1px solid #ddd;
    padding: 10px 15px;
    font-size: 16px;
}

.php-email-form .form-control:focus {
    border-color: #1977cc;
    box-shadow: 0 0 0 0.2rem rgba(25, 119, 204, 0.25);
}

.php-email-form select.form-control {
    height: 50px;
}

.php-email-form .btn-primary {
    background: #1977cc;
    border: 0;
    padding: 12px 40px;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.4s;
    font-size: 16px;
}

.php-email-form .btn-primary:hover {
    background: #1c84e3;
    transform: translateY(-2px);
}

.php-email-form .btn-outline-secondary {
    border: 2px solid #6c757d;
    padding: 12px 30px;
    border-radius: 5px;
    transition: 0.4s;
    font-size: 16px;
}

.php-email-form .btn-outline-secondary:hover {
    background: #6c757d;
    color: #fff;
}

.validate {
    display: none;
    color: #dc3545;
    font-size: 14px;
    margin-top: 5px;
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation basique côté client
    const form = document.querySelector('.php-email-form');
    const inputs = form.querySelectorAll('input[required], select[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#28a745';
            }
        });
    });
    
    // Confirmation avant soumission
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.style.borderColor = '#dc3545';
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
        }
    });
});
</script>
@endsection