<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class ResponseType extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'created_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function requestResponses()
    {
        return $this->hasMany(RequestResponse::class, 'response_type');
    }
}
