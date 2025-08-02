<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
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

    public function getTransactionDetails(Account $account, Transaction $transaction): Transaction
    {
        $user = Auth::user();

        if ($user->account->id !== $account->id || $user->id !== $transaction->initiated_by) {
            throw new Exception('Unauthorized');
        }

        return $transaction;
    }
}
