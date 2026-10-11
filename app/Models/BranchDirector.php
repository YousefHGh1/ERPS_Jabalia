<?php
namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchDirector extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
    protected $fillable = ['branch_id', 'employee_id', 'director_type'];
    protected $guarded  = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
