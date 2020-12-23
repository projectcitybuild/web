<?php

namespace App\Library\Recaptcha;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Validation\Rule;

class RecaptchaRule extends Rule
{
    private Client $client;

    private Request $request;

    private Logger $log;

    public function __construct(Client $client, Request $request, Logger $logger)
    {
        $this->client = $client;
        $this->request = $request;
        $this->log = $logger;
    }

    /**
     * Disables Recaptcha for the current request
     */
    public static function enable(bool $enabled = true): void
    {
        config(['recaptcha.enabled' => $enabled]);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'reCAPTCHA failed. Are you a bot?';
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  mixed  $value
     */
    public function passes(string $attribute, $value): bool
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
        $result = json_decode($response->getBody(), true);

        $this->log->debug('Recaptcha response', ['response' => $result]);

        $success = $result['success'];
        if ($success === null || $success === false) {
            return false;
        }

        return true;
    }
}
