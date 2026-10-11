<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use App\Models\Request as RequestModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestResponse extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $fillable = [
        'response_type',
        'request_id',
        'workflow_id',
        'section_id',
        'department_id',
        'notes',
        'created_by',
        'responsed_by',
        'responsed_at',
        'ip_address',
        'host',
    ];

    protected $casts = [
        'responsed_at' => 'datetime',
    ];

    public function responseType()
    {
        return $this->belongsTo(ResponseType::class, 'response_type');
    }

    public function request()
    {
        return $this->belongsTo(RequestModel::class, 'request_id');
    }

    public function workflow()
    {
        return $this->belongsTo(RequestWorkflow::class, 'workflow_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * اسم الجهة (قسم أو دائرة) الخاصة بهذا الرد — للعرض الموحد.
     */
    public function getUnitNameAttribute(): string
    {
        if ($this->relationLoaded('section') || $this->section_id) {
            $name = $this->section->name ?? null;
            if ($name) {
                return $name;
            }
        }
        if ($this->relationLoaded('department') || $this->department_id) {
            $name = $this->department->name ?? null;
            if ($name) {
                return $name;
            }
        }
        return $this->workflow->name ?? '-';
    }

    /**
     * نوع الجهة: department | section — للعرض الموحد.
     */
    public function getUnitTypeAttribute(): ?string
    {
        if ($this->section_id) {
            return 'section';
        }
        if ($this->department_id) {
            return 'department';
        }
        if ($this->workflow) {
            return $this->workflow->department_id ? 'department' : 'section';
        }
        return null;
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function responsedBy()
    {
        return $this->belongsTo(User::class, 'responsed_by');
    }
}
