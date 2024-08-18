<?php

namespace App\Http\Requests;

use App\Core\Rules\DiscourseUsernameRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PanelUpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // TODO: Check user has permissions to update users
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
            'email' => ['required', 'email', Rule::unique('accounts')->ignore($this->route('account'), 'account_id')],
            'username' => ['required', Rule::unique('accounts')->ignore($this->route('account'), 'account_id'), new DiscourseUsernameRule()],
        ];
    }
}
