<?php

use App\Enums\Status;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('Account', function () {
    test('can retrieve transactions between two users', function () {

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

        $response = $this->get("api/accounts/{$userA->account->id}/transactions");

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
});
