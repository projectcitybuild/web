<?php

namespace App\Http\Requests;

use App\Rules\DiscourseUsernameRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PanelUpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: Check user has permissions to update users
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', Rule::unique('accounts')->ignore($this->route('account'), 'account_id')],
            'username' => ['required', Rule::unique('accounts')->ignore($this->route('account'), 'account_id'), new DiscourseUsernameRule()],
        ];
    }
}
