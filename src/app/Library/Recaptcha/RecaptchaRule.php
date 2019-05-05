<?php

namespace App\Library\Recaptcha;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

final class RecaptchaRule extends Rule
{
    private $client;
    private $request;

    public function __construct(Client $client, Request $request)
    {
        $this->client = $client;
        $this->request = $request;
    }

    /**
     * Disables Recaptcha for the current request
     */
    public static function enable(bool $enabled = true)
    {
        config(['recaptcha.enabled' => $enabled]);
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
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (config('recaptcha.enabled', false) === false) {
            return true;
        }

        $response = $this->client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret'    => env('RECAPTCHA_SECRET_KEY'),
                'response'  => $value,
                'remoteip'  => $this->request->ip(),
            ],
        ]);
        $result = json_decode($response->getBody(), true);

        Log::debug('Recaptcha response', ['response' => $result]);

        $success = $result['success'];
        if ($success === null || $success === false) {
            return false;
        }

        return true;
    }
}
