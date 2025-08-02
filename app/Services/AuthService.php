<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * @throws \Exception
     */
    public function login($data)
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        try {
            $token = $user->createToken(config('fintek.token_name'))->plainTextToken;

            return ['token' => $token];
        } catch (Exception $e) {
            throw new Exception('Error logging in: '.$e->getMessage());
        }
    }
}
