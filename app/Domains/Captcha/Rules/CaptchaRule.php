<?php

namespace App\Domains\Captcha\Rules;

use App\Domains\Captcha\Validator\CaptchaValidator;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;

class CaptchaRule implements ValidationRule
{
    public function __construct(
        private readonly Request $request,
        private readonly CaptchaValidator $captchaValidator,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail('Captcha must be completed');
            return;
        }
        $passed = $this->captchaValidator->passed(
            token: $value,
            ip: $this->request->ip(),
        );
        if (! $passed) {
            $fail('Captcha failed. Please try again');
        }
    }
}
