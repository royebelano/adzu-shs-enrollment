<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AppointmentSchedule;
use Carbon\Carbon;

class UpdateExpiredSchedules extends Command
{
    // Ang command na i-type mo sa terminal
    protected $signature = 'schedules:update-expired';

    // Deskripsyon ng command
    protected $description = 'Awtomatikong sinasara ang mga appointment schedules na lumipas na ang petsa.';

    public function handle()
    {
        // Hanapin ang mga 'Available' pa pero lumipas na ang schedule_date
        $expiredSchedules = AppointmentSchedule::where('status', 'Available')
            ->where('schedule_date', '<', Carbon::today())
            ->get();

        if ($expiredSchedules->isEmpty()) {
            $this->info('Walang lumang schedule na kailangang baguhin.');
            return;
        }

        foreach ($expiredSchedules as $schedule) {
            $schedule->update(['status' => 'Closed']);
        }

        $this->info('Matagumpay na na-update at sinara ang ' . $expiredSchedules->count() . ' lumipas na schedule.');
    }
}
