<?php

namespace App\Domains\Login\UseCases;

use App\Domains\Login\Entities\LoginCredentials;
use App\Domains\Login\Exceptions\AccountNotActivatedException;
use App\Domains\Login\Exceptions\InvalidLoginCredentialsException;
use App\Http\Middleware\MfaGate;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Repositories\AccountRepository;

/**
 * @final
 */
class LoginAccount
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
    ) {
    }

    /**
     * @throws InvalidLoginCredentialsException if email or password is incorrect
     * @throws AccountNotActivatedException if account not verified
     */
    public function execute(
        LoginCredentials $credentials,
        bool $shouldRemember,
        string $ip
    ) {
        if (! Auth::validate(credentials: $credentials->toArray())) {
            throw new InvalidLoginCredentialsException();
        }

        $account = Account::whereEmail($credentials->email)->first();

        if (! $account->activated) {
            throw new AccountNotActivatedException();
        }

        Auth::loginUsingId(
            id: $account->getKey(),
            remember: $shouldRemember,
        );

        $this->accountRepository->touchLastLogin($account, $ip);

        // Check if the user needs to complete 2FA
        if ($account->is_totp_enabled) {
            Session::put(MfaGate::NEEDS_MFA_KEY, 'true');
        }
    }
}
