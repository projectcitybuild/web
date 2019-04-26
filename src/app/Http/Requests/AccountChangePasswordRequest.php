<?php
namespace App\Http\Requests;

use App\Entities\Accounts\Models\Account;
use App\Entities\Accounts\Repositories\AccountRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountChangePasswordRequest extends FormRequest
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
    protected $errorBag = 'password';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'old_password'          => 'required',
            'new_password'          => 'required|different:old_password|min:8',
            'new_password_confirm'  => 'required_with:new_password|same:new_password',
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
            $input    = $validator->getData();
            $password = $input['old_password'];

            $account = Auth::user();

            if (Hash::check($password, $account->password) === false) {
                $validator->errors()->add('old_password', 'Password is incorrect');
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
