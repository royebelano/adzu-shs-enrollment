<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapacitySlot extends Model
{
    public const MODES = ['online', 'walkin', 'closed'];

    protected $fillable = [
        'date',
        'start_time',
        'mode',
        'capacity',
    ];

    protected $casts = [
        'date' => 'date',
        'capacity' => 'integer',
    ];
}
