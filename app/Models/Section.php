<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory;
    use SoftDeletes;
    // Auditable trait disabled due to local environment issues
    // use Auditable;

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function director()
    {
        return $this->hasOne(SectionDirector::class)->where('director_type', 'permanent');
    }

    public function directors()
    {
        return $this->hasMany(SectionDirector::class);
    }

    /**
     * المدير المعروض في الهيكلية والطباعة بأولوية: المسكن أولاً ثم المكلف.
     */
    public function displayDirector()
    {
        return $this->hasOne(SectionDirector::class)
            ->whereIn('director_type', ['permanent', 'acting'])
            ->orderByRaw("FIELD(director_type, 'permanent', 'acting')")
            ->orderBy('id');
    }

    /**
     * اسم المدير المعروض (المسكن ثم المكلف) مع الإشارة لنوع التكليف، أو "شاغر".
     */
    public function getDirectorLabelAttribute(): string
    {
        $directors = $this->relationLoaded('directors')
            ? $this->directors
            : $this->directors()->with('employee')->get();

        $director = $directors->firstWhere('director_type', 'permanent')
            ?: $directors->firstWhere('director_type', 'acting');

        if (! $director || ! $director->employee) {
            return __('vacant');
        }

        return $director->employee->name
            . ($director->director_type === 'acting' ? ' (' . __('acting') . ')' : '');
    }

    /**
     * أسماء المسكن والمكلف منفصلين (تُستخدم في الطباعة لعرض كل نوع بلون مختلف).
     */
    public function getDirectorsMetaAttribute(): array
    {
        $directors = $this->relationLoaded('directors')
            ? $this->directors
            : $this->directors()->with('employee')->get();

        $permanent = $directors->firstWhere('director_type', 'permanent');
        $acting    = $directors->firstWhere('director_type', 'acting');

        return [
            'permanent' => $permanent?->employee?->name,
            'acting'    => $acting?->employee?->name,
        ];
    }
}
