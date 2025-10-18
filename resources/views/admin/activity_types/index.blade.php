@extends('layouts.admin')

@section('content')
<div class="pagetitle">
    <h1>Types d’activités</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Types d’activités</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-3 mb-3">
                <h5 class="card-title mb-0">Liste des types</h5>
                <form class="d-flex" method="GET" action="{{ route('admin.activity-types.index') }}">
                    <input type="text" class="form-control me-2" name="search" placeholder="Rechercher (nom, description)" value="{{ $search }}">
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input type="hidden" name="direction" value="{{ $direction }}">
                    <button class="btn btn-outline-primary">Rechercher</button>
                </form>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @php
                function sort_link2($label, $field, $currentSort, $currentDir) {
                    $dir = ($currentSort === $field && $currentDir === 'asc') ? 'desc' : 'asc';
                    $icon = '';
                    if ($currentSort === $field) {
                        $icon = $currentDir === 'asc' ? '↑' : '↓';
                    }
                    $params = array_merge(request()->query(), ['sort' => $field, 'direction' => $dir]);
                    $url = request()->url() . '?' . http_build_query($params);
                    return '<a href="'.$url.'" class="text-decoration-none">'.$label.' <span class="text-muted">'.$icon.'</span></a>';
                }
            @endphp

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{!! sort_link2('Nom', 'name', $sort, $direction) !!}</th>
                            <th>{!! sort_link2('Description', 'description', $sort, $direction) !!}</th>
                            <th class="text-end">
                                <a href="{{ route('admin.activity-types.create') }}" class="btn btn-primary btn-sm">+ Ajouter</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $type)
                            <tr>
                                <td>{{ $type->name }}</td>
                                <td>{{ $type->description }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.activity-types.edit', $type) }}" class="btn btn-outline-warning btn-sm">Modifier</a>
                                    <form action="{{ route('admin.activity-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce type ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">Aucun type d’activité.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $types->links() }}
            </div>
        </div>
    </div>
</section>
@endsection
