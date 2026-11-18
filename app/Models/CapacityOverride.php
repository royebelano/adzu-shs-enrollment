<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapacityOverride extends Model
{
    protected $fillable = [
        'date',
        'start_time',
        'is_closed',
        'mode',
        'capacity',
        'walk_in_reserve',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'is_closed' => 'boolean',
    ];
}
