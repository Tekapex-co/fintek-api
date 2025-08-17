<?php

use App\Models\Account;
use App\Models\Savings;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\withoutExceptionHandling;

describe('Savings', function () {

    test('user can get plans', function () {
        $user = User::factory()
            ->has(Account::factory()
                ->state(function (array $attributes, User $user) {
                    return ['account_name' => $user->first_name.' '.$user->last_name];
                }))
            ->has(Savings::factory()
                ->state(function (array $attributes, User $user) {
                    return ['user_id' => $user->id];
                })->count(3))
            ->create();

        Sanctum::actingAs($user);

        $response = $this->get('api/savings');

        $response->assertStatus(200);

        $responseData = $response->json();

        expect($responseData['data'])
            ->toBeArray()
            ->toHaveCount(3)
            ->and($responseData['data'][0])
            ->toBeArray()
            ->toHaveKeys(['id', 'name', 'duration', 'interest_rate', 'created_at'])
            ->and($responseData['data'][0]['id'])->toBeString()
            ->and($responseData['data'][0]['name'])->toBeString()
            ->and($responseData['data'][0]['duration'])->toBeNumeric()->toBeIn([3, 6, 12])
            ->and($responseData['data'][0]['interest_rate'])->toBeNumeric()->toBeIn([2.5, 5, 12])
            ->and($responseData['data'][0]['created_at'])->toBeString();
    });

    test('user can create plan', function () {
        // authenticate user
        $user = User::factory()
            ->has(Account::factory()
                ->state(function (array $attributes, User $user) {
                    return ['account_name' => $user->first_name.' '.$user->last_name];
                }))
            ->create();

        Sanctum::actingAs($user);

        // create plan
        $availableDurations = [3, 6, 12];
        $duration = $availableDurations[array_rand($availableDurations)];

        $payload = [
            'name' => fake()->word().' Plan',
            'duration' => $duration,
            'interest_rate' => $duration == 3 ? 2.5 : ($duration == 6 ? 5 : 12),
        ];

        $response = $this->post('api/savings', $payload, [
            'Idempotency-key' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        ]);

        $response->assertStatus(201);

        $responseData = $response->json();

        expect($responseData['data'])
            ->toBeArray()
            ->and($responseData['data'])
            ->toHaveKeys(['id', 'name', 'duration', 'interest_rate', 'created_at'])
            ->and($responseData['data']['id'])->toBeString()
            ->and($responseData['data']['name'])->toBeString()
            ->and($responseData['data']['duration'])->toBeNumeric()->toBeIn([3, 6, 12])
            ->and($responseData['data']['interest_rate'])->toBeNumeric()->toBeIn([2.5, 5, 12])
            ->and($responseData['data']['created_at'])->toBeString();
    });
});
