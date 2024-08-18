<?php

namespace App\Core\Domains\Recaptcha\Validator\Adapters;

use App\Core\Domains\Recaptcha\Validator\RecaptchaValidator;

final class StubRecaptchaValidator implements RecaptchaValidator
{
    public function __construct(
        private readonly bool $passed,
    ) {
    }

    public function passed(?string $token, string $ip): bool
    {
        return $this->passed;
    }
}
