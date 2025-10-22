<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'target_value',
        'unit',
        'reward_points',
        'difficulty',
        'category',
        'status',
        'created_by',
    ];

    // 🔗 Relations
 protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    // Un challenge peut être lié à plusieurs utilisateurs via la table pivot
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_challenges')
                    ->withPivot('progress', 'completed', 'score', 'start_date', 'end_date', 'status')
                    ->withTimestamps();
    }

    // Créateur du challenge (admin ou user)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
