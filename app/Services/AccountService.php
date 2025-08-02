<?php

namespace App\Services;

use App\Models\Account;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class AccountService
{
    /**
     * @throws Exception
     */
    public function getTransactions(Account $account, $count = null): Collection
    {
        if (Auth::user()->account->id !== $account->id) {
            throw new Exception('Unauthorized');
        }

        return $account->transactions()->latest()->get();
    }
}
