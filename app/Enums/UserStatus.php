<?php

namespace App\Enums;

use App\Traits\EnumMethods;

enum UserStatus: int
{
    use EnumMethods;

    case IS_ACTIVE = 1;
    case NOT_ACTIVE = 2;

    public function name(): mixed
    {
        return match ($this) {
            self::IS_ACTIVE => 'Ativo',
            self::NOT_ACTIVE => 'Inativo'
        };
    }
}
