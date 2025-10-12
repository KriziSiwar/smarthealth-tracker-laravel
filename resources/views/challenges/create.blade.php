@extends('layouts.app')

@section('title', 'Créer un Nouveau Challenge')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle"></i> Créer un Nouveau Challenge
                    </h4>
                </div>
                <div class="card-body">
                    {{-- Messages d'erreur --}}
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

                    <form action="{{ route('challenges.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Titre du Challenge *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Date de début *</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                           id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Date de fin *</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                           id="end_date" name="end_date" value="{{ old('end_date') }}" required>
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
                                           id="target_value" name="target_value" value="{{ old('target_value') }}" required>
                                    @error('target_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unité *</label>
                                    <input type="text" class="form-control @error('unit') is-invalid @enderror"
                                           id="unit" name="unit" value="{{ old('unit') }}" required>
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="reward_points" class="form-label">Points de récompense *</label>
                                    <input type="number" class="form-control @error('reward_points') is-invalid @enderror"
                                           id="reward_points" name="reward_points" value="{{ old('reward_points', 100) }}" required>
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
                                        <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>Facile</option>
                                        <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>Moyen</option>
                                        <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>Difficile</option>
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
                                        <option value="sport" {{ old('category') == 'sport' ? 'selected' : '' }}>Sport</option>
                                        <option value="nutrition" {{ old('category') == 'nutrition' ? 'selected' : '' }}>Nutrition</option>
                                        <option value="mental" {{ old('category') == 'mental' ? 'selected' : '' }}>Bien-être mental</option>
                                        <option value="ecologie" {{ old('category') == 'ecologie' ? 'selected' : '' }}>Écologie</option>
                                        <option value="apprentissage" {{ old('category') == 'apprentissage' ? 'selected' : '' }}>Apprentissage</option>
                                        <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>Général</option>
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
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('challenges.index') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Créer le Challenge
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
