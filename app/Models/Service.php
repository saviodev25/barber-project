<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    //Um serviço pode ter vários agendamentos.
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
