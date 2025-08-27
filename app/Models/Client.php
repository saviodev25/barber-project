<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Client extends Model
{
    use HasApiTokens, Notifiable;
    
    protected $fillable = [
        'name',
        'phone'
    ];
    /**
     * Um cliente pode ter vários agendamentos.
    */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
