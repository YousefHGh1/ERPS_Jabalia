<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmployeeDependent extends Model
{
    use HasFactory;
    use SoftDeletes;
    // Auditable trait disabled due to local environment issues
    // use Auditable;

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
    protected $guarded = [];
    
    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}
