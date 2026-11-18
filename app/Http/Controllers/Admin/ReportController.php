<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\EnrollmentDocument;
use App\Services\AppointmentSlots;
use App\Support\Requirements;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request, AppointmentSlots $slots)
    {
        /* ---------- date range (by the day the form was submitted) ---------- */

        $to = $this->date($request->query('to'), today());
        $from = $this->date($request->query('from'), $to->copy()->subDays(29));

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy(), $from->copy()];
        }

        // Keep the daily chart readable.
        if ($from->diffInDays($to) > 91) {
            $from = $to->copy()->subDays(91);
        }

        $apps = Enrollment::query()
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->get();

        $total = $apps->count();

        /* ---------- headline numbers ---------- */

        $enrolled = $apps->filter(fn ($e) => $e->stepNumber() === 6);
        $rejected = $apps->where('status', 'rejected')->count();

        $daysToEnroll = $enrolled
            ->filter(fn ($e) => $e->step6_at && $e->created_at)
            ->map(fn ($e) => $e->created_at->diffInHours($e->step6_at) / 24);

        $kpis = [
            'total' => $total,
            'enrolled' => $enrolled->count(),
            'in_progress' => $apps->filter(fn ($e) => $e->stepNumber() < 6 && $e->status !== 'rejected')->count(),
            'rejected' => $rejected,
            'walk_ins' => $apps->where('is_walk_in', true)->count(),
            'conversion' => $total ? round($enrolled->count() / $total * 100) : 0,
            'avg_days' => $daysToEnroll->isNotEmpty() ? round($daysToEnroll->avg(), 1) : null,
        ];

        /* ---------- funnel: how many reached each step ---------- */

        $funnel = [];
        $previous = null;

        foreach (Enrollment::STEPS as $n => $label) {
            $count = $apps->filter(fn ($e) => $e->stepNumber() >= $n)->count();

            $funnel[] = [
                'step' => $n,
                'label' => $n === 6 ? 'Enrolled' : 'Step ' . $n . ' · ' . $label,
                'count' => $count,
                'pct' => $total ? round($count / $total * 100) : 0,
                'drop' => $previous !== null && $previous > 0 ? $previous - $count : null,
            ];

            $previous = $count;
        }

        /* ---------- who applies ---------- */

        $strands = ['ABM' => 0, 'HUMSS' => 0, 'STEM' => 0, 'Not set' => 0];

        foreach ($apps as $e) {
            $strands[$e->strandCode() ?? 'Not set']++;
        }

        $genders = [
            'Male' => $apps->filter(fn ($e) => $e->sexLabel() === 'Male')->count(),
            'Female' => $apps->filter(fn ($e) => $e->sexLabel() === 'Female')->count(),
        ];
        $genders['Not set'] = $total - $genders['Male'] - $genders['Female'];

        $grades = $apps
            ->groupBy(fn ($e) => $e->grade_level ? 'Grade ' . $e->grade_level : 'Not set')
            ->map->count()
            ->sortKeys()
            ->all();

        $sources = [
            'Online' => $total - $kpis['walk_ins'],
            'Walk-in' => $kpis['walk_ins'],
        ];

        /* ---------- submissions per day ---------- */

        $perDay = $apps->groupBy(fn ($e) => $e->created_at->toDateString())->map->count();
        $daily = [];

        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $daily[] = ['date' => $d->copy(), 'count' => (int) $perDay->get($d->toDateString(), 0)];
        }

        /* ---------- how long each step takes ---------- */

        $stepTimes = [];

        foreach ([1 => 'Submitted to Infirmary', 2 => 'Infirmary to Admission', 3 => 'Admission to ID Capturing', 4 => 'ID Capturing to GCO', 5 => 'GCO to Enrolled'] as $n => $label) {
            $hours = $apps
                ->map(function ($e) use ($n) {
                    $a = $e->stepReachedAt($n);
                    $b = $e->stepReachedAt($n + 1);

                    return $a && $b ? $a->diffInHours($b) : null;
                })
                ->filter(fn ($h) => $h !== null);

            $stepTimes[] = [
                'label' => $label,
                'students' => $hours->count(),
                'days' => $hours->isNotEmpty() ? round($hours->avg() / 24, 1) : null,
            ];
        }

        /* ---------- admission checklist and GCO ---------- */

        $admitted = $apps->filter(fn ($e) => $e->stepNumber() >= 3);

        $checklist = [
            'F137' => $admitted->where('adm_f137', true)->count(),
            'PSA' => $admitted->where('adm_psa', true)->count(),
            'Recommendation: Mod' => $admitted->where('adm_rec_mod', true)->count(),
            'Recommendation: GC' => $admitted->where('adm_rec_gc', true)->count(),
        ];

        $atGco = $apps->filter(fn ($e) => $e->stepNumber() >= 5);

        $gco = [];

        foreach (Enrollment::GCO_STATUSES as $key => $label) {
            $gco[$label] = $atGco->filter(fn ($e) => ($e->gco_status ?: 'pending') === $key)->count();
        }

        /* ---------- missing documents (online students only) ---------- */

        $online = $apps->where('is_walk_in', false);

        $uploaded = EnrollmentDocument::query()
            ->whereIn('enrollment_id', $online->pluck('id'))
            ->selectRaw('type, count(distinct enrollment_id) as students')
            ->groupBy('type')
            ->pluck('students', 'type');

        $missingDocs = [];

        foreach (Requirements::ALL as $key => $req) {
            if (!($req['required'] ?? false)) {
                continue;
            }

            $missing = $online->count() - (int) $uploaded->get($key, 0);

            if ($missing > 0) {
                $missingDocs[$req['label']] = $missing;
            }
        }

        arsort($missingDocs);

        /* ---------- appointments ---------- */

        $week = [];

        for ($i = 0; $i < 7; $i++) {
            $day = today()->addDays($i);
            $list = $slots->slotsFor($day);
            $closed = $slots->dayClosed($day);

            $week[] = [
                'date' => $day,
                'closed' => $closed,
                'capacity' => $closed ? 0 : array_sum(array_column($list, 'capacity')),
                'online' => array_sum(array_column($list, 'online')),
                'walk_ins' => array_sum(array_column($list, 'walk_ins')),
            ];
        }

        $busiest = $apps
            ->filter(fn ($e) => $e->appointment_time)
            ->groupBy('appointment_time')
            ->map->count()
            ->sortDesc()
            ->take(6)
            ->mapWithKeys(fn ($count, $time) => [AppointmentSlots::label($time) => $count])
            ->all();

        return view('admin.reports.index', compact(
            'from', 'to', 'kpis', 'funnel', 'strands', 'genders', 'grades', 'sources',
            'daily', 'stepTimes', 'checklist', 'gco', 'missingDocs', 'week', 'busiest'
        ) + ['onlineCount' => $online->count()]);
    }

    private function date($value, Carbon $default): Carbon
    {
        try {
            return $value
                ? Carbon::createFromFormat('Y-m-d', (string) $value)->startOfDay()
                : $default->copy()->startOfDay();
        } catch (\Throwable $e) {
            return $default->copy()->startOfDay();
        }
    }
}
