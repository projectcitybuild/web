<?php

namespace App\Actions\Auth;

use App\Models\Eloquent\Account;
use App\Models\Rules\EmailValidationRules;
use App\Models\Rules\PasswordValidationRules;
use App\Models\Rules\UsernameValidationRules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;
    use UsernameValidationRules;
    use EmailValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param array<string, string> $input
     * @throws ValidationException
     */
    public function create(array $input): Account
    {
        Validator::make($input, [
            'username' => $this->usernameRules(),
            'email' => $this->emailRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        return Account::create([
            'username' => $input['username'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
