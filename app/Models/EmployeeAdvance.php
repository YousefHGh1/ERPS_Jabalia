<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAdvance extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;

    protected $guarded = [];

    protected $casts = [
        'request_date' => 'date',
        'expected_repayment_date' => 'date',
        'section_approved_at' => 'datetime',
        'mayor_approved_at' => 'datetime',
        'finance_approved_at' => 'datetime',
        'deleted_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function sectionDirector()
    {
        return $this->belongsTo(Employee::class, 'section_director_id');
    }

    public function mayor()
    {
        return $this->belongsTo(Employee::class, 'mayor_id');
    }

    public function financeOfficer()
    {
        return $this->belongsTo(Employee::class, 'finance_officer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the status label in Arabic
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'قيد الانتظار',
            'section_approved' => 'تمت الموافقة من مدير الدائرة',
            'mayor_approved' => 'تمت الموافقة من رئيس البلدية',
            'finance_approved' => 'تمت الموافقة من دائرة المالية',
            'rejected' => 'مرفوض',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get status color class
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'pending' => 'warning',
            'section_approved' => 'info',
            'mayor_approved' => 'primary',
            'finance_approved' => 'success',
            'rejected' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Scope: Get advances for a specific employee
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope: Get advances pending approval for a specific user role
     */
    public function scopePendingForSection($query, $employeeId)
    {
        return $query->where('section_director_id', $employeeId)
            ->where('status', 'pending');
    }

    public function scopePendingForMayor($query, $employeeId)
    {
        return $query->where('mayor_id', $employeeId)
            ->where('status', 'section_approved');
    }

    public function scopePendingForFinance($query, $employeeId)
    {
        return $query->where('finance_officer_id', $employeeId)
            ->where('status', 'mayor_approved');
    }
}
