@extends('layouts.simple')
@section('content')
<h1>Créer un type d'activité</h1>
<form method="POST" action="{{ route('activity-types.store') }}">
  @include('activity_types._form', ['activityType' => new \App\Models\ActivityType()])
  <button class="btn btn-primary">Enregistrer</button>
  <a href="{{ route('activity-types.index') }}" class="btn btn-secondary">Annuler</a>
</form>
@endsection
