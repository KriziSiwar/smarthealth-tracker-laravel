<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NutritionGoal extends Model
{
    protected $fillable = [
        'user_id',
        'calories',
        'protein',
        'carbohydrates',
        'fat',
        'fiber',
        'sugar',
        'sodium',
        'cholesterol',
        'water_goal_ml',
        'activity_level',
        'goal_type' // maintain, lose_weight, gain_weight, build_muscle
    ];

    protected $casts = [
        'calories' => 'integer',
        'protein' => 'integer',
        'carbohydrates' => 'integer',
        'fat' => 'integer',
        'fiber' => 'integer',
        'sugar' => 'integer',
        'sodium' => 'integer',
        'cholesterol' => 'integer',
        'water_goal_ml' => 'integer',
    ];

    /**
     * Get the user that owns the nutrition goals.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate recommended macronutrients based on user's goals
     */
    public static function calculateMacros(int $calories, string $goalType = 'maintain'): array
    {
        $macros = [
            'protein' => 0,
            'carbohydrates' => 0,
            'fat' => 0,
        ];

        switch ($goalType) {
            case 'lose_weight':
                // Higher protein (35%), moderate carbs (35%), lower fat (30%)
                $macros['protein'] = (int) round(($calories * 0.35) / 4);
                $macros['carbohydrates'] = (int) round(($calories * 0.35) / 4);
                $macros['fat'] = (int) round(($calories * 0.3) / 9);
                break;
            case 'gain_weight':
                // Higher carbs (50%), moderate protein (25%), higher fat (25%)
                $macros['protein'] = (int) round(($calories * 0.25) / 4);
                $macros['carbohydrates'] = (int) round(($calories * 0.5) / 4);
                $macros['fat'] = (int) round(($calories * 0.25) / 9);
                break;
            case 'build_muscle':
                // Higher protein (40%), moderate carbs (40%), lower fat (20%)
                $macros['protein'] = (int) round(($calories * 0.4) / 4);
                $macros['carbohydrates'] = (int) round(($calories * 0.4) / 4);
                $macros['fat'] = (int) round(($calories * 0.2) / 9);
                break;
            default: // maintain
                // Balanced approach (30% protein, 40% carbs, 30% fat)
                $macros['protein'] = (int) round(($calories * 0.3) / 4);
                $macros['carbohydrates'] = (int) round(($calories * 0.4) / 4);
                $macros['fat'] = (int) round(($calories * 0.3) / 9);
                break;
        }

        return $macros;
    }
}
