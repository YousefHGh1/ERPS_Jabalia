<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Engineering extends Model
{
    use HasFactory, SoftDeletes;
    // Auditable trait disabled due to local environment issues
    // use Auditable;

    protected $guarded = [];

    protected $casts = [
        'floors_count' => 'array',
        'deleted_at' => 'datetime',
    ];

    public function getDescriptionForEvent(string $eventName): string
    {
        $action = match ($eventName) {
            'created' => 'إنشاء',
            'updated' => 'تعديل',
            'deleted' => 'حذف',
            default => $eventName,
        };

        return " تم {$action} ملف هندسي ";
    }

    protected static function auditLogName(): string
    {
        return 'engineerings';
    }

    protected function auditAttributes(): array
    {
        return [
            'file_number', 'first_name', 'father_name', 'family_name', 'national_id',
            'plot_number', 'neighborhood', 'total_amount', 'paid_amount', 'remaining_amount',
        ];
    }
    /**
     * العلاقة مع الدفعات
     */
    public function payments()
    {
        return $this->hasMany(PaymentEngineering::class);
    }

    /**
     * العلاقة مع سجل التعديلات
     */
    public function feeHistories()
    {
        return $this->hasMany(EngineeringsFeeHistory::class);
    }

    public function attachments()
    {
        return $this->hasMany(EngineeringAttachment::class);
    }

    /**
     * العلاقة مع المستخدم الذي أنشأ السجل
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
