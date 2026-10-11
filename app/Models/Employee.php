<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;
    // Auditable trait disabled due to local environment issues
    // use Auditable;

    protected $fillable = ['czn_id', 'emp_number', 'name', 'email', 'gender', 'status', 'address', 'mobile',
        'birthdate', 'birthplace', 'salary', 'department_id', 'section_id', 'branch_id', 'place_of_work', 'job_id'];

    protected $casts = [
        'deleted_at' => 'datetime',
        'czn_id'     => 'string',
        'mobile'     => 'string',
    ];

    public function scopeVisibleTo($query, $user = null)
    {
        $user ??= auth()->user();

        if ($user?->hasRole('admin')) {
            return $query;
        }

        return $query->whereNotIn('name', ['شاغر', 'admin']);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getCurrentJobAttribute()
    {
        return $this->EmployeeJobInfo->current_job ?? $this->job->name ?? '';
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function job()
    {
        return $this->belongsTo(Job::class);
    }
    public function user()
    {
        return $this->hasOne(User::class);
    }
    public function EmployeeJobInfo()
    {
        return $this->hasOne(EmployeeJobInfo::class);
    }
    public function employeeHoliday()
    {
        return $this->hasMany(EmployeeHoliday::class);
    }
    public function employeeBonus()
    {
        return $this->hasMany(EmployeeBonus::class);
    }
    public function employeeDependents()
    {
        return $this->hasMany(EmployeeDependent::class);
    }
    public function employeeJobHistories()
    {
        return $this->hasMany(EmployeeJobHistory::class);
    }
    public function employeeMainHoliday()
    {
        return $this->hasMany(EmployeeMainHoliday::class);
    }
    public function employeePermissions()
    {
        return $this->hasMany(EmployeePermission::class);
    }
    public function employeeAdvances()
    {
        return $this->hasMany(EmployeeAdvance::class);
    }
}
