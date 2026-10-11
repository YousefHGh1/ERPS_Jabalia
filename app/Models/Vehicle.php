<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;
    protected $guarded = [];
    protected $casts = [
        'deleted_at' => 'datetime',
        'images' => 'array',
    ];


}