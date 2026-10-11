<?php
namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;


    protected $casts = [
        'deleted_at' => 'datetime',
    ];
    protected $guarded = [];

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function categoryDegrees()
    {
        return $this->hasMany(CategoryDegree::class);
    }

    public function degrees()
    {
        return $this->categoryDegrees();
    }
}
