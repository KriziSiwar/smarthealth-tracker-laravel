@extends('layouts.admin')

@section('content')
<div class="pagetitle">
    <h1>Créer un type d’activité</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.activity-types.index') }}">Types d’activités</a></li>
            <li class="breadcrumb-item active">Créer</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Nouveau type d’activité</h5>

            <form method="POST" action="{{ route('admin.activity-types.store') }}" class="row g-3">
                @csrf

                <div class="col-12">
                    <label class="form-label">Nom</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.activity-types.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                    <button class="btn btn-success px-4">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
