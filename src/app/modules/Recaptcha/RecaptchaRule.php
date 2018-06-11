<?php
namespace App\Modules\Recaptcha;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RecaptchaRule extends Rule {

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Request
     */
    private $request;

    public function __construct(Client $client, Request $request) {
        $this->client = $client;
        $this->request = $request;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return 'reCAPTCHA failed. Are you a bot?';
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value) {
        if(env('RECAPTCHA_ENABLED', false) === false) {
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

        $success = $result['success'];
        if($success === null || $success === false) {
            return false;
        }

        return true;
    }

}