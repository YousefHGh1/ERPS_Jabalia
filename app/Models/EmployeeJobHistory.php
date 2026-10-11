<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeJobHistory extends Model
{
    use HasFactory;
    use SoftDeletes;
    // Auditable trait disabled due to local environment issues
    // use Auditable;

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
    protected $table = 'employee_job_hestories';
    protected $guarded = [];
    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}
