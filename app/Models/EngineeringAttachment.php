<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class EngineeringAttachment extends Model
{
    use Auditable;

    protected $fillable = [
        'engineering_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function engineering()
    {
        return $this->belongsTo(Engineering::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

