<?php

namespace App\Enums;

enum FailureReason
{
    const INSUFFICIENT_FUNDS = 'insufficient funds';

    const ACCOUNT_NOT_FOUND = 'account not found';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
