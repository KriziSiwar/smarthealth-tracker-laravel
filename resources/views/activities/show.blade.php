@extends('layouts.simple')
@section('content')
<h1>Détail activité #{{ $activity->id }}</h1>
<ul>
  <li>Utilisateur : {{ $activity->user?->email ?? '—' }}</li>
  <li>Type : {{ $activity->activityType?->name }}</li>
  <li>Durée : {{ $activity->duration_minutes }} min</li>
  <li>Date : {{ $activity->activity_date->format('d/m/Y') }}</li>
  <li>Créée le : {{ $activity->created_at->format('d/m/Y H:i') }}</li>
</ul>
<a href="{{ route('activities.index') }}" class="btn btn-secondary">Retour</a>
@endsection
