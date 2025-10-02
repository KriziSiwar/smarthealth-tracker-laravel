<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WaterIntakeController;
use App\Http\Controllers\HealthMetricController;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});


// Auth perso
// Auth perso
Route::get('register', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('register', [AuthController::class, 'register'])->name('register');

Route::get('login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('health-metrics', HealthMetricController::class);


// Page protégée
Route::middleware(['auth'])->group(function () {
    // MET LA ROUTE DASHBOARD AVANT LA RESSOURCE
    Route::get('/dashboard-sante', [HealthMetricController::class, 'dashboard'])->name('health.dashboard');
    
    // Ensuite la resource
        Route::resource('health-metrics', HealthMetricController::class);

    Route::resource('waterintakes', WaterIntakeController::class);
    // routes/web.php - REMPLACE ta route store par ceci :

// routes/web.php - AJOUTE EN HAUT

// PUIS la route
// routes/web.php - AJOUTE
Route::post('/health-metrics-fix', function(Request $request) {
    $metric = App\Models\HealthMetric::create([
        'user_id' => Auth::id(),
        'weight_kg' => $request->weight_kg,
        'measurement' => $request->measurement,
        'measured_at' => $request->measured_at,
    ]);
    
    return redirect()->route('health.dashboard')
        ->with('success', 'Métrique créée avec ID: ' . $metric->id);
});
// routes/web.php - AJOUTE CETTE ROUTE
Route::get('/check-routes', function() {
    echo "<h3>Vérification des routes Health Metrics:</h3>";
    
    $routes = [
        'health-metrics.store' => route('health-metrics.store'),
        'health-metrics.index' => route('health-metrics.index'),
        'health-metrics.create' => route('health-metrics.create'),
    ];
    
    foreach ($routes as $name => $url) {
        echo "{$name}: {$url}<br>";
    }
    
    echo "<hr><h4>Toutes les routes health-metrics:</h4>";
    $allRoutes = Route::getRoutes();
    foreach ($allRoutes as $route) {
        if (str_contains($route->uri(), 'health-metrics')) {
            echo $route->methods()[0] . " - " . $route->uri() . " - " . ($route->getName() ?? 'no name') . "<br>";
        }
    }
});
});

