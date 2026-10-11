<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Reservation extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $fillable = [
        'reservation_number',
        'reservation_date',
        'reservation_time',
        'court_name',
        'judge_name',
        'case_number',
        'case_year',
        'description',
        'status',
        'created_by',
        'updated_by',
        'reservation_no',
        'subject',
        'area',
        'voucher',
        'widget',
        'address',
        'jebaia_no',
        'license_file',
        'contents',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'reservation_time' => 'datetime',
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
        return " تم {$eventName} حجز ";
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
        return $this->hasMany(ReservationAttachment::class);
    }

    public function summoner(): BelongsToMany
    {
        return $this->belongsToMany(Summoner::class, 'reservations_summoners', 'reservation_id', 'summoner_id', 'id', 'id');
    }

    public function defendant(): BelongsToMany
    {
        return $this->belongsToMany(Defendant::class, 'reservations_defendants', 'reservation_id', 'defendant_id', 'id', 'id');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'completed' => 'info'
        ];

        $statusNames = [
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'cancelled' => 'ملغي',
            'completed' => 'مكتمل'
        ];

        $color = $badges[$this->status] ?? 'secondary';
        $name = $statusNames[$this->status] ?? $this->status;

        return '<span class="badge badge-' . $color . '">' . $name . '</span>';
    }
    protected static function auditLogName(): string
    {
        return 'reservations';
    }

    protected function auditAttributes(): array
    {
        return [
            'reservation_number', 'reservation_date', 'court_name', 'judge_name', 'case_number', 'status',
        ];
    }

}