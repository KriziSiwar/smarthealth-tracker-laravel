@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <!-- En-tête du challenge -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-{{ $challenge->status == 'active' ? 'success' : ($challenge->status == 'draft' ? 'warning' : 'secondary') }} mb-2">
                                {{ $challenge->status }}
                            </span>
                            <h1 class="h3 mb-2">{{ $challenge->title }}</h1>
                        </div>
                        <span class="badge bg-primary fs-6">
                            {{ $challenge->reward_points }} points
                        </span>
                    </div>

                    <p class="lead">{{ $challenge->description }}</p>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-calendar text-primary me-3 fs-5"></i>
                                <div>
                                    <small class="text-muted d-block">Date de début</small>
                                    <strong>
                                        {{ \Carbon\Carbon::parse($challenge->start_date)->format('d/m/Y à H:i') }}
                                    </strong>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-flag-checkered text-success me-3 fs-5"></i>
                                <div>
                                    <small class="text-muted d-block">Date de fin</small>
                                    <strong>
                                        {{ \Carbon\Carbon::parse($challenge->end_date)->format('d/m/Y à H:i') }}
                                    </strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-bullseye text-danger me-3 fs-5"></i>
                                <div>
                                    <small class="text-muted d-block">Objectif</small>
                                    <strong>{{ $challenge->target_value }} {{ $challenge->unit }}</strong>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-tachometer-alt text-warning me-3 fs-5"></i>
                                <div>
                                    <small class="text-muted d-block">Difficulté</small>
                                    <strong>
                                        @if($challenge->difficulty == 'easy')
                                            ⭐ Facile
                                        @elseif($challenge->difficulty == 'medium')
                                            ⭐⭐ Moyen
                                        @else
                                            ⭐⭐⭐ Difficile
                                        @endif
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <span class="badge bg-light text-dark border me-2">
                            <i class="fas fa-tag me-1"></i> {{ $challenge->category }}
                        </span>
                        <span class="badge bg-light text-dark border">
                            <i class="fas fa-user me-1"></i> Créé par {{ $challenge->creator->name ?? 'Utilisateur' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Section Participation -->
            @auth
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Participer au Challenge</h5>
                    </div>
                    <div class="card-body">
                        @if($challenge->status == 'active')
                            @php
                                $userParticipation = $challenge->users->where('id', auth()->id())->first();
                            @endphp

                            @if($userParticipation)
                                <div class="alert alert-info">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-check-circle"></i> 
                                            <strong>Vous participez déjà à ce challenge!</strong>
                                            <div class="mt-2">
                                                <span class="badge bg-primary">
                                                    Progression: {{ $userParticipation->pivot->progress ?? 0 }}/{{ $challenge->target_value }} {{ $challenge->unit }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <form action="{{ route('user.challenges.join', $challenge) }}" method="POST">
                                    @csrf
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-play-circle"></i> Rejoindre ce Challenge
                                        </button>
                                        <p class="text-muted mt-2 small">
                                            En participant, vous acceptez de suivre ce challenge et gagnerez 
                                            <strong>{{ $challenge->reward_points }} points</strong> à sa complétion.
                                        </p>
                                    </div>
                                </form>
                            @endif
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-pause-circle"></i> 
                                <strong>Challenge Non Actif</strong>
                                <p class="mb-0 mt-1">Ce challenge n'est pas actuellement disponible pour la participation.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Vous souhaitez participer à ce challenge ?</h5>
                        <p class="text-muted">Connectez-vous pour rejoindre ce challenge et commencer votre progression.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Se connecter
                        </a>
                    </div>
                </div>
            @endauth
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Statistiques -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">📊 Statistiques</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <div class="h2 text-primary mb-1">{{ $challenge->users->count() }}</div>
                            <small class="text-muted">Participants</small>
                        </div>
                        
                        @if($challenge->users->count() > 0)
                            @php
                                $completedCount = $challenge->users->where('pivot.completed', true)->count();
                                $completionRate = round(($completedCount / $challenge->users->count()) * 100, 1);
                            @endphp
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-success" style="width: {{ $completionRate }}%;"></div>
                            </div>
                            <small class="text-muted">{{ $completionRate }}% de taux de complétion</small>
                        @else
                            <small class="text-muted">Aucune donnée de progression</small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Participants récents -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">👥 Participants Récents</h6>
                </div>
                <div class="card-body">
                    @if($challenge->users->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($challenge->users->take(5) as $user)
                                <div class="list-group-item px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 32px; height: 32px;">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <small class="fw-bold d-block">{{ $user->name }}</small>
                                            <small class="text-muted">
                                                Progression: {{ $user->pivot->progress ?? 0 }}%
                                            </small>
                                        </div>
                                        @if($user->pivot->completed ?? false)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($challenge->users->count() > 5)
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    et {{ $challenge->users->count() - 5 }} autres participants...
                                </small>
                            </div>
                        @endif
                    @else
                        <p class="text-muted text-center mb-0">Aucun participant pour le moment</p>
                    @endif
                </div>
            </div>

            <!-- Actions du créateur -->
            @auth
                @if(auth()->id() == $challenge->created_by)
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">⚙️ Gestion</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('challenges.edit', $challenge) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Modifier le Challenge
                                </a>
                                <form action="{{ route('challenges.destroy', $challenge) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm w-100" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce challenge ?')">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</div>
@endsection
