<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ImprisonmentOrder extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $fillable = [
        'notice_id',
        'order_number',
        'order_date',
        'court_name',
        'judge_name',
        'defendant_name',
        'defendant_identity',
        'imprisonment_period',
        'imprisonment_type',
        'case_number',
        'case_year',
        'description',
        'notes',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'order_date' => 'date',
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
        return " تم {$eventName} أمر حبس ";
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
        return $this->hasMany(ImprisonmentOrderAttachment::class);
    }

    public function notice()
    {
        return $this->belongsTo(Notice::class);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'active' => 'success',
            'completed' => 'info',
            'cancelled' => 'danger'
        ];

        $statusNames = [
            'pending' => 'قيد الانتظار',
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي'
        ];

        $color = $badges[$this->status] ?? 'secondary';
        $name = $statusNames[$this->status] ?? $this->status;

        return '<span class="badge badge-' . $color . '">' . $name . '</span>';
    }
    protected static function auditLogName(): string
    {
        return 'imprisonment_orders';
    }

    protected function auditAttributes(): array
    {
        return ['order_number', 'order_date', 'court_name', 'defendant_name', 'status'];
    }

}