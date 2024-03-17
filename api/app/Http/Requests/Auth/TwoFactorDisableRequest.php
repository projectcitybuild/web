<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class TwoFactorDisableRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'password' => 'required',
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->user()->two_factor_secret === null) {
                    $validator->errors()->add(
                        '2fa',
                        'Two Factor Authentication is already disabled'
                    );
                }
            },
            function (Validator $validator) {
                if (! Hash::check($this->get('password'), $this->user()->password)) {
                    $validator->errors()->add(
                        'password',
                        'Incorrect password'
                    );
                }
            }
        ];
    }
}
