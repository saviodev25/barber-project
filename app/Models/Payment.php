<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'appointment_id',
        'amount',
        'payment_method',
        'status',
        'payment_date'
        
    ];
    
    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function appoiment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
