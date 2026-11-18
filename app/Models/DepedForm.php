<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepedForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'version_tag',
        'file_path',
        'form_fields', // <-- Siguraduhing nandito ito!
        'is_active',
    ];

    // Kung gusto mo ring awtomatikong maging array/JSON ito sa Laravel:
    protected $casts = [
        'form_fields' => 'array',
        'is_active' => 'boolean',
    ];
}
