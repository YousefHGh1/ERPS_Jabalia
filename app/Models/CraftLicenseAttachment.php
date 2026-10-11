<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CraftLicenseAttachment extends Model
{
    use Auditable;

    protected $fillable = [
        'craft_license_id',
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

    public function craftLicense()
    {
        return $this->belongsTo(CraftLicense::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

