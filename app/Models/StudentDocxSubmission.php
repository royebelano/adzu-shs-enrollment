<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocxSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'parsed_data',
    ];

    protected $casts = [
        'parsed_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
