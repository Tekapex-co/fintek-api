<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * @throws \Exception
     */
    public function createUserAccount($data)
    {
        try {
            return DB::transaction(function () use ($data) {

                // create user record
                $user = User::create($data);

                // create an account for user
                $user->account()->create([
                    'user_id' => $user->id,
                    'account_name' => $user->first_name . ' ' . $user->last_name,
                    'account_number' => rand(1000000000, 9999999999),
                    'currency' => 'NGN',
                    'status' => 'active',
                    'type' => 'savings',
                    'interest_rate' => 0.00,
                    'interest_type' => 'flat',
                    'interest_period' => 'monthly'
                ]);

                // return user token
                return $user->createToken(config('fintek.token_name'))->plainTextToken;
            });
        } catch (\Exception $e) {
            throw new \Exception('Error creating user account: ' . $e->getMessage());
        }
    }
}
