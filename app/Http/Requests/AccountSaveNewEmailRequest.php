<?php

namespace App\Http\Requests;

use Entities\Repositories\AccountRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class AccountSaveNewEmailRequest extends FormRequest
{
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'new_email' => 'required|email',
            'old_email' => 'required|email',
            'password' => 'required',
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
            $oldEmail = $input['old_email'];
            $password = $input['password'];

            $account = $this->accountRepository->getByEmail($oldEmail);
            if ($account === null) {
                throw new \Exception('Failed to find account by email address');
            }

            if (Hash::check($password, $account->password) === false) {
                $validator->errors()->add('password', 'Password is incorrect');
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
