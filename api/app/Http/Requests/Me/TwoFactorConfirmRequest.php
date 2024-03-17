<?php

namespace App\Http\Requests\Me;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class TwoFactorConfirmRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required',
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
                        'Two Factor Authentication is not enabled'
                    );
                }
            },
            function (Validator $validator) {
                if ($this->user()->two_factor_confirmed_at !== null) {
                    $validator->errors()->add(
                        '2fa',
                        'Two Factor Authentication is already confirmed'
                    );
                }
            },
        ];
    }
}
