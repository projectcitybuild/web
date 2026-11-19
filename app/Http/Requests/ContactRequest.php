<?php

namespace App\Http\Requests;

use App\Domains\Captcha\Rules\CaptchaRule;
use App\Domains\HoneyPot\Rules\HoneyPotRule;
use Illuminate\Foundation\Http\FormRequest;

final class ContactRequest extends FormRequest
{
    public function rules(
        CaptchaRule $captchaRule,
        HoneyPotRule $honeyPotRule,
    ): array {
        return [
            'name' => ['max:80'],
            'email' => ['required', 'email'],
            'subject' => ['required', 'max:100'],
            'inquiry' => ['required', 'min:10', 'max:2000'], // 2000 due to Discord embed size limit
            'captcha-response' => ['required', $captchaRule],
            'details' => $honeyPotRule,
        ];
    }
}
