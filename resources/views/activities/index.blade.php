@extends('layouts.simple')
@section('content')
<h1>Activities</h1>
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
<a href="{{ route('activities.create') }}" class="btn btn-primary mb-3">Nouvelle activité</a>
<table class="table table-bordered">
  <thead><tr>
    <th>ID</th><th>Utilisateur</th><th>Type</th><th>Durée (min)</th><th>Date</th><th>Créée le</th><th>Actions</th>
  </tr></thead>
  <tbody>
    @forelse($activities as $a)
    <tr>
      <td>{{ $a->id }}</td>
      <td>{{ $a->user?->email ?? '—' }}</td>
      <td>{{ $a->activityType?->name }}</td>
      <td>{{ $a->duration_minutes }}</td>
      <td>{{ $a->activity_date->format('d/m/Y') }}</td>
      <td>{{ $a->created_at->format('d/m/Y H:i') }}</td>
      <td class="d-flex gap-2">
        <a class="btn btn-sm btn-info" href="{{ route('activities.show', $a) }}">Voir</a>
        <a class="btn btn-sm btn-warning" href="{{ route('activities.edit', $a) }}">Éditer</a>
        <form method="POST" action="{{ route('activities.destroy', $a) }}" onsubmit="return confirm('Supprimer ?')">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-danger">Supprimer</button>
        </form>
      </td>
    </tr>
    @empty
      <tr><td colspan="7">Aucune activité.</td></tr>
    @endforelse
  </tbody>
</table>
{{ $activities->links() }}
@endsection
