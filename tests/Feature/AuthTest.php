<?php

use App\Models\Account;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('User authentication', function () {

    test('user can login', function () {

        $user = User::factory()
            ->has(Account::factory()->state(function (array $attributes, User $user) {
                return ['account_name' => $user->first_name.' '.$user->last_name];
            }))
            ->create();

        $response = $this->post('api/login', [
            'email' => $user->email,
            'password' => 'F1Ntek#Pass!',
        ]);

        $response->assertStatus(200);

        $responseData = $response->json();
        expect($responseData['data'])->toHaveKey('token')
            ->and($responseData['data']['token'])->toBeString()
            ->and($user->tokens()->count())->toBe(1)
            ->and($user->account)->toBeInstanceOf(Account::class);

        return $user;
    });

    test('user can logout', function ($user) {

        Sanctum::actingAs($user);

        $response = $this->post('api/logout');

        $response->assertStatus(204);

        expect($user->tokens()->count())->toBe(0);
    })->depends('user can login');
});
