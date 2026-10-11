<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Document extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $fillable = [
        'document_number',
        'document_type',
        'document_date',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'document_date' => 'date',
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
        return " تم {$eventName} مستند ";
    }
public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'secondary',
            'active' => 'success',
            'archived' => 'info',
            'deleted' => 'danger'
        ];

        $statusNames = [
            'draft' => 'مسودة',
            'active' => 'نشط',
            'archived' => 'مؤرشف',
            'deleted' => 'محذوف'
        ];

        $color = $badges[$this->status] ?? 'secondary';
        $name = $statusNames[$this->status] ?? $this->status;

        return '<span class="badge badge-' . $color . '">' . $name . '</span>';
    }

    public function getFileSizeFormattedAttribute()
    {
        if ($this->file_size >= 1073741824) {
            return number_format($this->file_size / 1073741824, 2) . ' GB';
        } elseif ($this->file_size >= 1048576) {
            return number_format($this->file_size / 1048576, 2) . ' MB';
        } elseif ($this->file_size >= 1024) {
            return number_format($this->file_size / 1024, 2) . ' KB';
        } elseif ($this->file_size > 0) {
            return $this->file_size . ' bytes';
        } else {
            return '0 bytes';
        }
    }
    protected static function auditLogName(): string
    {
        return 'documents';
    }

    protected function auditAttributes(): array
    {
        return ['document_number', 'document_type', 'title', 'status'];
    }

}