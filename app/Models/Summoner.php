<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Summoner extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $guarded = [];

    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservations_summoners', 'summoner_id', 'reservation_id', 'id', 'id');
    }
}
