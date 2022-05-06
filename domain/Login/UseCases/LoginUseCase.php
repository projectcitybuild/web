<?php

namespace Domain\Login\UseCases;

use App\Http\Middleware\MfaGate;
use Domain\Login\Entities\LoginCredentials;
use Domain\Login\Exceptions\AccountNotActivatedException;
use Domain\Login\Exceptions\InvalidLoginCredentialsException;
use Entities\Repositories\AccountRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * @final
 */
class LoginUseCase
{
    public function __construct(
        private AccountRepository $accountRepository,
    ) {}

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

        $account = $this->accountRepository->getByEmail($credentials->email);

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
