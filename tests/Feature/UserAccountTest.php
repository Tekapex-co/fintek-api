<?php

use App\Enums\AccountType;
use App\Enums\Status;
use App\Models\Account;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('User', function () {
    it('can retrieve a user account', function () {
        $user = User::factory()
            ->has(Account::factory()->state(function (array $attributes, User $user) {
                return ['account_name' => $user->first_name . ' ' . $user->last_name];
            }))
            ->create();

        Sanctum::actingAs($user);

        $response = $this->get('api/users/' . $user->id);

        $response->assertStatus(200);

        $responseData = $response->json();

        expect($responseData['data'])->toHaveKey('id')
            ->and($responseData['data']['id'])->toBe($user->id)
            ->and($responseData['data']['first_name'])->toBe($user->first_name)
            ->and($responseData['data']['last_name'])->toBe($user->last_name)
            ->and($responseData['data']['email'])->toBe($user->email)
            ->and($responseData['data']['account']['account_name'])->toBe($user->first_name . ' ' . $user->last_name)
            ->and($responseData['data']['account']['account_number'])->toBeNumeric()
            ->and($responseData['data']['account']['balance'])->toBeGreaterThanOrEqual(0)
            ->and($responseData['data']['account']['book_balance'])->toBeGreaterThanOrEqual(0)
            ->and($responseData['data']['account']['status'])->toBe(Status::ACTIVE->value)
            ->and($responseData['data']['account']['type'])->toBe(AccountType::SAVINGS->value);
    });

    it('can create a user account', function () {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $email = fake()->unique()->safeEmail();
        $password = 'F1Ntek#Pass!';

        $response = $this->post('api/users', [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'token'
                ]
            ]);

        $responseData = $response->json();
        expect($responseData['data'])->toHaveKey('token')
            ->and($responseData['data']['token'])->toBeString()
            ->and(User::count())->toBe(1);

        $user = User::first();
        expect($user->account)->not->toBeNull()
            ->and($user->account->account_name)->toBe($firstName . ' ' . $lastName)
            ->and($user->account->account_number)->toBeNumeric()
            ->and($user->account->balance)->toBe(0)
            ->and($user->account->book_balance)->toBe(0)
            ->and($user->account->status)->toBe(Status::ACTIVE)
            ->and($user->account->type)->toBe(AccountType::SAVINGS);
    });
});

