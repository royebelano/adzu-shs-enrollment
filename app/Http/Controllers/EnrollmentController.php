<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Support\FormFields;
use App\Services\AppointmentSlots;
use App\Support\Requirements;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class EnrollmentController extends Controller
{
    /**
     * Display the enrollment form.
     */
    public function create(AppointmentSlots $slots)
    {
        if (!\App\Models\Setting::enrollmentOpen()) {
            return view('enrollment.closed', [
                'schoolName' => \App\Models\Setting::schoolName(),
                'message' => \App\Models\Setting::closedMessage(),
            ]);
        }

        return view('enrollment.form', [
            'walkIn' => false,
            'apptDates' => $slots->datesAhead(),
            'schoolName' => \App\Models\Setting::schoolName(),
            'schoolYear' => \App\Models\Setting::schoolYear(),
        ]);
    }

    /**
     * Staff fill in the form for a walk-in student.
     */
    public function walkIn()
    {
        return view('enrollment.form', [
            'walkIn' => true,
            'apptDates' => [],
        ]);
    }

    /**
     * Available admission slots for one date (used by the booking calendar).
     */
    public function slots(Request $request, AppointmentSlots $slots): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $validated['date'])->startOfDay();

        if ($date->lt(today())) {
            return response()->json([
                'date' => $validated['date'],
                'open' => false,
                'message' => 'Lumipas na ang petsang ito.',
                'slots' => [],
            ]);
        }

        $list = $slots->slotsFor($date);
        $closed = $slots->dayClosed($date);

        return response()->json([
            'date' => $validated['date'],
            'open' => !$closed,
            'message' => $closed ? 'Walang schedule ang admission sa petsang ito.' : null,
            'slots' => $closed ? [] : array_map(fn ($s) => [
                'time' => $s['time'],
                'label' => $s['label'],
                'mode' => $s['mode'],
                'capacity' => $s['capacity'],
                'left' => $s['left'],
                'bookable' => $s['bookable'],
                'past' => $s['past'],
                'closed' => $s['closed'],
            ], $list),
        ]);
    }

    /**
     * Display individual enrollment details in Step 1.
     */
 public function show(Enrollment $enrollment)
{
    return view('admin.steps.step1.step1-view', compact('enrollment'));
}

    /**
     * Public page where a student checks the status of an application.
     */
    public function track()
    {
        return view('enrollment.track');
    }

    public function trackCheck(Request $request)
    {
        $validated = $request->validate([
            'tracking_number' => 'required|string|max:50',
            'email' => 'required|email|max:255',
        ]);

        $ref = strtoupper(trim($validated['tracking_number']));

        // Ref ID + email must both match, so IDs cannot be guessed by strangers.
        $enrollment = Enrollment::where('tracking_number', $ref)
            ->whereRaw('LOWER(email) = ?', [strtolower(trim($validated['email']))])
            ->first();

        if (!$enrollment) {
            return back()
                ->withInput()
                ->withErrors([
                    'tracking_number' => 'Walang nahanap na application sa Reference ID at email na ito.',
                ]);
        }

        return view('enrollment.track', ['enrollment' => $enrollment]);
    }

    /**
     * Save enrollment information and uploaded requirements.
     */
    public function store(Request $request): JsonResponse
    {
        return $this->handleStore($request, false);
    }

    /**
     * Walk-in students are entered by admission staff (login required).
     */
    public function storeWalkIn(Request $request): JsonResponse
    {
        return $this->handleStore($request, true);
    }

    protected function handleStore(Request $request, bool $walkIn): JsonResponse
    {
        // Students cannot submit while online enrollment is closed. Staff walk-ins still can.
        if (!$walkIn && !\App\Models\Setting::enrollmentOpen()) {
            return response()->json([
                'success' => false,
                'message' => \App\Models\Setting::closedMessage(),
            ], 403);
        }

        $storedFiles = [];

        try {
            /*
            |--------------------------------------------------------------------------
            | 1. DECODE FORM DATA
            |--------------------------------------------------------------------------
            */

            $rawData = $request->input('data');

            if (is_string($rawData)) {
                $data = json_decode($rawData, true);
            } elseif (is_array($rawData)) {
                $data = $rawData;
            } else {
                $data = null;
            }

            if (!is_array($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid enrollment data. Please reload the form.',
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | 2. NORMALIZE FORM FIELD NAMES
            |--------------------------------------------------------------------------
            */

            // School year.
            if (empty($data['school_year'])) {
                $data['school_year'] = $data['sy_from'] ?? null;
            }

            // Sex / Gender.
            if (empty($data['sex'])) {
                $data['sex'] = $data['gender'] ?? null;
            }

            // Birthdate.
            if (empty($data['birthdate'])) {
                $year = (int) (
                    $data['bd_year'] ??
                    $data['birth_year'] ??
                    0
                );

                $month = (int) (
                    $data['bd_month'] ??
                    $data['birth_month'] ??
                    0
                );

                $day = (int) (
                    $data['bd_day'] ??
                    $data['birth_day'] ??
                    0
                );

                if (
                    $year > 0 &&
                    $month > 0 &&
                    $day > 0 &&
                    checkdate($month, $day, $year)
                ) {
                    $data['birthdate'] = sprintf(
                        '%04d-%02d-%02d',
                        $year,
                        $month,
                        $day
                    );
                }
            }

            // Normalize contact information.
            $data['phone_number'] = trim(
                (string) ($data['phone_number'] ?? '')
            );

            $data['email'] = trim(
                (string) ($data['email'] ?? '')
            );

            $request->merge([
                'data' => $data,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 3. VALIDATION
            |--------------------------------------------------------------------------
            */

            $rules = [
                'data' => [
                    'required',
                    'array',
                ],

                'data.last_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'data.first_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'data.lrn' => [
                    'nullable',
                    'digits:12',
                ],

                'data.school_year' => [
                    'nullable',
                    'string',
                    'max:9',
                ],

                'data.birthdate' => [
                    'nullable',
                    'date_format:Y-m-d',
                ],

                'data.sex' => [
                    'nullable',
                    'string',
                    'max:6',
                ],

                'data.phone_number' => [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^[0-9+\-\s()]{7,20}$/',
                ],

                'data.email' => [
                    'required',
                    'email',
                    'max:255',
                ],
            ];

            $docRules = Requirements::rules();

            // Walk-in students hand in paper copies, so uploads are optional.
            if ($walkIn) {
                foreach ($docRules as $key => $docRule) {
                    $docRules[$key] = array_map(
                        fn ($r) => $r === 'required' ? 'nullable' : $r,
                        $docRule
                    );
                }
            } else {
                $rules['appointment_date'] = ['required', 'date_format:Y-m-d'];
                $rules['appointment_time'] = ['required', 'date_format:H:i'];
            }

            $rules = array_merge($rules, $docRules);

            $validator = Validator::make(
                $request->all(),
                $rules,
                Requirements::messages() + [
                    'appointment_date.required' => 'Pumili ng petsa ng admission appointment.',
                    'appointment_time.required' => 'Pumili ng oras ng admission appointment.',
                ],
                Requirements::attributes()
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please check your submitted information.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | 4. SANITIZE FORM DATA
            |--------------------------------------------------------------------------
            */

            $cleanData = FormFields::sanitize($data);

            if (!is_array($cleanData)) {
                $cleanData = [];
            }

            // Preserve normalized fields after sanitization.
            $fieldsToPreserve = [
                'phone_number',
                'email',
                'school_year',
                'birthdate',
                'sex',
                'sy_from',
                'sy_to',
                'bd_month',
                'bd_day',
                'bd_year',
                'last_name',
                'first_name',
                'middle_name',
                'extension_name',
                'lrn',
            ];

            foreach ($fieldsToPreserve as $field) {
                // Do not overwrite values computed by FormFields::sanitize()
                // (e.g. sex from the Male/Female checkboxes) with empty ones.
                if (
                    array_key_exists($field, $data) &&
                    $data[$field] !== null &&
                    $data[$field] !== ''
                ) {
                    $cleanData[$field] = $data[$field];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 5. PREPARE DATABASE ATTRIBUTES
            |--------------------------------------------------------------------------
            */

            $columns = Schema::getColumnListing('enrollments');

            $attributes = [
                'status' => 'pending',
            ];

            // Reference / tracking ID for the student.
            if (in_array('tracking_number', $columns, true)) {
                $attributes['tracking_number'] = Enrollment::generateTrackingNumber();
            }

            // Admission appointment.
            if (in_array('appointment_date', $columns, true)) {
                if ($walkIn) {
                    $attributes['appointment_date'] = today()->toDateString();
                    $attributes['appointment_time'] = app(AppointmentSlots::class)->currentSlotTime();
                    $attributes['is_walk_in'] = 1;
                    $attributes['walk_in_by'] = Auth::user()?->name ?: 'Admin';
                } else {
                    $attributes['appointment_date'] = $request->input('appointment_date');
                    $attributes['appointment_time'] = $request->input('appointment_time');
                    $attributes['is_walk_in'] = 0;
                }
            }

            // Save all form answers in JSON data column.
            if (in_array('data', $columns, true)) {
                $attributes['data'] = json_encode(
                    $cleanData,
                    JSON_UNESCAPED_UNICODE |
                    JSON_INVALID_UTF8_SUBSTITUTE
                );
            }

            $excludedColumns = [
                'id',
                'created_at',
                'updated_at',
                'deleted_at',
                'data',
                'tracking_number',
            ];

            // Automatically map matching database columns.
            foreach ($cleanData as $key => $value) {
                if (
                    !is_string($key) ||
                    !in_array($key, $columns, true) ||
                    in_array($key, $excludedColumns, true)
                ) {
                    continue;
                }

                if (is_array($value)) {
                    $attributes[$key] = json_encode(
                        $value,
                        JSON_UNESCAPED_UNICODE
                    );
                } elseif (is_bool($value)) {
                    $attributes[$key] = $value ? 1 : 0;
                } else {
                    $attributes[$key] = $value;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 6. EXPLICITLY MAP IMPORTANT FIELDS
            |--------------------------------------------------------------------------
            */

            $importantFields = [
                'phone_number',
                'email',
                'lrn',
                'last_name',
                'first_name',
                'middle_name',
                'extension_name',
                'grade_level',
                'school_year',
                'sex',
                'birthdate',
                'sy_from',
                'sy_to',
                'bd_month',
                'bd_day',
                'bd_year',
                'place_of_birth',
                'religion',
                'mother_tongue',
                'age',
                'psa_no',
            ];

            foreach ($importantFields as $field) {
                if (
                    array_key_exists($field, $cleanData) &&
                    in_array($field, $columns, true)
                ) {
                    $attributes[$field] = $cleanData[$field];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 7. SAVE ENROLLMENT AND DOCUMENTS
            |--------------------------------------------------------------------------
            */

            $enrollment = DB::transaction(function () use (
                $attributes,
                $request,
                $walkIn,
                &$storedFiles
            ) {

                // Online bookings: lock the schedule and check the slot again,
                // so two students cannot take the last seat at the same time.
                if (!$walkIn && isset($attributes['appointment_date'])) {
                    \App\Models\CapacitySlot::query()
                        ->whereDate('date', $attributes['appointment_date'])
                        ->lockForUpdate()
                        ->get();

                    $slotError = app(AppointmentSlots::class)->validateOnline(
                        (string) $attributes['appointment_date'],
                        (string) $attributes['appointment_time']
                    );

                    if ($slotError) {
                        throw new \DomainException($slotError);
                    }
                }

                // Save enrollment record.
                $enrollment = new Enrollment();

                $enrollment->forceFill($attributes);
                $enrollment->save();

                // Save uploaded requirements.
                foreach (Requirements::ALL as $key => $requirement) {

                    $file = $request->file("docs.{$key}");

                    if (!$file) {
                        continue;
                    }

                    if (!$file->isValid()) {
                        throw new \RuntimeException(
                            "Uploaded file for {$key} is invalid."
                        );
                    }

                    $extension = strtolower(
                        $file->getClientOriginalExtension()
                    );

                    $allowedExtensions = [
                        'jpg',
                        'jpeg',
                        'png',
                        'pdf',
                    ];

                    if (!in_array($extension, $allowedExtensions, true)) {
                        throw new \RuntimeException(
                            "Invalid file type for {$key}."
                        );
                    }

                    // Store uploaded file.
                    $path = $file->store(
                        "enrollment-docs/{$enrollment->id}",
                        'local'
                    );

                    if (!$path) {
                        throw new \RuntimeException(
                            "Unable to store uploaded document: {$key}."
                        );
                    }

                    $storedFiles[] = $path;

                    // Match actual enrollment_documents columns.
                    $document = $enrollment->documents()->make();

                    $document->forceFill([
                        'type' => $key,
                        'original_name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ]);

                    $document->save();
                }

                return $enrollment;
            });

            /*
            |--------------------------------------------------------------------------
            | 8. SUCCESS RESPONSE
            |--------------------------------------------------------------------------
            */

            $enrollment->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Enrollment successfully submitted!',
                'enrollment_id' => $enrollment->id,
                'tracking_number' => $enrollment->tracking_number,
                'appointment_label' => $enrollment->appointmentLabel(),
                'is_walk_in' => (bool) $enrollment->is_walk_in,
                'view_url' => $walkIn
                    ? route('admin.step1.view', $enrollment->id)
                    : null,
            ], 201);

        } catch (\DomainException $e) {

            // The chosen slot was taken or closed while the form was open.
            return response()->json([
                'success' => false,
                'slot_unavailable' => true,
                'message' => $e->getMessage(),
            ], 409);

        } catch (Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | 9. CLEAN UP FILES IF SAVING FAILS
            |--------------------------------------------------------------------------
            */

            foreach ($storedFiles as $path) {
                try {
                    Storage::disk('local')->delete($path);
                } catch (Throwable $cleanupError) {
                    Log::error(
                        'Enrollment document cleanup failed.',
                        [
                            'path' => $path,
                            'error' => $cleanupError->getMessage(),
                        ]
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 10. LOG ERROR
            |--------------------------------------------------------------------------
            */

            Log::error('Enrollment saving failed.', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug')
                    ? 'Save failed: ' . $e->getMessage()
                    : 'May problema sa pag-save. Subukan ulit.',
            ], 500);
        }
    }
}
