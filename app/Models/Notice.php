<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Notice extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $fillable = [
        'notice_number',
        'identity_number',
        'notice_date',
        'notice_reason_id',
        'rent_location_id',
        'description',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'notice_date' => 'date',
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
        return " تم {$eventName} إخطار ";
    }
public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function noticeReason()
    {
        return $this->belongsTo(NoticeReason::class);
    }

    public function rentLocation()
    {
        return $this->belongsTo(RentLocation::class);
    }

    public function attachments()
    {
        return $this->hasMany(NoticeAttachment::class);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'secondary',
            'sent' => 'info',
            'delivered' => 'success',
            'cancelled' => 'danger'
        ];

        $statusNames = [
            'draft' => 'مسودة',
            'sent' => 'مرسل',
            'delivered' => 'مسلّم',
            'cancelled' => 'ملغي'
        ];

        $color = $badges[$this->status] ?? 'secondary';
        $name = $statusNames[$this->status] ?? $this->status;

        return '<span class="badge badge-' . $color . '">' . $name . '</span>';
    }
    protected static function auditLogName(): string
    {
        return 'notices';
    }

    protected function auditAttributes(): array
    {
        return [
            'notice_number', 'notice_date', 'notice_reason_id', 'rent_location_id', 'status',
        ];
    }

}