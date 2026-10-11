<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Request extends Model
{
    use HasFactory, SoftDeletes;
    // Auditable trait disabled due to local environment issues
    // use Auditable;

    protected $fillable = [
        'text',
        'request_type_id',
        'status_id',
        'status_reason',
        'mokalaf_id',
        'deadline',
        'is_urgent',
        'join',
        'common_name',
        'address',
        'mobile',
        'jebaia_no',
        'neighborhood',
        'widget',
        'voucher',
        'divider',
        'building',
        'floor',
        'apartment',
        'owner',
        'tenant',
        'created_by',
        'updated_by',
        'fee_amount',
        'paid_amount',
        'remaining_amount',
    ];

    protected $casts = [
        'deadline' => 'date',
        'is_urgent' => 'boolean',
        'join' => 'boolean',
        'fee_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getDescriptionForEvent(string $eventName): string
    {
        if ($eventName == 'created'){
            $eventName = 'إنشاء';
        }else if($eventName == 'updated'){
            $eventName = 'تعديل';
        }else if ($eventName == 'deleted'){
            $eventName = 'حذف';
        }
        return " تم {$eventName} طلب ";
    }
public function requestType()
    {
        return $this->belongsTo(RequestType::class);
    }

    public function status()
    {
        return $this->belongsTo(RequestStatus::class);
    }

    public function mokalaf()
    {
        return $this->belongsTo(Mokalaf::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function attachments()
    {
        return $this->hasMany(RequestAttachment::class);
    }

    public function payments()
    {
        return $this->hasMany(RequestPayment::class, 'request_id')
            ->orderByDesc('payment_date')
            ->orderByDesc('id');
    }

    public function responses()
    {
        return $this->hasMany(RequestResponse::class, 'request_id');
    }

    /**
     * Get the maximum response type for this request (excluding waiting status).
     * Returns: 1=approved, 2=pending, 3=rejected
     */
    public function maxResponseType()
    {
        return $this->responses()
            ->where('response_type', '!=', 4) // Exclude "قيد الانتظار"
            ->max('response_type');
    }

    /**
     * Count waiting responses (response_type = 4).
     */
    public function countWaiting()
    {
        return $this->responses()
            ->where('response_type', 4)
            ->count();
    }

    /**
     * Count responses by type.
     */
    public function countResponsesByType($responseType)
    {
        return $this->responses()
            ->where('response_type', $responseType)
            ->count();
    }

    /**
     * الأقسام/الدوائر التي رفضت الطلب (response_type = 3).
     */
    public function rejectedSections()
    {
        $responses = $this->relationLoaded('responses')
            ? $this->responses->where('response_type', 3)
            : $this->responses()->where('response_type', 3)->with(['section', 'department'])->get();
        return $responses->map(fn($r) => $r->unit_name)->filter(fn($n) => $n !== '-')->unique()->values();
    }

    /**
     * الأقسام/الدوائر التي علقت على الطلب (response_type = 2).
     */
    public function pendingSections()
    {
        $responses = $this->relationLoaded('responses')
            ? $this->responses->where('response_type', 2)
            : $this->responses()->where('response_type', 2)->with(['section', 'department'])->get();
        return $responses->map(fn($r) => $r->unit_name)->filter(fn($n) => $n !== '-')->unique()->values();
    }

    /**
     * نص يوضح الجهة (قسم/دائرة) التي علقت أو رفضت الطلب؛ للعرض بجانب الحالة.
     * يُرجع null إذا كانت الحالة ليست مرفوض أو معلق.
     */
    public function getStatusReasonSectionsTextAttribute(): ?string
    {
        $statusName = $this->status->name ?? null;
        if ($statusName === 'مرفوض') {
            $sections = $this->rejectedSections();
            if ($sections->isEmpty()) {
                return null;
            }
            $names = $sections->implode('، ');
            return $sections->count() === 1
                ? 'من: ' . $names
                : 'من: ' . $names;
        }
        if ($statusName === 'معلق') {
            $sections = $this->pendingSections();
            if ($sections->isEmpty()) {
                return null;
            }
            $names = $sections->implode('، ');
            return $sections->count() === 1
                ? 'من: ' . $names
                : 'من: ' . $names;
        }
        return null;
    }

    /**
     * Query scope: Get pending requests (status = "قيد الانتظار")
     */
    public function scopePending($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('name', 'قيد الانتظار');
        });
    }

    /**
     * Query scope: Get urgent requests
     */
    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    /**
     * Query scope: Get requests by status ID
     */
    public function scopeByStatus($query, int $statusId)
    {
        return $query->where('status_id', $statusId);
    }

    /**
     * Query scope: Get requests by request type ID
     */
    public function scopeByRequestType($query, int $requestTypeId)
    {
        return $query->where('request_type_id', $requestTypeId);
    }

    /**
     * Query scope: Get requests by mokalaf ID
     */
    public function scopeByMokalaf($query, int $mokalafId)
    {
        return $query->where('mokalaf_id', $mokalafId);
    }

    /**
     * Query scope: Get requests created by user
     */
    public function scopeByCreator($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Query scope: Get requests with deadline approaching (within next 7 days)
     */
    public function scopeDeadlineApproaching($query, int $days = 7)
    {
        return $query->whereNotNull('deadline')
            ->whereBetween('deadline', [now(), now()->addDays($days)])
            ->where('deadline', '>=', now())
            ->whereHas('status', function ($q) {
                $q->where('color', '!=', 'success');
            });
    }

    /**
     * Query scope: Get requests with overdue deadline
     */
    public function scopeOverdue($query)
    {
        return $query->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->whereHas('status', function ($q) {
                $q->where('color', '!=', 'success');
            });
    }

    /**
     * Query scope: Search in requests text and mokalaf name
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('text', 'like', "%{$term}%")
              ->orWhere('id', $term)
              ->orWhere('address', 'like', "%{$term}%")
              ->orWhere('mobile', 'like', "%{$term}%")
              ->orWhere('jebaia_no', 'like', "%{$term}%")
              ->orWhere('common_name', 'like', "%{$term}%")
              ->orWhere('owner', 'like', "%{$term}%")
              ->orWhere('tenant', 'like', "%{$term}%")
              ->orWhereHas('requestType', function ($q) use ($term) {
                  $q->where('name', 'like', "%{$term}%");
              })
              ->orWhereHas('status', function ($q) use ($term) {
                  $q->where('name', 'like', "%{$term}%");
              })
              ->orWhereHas('mokalaf', function ($q) use ($term) {
                  $q->where('name', 'like', "%{$term}%")
                    ->orWhere('identity_number', 'like', "%{$term}%")
                    ->orWhere('license_number', 'like', "%{$term}%")
                    ->orWhere('mobile', 'like', "%{$term}%");
              });
        });
    }

    /**
     * Query scope: Filter by date range
     */
    public function scopeDateRange($query, $fromDate = null, $toDate = null)
    {
        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }
        
        return $query;
    }
    protected static function auditLogName(): string
    {
        return 'requests';
    }

    protected function auditAttributes(): array
    {
        return [
            'text', 'request_type_id', 'status_id', 'status_reason',
            'mokalaf_id', 'deadline', 'is_urgent',
        ];
    }

}