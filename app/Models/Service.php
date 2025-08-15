<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'active',
        'duration_minutes',
        'image',
    ];

    //Um serviço pode estar em vários agendamentos
    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class)
                    ->withPivot('price')
                    ->withTimestamps();
    }
}
