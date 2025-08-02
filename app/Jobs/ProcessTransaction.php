<?php

namespace App\Jobs;

use App\Enums\FailureReason;
use App\Enums\Status;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ProcessTransaction implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Transaction $transaction) {}

    /**
     * Execute the job.
     *
     * @throws \Throwable
     */
    public function handle(): void
    {
        $this->transaction->update(['processed_at' => now()]);

        DB::beginTransaction();

        // get sender account balance
        $senderAccount = Account::where('id', $this->transaction->from_account_id)
            ->first();

        // debit sender
        if ($senderAccount->balance < $this->transaction->amount) {
            $this->updateTransactionStatus(
                $this->transaction,
                Status::FAILED,
                FailureReason::INSUFFICIENT_FUNDS
            );
        }

        $senderAccount->decrement('balance', $this->transaction->amount);

        // credit receiving account
        $receiverAccount = Account::where('id', $this->transaction->to_account_id)
            ->first();

        if (is_null($receiverAccount)) {
            $this->updateTransactionStatus(
                $this->transaction,
                Status::FAILED,
                FailureReason::ACCOUNT_NOT_FOUND
            );
        }

        $receiverAccount->increment('balance', $this->transaction->amount);

        // update transaction status
        $this->updateTransactionStatus($this->transaction, Status::COMPLETED);

        DB::commit();
    }

    private function updateTransactionStatus($transaction, $status, $failureReason = null): void
    {
        // update transaction status
        $this->transaction->update([
            'status' => $status,
            'processed_at' => now(),
            'failure_reason' => $failureReason,
        ]);

        // send push notification - insufficient funds
    }
}
