@extends('layouts.activity')

@section('content')
<div class="container py-4">

    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-primary">
            🏃‍♀️ Liste des Activités
        </h1>
        @auth
            <a href="{{ route('activities.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Nouvelle Activité
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

    <!-- Liste des activités -->
    @if($activities->count() > 0)
        <div class="row">
            @foreach($activities as $activity)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">

                            <!-- Type d'activité -->
                            <span class="badge bg-info mb-2">
                                {{ $activity->type->name ?? 'Non spécifié' }}
                            </span>

                            <!-- Titre -->
                            <h5 class="card-title text-primary mb-2">
                                {{ $activity->title ?? 'Activité #' . $activity->id }}
                            </h5>

                            <!-- Description -->
                            <p class="text-muted small flex-grow-1">
                                {{ Str::limit($activity->description ?? 'Aucune description.', 100) }}
                            </p>

                            <!-- Détails -->
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-dark border">
                                    ⏱ {{ $activity->duration ?? '0' }} min
                                </span>

                                <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-primary btn-sm">
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
            Aucune activité enregistrée pour le moment.
        </div>
    @endif
</div>
@endsection
