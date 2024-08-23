<?php

namespace App\Core\Domains\Recaptcha\Validator\Adapters;

use App\Core\Domains\Recaptcha\Validator\RecaptchaValidator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class GoogleRecaptchaValidator implements RecaptchaValidator
{
    public function passed(?string $token, string $ip): bool
    {
        if (empty($token)) {
            return false;
        }
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
