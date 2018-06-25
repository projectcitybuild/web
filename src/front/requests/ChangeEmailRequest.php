<?php
namespace Front\Requests;

use App\Modules\Accounts\Models\Account;
use App\Modules\Accounts\Repositories\AccountRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangeEmailRequest extends FormRequest {

    /**
     * @var AccountRepository
     */
    private $accountRepository;


    public function __construct(AccountRepository $accountRepository) {
        $this->accountRepository = $accountRepository;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array {
        return [
            'email'     => 'required|email',
            'password'  => 'required|min:8',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator) {
        $validator->after(function ($validator) {
            $input    = $validator->getData();
            $email    = $input['email'];
            $password = $input['password'];

            $account = $this->accountRepository->getByEmail($email);

            if ($account !== null) {
                if ($account->getKey() === Auth::user()->getKey()) {
                    $validator->errors()->add('email', 'You are already using this email address');
                } else {
                    $validator->errors()->add('email', 'This email address is already in use');
                }
                return;
            }

            $currentUser = Auth::user();
            if (Hash::check($password, $currentUser->password) === false) {
                $validator->errors()->add('email', 'Password is incorrect');
            }
        });
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool {
        return true;
    }

}