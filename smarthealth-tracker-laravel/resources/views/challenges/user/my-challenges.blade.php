@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Mes Challenges</h1>

    @if($userChallenges->isEmpty())
        <div class="alert alert-info">
            Vous ne participez à aucun challenge pour le moment.
        </div>
    @else
        <div class="row">
            @foreach($userChallenges as $challenge)
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $challenge->title }}</h5>
                            <p class="card-text text-muted">
                                Progression : {{ $challenge->pivot->progress ?? 0 }}/{{ $challenge->target_value }} {{ $challenge->unit }}
                            </p>
                            <p class="card-text">
                                <span class="badge bg-{{ $challenge->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($challenge->status) }}
                                </span>
                            </p>
                            <a href="{{ route('challenges.show', $challenge->id) }}" class="btn btn-primary btn-sm">
                                Voir le Challenge
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
