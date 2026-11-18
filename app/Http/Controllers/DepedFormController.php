<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EnrollmentTemplate;

class DepedFormController extends Controller
{
    /**
     * I-display ang page kung saan pwede mag-upload ang admin ng Word template.
     */
    public function index()
    {
        $templates = EnrollmentTemplate::latest()->get();
        return view('admin.deped-form.index', compact('templates'));
    }

    /**
     * I-handle ang pag-upload ng Word template (.docx) ng admin.
     */
    public function uploadTemplate(Request $request)
    {
        $request->validate([
            'template_file' => 'required|mimes:docx|max:10240', // Max 10MB
            'title' => 'required|string|max:255'
        ]);

        // I-save ang file sa storage/app/public/templates
        $path = $request->file('template_file')->store('templates', 'public');

        // I-save ang record sa database
        EnrollmentTemplate::create([
            'title' => $request->title,
            'file_path' => $path
        ]);

        return back()->with('success', 'Matagumpay na na-upload ang Word template!');
    }
}
