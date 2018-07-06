<?php
namespace Front\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use bandwidthThrottle\tokenBucket\storage\SessionStorage;
use bandwidthThrottle\tokenBucket\Rate;
use bandwidthThrottle\tokenBucket\TokenBucket;
use App\Modules\Authentication\Services\LoginRateLimitService;

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

        $rateLimiter = resolve(LoginRateLimitService::class);

        $validator->after(function ($validator) use($rateLimiter) {
            dump($rateLimiter->consume(1));
            dd($rateLimiter->getRemainingTokens());
            if (!$rateLimiter->consume(1)) {
                $validator->errors()->add('error',  'Too many login attempts. Please try again in a few minutes');
            }
        });

        $validator->after(function ($validator) use($rateLimiter) {
            $input      = $validator->getData();
            $email      = $input['email'];
            $password   = $input['password'];

            $credentials = [
                'email'     => $email,
                'password'  => $password,
            ];

            if(Auth::attempt($credentials, true) === false) {
                $triesLeft = $rateLimiter->getRemainingTokens();
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