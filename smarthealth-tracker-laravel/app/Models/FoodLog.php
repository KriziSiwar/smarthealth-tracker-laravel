<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodLog extends Model
{
    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'food_name',
        'calories',
        'protein',
        'carbs',
        'fat',
        'serving_size',
        'serving_unit',
        'meal_type',
        'consumed_at',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'calories' => 'decimal:2',
        'protein' => 'decimal:2',
        'carbs' => 'decimal:2',
        'fat' => 'decimal:2',
        'serving_size' => 'decimal:2',
        'consumed_at' => 'datetime',
    ];

    /**
     * Obtenez l'utilisateur propriétaire de ce journal alimentaire.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
