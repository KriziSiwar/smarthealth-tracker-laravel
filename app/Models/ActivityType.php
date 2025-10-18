<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Relation : un type d’activité peut avoir plusieurs activités
     */
    public function activities()
    {
        return $this->hasMany(Activity::class, 'activity_type_id');
    }
}
