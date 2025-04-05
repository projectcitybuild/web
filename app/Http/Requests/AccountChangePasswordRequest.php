<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountChangePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'old_password' => 'required',
            'new_password' => ['required', 'different:old_password', Password::defaults()],
            'new_password_confirm' => 'required_with:new_password|same:new_password',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if ($validator->failed()) {
            return;
        }

        $validator->after(function ($validator) {
            $input = $validator->getData();
            $password = $input['old_password'];

            $account = Auth::user();

            if (Hash::check($password, $account->password) === false) {
                $validator->errors()->add('old_password', 'Password is incorrect');
            }
        });
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
