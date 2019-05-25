<?php

namespace App\Http\Requests;

use App\Entities\Accounts\Repositories\AccountRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

final class AccountChangeEmailRequest extends FormRequest
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
     * The key to be used for the view error bag.
     *
     * @var string
     */
    protected $errorBag = 'email';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
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

            $account = $this->accountRepository->getByEmail($email);

            if ($account !== null) {
                if ($account->getKey() === Auth::user()->getKey()) {
                    $validator->errors()->add('email', 'You are already using this email address');
                } else {
                    $validator->errors()->add('email', 'This email address is already in use');
                }
                return;
            }
        });
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }
}
