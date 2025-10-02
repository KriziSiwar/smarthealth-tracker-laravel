{{-- resources/views/waterintakes/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier la Prise d\'Eau - Mon App Santé')

@section('content')
<!-- ======= Edit Water Section ======= -->
<section id="edit-water" class="edit-water section">
    <div class="container" data-aos="fade-up">

        <div class="section-header">
            <h2>Modifier la Prise d'Eau</h2>
            <p>🚰 Mettez à jour votre consommation d'eau</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- ======= Edit Water Form ======= -->
                <form action="{{ route('waterintakes.update', $waterintake->id) }}" method="POST" class="php-email-form">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-12 form-group">
                            <label for="amount_ml" class="form-label">Quantité (ml)</label>
                            <input type="number" name="amount_ml" id="amount_ml" 
                                   class="form-control @error('amount_ml') is-invalid @enderror"
                                   value="{{ old('amount_ml', $waterintake->amount_ml) }}" 
                                   required min="50" max="5000"
                                   placeholder="Ex: 500">
                            <div class="validate"></div>
                            @error('amount_ml')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12 form-group">
                            <label for="intake_date" class="form-label">Date de consommation</label>
                            <input type="date" name="intake_date" id="intake_date"
                                   class="form-control @error('intake_date') is-invalid @enderror"
                                   value="{{ old('intake_date', $waterintake->intake_date) }}" 
                                   required>
                            <div class="validate"></div>
                            @error('intake_date')
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
                        <a href="{{ route('waterintakes.index') }}" class="btn btn-outline-secondary btn-lg ms-2">
                            <i class="bi bi-arrow-left me-2"></i>
                            Annuler
                        </a>
                    </div>

                </form>
                <!-- End Edit Water Form -->

            </div>
        </div>

    </div>
</section><!-- End Edit Water Section -->
@endsection

@section('styles')
<style>
.edit-water.section {
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
    const inputs = form.querySelectorAll('input[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                this.style.borderColor = '#28a745';
            }
        });
        
        // Restaurer la valeur originale si annulation
        const originalValue = this.value;
        const cancelBtn = document.querySelector('.btn-outline-secondary');
        cancelBtn.addEventListener('click', function(e) {
            if (!confirm('Les modifications non enregistrées seront perdues. Continuer ?')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection