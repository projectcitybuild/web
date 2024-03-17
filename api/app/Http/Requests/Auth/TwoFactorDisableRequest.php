<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class TwoFactorDisableRequest extends FormRequest
{
    private const RATE_LIMIT_KEY = 'login';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'password' => 'required',
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->user()->two_factor_secret === null) {
                    $validator->errors()->add(
                        '2fa',
                        'Two Factor Authentication is already disabled'
                    );
                }
            }
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Hash::check($this->get('password'), $this->user()->password)) {
            RateLimiter::hit(self::RATE_LIMIT_KEY);

            throw ValidationException::withMessages([
                'password' => 'Incorrect password',
            ]);
        }

        RateLimiter::clear(self::RATE_LIMIT_KEY);
    }

    private function ensureIsNotRateLimited(): void
    {
        if (RateLimiter::tooManyAttempts(self::RATE_LIMIT_KEY, maxAttempts: 5)) {
            $seconds = RateLimiter::availableIn(self::RATE_LIMIT_KEY);

            abort(429, __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]));
        }
    }
}
