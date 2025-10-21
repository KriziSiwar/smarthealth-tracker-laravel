<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Food extends Model
{
    protected $table = 'foods';

    protected $fillable = [
        'name',
        'description',
        'serving_size',
        'serving_unit',
        'calories',
        'protein',
        'carbohydrates',
        'fat',
        'fiber',
        'sugar',
        'sodium',
        'cholesterol',
        'is_approved',
        'added_by',
    ];

    protected $casts = [
        'calories' => 'float',
        'protein' => 'float',
        'carbohydrates' => 'float',
        'fat' => 'float',
        'fiber' => 'float',
        'sugar' => 'float',
        'sodium' => 'float',
        'cholesterol' => 'float',
        'is_approved' => 'boolean',
    ];

    public function mealPlans(): BelongsToMany
    {
        return $this->belongsToMany(MealPlan::class, 'meal_plan_food')
            ->withPivot('serving_size', 'serving_unit', 'meal_type')
            ->withTimestamps();
    }

    public function nutritionLogs(): HasMany
    {
        return $this->hasMany(NutritionLog::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}