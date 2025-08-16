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
    test('can get account transactions', function () {

        // create users
        $users = User::factory()
            ->count(2)
            ->has(Account::factory()->state(function (array $attributes, User $user) {
                return ['account_name' => $user->first_name.' '.$user->last_name];
            }))
            ->create();

        $userA = $users->first();
        $userB = $users->last();

        // generate transactions using factory
        Transaction::factory()->count(3)->create([
            'from_account_id' => $userA->account->id,
            'to_account_id' => $userB->account->id,
            'initiated_by' => $userA->id,
            'status' => fake()->randomElement(Status::values()),
        ]);

        Sanctum::actingAs($userA);

        $response = $this->get('api/account/transactions');

        $response->assertStatus(200);

        $responseData = $response->json();

        expect($responseData)->toHaveKey('status')
            ->and($responseData['status'])->toBeTrue()
            ->and($responseData)->toHaveKey('data')
            ->and($responseData['data'])->toBeArray()
            ->and(count($responseData['data']))->toBe(3)
            ->and($responseData['data'][0])->toBeArray()
            ->and($responseData['data'][0])->toHaveKey('id')
            ->and($responseData['data'][0])->toHaveKey('reference')
            ->and($responseData['data'][0])->toHaveKey('from')
            ->and($responseData['data'][0])->toHaveKey('to')
            ->and($responseData['data'][0])->toHaveKey('type')
            ->and($responseData['data'][0])->toHaveKey('amount')
            ->and($responseData['data'][0])->toHaveKey('note')
            ->and($responseData['data'][0])->toHaveKey('processed_at')
            ->and($responseData['data'][0])->toHaveKey('status');
    });

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

        $response = $this->post('api/account/transfer', $payload,
            ['Idempotency-key' => \Ramsey\Uuid\Uuid::uuid4()->toString()]
        );

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

    test('can get transaction details', function () {
        // create users
        $users = User::factory()
            ->count(2)
            ->has(Account::factory()->state(function (array $attributes, User $user) {
                return ['account_name' => $user->first_name.' '.$user->last_name];
            }))
            ->create();

        $userA = $users->first();
        $userB = $users->last();

        // generate transactions using factory
        $transaction = Transaction::factory()->create([
            'from_account_id' => $userA->account->id,
            'to_account_id' => $userB->account->id,
            'initiated_by' => $userA->id,
            'status' => fake()->randomElement(Status::values()),
        ]);

        Sanctum::actingAs($userA);

        $response = $this->get("api/account/transactions/{$transaction->id}");

        $response->assertStatus(200);

        $responseData = $response->json();

        expect($responseData)->toHaveKey('status')
            ->and($responseData['status'])->toBeTrue()
            ->and($responseData)->toHaveKey('data')
            ->and($responseData['data'])->toBeArray()
            ->and($responseData['data'])->toHaveKey('id')
            ->and($responseData['data'])->toHaveKey('reference')
            ->and($responseData['data'])->toHaveKey('from')
            ->and($responseData['data'])->toHaveKey('to')
            ->and($responseData['data'])->toHaveKey('type')
            ->and($responseData['data'])->toHaveKey('amount')
            ->and($responseData['data'])->toHaveKey('note')
            ->and($responseData['data'])->toHaveKey('processed_at')
            ->and($responseData['data'])->toHaveKey('status');
    });
});
