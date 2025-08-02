<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'type' => $this->type,
            'from' => $this->fromAccount->account_name,
            'to' => $this->toAccount->account_name,
            'amount' => $this->amount,
            'note' => $this->note,
            'processed_at' => $this->processed_at,
            'status' => $this->status,
        ];
    }
}
