<div class="card mb-3 shadow-sm">
    <div class="card-body">
        <h5 class="card-title">💧 {{ $amount }} ml</h5>
        <p class="card-text">📅 {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>

        <!-- Contenu supplémentaire venant de $slot -->
        <div>
            {{ $slot }}
        </div>
    </div>
</div>
