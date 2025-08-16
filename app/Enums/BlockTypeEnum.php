<?php

namespace App\Enums;

enum BlockTypeEnum: string
{
    case IMAGE = 'img';
    case LINK = 'link';
    case CODE = 'code';

    public static function getValues(): array
    {
        return array_map(fn($type) => $type->value, self::cases());
    }
    public static function isValid(string $value): bool
    {
        return in_array($value, self::getValues(), true);
    }
}
