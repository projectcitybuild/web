<?php

namespace App\Domains\Accounts\Data\Rules;

use App\Models\Account;
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
