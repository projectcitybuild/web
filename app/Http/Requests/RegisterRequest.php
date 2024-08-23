<?php

namespace App\Http\Requests;

use App\Core\Rules\DiscourseUsernameRule;
use Illuminate\Foundation\Http\FormRequest;

final class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:accounts,email',
            'username' => ['required', 'unique:accounts,username', new DiscourseUsernameRule()],
            'password' => 'required|min:8',    // discourse min is 8 or greater
            'password_confirm' => 'required_with:password|same:password',
            'g-recaptcha-response' => 'recaptcha',
            'terms' => 'accepted',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
