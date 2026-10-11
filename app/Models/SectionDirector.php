<?php
namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionDirector extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
    protected $fillable = ['section_id', 'employee_id', 'director_type'];
    protected $guarded  = [];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
