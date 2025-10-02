<?php

namespace App\Http\Controllers;

use App\Models\WaterIntake;
use Illuminate\Http\Request;

class WaterIntakeController extends Controller
{
    /**
     * Liste toutes les prises d’eau
     */
   public function index()
{
    // SEULEMENT les prises d'eau de l'utilisateur connecté
    $waterIntakes = WaterIntake::where('user_id', auth()->id())
                              ->latest()
                              ->get();

    return view('waterintakes.index', compact('waterIntakes'));
}

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('waterintakes.create');
    }

    /**
     * Enregistre une nouvelle prise d’eau
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount_ml'   => 'required|integer|min:50',
            'intake_date' => 'required|date',
        ]);

        WaterIntake::create([
            'user_id'    => auth()->id() , // provisoire si pas de login
            'amount_ml'  => $request->amount_ml,
            'intake_date'=> $request->intake_date,
        ]);

        return redirect()->route('waterintakes.index')
                         ->with('success', '💧 Water intake added successfully!');
    }

    /**
     * Affiche une prise d’eau
     */
    public function show(WaterIntake $waterintake)
    {
        return view('waterintakes.show', compact('waterintake'));
    }

    /**
     * Formulaire d’édition
     */
  
// ...existing code...
public function edit(WaterIntake $waterintake)
{
    return view('components.water-edit', compact('waterintake'));
}
// ...existing code...

    /**
     * Met à jour une prise d’eau
     */
    public function update(Request $request, WaterIntake $waterintake)
    {
        $request->validate([
            'amount_ml'   => 'required|integer|min:50',
            'intake_date' => 'required|date',
        ]);

        $waterintake->update($request->only(['amount_ml', 'intake_date']));

        return redirect()->route('waterintakes.index')
                         ->with('success', '💧 Water intake updated successfully!');
    }

    /**
     * Supprime une prise d’eau
     */
    public function destroy(WaterIntake $waterintake)
    {
        $waterintake->delete();

        return redirect()->route('waterintakes.index')
                         ->with('success', '🗑️ Water intake deleted!');
    }
}
