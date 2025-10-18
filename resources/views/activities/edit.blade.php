@extends('layouts.activity')

@section('content')
<div class="pagetitle">
    <h1>Modifier une activité</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('activities.index') }}">Activités</a></li>
            <li class="breadcrumb-item active">Modifier</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Édition de l’activité</h5>

            <form action="{{ route('activities.update', $activity) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-12">
                    <label class="form-label fw-semibold">Type d'activité</label>
                    <select name="activity_type_id" class="form-select" required>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" @selected(old('activity_type_id', $activity->activity_type_id)==$type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('activity_type_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Durée (min)</label>
                    <input type="number" name="duration" class="form-control" min="1" value="{{ old('duration', $activity->duration) }}" required>
                    @error('duration') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Calories brûlées</label>
                    <input type="number" name="calories_burned" class="form-control" min="0" value="{{ old('calories_burned', $activity->calories_burned) }}">
                    @error('calories_burned') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Intensité</label>
                    <select name="intensity" class="form-select" required>
                        <option value="low"    @selected(old('intensity', $activity->intensity)==='low')>Faible</option>
                        <option value="medium" @selected(old('intensity', $activity->intensity)==='medium')>Moyenne</option>
                        <option value="high"   @selected(old('intensity', $activity->intensity)==='high')>Forte</option>
                    </select>
                    @error('intensity') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date</label>
                    <input type="date" name="activity_date" class="form-control" value="{{ old('activity_date', \Illuminate\Support\Str::of($activity->activity_date)->substr(0,10)) }}" required>
                    @error('activity_date') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" class="form-control" rows="1">{{ old('notes', $activity->notes) }}</textarea>
                    @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                    <button type="submit" class="btn btn-primary px-4">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
