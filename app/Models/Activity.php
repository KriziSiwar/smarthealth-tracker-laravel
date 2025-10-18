<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_type_id',
        'duration',
        'calories_burned',
        'intensity',
        'activity_date',
        'notes',
    ];

    /**
     * Relation : une activité appartient à un type d’activité
     */
    public function type()
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id');
    }
}
