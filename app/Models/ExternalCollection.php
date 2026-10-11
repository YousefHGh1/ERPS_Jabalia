<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ExternalCollection extends Model
{
    use HasFactory, SoftDeletes;
    // Auditable trait disabled due to local environment issues
    // use Auditable;

    protected $fillable = [
        'mokalaf_id',
        'taxpayer_name',
        'identity_number',
        'section_id',
        'amount',
        'notes',
        'payment_status',
        'paid_amount',
        'remaining_amount',
        'last_payment_date',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'last_payment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * العلاقة مع المكلف
     */
    public function mokalaf()
    {
        return $this->belongsTo(Mokalaf::class);
    }

    /**
     * العلاقة مع القسم
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * العلاقة مع المستخدم الذي أنشأ السجل
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع المستخدم الذي حدث السجل
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * الحصول على اسم المكلف الكامل
     */
    public function getTaxpayerFullNameAttribute()
    {
        if ($this->mokalaf) {
            return $this->mokalaf->name;
        }
        return $this->taxpayer_name;
    }

    /**
     * الحصول على رقم الهوية
     */
    public function getTaxpayerIdentityAttribute()
    {
        if ($this->mokalaf) {
            return $this->mokalaf->identity_number;
        }
        return $this->identity_number;
    }

    /**
     * البحث عن المكلف حسب رقم الهوية
     */
    public static function findTaxpayerByIdentity($identityNumber)
    {
        return Mokalaf::where('identity_number', $identityNumber)->first();
    }

    /**
     * العلاقة مع المدفوعات
     */
    public function payments()
    {
        return $this->hasMany(ExternalCollectionPayment::class);
    }

    /**
     * الحصول على حالات الدفع المتاحة
     */
    public static function getPaymentStatuses(): array
    {
        return [
            'pending' => 'في انتظار الدفع',
            'partial' => 'مدفوع جزئياً',
            'paid' => 'مدفوع بالكامل',
            'overpaid' => 'مدفوع زيادة',
        ];
    }

    /**
     * التحقق مما إذا كان التحصيل مدفوع بالكامل
     */
    public function isFullyPaid(): bool
    {
        return $this->payment_status === 'paid' || $this->remaining_amount <= 0;
    }

    /**
     * التحقق مما إذا كان التحصيل مدفوع جزئياً
     */
    public function isPartiallyPaid(): bool
    {
        return $this->payment_status === 'partial' && $this->paid_amount > 0 && $this->remaining_amount > 0;
    }

    /**
     * التحقق مما إذا كان التحصيل في انتظار الدفع
     */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending' || $this->paid_amount == 0;
    }

    /**
     * تحديث حالة الدفع والمبالغ
     */
    public function updatePaymentStatus()
    {
        $totalPaid = (float) $this->payments()->where('status', 'paid')->sum('amount');
        $lastPayment = $this->payments()->where('status', 'paid')->latest('payment_date')->first();

        if ($totalPaid >= $this->amount) {
            $status = $totalPaid > $this->amount ? 'overpaid' : 'paid';
        } elseif ($totalPaid > 0) {
            $status = 'partial';
        } else {
            $status = 'pending';
        }

        // remaining_amount may be a generated column (storedAs), which cannot be updated directly.
        // Try full update first; if it fails, update without remaining_amount and let DB compute it.
        try {
            $this->update([
                'paid_amount' => $totalPaid,
                'remaining_amount' => (float) $this->amount - $totalPaid,
                'payment_status' => $status,
                'last_payment_date' => $lastPayment?->payment_date,
            ]);
        } catch (\Exception $e) {
            $this->update([
                'paid_amount' => $totalPaid,
                'payment_status' => $status,
                'last_payment_date' => $lastPayment?->payment_date,
            ]);
        }
    }

    /**
     * الحصول على نسبة الدفع
     */
    public function getPaymentPercentageAttribute(): float
    {
        if ($this->amount == 0) return 0;
        return ($this->paid_amount / $this->amount) * 100;
    }

    /**
     * إعادة حساب حالة الدفع يدوياً (لإصلاح البيانات الموجودة)
     */
    public function recalculatePaymentStatus()
    {
        $totalPaid = (float) $this->payments()->where('status', 'paid')->sum('amount');
        $lastPayment = $this->payments()->where('status', 'paid')->latest('payment_date')->first();

        if ($totalPaid >= $this->amount) {
            $status = $totalPaid > $this->amount ? 'overpaid' : 'paid';
        } elseif ($totalPaid > 0) {
            $status = 'partial';
        } else {
            $status = 'pending';
        }

        // تحديث مباشر في قاعدة البيانات لتجاوز مشاكل generated column
        DB::table('external_collections')
            ->where('id', $this->id)
            ->update([
                'paid_amount' => $totalPaid,
                'payment_status' => $status,
                'last_payment_date' => $lastPayment?->payment_date,
            ]);

        $this->refresh();
    }

    /**
     * الحصول على شارة حالة الدفع مع اللون المناسب
     */
    public function getPaymentStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">في انتظار الدفع</span>',
            'partial' => '<span class="badge bg-info">مدفوع جزئياً</span>',
            'paid' => '<span class="badge bg-success">مدفوع بالكامل</span>',
            'overpaid' => '<span class="badge bg-primary">مدفوع زيادة</span>',
        ];
        
        return $badges[$this->payment_status] ?? '<span class="badge bg-secondary">غير معروف</span>';
    }
}
