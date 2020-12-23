<?php

namespace App\Http\Requests;

use App\Entities\Accounts\Repositories\AccountRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

final class AccountChangeEmailRequest extends FormRequest
{
    /**
     * The key to be used for the view error bag.
     */
    protected string $errorBag = 'email';
    private AccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($validator): void {
            $input = $validator->getData();
            $email = $input['email'];

            if ($email === null) {
                return;
            }

            $account = $this->accountRepository->getByEmail($email);

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

    /**
     * Redirect back to the form anchor
     */
    protected function getRedirectUrl(): string
    {
        $url = $this->redirector->getUrlGenerator();
        return $url->previous() . '#change-email';
    }
}
