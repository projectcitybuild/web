<?php

namespace App\Http\Requests;

use App\Entities\Accounts\Models\Account;
use App\Entities\Accounts\Repositories\AccountRepository;
use Illuminate\Foundation\Http\FormRequest;

final class SendPasswordEmailRequest extends FormRequest
{
    private Account $account;

    private AccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'email|required',
            'g-recaptcha-response' => 'recaptcha',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        if ($validator->failed()) {
            return;
        }

        $validator->after(function ($validator): void {
            $input = $validator->getData();
            $email = $input['email'];

            $account = $this->accountRepository->getByEmail($email);

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
