@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Ajouter un nouvel aliment</h1>
        <a href="{{ route('admin.nutrition.foods.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.nutrition.foods.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom de l'aliment <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                     id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Catégorie</label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category">
                                <option value="">Sélectionnez une catégorie</option>
                                <option value="fruits" {{ old('category') == 'fruits' ? 'selected' : '' }}>Fruits</option>
                                <option value="legumes" {{ old('category') == 'legumes' ? 'selected' : '' }}>Légumes</option>
                                <option value="viandes" {{ old('category') == 'viandes' ? 'selected' : '' }}>Viandes</option>
                                <option value="poissons" {{ old('category') == 'poissons' ? 'selected' : '' }}>Poissons</option>
                                <option value="produits-laitiers" {{ old('category') == 'produits-laitiers' ? 'selected' : '' }}>Produits laitiers</option>
                                <option value="cereales" {{ old('category') == 'cereales' ? 'selected' : '' }}>Céréales</option>
                                <option value="boissons" {{ old('category') == 'boissons' ? 'selected' : '' }}>Boissons</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">Valeurs nutritionnelles (pour 100g)</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label for="calories" class="form-label">Calories (kcal) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.1" class="form-control @error('calories') is-invalid @enderror" 
                                               id="calories" name="calories" value="{{ old('calories') }}" required>
                                        @error('calories')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="protein" class="form-label">Protéines (g) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.1" class="form-control @error('protein') is-invalid @enderror" 
                                               id="protein" name="protein" value="{{ old('protein') }}" required>
                                        @error('protein')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="carbohydrates" class="form-label">Glucides (g) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.1" class="form-control @error('carbohydrates') is-invalid @enderror" 
                                               id="carbohydrates" name="carbohydrates" value="{{ old('carbohydrates') }}" required>
                                        @error('carbohydrates')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="fat" class="form-label">Lipides (g) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.1" class="form-control @error('fat') is-invalid @enderror" 
                                               id="fat" name="fat" value="{{ old('fat') }}" required>
                                        @error('fat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="sugar" class="form-label">Sucres (g)</label>
                                        <input type="number" step="0.1" class="form-control @error('sugar') is-invalid @enderror" 
                                               id="sugar" name="sugar" value="{{ old('sugar', 0) }}">
                                        @error('sugar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="fiber" class="form-label">Fibres (g)</label>
                                        <input type="number" step="0.1" class="form-control @error('fiber') is-invalid @enderror" 
                                               id="fiber" name="fiber" value="{{ old('fiber', 0) }}">
                                        @error('fiber')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="serving_size" class="form-label">Taille de portion <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" step="0.1" class="form-control @error('serving_size') is-invalid @enderror" 
                                                       id="serving_size" name="serving_size" value="{{ old('serving_size', 100) }}" required>
                                                <select class="form-select @error('serving_unit') is-invalid @enderror" 
                                                        id="serving_unit" name="serving_unit" style="max-width: 150px;" required>
                                                    <option value="g" {{ old('serving_unit') == 'g' ? 'selected' : '' }}>Grammes (g)</option>
                                                    <option value="ml" {{ old('serving_unit') == 'ml' ? 'selected' : '' }}>Millilitres (ml)</option>
                                                    <option value="tasse" {{ old('serving_unit') == 'tasse' ? 'selected' : '' }}>Tasse</option>
                                                    <option value="cas" {{ old('serving_unit') == 'cas' ? 'selected' : '' }}>Cuillère à soupe</option>
                                                    <option value="cac" {{ old('serving_unit') == 'cac' ? 'selected' : '' }}>Cuillère à café</option>
                                                    <option value="unite" {{ old('serving_unit') == 'unite' ? 'selected' : '' }}>Unité</option>
                                                </select>
                                            </div>
                                            @error('serving_size')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            @error('serving_unit')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="carbohydrates" class="form-label">Glucides (g) <span class="text-danger">*</span></label>
                                            <input type="number" step="0.1" class="form-control @error('carbohydrates') is-invalid @enderror" 
                                                   id="carbohydrates" name="carbohydrates" value="{{ old('carbohydrates', 0) }}" required>
                                            @error('carbohydrates')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label for="fat" class="form-label">Lipides (g) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.1" class="form-control @error('fat') is-invalid @enderror" 
                                               id="fat" name="fat" value="{{ old('fat', 0) }}" required>
                                        @error('fat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="fiber" class="form-label">Fibres (g)</label>
                                        <input type="number" step="0.1" class="form-control @error('fiber') is-invalid @enderror" 
                                               id="fiber" name="fiber" value="{{ old('fiber', 0) }}">
                                        @error('fiber')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="sugar" class="form-label">Sucres (g)</label>
                                        <input type="number" step="0.1" class="form-control @error('sugar') is-invalid @enderror" 
                                               id="sugar" name="sugar" value="{{ old('sugar', 0) }}">
                                        @error('sugar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="sodium" class="form-label">Sodium (mg)</label>
                                        <input type="number" step="1" class="form-control @error('sodium') is-invalid @enderror" 
                                               id="sodium" name="sodium" value="{{ old('sodium', 0) }}">
                                        @error('sodium')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="cholesterol" class="form-label">Cholestérol (mg)</label>
                                        <input type="number" step="1" class="form-control @error('cholesterol') is-invalid @enderror" 
                                               id="cholesterol" name="cholesterol" value="{{ old('cholesterol', 0) }}">
                                        @error('cholesterol')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Image de l'aliment</label>
                            <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @error('serving_unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Image de l'aliment</label>
                            <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Format recommandé : 500x500px, max 2MB</div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved" value="1" 
                                   {{ old('is_approved') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_approved">Approuver cet aliment</label>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="reset" class="btn btn-outline-secondary me-md-2">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer l'aliment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Validation côté client
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        
        form.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Vérification des champs requis
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = 'Ce champ est requis';
                        field.parentNode.insertBefore(errorDiv, field.nextSibling);
                    }
                }
            });
            
            if (!isValid) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
        
        // Réinitialisation des états de validation lors de la modification
        form.querySelectorAll('input, textarea, select').forEach(function(input) {
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });
        });
    });
</script>
@endpush

@endsection