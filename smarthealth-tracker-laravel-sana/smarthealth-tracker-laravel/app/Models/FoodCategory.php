<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the foods for the category.
     */
    public function foods(): HasMany
    {
        return $this->hasMany(Food::class);
    }

    /**
     * Get active categories with their food count
     */
    public static function getActiveWithCount()
    {
        return self::withCount(['foods' => function($query) {
            $query->where('is_active', true);
        }])
        ->where('is_active', true)
        ->orderBy('name')
        ->get();
    }

    /**
     * Get popular categories with food count
     */
    public static function getPopularCategories($limit = 6)
    {
        return self::withCount(['foods' => function($query) {
            $query->where('is_active', true);
        }])
        ->where('is_active', true)
        ->orderBy('foods_count', 'desc')
        ->limit($limit)
        ->get();
    }
}
