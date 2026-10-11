<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class WorkflowDepartment extends Model
{
    use Auditable;

    protected $fillable = [
        'workflow_id',
        'department_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function workflow()
    {
        return $this->belongsTo(RequestWorkflow::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
