<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppointmentSchedule;

class AppointmentScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Halimbawa ng mga iskedyul para sa enrollment appointment
        AppointmentSchedule::create([
            'schedule_date' => now()->addDays(3)->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'online_capacity' => 20,
            'walkin_capacity' => 5,
            'status' => 'Available',
        ]);

        AppointmentSchedule::create([
            'schedule_date' => now()->addDays(3)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'online_capacity' => 20,
            'walkin_capacity' => 5,
            'status' => 'Available',
        ]);
    }
}
