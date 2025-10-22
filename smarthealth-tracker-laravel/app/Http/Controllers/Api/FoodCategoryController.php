<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FoodCategoryController extends Controller
{
    /**
     * Display a listing of food categories.
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'search' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'sort_by' => 'nullable|in:name,sort_order,created_at',
            'sort_order' => 'nullable|in:asc,desc',
            'with_food_count' => 'nullable|boolean',
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
        $isActive = $request->input('is_active');
        $sortBy = $request->input('sort_by', 'sort_order');
        $sortOrder = $request->input('sort_order', 'asc');
        $withFoodCount = $request->boolean('with_food_count', false);

        $query = FoodCategory::query();

        // Apply search filter
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Apply active filter
        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        // Load food count if requested
        if ($withFoodCount) {
            $query->withCount(['foods' => function($q) {
                $q->where('is_active', true);
            }]);
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Get paginated results
        $categories = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Store a newly created food category in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:food_categories,name',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $data = $request->only([
            'name', 'description', 'icon', 'color', 'is_active', 'sort_order'
        ]);

        // Generate slug from name
        $data['slug'] = $this->createSlug($request->name);

        // Handle image upload if present
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('food-categories', 'public');
            $data['image_path'] = $path;
        }

        $category = FoodCategory::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Food category created successfully',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified food category.
     */
    public function show(string $id): JsonResponse
    {
        $category = FoodCategory::withCount(['foods' => function($q) {
            $q->where('is_active', true);
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Update the specified food category in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $category = FoodCategory::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('food_categories', 'name')->ignore($category->id)
            ],
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $data = $request->only([
            'name', 'description', 'icon', 'color', 'is_active', 'sort_order'
        ]);

        // Update slug if name is being updated
        if ($request->has('name') && $request->name !== $category->name) {
            $data['slug'] = $this->createSlug($request->name, $category->id);
        }

        // Handle image upload if present
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image_path) {
                \Storage::disk('public')->delete($category->image_path);
            }
            
            $path = $request->file('image')->store('food-categories', 'public');
            $data['image_path'] = $path;
        } elseif ($request->boolean('remove_image') && $category->image_path) {
            // Remove image if requested
            \Storage::disk('public')->delete($category->image_path);
            $data['image_path'] = null;
        }

        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Food category updated successfully',
            'data' => $category->fresh()
        ]);
    }

    /**
     * Remove the specified food category from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $category = FoodCategory::findOrFail($id);
        
        // Check if the category has associated foods
        if ($category->foods()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category that has associated foods.'
            ], 422);
        }

        // Delete image if exists
        if ($category->image_path) {
            \Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Food category deleted successfully'
        ]);
    }

    /**
     * Get all active food categories with food count.
     */
    public function activeWithCount(): JsonResponse
    {
        $categories = FoodCategory::withCount(['foods' => function($q) {
            $q->where('is_active', true);
        }])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get popular food categories with food count.
     */
    public function popular(int $limit = 6): JsonResponse
    {
        $categories = FoodCategory::withCount(['foods' => function($q) {
            $q->where('is_active', true);
        }])
        ->where('is_active', true)
        ->orderBy('foods_count', 'desc')
        ->limit($limit)
        ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Toggle the active status of a food category.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $category = FoodCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Food category status updated successfully',
            'data' => [
                'id' => $category->id,
                'is_active' => $category->is_active
            ]
        ]);
    }

    /**
     * Create a URL-friendly slug from a string.
     */
    private function createSlug(string $name, int $id = null): string
    {
        $slug = Str::slug($name);
        $count = FoodCategory::where('slug', 'like', "{$slug}%");
        
        if ($id) {
            $count->where('id', '!=', $id);
        }
        
        $count = $count->count();
        
        return $count ? "{$slug}-{$count}" : $slug;
    }
}
