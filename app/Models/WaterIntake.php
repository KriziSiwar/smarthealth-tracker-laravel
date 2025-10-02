<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterIntake extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
            'health_metric_id',  // ← AJOUTE ÇA

        'amount_ml',
        
        'intake_date',
    ];


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

}
