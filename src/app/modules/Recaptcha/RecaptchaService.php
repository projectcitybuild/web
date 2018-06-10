<?php
namespace App\Modules\Recaptcha;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;


class RecaptchaService {
    
    /**
     * The POST field name of reCAPTCHA
     *
     * @var string
     */
    public $field = 'g-recaptcha-response';

    public $errorMessage = 'reCAPTCHA verification failed';

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client) {
        $this->client = $client;
    }



    /**
     * Determines whether the given request
     * passed the reCAPTCHA test
     *
     * @param Request $request
     * @return bool
     */
    public function fails(Request $request) : bool {
        if(env('RECAPTCHA_ENABLED', false) === false) {
            return false;
        }

        $response = $this->client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret'    => env('RECAPTCHA_SECRET_KEY'),
                'response'  => $request->get($this->field),
                'remoteip'  => $request->ip(),
            ],
        ]);
        $result = json_decode($response->getBody(), true);

        $success = $result['success'];
        if($success === null || $success === false) {
            return true;
        }

        return false;
    }

    /**
     * Adds an error message to the given validator
     * if the request failed the reCAPTCHA test
     *
     * @param Request $request
     * @param Validator $validator
     * @return void
     */
    public function validate(Request $request, Validator $validator) {
        if($this->fails($request)) {
            $validator->errors()->add($this->field, 'reCAPTCHA failed. Are you a bot?');
        }
    }

}