@extends('layouts.admin')

@section('title', 'Créer un plan de repas')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Créer un nouveau plan de repas</h1>
        <a href="{{ route('admin.nutrition.meal-plans.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour à la liste
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informations du plan de repas</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.nutrition.meal-plans.store') }}" id="mealPlanForm">
                @csrf
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Nom du plan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                     id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">Rendre ce plan public</label>
                                    <small class="form-text text-muted d-block">Les plans publics sont visibles par tous les utilisateurs</small>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="calories" class="form-label">Objectif calorique (kcal)</label>
                                    <input type="number" class="form-control" id="calories" name="calories" 
                                           value="{{ old('calories') }}" min="0" step="1">
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="protein" class="form-label">Protéines (g)</label>
                                            <input type="number" class="form-control" id="protein" name="protein" 
                                                   value="{{ old('protein') }}" min="0" step="0.1">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="carbohydrates" class="form-label">Glucides (g)</label>
                                            <input type="number" class="form-control" id="carbohydrates" name="carbohydrates" 
                                                   value="{{ old('carbohydrates') }}" min="0" step="0.1">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="fat" class="form-label">Lipides (g)</label>
                                            <input type="number" class="form-control" id="fat" name="fat" 
                                                   value="{{ old('fat') }}" min="0" step="0.1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer le plan
                    </button>
                    <a href="{{ route('admin.nutrition.meal-plans.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-label {
        font-weight: 600;
    }
    .card {
        border: none;
        border-radius: 0.35rem;
    }
    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calcul automatique des calories si les macros sont modifiées
        const proteinInput = document.getElementById('protein');
        const carbsInput = document.getElementById('carbohydrates');
        const fatInput = document.getElementById('fat');
        const caloriesInput = document.getElementById('calories');
        
        function calculateCalories() {
            const protein = parseFloat(proteinInput.value) || 0;
            const carbs = parseFloat(carbsInput.value) || 0;
            const fat = parseFloat(fatInput.value) || 0;
            
            // 1g de protéine = 4 kcal, 1g de glucides = 4 kcal, 1g de lipides = 9 kcal
            const calories = (protein * 4) + (carbs * 4) + (fat * 9);
            
            if (!isNaN(calories) && calories > 0) {
                caloriesInput.value = Math.round(calories);
            }
        }
        
        [proteinInput, carbsInput, fatInput].forEach(input => {
            input.addEventListener('change', calculateCalories);
            input.addEventListener('keyup', calculateCalories);
        });
    });
</script>
@endpush
@endsection
