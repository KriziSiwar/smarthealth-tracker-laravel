{{-- resources/views/health-metrics/index.blade.php --}}

<h2>Mes Métriques Santé</h2>

<a href="{{ route('health-metrics.create') }}" style="background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 20px;">
    + Ajouter une Métrique
</a>

@foreach($metrics as $metric)
<div style="background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #007bff;">
    <h3>{{ $metric->measured_at->format('d/m/Y') }} - {{ $metric->measurement }}</h3>
    <p><strong>Poids:</strong> {{ $metric->weight_kg }} kg</p>
    <p><strong>Eau bue ce jour:</strong> {{ $metric->total_water_intake }} ml</p>
    
    @if($metric->waterIntakes->count() > 0)
    <div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 4px;">
        <strong>Détails eau:</strong>
        @foreach($metric->waterIntakes as $water)
        <span style="display: inline-block; background: #e3f2fd; padding: 2px 8px; margin: 2px; border-radius: 3px;">
            {{ $water->amount_ml }}ml
        </span>
        @endforeach
    </div>
    @endif
</div>
@endforeach

{{ $metrics->links() }}
