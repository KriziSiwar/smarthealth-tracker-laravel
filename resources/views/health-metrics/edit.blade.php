{{-- resources/views/health-metrics/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier la Métrique Santé - Mon App Santé')

@section('content')
<!-- ======= Edit Metric Section ======= -->
<section id="edit-metric" class="edit-metric section">
    <div class="container" data-aos="fade-up">

        <div class="section-header">
            <h2>Modifier la Métrique Santé</h2>
            <p>⚖️ Mettez à jour vos mesures de santé</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- ======= Edit Metric Form ======= -->
                <form action="{{ route('health-metrics.update', $metric->id) }}" method="POST" class="php-email-form">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-12 form-group">
                            <label for="weight_kg" class="form-label">Poids (kg)</label>
                            <input type="number" name="weight_kg" id="weight_kg" step="0.1"
                                   class="form-control @error('weight_kg') is-invalid @enderror"
                                   value="{{ old('weight_kg', $metric->weight_kg) }}" 
                                   required min="30" max="300"
                                   placeholder="Ex: 70.5">
                            <div class="validate"></div>
                            @error('weight_kg')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12 form-group">
                            <label for="measurement" class="form-label">Type de mesure</label>
                            <select name="measurement" id="measurement" 
                                    class="form-control @error('measurement') is-invalid @enderror" required>
                                <option value="">Sélectionnez le moment</option>
                                <option value="matin" {{ old('measurement', $metric->measurement) == 'matin' ? 'selected' : '' }}>Matin</option>
                                <option value="soir" {{ old('measurement', $metric->measurement) == 'soir' ? 'selected' : '' }}>Soir</option>
                                <option value="avant_entrainement" {{ old('measurement', $metric->measurement) == 'avant_entrainement' ? 'selected' : '' }}>Avant entraînement</option>
                                <option value="apres_entrainement" {{ old('measurement', $metric->measurement) == 'apres_entrainement' ? 'selected' : '' }}>Après entraînement</option>
                            </select>
                            <div class="validate"></div>
                            @error('measurement')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12 form-group">
                            <label for="measured_at" class="form-label">Date de mesure</label>
                            <input type="date" name="measured_at" id="measured_at"
                                   class="form-control @error('measured_at') is-invalid @enderror"
                                   value="{{ old('measured_at', $metric->measured_at->format('Y-m-d')) }}" 
                                   required>
                            <div class="validate"></div>
                            @error('measured_at')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle me-2"></i>
                            Mettre à jour
                        </button>
                        <a href="{{ route('health.dashboard') }}" class="btn btn-outline-secondary btn-lg ms-2">
                            <i class="bi bi-arrow-left me-2"></i>
                            Annuler
                        </a>
                    </div>

                </form>
                <!-- End Edit Metric Form -->

            </div>
        </div>

    </div>
</section><!-- End Edit Metric Section -->
@endsection

@section('styles')
<style>
.edit-metric.section {
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

.php-email-form .form-label {
    font-weight: 600;
    color: #2c4964;
    margin-bottom: 8px;
    display: block;
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
    color: #6c757d;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
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

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.is-invalid {
    border-color: #dc3545 !important;
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation basique
    const form = document.querySelector('.php-email-form');
    const inputs = form.querySelectorAll('input[required], select[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                this.style.borderColor = '#28a745';
            }
        });
    });

    // Confirmation avant annulation
    const cancelBtn = document.querySelector('.btn-outline-secondary');
    cancelBtn.addEventListener('click', function(e) {
        if (!confirm('Les modifications non enregistrées seront perdues. Continuer ?')) {
            e.preventDefault();
        }
    });

    // Formatage automatique du poids
    const weightInput = document.getElementById('weight_kg');
    weightInput.addEventListener('change', function() {
        if (this.value) {
            this.value = parseFloat(this.value).toFixed(1);
        }
    });
});
</script>
@endsection