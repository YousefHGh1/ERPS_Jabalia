<?php

namespace App\Enums;

enum CraftLicenseStatus: string
{
    case New = 'new';
    case Renewal = 'renewal';
    case Pending = 'pending';
    case Active = 'active';
    case Stopped = 'stopped';
    case Closed = 'closed';

    public function getLabel(): string
    {
        return match($this) {
            self::New => 'جديد',
            self::Renewal => 'تجديد',
            self::Pending => 'قيد الانتظار',
            self::Active => 'نشط',
            self::Stopped => 'متوقف',
            self::Closed => 'مغلق',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::New => 'primary',
            self::Renewal => 'info',
            self::Pending => 'warning',
            self::Active => 'success',
            self::Stopped => 'secondary',
            self::Closed => 'danger',
        };
    }
}
