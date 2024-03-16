<?php

namespace App\Models\Rules;

use App\Models\Eloquent\Account;
use Illuminate\Validation\Rule;

trait UsernameValidationRules
{
    /**
     * Get the validation rules used to validate usernames.
     *
     * @return array<int, Rule|array|string>
     */
    protected function usernameRules(): array
    {
        return [
            'required',
            'string',
            'max:60',
            Rule::unique(Account::class),
        ];
    }
}
