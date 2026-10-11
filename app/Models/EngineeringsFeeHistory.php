<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngineeringsFeeHistory extends Model
{
    use HasFactory;
    use Auditable;

    protected $fillable = [
        'engineering_id',
        'fee_amount',
        'operation_type',
        'action_date',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'action_date' => 'datetime',
    ];

    /**
     * العلاقة مع ملف الهندسة
     */
    public function engineering()
    {
        return $this->belongsTo(Engineering::class);
    }
}
