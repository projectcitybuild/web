<?php

namespace App\Http\Requests\Auth;

use App\Domains\Accounts\Data\Rules\EmailValidationRules;
use App\Domains\Accounts\Data\Rules\PasswordValidationRules;
use App\Domains\Accounts\Data\Rules\UsernameValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    use PasswordValidationRules;
    use UsernameValidationRules;
    use EmailValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'username' => $this->usernameRules(),
            'email' => $this->emailRules(),
            'password' => $this->passwordRules(),
        ];
    }
}
