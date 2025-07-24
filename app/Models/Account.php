<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory, HasUlids;

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
        'interest_period',
    ];

    protected $casts = [
        'type' => AccountType::class,
        'status' => Status::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return Transaction::where('from_account_id', $this->id)
            ->orWhere('to_account_id', $this->id)
            ->orderBy('created_at', 'desc');
    }

    public function incomingTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    public function outgoingTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'from_account_id');
    }
}
