<?php

namespace App\Enums;

enum TransactionType: string
{
    case TRANSFER = 'transfer';
    case DEPOSIT = 'deposit';
    case WITHDRAWAL = 'withdrawal';
    case FEE = 'fee';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
