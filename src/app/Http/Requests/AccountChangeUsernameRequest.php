<?php

namespace App\Http\Requests;

use App\Entities\Accounts\Repositories\AccountRepository;
use App\Rules\DiscourseUsernameRule;
use Illuminate\Foundation\Http\FormRequest;

class AccountChangeUsernameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => ['required', 'unique:accounts,username', new DiscourseUsernameRule]
        ];
    }
}
