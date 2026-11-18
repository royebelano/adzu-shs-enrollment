<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentTrackingController extends Controller
{
    public function index()
    {
        return view('student.tracking');
    }

    public function search(Request $request)
    {
        $request->validate([
            'reference_id' => 'required|string|exists:students,reference_id'
        ], [
            'reference_id.exists' => 'Ang Reference ID na ito ay hindi matagpuan sa aming sistema.'
        ]);

        $student = Student::with('documents')->where('reference_id', $request->reference_id)->first();

        return view('student.tracking', compact('student'));
    }
}
