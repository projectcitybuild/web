<?php
namespace Front\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Library\RateLimit\TokenRate;
use App\Library\RateLimit\TokenBucket;
use App\Library\RateLimit\Storage\SessionTokenStorage;

class LoginRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array {
        return [
            'email'     => 'required',
            'password'  => 'required',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator) {
        if ($validator->fails()) {
            return;
        }

        $refillRate = TokenRate::refill(3)->every(2, TokenRate::MINUTES);
        $sessionStorage = new SessionTokenStorage('login.rate', 5);
        $rateLimit = new TokenBucket(6, $refillRate, $sessionStorage);

        $validator->after(function ($validator) use($rateLimit) {
            if ($rateLimit->consume(1) === false) {
                $validator->errors()->add('error',  'Too many login attempts. Please try again in a few minutes');
            }
        });

        $validator->after(function ($validator) use($rateLimit) {
            $input      = $validator->getData();
            $email      = $input['email'];
            $password   = $input['password'];

            $credentials = [
                'email'     => $email,
                'password'  => $password,
            ];

            if(Auth::attempt($credentials, true) === false) {
                $triesLeft = floor($rateLimit->getAvailableTokens());
                $validator->errors()->add('error', 'Email or password is incorrect: '.$triesLeft.' attempts remaining');
            }
        });
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool {
        return true;
    }

}