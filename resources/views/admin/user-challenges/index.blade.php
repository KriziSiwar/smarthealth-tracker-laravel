@extends('layouts.admin')

@section('title', 'Gestion des Challenges')
@section('page-title', 'Liste des Challenges')

@section('content')
    <a href="{{ route('admin.challenges.create') }}" class="btn btn-primary mb-3">+ Ajouter un Challenge</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Difficulté</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($challenges as $challenge)
                <tr>
                    <td>{{ $challenge->title }}</td>
                    <td>{{ ucfirst($challenge->difficulty) }}</td>
                    <td>{{ ucfirst($challenge->status) }}</td>
                    <td>
                        <a href="{{ route('admin.challenges.show', $challenge->id) }}" class="btn btn-info btn-sm">Voir</a>
                        <a href="{{ route('admin.challenges.edit', $challenge->id) }}" class="btn btn-warning btn-sm">Modifier</a>
                        <form action="{{ route('admin.challenges.destroy', $challenge->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce challenge ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@section('title', 'Gestion des Participations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-users"></i> Gestion des Participations
                        </h3>
                        <div class="card-tools">
                            <span class="badge bg-primary">Total: {{ $stats['total'] }}</span>
                            <span class="badge bg-success ms-1">Complétés: {{ $stats['completed'] }}</span>
                            <span class="badge bg-warning ms-1">En cours: {{ $stats['in_progress'] }}</span>
                            <span class="badge bg-danger ms-1">Abandonnés: {{ $stats['abandoned'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- 🔍 Filtres -->
                    <form method="GET" action="{{ route('admin.user-challenges.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="status" class="form-label small">Statut</label>
                                <select name="status" id="status" class="form-select form-select-sm">
                                    <option value="">Tous</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Complété</option>
                                    <option value="abandoned" {{ request('status') == 'abandoned' ? 'selected' : '' }}>Abandonné</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="completed" class="form-label small">Complétion</label>
                                <select name="completed" id="completed" class="form-select form-select-sm">
                                    <option value="">Tous</option>
                                    <option value="true" {{ request('completed') === 'true' ? 'selected' : '' }}>Complétés</option>
                                    <option value="false" {{ request('completed') === 'false' ? 'selected' : '' }}>Non complétés</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="user_id" class="form-label small">Utilisateur</label>
                                <select name="user_id" id="user_id" class="form-select form-select-sm">
                                    <option value="">Tous</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="challenge_id" class="form-label small">Challenge</label>
                                <select name="challenge_id" id="challenge_id" class="form-select form-select-sm">
                                    <option value="">Tous les challenges</option>
                                    @foreach($challenges as $challenge)
                                        <option value="{{ $challenge->id }}" {{ request('challenge_id') == $challenge->id ? 'selected' : '' }}>
                                            {{ Str::limit($challenge->title, 30) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <div class="row g-1">
                                    <div class="col-6">
                                        <label for="start_date" class="form-label small">Début après</label>
                                        <input type="date" name="start_date" id="start_date"
                                            class="form-control form-control-sm"
                                            value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-6">
                                        <label for="end_date" class="form-label small">Fin avant</label>
                                        <input type="date" name="end_date" id="end_date"
                                            class="form-control form-control-sm"
                                            value="{{ request('end_date') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-filter"></i> Filtrer
                                    </button>
                                    <a href="{{ route('admin.user-challenges.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-redo"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- 📋 Tableau -->
                    @if($userChallenges->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Utilisateur</th>
                                        <th>Challenge</th>
                                        <th>Progression</th>
                                        <th>Statut</th>
                                        <th>Score</th>
                                        <th>Date Début</th>
                                        <th>Date Fin</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($userChallenges as $uc)
                                        <tr>
                                            <td>{{ $uc->id }}</td>
                                            <td>
                                                <strong>{{ $uc->user->name }}</strong><br>
                                                <small class="text-muted">{{ $uc->user->email }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $uc->challenge->title }}</strong><br>
                                                <small>{{ $uc->challenge->target_value }} {{ $uc->challenge->unit }}</small>
                                            </td>
                                            <td>
                                                <div class="progress" style="height:20px;">
                                                    <div class="progress-bar 
                                                        @if($uc->progress >= $uc->challenge->target_value) bg-success
                                                        @elseif($uc->progress > 0) bg-warning
                                                        @else bg-secondary @endif" 
                                                        role="progressbar"
                                                        style="width: {{ min(100, ($uc->progress / $uc->challenge->target_value) * 100) }}%">
                                                        {{ $uc->progress }}/{{ $uc->challenge->target_value }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($uc->status == 'completed')
                                                    <span class="badge bg-success">Complété</span>
                                                @elseif($uc->status == 'in_progress')
                                                    <span class="badge bg-warning">En cours</span>
                                                @else
                                                    <span class="badge bg-danger">Abandonné</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-info">{{ $uc->score ?? 0 }} pts</span></td>
                                            <td><small>{{ \Carbon\Carbon::parse($uc->start_date)->format('d/m/Y') }}</small></td>
<td><small>{{ $uc->end_date ? \Carbon\Carbon::parse($uc->end_date)->format('d/m/Y') : '-' }}</small></td>

                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.user-challenges.show', $uc) }}" class="btn btn-info"><i class="fas fa-eye"></i></a>
                                                    <a href="{{ route('admin.user-challenges.edit', $uc) }}" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                                    <form action="{{ route('admin.user-challenges.destroy', $uc) }}" method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Supprimer cette participation ?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                Affichage de {{ $userChallenges->firstItem() }} à {{ $userChallenges->lastItem() }}
                                sur {{ $userChallenges->total() }} participations
                            </small>
                            {{ $userChallenges->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h4>Aucune participation trouvée</h4>
                            <p class="text-muted">Aucun utilisateur n'a encore rejoint de challenge.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.progress {
    min-width: 120px;
}
.table td {
    vertical-align: middle;
}
</style>
@endpush
