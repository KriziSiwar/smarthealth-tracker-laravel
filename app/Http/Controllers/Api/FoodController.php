<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\FoodCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FoodController extends Controller
{
    /**
     * Display a listing of foods with pagination and filters.
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'search' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:food_categories,id',
            'sort_by' => 'nullable|in:name,calories,protein,carbohydrates,fat,created_at',
            'sort_order' => 'nullable|in:asc,desc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');

        $query = Food::with('category')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });

        $foods = $query->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $foods
        ]);
    }

    /**
     * Store a newly created food item in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'category_id' => 'required|exists:food_categories,id',
            'serving_size' => 'required|numeric|min:0.1',
            'serving_unit' => 'required|string|max:20',
            'calories' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
            'carbohydrates' => 'required|numeric|min:0',
            'fat' => 'required|numeric|min:0',
            'fiber' => 'nullable|numeric|min:0',
            'sugar' => 'nullable|numeric|min:0',
            'sodium' => 'nullable|numeric|min:0',
            'cholesterol' => 'nullable|numeric|min:0',
            'is_verified' => 'boolean',
            'is_common' => 'boolean',
            'barcode' => 'nullable|string|max:50|unique:foods,barcode',
            'image_url' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $food = Food::create([
            'name' => $request->name,
            'slug' => $this->createSlug($request->name),
            'brand' => $request->brand,
            'category_id' => $request->category_id,
            'serving_size' => $request->serving_size,
            'serving_unit' => $request->serving_unit,
            'calories' => $request->calories,
            'protein' => $request->protein,
            'carbohydrates' => $request->carbohydrates,
            'fat' => $request->fat,
            'fiber' => $request->fiber ?? 0,
            'sugar' => $request->sugar ?? 0,
            'sodium' => $request->sodium ?? 0,
            'cholesterol' => $request->cholesterol ?? 0,
            'is_verified' => $request->boolean('is_verified', false),
            'is_common' => $request->boolean('is_common', false),
            'barcode' => $request->barcode,
            'image_url' => $request->image_url,
            'added_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Food item created successfully',
            'data' => $food->load('category')
        ], 201);
    }

    /**
     * Display the specified food item.
     */
    public function show(string $id): JsonResponse
    {
        $food = Food::with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $food
        ]);
    }

    /**
     * Update the specified food item in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $food = Food::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'category_id' => 'sometimes|required|exists:food_categories,id',
            'serving_size' => 'sometimes|required|numeric|min:0.1',
            'serving_unit' => 'sometimes|required|string|max:20',
            'calories' => 'sometimes|required|numeric|min:0',
            'protein' => 'sometimes|required|numeric|min:0',
            'carbohydrates' => 'sometimes|required|numeric|min:0',
            'fat' => 'sometimes|required|numeric|min:0',
            'fiber' => 'nullable|numeric|min:0',
            'sugar' => 'nullable|numeric|min:0',
            'sodium' => 'nullable|numeric|min:0',
            'cholesterol' => 'nullable|numeric|min:0',
            'is_verified' => 'boolean',
            'is_common' => 'boolean',
            'barcode' => 'nullable|string|max:50|unique:foods,barcode,' . $food->id,
            'image_url' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $data = $request->all();
        
        // Update slug if name is being updated
        if ($request->has('name') && $request->name !== $food->name) {
            $data['slug'] = $this->createSlug($request->name, $food->id);
        }

        $food->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Food item updated successfully',
            'data' => $food->load('category')
        ]);
    }

    /**
     * Remove the specified food item from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $food = Food::findOrFail($id);
        
        // Check if the food item is being used in any logs or meal plans
        $inUse = $food->nutritionLogs()->exists() || $food->mealPlanItems()->exists();
        
        if ($inUse) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete food item that is being used in logs or meal plans.'
            ], 422);
        }

        $food->delete();

        return response()->json([
            'success' => true,
            'message' => 'Food item deleted successfully'
        ]);
    }

    /**
     * Search for food items by name or barcode.
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2|max:100',
            'category_id' => 'nullable|exists:food_categories,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $query = $request->input('query');
        $categoryId = $request->input('category_id');
        $perPage = $request->input('per_page', 15);

        $results = Food::with('category')
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('brand', 'like', "%{$query}%")
                  ->orWhere('barcode', $query);
            })
            ->when($categoryId, function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Get popular food items.
     */
    public function popular(): JsonResponse
    {
        $popularFoods = Food::where('is_common', true)
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $popularFoods
        ]);
    }

    /**
     * Get recently added food items.
     */
    public function recent(): JsonResponse
    {
        $recentFoods = Food::with('category')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $recentFoods
        ]);
    }

    /**
     * Create a URL-friendly slug from a string.
     */
    private function createSlug(string $name, int $id = null): string
    {
        $slug = Str::slug($name);
        $count = Food::where('slug', 'like', "{$slug}%");
        
        if ($id) {
            $count->where('id', '!=', $id);
        }
        
        $count = $count->count();
        
        return $count ? "{$slug}-{$count}" : $slug;
    }
}
