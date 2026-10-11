<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // Auditable trait disabled due to local environment issues
    // use Auditable;
    use HasRoles;

    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false)->orderBy('created_at', 'desc');
    }

    public function logs(){
        return $this->hasMany(ZKData::class,'emp_id','employee_number');
    }

    protected function auditAttributes(): array
    {
        return [
            'name', 'email', 'mobile', 'employee_id', 'employee_number',
            'theme', 'language', 'font_size', 'avatar',
            'email_notifications', 'system_notifications', 'sms_notifications',
        ];
    }
}