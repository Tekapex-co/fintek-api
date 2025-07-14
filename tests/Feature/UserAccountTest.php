<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

describe('User Account', function () {
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
            ->and($user->account->status)->toBe('active')
            ->and($user->account->type)->toBe('savings');
    });
});

