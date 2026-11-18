<?php

namespace App\Services;

use App\Models\CapacitySlot;
use App\Models\Enrollment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Admission schedule, set date by date.
 *
 * Staff add the dates and the hours of each date. Every hour has a capacity
 * and a mode:
 *   online : students can book it on the enrollment form
 *   walkin : hidden from online booking (students see "Walk-in only");
 *            staff fill in walk-in students for that hour
 *   closed : nobody can be scheduled
 *
 * A date with no hours is simply not offered to students.
 */
class AppointmentSlots
{
    /** "08:00" becomes "8:00 – 9:00 AM". "11:00" becomes "11:00 AM – 12:00 PM". */
    public static function label(string $start): string
    {
        $from = Carbon::createFromFormat('H:i', substr($start, 0, 5));
        $to = $from->copy()->addHour();

        return $from->format('A') === $to->format('A')
            ? $from->format('g:i') . ' – ' . $to->format('g:i A')
            : $from->format('g:i A') . ' – ' . $to->format('g:i A');
    }

    /** True when students cannot be scheduled on this date (no hours, or all closed). */
    public function dayClosed(Carbon $date): bool
    {
        return !CapacitySlot::query()
            ->whereDate('date', $date->toDateString())
            ->where('mode', '!=', 'closed')
            ->exists();
    }

    /**
     * The hours of one date with their mode, capacity and seats used.
     *
     * @return array<int, array<string, mixed>>
     */
    public function blocksFor(Carbon $date): array
    {
        $day = $date->toDateString();

        $slots = CapacitySlot::query()
            ->whereDate('date', $day)
            ->orderBy('start_time')
            ->get();

        $students = Enrollment::query()
            ->whereDate('appointment_date', $day)
            ->whereNotNull('appointment_time')
            ->get(['appointment_time', 'is_walk_in']);

        return $this->build($day, $slots, $students);
    }

    /** Same as blocksFor(). Students pick one of these hours. */
    public function slotsFor(Carbon $date): array
    {
        return $this->blocksFor($date);
    }

    private function build(string $day, Collection $slots, Collection $students): array
    {
        $perHour = $students->groupBy(fn ($e) => substr($e->appointment_time, 0, 2) . ':00');

        $now = now();
        $blocks = [];

        foreach ($slots as $slot) {
            $time = substr($slot->start_time, 0, 5);
            $capacity = (int) $slot->capacity;

            $rows = $perHour->get($time, collect());
            $online = $rows->where('is_walk_in', false)->count();
            $walkIns = $rows->where('is_walk_in', true)->count();
            $booked = $online + $walkIns;

            $closed = $slot->mode === 'closed';
            $mode = $slot->mode === 'walkin' ? 'walkin' : ($closed ? 'closed' : 'online');
            $left = max(0, $capacity - $booked);

            $endsAt = Carbon::createFromFormat('Y-m-d H:i', $day . ' ' . $time)->addHour();
            $past = $endsAt->lte($now);

            $blocks[] = [
                'time' => $time,
                'label' => self::label($time),
                'mode' => $mode,
                'capacity' => $capacity,
                'online' => $online,
                'walk_ins' => $walkIns,
                'booked' => $booked,
                'left' => $left,
                'available' => $left,
                'closed' => $closed,
                'hour_closed' => $closed,
                'past' => $past,
                'bookable' => $mode === 'online' && !$past && $left > 0,
            ];
        }

        return $blocks;
    }

    /**
     * Dates students can choose from. Only dates the staff set up, today or
     * later, that still have an online hour. Uses two queries for all dates.
     *
     * @return array<int, array{value: string, label: string, full: bool}>
     */
    public function datesAhead(): array
    {
        $slots = CapacitySlot::query()
            ->whereDate('date', '>=', today()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn ($s) => $s->date->toDateString());

        if ($slots->isEmpty()) {
            return [];
        }

        $students = Enrollment::query()
            ->whereDate('appointment_date', '>=', today()->toDateString())
            ->whereNotNull('appointment_time')
            ->get(['appointment_date', 'appointment_time', 'is_walk_in'])
            ->groupBy(fn ($e) => $e->appointment_date->toDateString());

        $dates = [];

        foreach ($slots as $day => $daySlots) {
            $blocks = collect($this->build($day, $daySlots, $students->get($day, collect())));

            // Nothing students could ever book on this date.
            if (!$blocks->contains(fn ($b) => $b['mode'] === 'online' && !$b['past'])) {
                continue;
            }

            $dates[] = [
                'value' => $day,
                'label' => Carbon::createFromFormat('Y-m-d', $day)->format('l, F j, Y'),
                'full' => !$blocks->contains(fn ($b) => $b['bookable']),
            ];
        }

        return $dates;
    }

    /** Error message if students cannot book this hour, or null if they can. */
    public function validateOnline(string $date, string $time): ?string
    {
        try {
            $day = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        } catch (\Throwable $e) {
            return 'Hindi valid ang petsa ng appointment.';
        }

        if ($day->lt(today())) {
            return 'Lumipas na ang petsang ito.';
        }

        $slot = collect($this->blocksFor($day))->firstWhere('time', $time);

        if (!$slot || $slot['closed']) {
            return 'Sarado ang admission sa napiling oras o petsa.';
        }

        if ($slot['mode'] !== 'online') {
            return 'Walk-in lang ang napiling oras. Pumili ng ibang oras.';
        }

        if ($slot['past']) {
            return 'Lumipas na ang napiling oras.';
        }

        if ($slot['left'] < 1) {
            return 'Puno na ang napiling oras. Pumili ng ibang oras.';
        }

        return null;
    }

    /** The scheduled hour that is running right now (for walk-ins), or null. */
    public function currentSlotTime(): ?string
    {
        $now = now();

        foreach ($this->blocksFor($now->copy()->startOfDay()) as $block) {
            if ($block['closed']) {
                continue;
            }

            $start = Carbon::createFromFormat('Y-m-d H:i', $now->toDateString() . ' ' . $block['time']);

            if ($now->gte($start) && $now->lt($start->copy()->addHour())) {
                return $block['time'];
            }
        }

        return null;
    }
}
