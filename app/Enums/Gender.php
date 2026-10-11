<?php

namespace App\Enums;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';

    public function getLabel(): string
    {
        return match($this) {
            self::Male => 'ذكر',
            self::Female => 'أنثى',
        };
    }
}
