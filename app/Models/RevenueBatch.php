<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueBatch extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;

    public const KIND_UNIFIED = 'unified';

    public const KIND_SECTION = 'section';

    protected $fillable = [
        'date',
        'status',
        'opened_by_user_id',
        'closed_by_user_id',
        'opened_at',
        'closed_at',
        'total_amount',
        'payments_count',
        'sequence_number',
        'batch_kind',
        'parent_batch_id',
        'section_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'date'         => 'date',
        'opened_at'    => 'datetime',
        'closed_at'    => 'datetime',
        'total_amount' => 'decimal:2',
        'sequence_number' => 'integer',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'revenue_batch_id');
    }

    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by_user_id');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_batch_id');
    }

    public function sectionBatches()
    {
        return $this->hasMany(self::class, 'parent_batch_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeUnified($query)
    {
        return $query->where('batch_kind', self::KIND_UNIFIED);
    }

    public function scopeSectionBatchesOnly($query)
    {
        return $query->where('batch_kind', self::KIND_SECTION);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isUnified(): bool
    {
        return $this->batch_kind === self::KIND_UNIFIED;
    }

    public function isSectionBatch(): bool
    {
        return $this->batch_kind === self::KIND_SECTION;
    }

    /**
     * معرفات الرزم المستخدمة لجمع الدفعات (رزم الأقسام + الرزمة الموحدة للبيانات القديمة المباشرة).
     */
    public function collectPaymentBatchIds(): array
    {
        if ($this->isUnified()) {
            $ids = $this->sectionBatches()->pluck('id')->all();

            return array_values(array_unique(array_merge([$this->id], $ids)));
        }

        return [$this->id];
    }

    /**
     * الحصول على الرزمة المفتوحة الحالية
     */
    public static function getOpenBatch()
    {
        return self::open()->first();
    }

    /**
     * الحصول على الرزمة المفتوحة الموحدة الحالية
     */
    public static function getOpenUnifiedBatch()
    {
        return self::unified()->open()->first();
    }

    /**
     * الحصول على الرزمة المفتوحة لقسم معين
     */
    public static function getOpenSectionBatch($sectionId)
    {
        return self::sectionBatchesOnly()->where('section_id', $sectionId)->open()->first();
    }
}
