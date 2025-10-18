@csrf
<div class="mb-3">
  <label class="form-label">Utilisateur</label>
  <select name="user_id" class="form-control">
    <option value="">-- Aucun utilisateur --</option>
    @foreach($users as $id => $email)
      <option value="{{ $id }}" {{ (string)old('user_id', $activity->user_id ?? '') === (string)$id ? 'selected' : '' }}>
        {{ $email }}
      </option>
    @endforeach
  </select>
  @error('user_id') <div class="text-danger">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
  <label class="form-label">Type d'activité</label>
  <select name="activity_type_id" class="form-control">
    <option value="">-- choisir --</option>
    @foreach($types as $id => $name)
      <option value="{{ $id }}" {{ (string)old('activity_type_id', $activity->activity_type_id ?? '') === (string)$id ? 'selected' : '' }}>
        {{ $name }}
      </option>
    @endforeach
  </select>
  @error('activity_type_id') <div class="text-danger">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
  <label class="form-label">Durée (minutes)</label>
  <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', $activity->duration_minutes ?? '') }}">
  @error('duration_minutes') <div class="text-danger">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
  <label class="form-label">Date</label>
  <input type="date" name="activity_date" class="form-control" value="{{ old('activity_date', $activity->activity_date ?? '') }}">
  @error('activity_date') <div class="text-danger">{{ $message }}</div> @enderror
</div>
