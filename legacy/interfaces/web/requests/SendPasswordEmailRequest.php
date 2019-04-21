<?php
namespace Interfaces\Web\Requests;

use Entities\Accounts\Models\Account;
use Entities\Accounts\Repositories\AccountRepository;
use Illuminate\Foundation\Http\FormRequest;

class SendPasswordEmailRequest extends FormRequest
{

    /**
     * @var Account
     */
    private $account;

    /**
     * @var AccountRepository
     */
    private $accountRepository;


    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function getAccount() : ?Account
    {
        return $this->account;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'email'                 => 'email|required',
            'g-recaptcha-response'  => 'required|recaptcha',
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

            if ($account === null) {
                $validator->errors()->add('email', 'No account belongs to the given email address');
            }

            $this->account = $account;
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
