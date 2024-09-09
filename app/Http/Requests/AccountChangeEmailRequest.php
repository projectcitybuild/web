<?php

namespace App\Http\Requests;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

final class AccountChangeEmailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $input = $validator->getData();
            $email = $input['email'];

            if ($email === null) {
                return;
            }

            $account = Account::whereEmail($email)->first();

            if ($account !== null) {
                if ($account->getKey() === Auth::user()->getKey()) {
                    $validator->errors()->add('email', 'You are already using this email address');
                } else {
                    $validator->errors()->add('email', 'This email address is already in use');
                }
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
