@extends('layouts.simple')
@section('content')
<h1>Détail type d'activité</h1>
<ul>
  <li>ID : {{ $activityType->id }}</li>
  <li>Nom : {{ $activityType->name }}</li>
  <li>Calories/min : {{ $activityType->calories_per_minute }}</li>
  <li>Créé le : {{ $activityType->created_at->format('d/m/Y H:i') }}</li>
</ul>
<a href="{{ route('activity-types.index') }}" class="btn btn-secondary">Retour</a>
@endsection
