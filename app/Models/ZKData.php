<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZKData extends Model
{
    use HasFactory;
    protected $guarded= [];

    public function employee()
    {
        return $this->belongsTo(EmployeeJobInfo::class, 'emp_id', 'emp_number');
    }
}
