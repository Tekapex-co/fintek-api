<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Enums\Status;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'account_name' => fake()->name(),
            'account_number' => fake()->numerify('##########'),
            'balance' => 0,
            'book_balance' => 0,
            'status' => Status::ACTIVE,
            'type' => AccountType::SAVINGS,
            'interest_rate' => 0,
            'interest_type' => 'flat',
            'interest_period' => 'monthly',
        ];
    }
}
