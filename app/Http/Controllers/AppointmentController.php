<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\AppointmentSchedule;
use App\Models\Appointment;
use App\Models\StudentDocument;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validation kasama ang file uploads
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'contact_number' => 'required|string|max:20',
            'preferred_strand' => 'required|string',
            'schedule_id' => 'required|exists:appointment_schedules,schedule_id',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // 2. Duplicate Entry Checking
        $existingStudent = Student::where('email', $request->email)
            ->orWhere(function ($query) use ($request) {
                $query->where('first_name', $request->first_name)
                      ->where('last_name', $request->last_name);
            })->first();

        if ($existingStudent) {
            $hasAppointment = Appointment::where('student_id', $existingStudent->student_id)
                ->where('status', 'Scheduled')
                ->exists();

            if ($hasAppointment) {
                return back()->with('error', 'May kasalukuyan nang aktibong appointment ang aplikanteng ito.');
            }
        }

        // 3. Online Capacity Checking
        $schedule = AppointmentSchedule::findOrFail($request->schedule_id);
        $currentOnlineBookings = Appointment::where('schedule_id', $schedule->schedule_id)
            ->where('appointment_type', 'Online')
            ->where('status', 'Scheduled')
            ->count();

        if ($currentOnlineBookings >= $schedule->online_capacity) {
            return back()->with('error', 'Puno na ang online slots para sa napili mong oras.');
        }

        // 4. Create or Get Student Record
        $student = $existingStudent ?? Student::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth ?? '2008-01-01',
            'gender' => $request->gender ?? 'Male',
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'preferred_strand' => $request->preferred_strand,
        ]);

        // 5. Handle Document Uploads (Preliminary Review)
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $key => $file) {
                $path = $file->store('student_documents', 'public');

                StudentDocument::create([
                    'student_id' => $student->student_id,
                    'document_name' => 'Uploaded Document ' . ($key + 1),
                    'file_path' => $path,
                    'status' => 'Pending',
                ]);
            }
        }

        // 6. Generate Appointment Code & Save
        $appointmentCode = 'ADZU-' . strtoupper(Str::random(6));

        $appointment = Appointment::create([
            'student_id' => $student->student_id,
            'schedule_id' => $schedule->schedule_id,
            'appointment_code' => $appointmentCode,
            'appointment_type' => 'Online',
            'status' => 'Scheduled',
        ]);

        return back()->with('success', 'Matagumpay na nakapag-book ng appointment! Ang iyong Reference Code ay: ' . $appointmentCode);
    }
}
