<?php

namespace App\Domains\Accounts\Data\Rules;

use App\Models\Account;
use Illuminate\Validation\Rule;

trait EmailValidationRules
{
    /**
     * Get the validation rules used to validate emails.
     *
     * @return array<int, Rule|array|string>
     */
    protected function emailRules(): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique(Account::class),
        ];
    }
}
