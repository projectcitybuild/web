<?php

namespace App\Http\Requests;

use App\Domains\Captcha\Rules\CaptchaRule;
use Illuminate\Foundation\Http\FormRequest;

final class ContactRequest extends FormRequest
{
    public function rules(CaptchaRule $captchaRule): array
    {
        return [
            'name' => ['max:80'],
            'email' => ['required', 'email'],
            'subject' => ['required', 'max:100'],
            'inquiry' => ['required', 'min:10', 'max:2000'], // 2000 due to Discord embed size limit
            'captcha-response' => ['required', $captchaRule],
        ];
    }
}
