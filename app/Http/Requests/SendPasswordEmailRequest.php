<?php

namespace App\Http\Requests;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

final class SendPasswordEmailRequest extends FormRequest
{
    /**
     * @var Account
     */
    private $account;

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
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
            $email = $input['email'];

            if (empty($email)) {
                return;
            }
            $account = Account::whereEmail($email)->first();

            if ($account === null) {
                $validator->errors()->add('email', 'No account belongs to the given email address');
            }

            $this->account = $account;
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
