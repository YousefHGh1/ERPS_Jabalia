<?php
namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestWorkflow extends Model
{
    use Auditable;
    use SoftDeletes;

    protected $fillable = [
        'request_type_id',
        'previous_workflow_id',
        'section_id',
        'department_id',
        'name',
        'description',
        'step_order',
        'is_active',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function requestType()
    {
        return $this->belongsTo(RequestType::class);
    }

    public function workflowDepartments()
    {
        return $this->hasMany(WorkflowDepartment::class, 'workflow_id');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'workflow_departments', 'workflow_id', 'department_id')
            ->withPivot('id')
            ->withTimestamps();
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function previousWorkflow()
    {
        return $this->belongsTo(RequestWorkflow::class, 'previous_workflow_id');
    }

    public function nextWorkflows()
    {
        return $this->hasMany(RequestWorkflow::class, 'previous_workflow_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('step_order');
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('previous_workflow_id');
    }
}
