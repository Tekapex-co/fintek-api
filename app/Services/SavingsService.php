<?php

namespace App\Services;

use App\Models\Savings;

class SavingsService
{
    public function getSavingsPlans()
    {
        $user = auth()->user();

        return Savings::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function createSavingsPlan($data): Savings
    {
        $user = auth()->user();
        $data['user_id'] = $user->id;

        return Savings::create($data);
    }
}
