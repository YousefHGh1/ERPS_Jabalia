<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mokalaf extends Model
{
    use HasFactory, SoftDeletes;
    // Auditable trait disabled due to local environment issues
    // use Auditable;

    protected $fillable = [
        'name',
        'identity_number',
        'license_number',
        'address',
        'mobile',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getIdentifierAttribute()
    {
        return $this->identity_number ?: $this->license_number ?: '-';
    }

    public function getIdentifierTypeAttribute()
    {
        if ($this->identity_number) {
            return 'هوية';
        } elseif ($this->license_number) {
            return 'مشغل مرخص';
        }
        return '-';
    }

    /**
     * الطلبات المرتبطة بهذا المكلف (طلب ترخيص بناء، طلب حرفة، إلخ)
     */
    public function requests()
    {
        return $this->hasMany(\App\Models\Request::class);
    }
}
