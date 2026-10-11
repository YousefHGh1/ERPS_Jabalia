<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestType extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;
    protected $fillable = [
        'name',
        'description'
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function workflows()
    {
        return $this->hasMany(RequestWorkflow::class);
    }
}
