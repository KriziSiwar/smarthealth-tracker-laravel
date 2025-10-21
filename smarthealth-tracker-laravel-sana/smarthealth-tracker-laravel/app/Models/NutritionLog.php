<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NutritionLog extends Model
{
    protected $fillable = [
        'user_id',
        'food_id',
        'serving_size',
        'serving_unit',
        'meal_type',
        'consumed_at',
        'calories',
        'protein',
        'carbohydrates',
        'fat',
        'notes',
    ];

    protected $casts = [
        'consumed_at' => 'datetime',
        'serving_size' => 'float',
        'calories' => 'float',
        'protein' => 'float',
        'carbohydrates' => 'float',
        'fat' => 'float',
    ];

    protected $with = ['food'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }

    // Calculate nutrition based on serving size
    public function calculateNutrition()
    {
        if (!$this->food) {
            return;
        }

        $ratio = $this->serving_size / $this->food->serving_size;
        
        $this->calories = round($this->food->calories * $ratio, 2);
        $this->protein = round($this->food->protein * $ratio, 2);
        $this->carbohydrates = round($this->food->carbohydrates * $ratio, 2);
        $this->fat = round($this->food->fat * $ratio, 2);
    }

    protected static function booted()
    {
        static::saving(function ($log) {
            if ($log->isDirty(['serving_size', 'food_id'])) {
                $log->calculateNutrition();
            }
        });
    }
}