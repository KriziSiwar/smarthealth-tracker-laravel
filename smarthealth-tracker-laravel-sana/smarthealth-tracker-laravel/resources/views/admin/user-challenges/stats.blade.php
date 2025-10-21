@extends('layouts.admin') 

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">📈 Suivi des performances des utilisateurs</h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h5>Utilisateurs actifs</h5>
                <h3>{{ $activeUsers }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h5>Challenges terminés</h5>
                <h3>{{ $completedChallenges }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h5>Score moyen</h5>
                <h3>{{ round($averageScore, 1) }}</h3>
            </div>
        </div>
    </div>

    <hr>

    <div class="mt-4">
        <h4>📊 Scores moyens par utilisateur</h4>
        <canvas id="userScoresChart"></canvas>
    </div>

    <div class="mt-5">
        <h4>📈 Progression moyenne par challenge</h4>
        <canvas id="challengeProgressChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userScoresCtx = document.getElementById('userScoresChart').getContext('2d');
    new Chart(userScoresCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($userNames) !!},
            datasets: [{
                label: 'Score moyen',
                data: {!! json_encode($userScores) !!},
                borderWidth: 1,
                backgroundColor: 'rgba(54, 162, 235, 0.5)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            }
        }
    });

    const challengeProgressCtx = document.getElementById('challengeProgressChart').getContext('2d');
    new Chart(challengeProgressCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($challengeNames) !!},
            datasets: [{
                label: 'Progression moyenne (%)',
                data: {!! json_encode($challengeProgress) !!},
                fill: true,
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } }
        }
    });
});
</script>

@endsection
