<?php

namespace App\Domains\Captcha\Validator\Adapters;

use App\Domains\Captcha\Validator\CaptchaValidator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class TurntileCaptchaValidator implements CaptchaValidator
{
    public function passed(?string $token, string $ip): bool
    {
        if (empty($token)) {
            return false;
        }
        $response = Http::asForm()->post(
            url: 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            data: [
                'secret' => config('captcha.keys.secret'),
                'response' => $token,
                'remoteip' => $ip,
            ],
        );

        Log::debug('Captcha response', ['response' => $response->json()]);

        return $response->json(key: 'success', default: false);
    }
}
