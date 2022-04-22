<?php

namespace Library\Recaptcha\Validator\Adapters;

use Library\Recaptcha\Validator\RecaptchaValidator;

final class StubRecaptchaValidator implements RecaptchaValidator
{
    public function passed(string $token, string $ip): bool
    {
        return true;
    }
}
