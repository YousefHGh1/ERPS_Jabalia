<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CraftFeeHistory extends Model
{
    use HasFactory;
    use Auditable;

    protected $fillable = [
        'file_number',
        'craft_type',
        'fee_amount',
        'operation_type',
        'action_date',
    ];
}
