<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Services\EnrollmentPdf;
use App\Support\FormFields;
use App\Support\Requirements;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use ZipArchive;

class EnrollmentAdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STEP 1 - ENROLLMENT LIST
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Enrollment::query()->withCount('documents');

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

        if ($request->filled('grade')) {
            $query->where('grade_level', $request->input('grade'));
        }

        if ($request->filled('sy')) {
            $query->where('school_year', $request->input('sy'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $enrollments = $query
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.steps.step1.index', [
            'enrollments' => $enrollments,
            'q' => $request->input('q', ''),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STUDENT DETAILS
    |--------------------------------------------------------------------------
    */

    public function show(Enrollment $enrollment)
    {
        $enrollment->load('documents');

        return view('admin.steps.step1.step1-view', [
            'enrollment' => $enrollment,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE ENROLLMENT STATUS
    |--------------------------------------------------------------------------
    */

    public function updateStatus(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,for_compliance,approved,rejected',
            'remarks' => 'nullable|string|max:2000',
        ]);

        $enrollment->status = $validated['status'];
        $enrollment->remarks = $validated['remarks'] ?? null;
        $enrollment->reviewed_at = now();

        $currentUser = Auth::user();

        $enrollment->reviewed_by = $currentUser
            ? $currentUser->name
            : 'Admin';

        $enrollment->save();

        return redirect()
            ->route('admin.step1.view', $enrollment)
            ->with('status', 'Enrollment status successfully updated!');
    }


    /*
    |--------------------------------------------------------------------------
    | VIEW OR DOWNLOAD STUDENT DOCUMENT
    |--------------------------------------------------------------------------
    */

    public function document(
        Request $request,
        Enrollment $enrollment,
        string $type
    ) {
        abort_unless(
            array_key_exists($type, Requirements::ALL),
            404
        );

        $docQuery = $enrollment->documents()->where('type', $type);

        // When a specific file is requested, open exactly that one.
        if ($request->filled('doc')) {
            $docQuery->where('id', (int) $request->input('doc'));
        }

        $doc = $docQuery->latest('id')->firstOrFail();

        $disk = Storage::disk('local');

        abort_unless(
            $doc->path && $disk->exists($doc->path),
            404,
            'Uploaded document file not found.'
        );

        $absolutePath = $disk->path($doc->path);

        // Detect the file type using the actual stored file.
        $extension = strtolower(
            pathinfo($doc->path, PATHINFO_EXTENSION)
        );

        // Detect MIME type using PHP native file inspection.
        // This avoids the Intelephense undefined-method warning for mimeType().
        $mime = null;

        if (function_exists('mime_content_type')) {
            $mime = mime_content_type($absolutePath) ?: null;
        }

        // Use the MIME value saved during upload if detection is unavailable.
        if (!$mime || $mime === 'application/octet-stream') {
            $mime = $doc->mime ?: $mime;
        }

        // Fallback for common uploaded document formats.
        $mimeMap = [
            'pdf'  => 'application/pdf',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
        ];

        if (
            !$mime ||
            $mime === 'application/octet-stream'
        ) {
            $mime = $mimeMap[$extension] ?? 'application/octet-stream';
        }

        // Only PDF and image files are previewed in the browser.
        $isPreviewable = $mime === 'application/pdf'
            || str_starts_with($mime, 'image/');

        $filename = $doc->original_name
            ?: Str::slug(
                $enrollment->last_name . '-' .
                $enrollment->first_name . '-' .
                $type
            ) . '.' . ($extension ?: 'file');

        // Prevent directory traversal or unsafe filename characters.
        $filename = basename(
            str_replace('\\', '/', $filename)
        );

        $headers = [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ];

        // Download when requested or when the file cannot be previewed.
        if ($request->boolean('download') || !$isPreviewable) {
            return response()->download(
                $absolutePath,
                $filename,
                $headers
            );
        }

        // Display PDF or image directly in the browser.
        $headers['Content-Disposition'] =
            'inline; filename="' . addslashes($filename) . '"';

        return response()->file(
            $absolutePath,
            $headers
        );
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW OR DOWNLOAD GENERATED ENROLLMENT PDF
    |--------------------------------------------------------------------------
    */

    public function pdf(
        Request $request,
        Enrollment $enrollment,
        EnrollmentPdf $pdf
    ) {
        $binary = $pdf->render($enrollment->data ?? []);

        $filename = Str::slug(
            'enrollment-' .
            $enrollment->id . '-' .
            $enrollment->last_name . '-' .
            $enrollment->first_name
        ) . '.pdf';

        $disposition = $request->boolean('download')
            ? 'attachment'
            : 'inline';

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition .
                '; filename="' . $filename . '"',
            'Cache-Control' => 'private, no-store',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD COMPLETE ENROLLMENT ZIP
    |--------------------------------------------------------------------------
    */

    public function zip(
        Enrollment $enrollment,
        EnrollmentPdf $pdf
    ) {
        if (!class_exists(ZipArchive::class)) {
            return back()->withErrors([
                'zip' => 'PHP ZIP extension is not enabled.',
            ]);
        }

        $tempDirectory = storage_path('app/temp');

        if (!is_dir($tempDirectory)) {
            if (!mkdir($tempDirectory, 0755, true) && !is_dir($tempDirectory)) {
                throw new \RuntimeException(
                    'Unable to create temporary directory.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STUDENT FOLDER NAME
        |--------------------------------------------------------------------------
        | FAMILY NAME + MIDDLE NAME + FIRST NAME
        |--------------------------------------------------------------------------
        */

        $studentFolder = collect([
            $enrollment->last_name,
            $enrollment->middle_name,
            $enrollment->first_name,
        ])
            ->filter(fn ($value) => filled($value))
            ->implode(' ');

        $studentFolder = Str::upper($studentFolder);

        $safeName = preg_replace(
            '/[<>:"\/\\\\|?*\x00-\x1F]/',
            '',
            $studentFolder
        );

        $safeName = trim($safeName, " .");

        if (empty($safeName)) {
            $safeName = 'STUDENT-' . $enrollment->id;
        }

        /*
        |--------------------------------------------------------------------------
        | ZIP PATH
        |--------------------------------------------------------------------------
        */

        $zipPath = $tempDirectory .
            DIRECTORY_SEPARATOR .
            'enrollment-' .
            $enrollment->id .
            '-' .
            uniqid() .
            '.zip';

        $zip = new ZipArchive();
        $zipOpened = false;

        try {

            /*
            |--------------------------------------------------------------------------
            | CREATE ZIP
            |--------------------------------------------------------------------------
            */

            $result = $zip->open(
                $zipPath,
                ZipArchive::CREATE | ZipArchive::OVERWRITE
            );

            if ($result !== true) {
                throw new \RuntimeException(
                    'Unable to create ZIP. Error code: ' . $result
                );
            }

            $zipOpened = true;

            $rootFolder = $safeName . '/';

            if (!$zip->addEmptyDir($rootFolder)) {
                throw new \RuntimeException(
                    'Unable to create student folder.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | ADD GENERATED ENROLLMENT PDF
            |--------------------------------------------------------------------------
            */

            $pdfBinary = $pdf->render(
                $enrollment->data ?? []
            );

            if (!is_string($pdfBinary) || strlen($pdfBinary) === 0) {
                throw new \RuntimeException(
                    'Generated enrollment PDF is empty.'
                );
            }

            if (!$zip->addFromString(
                $rootFolder . 'Enrollment-Form.pdf',
                $pdfBinary
            )) {
                throw new \RuntimeException(
                    'Unable to add enrollment PDF.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | ADD UPLOADED DOCUMENTS
            |--------------------------------------------------------------------------
            */

            $enrollment->loadMissing('documents');

            $disk = Storage::disk('local');

            $requirementsFolder = $rootFolder . 'Requirements/';

            if (!$zip->addEmptyDir($requirementsFolder)) {
                throw new \RuntimeException(
                    'Unable to create Requirements folder.'
                );
            }

            // Prevent duplicate folder creation
            $createdFolders = [];

            foreach ($enrollment->documents as $document) {

                if (!$document->path || !$disk->exists($document->path)) {
                    Log::warning('Enrollment document not found.', [
                        'enrollment_id' => $enrollment->id,
                        'document_id' => $document->id,
                        'path' => $document->path,
                    ]);

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | READ FILE CONTENT
                |--------------------------------------------------------------------------
                */

                $fileContents = $disk->get($document->path);

                if (!is_string($fileContents) || $fileContents === '') {
                    Log::warning('Enrollment document is empty or unreadable.', [
                        'enrollment_id' => $enrollment->id,
                        'document_id' => $document->id,
                        'path' => $document->path,
                    ]);

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | REQUIREMENT FOLDER
                |--------------------------------------------------------------------------
                */

                $type = Str::slug(
                    $document->type ?: 'other'
                );

                $documentFolder = $requirementsFolder . $type . '/';

                if (!isset($createdFolders[$type])) {

                    if (!$zip->addEmptyDir($documentFolder)) {
                        throw new \RuntimeException(
                            'Unable to create requirement folder: ' . $type
                        );
                    }

                    $createdFolders[$type] = true;
                }

                /*
                |--------------------------------------------------------------------------
                | ORIGINAL DOCUMENT FILENAME
                |--------------------------------------------------------------------------
                */

                $originalFilename = $document->original_name
                    ?: basename($document->path);

                $originalFilename = basename(
                    str_replace('\\', '/', $originalFilename)
                );

                $originalFilename = preg_replace(
                    '/[<>:"\/\\\\|?*\x00-\x1F]/',
                    '_',
                    $originalFilename
                );

                if (empty($originalFilename)) {
                    $originalFilename = 'document-' . $document->id . '.pdf';
                }

                /*
                |--------------------------------------------------------------------------
                | ADD FILE CONTENT TO ZIP
                |--------------------------------------------------------------------------
                */

                $zipEntry = $documentFolder . $originalFilename;

                if (!$zip->addFromString($zipEntry, $fileContents)) {
                    throw new \RuntimeException(
                        'Unable to add document to ZIP: ' . $originalFilename
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CLOSE ZIP
            |--------------------------------------------------------------------------
            */

            if (!$zip->close()) {
                $zipOpened = false;

                throw new \RuntimeException(
                    'Failed to finalize ZIP archive.'
                );
            }

            $zipOpened = false;

            /*
            |--------------------------------------------------------------------------
            | VERIFY ZIP
            |--------------------------------------------------------------------------
            */

            clearstatcache(true, $zipPath);

            if (!file_exists($zipPath) || filesize($zipPath) <= 0) {
                throw new \RuntimeException(
                    'ZIP file was not created correctly.'
                );
            }

            $verifyZip = new ZipArchive();

            $verifyResult = $verifyZip->open(
                $zipPath,
                ZipArchive::CHECKCONS
            );

            if ($verifyResult !== true) {
                throw new \RuntimeException(
                    'ZIP integrity verification failed. Error code: ' .
                    $verifyResult
                );
            }

            $verifyZip->close();

            /*
            |--------------------------------------------------------------------------
            | DOWNLOAD
            |--------------------------------------------------------------------------
            */

            return response()->download(
                $zipPath,
                $safeName . '.zip',
                [
                    'Content-Type' => 'application/zip',
                    'Content-Length' => filesize($zipPath),
                    'X-Content-Type-Options' => 'nosniff',
                    'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
                    'Pragma' => 'no-cache',
                ]
            )->deleteFileAfterSend(true);

        } catch (\Throwable $e) {

            if ($zipOpened) {
                try {
                    $zip->close();
                } catch (\Throwable $closeError) {
                    Log::error('ZIP close error.', [
                        'error' => $closeError->getMessage(),
                    ]);
                }
            }

            if (file_exists($zipPath)) {
                @unlink($zipPath);
            }

            Log::error('Enrollment ZIP generation failed.', [
                'enrollment_id' => $enrollment->id,
                'student_name' => $safeName,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->withErrors([
                'zip' => 'Unable to generate ZIP file. Check storage/logs/laravel.log.',
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORT ENROLLMENTS CSV
    |--------------------------------------------------------------------------
    */

    public function export(Request $request)
    {
        $query = Enrollment::query();

        if ($request->filled('q')) {
            $search = $request->input('q');

            $query->where(function ($q) use ($search) {
                $q->where('lrn', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $rows = $query
            ->orderBy('last_name')
            ->get();

        $keys = FormFields::keys();

        return response()->streamDownload(
            function () use ($rows, $keys) {

                $output = fopen('php://output', 'w');

                // UTF-8 BOM for Excel
                fwrite($output, "\xEF\xBB\xBF");

                fputcsv($output, array_merge([
                    'ID',
                    'Saved At',
                ], $keys));

                foreach ($rows as $enrollment) {

                    $line = [
                        $enrollment->id,
                        optional($enrollment->created_at)
                            ->format('Y-m-d H:i:s'),
                    ];

                    foreach ($keys as $key) {

                        $value = $enrollment->data[$key] ?? '';

                        if (is_array($value)) {
                            $value = implode('; ', $value);
                        }

                        $line[] = $value;
                    }

                    fputcsv($output, $line);
                }

                fclose($output);
            },
            'students-' . now()->format('Ymd-His') . '.csv',
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE ENROLLMENT
    |--------------------------------------------------------------------------
    */

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        return redirect()
            ->route('admin.index')
            ->with(
                'status',
                'Enrollment record successfully deleted.'
            );
    }
}
