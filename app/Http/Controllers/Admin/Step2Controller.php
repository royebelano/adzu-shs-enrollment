<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Services\HealthRecordPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Step2Controller extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SEND A STUDENT FROM STEP 1 TO STEP 2
    |--------------------------------------------------------------------------
    | The student's data is not retyped or duplicated. The same record simply
    | moves forward, so Step 2 always shows what was submitted in Step 1.
    */

    public function proceed(Enrollment $enrollment)
    {
        if (!$enrollment->isInStep2()) {
            $enrollment->current_step = 2;
            $enrollment->step2_at = now();
            $enrollment->step2_by = Auth::user()?->name ?: 'Admin';
            $enrollment->save();
        }

        return redirect()
            ->route('admin.step2.view', $enrollment)
            ->with('success', 'Student moved to Step 2 (Infirmary).');
    }

    /*
    |--------------------------------------------------------------------------
    | STEP 2 LIST
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Enrollment::query()->where('current_step', '>=', 2);

        if ($request->filled('q')) {
            $search = $request->input('q');

            $query->where(function ($q) use ($search) {
                $q->where('lrn', 'like', "%{$search}%")
                    ->orWhere('tracking_number', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%");
            });
        }

        $enrollments = $query
            ->orderByDesc('step2_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.steps.step2.index', [
            'enrollments' => $enrollments,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STEP 2 DETAILS
    |--------------------------------------------------------------------------
    */

    public function show(Enrollment $enrollment, HealthRecordPdf $record)
    {
        // Students who have not been sent to Step 2 go back to Step 1.
        if (!$enrollment->isInStep2()) {
            return redirect()->route('admin.step1.view', $enrollment);
        }

        return view('admin.steps.step2.step2-view', [
            'enrollment' => $enrollment,
            'filled' => $record->values($enrollment),
            'labels' => HealthRecordPdf::labels(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW / DOWNLOAD THE HEALTH EXAMINATION RECORD (PDF)
    |--------------------------------------------------------------------------
    */

    public function print(Request $request, Enrollment $enrollment, HealthRecordPdf $record)
    {
        abort_unless($enrollment->isInStep2(), 404);

        $binary = $record->render($enrollment);

        $filename = Str::slug(
            'health-record-' . $enrollment->last_name . '-' . $enrollment->first_name
        ) . '.pdf';

        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
            'Content-Length' => strlen($binary),
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
