<?php

namespace App\Http\Requests\Me;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                'current_password:web',
            ],
            'password' => $this->passwordRules(),
        ];
    }

    public function messages()
    {
        return [
            'current_password.current_password' => 'The provided password does not match your current password.',
        ];
    }
}
