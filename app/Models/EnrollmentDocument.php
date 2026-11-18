<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentDocument extends Model
{
    protected $fillable = [
        'enrollment_id',
        'type',
        'original_name',
        'path',
        'mime',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    /**
     * Relationship to enrollment.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Check whether the uploaded document is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with(
            (string) $this->mime,
            'image/'
        );
    }

    /**
     * Check whether the uploaded document is a PDF.
     */
    public function isPdf(): bool
    {
        return $this->mime === 'application/pdf';
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }

    /**
     * Get the original uploaded filename.
     */
    public function getFilenameAttribute(): string
    {
        return $this->original_name ?: basename(
            (string) $this->path
        );
    }
}
