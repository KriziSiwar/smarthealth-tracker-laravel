@extends('layouts.app')

@section('title', 'Modifier le Challenge')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Modifier: {{ $challenge->title }}
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Messages d'erreur -->
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h5>Erreurs de validation :</h5>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('challenges.update', $challenge->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre du Challenge *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $challenge->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" required>{{ old('description', $challenge->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Date de début *</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" 
                                           value="{{ old('start_date', $challenge->start_date->format('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Date de fin *</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" 
                                           value="{{ old('end_date', $challenge->end_date->format('Y-m-d')) }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="target_value" class="form-label">Valeur cible *</label>
                                    <input type="number" class="form-control @error('target_value') is-invalid @enderror" 
                                           id="target_value" name="target_value" 
                                           value="{{ old('target_value', $challenge->target_value) }}" required>
                                    @error('target_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unité *</label>
                                    <input type="text" class="form-control @error('unit') is-invalid @enderror" 
                                           id="unit" name="unit" value="{{ old('unit', $challenge->unit) }}" required>
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="reward_points" class="form-label">Points de récompense *</label>
                                    <input type="number" class="form-control @error('reward_points') is-invalid @enderror" 
                                           id="reward_points" name="reward_points" 
                                           value="{{ old('reward_points', $challenge->reward_points) }}" required>
                                    @error('reward_points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="difficulty" class="form-label">Difficulté *</label>
                                    <select class="form-control @error('difficulty') is-invalid @enderror" 
                                            id="difficulty" name="difficulty" required>
                                        <option value="">Sélectionner la difficulté</option>
                                        <option value="easy" {{ old('difficulty', $challenge->difficulty) == 'easy' ? 'selected' : '' }}>Facile</option>
                                        <option value="medium" {{ old('difficulty', $challenge->difficulty) == 'medium' ? 'selected' : '' }}>Moyen</option>
                                        <option value="hard" {{ old('difficulty', $challenge->difficulty) == 'hard' ? 'selected' : '' }}>Difficile</option>
                                    </select>
                                    @error('difficulty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Catégorie *</label>
                                    <select class="form-control @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="">Sélectionner une catégorie</option>
                                        <option value="sport" {{ old('category', $challenge->category) == 'sport' ? 'selected' : '' }}>Sport</option>
                                        <option value="nutrition" {{ old('category', $challenge->category) == 'nutrition' ? 'selected' : '' }}>Nutrition</option>
                                        <option value="mental" {{ old('category', $challenge->category) == 'mental' ? 'selected' : '' }}>Bien-être mental</option>
                                        <option value="ecologie" {{ old('category', $challenge->category) == 'ecologie' ? 'selected' : '' }}>Écologie</option>
                                        <option value="apprentissage" {{ old('category', $challenge->category) == 'apprentissage' ? 'selected' : '' }}>Apprentissage</option>
                                        <option value="general" {{ old('category', $challenge->category) == 'general' ? 'selected' : '' }}>Général</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Statut *</label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="active" {{ old('status', $challenge->status) == 'active' ? 'selected' : '' }}>Actif</option>
                                <option value="inactive" {{ old('status', $challenge->status) == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                <option value="draft" {{ old('status', $challenge->status) == 'draft' ? 'selected' : '' }}>Brouillon</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Informations sur le créateur (affichage seulement) -->
                        <div class="card bg-light mb-3">
                            <div class="card-body py-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Ce challenge a été créé par <strong>{{ $challenge->creator->name ?? 'Utilisateur' }}</strong>
                                    le {{ $challenge->created_at->format('d/m/Y à H:i') }}
                                </small>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('challenges.show', $challenge->id) }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                            
                            <!-- Bouton de suppression -->
                            <button type="button" class="btn btn-danger ms-2" 
                                    onclick="confirmDelete({{ $challenge->id }})">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </form>

                    <!-- Formulaire de suppression caché -->
                    <form id="delete-form-{{ $challenge->id }}" 
                          action="{{ route('challenges.destroy', $challenge->id) }}" 
                          method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(challengeId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce challenge ? Cette action est irréversible.')) {
        document.getElementById('delete-form-' + challengeId).submit();
    }
}
</script>
@endpush