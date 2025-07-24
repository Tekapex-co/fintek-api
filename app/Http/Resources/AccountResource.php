<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'balance' => (float) $this->balance,
            'book_balance' => (float) $this->book_balance,
            'currency' => $this->currency,
            'status' => $this->status,
            'type' => $this->type,
        ];
    }
}
