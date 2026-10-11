<?php
namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentDirector extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
    protected $fillable = ['department_id', 'employee_id', 'director_type'];
    protected $guarded  = [];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
