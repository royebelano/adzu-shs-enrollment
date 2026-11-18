<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CapacitySlot;
use App\Models\Enrollment;
use App\Services\AppointmentSlots;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * The admission schedule is set by hand: the admin adds a date, then adds the
 * hours of that date one by one, each with its own mode and capacity.
 * Nothing is filled in automatically.
 */
class CapacityController extends Controller
{
    /** Hours the admin can choose from (6 AM to 9 PM), as "HH:00" => label. */
    private function hourOptions(): array
    {
        $options = [];

        for ($h = 6; $h <= 20; $h++) {
            $time = sprintf('%02d:00', $h);
            $options[$time] = AppointmentSlots::label($time);
        }

        return $options;
    }

    /*
    |--------------------------------------------------------------------------
    | LIST OF SCHEDULED DATES
    |--------------------------------------------------------------------------
    */

    public function index(Request $request, AppointmentSlots $slots)
    {
        $past = $request->query('view') === 'past';

        $datesQuery = CapacitySlot::query()->select('date')->distinct();

        $datesQuery = $past
            ? $datesQuery->whereDate('date', '<', today()->toDateString())->orderByDesc('date')
            : $datesQuery->whereDate('date', '>=', today()->toDateString())->orderBy('date');

        $dates = $datesQuery->paginate(7)->withQueryString();

        $schedule = [];

        foreach ($dates as $row) {
            $day = Carbon::parse($row->date)->startOfDay();

            $schedule[] = [
                'date' => $day,
                'blocks' => $slots->blocksFor($day),
            ];
        }

        $today = today()->toDateString();

        $stats = [
            'dates' => CapacitySlot::query()->whereDate('date', '>=', $today)->distinct()->count('date'),
            'online_seats' => (int) CapacitySlot::query()->whereDate('date', '>=', $today)->where('mode', 'online')->sum('capacity'),
            'walkin_seats' => (int) CapacitySlot::query()->whereDate('date', '>=', $today)->where('mode', 'walkin')->sum('capacity'),
            'booked' => Enrollment::query()->whereDate('appointment_date', '>=', $today)->count(),
        ];

        return view('admin.capacity.index', [
            'schedule' => $schedule,
            'dates' => $dates,
            'past' => $past,
            'stats' => $stats,
            'hasAny' => CapacitySlot::query()->exists(),
            'hourOptions' => $this->hourOptions(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ADD ONE DATE WITH ITS HOURS
    |--------------------------------------------------------------------------
    */

    public function addDate(Request $request)
    {
        $options = $this->hourOptions();

        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'hours' => 'required|array|min:1',
            'hours.*.time' => 'required|in:' . implode(',', array_keys($options)),
            'hours.*.mode' => 'required|in:online,walkin',
            'hours.*.capacity' => 'required|integer|min:0|max:1000',
        ], [
            'date.required' => 'Pumili ng petsa.',
            'date.after_or_equal' => 'The date cannot be in the past.',
            'hours.required' => 'Magdagdag ng kahit isang oras.',
            'hours.min' => 'Magdagdag ng kahit isang oras.',
            'hours.*.time.required' => 'Pumili ng oras para sa bawat hilera.',
            'hours.*.mode.required' => 'Pumili kung Online o Walk-in ang bawat oras.',
            'hours.*.capacity.required' => 'Ilagay ang capacity ng bawat oras.',
            'hours.*.capacity.integer' => 'Ang capacity ay dapat na numero.',
        ]);

        $times = array_column($validated['hours'], 'time');

        if (count($times) !== count(array_unique($times))) {
            return back()->withInput()->withErrors([
                'hours' => 'Pareho ang dalawang oras. Bawat oras ay isang beses lang.',
            ]);
        }

        if (CapacitySlot::query()->whereDate('date', $validated['date'])->exists()) {
            return back()->withInput()->withErrors([
                'date' => 'That date already has a schedule. Edit it in the list below, or add an hour to it.',
            ]);
        }

        DB::transaction(function () use ($validated) {
            foreach ($validated['hours'] as $row) {
                CapacitySlot::create([
                    'date' => $validated['date'],
                    'start_time' => $row['time'],
                    'mode' => $row['mode'],
                    'capacity' => (int) $row['capacity'],
                ]);
            }
        });

        return redirect()
            ->route('admin.capacity.index')
            ->with('success', Carbon::parse($validated['date'])->format('F d, Y') . ' added.');
    }

    /*
    |--------------------------------------------------------------------------
    | ADD ONE MORE HOUR TO A DATE THAT ALREADY EXISTS
    |--------------------------------------------------------------------------
    */

    public function addHour(Request $request)
    {
        $options = $this->hourOptions();

        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|in:' . implode(',', array_keys($options)),
            'mode' => 'required|in:online,walkin',
            'capacity' => 'required|integer|min:0|max:1000',
        ], [
            'time.required' => 'Pumili ng oras na idadagdag.',
            'mode.required' => 'Pumili kung Online o Walk-in.',
            'capacity.required' => 'Ilagay ang capacity.',
        ]);

        if (!CapacitySlot::query()->whereDate('date', $validated['date'])->exists()) {
            return back()->withErrors(['date' => 'That date is not in the schedule. Add the date first.']);
        }

        $exists = CapacitySlot::query()
            ->whereDate('date', $validated['date'])
            ->where('start_time', $validated['time'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['time' => 'That hour is already in this date.']);
        }

        CapacitySlot::create([
            'date' => $validated['date'],
            'start_time' => $validated['time'],
            'mode' => $validated['mode'],
            'capacity' => (int) $validated['capacity'],
        ]);

        return back()->with('success', $options[$validated['time']] . ' added to ' . Carbon::parse($validated['date'])->format('F d, Y') . '.');
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE CHANGES TO THE LISTED DATES
    |--------------------------------------------------------------------------
    */

    public function save(Request $request)
    {
        $validated = $request->validate([
            'slots' => 'required|array',
            'slots.*.*.mode' => 'required|in:' . implode(',', CapacitySlot::MODES),
            'slots.*.*.capacity' => 'required|integer|min:0|max:1000',
            'slots.*.*.remove' => 'nullable|boolean',
        ], [
            'slots.*.*.capacity.required' => 'Ilagay ang capacity ng bawat oras.',
            'slots.*.*.capacity.integer' => 'Ang capacity ay dapat na numero.',
        ]);

        $blocked = [];

        DB::transaction(function () use ($validated, &$blocked) {
            foreach ($validated['slots'] as $date => $hours) {
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)) {
                    continue;
                }

                foreach ($hours as $time => $row) {
                    $query = CapacitySlot::query()
                        ->whereDate('date', $date)
                        ->where('start_time', $time);

                    if (!empty($row['remove'])) {
                        $booked = Enrollment::query()
                            ->whereDate('appointment_date', $date)
                            ->where('appointment_time', 'like', substr((string) $time, 0, 2) . ':%')
                            ->count();

                        if ($booked > 0) {
                            $blocked[] = $date . ' ' . AppointmentSlots::label((string) $time);
                            continue;
                        }

                        $query->delete();
                        continue;
                    }

                    $query->update([
                        'mode' => $row['mode'],
                        'capacity' => (int) $row['capacity'],
                    ]);
                }
            }
        });

        if ($blocked) {
            return back()->with('success', 'Schedule saved.')->withErrors([
                'slots' => 'Hindi natanggal ang may booking na oras: ' . implode(', ', $blocked) . '. I-set na lang sa Closed.',
            ]);
        }

        return back()->with('success', 'Schedule saved.');
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE A WHOLE DATE
    |--------------------------------------------------------------------------
    */

    public function removeDate(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $booked = Enrollment::query()->whereDate('appointment_date', $validated['date'])->count();

        if ($booked > 0) {
            return back()->withErrors([
                'date' => $booked . ' student' . ($booked === 1 ? ' has' : 's have') . ' an appointment on that date. Set its hours to Closed instead of removing it.',
            ]);
        }

        CapacitySlot::query()->whereDate('date', $validated['date'])->delete();

        return back()->with('success', 'Date removed.');
    }
}
