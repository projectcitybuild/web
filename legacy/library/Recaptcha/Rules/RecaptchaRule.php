<?php

namespace Library\Recaptcha\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Library\Recaptcha\Validator\RecaptchaValidator;

class RecaptchaRule implements Rule
{
    public function __construct(
        private Request $request,
        private RecaptchaValidator $recaptchaValidator,
    ) {
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'reCAPTCHA failed. Please try again';
    }

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        return $this->recaptchaValidator->passed(
            token: $value,
            ip: $this->request->ip(),
        );
    }
}
