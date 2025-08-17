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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'duration' => (int) $this->duration,
            'interest_rate' => (float) $this->interest_rate,
            'created_at' => $this->created_at,
        ];
    }
}
