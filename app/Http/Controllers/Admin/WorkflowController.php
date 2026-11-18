<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Support\Requirements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Steps 3 to 6 of the enrollment workflow.
 *
 *  Step 3 - Admission      (documents + checklist, student ID number)
 *  Step 4 - ID Capturing   (appointment, bar code)
 *  Step 5 - GCO Assessment (assessment date and result)
 *  Step 6 - Enrolled
 *
 * The same enrollment record moves from step to step, so nothing is retyped.
 */
class WorkflowController extends Controller
{
    /* ==================================================================
     | Shared helpers
     * ================================================================== */

    private function search($query, Request $request)
    {
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

        return $query;
    }

    /** Send the person to the page of the step the student is really in. */
    private function backToCurrentStep(Enrollment $enrollment)
    {
        $step = $enrollment->stepNumber();

        return redirect()->route(
            $step === 1 ? 'admin.step1.view' : "admin.step{$step}.view",
            $enrollment
        );
    }

    /** Move a student forward, only if they are exactly at the expected step. */
    private function advance(Enrollment $enrollment, int $from, array $changes)
    {
        if ($enrollment->stepNumber() !== $from) {
            return false;
        }

        $enrollment->forceFill($changes)->save();

        return true;
    }

    /* ==================================================================
     | STEP 3 - ADMISSION
     * ================================================================== */

    public function step3Index(Request $request)
    {
        $base = Enrollment::query()->where('current_step', '>=', 3);

        // Counts shown at the top, like the admission sheet header.
        $everyone = (clone $base)->get(['id', 'sex', 'shs_strand', 'data']);

        $strands = ['ABM' => 0, 'HUMSS' => 0, 'STEM' => 0];

        foreach ($everyone as $row) {
            $code = $row->strandCode();

            if ($code) {
                $strands[$code]++;
            }
        }

        $stats = [
            'total' => $everyone->count(),
            'strands' => $strands,
            'male' => $everyone->filter(fn ($r) => $r->sexLabel() === 'Male')->count(),
            'female' => $everyone->filter(fn ($r) => $r->sexLabel() === 'Female')->count(),
        ];

        $enrollments = $this->search($base, $request)
            ->orderBy('student_id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.steps.step3.index', compact('enrollments', 'stats'));
    }

    public function step3Show(Enrollment $enrollment)
    {
        if (!$enrollment->reachedStep(3)) {
            return $this->backToCurrentStep($enrollment);
        }

        $enrollment->load('documents');

        return view('admin.steps.step3.step3-view', [
            'enrollment' => $enrollment,
            'requirements' => Requirements::ALL,
        ]);
    }

    /** Step 2 -> Step 3. Gives the student an ID number. */
    public function proceedStep3(Enrollment $enrollment)
    {
        $moved = false;

        DB::transaction(function () use ($enrollment, &$moved) {
            if ($enrollment->stepNumber() !== 2) {
                return;
            }

            $studentId = $enrollment->student_id ?: Enrollment::generateStudentId();

            $enrollment->forceFill([
                'current_step' => 3,
                'step3_at' => now(),
                'student_id' => $studentId,
                'barcode' => $enrollment->barcode ?: $studentId,
            ])->save();

            $moved = true;
        });

        return redirect()
            ->route('admin.step3.view', $enrollment)
            ->with('success', $moved ? 'Student moved to Step 3 (Admission).' : 'Nothing changed.');
    }

    public function step3Save(Request $request, Enrollment $enrollment)
    {
        abort_unless($enrollment->reachedStep(3), 404);

        $validated = $request->validate([
            'admission_remarks' => 'nullable|string|max:1000',
        ]);

        $enrollment->forceFill([
            'adm_f137' => $request->boolean('adm_f137'),
            'adm_psa' => $request->boolean('adm_psa'),
            'adm_rec_mod' => $request->boolean('adm_rec_mod'),
            'adm_rec_gc' => $request->boolean('adm_rec_gc'),
            'admission_remarks' => $validated['admission_remarks'] ?? null,
        ])->save();

        return back()->with('success', 'Admission checklist saved.');
    }

    /** Step 3 -> Step 4 */
    public function proceedStep4(Enrollment $enrollment)
    {
        $moved = $this->advance($enrollment, 3, [
            'current_step' => 4,
            'step4_at' => now(),
        ]);

        return redirect()
            ->route('admin.step4.view', $enrollment)
            ->with('success', $moved ? 'Student moved to Step 4 (ID Capturing).' : 'Nothing changed.');
    }

    /* ==================================================================
     | STEP 4 - ID CAPTURING
     * ================================================================== */

