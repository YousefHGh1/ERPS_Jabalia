<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLateHistory extends Model
{
    use HasFactory;
    use Auditable;
    protected $table = 'employee_late_hestories';
    protected $guarded = [];
}
