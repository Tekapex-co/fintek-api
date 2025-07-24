<?php

namespace App\Enums;

enum AccountType: string
{
    case SAVINGS = 'savings';
    case CURRENT = 'current';
    case FIXED = 'fixed';
    case LOAN = 'loan';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::SAVINGS => 'Savings Account',
            self::CURRENT => 'Current Account',
            self::FIXED => 'Fixed Deposit',
            self::LOAN => 'Loan Account',
        };
    }
}
