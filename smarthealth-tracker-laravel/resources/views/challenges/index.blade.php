@extends('layouts.app')

@section('content')
<div class="container py-4">

    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-primary">
            🏆 Liste des Challenges
        </h1>
        @auth
            <a href="{{ route('challenges.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Nouveau Challenge
            </a>
        @endauth
    </div>

    <!-- Messages de succès / erreur -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Liste des challenges -->
    @if($challenges->count() > 0)
        <div class="row">
            @foreach($challenges as $challenge)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">

                            <!-- Statut -->
                            <div class="mb-2">
                                <span class="badge bg-{{ $challenge->status == 'active' ? 'success' : ($challenge->status == 'draft' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($challenge->status) }}
                                </span>
                                <span class="badge bg-light text-dark border">
                                    {{ ucfirst($challenge->difficulty) }}
                                </span>
                            </div>

                            <!-- Titre -->
                            <h5 class="card-title mb-2 text-primary">{{ $challenge->title }}</h5>

                            <!-- Description courte -->
                            <p class="text-muted small flex-grow-1">
                                {{ Str::limit($challenge->description, 100) }}
                            </p>

                            <!-- Dates -->
                            <div class="mb-2 small text-muted">
                                <i class="fas fa-calendar-alt"></i>
                                Du {{ \Carbon\Carbon::parse($challenge->start_date)->format('d/m/Y') }}
                                au {{ \Carbon\Carbon::parse($challenge->end_date)->format('d/m/Y') }}
                            </div>

                            <!-- Points et lien -->
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="badge bg-primary">
                                    🎯 {{ $challenge->reward_points }} pts
                                </span>

                                <a href="{{ route('challenges.show', $challenge) }}" class="btn btn-outline-primary btn-sm">
                                    Voir <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center">
            Aucun challenge disponible pour le moment.
        </div>
    @endif
</div>
@endsection
