<?php

namespace App\Enums;

use App\Traits\EnumMethods;

enum IsActive: int
{
    use EnumMethods;

    case YES = 1;
    case NOT = 2;

    public function name(): mixed
    {
        return match ($this) {
            self::YES => 'Sim',
            self::NOT => 'Não'
        };
    }
}
