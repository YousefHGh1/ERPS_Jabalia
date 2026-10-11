<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CraftLicensePartner extends Model
{
    use Auditable;

    protected $fillable = [
        'craft_license_id',
        'identity_number',
        'name',
        'status',
        'cancellation_reason',
    ];

    public function craftLicense()
    {
        return $this->belongsTo(CraftLicense::class);
    }
}

