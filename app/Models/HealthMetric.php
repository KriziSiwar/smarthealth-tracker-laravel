<?php
// app/Models/HealthMetric.php

namespace App\Models;
use App\Models\waterIntakes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'weight_kg',
        'measurement',
        'measured_at',
    ];

    protected $casts = [
        'measured_at' => 'date',
        'weight_kg' => 'float',
    ];

    // Relation avec User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec WaterIntakes par date
    public function waterIntakes()
    {
        return $this->hasMany(WaterIntake::class, 'user_id', 'user_id')
                    ->whereDate('intake_date', $this->measured_at);
    }

    // Accessor pour le total d'eau consommé ce jour-là
    public function getTotalWaterIntakeAttribute()
    {
        return $this->waterIntakes->sum('amount_ml');
    }

    // Scope pour les métriques d'aujourd'hui
    public function scopeToday($query)
    {
        return $query->whereDate('measured_at', today());
    }

    // Scope pour un utilisateur spécifique
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    // app/Models/HealthMetric.php

}