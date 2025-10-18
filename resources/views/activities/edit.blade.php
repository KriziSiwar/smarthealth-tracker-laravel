@extends('layouts.simple')
@section('content')
<h1>Modifier l’activité #{{ $activity->id }}</h1>
<form method="POST" action="{{ route('activities.update', $activity) }}">
  @method('PUT')
  @include('activities._form')
  <button class="btn btn-primary">Mettre à jour</button>
  <a href="{{ route('activities.index') }}" class="btn btn-secondary">Annuler</a>
</form>
@endsection
