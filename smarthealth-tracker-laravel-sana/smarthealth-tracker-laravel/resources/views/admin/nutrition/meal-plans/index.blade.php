@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Gestion des plans de repas</h1>
        <a href="{{ route('admin.nutrition.meal-plans.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nouveau plan de repas
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des plans de repas</h6>
            <div class="d-flex">
                <form action="{{ route('admin.nutrition.meal-plans.index') }}" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Rechercher..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            @if($mealPlans->isEmpty())
                <div class="alert alert-info">
                    Aucun plan de repas trouvé.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Calories</th>
                                <th>Protéines</th>
                                <th>Glucides</th>
                                <th>Lipides</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mealPlans as $mealPlan)
                                <tr>
                                    <td>{{ $mealPlan->name }}</td>
                                    <td>{{ Str::limit($mealPlan->description, 50) }}</td>
                                    <td>{{ number_format($mealPlan->calories, 0) }} kcal</td>
                                    <td>{{ number_format($mealPlan->protein, 1) }}g</td>
                                    <td>{{ number_format($mealPlan->carbohydrates, 1) }}g</td>
                                    <td>{{ number_format($mealPlan->fat, 1) }}g</td>
                                    <td>{{ $mealPlan->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.nutrition.meal-plans.show', $mealPlan) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.nutrition.meal-plans.edit', $mealPlan) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.nutrition.meal-plans.destroy', $mealPlan) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce plan de repas ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $mealPlans->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
</style>
@endpush

@push('scripts')
<script>
    // Scripts JavaScript si nécessaire
</script>
@endpush
@endsection
