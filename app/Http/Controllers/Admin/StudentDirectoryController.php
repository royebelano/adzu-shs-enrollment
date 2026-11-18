<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class StudentDirectoryController extends Controller
{
    /** Apply the search and filters from the query string. */
    private function filtered(Request $request)
    {
        $query = Enrollment::query();

        if ($request->filled('q')) {
            $search = $request->input('q');

            $query->where(function ($q) use ($search) {
                $q->where('lrn', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%")
                    ->orWhere('tracking_number', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('step') && array_key_exists((int) $request->input('step'), Enrollment::STEPS)) {
            $query->where('current_step', (int) $request->input('step'));
        }

        if ($request->filled('status') && array_key_exists($request->input('status'), Enrollment::STATUSES)) {
            $query->where('status', $request->input('status'));
        }

        if (in_array($request->input('strand'), ['ABM', 'HUMSS', 'STEM'], true)) {
            $query->where('shs_strand', 'like', '%' . $request->input('strand') . '%');
        }

        if ($request->filled('grade')) {
            $query->where('grade_level', $request->input('grade'));
        }

        if ($request->input('source') === 'walkin') {
            $query->where('is_walk_in', true);
        } elseif ($request->input('source') === 'online') {
            $query->where('is_walk_in', false);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $everyone = Enrollment::query()->get(['id', 'current_step', 'is_walk_in']);

        $perStep = [];

        foreach (array_keys(Enrollment::STEPS) as $step) {
            $perStep[$step] = $everyone->filter(fn ($e) => (int) $e->current_step === $step)->count();
        }

        $stats = [
            'total' => $everyone->count(),
            'enrolled' => $perStep[6],
            'in_progress' => $everyone->count() - $perStep[6],
            'walk_ins' => $everyone->where('is_walk_in', true)->count(),
            'per_step' => $perStep,
        ];

        $enrollments = $this->filtered($request)
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        $grades = Enrollment::query()
            ->whereNotNull('grade_level')
            ->where('grade_level', '!=', '')
            ->distinct()
            ->orderBy('grade_level')
            ->pluck('grade_level');

        return view('admin.students.index', compact('enrollments', 'stats', 'grades'));
    }

    public function export(Request $request)
    {
        $rows = $this->filtered($request)->orderBy('last_name')->orderBy('first_name');

        $headers = [
            'Student ID', 'Reference ID', 'Last name', 'First name', 'Middle name', 'Suffix',
            'LRN', 'Grade', 'Strand', 'Sex', 'Birthdate', 'Mobile', 'Email',
            'Current step', 'Application status', 'Source', 'Appointment', 'Submitted',
        ];

        return response()->streamDownload(function () use ($rows, $headers) {
            $out = fopen('php://output', 'w');

            // Byte order mark so Excel reads names with accents correctly.
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers);

            $rows->chunk(500, function ($chunk) use ($out) {
                foreach ($chunk as $e) {
                    fputcsv($out, [
                        $e->student_id,
                        $e->tracking_number,
                        $e->last_name,
                        $e->first_name,
                        $e->middle_name,
                        $e->extension_name,
                        $e->lrn ? "'" . $e->lrn : '',
                        $e->grade_level,
                        $e->strandCode(),
                        $e->sexLabel(),
                        $e->birthdate?->format('Y-m-d'),
                        $e->phone_number,
                        $e->email,
                        $e->stageLabel(),
                        $e->statusLabel(),
                        $e->is_walk_in ? 'Walk-in' : 'Online',
                        $e->appointmentLabel(),
                        $e->created_at?->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($out);
        }, 'students-' . now()->format('Ymd-His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
