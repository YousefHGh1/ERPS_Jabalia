<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;
    protected $guarded = [];
    protected $casts = [
        'deleted_at' => 'datetime',
        'days' => 'array',
    ];
    public function getLateStartTimestamp()
    {
        return Carbon::parse($this->start_time)->addMinutes($this->start_late)->format('H:i:s');
    }
    public function getLateEndTimestamp()
    {
        return Carbon::parse($this->end_time)->subMinutes($this->end_late)->format('H:i:s');
    }
    public function  employeeJobInfos()
    {
        return $this->belongsToMany(EmployeeJobInfo::class,'employee_job_infos_shift','shift_id','employee_job_info_id');
    }
   
}