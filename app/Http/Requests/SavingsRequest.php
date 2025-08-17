<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavingsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'integer', Rule::in([3, 6, 12])],
            'interest_rate' => [
                'required',
                'numeric',
                Rule::in([2.5, 5, 12]),
                function ($attribute, $value, $fail) {
                    $duration = $this->input('duration');
                    $validPairs = [
                        3 => 2.5,
                        6 => 5,
                        12 => 12,
                    ];

                    if (isset($validPairs[$duration]) && $validPairs[$duration] != $value) {
                        $fail("The interest rate must be {$validPairs[$duration]} when duration is {$duration}.");
                    }
                },
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
