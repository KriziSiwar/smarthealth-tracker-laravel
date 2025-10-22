@extends('layouts.admin') 
@section('title', 'Gestion des Participations')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-users"></i> Gestion des Participations</h4>
            <div>
                <span class="badge bg-primary">Total : {{ $stats['total'] ?? 0 }}</span>
                <span class="badge bg-success ms-1">Complétés : {{ $stats['completed'] ?? 0 }}</span>
                <span class="badge bg-warning ms-1">En cours : {{ $stats['in_progress'] ?? 0 }}</span>
                <span class="badge bg-danger ms-1">Abandonnés : {{ $stats['abandoned'] ?? 0 }}</span>
            </div>
        </div>

        <div class="card-body">
            <!-- 🔍 Filtres -->
            <form method="GET" action="{{ route('admin.user-challenges.index') }}" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="status" class="form-label small">Statut</label>
                        <select name="status" id="status" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Complété</option>
                            <option value="abandoned" {{ request('status') == 'abandoned' ? 'selected' : '' }}>Abandonné</option>
                        </select>
                    </div>
                    <div class="col-md-3">
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
                            <option value="">Tous</option>
                            @foreach($challenges as $challenge)
                                <option value="{{ $challenge->id }}" {{ request('challenge_id') == $challenge->id ? 'selected' : '' }}>
                                    {{ Str::limit($challenge->title, 30) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>
                    </div>
                </div>
            </form>

            <!-- 📋 Tableau -->
            @if($userChallenges->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-dark text-center">
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
                            <td class="text-center">{{ $uc->id }}</td>
                            <td>
                                <strong>{{ $uc->user->name }}</strong><br>
                                <small class="text-muted">{{ $uc->user->email }}</small>
                            </td>
                            <td>{{ $uc->challenge->title }}</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar 
                                        @if($uc->status == 'completed') bg-success
                                        @elseif($uc->status == 'in_progress') bg-warning
                                        @else bg-danger @endif" 
                                        style="width: {{ min(100, ($uc->progress / $uc->challenge->target_value) * 100) }}%;">
                                        {{ $uc->progress }}/{{ $uc->challenge->target_value }}
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($uc->status == 'completed')
                                    <span class="badge bg-success">Complété</span>
                                @elseif($uc->status == 'in_progress')
                                    <span class="badge bg-warning text-dark">En cours</span>
                                @else
                                    <span class="badge bg-danger">Abandonné</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $uc->score ?? 0 }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($uc->start_date)->format('d/m/Y') }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($uc->end_date)->format('d/m/Y') ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.user-challenges.show', $uc->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.user-challenges.destroy', $uc->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Affichage de {{ $userChallenges->firstItem() }} à {{ $userChallenges->lastItem() }} sur {{ $userChallenges->total() }}
                </small>
                {{ $userChallenges->links() }}
            </div>

            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5>Aucune participation trouvée</h5>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table td, .table th { vertical-align: middle !important; }
.progress { min-width: 120px; }
</style>
@endpush
