<?php

namespace App\Http\Controllers;

use App\Models\HealthMetric;
use App\Models\WaterIntake;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HealthMetricController extends Controller
{
    /**
     * Dashboard avec données du jour et historique 7 jours
     */
   public function dashboard()
{
    $userId = auth()->id();
    
    if (!$userId) {
        return redirect()->route('login')->with('error', 'Veuillez vous connecter');
    }

    $today = now()->toDateString();

    // Données du jour
    $metricsToday = HealthMetric::where('user_id', $userId)
        ->whereDate('measured_at', $today)
        ->orderBy('created_at', 'desc')
        ->get();

    // Eau du jour pour CET utilisateur
    $waterToday = WaterIntake::where('user_id', $userId)
        ->whereDate('intake_date', $today)
        ->orderBy('created_at', 'desc')
        ->get();

    $totalWaterMl = $waterToday->sum('amount_ml');
    $latestWeight = $metricsToday->first() ? $metricsToday->first()->weight_kg : null;

    $todayData = [
        'today' => $today,
        'metrics' => $metricsToday,
        'waterIntakes' => $waterToday,
        'totalWaterMl' => $totalWaterMl,
        'latestWeight' => $latestWeight,
    ];

    // Données des 7 derniers jours AVEC jointure par date
    $weeklyMetrics = HealthMetric::with(['waterIntakes' => function($query) use ($userId) {
            $query->where('user_id', $userId);
            // Le whereDate est déjà dans la relation du modèle
        }])
        ->where('user_id', $userId)
        ->where('measured_at', '>=', now()->subDays(7))
        ->orderBy('measured_at', 'desc')
        ->get();

    return view('health-metrics.dashboard', compact('todayData', 'weeklyMetrics'));
}
    /**
     * Liste toutes les métriques de l'utilisateur
     */
    public function index()
    {
        $userId = Auth::id();
        
        if (!$userId) {
            return redirect()->route('login');
        }

        $metrics = HealthMetric::with('waterIntakes')
            ->where('user_id', $userId)
            ->orderBy('measured_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('health-metrics.index', compact('metrics'));
    }

    /**
     * Affiche une métrique spécifique
     */
    public function show($id)
    {
        $userId = Auth::id();
        
        if (!$userId) {
            return redirect()->route('login');
        }

        $metric = HealthMetric::with('waterIntakes')
            ->where('user_id', $userId)
            ->findOrFail($id);

        return view('health-metrics.show', compact('metric'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
       

        return view('health-metrics.create');
    }

    /**
     * Stocke une nouvelle métrique
     */
    public function store(Request $request)
{
    // SIMPLE ET DIRECT comme la route test
    $metric = HealthMetric::create([
        'user_id' => Auth::id(),
        'weight_kg' => $request->weight_kg,
        'measurement' => $request->measurement,
        'measured_at' => $request->measured_at,
    ]);
    
    return redirect()->route('health-metrics.index')
        ->with('success', 'Métrique créée avec ID: ' . $metric->id);
}
    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $userId = Auth::id();
        
        if (!$userId) {
            return redirect()->route('login');
        }

        $metric = HealthMetric::where('user_id', $userId)
            ->findOrFail($id);

        return view('health-metrics.edit', compact('metric'));
    }

    /**
     * Met à jour une métrique existante
     */
    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        
        if (!$userId) {
            return redirect()->route('login');
        }

        $metric = HealthMetric::where('user_id', $userId)
            ->findOrFail($id);

        $validated = $request->validate([
            
            'weight_kg'   => 'required|numeric|min:30|max:300',
            'measurement' => 'required|string|max:50',
            'measured_at' => 'required|date',
        ]);

        try {
            $metric->update($validated);

            return redirect()->route('health-metrics.index')
                ->with('success', 'Métrique mise à jour avec succès !');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprime une métrique
     */
    public function destroy($id)
    {
        $userId = Auth::id();
        
        if (!$userId) {
            return redirect()->route('login');
        }

        $metric = HealthMetric::where('user_id', $userId)
            ->findOrFail($id);

        try {
            $metric->delete();

            return redirect()->route('health.dashboard')
                ->with('success', 'Métrique supprimée avec succès !');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * API - Retourne les métriques en JSON (pour debug)
     */
    public function apiIndex()
    {
        $userId = Auth::id();
        
        if (!$userId) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $metrics = HealthMetric::where('user_id', $userId)
            ->orderBy('measured_at', 'desc')
            ->get();

        return response()->json([
            'user_id' => $userId,
            'count' => $metrics->count(),
            'data' => $metrics
        ]);
    }
}