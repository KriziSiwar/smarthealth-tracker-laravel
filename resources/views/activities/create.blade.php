@extends('layouts.simple')
@section('content')
<h1>Créer une activité</h1>
<form method="POST" action="{{ route('activities.store') }}">
  @include('activities._form', ['activity' => new \App\Models\Activity()])
  <button class="btn btn-primary">Enregistrer</button>
  <a href="{{ route('activities.index') }}" class="btn btn-secondary">Annuler</a>
</form>
@endsection
