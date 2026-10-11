<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportedExcelFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'mime_type',
        'file_size',
        'file_data',
        'records_count',
        'imported_at',
    ];

    protected $casts = [
        'imported_at' => 'datetime',
    ];
}
