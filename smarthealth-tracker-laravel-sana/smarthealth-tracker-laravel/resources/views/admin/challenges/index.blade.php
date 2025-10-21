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
