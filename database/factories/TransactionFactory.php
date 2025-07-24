<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Enums\Status;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fromAccountId = Account::factory();
        $toAccountId = Account::factory();

        return [
            'reference' => $this->faker->uuid(),
            'from_account_id' => $fromAccountId->id,
            'to_account_id' => $toAccountId->id,
            'type' => $this->faker->randomElement(AccountType::values()),
            'amount' => $this->faker->randomFloat(2, 10),
            'status' => $this->faker->randomElement(Status::values()),
            'initiated_by' => $fromAccountId->user->id,
        ];
    }
}
