@extends('layouts.simple')
@section('content')
<h1>Modifier le type d'activité</h1>
<form method="POST" action="{{ route('activity-types.update', $activityType) }}">
  @method('PUT')
  @include('activity_types._form')
  <button class="btn btn-primary">Mettre à jour</button>
  <a href="{{ route('activity-types.index') }}" class="btn btn-secondary">Annuler</a>
</form>
@endsection
