<?php

namespace App\Domains\Login\UseCases;

use App\Domains\Login\Data\LoginCredentials;
use App\Domains\Login\Exceptions\InvalidLoginCredentialsException;
use App\Http\Middleware\MfaAuthenticated;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginAccount
{
    /**
     * @throws InvalidLoginCredentialsException if email or password is incorrect
     */
    public function execute(
        LoginCredentials $credentials,
        bool $shouldRemember,
        string $ip
    ): Account {
        if (! Auth::validate(credentials: $credentials->toArray())) {
            throw new InvalidLoginCredentialsException();
        }

        $account = Account::whereEmail($credentials->email)->first();
        $account->updateLastLogin($ip);

        Auth::loginUsingId(
            id: $account->getKey(),
            remember: $shouldRemember,
        );

        // Check if the user needs to complete 2FA
        if ($account->is_totp_enabled) {
            Session::put(MfaAuthenticated::NEEDS_MFA_KEY, 'true');
        }

        return $account;
    }
}
