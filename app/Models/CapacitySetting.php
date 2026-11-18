<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapacitySetting extends Model
{
    protected $fillable = [
        'open_time',
        'close_time',
        'capacity_per_hour',
        'walk_in_reserve',
        'open_days',
        'booking_days',
        'walk_in_hours',
    ];

    /** The single settings row, created with defaults the first time. */
    public static function current(): self
    {
        return static::query()->first() ?? static::create([]);
    }

    /** Hours that are walk-in only by default, as "H:00" strings. */
    public function walkInHours(): array
    {
        return collect(explode(',', (string) $this->walk_in_hours))
            ->map(fn ($h) => trim($h))
            ->filter()
            ->values()
            ->all();
    }

    /** ISO weekday numbers the school accepts admissions, e.g. [1,2,3,4,5]. */
    public function openDays(): array
    {
        return collect(explode(',', (string) $this->open_days))
            ->map(fn ($d) => (int) $d)
            ->filter(fn ($d) => $d >= 1 && $d <= 7)
            ->values()
            ->all();
    }
}
