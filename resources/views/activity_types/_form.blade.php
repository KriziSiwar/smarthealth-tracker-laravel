@csrf
<div class="mb-3">
  <label class="form-label">Nom du type d'activité</label>
  <input type="text" name="name" class="form-control" value="{{ old('name', $activityType->name ?? '') }}">
  @error('name') <div class="text-danger">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
  <label class="form-label">Calories par minute</label>
  <input type="number" step="0.01" name="calories_per_minute" class="form-control"
         value="{{ old('calories_per_minute', $activityType->calories_per_minute ?? '') }}">
  @error('calories_per_minute') <div class="text-danger">{{ $message }}</div> @enderror
</div>
