<?php

namespace App\Core\Domains\Recaptcha\Rules;

use App\Core\Domains\Recaptcha\Validator\RecaptchaValidator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

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
