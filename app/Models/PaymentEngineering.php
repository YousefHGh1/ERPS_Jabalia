<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentEngineering extends Model
{
    use HasFactory, SoftDeletes;
    // Auditable trait disabled due to local environment issues
    // use Auditable;

    protected $fillable = [
        'engineering_id',
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

    /**
     * العلاقة مع ملف الهندسة
     */
    public function engineering()
    {
        return $this->belongsTo(Engineering::class);
    }

    /**
     * العلاقة مع المستخدم الذي قام بالدفع
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * الحصول على طرق الدفع المتاحة
     */
    public static function getPaymentMethods()
    {
        return [
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'check' => 'شيك',
        ];
    }

    public function revenueBatch()
    {
        return $this->belongsTo(RevenueBatch::class, 'revenue_batch_id');
    }

    protected static function booted()
    {
        static::updating(function (PaymentEngineering $payment) {
            if ($payment->revenueBatch && ! $payment->revenueBatch->isOpen()) {
                throw new \RuntimeException('لا يمكن تعديل دفعة هندسة بعد إغلاق الرزمة اليومية.');
            }
        });

        static::deleting(function (PaymentEngineering $payment) {
            if ($payment->revenueBatch && ! $payment->revenueBatch->isOpen()) {
                throw new \RuntimeException('لا يمكن حذف دفعة هندسة بعد إغلاق الرزمة اليومية.');
            }
        });
    }
}
