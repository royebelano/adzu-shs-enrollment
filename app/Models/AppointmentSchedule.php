<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_date',
        'start_time',
        'end_time',
        'max_slots',
        'walk_in_slots',
        'mode',        // <--- Siguraduhing andito ito!
        'is_active'
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'schedule_id');
    }
}
