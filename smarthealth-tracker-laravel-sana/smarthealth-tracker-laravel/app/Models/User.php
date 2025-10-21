<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // ✅ ajouté
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

      public function mealPlans()
    {
        return $this->hasMany(MealPlan::class);
    }

    /**
     * Obtenez les journaux alimentaires de l'utilisateur.
     */
    public function foodLogs()
    {
        return $this->hasMany(FoodLog::class);
    }

    /** ✅ Helpers pour vérifier le rôle */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }
    public function challenges()
{
    return $this->belongsToMany(Challenge::class, 'user_challenges')
                ->withPivot('progress', 'completed', 'score', 'start_date', 'end_date', 'status')
                ->withTimestamps();
}
public function activities() { return $this->hasMany(Activity::class); }
}
