<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkBreak extends Model
{
    /** @use HasFactory<\Database\Factories\WorkBreakFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];
}
