<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealPlan extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_public',
        'date',
        'calories',
        'protein',
        'carbohydrates',
        'fat',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'date' => 'date',
        'calories' => 'float',
        'protein' => 'float',
        'carbohydrates' => 'float',
        'fat' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function foods(): BelongsToMany
    {
        return $this->belongsToMany(Food::class, 'meal_plan_food')
            ->withPivot('serving_size', 'serving_unit', 'meal_type')
            ->withTimestamps();
    }

    public function calculateNutrition()
    {
        $this->calories = 0;
        $this->protein = 0;
        $this->carbohydrates = 0;
        $this->fat = 0;

        foreach ($this->foods as $food) {
            $ratio = $food->pivot->serving_size / $food->serving_size;
            
            $this->calories += round($food->calories * $ratio, 2);
            $this->protein += round($food->protein * $ratio, 2);
            $this->carbohydrates += round($food->carbohydrates * $ratio, 2);
            $this->fat += round($food->fat * $ratio, 2);
        }
    }

    protected static function booted()
    {
        static::saved(function ($mealPlan) {
            $mealPlan->calculateNutrition();
            $mealPlan->saveQuietly(); // Prevent infinite loop
        });
    }
}