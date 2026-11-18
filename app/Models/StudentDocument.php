<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    use HasFactory;

    protected $table = 'student_documents';
    protected $primaryKey = 'document_id';

    protected $fillable = [
        'student_id',
        'document_name',
        'document_type',
        'file_path',
        'status',
        'remarks',
    ];
}
