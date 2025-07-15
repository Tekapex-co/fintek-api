<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    use HasUlids, HasFactory;

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

    protected $casts = [
        'type' => AccountType::class,
        'status' => Status::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
