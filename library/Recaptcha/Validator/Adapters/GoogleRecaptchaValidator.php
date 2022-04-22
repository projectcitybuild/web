<?php

namespace Library\Recaptcha\Validator\Adapters;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Library\Recaptcha\Validator\RecaptchaValidator;

final class GoogleRecaptchaValidator implements RecaptchaValidator
{
    public function passed(string $token, string $ip): bool
    {
        $response = Http::asForm()->post(
            url: 'https://www.google.com/recaptcha/api/siteverify',
            data: [
                'secret' => config('recaptcha.keys.secret'),
                'response' => $token,
                'remoteip' => $ip,
            ],
        );

        Log::debug('Recaptcha response', ['response' => $response->json()]);

        return $response->json(key: 'success', default: false);
    }
}
