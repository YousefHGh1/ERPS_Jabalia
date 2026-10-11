<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeHoliday extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function holiday()
    {
        return $this->belongsTo(Holiday::class);
    }

}