<?php
namespace Infrastructure\Library\Recaptcha;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Log\Logger;

class RecaptchaRule extends Rule
{

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Logger
     */
    private $log;


    public function __construct(Client $client, Request $request, Logger $logger)
    {
        $this->client = $client;
        $this->request = $request;
        $this->log = $logger;
    }

    /**
     * Disables Recaptcha for the current request
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

        $this->log->debug('Recaptcha response', ['response' => $result]);

        $success = $result['success'];
        if ($success === null || $success === false) {
            return false;
        }

        return true;
    }
}
