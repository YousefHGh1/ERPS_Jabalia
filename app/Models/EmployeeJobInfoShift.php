<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeJobInfoShift extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;


    protected $casts = [
        'deleted_at' => 'datetime',
    ];
    protected $guarded = [];
    
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    public function employeeJobInfo()
    {
        return $this->belongsTo(EmployeeJobInfo::class);
    }
}