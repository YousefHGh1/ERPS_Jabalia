<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class RentLocation extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'address',
        'description'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function notices()
    {
        return $this->hasMany(Notice::class);
    }
}
