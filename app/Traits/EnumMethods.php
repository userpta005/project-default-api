<?php

namespace App\Traits;

trait EnumMethods
{
    public static function all(array $types = []): array
    {
        $enums = [];
        foreach (self::cases() as $enum) {
            $arr = [
                'id' => $enum->value,
                'name' => $enum->name(),
            ];

            if (! empty($types) && in_array($enum, $types)) {
                $enums[] = $arr;
                break;
            }

            $enums[] = $arr;
        }

        return $enums;
    }
}
