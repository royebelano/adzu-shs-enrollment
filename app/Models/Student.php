<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';
    protected $primaryKey = 'student_id';

    // Kung ang student_id ay string o hindi auto-incrementing integer, i-set ito:
    // public $incrementing = false;
    // protected $keyType = 'string';

    protected $casts = [
        'physical_exam' => 'boolean',
        'dental_exam' => 'boolean',
        'medical_certificate' => 'boolean',
        'form_138' => 'boolean',
        'psa' => 'boolean',
        'good_moral' => 'boolean',
    ];

  protected $fillable = [
        'student_id',
        'editable_student_id', // Idinagdag para sa malayang pag-edit ng ID
        'reference_id',
        'lrn',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'contact_number',
        'email',
        'suffix',
        'preferred_strand',
        'status',
        'medical_assessment_date',
        'gco_assessment_date',
        'physical_exam',
        'dental_exam',
        'medical_certificate',
        'school',
        'section',
        'form_138',
        'psa',
        'good_moral',
        'admission_step3_status',
        'contact_person',
        'contact_address',
        'contact_relationship',
        'barcode',
    ];

    public function documents()
    {
        return $this->hasMany(StudentDocument::class, 'student_id', 'student_id');
    }
}
