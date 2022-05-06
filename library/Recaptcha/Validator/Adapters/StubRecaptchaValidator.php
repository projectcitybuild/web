<?php

namespace Library\Recaptcha\Validator\Adapters;

use Library\Recaptcha\Validator\RecaptchaValidator;

final class StubRecaptchaValidator implements RecaptchaValidator
{
    public function __construct(
        private bool $passed,
    ) {}

    public function passed(?string $token, string $ip): bool
    {
        return $this->passed;
    }
}
