<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Repositories\AccountRepository;

final class AccountChangeEmailRequest extends FormRequest
{
    /**
     * The key to be used for the view error bag.
     *
     * @var string
     */
    protected $errorBag = 'email';

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
        $validator->after(function ($validator) {
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
     * Redirect back to the form anchor.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        $url = $this->redirector->getUrlGenerator();

        return $url->previous().'#change-email';
    }
}
