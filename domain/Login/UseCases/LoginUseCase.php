<?php

namespace Domain\Login\UseCases;

use App\Entities\Models\Eloquent\Account;
use App\Http\Middleware\MfaGate;
use Domain\Login\Entities\LoginCredentials;
use Domain\Login\Exceptions\AccountNotActivatedException;
use Domain\Login\Exceptions\InvalidLoginCredentialsException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Shared\ExternalAccounts\Sync\ExternalAccountSync;

/**
 * @final
 */
class LoginUseCase
{
    public function __construct(
        private ExternalAccountSync $externalAccountSync,
    ) {}

    /**
     * @throws InvalidLoginCredentialsException if email or password is incorrect
     * @throws AccountNotActivatedException if account not verified
     * @return string SSO login URL of external service
     */
    public function execute(
        LoginCredentials $credentials,
        bool $shouldRemember,
        string $ip
    ): string {
        if (! Auth::attempt(
            credentials: $credentials->toArray(),
            remember: $shouldRemember,
        )) {
            throw new InvalidLoginCredentialsException();
        }

        /** @var Account $account */
        $account = Auth::user();

        if (! $account->activated) {
            Auth::logout();
            throw new AccountNotActivatedException();
        }

        $account->updateLastLogin($ip);

        // Set the account's username to their external service account's
        // if it isn't already set
        if ($account->username === null) {
            $this->externalAccountSync->matchExternalUsername($account);
        }

        // Check if the user needs to complete 2FA
        if ($account->is_totp_enabled) {
            Session::put(MfaGate::NEEDS_MFA_KEY, 'true');
        }

        return true;
    }
}
