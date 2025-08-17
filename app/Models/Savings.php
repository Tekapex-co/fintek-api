<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Savings extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'user_id',
        'name',
        'balance',
        'duration',
        'interest_rate',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
