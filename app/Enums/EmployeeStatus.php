<?php

namespace App\Enums;

enum EmployeeStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';

    public function getLabel(): string
    {
        return match($this) {
            self::Active => 'نشط',
            self::Inactive => 'غير نشط',
            self::Suspended => 'موقوف',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::Active => 'success',
            self::Inactive => 'secondary',
            self::Suspended => 'danger',
        };
    }
}
