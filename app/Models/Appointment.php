<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    protected $fillable =[
        'user_id',
        'client_id',
        'service_id',
        'start_time',
        'end_time',
        'total_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];


    //Cada agendamento pertence a um usuário (barbeiro).

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    //Cada agendamento pertence a um cliente.
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    

    //Um agendamento pertence a um serviço.
    public function services(): BelongsTo
    {
        return $this->belongsTo(Service::class);

    }


    //Cada agendamento tem um pagamento associado.
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
