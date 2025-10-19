@extends('layouts.app')

@section('title', 'Ma Progression')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <!-- Progression -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-line"></i> Ma Progression
                    </h4>
                    <span class="badge bg-light text-dark fs-6">
                        {{ $userChallenge->challenge->reward_points }} points
                    </span>
                </div>

                <div class="card-body">
                    <!-- En-tête du challenge -->
                    <div class="text-center mb-4">
                        <h2>{{ $userChallenge->challenge->title }}</h2>
                        <p class="text-muted">{{ $userChallenge->challenge->description }}</p>
                        
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Objectif</small>
                                <strong>{{ $userChallenge->challenge->target_value }} {{ $userChallenge->challenge->unit }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Début</small>
                                <strong>
                                    {{ \Carbon\Carbon::parse($userChallenge->start_date)->format('d/m/Y') }}
                                </strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Statut</small>
                                <span class="badge bg-{{ $userChallenge->completed ? 'success' : 'warning' }}">
                                    {{ $userChallenge->completed ? 'Complété' : 'En cours' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Barre de progression -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Progression actuelle</span>
                            <span>
                                <strong>{{ $userChallenge->progress }}</strong> / 
                                {{ $userChallenge->challenge->target_value }} {{ $userChallenge->challenge->unit }}
                            </span>
                        </div>

                        @php
                            $percentage = $userChallenge->challenge->target_value > 0 
                                ? min(100, ($userChallenge->progress / $userChallenge->challenge->target_value) * 100)
                                : 0;
                        @endphp

                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar 
                                @if($percentage >= 100) bg-success
                                @elseif($percentage >= 50) bg-warning
                                @else bg-info @endif" 
                                role="progressbar" 
                                style="width: {{ $percentage }}%"
                                aria-valuenow="{{ $userChallenge->progress }}" 
                                aria-valuemin="0" 
                                aria-valuemax="{{ $userChallenge->challenge->target_value }}">
                                {{ round($percentage, 1) }}%
                            </div>
                        </div>
                    </div>

                    @if(!$userChallenge->completed)
                    <!-- Formulaire de mise à jour -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Mettre à jour ma progression</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('user.challenges.update-progress', $userChallenge) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <label for="progress" class="form-label">
                                        Nouvelle progression (0 - {{ $userChallenge->challenge->target_value }})
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="progress" 
                                           name="progress" 
                                           value="{{ $userChallenge->progress }}"
                                           min="0" 
                                           max="{{ $userChallenge->challenge->target_value }}"
                                           required>
                                    <div class="form-text">
                                        Actuellement: {{ $userChallenge->progress }} {{ $userChallenge->challenge->unit }}
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Mettre à jour
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Bouton complétion -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Compléter le challenge</h5>
                        </div>
                        <div class="card-body text-center">
                            <p>Vous avez atteint votre objectif ? Marquez ce challenge comme complété !</p>
                            <form action="{{ route('user.challenges.complete', $userChallenge) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check-circle"></i> Marquer comme complété
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <!-- Félicitations -->
                    <div class="alert alert-success text-center">
                        <h4><i class="fas fa-trophy"></i> Félicitations !</h4>
                        <p>
                            Vous avez complété ce challenge le 
                            {{ \Carbon\Carbon::parse($userChallenge->end_date ?? now())->format('d/m/Y à H:i') }}.
                        </p>
                        <p>Vous avez gagné <strong>{{ $userChallenge->challenge->reward_points }} points</strong> !</p>
                    </div>
                    @endif

                    <!-- Retour -->
                    <div class="mt-4 text-center">
                        <a href="{{ route('challenges.show', $userChallenge->challenge) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Retour au challenge
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📈 Mes Statistiques</h5>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="h3 text-primary">{{ $userChallenge->progress }}</div>
                            <small class="text-muted">Progression actuelle</small>
                        </div>
                        <div class="col-md-4">
                            <div class="h3 text-warning">
                                {{ max(0, $userChallenge->challenge->target_value - $userChallenge->progress) }}
                            </div>
                            <small class="text-muted">Reste à accomplir</small>
                        </div>
                        <div class="col-md-4">
                            @php
                                $startDate = \Carbon\Carbon::parse($userChallenge->start_date);
                                $daysSinceStart = max(1, $startDate->diffInDays(now()));
                                $average = round($userChallenge->progress / $daysSinceStart, 1);
                            @endphp
                            <div class="h3 text-info">{{ $average }}</div>
                            <small class="text-muted">{{ $userChallenge->challenge->unit }}/jour en moyenne</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.progress {
    border-radius: 10px;
    overflow: hidden;
}
.progress-bar {
    transition: width 0.5s ease-in-out;
    font-weight: bold;
}
</style>
@endpush
