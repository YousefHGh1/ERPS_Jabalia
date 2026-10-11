<?php

namespace App\Enums;

enum MaritalStatus: string
{
    case Single = 'single';
    case Married = 'married';
    case Divorced = 'divorced';
    case Widow = 'widow';

    public function getLabel(): string
    {
        return match($this) {
            self::Single => 'أعزب',
            self::Married => 'متزوج',
            self::Divorced => 'مطلق',
            self::Widow => 'أرمل',
        };
    }
}
