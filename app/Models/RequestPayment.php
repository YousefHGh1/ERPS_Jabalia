<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestPayment extends Model
{
    use HasFactory, SoftDeletes;
    use Auditable;

    protected $table = 'request_payments';

    protected $fillable = [
        'request_id',
        'amount',
        'balance_after',
        'payment_date',
        'payment_method',
        'receipt_number',
        'notes',
        'created_by',
        'revenue_batch_id',
        'status',
        'cancellation_reason',
        'payment_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function citizenRequest()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function revenueBatch()
    {
        return $this->belongsTo(RevenueBatch::class, 'revenue_batch_id');
    }

    public static function getPaymentMethods(): array
    {
        return [
            'cash' => 'نقدي',
            'bank' => 'حوالة بنكية',
            'check' => 'شيك',
        ];
    }

    protected static function booted()
    {
        static::updating(function (RequestPayment $payment) {
            if ($payment->revenueBatch && ! $payment->revenueBatch->isOpen()) {
                throw new \RuntimeException('لا يمكن تعديل دفعة طلب بعد إغلاق الرزمة اليومية.');
            }
        });

        static::deleting(function (RequestPayment $payment) {
            if ($payment->revenueBatch && ! $payment->revenueBatch->isOpen()) {
                throw new \RuntimeException('لا يمكن حذف دفعة طلب بعد إغلاق الرزمة اليومية.');
            }
        });
    }
}
