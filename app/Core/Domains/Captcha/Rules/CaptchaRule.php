<?php

namespace App\Core\Domains\Captcha\Rules;

use App\Core\Domains\Captcha\Validator\CaptchaValidator;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Validator;

class RecaptchaRule implements ValidationRule, DataAwareRule
{
    protected $data = [];

    public function __construct(
        private readonly CaptchaValidator $recaptchaValidator,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $passed = $this->recaptchaValidator->passed(
            token: $value,
            ip: $this->request->ip(),
        );
        if (! $passed) {
            $fail('Captcha failed. Please try again');
        }
    }

    public function setData(array $data)
    {
        // TODO: Implement setData() method.
    }
}
