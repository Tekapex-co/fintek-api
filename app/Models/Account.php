<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'account_name',
        'account_number',
        'balance',
        'book_balance',
        'currency',
        'status',
        'type',
        'interest_rate',
        'interest_type',
        'interest_period'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
