@extends('layouts.admin')

@section('title', 'Détails du Challenge')
@section('page-title', 'Détails du Challenge')

@section('content')
    <h3>{{ $challenge->title }}</h3>
    <p>{{ $challenge->description }}</p>
    <p>Début : {{ \Carbon\Carbon::parse($challenge->start_date)->format('d/m/Y') }}</p>
    <p>Fin : {{ \Carbon\Carbon::parse($challenge->end_date)->format('d/m/Y') }}</p>
    <p>Objectif : {{ $challenge->target_value }} {{ $challenge->unit }}</p>
    <p>Points de récompense : {{ $challenge->reward_points }}</p>
    <p>Difficulté : {{ ucfirst($challenge->difficulty) }}</p>
    <p>Catégorie : {{ ucfirst($challenge->category) }}</p>
    <p>Statut : {{ ucfirst($challenge->status) }}</p>

    <a href="{{ route('admin.challenges.index') }}" class="btn btn-secondary mt-3">Retour à la liste</a>
@endsection
