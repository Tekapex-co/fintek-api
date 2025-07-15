<?php

namespace App\Enums;

enum Status: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case CLOSED = 'closed';
    case PENDING = 'pending';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
