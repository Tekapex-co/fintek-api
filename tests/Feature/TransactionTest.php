<?php

use App\Enums\Status;
use App\Enums\TransactionType;
use App\Jobs\ProcessTransaction;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

describe('Transaction', function () {
    test('can transfer funds', function () {
        Queue::fake();

        $minBalance = 1000;
        $maxBalance = 10000;

        User::factory()
            ->count(2)
            ->has(Account::factory([
                'balance' => rand($minBalance, $maxBalance),
                'status' => Status::ACTIVE,
            ])->state(function (array $attributes, User $user) {
                return ['account_name' => $user->first_name.' '.$user->last_name];
            }))
            ->create();

        $userA = User::with('account')->first();
        $userB = User::with('account')->orderByDesc('id')->first();

        Sanctum::actingAs($userA);

        $payload = [
            'from_account' => $userA->account->account_number,
            'to_account' => $userB->account->account_number,
            'amount' => 1000,
            'note' => fake()->sentence(5, true),
        ];

        $response = $this->post('api/transfer', $payload);

        $response->assertStatus(200);

        $responseData = $response->json();
        $transaction = Transaction::latest()->first();

        expect($responseData)->toHaveKey('status')
            ->and($responseData['status'])->toBeTrue()
            ->and($responseData)->toHaveKey('message')
            ->and($responseData['message'])->toBe('Transaction initiated successfully.')
            ->and(Transaction::count())->toBeGreaterThan(0)
            ->and($transaction)->toBeInstanceOf(Transaction::class)
            ->and($transaction->reference)->toBeString()
            ->and($transaction->type)->toBe(TransactionType::TRANSFER->value)
            ->and($transaction->from_account_id)->toBe($userA->account->id)
            ->and($transaction->to_account_id)->toBe($userB->account->id)
            ->and($transaction->amount)->toBeGreaterThan(0)
            ->and($transaction->status)->toBe(Status::PROCESSING->value)
            ->and($transaction->initiated_by)->toBe($userA->id);

        Queue::assertPushed(ProcessTransaction::class);

    });
});
