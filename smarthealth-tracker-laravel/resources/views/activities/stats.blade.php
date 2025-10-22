@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">📊 Statistiques des activités</h2>

    <h4>Nombre d'activités par type</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Type d'activité</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activitiesByType as $item)
                <tr>
                    <td>{{ $types[$item->activity_type_id] ?? 'Inconnu' }}</td>
                    <td>{{ $item->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Calories brûlées par type</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Type d'activité</th>
                <th>Total Calories</th>
            </tr>
        </thead>
        <tbody>
            @foreach($caloriesByType as $item)
                <tr>
                    <td>{{ $types[$item->activity_type_id] ?? 'Inconnu' }}</td>
                    <td>{{ round($item->total_calories, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
