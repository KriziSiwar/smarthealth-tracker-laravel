@extends('layouts.activity')

@section('content')
<div class="pagetitle">
    <h1>Ajouter une activité</h1>
</div>

<section class="section">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Nouvelle activité</h5>

            @if (session('success'))
                <div class="alert alert-success mt-2">
                    <i class="bi bi-robot"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('activities.store') }}" method="POST" class="row g-3 mt-3">
                @csrf

                <div class="col-12">
                    <label class="form-label fw-semibold">Type d'activité</label>
                    <select name="activity_type_id" class="form-select" required>
                        <option value="">Choisir…</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" @selected(old('activity_type_id')==$type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('activity_type_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Durée (min)</label>
                    <input type="number" name="duration" class="form-control" min="1" value="{{ old('duration') }}" required>
                    @error('duration') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Calories brûlées (calculées par IA)</label>
                    <input type="text" name="calories_burned" class="form-control" readonly placeholder="Calcul automatique">
                    @error('calories_burned') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Intensité</label>
                    <select name="intensity" class="form-select" required>
                        <option value="">Choisir…</option>
                        <option value="low">Faible</option>
                        <option value="medium">Moyenne</option>
                        <option value="high">Forte</option>
                    </select>
                    @error('intensity') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date</label>
                    <input type="date" name="activity_date" class="form-control" required>
                    @error('activity_date') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" class="form-control" rows="1" placeholder="Ajoutez une remarque."></textarea>
                    @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                    <button type="submit" class="btn btn-success px-4">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- ✅ Script déplacé ici pour être sûr qu’il s’exécute même avec le template MediTrust --}}
<script>
window.addEventListener('load', function () {
    const durationInput = document.querySelector('input[name="duration"]');
    const intensitySelect = document.querySelector('select[name="intensity"]');
    const caloriesInput = document.querySelector('input[name="calories_burned"]');

    if (!durationInput || !intensitySelect || !caloriesInput) {
        console.warn("⚠️ Impossible de trouver les champs nécessaires !");
        return;
    }

    async function updateCalories() {
        const duration = durationInput.value;
        const intensity = intensitySelect.value;

        if (!duration || duration <= 0 || !intensity) {
            caloriesInput.value = "Calcul automatique";
            return;
        }

        caloriesInput.value = "⏳ Calcul...";

        try {
            const response = await fetch("http://127.0.0.1:5000/predict", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ duration, intensity })
            });

            const data = await response.json();

            if (data && data.prediction) {
                caloriesInput.value = Math.round(data.prediction);
            } else {
                caloriesInput.value = "⚠️ Réponse IA invalide";
            }
        } catch (error) {
            console.error("Erreur IA :", error);
            caloriesInput.value = "❌ Erreur serveur";
        }
    }

    durationInput.addEventListener("input", updateCalories);
    intensitySelect.addEventListener("change", updateCalories);
});
</script>
@endsection
