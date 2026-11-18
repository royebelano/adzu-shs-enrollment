namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EnrollmentTemplate;
use App\Models\StudentDocxSubmission;
PhpOffice\PhpWord\IOFactory;

class StudentDocumentController extends Controller
{
    // 1. Download Template
    public function downloadTemplate()
    {
        $template = EnrollmentTemplate::latest()->first();
        if (!$template) {
            return back()->with('error', 'Wala pang naka-upload na template ang admin.');
        }

        return response()->download(storage_path('app/public/' . $template->file_path));
    }

    // 2. Upload and Parse Student Word File
    public function uploadSubmission(Request $request)
    {
        $request->validate([
            'filled_file' => 'required|mimes:docx|max:10240',
        ]);

        $file = $request->file('filled_file');
        $path = $file->store('submissions', 'public');
        $fullPath = storage_path('app/public/' . $path);

        // --- BASAHIN ANG LAMAN NG WORD FILE (PARSING) ---
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

        // I-save sa Database para makita ng Admin
        StudentDocxSubmission::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'file_path' => $path,
                'parsed_data' => ['raw_text' => trim($extractedText)]
            ]
        );

        return back()->with('success', 'Na-upload at nabasa na ang iyong Word form!');
    }
}
