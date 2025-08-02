<?php

namespace App\Services;

use App\Enums\Status;
use App\Enums\TransactionType;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\InvalidTransactionException;
use App\Jobs\ProcessTransaction;
use App\Models\Account;
use App\Models\Transaction;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class TransactionService
{
    /**
     * @throws InsufficientFundsException
     * @throws InvalidTransactionException
     */
    public function transfer($data): void
    {
        $user = Auth::user();
        $fromAccount = Account::where('account_number', $data['from_account'])->first();
        $toAccount = Account::where('account_number', $data['to_account'])->first();

        $this->validateTransfer($user, $fromAccount, $toAccount, $data['amount']);

        $transaction = Transaction::create([
            'from_account_id' => $fromAccount->id,
            'to_account_id' => $toAccount->id,
            'type' => TransactionType::TRANSFER,
            'amount' => $data['amount'],
            'status' => Status::PROCESSING,
            'note' => $data['note'] ?? null,
            'initiated_by' => $user->id,
        ]);

        ProcessTransaction::dispatch($transaction);
    }

    /**
     * @throws InvalidTransactionException
     * @throws InsufficientFundsException
     */
    private function validateTransfer($user, Account $fromAccount, Account $toAccount, float $amount): void
    {
        if ($user->account->account_number !== $fromAccount->account_number) {
            throw new UnauthorizedException('Unauthorized');
        }

        if ($amount <= 0) {
            throw new InvalidTransactionException('Transfer amount must be positive');
        }

        if ($fromAccount->id === $toAccount->id) {
            throw new InvalidTransactionException('Cannot transfer to the same account');
        }

        if ($fromAccount->balance < $amount) {
            throw new InsufficientFundsException('Insufficient funds');
        }

        if ($fromAccount->currency !== $toAccount->currency) {
            throw new InvalidTransactionException('Currency mismatch');
        }

        if ($fromAccount->status !== Status::ACTIVE || $toAccount->status !== Status::ACTIVE) {
            throw new InvalidTransactionException('One or both accounts are inactive');
        }
    }

    /**
     * @throws Exception
     */
    public function getTransactions($count = null): Collection
    {
        $user = Auth::user()->load('account');

        if (! $user->account) {
            throw new ModelNotFoundException('User account not found');
        }

        return $user->account->transactions()->latest()->get();
    }

    public function getTransactionDetails(Transaction $transaction): Transaction
    {
        $user = Auth::user()->load('account');

        if ($transaction->from_account_id !== $user->account->id && $transaction->to_account_id !== $user->account->id) {
            throw new UnauthorizedException('You are not authorized to view this transaction');
        }

        return $transaction;
    }
}
