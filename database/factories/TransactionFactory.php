<?php

namespace Database\Factories;

use App\Enums\Status;
use App\Enums\TransactionType;
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
        $fromAccount = Account::factory()->create();

        return [
            'from_account_id' => $fromAccount->id,
            'to_account_id' => Account::factory(),
            'type' => $this->faker->randomElement(TransactionType::values()),
            'amount' => $this->faker->randomFloat(2, 10),
            'status' => $this->faker->randomElement(Status::values()),
            'initiated_by' => $fromAccount->user_id,
        ];
    }
}
