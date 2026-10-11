<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;
    // Auditable trait disabled due to local environment issues
    // use Auditable;


    protected $casts = [
        'deleted_at' => 'datetime',
    ];
    protected $fillable = [
        'craft_license_id',
        'amount',
        'balance_after',
        'payment_method',
        'reference_number',
        'user_id',
        'revenue_batch_id',
        'payment_date',
        'notes',
        'status',
        'cancellation_reason',
        'payment_number',
    ];

    public function craftLicense()
    {
        return $this->belongsTo(CraftLicense::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function revenueBatch()
    {
        return $this->belongsTo(RevenueBatch::class, 'revenue_batch_id');
    }

    protected static function booted()
    {
        static::updating(function (Payment $payment) {
            if ($payment->revenueBatch && ! $payment->revenueBatch->isOpen()) {
                throw new \RuntimeException('لا يمكن تعديل دفعة بعد إغلاق الرزمة اليومية.');
            }
        });

        static::deleting(function (Payment $payment) {
            if ($payment->revenueBatch && ! $payment->revenueBatch->isOpen()) {
                throw new \RuntimeException('لا يمكن حذف دفعة بعد إغلاق الرزمة اليومية.');
            }
        });
    }
}
