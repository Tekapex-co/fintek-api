<?php

namespace App\Http\Resources;

use App\Models\Savings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Savings */
class SavingsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $progress = min(100, max(0, round(
            $this->created_at->diffInMonths(now()) / $this->duration * 100
        )));

        return [
            'id' => $this->id,
            'name' => $this->name,
            'balance' => (float) $this->balance,
            'duration' => (int) $this->duration,
            'interest_rate' => (float) $this->interest_rate,
            'progress' => $progress,
            'created_at' => $this->created_at,
        ];
    }
}
