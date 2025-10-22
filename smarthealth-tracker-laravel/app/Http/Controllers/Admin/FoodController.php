<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Rap2hpoutre\FastExcel\FastExcel;

class FoodController extends Controller
{
    /**
     * Display a listing of the foods.
     */
    public function index(Request $request)
    {
        $query = Food::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by approval status
        if ($request->has('status') && in_array($request->status, ['approved', 'pending'])) {
            $query->where('is_approved', $request->status === 'approved');
        }

        // Filter by date range
        if ($request->has('date_range') && !empty($request->date_range)) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) === 2) {
                $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['name', 'calories', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        // Eager load relationships
        $query->with('addedBy');

        // Get paginated results
        $perPage = $request->get('per_page', 15);
        $foods = $query->paginate($perPage)->withQueryString();

        // Get statistics for the dashboard
        $stats = [
            'total' => Food::count(),
            'approved' => Food::where('is_approved', true)->count(),
            'pending' => Food::where('is_approved', false)->count(),
            'avg_calories' => Food::avg('calories') ?: 0,
        ];

        return view('admin.nutrition.foods.index', compact('foods', 'stats'));
    }

    /**
     * Display the specified food item.
     */
    public function show(Food $food)
    {
        $food->load('addedBy');
        return view('admin.nutrition.foods.show', compact('food'));
    }

    /**
     * Show the form for creating a new food item.
     */
    public function create()
    {
        return view('admin.nutrition.foods.create');
    }

    /**
     * Store a newly created food item in storage.
     */
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'serving_size' => 'required|numeric|min:0.1',
            'serving_unit' => 'required|string|max:50',
            'calories' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
            'carbohydrates' => 'required|numeric|min:0',
            'fat' => 'required|numeric|min:0',
            'fiber' => 'nullable|numeric|min:0',
            'sugar' => 'nullable|numeric|min:0',
            'sodium' => 'nullable|numeric|min:0',
            'cholesterol' => 'nullable|numeric|min:0',
        ]);

        $validated['is_approved'] = true;
        $validated['added_by'] = Auth::id();

        Food::create($validated);

        return redirect()->route('admin.nutrition.foods.index')
                        ->with('success', 'Food item created successfully.');
    }

    /**
     * Show the form for editing the specified food item.
     */
    public function edit(Food $food)
    {
        return view('admin.nutrition.foods.edit', compact('food'));
    }

    /**
     * Update the specified food item in storage.
     */
    public function update(Request $request, Food $food)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'calories' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
            'carbs' => 'required|numeric|min:0',
            'fat' => 'required|numeric|min:0',
            'is_approved' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($food->image_path && Storage::exists($food->image_path)) {
                Storage::delete($food->image_path);
            }
            
            // Store new image
            $path = $request->file('image')->store('public/foods');
            $validated['image_path'] = $path;
        }

        $food->update($validated);

        return redirect()->route('admin.nutrition.foods.index')
                        ->with('success', 'L\'aliment a été mis à jour avec succès.');
    }

    /**
     * Remove the specified food item from storage.
     */
    public function destroy(Food $food)
    {
        // Delete associated image if exists
        if ($food->image_path && Storage::exists($food->image_path)) {
            Storage::delete($food->image_path);
        }
        
        $food->delete();

        return redirect()->route('admin.nutrition.foods.index')
                         ->with('success', 'L\'aliment a été supprimé avec succès.');
    }

    /**
     * Approve the specified food item.
     */
    public function approve(Food $food)
    {
        $food->update(['is_approved' => true]);
        
        return redirect()->back()
                         ->with('success', 'L\'aliment a été approuvé avec succès.');
    }

    /**
     * Handle bulk actions for selected foods.
     */
    public function bulkActions(Request $request)
    {
        $action = $request->input('action');
        $selectedIds = $request->input('selected_ids', []);
        
        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Aucun aliment sélectionné.');
        }
        
        switch ($action) {
            case 'approve':
                Food::whereIn('id', $selectedIds)->update(['is_approved' => true]);
                $message = 'Les aliments sélectionnés ont été approuvés avec succès.';
                break;
                
            case 'delete':
                $foods = Food::whereIn('id', $selectedIds)->get();
                foreach ($foods as $food) {
                    // Delete associated image if exists
                    if ($food->image_path && Storage::exists($food->image_path)) {
                        Storage::delete($food->image_path);
                    }
                    $food->delete();
                }
                $message = 'Les aliments sélectionnés ont été supprimés avec succès.';
                break;
                
            default:
                return redirect()->back()->with('error', 'Action non valide.');
        }
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Export foods data in various formats.
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'xlsx');
        $columns = $request->input('columns', ['name', 'calories', 'protein', 'carbs', 'fat', 'is_approved']);
        $filters = json_decode($request->input('filters', '{}'), true);
        
        $query = Food::query();
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if (!empty($filters['status']) && in_array($filters['status'], ['approved', 'pending'])) {
            $query->where('is_approved', $filters['status'] === 'approved');
        }
        
        if (!empty($filters['date_range'])) {
            $dates = explode(' - ', $filters['date_range']);
            if (count($dates) === 2) {
                $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }
        
        $foods = $query->get();
        
        // Map the data for export
        $exportData = $foods->map(function($food) use ($columns) {
            $data = [];
            
            if (in_array('name', $columns)) $data['Nom'] = $food->name;
            if (in_array('description', $columns)) $data['Description'] = $food->description;
            if (in_array('calories', $columns)) $data['Calories (kcal)'] = $food->calories;
            if (in_array('protein', $columns)) $data['Protéines (g)'] = $food->protein;
            if (in_array('carbs', $columns)) $data['Glucides (g)'] = $food->carbs;
            if (in_array('fat', $columns)) $data['Lipides (g)'] = $food->fat;
            if (in_array('is_approved', $columns)) $data['Statut'] = $food->is_approved ? 'Approuvé' : 'En attente';
            if (in_array('created_at', $columns)) $data['Date d\'ajout'] = $food->created_at->format('d/m/Y H:i');
            
            return $data;
        });
        
        $fileName = 'aliments-export-' . now()->format('Y-m-d-H-i-s');
        
        switch ($format) {
            case 'csv':
                $filePath = storage_path('app/' . $fileName . '.csv');
                (new FastExcel($exportData))->export($filePath);
                return response()->download($filePath)->deleteFileAfterSend(true);
                
            case 'xlsx':
                $filePath = storage_path('app/' . $fileName . '.xlsx');
                (new FastExcel($exportData))->export($filePath);
                return response()->download($filePath)->deleteFileAfterSend(true);
                
            case 'pdf':
                $pdf = PDF::loadView('admin.nutrition.foods.export-pdf', [
                    'foods' => $exportData,
                    'columns' => $columns,
                    'title' => 'Export des aliments - ' . now()->format('d/m/Y')
                ]);
                
                return $pdf->download($fileName . '.pdf');
                
            default:
                return back()->with('error', 'Format d\'export non valide.');
        }
    }

    /**
     * Toggle the approval status of a food item.
     */
    public function toggleApproval(Food $food)
    {
        $food->update(['is_approved' => !$food->is_approved]);
        
        $status = $food->is_approved ? 'approuvé' : 'en attente';
        
        return back()->with('success', "L'aliment a été marqué comme {$status}.");
    }
}
