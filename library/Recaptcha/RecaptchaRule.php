<?php

namespace Library\Recaptcha;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use function config;

class RecaptchaRule extends Rule
{
    public function __construct(
        private Client $client,
        private Request $request,
    ) {}

    /**
     * Disables Recaptcha for the current request.
     */
    public static function disable()
    {
        config(['recaptcha.enabled' => false]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'reCAPTCHA failed. Are you a bot?';
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (config('recaptcha.enabled', true) === false) {
            return true;
        }

        $response = $this->client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => config('recaptcha.keys.secret'),
                'response' => $value,
                'remoteip' => $this->request->ip(),
            ],
        ]);

        $result = json_decode($response->getBody(), associative: true);

        Log::debug('Recaptcha response', ['response' => $result]);

        $success = $result['success'];

        if ($success === null) {
            Log::warning('Recaptcha response success was null', ['response' => $result]);
            return false;
        }
        return $success;
    }
}
