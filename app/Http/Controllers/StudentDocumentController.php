<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EnrollmentTemplate;
use App\Models\StudentDocxSubmission;
use PhpOffice\PhpWord\IOFactory;

class StudentDocumentController extends Controller
{
    /**
     * I-download ng estudyante ang Word template na in-upload ng admin.
     */
    public function downloadWordTemplate()
    {
        $template = EnrollmentTemplate::latest()->first();

        if (!$template) {
            return back()->with('error', 'Wala pang naka-upload na Word template.');
        }

        $filePath = storage_path('app/public/' . $template->file_path);

        if (!file_exists($filePath)) {
            return back()->with('error', 'Hindi matagpuan ang template file sa server.');
        }

        return response()->download($filePath);
    }

    /**
     * I-upload at basahin (parse) ang sinagutang Word file (.docx) ng estudyante.
     */
    public function uploadSubmission(Request $request)
    {
        $request->validate([
            'filled_file' => 'required|mimes:docx|max:10240', // Max 10MB
        ]);

        $file = $request->file('filled_file');
        $path = $file->store('submissions', 'public');
        $fullPath = storage_path('app/public/' . $path);

        // --- BASAHIN ANG LAMAN NG WORD FILE (PHPWORD PARSING) ---
        $phpWord = IOFactory::load($fullPath);
        $extractedText = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (get_class($element) === 'PhpOffice\PhpWord\Element\TextRun') {
                    foreach ($element->getElements() as $childElement) {
                        if (method_exists($childElement, 'getText')) {
                            $extractedText .= $childElement->getText() . " ";
                        }
                    }
                } elseif (method_exists($element, 'getText')) {
                    $extractedText .= $element->getText() . " ";
                }
            }
        }

        // I-save o i-update sa database para makita ng admin
        StudentDocxSubmission::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'file_path' => $path,
                'parsed_data' => ['raw_text' => trim($extractedText)]
            ]
        );

        return back()->with('success', 'Matagumpay na na-upload at nabasa ang iyong sinagutang Word form!');
    }
}
