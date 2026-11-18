<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\AppointmentSchedule;
use App\Models\Appointment;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'scheduled_today' => Student::whereDate('created_at', today())->count(),
            'officially_enrolled' => Student::where('status', 'Enrolled')->count(),
            'pending_count' => Student::where('status', 'Pending')->count(),
            'incomplete_docs' => 0,
        ];

        $appointments = Student::with('documents')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'appointments'));
    }

    public function step1()
    {
        $step1_students = Student::with('documents')
            ->where('status', 'Pending')
            ->latest()
            ->get();

        return view('admin.steps.step1', compact('step1_students'));
    }

    public function step2()
    {
        $user = Auth::user();

        if (!$user || !in_array(
            strtolower(trim($user->role ?? '')),
            ['infirmary', 'super admin'],
            true
        )) {
            abort(403, 'Unauthorized access.');
        }

        $step2_students = Student::with('documents')
            ->where(function ($query) {
                $query->where('status', 'Step 2')
                    ->orWhere('infirmary_completed', true);
            })
            ->latest()
            ->get();

        return view('admin.steps.step2', compact('step2_students'));
    }

    public function step3()
    {
        $step3_students = Student::with('documents')
            ->where('status', 'Step 3')
            ->latest()
            ->get();

        return view('admin.steps.step3', compact('step3_students'));
    }

    public function step4()
    {
        $step4_students = Student::with('documents')
            ->where('status', 'Step 4')
            ->latest()
            ->get();

        return view('admin.steps.step4', compact('step4_students'));
    }

    public function step5()
    {
        $step5_students = Student::with('documents')
            ->where(function ($query) {
                $query->where('status', 'Step 5')
                    ->orWhere('status', 'GCO Assessment');
            })
            ->latest()
            ->get();

        return view('admin.steps.step5', compact('step5_students'));
    }

    public function step6()
    {
        $step6_students = Student::with('documents')
            ->where(function ($query) {
                $query->where('status', 'Step 6')
                    ->orWhere('status', 'Enrolled');
            })
            ->latest()
            ->get();

        return view('admin.steps.step6', compact('step6_students'));
    }

    public function advanceStudent($id)
    {
        try {
            $student = Student::where('reference_id', $id)
                ->orWhere('student_id', $id)
                ->firstOrFail();

            $student->status = 'Step 2';
            $student->save();

            return response()->json([
                'success' => true,
                'message' => 'Successfully moved ' . $student->first_name . ' to Step 2!',
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while advancing the student.',
            ], 500);
        }
    }

    public function advanceToStep3($id)
    {
        try {
            $student = Student::where('reference_id', $id)
                ->orWhere('student_id', $id)
                ->firstOrFail();

            $student->status = 'Step 3';
            $student->infirmary_completed = true;
            $student->save();

            return response()->json([
                'success' => true,
                'message' => 'Successfully moved ' . $student->first_name . ' to Step 3!',
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while advancing the student.',
            ], 500);
        }
    }

    public function advanceToStep4($id)
    {
        try {
            $student = Student::where('reference_id', $id)
                ->orWhere('student_id', $id)
                ->firstOrFail();

            $student->status = 'Step 4';
            $student->save();

            return response()->json([
                'success' => true,
                'message' => 'Successfully moved ' . $student->first_name . ' to Step 4!',
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while advancing the student.',
            ], 500);
        }
    }

    public function advanceToStep5($id)
    {
        try {
            $student = Student::where('reference_id', $id)
                ->orWhere('student_id', $id)
                ->firstOrFail();

            $student->status = 'Step 5';
            $student->save();

            return response()->json([
                'success' => true,
                'message' => 'The student has been successfully advanced to Step 5!',
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while advancing the student.',
            ], 500);
        }
    }

    public function advanceToStep6($id)
    {
        $student = Student::where('student_id', $id)
            ->orWhere('reference_id', $id)
            ->firstOrFail();

        $student->status = 'Step 6';
        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'The student has been successfully advanced to Step 6!',
        ]);
    }

    // ==========================================
    // CAPACITY CONTROL & SCHEDULING MODULE
    // ==========================================

    public function capacityControl()
    {
        $schedules = AppointmentSchedule::withCount('appointments')
            ->orderBy('schedule_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        foreach ($schedules as $schedule) {
            $schedule->booked_count = $schedule->appointments_count;

            $schedule->remaining_slots = max(
                0,
                $schedule->max_slots - $schedule->booked_count
            );
        }

        return view('admin.capacity', compact('schedules'));
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'schedule_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'max_slots' => 'required|integer|min:1',
        ]);

        $existingSchedule = AppointmentSchedule::where(
            'schedule_date',
            $request->schedule_date
        )
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [
                    $request->start_time,
                    $request->end_time,
                ])
                ->orWhereBetween('end_time', [
                    $request->start_time,
                    $request->end_time,
                ])
                ->orWhere(function ($q) use ($request) {
                    $q->where('start_time', '<=', $request->start_time)
                        ->where('end_time', '>=', $request->end_time);
                });
            })
            ->first();

        if ($existingSchedule) {
            return redirect()
                ->back()
                ->withErrors([
                    'start_time' => 'This time slot is already scheduled or overlaps with an existing time block on this date.',
                ])
                ->withInput();
        }

        AppointmentSchedule::create([
            'schedule_date' => $request->schedule_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'max_slots' => $request->max_slots,
            'walk_in_slots' => 0,
            'mode' => 'Online',
            'is_active' => true,
        ]);

        return redirect()->back()
            ->with('success', 'Custom time slot range created successfully.');
    }

    public function updateSchedules(Request $request)
    {
        if ($request->has('schedules')) {
            foreach ($request->schedules as $id => $data) {
                $schedule = AppointmentSchedule::find($id);

                if ($schedule) {
                    $mode = $data['mode'] ?? 'Online';

                    $schedule->update([
                        'max_slots' => $data['max_slots'] ?? $schedule->max_slots,
                        'mode' => $mode,
                        'is_active' => ($mode !== 'Closed'),
                    ]);
                }
            }
        }

        return redirect()->back()
            ->with('success', 'Hourly schedule and capacity updated successfully!');
    }

    public function toggleSchedule($id)
    {
        $schedule = AppointmentSchedule::findOrFail($id);
        $newActive = !$schedule->is_active;

        $schedule->update([
            'is_active' => $newActive,
            'mode' => $newActive ? 'Online' : 'Closed',
        ]);

        return redirect()->back()
            ->with('success', 'Schedule status updated successfully.');
    }

    public function manageAccounts()
    {
        $users = \App\Models\User::all();

        return view('admin.accounts', compact('users'));
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string',
            'password' => 'required|min:6',
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'department' => $request->role,
            'role' => $request->role,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('admin.accounts')
            ->with('success', 'Account created successfully!');
    }

    public function updateAccount(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|string',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'department' => $request->role,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'nullable|string|min:6',
            ]);

            $user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        return redirect()->route('admin.accounts')
            ->with('success', 'Account and role updated successfully!');
    }

    public function updateStudent(Request $request, $id)
    {
        try {
            $student = Student::where('reference_id', $id)
                ->orWhere('student_id', $id)
                ->firstOrFail();

            $allowedFields = [
                'student_id' => 'editable_student_id',
                'last_name' => 'last_name',
                'first_name' => 'first_name',
                'middle_name' => 'middle_name',
                'suffix' => 'suffix',
                'date_of_birth' => 'date_of_birth',
                'lrn' => 'lrn',
                'school' => 'school',
                'preferred_strand' => 'preferred_strand',
                'medical_assessment_date' => 'medical_assessment_date',
                'gco_assessment_date' => 'gco_assessment_date',
                'physical_exam' => 'physical_exam',
                'dental_exam' => 'dental_exam',
                'medical_certificate' => 'medical_certificate',
                'form_138' => 'form_138',
                'psa' => 'psa',
                'good_moral' => 'good_moral',
                'contact_person' => 'contact_person',
                'contact_address' => 'contact_address',
                'contact_relationship' => 'contact_relationship',
            ];

            $updates = [];

            foreach ($allowedFields as $input => $column) {
                if ($request->has($input)) {
                    $value = $request->input($input);

                    if (in_array($column, [
                        'physical_exam',
                        'dental_exam',
                        'medical_certificate',
                        'form_138',
                        'psa',
                        'good_moral',
                    ], true)) {
                        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                    }

                    $updates[$column] = $value;
                }
            }

            // Checkbox fields omitted from the request are treated as unchecked
            // only when the request submits at least one assessment field.
            $assessmentFields = [
                'physical_exam',
                'dental_exam',
                'medical_certificate',
            ];

            $hasAssessmentInput = $request->has('medical_assessment_date');

            foreach ($assessmentFields as $field) {
                if ($request->has($field)) {
                    $updates[$field] = filter_var(
                        $request->input($field),
                        FILTER_VALIDATE_BOOLEAN
                    ) ? 1 : 0;
                } elseif ($hasAssessmentInput) {
                    $updates[$field] = 0;
                }
            }

            // Preserve document completion status unless the document flags
            // are submitted by the request.
            if ($request->has('form_138') || $request->has('psa')) {
                $form138 = array_key_exists('form_138', $updates)
                    ? (int) $updates['form_138']
                    : (int) $student->form_138;

                $psa = array_key_exists('psa', $updates)
                    ? (int) $updates['psa']
                    : (int) $student->psa;

                $updates['admission_step3_status'] =
                    ($form138 === 1 && $psa === 1) ? 'Done' : 'Pending';
            }

            // Set the Step 2 completion flag when all three medical checks pass.
            if ($hasAssessmentInput || collect($assessmentFields)->contains(
                fn ($field) => $request->has($field)
            )) {
                $physical = (int) ($updates['physical_exam'] ?? $student->physical_exam);
                $dental = (int) ($updates['dental_exam'] ?? $student->dental_exam);
                $certificate = (int) ($updates['medical_certificate'] ?? $student->medical_certificate);

                $medicalPassed = $physical === 1
                    && $dental === 1
                    && $certificate === 1;

                $updates['infirmary_completed'] = $medicalPassed;
            }

            if (!empty($updates)) {
                $student->update($updates);
                $student->refresh();
            }

            if ($request->hasFile('barcode_file')) {
                $path = $request->file('barcode_file')->store('barcodes', 'public');

                StudentDocument::updateOrCreate(
                    [
                        'student_id' => $student->student_id,
                        'document_name' => 'Barcode',
                    ],
                    [
                        'file_path' => $path,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Student information saved successfully.',
                'student' => $student,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please check the submitted information.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving student information.',
            ], 500);
        }
    }
}

