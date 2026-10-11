<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ImprisonmentOrderAttachment extends Model
{
    use Auditable;
    use SoftDeletes;

    protected $fillable = [
        'imprisonment_order_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'created_by'
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function imprisonmentOrder()
    {
        return $this->belongsTo(ImprisonmentOrder::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
