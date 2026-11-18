<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Support\Requirements;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Required enrollment documents
        $requiredKeys = array_keys(
            array_filter(
                Requirements::ALL,
                fn ($requirement) => $requirement['required'] ?? false
            )
        );

        // Total applicants
        $total = Enrollment::count();

        // Complete applications
        $complete = 0;

        if (count($requiredKeys) > 0) {
            $complete = Enrollment::whereHas(
                'documents',
                function ($query) use ($requiredKeys) {
                    $query->whereIn('type', $requiredKeys);
                },
                '>=',
                count($requiredKeys)
            )->count();
        }

        // Today's applications
        $today = Enrollment::whereDate(
            'created_at',
            today()
        )->count();

        // Applications within the last 7 days
        $week = Enrollment::where(
            'created_at',
            '>=',
            now()->subDays(7)
        )->count();

        // Application status counts
        $pending = Enrollment::where(
            'status',
            'pending'
        )->count();

        $approved = Enrollment::where(
            'status',
            'approved'
        )->count();

        $rejected = Enrollment::where(
            'status',
            'rejected'
        )->count();

        $inProgress = Enrollment::where(
            'status',
            'for_compliance'
        )->count();

        // Applications by status
        $byStatus = Enrollment::select(
                'status',
                DB::raw('COUNT(*) as c')
            )
            ->groupBy('status')
            ->pluck('c', 'status');

        // Applications by grade level
        $byGrade = Enrollment::select(
                'grade_level',
                DB::raw('COUNT(*) as c')
            )
            ->groupBy('grade_level')
            ->orderBy('grade_level')
            ->pluck('c', 'grade_level');

        // Applications by sex
        $bySex = Enrollment::select(
                'sex',
                DB::raw('COUNT(*) as c')
            )
            ->groupBy('sex')
            ->pluck('c', 'sex');

        // Latest enrollment applications
        $recent = Enrollment::withCount('documents')
            ->latest()
            ->take(8)
            ->get();

        // Dashboard view
        return view('admin.dashboard', [

            'total'       => $total,
            'today'       => $today,
            'week'        => $week,

            'pending'     => $pending,
            'approved'    => $approved,
            'rejected'    => $rejected,
            'inProgress'  => $inProgress,

            'complete'    => $complete,
            'incomplete'  => $total - $complete,

            'byStatus'    => $byStatus,
            'byGrade'     => $byGrade,
            'bySex'       => $bySex,

            'recent'      => $recent,
        ]);
    }
}
