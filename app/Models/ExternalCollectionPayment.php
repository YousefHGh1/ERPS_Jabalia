<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class ExternalCollectionPayment extends Model
{
    use HasFactory, SoftDeletes;
    // Auditable trait disabled due to local environment issues
    // use Auditable;

    protected $table = 'external_collection_payments';

    protected $fillable = [
        'external_collection_id',
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
     * العلاقة مع التحصيل الخارجي
     */
    public function externalCollection()
    {
        return $this->belongsTo(ExternalCollection::class);
    }

    /**
     * العلاقة مع المستخدم الذي أنشأ الدفعة
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع رزمة الإيرادات
     */
    public function revenueBatch()
    {
        return $this->belongsTo(RevenueBatch::class, 'revenue_batch_id');
    }

    /**
     * الحصول على طرق الدفع المتاحة
     */
    public static function getPaymentMethods(): array
    {
        return [
            'cash' => 'نقدي',
            'bank' => 'حوالة بنكية',
            'check' => 'شيك',
            'pos' => 'نقطة بيع',
            'online' => 'دفع إلكتروني',
        ];
    }

    /**
     * الحصول على الحالات المتاحة
     */
    public static function getStatuses(): array
    {
        return [
            'paid' => 'مدفوع',
            'cancelled' => 'ملغي',
            'refunded' => 'مسترجع',
        ];
    }

    /**
     * التحقق مما إذا كانت الدفعة قابلة للتعديل
     */
    public function isEditable(): bool
    {
        return $this->status === 'paid' && (!$this->revenueBatch || $this->revenueBatch->isOpen());
    }

    /**
     * التحقق مما إذا كانت الدفعة قابلة للإلغاء
     */
    public function isCancellable(): bool
    {
        $isPaid = $this->status === 'paid';
        $hasNoBatch = !$this->revenueBatch;
        $batchIsOpen = $this->revenueBatch?->isOpen();
        
        \Log::info('isCancellable Check:', [
            'payment_id' => $this->id,
            'status' => $this->status,
            'is_paid' => $isPaid,
            'has_no_batch' => $hasNoBatch,
            'batch_status' => $this->revenueBatch?->status,
            'batch_is_open' => $batchIsOpen,
            'result' => $isPaid && ($hasNoBatch || $batchIsOpen)
        ]);
        
        return $isPaid && ($hasNoBatch || $batchIsOpen);
    }

    protected static function booted()
    {
        static::updating(function (ExternalCollectionPayment $payment) {
            if ($payment->revenueBatch && !$payment->revenueBatch->isOpen()) {
                throw new \RuntimeException('لا يمكن تعديل دفعة التحصيل الخارجي بعد إغلاق الرزمة اليومية.');
            }
        });

        static::deleting(function (ExternalCollectionPayment $payment) {
            if ($payment->revenueBatch && !$payment->revenueBatch->isOpen()) {
                throw new \RuntimeException('لا يمكن حذف دفعة التحصيل الخارجي بعد إغلاق الرزمة اليومية.');
            }
        });
    }
}
