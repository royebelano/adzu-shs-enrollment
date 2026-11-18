<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Enrollment extends Model
{
    public const STATUSES = [
        'pending' => 'Pending',
        'for_compliance' => 'For Compliance',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];

    /** The enrollment workflow, in order. */
    public const STEPS = [
        1 => 'Enrollment',
        2 => 'Infirmary',
        3 => 'Admission',
        4 => 'ID Capturing',
        5 => 'GCO Assessment',
        6 => 'Enrolled',
    ];

    public const GCO_STATUSES = [
        'pending' => 'Pending',
        'done' => 'Done',
        'no_show' => 'No show',
    ];

    protected $fillable = [
        // Existing fields
        'school_year',
        'grade_level',
        'lrn',

        // Student contact and tracking information
        'phone_number',
        'email',
        'tracking_number',

        'last_name',
        'first_name',
        'middle_name',
        'extension_name',
        'birthdate',
        'sex',
        'data',

        // School year details
        'sy_from',
        'sy_to',

        // Birth details
        'bd_month',
        'bd_day',
        'bd_year',
        'place_of_birth',
        'religion',
        'mother_tongue',

        // Enrollment details
        'graded',
        'non_graded',
        'attended_elp',
        'elp_specify',
        'psa_no',

        // IP and 4Ps
        'ip_yes',
        'ip_no',
        'ip_specify',
        'fourps_yes',
        'fourps_no',
        'fourps_id',

        // Current address
        'cur_house',
        'cur_street',
        'cur_barangay',
        'cur_city',
        'cur_province',
        'cur_country',
        'cur_zip',

        // Permanent address
        'same_address_yes',
        'same_address_no',
        'per_house',
        'per_street',
        'per_barangay',
        'per_city',
        'per_province',
        'per_country',
        'per_zip',

        // Parents and guardian
        'father_last',
        'father_first',
        'father_middle',
        'father_contact',
        'mother_last',
        'mother_first',
        'mother_middle',
        'mother_contact',
        'guardian_last',
        'guardian_first',
        'guardian_middle',
        'guardian_contact',

        // Special education needs
        'sned_yes',
        'sned_no',
        'dx',
        'manifest',
        'dx_cancer',
        'dx_noncancer',
        'dx_blind',
        'dx_lowvision',

        // PWD
        'pwd_yes',
        'pwd_no',

        // Previous school
        'last_grade',
        'last_sy',
        'last_school',
        'school_id',

        // SHS
        'sem_1',
        'sem_2',
        'shs_track',
        'shs_strand',
        'modality',

        // Signature
        'parent_signature_name',
        'sign_date',

        // Admin review
        'status',
        'remarks',
        'reviewed_at',
        'reviewed_by',

        // Workflow step (1 = enrollment, 2 = infirmary, ...)
        'current_step',
        'step2_at',
        'step2_by',

        // Admission appointment
        'appointment_date',
        'appointment_time',
        'is_walk_in',
        'walk_in_by',

        'step3_at',
        'step4_at',
        'step5_at',
        'step6_at',

        // Step 3 - Admission
        'student_id',
        'adm_f137',
        'adm_psa',
        'adm_rec_mod',
        'adm_rec_gc',
        'admission_remarks',

        // Step 4 - ID Capturing
        'barcode',
        'id_capture_date',
        'id_capture_time',
        'id_capture_end',
        'id_captured',

        // Step 5 - GCO Assessment
        'gco_date',
        'gco_time',
        'gco_status',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'sign_date' => 'date',
        'data' => 'array',
        'dx' => 'array',
        'manifest' => 'array',
        'modality' => 'array',
        'reviewed_at' => 'datetime',
        'step2_at' => 'datetime',
        'step3_at' => 'datetime',
        'step4_at' => 'datetime',
        'step5_at' => 'datetime',
        'step6_at' => 'datetime',
        'appointment_date' => 'date',
        'is_walk_in' => 'boolean',
        'id_capture_date' => 'date',
        'gco_date' => 'date',

        'adm_f137' => 'boolean',
        'adm_psa' => 'boolean',
        'adm_rec_mod' => 'boolean',
        'adm_rec_gc' => 'boolean',
        'id_captured' => 'boolean',

        'graded' => 'boolean',
        'non_graded' => 'boolean',
        'attended_elp' => 'boolean',
        'ip_yes' => 'boolean',
        'ip_no' => 'boolean',
        'fourps_yes' => 'boolean',
        'fourps_no' => 'boolean',
        'same_address_yes' => 'boolean',
        'same_address_no' => 'boolean',
        'sned_yes' => 'boolean',
        'sned_no' => 'boolean',
        'dx_cancer' => 'boolean',
        'dx_noncancer' => 'boolean',
        'dx_blind' => 'boolean',
        'dx_lowvision' => 'boolean',
        'pwd_yes' => 'boolean',
        'pwd_no' => 'boolean',
        'sem_1' => 'boolean',
        'sem_2' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Enrollment $enrollment) {
            Storage::disk('local')->deleteDirectory(
                "enrollment-docs/{$enrollment->id}"
            );
        });
    }

    /**
     * Generate a unique reference / tracking ID, e.g. ADZU-2026-7K3F9Q.
     * Ambiguous characters (0/O, 1/I) are left out so it is easy to read and type.
     */
    public static function generateTrackingNumber(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }

            $tracking = 'ADZU-' . now()->format('Y') . '-' . $code;
        } while (static::where('tracking_number', $tracking)->exists());

        return $tracking;
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EnrollmentDocument::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status ?? 'pending'] ?? 'Unknown';
    }

    /** Current workflow step, always between 1 and 6. */
    public function stepNumber(): int
    {
        return max(1, min(6, (int) ($this->current_step ?? 1)));
    }

    /** True once the student has reached the given step (or gone past it). */
    public function reachedStep(int $step): bool
    {
        return $this->stepNumber() >= $step;
    }

    /** True once the student has been sent to Step 2 (Infirmary). */
    public function isInStep2(): bool
    {
        return $this->reachedStep(2);
    }

    /** Short label for where the student is now, e.g. "Step 3 · Admission". */
    public function stageLabel(): string
    {
        $step = $this->stepNumber();

        return $step === 6
            ? 'Enrolled'
            : 'Step ' . $step . ' · ' . self::STEPS[$step];
    }

    /** When the student reached a step (null if not yet). */
    public function stepReachedAt(int $step)
    {
        return match ($step) {
            1 => $this->created_at,
            2 => $this->step2_at,
            3 => $this->step3_at,
            4 => $this->step4_at,
            5 => $this->step5_at,
            6 => $this->step6_at,
            default => null,
        };
    }

    /** ABM, HUMSS, STEM or null, from the strand the student chose. */
    public function strandCode(): ?string
    {
        $raw = strtoupper((string) (
            $this->shs_strand
            ?: data_get($this->data, 'shs_strand')
            ?: data_get($this->data, 'preferred_strand')
            ?: data_get($this->data, 'strand')
        ));

        return match (true) {
            str_contains($raw, 'STEM') => 'STEM',
            str_contains($raw, 'HUMSS') => 'HUMSS',
            str_contains($raw, 'ABM') => 'ABM',
            default => null,
        };
    }

    /** Guardian first, then mother, then father. */
    public function contactPerson(): array
    {
        foreach (['guardian' => 'Guardian', 'mother' => 'Mother', 'father' => 'Father'] as $who => $relation) {
            $name = trim(implode(' ', array_filter([
                $this->{$who . '_first'},
                $this->{$who . '_middle'},
                $this->{$who . '_last'},
            ])));

            if ($name !== '') {
                return [
                    'name' => $name,
                    'relationship' => $relation,
                    'number' => (string) $this->{$who . '_contact'},
                ];
            }
        }

        return ['name' => '', 'relationship' => '', 'number' => ''];
    }

    /** Current address as one line. */
    public function currentAddress(): string
    {
        return collect([
            $this->cur_house,
            $this->cur_street,
            $this->cur_barangay,
            $this->cur_city,
            $this->cur_province,
        ])->filter(fn ($v) => filled($v))->implode(', ');
    }

    /** "13:30" becomes "1:30 PM". Empty or invalid input gives an empty string. */
    public static function formatTime(?string $time): string
    {
        if (blank($time)) {
            return '';
        }

        try {
            return \Illuminate\Support\Carbon::createFromFormat('H:i', substr($time, 0, 5))->format('g:i A');
        } catch (\Throwable $e) {
            return '';
        }
    }

    /** "May 13, 2026 · 1:30 PM - 4:30 PM", or an empty string if no date is set. */
    public function idCaptureLabel(): string
    {
        if (!$this->id_capture_date) {
            return '';
        }

        $start = self::formatTime($this->id_capture_time);
        $end = self::formatTime($this->id_capture_end);

        $time = $start && $end ? $start . ' - ' . $end : $start;

        return $this->id_capture_date->format('M d, Y') . ($time ? ' · ' . $time : '');
    }

    /** "May 14, 2026 · 8:30 AM", or an empty string if no date is set. */
    public function gcoLabel(): string
    {
        if (!$this->gco_date) {
            return '';
        }

        $time = self::formatTime($this->gco_time);

        return $this->gco_date->format('M d, Y') . ($time ? ' · ' . $time : '');
    }

    /** "Nov 17, 2026 · 8:00 AM - 9:00 AM", or an empty string when there is no appointment. */
    public function appointmentLabel(): string
    {
        if (!$this->appointment_date) {
            return '';
        }

        $time = $this->appointment_time
            ? \App\Services\AppointmentSlots::label($this->appointment_time)
            : '';

        return $this->appointment_date->format('M d, Y') . ($time ? ' · ' . $time : '');
    }

    /**
     * Male, Female or an empty string.
     *
     * The enrollment form saves everything in capital letters (MALE, FEMALE),
     * so the saved value is matched without caring about case. "M" and "F"
     * are accepted too. Old records that only have the Male or Female
     * checkbox saved in the form data are read from there.
     */
    public function sexLabel(): string
    {
        $raw = strtolower(trim((string) ($this->sex ?: data_get($this->data, 'sex'))));

        if (in_array($raw, ['male', 'm'], true)) {
            return 'Male';
        }

        if (in_array($raw, ['female', 'f'], true)) {
            return 'Female';
        }

        if ($raw === '') {
            if (data_get($this->data, 'sex_male')) {
                return 'Male';
            }

            return data_get($this->data, 'sex_female') ? 'Female' : '';
        }

        // Any other value is shown as it was typed.
        return ucfirst($raw);
    }

    /** Next student ID, like 260001 (year + 4-digit running number). */
    public static function generateStudentId(): string
    {
        $prefix = now()->format('y');

        $last = static::where('student_id', 'like', $prefix . '%')
            ->orderByDesc('student_id')
            ->value('student_id');

        $next = $last ? ((int) substr($last, 2)) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isForCompliance(): bool
    {
        return $this->status === 'for_compliance';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isReviewed(): bool
    {
        return in_array(
            $this->status,
            ['approved', 'rejected', 'for_compliance'],
            true
        );
    }
}
