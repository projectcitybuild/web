<?php

namespace App\Http\Requests;

use App\Core\Domains\Captcha\Rules\CaptchaRule;
use App\Core\Rules\DiscourseUsernameRule;
use Illuminate\Foundation\Http\FormRequest;

final class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(CaptchaRule $captchaRule): array
    {
        return [
            'email' => ['required', 'email', 'unique:accounts', 'email'],
            'username' => ['required', 'unique:accounts,username', new DiscourseUsernameRule()],
            'password' => ['required', 'min:12'],
            'captcha-response' => ['required', $captchaRule],
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
