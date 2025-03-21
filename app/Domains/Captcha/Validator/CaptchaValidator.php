<?php

namespace App\Domains\Captcha\Validator;

interface CaptchaValidator
{
    /**
     * Fetches the recaptcha result of the given token and IP
     *
     * @param ?string  $token
     * @param string  $ip IP address of the user
     * @return bool Whether the user passed the validation
     */
    public function passed(?string $token, string $ip): bool;
}
