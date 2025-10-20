@extends('layouts.activity')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4 text-success"><i class="bi bi-clipboard-data"></i> Liste des activités</h3>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
        <div class="d-flex flex-wrap gap-2 mb-2 mb-md-0">
            <a href="{{ route('activities.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Ajouter une activité
            </a>

            {{-- ✅ Nouveau bouton Statistique --}}
            <a href="{{ route('activities.stats') }}" class="btn btn-outline-success">
                <i class="bi bi-graph-up"></i> Statistique
            </a>
        </div>

        {{-- Barre de recherche --}}
        <form method="GET" action="{{ route('activities.index') }}" class="d-flex">
            <input type="text" name="search" class="form-control me-2"
                   placeholder="Rechercher..." value="{{ $search ?? '' }}">
            <input type="hidden" name="sort" value="{{ $sort ?? 'activity_date' }}">
            <input type="hidden" name="direction" value="{{ $direction ?? 'desc' }}">
            <button class="btn btn-outline-success" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        // Génère les liens de tri dans l'en-tête
        function sort_link($label, $field, $currentSort, $currentDir) {
            $dir = ($currentSort === $field && $currentDir === 'asc') ? 'desc' : 'asc';
            $icon = '';
            if ($currentSort === $field) {
                $icon = $currentDir === 'asc' ? '↑' : '↓';
            }
            $params = array_merge(request()->query(), ['sort' => $field, 'direction' => $dir]);
            $url = request()->url() . '?' . http_build_query($params);
            return '<a href="'.$url.'" class="text-decoration-none text-success fw-semibold">'.$label.' <span class="text-muted">'.$icon.'</span></a>';
        }
    @endphp

    <table class="table table-striped table-bordered text-center align-middle">
        <thead class="table-success">
            <tr>
                <th>{!! sort_link('Type', 'activity_type_id', $sort ?? '', $direction ?? '') !!}</th>
                <th>{!! sort_link('Durée (min)', 'duration', $sort ?? '', $direction ?? '') !!}</th>
                <th>{!! sort_link('Calories', 'calories_burned', $sort ?? '', $direction ?? '') !!}</th>
                <th>{!! sort_link('Intensité', 'intensity', $sort ?? '', $direction ?? '') !!}</th>
                <th>{!! sort_link('Date', 'activity_date', $sort ?? '', $direction ?? '') !!}</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($activities as $activity)
                <tr>
                    {{-- ✅ Affiche le type d’activité correctement --}}
                    <td>{{ $activity->type ? $activity->type->name : '-' }}</td>
                    <td>{{ $activity->duration }}</td>
                    <td>{{ $activity->calories_burned }}</td>
                    <td>{{ ucfirst($activity->intensity) }}</td>
                    <td>{{ \Carbon\Carbon::parse($activity->activity_date)->format('d/m/Y') }}</td>
                    <td>{{ $activity->notes ?? '-' }}</td>
                    <td>
                        <a href="{{ route('activities.edit', $activity->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Voulez-vous vraiment supprimer cette activité ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-muted">Aucune activité trouvée.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ✅ Pagination simplifiée --}}
    <div class="d-flex justify-content-center mt-3">
        @if ($activities->onFirstPage())
            <span class="btn btn-outline-secondary disabled me-2">Previous</span>
        @else
            <a href="{{ $activities->previousPageUrl() }}" class="btn btn-outline-success me-2">Previous</a>
        @endif

        @if ($activities->hasMorePages())
            <a href="{{ $activities->nextPageUrl() }}" class="btn btn-outline-success">Next</a>
        @else
            <span class="btn btn-outline-secondary disabled">Next</span>
        @endif
    </div>
</div>
@endsection
