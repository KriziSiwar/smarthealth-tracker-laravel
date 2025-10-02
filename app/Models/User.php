<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function waterIntakes()
{
    return $this->hasMany(WaterIntake::class);
}


public function healthMetrics()
{
    return $this->hasMany(HealthMetric::class);
}

public function getTodayHealthData()
    {
        return [
            'health_metric' => $this->healthMetrics()->today()->first(),
            'water_intakes' => $this->waterIntakes()->whereDate('intake_date', today())->get(),
            'total_water' => $this->waterIntakes()->whereDate('intake_date', today())->sum('amount_ml')
        ];
    }
}
