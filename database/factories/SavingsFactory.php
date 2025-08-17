<?php

namespace Database\Factories;

use App\Models\Savings;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SavingsFactory extends Factory
{
    protected $model = Savings::class;

    public function definition(): array
    {
        $availableDurations = [3, 6, 12];
        $duration = $availableDurations[array_rand($availableDurations)];

        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word().' Plan',
            'duration' => $duration,
            'interest_rate' => $duration == 3 ? 2.5 : ($duration == 6 ? 5 : 12),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