    public function step4Index(Request $request)
    {
        $enrollments = $this->search(
            Enrollment::query()->where('current_step', '>=', 4),
            $request
        )
            ->orderBy('student_id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.steps.step4.index', compact('enrollments'));
    }

    public function step4Show(Enrollment $enrollment)
    {
        if (!$enrollment->reachedStep(4)) {
            return $this->backToCurrentStep($enrollment);
        }

        return view('admin.steps.step4.step4-view', compact('enrollment'));
    }

    public function step4Save(Request $request, Enrollment $enrollment)
    {
        abort_unless($enrollment->reachedStep(4), 404);

        $validated = $request->validate([
            'id_capture_date' => 'nullable|date',
            'id_capture_time' => 'nullable|date_format:H:i',
            'id_capture_end' => 'nullable|date_format:H:i|after:id_capture_time',
            'barcode' => 'nullable|string|max:40|regex:/^[A-Za-z0-9\-]+$/',
        ], [
            'id_capture_end.after' => 'The end time must be later than the start time.',
            'barcode.regex' => 'The bar code can only have letters, numbers, and dashes.',
        ]);

        $enrollment->forceFill([
            'id_capture_date' => $validated['id_capture_date'] ?? null,
            'id_capture_time' => $validated['id_capture_time'] ?? null,
            'id_capture_end' => $validated['id_capture_end'] ?? null,
            'barcode' => $validated['barcode'] ?? $enrollment->student_id,
            'id_captured' => $request->boolean('id_captured'),
        ])->save();

        return back()->with('success', 'ID capturing details saved.');
    }

    /** Step 4 -> Step 5 */
    public function proceedStep5(Enrollment $enrollment)
    {
        $moved = $this->advance($enrollment, 4, [
            'current_step' => 5,
            'step5_at' => now(),
        ]);

        return redirect()
            ->route('admin.step5.view', $enrollment)
            ->with('success', $moved ? 'Student moved to Step 5 (GCO Assessment).' : 'Nothing changed.');
    }

    /* ==================================================================
     | STEP 5 - GCO ASSESSMENT
     * ================================================================== */

    public function step5Index(Request $request)
    {
        // Same order as the GCO sheet: ID capturing date and time, then
        // family name. Students without a date go to the end of the list.
        $enrollments = $this->search(
            Enrollment::query()->where('current_step', '>=', 5),
            $request
        )
            ->orderByRaw('id_capture_date is null')
            ->orderBy('id_capture_date')
            ->orderBy('id_capture_time')
            ->orderBy('last_name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.steps.step5.index', compact('enrollments'));
    }

    public function step5Show(Enrollment $enrollment)
    {
        if (!$enrollment->reachedStep(5)) {
            return $this->backToCurrentStep($enrollment);
        }

        return view('admin.steps.step5.step5-view', compact('enrollment'));
    }

    public function step5Save(Request $request, Enrollment $enrollment)
    {
        abort_unless($enrollment->reachedStep(5), 404);

        $validated = $request->validate([
            'gco_date' => 'nullable|date',
            'gco_time' => 'nullable|date_format:H:i',
            'gco_status' => 'required|in:' . implode(',', array_keys(Enrollment::GCO_STATUSES)),
        ]);

        $enrollment->forceFill([
            'gco_date' => $validated['gco_date'] ?? null,
            'gco_time' => $validated['gco_time'] ?? null,
            'gco_status' => $validated['gco_status'],
        ])->save();

        return back()->with('success', 'GCO assessment saved.');
    }

    /** Step 5 -> Step 6 (Enrolled). Needs the GCO assessment to be Done. */
    public function proceedStep6(Enrollment $enrollment)
    {
        if ($enrollment->stepNumber() === 5 && $enrollment->gco_status !== 'done') {
            return back()->withErrors([
                'gco_status' => 'Set the GCO assessment to Done before marking the student as enrolled.',
            ]);
        }

        $moved = $this->advance($enrollment, 5, [
            'current_step' => 6,
            'step6_at' => now(),
            'status' => 'approved',
        ]);

        return redirect()
            ->route('admin.step6.view', $enrollment)
            ->with('success', $moved ? 'Student is now enrolled.' : 'Nothing changed.');
    }

    /* ==================================================================
     | STEP 6 - ENROLLED
     * ================================================================== */

    public function step6Index(Request $request)
    {
        $enrollments = $this->search(
            Enrollment::query()->where('current_step', '>=', 6),
            $request
        )
            ->orderByDesc('step6_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.steps.step6.index', compact('enrollments'));
    }

    public function step6Show(Enrollment $enrollment)
    {
        if (!$enrollment->reachedStep(6)) {
            return $this->backToCurrentStep($enrollment);
        }

        return view('admin.steps.step6.step6-view', compact('enrollment'));
    }
}
