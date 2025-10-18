@extends('layouts.simple')
@section('content')
<h1>Types d'activités</h1>
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
<a href="{{ route('activity-types.create') }}" class="btn btn-primary mb-3">Nouveau type</a>
<table class="table table-bordered">
  <thead><tr><th>ID</th><th>Nom</th><th>Calories/min</th><th>Créé le</th><th>Actions</th></tr></thead>
  <tbody>
    @forelse($types as $t)
    <tr>
      <td>{{ $t->id }}</td>
      <td>{{ $t->name }}</td>
      <td>{{ $t->calories_per_minute }}</td>
      <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
      <td class="d-flex gap-2">
        <a class="btn btn-sm btn-info" href="{{ route('activity-types.show', $t) }}">Voir</a>
        <a class="btn btn-sm btn-warning" href="{{ route('activity-types.edit', $t) }}">Éditer</a>
        <form method="POST" action="{{ route('activity-types.destroy', $t) }}" onsubmit="return confirm('Supprimer ?')">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-danger">Supprimer</button>
        </form>
      </td>
    </tr>
    @empty
      <tr><td colspan="5">Aucun type.</td></tr>
    @endforelse
  </tbody>
</table>
{{ $types->links() }}
@endsection
